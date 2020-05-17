<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\Event\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LoginFormAuthenticator.
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private EntityManagerInterface $entityManager;

    private UrlGeneratorInterface $urlGenerator;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private UserPasswordEncoderInterface $passwordEncoder;

    private EventDispatcherInterface $dispatcher;

    private int $maxTryLogin;

    private TranslatorInterface $translator;

    public function __construct(
        int $maxTryLogin,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator
    ) {
        $this->translator       = $translator;
        $this->dispatcher       = $dispatcher;
        $this->maxTryLogin      = $maxTryLogin;
        $this->urlGenerator     = $urlGenerator;
        $this->entityManager    = $entityManager;
        $this->passwordEncoder  = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request): bool
    {
        return 'alita_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email'      => $request->request->get('email'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('form.generic.error.emailnotfound');
        }

        if ($user->getBlockedAt()) {
            $session = new Session();
            $session->set('disabledFormLogin', true);
            if ($user->getBlockedFor()) {
                throw new CustomUserMessageAuthenticationException('form.login.user.blocked.reason', ['%reason%' => $user->getBlockedFor()]);
            }

            throw new CustomUserMessageAuthenticationException('form.login.user.blocked');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        /** @var User $user */
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            $user->upTryToConnect();
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->entityManager->refresh($user);

            if ($this->maxTryLogin - 1 === $user->getTryToConnect()) {
                throw new CustomUserMessageAuthenticationException('form.login.user.preblocked');
            }

            if ($this->maxTryLogin === $user->getTryToConnect()) {
                $reason = $this->translator->trans('error.user.max.connect', [], 'error');
                $user->setBlockedFor($reason);

                $event = new UserEvent($user);
                $this->dispatcher->dispatch($event, 'user.failedlogin');

                $session = new Session();
                $session->set('disabledFormLogin', true);

                throw new CustomUserMessageAuthenticationException('form.login.user.blocked.reason', ['%reason%' => $user->getBlockedFor()]);
            }

            return false;
        }

        $user->setTryToConnect(0);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('alita_login'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('alita_login');
    }
}