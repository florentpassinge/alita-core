<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Service\alita\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sandbox", condition="'dev' === '%kernel.environment%'")
 */
class SandboxController extends AbstractController
{
    /**
     * @Route("/send-mail", name="sandbox_sendmail")
     *
     * @return Response
     */
    final public function sendMail(MailerService $mailer): Response
    {
        $mailer
            ->setSubject('Send mail')
            ->addTo('example@example.com')
            ->setBody('This is a message for my mail');
        $mailer->send();

        return new Response('Mail sended');
    }
}
