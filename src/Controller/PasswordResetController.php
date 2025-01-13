<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Mail\SendMail;
use OpenApi\Annotations as OA;

class PasswordResetController extends AbstractController
{
    #[Route('/password/reset', name: 'app_password_reset', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/password/reset",
     *     summary="Send password reset email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset email sent"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    private SendMail $sendMail;

    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

    public function sendMail(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données JSON depuis la requête POST
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || empty($data['email'])) {
            return new Response('Email invalide ou manquant.', Response::HTTP_BAD_REQUEST);
        }
    
        $email = $data['email'];
    
        // Chercher l'utilisateur dans la base de données
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);
    
        // Vérifier si l'utilisateur existe
        if (!$user) {
            return new Response('Utilisateur non trouvé.', Response::HTTP_NOT_FOUND);
        }
    
        // Appeler le service pour envoyer l'email
        $this->sendMail->send($user);
    
        // Retourner une réponse appropriée
        return new Response("Email envoyé avec succès.", Response::HTTP_OK);
    }
}
