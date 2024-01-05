<?php

namespace Drupal\pagseguro\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the custom account link content.
 */
class CustomAccountLinkController extends ControllerBase {

  /**
   * Content callback for the custom account link.
   */
  public function content() {
    // Your custom content goes here.
    return [
      '#markup' => $this->t('Hello, this is your custom account link content.'),
    ];
  }

}
