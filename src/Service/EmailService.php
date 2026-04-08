<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function sendMail(MailerInterface $mailer, Contact $contact){

    $email = (new TemplatedEmail())
    ->from(new Address('contact@symfogame.com', 'SymfoGame'))
    ->to(new Address($contact->getEmail(), $contact->getNom() ))
    ->subject('Bonjour')
    ->htmlTemplate('emails/welcome.html.twig')
    ->context([
        'contact' => $contact
    ]);

    $mailer->send($email);
    }
    
    //     public function sendMail(MailerInterface $mailer){

    //     $email = (new Email())
    //         ->from('demo@example.com')
    //         ->to('test@exemple.com')
    //         ->subject('Bonjour depuis Symfony !')
    //         ->text('Ceci est un email de test.');

    //     $mailer->send($email);

    // }


}