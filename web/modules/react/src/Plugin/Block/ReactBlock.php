<?php

namespace Drupal\react\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReactBlock' block.
 *
 * @Block(
 *  id = "react_block",
 *  admin_label = @Translation("React block"),
 * )
 */
class ReactBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['react_block'] = [
      '#markup' => '<div id="react-app"></div>',
      '#attached' => [
        'library' => 'react/react_app'
      ],
    ];
    return $build;
  }
}