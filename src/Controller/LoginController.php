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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends BaseController
{
    /**
     * @Route("/login", name="alita_login")
     */
    public function login()
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

        if ($form->isSubmitted() and $form->isValid()) {
        }
        /*
                $error = null;
                if ($request->isMethod(Request::METHOD_POST)) {
                    $email = $request->request->get('email');

                    $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
                    if (null === $user) {
                        $error = 'error.entity.user.notfound';
                    }

                    if ($user->getBlockedAt()) {
                        $error = 'error.user.blocked';
                    }

                    if (null === $error) {
                        $forgotMailerService->send($user);
                        $this->addFlash('success', $this->trans('alita.forgotpassword'));
                    }
                }
        */

        return ['form' => $form->createView()];
    }
}
