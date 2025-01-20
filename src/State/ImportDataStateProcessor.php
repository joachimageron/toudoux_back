<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\Task;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\ImportData;

class ImportDataStateProcessor implements ProcessorInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ImportData {
        $slugify = new Slugify();
        $user = $this->security->getUser();
        $data->setUser($user);
        $data->setCreatedAt(new \DateTimeImmutable());
        $data->setStatus('IN_PROGRESS');  // statut temporaire

        // On parse le JSON
        $rawJson = $data->getData();
        $parsed = json_decode($rawJson, true);

        // Compteurs et logs
        $nbItems = 0;
        $logMessages = [];

        // Vérif basique de la structure
        if (!isset($parsed['items']) || !is_array($parsed['items'])) {
            $data->setStatus('ERROR');
            $data->setLog('Invalid JSON format: missing "items" array.');
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            return $data;
        }

        // Pour chaque "item" du JSON, on crée une Category
        foreach ($parsed['items'] as $catArray) {
            $catTitle = $catArray['title'] ?? 'Untitled category';
            $updated = $catArray['updated'] ?? null;

            // Exemple d’instanciation d’une Category
            $category = new Category();
            $category->setUser($user);
            $category->setName($catTitle);

            // Créez un slug à votre convenance (exemple basique)
            $category->setSlug($slugify->slugify($catTitle.'-'.$user->getId()));

            // Par exemple, on renseigne createdAt/updatedAt
            $now = new \DateTimeImmutable();
            $category->setUpdatedAt($now);
            // Si "updated" est un string valide
            try {
                $category->setCreatedAt(new \DateTimeImmutable($updated));
            } catch (\Exception $e) {
                // en cas de date invalide, on met la date du jour
                $category->setCreatedAt($now);
            }

            // Persist la category
            $this->entityManager->persist($category);

            // Récupère la liste de sous-items
            $subItems = $catArray['items'] ?? [];
            foreach ($subItems as $taskArray) {
                $taskTitle = $taskArray['title'] ?? 'Untitled task';
                $taskUpdated = $taskArray['updated'] ?? null;
                $status = $taskArray['status'] ?? 'unknown';

                $task = new Task();
                $task->setCategory($category);
                $task->setTitle($taskTitle);
                // Ici on pourrait mapper d'autres champs
                $task->setDescription($taskArray['kind'] ?? '');

                // On décide qu’un statut “completed” = done = true
                $task->setDone($status === 'completed');

                // createdAt / updatedAt
                $taskNow = new \DateTimeImmutable();
                $task->setUpdatedAt($taskNow);
                try {
                    $task->setCreatedAt(new \DateTimeImmutable($taskUpdated));
                } catch (\Exception $e) {
                    $task->setCreatedAt($taskNow);
                }

                $this->entityManager->persist($task);
                $nbItems++;
            }

            $logMessages[] = "Imported category '{$catTitle}' with "
                . count($subItems) . " task(s).";
        }

        // On finalize l’ImportData
        $data->setItemNumber($nbItems);
        $data->setStatus('DONE');
        $data->setLog(implode("\n", $logMessages));

        // On persiste l’objet ImportData
        $this->entityManager->persist($data);

//        dd($this->entityManager->getUnitOfWork()->getScheduledEntityInsertions());
        // Flush final
        $this->entityManager->flush();

        return $data;
    }

}
