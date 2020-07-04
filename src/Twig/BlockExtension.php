<?php

declare(strict_types = 1);

namespace App\Twig;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    protected EntityManagerInterface $em;
    protected RequestStack $request;
    protected ?Site $site = null;
    protected SiteRepository $siteRepository;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request,
        SiteRepository $siteRepository
    ) {
        $this->em             = $em;
        $this->request        = $request;
        $this->siteRepository = $siteRepository;

        if (null !== $request->getCurrentRequest()) {
            /** @var Site $site */
            $site = $siteRepository->findOneBy([
                'url' => $request->getCurrentRequest()->getHost(),
            ]);
            if (null !== $site) {
                $this->site = $site;
            }
        }
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSite', [$this, 'getSite']),
        ];
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }
}
