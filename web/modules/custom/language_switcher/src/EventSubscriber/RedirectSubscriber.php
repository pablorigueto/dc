<?php

namespace Drupal\language_switcher\EventSubscriber;

use Drupal\Core\Cache\CacheableRedirectResponse;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects to langcode if the path didn't have the / at the end.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * The Language Manager Interface.
   *
   * @var Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(LanguageManagerInterface $languageManager) {
    $this->languageManager = $languageManager;
  }

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
    $response->getCacheableMetadata()->setCacheMaxAge(0);
    $event->setResponse($response);
    $event->stopPropagation();
  }

  // public function getDefaultLanguage() {
  //   // Get the default language object.
  //   $defaultLanguage = $this->languageManager->getDefaultLanguage();
  //   // Get the language code of the default language.
  //   return '/' . $defaultLanguage->getId();
  // }

}
