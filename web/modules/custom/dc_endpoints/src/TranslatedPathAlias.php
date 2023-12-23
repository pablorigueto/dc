<?php

namespace Drupal\dc_endpoints;

use Drupal\path_alias\AliasManager;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Overrides the core path alias manager service.
 */
class TranslatedPathAlias extends AliasManager {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * MyModuleAliasManager constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    parent::__construct(\Drupal::getContainer()->get('path_alias.storage'), $language_manager);

    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getPathAlias($source, $langcode = NULL) {
    // Use the translation manager service to get the translated path.
    $language = $this->languageManager->getLanguage($langcode);
    $path_alias = parent::getPathAlias($source, $language->getId());

    return $path_alias;
  }

}
