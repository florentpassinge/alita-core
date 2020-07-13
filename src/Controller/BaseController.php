<?php

declare(strict_types = 1);

namespace Alita\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BaseController.
 */
class BaseController extends AbstractController
{
    protected EntityManagerInterface $em;

    protected EventDispatcherInterface $dispatcher;

    protected TranslatorInterface $translator;

    /**
     * @var Session<string, mixed>
     */
    protected Session $session;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->em         = $em;
        $this->session    = new Session();
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }

    /**
     * @param array<string, string[]> $param
     */
    public function trans(string $id, array $param = [], string $domain = 'alita', ?string $local = null): string
    {
        return $this->translator->trans($id, $param, $domain, $local);
    }
}
