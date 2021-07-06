<?php

namespace Drupal\home_template\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Clase de control del bloque HolaMundo.
 *
 * @Block(
 *   id = "hola_mundo",
 *   admin_label = @Translation("Hola mundo"),
 *   category = @Translation("Hola mundo"),
 * )
 */
class Home_template extends BlockBase {

  /**
   * {@inheritdoc}
   */
public function build() {

  return [
    'title' => t('Hola mundo!'),
    'description' => t('Esto es un bloque que pinta un hola mundo'),
  ];
}
}
