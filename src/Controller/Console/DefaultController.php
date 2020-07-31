<?php

namespace Alita\Controller\Console;

use Alita\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/alita")
 * @IsGranted("ROLE_ADMIN")
 */
class DefaultController extends BaseController
{
    /**
     * @Template()
     * @Route("/", name="alita_dashboard")
     */
    public function index(): void
    {
    }
}
