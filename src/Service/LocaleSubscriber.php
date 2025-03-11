<?php

namespace App\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{


    public function __construct(
        private readonly string            $defaultLocale,

    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->getSession()->get('_locale')) {
            $request->setLocale($locale);
        } else {
            $request->setLocale($this->defaultLocale);
            $request->getSession()->set('_locale', $this->defaultLocale);
        }
    }


}