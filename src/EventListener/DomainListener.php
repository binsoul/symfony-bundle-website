<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\EventListener;

use BinSoul\Symfony\Bundle\Website\Entity\DomainEntity;
use BinSoul\Symfony\Bundle\Website\Repository\DomainRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class DomainListener implements EventSubscriberInterface
{
    /**
     * @var RouterInterface|null
     */
    private $router;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var DomainRepository
     */
    private $domainRepository;
    /**
     * @var DomainEntity[]
     */
    private $domains = [];

    /**
     * Constructs an instance of this class.
     */
    public function __construct(RequestStack $requestStack, DomainRepository $domainRepository, RouterInterface $router = null)
    {
        $this->requestStack = $requestStack;
        $this->domainRepository = $domainRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 150]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->setDomainParameters($event->getRequest());
    }

    public function onKernelFinishRequest(): void
    {
        $parentRequest = $this->requestStack->getParentRequest();
        if ($parentRequest !== null) {
            $this->setDomainParameters($parentRequest);
        }
    }

    private function setDomainParameters(Request $request): void
    {
        $domains = $this->loadDomains();
        if (count($domains) === 0) {
            return;
        }

        $uri = $request->getUri();

        $matches = [];
        foreach ($domains as $domain) {
            if (stripos($uri, $domain->getUrl()) === 0) {
                $matches[strlen($domain->getUrl())] = $domain;
            }
        }

        if (count($matches) === 0) {
            return;
        }

        /** @var DomainEntity $domain */
        $domain = array_pop($matches);

        $request->attributes->set('domain', $domain);

        if ($this->router !== null) {
            $this->router->getContext()->setParameter('domain', $domain);
        }
    }

    /**
     * @return DomainEntity[]
     */
    private function loadDomains(): array
    {
        if (count($this->domains) === 0) {
            $this->domains = $this->domainRepository->loadAll();
        }

        return $this->domains;
    }
}
