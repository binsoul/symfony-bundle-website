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
    /**
     * @var RouterInterface|null
     */
    private $router;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router = null)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

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
        if (!($domain instanceof DomainEntity)) {
            return;
        }

        $request->setDefaultLocale($domain->getWebsite()->getDefaultLocale()->getCode('_'));
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $domain = $request->attributes->get('domain');
        if (!($domain instanceof DomainEntity)) {
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
        if (!($domain instanceof DomainEntity)) {
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
        $availableLocales = $website->getAllLocales();

        $locale = null;
        if ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_SUBDOMAIN) {
            $locale = $domain->getDefaultLocale();
        } elseif ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_PARAMETER) {
            $locale = $this->localeFromRequest($request, $availableLocales);
        } elseif ($website->getLocaleType() === WebsiteEntity::LOCALE_TYPE_PATH) {
            $uri = $request->getUri();
            $path = (string) substr($uri, strlen($domain->getUrl()));
            $path = trim($path, '/');
            if ($path !== '') {
                $parts = explode('/', $path);
                $locale = $this->findLocale($parts[0], $availableLocales);
            }
        }

        if ($locale === null) {
            $locale = $website->getDefaultLocale();
        }

        return $locale;
    }

    /**
     * @param LocaleEntity[] $availableLocales
     */
    private function localeFromRequest(Request $request, array $availableLocales): ?LocaleEntity
    {
        $session = $request->getSession();

        $code = $request->get('_locale');
        if ($code === null && $session !== null && $request->hasPreviousSession()) {
            $code = $session->get('_locale');
        }

        if (!$code) {
            return null;
        }

        $locale = $this->findLocale((string) $code, $availableLocales);
        if ($locale === null) {
            return null;
        }

        if ($session !== null) {
            $session->set('_locale', $locale->getCode());
        }

        return $locale;
    }

    /**
     * @param LocaleEntity[] $availableLocales
     */
    private function findLocale(string $code, array $availableLocales): ?LocaleEntity
    {
        foreach ($availableLocales as $locale) {
            if (strtolower($locale->getCode()) === strtolower($code)) {
                return $locale;
            }
        }

        return null;
    }
}
