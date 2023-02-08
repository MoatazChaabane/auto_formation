<?php

namespace App\service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class mailerService
{
private $replyTo;
    public function __construct(private MailerInterface $mailer,$replyTo)
    {
        $this->replyTo=$replyTo;
    }

    public function sendEmail(
        $to='chaabanemoataz@outlook.fr',
        $content= '<p>See Twig integration for better HTML integration!</p>',
        $subject='Time for Symfony Mailer!'
    ): void

    {
        $email = (new Email())
            ->from('moatazchaabane26@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($this->replyTo)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
//            ->text('Sending emails is fun again!')
            ->html($content);

         $this->mailer->send($email);

        // ...
    }

}