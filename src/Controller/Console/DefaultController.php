<?php

namespace Alita\Controller\Console;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Blog'),

            MenuItem::section('Users'),
            MenuItem::linkToUrl('Comments', 'fa fa-comment', '#'),
            MenuItem::linkToUrl('Users', 'fa fa-user', '#'),
        ];
    }
}
