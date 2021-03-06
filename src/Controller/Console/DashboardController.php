<?php

namespace Alita\Controller\Console;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/alita")
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends BaseController
{
    /**
     * @Route("/", name="alita_dashboard")
     * @Template()
     */
    public function index(): array
    {
        return [];
    }
}
