<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\EventListener;

use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use BinSoul\Symfony\Bundle\Website\Entity\DomainEntity;
use BinSoul\Symfony\Bundle\Website\Entity\WebsiteEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class LocaleListener implements EventSubscriberInterface
{
    private readonly ?RouterInterface $router;

    private readonly RequestStack $requestStack;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(RequestStack $requestStack, ?RouterInterface $router = null)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * @return mixed[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['setDefaultLocale', 100],
                ['onKernelRequest', 90],
            ],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }

    public function setDefaultLocale(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $domain = $request->attributes->get('domain');

        if (! ($domain instanceof DomainEntity)) {
            return;
        }

        $request->setDefaultLocale($domain->getWebsite()->getDefaultLocale()->getCode('_'));
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $domain = $request->attributes->get('domain');

        if (! ($domain instanceof DomainEntity)) {
            return;
        }

        $locale = $this->getLocale($domain, $request);

        $this->setLocale($request, $locale);
        $this->setRouterContext($locale);
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

        $this->setRouterContext($this->getLocale($domain, $parentRequest));
    }

    private function setLocale(Request $request, LocaleEntity $locale): void
    {
        $request->setLocale($locale->getCode('_'));
        $request->attributes->set('_locale', $locale->getCode('_'));
        $request->attributes->set('locale', $locale);
    }

    private function setRouterContext(LocaleEntity $locale): void
    {
        if ($this->router === null) {
            return;
        }

        $this->router->getContext()->setParameter('_locale', $locale->getCode('_'));
        $this->router->getContext()->setParameter('locale', $locale);
    }

    private function getLocale(DomainEntity $domain, Request $request): LocaleEntity
    {
        $website = $domain->getWebsite();
        $locale = null;

        if ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_SUBDOMAIN) {
            $locale = $domain->getDefaultLocale();
        } elseif ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_PARAMETER) {
            $locale = $this->localeFromRequest($request, $website);
        } elseif ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_PATH) {
            $uri = $request->getUri();
            $path = substr($uri, strlen($domain->getUrl()));
            $path = trim($path, '/');

            if ($path !== '') {
                $parts = explode('/', $path);
                $locale = $website->chooseAvailableLocale($parts[0]);
            }
        }

        if ($locale === null) {
            $locale = $website->getDefaultLocale();
        }

        return $locale;
    }

    private function localeFromRequest(Request $request, WebsiteEntity $website): ?LocaleEntity
    {
        $session = null;

        if ($request->hasPreviousSession()) {
            $session = $request->getSession();
        }

        $code = $request->get('_locale');

        if ($code === null && $session !== null) {
            $code = $session->get('_locale');
        }

        if (! $code) {
            return $website->getDefaultLocale();
        }

        $locale = $website->chooseAvailableLocale((string) $code);

        if ($locale === null) {
            $locale = $website->getDefaultLocale();
        }

        if ($request->hasSession()) {
            $request->getSession()->set('_locale', $locale->getCode());
        }

        return $locale;
    }
}
