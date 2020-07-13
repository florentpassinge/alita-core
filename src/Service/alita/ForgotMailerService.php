<?php

declare(strict_types = 1);

namespace Alita\Service\alita;

use Alita\Entity\Site;
use Alita\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ForgotMailerService.
 */
class ForgotMailerService
{
    private MailerService $mailer;

    private TranslatorInterface $translator;

    private RequestStack $request;

    private RouterInterface $router;

    private EntityManagerInterface $em;

    private string $subject = 'mail.subject.forgotpassword';

    private string $template = 'Email/forgotPassword.html.twig';

    public function __construct(
        MailerService $mailer,
        TranslatorInterface $translator,
        RequestStack $request,
        RouterInterface $router,
        EntityManagerInterface $em
    ) {
        $this->em         = $em;
        $this->mailer     = $mailer;
        $this->request    = $request;
        $this->router     = $router;
        $this->translator = $translator;
    }

    public function send(?User $user, bool $needIp = true, ?Site $site = null): void
    {
        if (null === $user) {
            new \InvalidArgumentException('Error : $user can\'t be nullable');
        }

        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException('Error : $user isn\'t instance of UserInterface');
        }

        if (null !== $user->getBlockedAt()) {
            throw new AccountExpiredException('Error : This account has been blocked.');
        }

        if (null !== $site && $site instanceof Site) {
            $this->router->getContext()->setHost((string) $site->getUrl());
        }

        $time = Carbon::now()->addHours(2);
        $user->setRenewAt($time);
        $this->em->persist($user);
        $this->em->flush();

        $link = $this->router->generate('alita_resetPassword',
            [
                'id'   => $user->getId(),
                'data' => urlencode(base64_encode((string) $user->getSalt())),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL);

        $aParameters = [
            'link' => $link,
        ];

        if ($needIp) {
            /** @var Request $request */
            $request = $this->request->getCurrentRequest();
            $aParameters += ['ip' => $request->getClientIp()];
        }

        $subject = $this->translator->trans($this->subject, [], 'mail');

        $this->mailer
            ->setSubject($subject)
            ->addTo((string) $user->getEmail())
            ->setBody(null, $this->template, $aParameters);
        $this->mailer->send();
    }
}
