<?php

namespace App\Mail;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;

class SendMail
{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(User $user): bool
    {
        $email = (new Email())
            ->from("sandaraly1@gmail.com")
            ->to($user->getEmail())
            ->subject("Inscription à la newsletter")
            ->text("Votre email " . $user->getEmail() . " a bien été enregistré, merci.");

        try {
            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            // Log or handle the exception
            error_log('Erreur d\'envoi d\'email : ' . $e->getMessage());
            return false;
        }
    }

}