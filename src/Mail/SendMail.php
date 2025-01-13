<?php

namespace App\Mail;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMail
{
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function send(User $user): void
    {
        $email = (new Email())
            ->from("admin@hb-corp.com")
            ->to("sandaraly1@gmail.com")
            ->subject("Inscription à la newsletter")
            ->text("Votre email " . $user->getEmail() . " a bien été enregistré, merci");
    
        $this->mailer->send($email);
    }
    
}