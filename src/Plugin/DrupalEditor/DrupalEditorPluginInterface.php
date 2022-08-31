<?php

namespace Drupal\drupal_editor\Plugin\DrupalEditor;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for drupal_editor plugins.
 */
interface DrupalEditorPluginInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Returns elements for add to widget.
   *
   * @param array $elements
   *   The plugin form elements.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   * @param array $values
   *   The values on form.
   *
   * @return array
   *   The renderable form elements.
   */
  public function form(array $elements, FormStateInterface $formState, array $values = []): array;

  /**
   * Build renderable array on plugin.
   *
   * @param array $values
   *   The values for render.
   *
   * @return array
   *   The renderable array.
   */
  public function view(array $values = []): array;

}
