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
        // Générer le lien de réinitialisation avec le token
        $resetLink = sprintf('http://localhost:3000/password/reset-password?token=%s', $user->getResetToken());
    
        // Créer l'email avec un message personnalisé
        $email = (new Email())
            ->from("sandaraly1@gmail.com")
            ->to($user->getEmail())
            ->subject("Réinitialisation de votre mot de passe sur Toudoux")
            ->html(
                sprintf(
                    '<p>Bonjour %s,</p>
                    <p>Nous avons reçu une demande pour réinitialiser votre mot de passe sur <strong>Toudoux</strong>. 
                    Si vous êtes à l\'origine de cette demande, veuillez cliquer sur le lien ci-dessous pour créer un nouveau mot de passe.</p>
                    <p><a href="%s">Cliquez ici pour réinitialiser votre mot de passe</a></p>
                    <p>Si vous n\'avez pas effectué cette demande, il n\'y a rien à faire. Votre mot de passe reste inchangé et votre compte est toujours sécurisé.</p>
                    <p>Cette demande expirera dans 1 heure. Si vous ne réinitialisez pas votre mot de passe dans ce délai, vous devrez recommencer le processus.</p>
                    <p>Merci d\'utiliser <strong>Toudoux</strong> pour mieux organiser vos tâches !</p>
                    <p><strong>Note :</strong> Si vous avez des questions ou rencontrez des problèmes, notre équipe d\'assistance est disponible à <a href="mailto:support@toudoux.com">support@toudoux.com</a>.</p>',
                    $user->getEmail(), // Prénom de l'utilisateur
                    $resetLink
                )
            );
    
        // Essayer d'envoyer l'email
        try {
            $this->mailer->send($email);
            return true; // Envoi réussi
        } catch (\Exception $e) {
            // Loguer ou gérer l'exception
            error_log('Erreur d\'envoi d\'email : ' . $e->getMessage());
            return false; // Envoi échoué
        }
    }
    
}