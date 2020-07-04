<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Service\alita\ForgotMailerService;
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

    /**
     * @Route("/send-forgot-mail/{id}", name="sandbox_sendforgotmail")
     */
    final public function sendForgotMail(int $id, ForgotMailerService $mailer): Response
    {
        /** @var User|null $user */
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if (null === $user) {
            return new Response('User not found');
        }
        $mailer->send($user);

        return new Response('Forgot Mail sended');
    }
}
