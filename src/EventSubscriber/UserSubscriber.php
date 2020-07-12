<?php

declare(strict_types = 1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\UserEvent;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->em         = $em;
        $this->translator = $translator;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'user.create'      => 'create',
            'user.update'      => 'update',
            'user.failedlogin' => 'failedLogin',
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function create(UserEvent $event): void
    {
        $user  = $event->getUser();
        $email = $user->getEmail();
        $aUser = $this->em->getRepository(User::class)->findBy(['email' => $email]);

        if (count($aUser) > 0) {
            throw new NonUniqueResultException($this->translator->trans('error.exception.user.email.unicity', [], 'error'));
        }

        $user
            ->setForceRenewPassword(false)
            ->generateSalt()
            ->setCreatedBy('register_user')
            ->setUpdatedBy('register_user');

        $this->em->persist($user);
        $this->em->flush();
    }

    public function update(UserEvent $event): void
    {
        $user = $event->getUser();

        $user
            ->generateSalt()
            ->setUpdatedBy('register_user');

        $this->em->persist($user);
        $this->em->flush();
    }

    public function failedLogin(UserEvent $event): void
    {
        $user = $event->getUser();

        $user
            ->setBlockedBy('guard_security')
            ->setBlockedAt(Carbon::now())
            ->setUpdatedBy('guard_security');
        $this->em->persist($user);
        $this->em->flush();
    }
}
