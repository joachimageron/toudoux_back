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
    #[Route('/password/reset', name: 'test_send_mail', methods: ['POST'])]
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

    private $sendMailService;

    public function __construct(SendMail $sendMailService)
    {
        $this->sendMailService = $sendMailService;
    }

    public function sendMail(Request $request, SendMail $sendMail): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new Response("Invalid input", Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($email);

        $result = $sendMail->send($user);

        if ($result) {
            return new Response("Email envoyé avec succès.");
        } else {
            return new Response("Échec de l'envoi de l'email.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
