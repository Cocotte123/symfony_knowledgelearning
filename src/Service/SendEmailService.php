<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SendEmailService 
{
    
    private MailerInterface $mailer;
    

    public function __construct(MailerInterface $mailer)
    {
       
        $this->mailer = $mailer;
       
    }

    public function sendMail(string $from,string $to,string $subject,string $template,array $context):void
    {
        //create
        $email = (new TemplatedEmail)
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("email/$template.email.html.twig")
            ->context($context);

        //send
        $this->mailer->send($email);
    }
}