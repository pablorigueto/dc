<?php

namespace Drupal\dc_init_form\EventSubscriber;

use Drupal\Core\Cache\CacheableRedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects to langcode if the path didn't have the / at the end.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {    
    $events[KernelEvents::REQUEST][] = ['onRequest'];
    return $events;
  }

  /**
   * Limit this solution only to homepage.
   */
  public function onRequest(RequestEvent $event) {
    $path = $event->getRequest()->getPathInfo();
    $cookie = $event->getRequest()->cookies->get('selectedLanguage');

    // If path is default language.
    if ($path === '/pt-br' && empty($cookie)) {
      return;
    }

    if (empty($cookie)) {
      return;
    }

    if ($path === $cookie) {
      return;
    }

    $path_parts = explode('/', $path);
    if (isset($path_parts[2])) {
      return;
    }

    $response = new CacheableRedirectResponse($cookie);
    //$response = new TrustedRedirectResponse($cookie);
    $response->getCacheableMetadata()->setCacheMaxAge(0);
    $event->setResponse($response);
    $event->stopPropagation();

  }

}
