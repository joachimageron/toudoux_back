<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends AbstractController
{
    // Définir la route avec l'attribut #[Route]
    #[Route('/reset-password?{token}', name: 'reset-password', methods: ['GET'])]
    public function controlToken(Request $request, UserRepository $userRepository): Response
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        $now = new \DateTime();
        // Vérifier si l'utilisateur a bien été trouvé
        if (!$user) {
            return new Response('Invalid token.', Response::HTTP_NOT_FOUND);
        } else if ($user->getResetTokenExpiresAt() < $now) {
            return new Response('Token expired.', Response::HTTP_BAD_REQUEST);
        } else {
            return $this->json([
                'userId' => $user->getId()
            ], Response::HTTP_OK);
        }
    }


    #[Route('/change-password', name: 'change-password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasher $passwordEncoder,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer le token depuis la query string
        $token = $request->query->get('token');
        $userId = $request->query->get('userId');
        $now = new \DateTime();
        //$password = $request->query->get('password');
        // Répondre avec un message de succès*/

        $data = json_decode($request->getContent(), true); // Parse the body content as an associative array
        $password = $data['password'] ?? null;

        // Si le mot de passe est vide
        if (empty($password)) {
            return new JsonResponse(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
        }

        // trouver l'utilisateur part son ID 
        $user = $userRepository->find($userId);

        // Vérification des tokens de l'utilisateur
        if ($user) {
            $token = $userRepository->findOneBy(['resetToken' => $token]);
            if (!$token) {
                return new JsonResponse('Invalid token.', Response::HTTP_NOT_FOUND);
            } else if ($user->getResetTokenExpiresAt() < $now) {
                return new JsonResponse('Token expired.', Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse('User not found.', Response::HTTP_NOT_FOUND);
        }

        // Hachage du mot de passe avec le password encoder
        $hashedPassword = $passwordEncoder->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Sauvegarde de l'utilisateur
        try {
            $entityManager->persist($user); // Persiste l'utilisateur
            $entityManager->flush(); // Sauvegarde les changements en base de données
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the user: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            [
                "Changing password successful"
            ],
            Response::HTTP_OK
        );
    }
}
