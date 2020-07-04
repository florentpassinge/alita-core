<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Event\UserEvent;
use App\Form\Login\ForgotPasswordType;
use App\Form\Login\ResetPasswordType;
use App\Service\alita\ForgotMailerService;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends BaseController
{
    /**
     * @Template()
     * @Route("/login", name="alita_login")
     *
     * @return array|RedirectResponse
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            if ($this->getUser()->isAdmin()) {
                return $this->redirectToRoute('alita_dashboard');
            }

            return $this->redirectToRoute('front_index');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return ['last_email' => $lastUsername, 'error' => $error];
    }

    /**
     * @Route("/logout", name="alita_logout")
     */
    public function logout(): void
    {
    }

    /**
     * @Template()
     * @Route("/resetPassword/{id}/{data}", name="alita_resetPassword")
     *
     * @return array|RedirectResponse
     */
    public function resetPassword(
        int $id,
        string $data,
        Request $request,
        UserPasswordEncoderInterface $encoder
    ) {
        $user       = $this->getDoctrine()->getRepository(User::class)->find($id);
        $parameters = [];
        if (null !== $user) {
            $salt = base64_decode(urldecode($data));

            if ($salt === $user->getSalt()) {
                if (Carbon::now()->gt($user->getRenewAt())) {
                    $parameters['error']['message'] = 'error.entity.user.link.renew.notvalid';
                }
            } else {
                $parameters['error']['message'] = 'error.form.entity.user.salt';
            }
        } else {
            $parameters['error']['message'] = 'error.entity.user.notfound';
        }

        if (!array_key_exists('error', $parameters)) {
            $parameters['showForm'] = true;
            $form                   = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $password = $encoder->encodePassword($user, $data['password']);
                $user->setPassword($password)
                    ->setRenewAt(null);

                $event = new UserEvent($user);
                $this->dispatcher->dispatch($event, 'user.update');

                return $this->redirectToRoute('alita_login');
            }
            $parameters['form'] = $form->createView();
        }

        if (array_key_exists('error', $parameters)) {
            $parameters['error']['params'] = [];
        }

        return $parameters;
    }

    /**
     * @Template()
     * @Route("forgotPassword", name="alita_forgotPassword")
     */
    public function forgotPassword(Request $request, ForgotMailerService $forgotMailerService): array
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user->getBlockedAt()) {
                $form->addError((new FormError($this->trans('error.entity.user.blocked', [], 'error'))));
            } else {
                $forgotMailerService->send($user);
                $this->addFlash('success', $this->trans('alita._flash.forgotpassword'));
            }
        }

        return ['form' => $form->createView()];
    }
}
