<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\EventListener;

use BinSoul\Symfony\Bundle\I18n\Entity\CurrencyEntity;
use BinSoul\Symfony\Bundle\Website\Entity\DomainEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class CurrencyListener implements EventSubscriberInterface
{
    private ?RouterInterface $router;

    private RequestStack $requestStack;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(RequestStack $requestStack, ?RouterInterface $router = null)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 80]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $domain = $request->attributes->get('domain');

        if (! ($domain instanceof DomainEntity)) {
            return;
        }

        $currency = $this->getCurrency($domain, $request);

        $this->setRequestAttributes($request, $currency);
        $this->setRouterContext($currency);
    }

    public function onKernelFinishRequest(): void
    {
        $parentRequest = $this->requestStack->getParentRequest();

        if ($parentRequest === null) {
            return;
        }

        $domain = $parentRequest->attributes->get('domain');

        if (! ($domain instanceof DomainEntity)) {
            return;
        }

        $this->setRouterContext($this->getCurrency($domain, $parentRequest));
    }

    private function setRequestAttributes(Request $request, CurrencyEntity $currency): void
    {
        $request->attributes->set('_currency', $currency->getIso3());
        $request->attributes->set('currency', $currency);
    }

    private function setRouterContext(CurrencyEntity $currency): void
    {
        if ($this->router === null) {
            return;
        }

        $this->router->getContext()->setParameter('_currency', $currency->getIso3());
        $this->router->getContext()->setParameter('currency', $currency);
    }

    private function getCurrency(DomainEntity $domain, Request $request): CurrencyEntity
    {
        $website = $domain->getWebsite();
        $session = null;

        if ($request->hasPreviousSession()) {
            $session = $request->getSession();
        }

        $code = $request->get('_currency');

        if ($code === null && $session !== null) {
            $code = $session->get('_currency');
        }

        if (! $code) {
            return $website->getDefaultCurrency();
        }

        $currency = null;
        $code = strtoupper((string) $code);

        foreach ($website->getAllCurrencies() as $availableCurrency) {
            if ($code === $availableCurrency->getIso3()) {
                $currency = $availableCurrency;

                break;
            }
        }

        if ($currency === null) {
            $currency = $website->getDefaultCurrency();
        }

        if ($request->hasSession()) {
            $request->getSession()->set('_currency', $currency->getIso3());
        }

        return $currency;
    }
}
