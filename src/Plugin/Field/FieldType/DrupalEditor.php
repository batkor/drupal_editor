<?php

namespace Drupal\drupal_editor\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TraversableTypedDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'drupal_editor_example' field type.
 *
 * @FieldType(
 *   id = "drupal_editor",
 *   label = @Translation("Drupal Editor"),
 *   category = @Translation("Text"),
 *   default_widget = "drupal_editor",
 *   default_formatter = "drupal_editor",
 *   cardinality = 1
 * )
 */
final class DrupalEditor extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'elements' => ['paragraph'],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = $options = [];
    $settings = $this->getSettings();

    $elementDefs = \Drupal::service('plugin.manager.drupal_editor')
      ->getDefinitions();

    foreach ($elementDefs as $id => $elementDef) {
      $options[$id] = $elementDef['label'];
    }

    $element['elements'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Support editor elements'),
      '#options' => $options,
      '#default_value' => $settings['elements'] ?? [],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties[self::mainPropertyName()] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Raw value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    if (!isset($values[self::mainPropertyName()])) {
      $values = [self::mainPropertyName() => $values];
    }
    
    if (is_array($values[self::mainPropertyName()])) {
      $values[self::mainPropertyName()] = serialize($values[self::mainPropertyName()]);
    }

    parent::setValue($values, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $value = $this->get(self::mainPropertyName())->getValue();

    if (!is_array($value)) {
      $value = unserialize($value, ['allowed_classes' => FALSE]);
    }

    return $value ?: [];
  }
}
