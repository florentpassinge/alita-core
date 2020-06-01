<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Service\alita\ForgotMailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends BaseController
{
    /**
     * @Route("/logout", name="alita_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/forgotPassword", name="alita_forgotPassword")
     */
    public function forgotPassword(Request $request, ForgotMailerService $forgotMailer): Response
    {
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
                $forgotMailer->send($user);
                $this->addFlash('success', $this->trans('alita.forgotpassword'));
            }
        }

        return $this->render('Form/forgot.html.twig', ['error' => $error]);
    }
}
