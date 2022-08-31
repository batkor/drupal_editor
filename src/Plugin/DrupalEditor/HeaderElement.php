<?php

namespace Drupal\drupal_editor\Plugin\DrupalEditor;

use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the drupal_editor.
 *
 * @DrupalEditor(
 *   id = "header",
 *   label = @Translation("Header"),
 *   description = @Translation("Add header element.")
 * )
 */
class HeaderElement extends DrupalEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $elements, FormStateInterface $formState, array $values = []): array {
    $elements['value'] = [
      '#type' => 'textfield',
      '#default_value' => $values['value'] ?? '',
    ];

    $elements['tag'] = [
      '#type' => 'select',
      '#default_value' => $values['tag'] ?? '',
      '#options' => [
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function view(array $values = []): array {
    return [
      '#type' => 'inline_template',
      '#template' => '<{{ tag }}>{{ value }} </{{ tag }}>',
      '#context' => [
        'value' => $values['value'],
        'tag' => $values['tag'],
      ],
    ];
  }
  
}
