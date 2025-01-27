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
use App\Repository\UserRepository;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot-password', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/forgot-password",
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

    private $sendMailService;
    private $entityManager;
    private $userRepository;

    public function __construct(SendMail $sendMailService, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->sendMailService = $sendMailService;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function sendMail(Request $request, SendMail $sendMail, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new Response("Invalid input", Response::HTTP_BAD_REQUEST);
        }

        // Assuming you have a method to find the user by email
        $user = $userRepository->findOneBy(["email"=> $email]);

        if (!$user) {
            return new Response("User not found", Response::HTTP_NOT_FOUND);
        }

        // Generate a unique token and expiration date
        $resetToken = bin2hex(random_bytes(32));
        $expireToken = (new \DateTime())->modify('+1 hour');

        // Save the token and expiration date to the user
        $user->setResetToken($resetToken);
        $user->setResetTokenExpiresAt($expireToken);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        // Send the email
        $result = $sendMail->send($user);

        if ($result) {
            return new Response("Succès de l'envoi de l'email", Response::HTTP_OK, ['Content-Type' => 'application/json']);
        } else {
            return new Response("Échec de l'envoi de l'email.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
