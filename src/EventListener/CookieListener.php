<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\EventListener;

use BinSoul\Symfony\Bundle\Website\Entity\DomainEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieListener implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 140]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->setCookieParameters($event->getRequest());
    }

    public function onKernelFinishRequest(): void
    {
        $parentRequest = $this->requestStack->getParentRequest();
        if ($parentRequest !== null) {
            $this->setCookieParameters($parentRequest);
        }
    }

    private function setCookieParameters(Request $request): void
    {
        if (session_status() !== PHP_SESSION_NONE || !$request->attributes->has('domain')) {
            return;
        }

        $domain = $request->attributes->get('domain');
        if (!($domain instanceof DomainEntity)) {
            return;
        }

        $host = parse_url($domain->getUrl(), PHP_URL_HOST);
        if ($host) {
            ini_set('session.cookie_domain', $host);

            $path = (string) parse_url($domain->getUrl(), PHP_URL_PATH);
            if (trim($path, '/') !== '') {
                ini_set('session.cookie_path', $path);
            }
        }
    }
}
