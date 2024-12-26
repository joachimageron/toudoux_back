<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    #[OA\Tag(name: 'Register')]
    #[Security(name: 'Bearer')]
    #[OA\Parameter(
        name: 'Content-Type',
        description: 'The content type of the request',
        in: 'header',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            example: 'application/json'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'password123')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns a message if the user is registered',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User created successfully'),
                new OA\Property(property: 'user', properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com')
                ], type: 'object')
            ],
            type: 'object'
        )
    )]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // 1. Récupérer le contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);

        // 2. Vérifier la présence des champs requis (email et password)
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        // 3. Créer une nouvelle instance de l'entité User
        $user = new User();
        $user->setEmail($data['email']);

        // 4. Hacher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // 5. Attribuer un rôle par défaut (facultatif, selon votre logique)
        $user->setRoles(['ROLE_USER']);

        // 6. Sauvegarder l'utilisateur en base
        $entityManager->persist($user);
        $entityManager->flush();

        // 7. Retourner une réponse JSON
        return new JsonResponse(
            [
                'message' => 'User created successfully',
                'user' => [
                    'email' => $user->getEmail(),
                ]
            ],
            201
        );
    }
}
