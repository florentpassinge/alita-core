<?php

declare(strict_types = 1);

namespace Alita\Twig;

use Alita\Entity\Site;
use Alita\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    protected EntityManagerInterface $em;
    protected ?MatcherInterface $matcher = null;
    protected RequestStack $request;
    protected ?Site $site = null;
    protected SiteRepository $siteRepository;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request,
        SiteRepository $siteRepository,
        ?MatcherInterface $matcher = null
    ) {
        $this->em             = $em;
        $this->matcher        = $matcher;
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
            new TwigFunction('isCurrent', [$this, 'isCurrent']),
            new TwigFunction('isAncestor', [$this, 'isAncestor']),
        ];
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function isCurrent(ItemInterface $item): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isCurrent($item);
    }

    public function isAncestor(ItemInterface $item): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item);
    }
}
