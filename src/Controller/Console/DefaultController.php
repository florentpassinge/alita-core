<?php

namespace Alita\Controller\Console;

use Alita\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DefaultController.
 *
 * @Route("/alita")
 * @IsGranted("ROLE_ADMIN")
 */
class DefaultController extends AbstractDashboardController
{
    /**
     * @Route("/", name="alita_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::subMenu('Blog', 'fa-page')->setSubItems([
                MenuItem::linkToUrl('Comments', 'fa fa-comment', '#'),
                MenuItem::linkToUrl('Users', 'fa fa-user', '#'),
            ]),

            MenuItem::subMenu('Users', 'fa-cogs')->setSubItems([
                MenuItem::linkToUrl('Comments', 'fa fa-comment', '#'),
                MenuItem::linkToUrl('Users', 'fa fa-user', '#'),
            ]),
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /** @var User $user */
        $user = clone $user;

        return UserMenu::new()
            ->setName($user->getName())
            ->displayUserName(true)
            ->setAvatarUrl('https://gravatar.com/avatar/59fc9007ea9812447d85cf49909a0ba2?s=32&d=robohash&r=x')
            ->addMenuItems([
                MenuItem::linkToRoute('alita.console.profile', 'fa fa-id-card', 'alita_dashboard'),
                MenuItem::linkToLogout('alita.console.logout', 'fas fa-sign-out-alt'),
            ]);
    }
}
