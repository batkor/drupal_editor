<?php

namespace Drupal\drupal_editor\Plugin\DrupalEditor;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for drupal_editor plugins.
 */
abstract class DrupalEditorPluginBase extends PluginBase implements DrupalEditorPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return (string) $this->pluginDefinition['label'];
  }

  protected function getValues(): array {
    return $this->configuration['value'];
  }

}
