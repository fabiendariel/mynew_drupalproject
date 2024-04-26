<?php

/**
 * @file
 * Generate markup to be displayed. Functionality in this controller is
 * wired to Drupal in mymodule.routing.yml
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {

  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello Drupal world.'),
    ];
  }

  public function variableContent(string $name_1, string $name_2)
  {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('@name_1 and @name_2 say hello to you.',
      ['@name_1' => $name_1, '@name_2' => $name_2]),
    ];
  }
}