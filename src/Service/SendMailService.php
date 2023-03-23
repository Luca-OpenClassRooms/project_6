<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService 
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
        // 
    }

    public function send(string $from, string $to, string $subject, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        return $this->mailer->send($email);
    }
}