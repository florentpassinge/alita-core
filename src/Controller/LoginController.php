<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends BaseController
{
    /**
     * @Route("/resetPassword/{user_id}/{data}", name="alita_resetPassword")
     * @ParamConverter("user", options={"id" = "user_id"})
     */
    public function resetPassword(User $user, string $data, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        return new Response(__FUNCTION__.'in '.__CLASS__);
    }
}
