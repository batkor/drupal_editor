<?php

namespace Drupal\drupal_editor\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * Defines drupal_editor annotation object.
 *
 * @Annotation
 */
class DrupalEditor extends Plugin {

  /**
   * The plugin ID.
   */
  public string $id;

  /**
   * The human-readable name of the plugin.
   *
   * @ingroup plugin_translatable
   */
  public Translation $title;

  /**
   * The description of the plugin.
   *
   * @ingroup plugin_translatable
   */
  public Translation $description;

}
