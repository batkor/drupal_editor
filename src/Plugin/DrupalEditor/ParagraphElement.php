<?php

namespace Drupal\drupal_editor\Plugin\DrupalEditor;

use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the drupal_editor.
 *
 * @DrupalEditor(
 *   id = "paragraph",
 *   label = @Translation("Paragraph"),
 *   description = @Translation("Add paragraph element.")
 * )
 */
class ParagraphElement extends DrupalEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $elements, FormStateInterface $formState, array $values = []): array {
    $elements['paragraph'] = [
      '#type' => 'textarea',
      '#default_value' => $values['paragraph'] ?? '',
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function view(array $values = []): array {
    return [
      '#type' => 'inline_template',
      '#template' => '<p>{{ value|nl2br }}</p>',
      '#context' => ['value' => $values['paragraph']],
    ];
  }

}
