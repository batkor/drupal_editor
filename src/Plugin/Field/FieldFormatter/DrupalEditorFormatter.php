<?php

namespace Drupal\drupal_editor\Plugin\Field\FieldFormatter;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'DrupalEditorFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "drupal_editor",
 *   label = @Translation("Drupal Editor"),
 *   field_types = {
 *     "drupal_editor"
 *   }
 * )
 */
final class DrupalEditorFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The Drupal Editor plugin manager.
   */
  protected PluginManagerInterface $drupalEditorManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $static = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $static->drupalEditorManager = $container->get('plugin.manager.drupal_editor');

    return $static;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $build = [];

    foreach ($items as $delta => $item) {
      foreach ($item->toArray() as $element) {
        /** @var \Drupal\drupal_editor\Plugin\DrupalEditor\DrupalEditorPluginInterface $editorElementInstance */
        $elementInstance = $this
          ->drupalEditorManager
          ->createInstance($element['type']);
        $build[] = $elementInstance->view($element['values']);
      }
    }

    return [$delta => $build];
  }

}
