<?php

namespace Drupal\drupal_editor\Plugin\Field\FieldWidget;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'drupal_editor' field widget.
 *
 * @FieldWidget(
 *   id = "drupal_editor",
 *   label = @Translation("Drupal Editor"),
 *   field_types = {"drupal_editor"},
 * )
 */
final class DrupalEditorWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $formState) {
    $this->setDrupalEditorElements($items, $formState);
    $formState->set('drupal_editor_field', $items->getName());
    $title = $this->fieldDefinition->getLabel();
    $description = $this->getFilteredDescription();
    $wrapper = Html::getId('values-' . $items->getName() . '_wrapper');
    $element = [
      '#type' => 'fieldset',
      '#title' => $title,
      '#title_display' => 'before',
      '#description' => $description,
    ];


    $element['values'] = [
      '#type' => 'container',
      '#prefix' => '<div id="' . $wrapper . '">',
      '#suffix' => '</div>',
    ];

    $editorElements = $formState->get('drupal_editor_elements') ?? [];

    foreach ($editorElements as $editorElement) {
      /** @var \Drupal\drupal_editor\Plugin\DrupalEditor\DrupalEditorPluginInterface $editorElementInstance */
      $editorElementInstance = $this
        ->drupalEditorManager
        ->createInstance($editorElement['type']);

      $element['values'][] = [
        'type' => ['#type' => 'hidden', '#value' => $editorElement['type']],
        'values' => $editorElementInstance->form([], $formState, $editorElement['values']),
      ];
    }

    $element['actions']['#type'] = 'actions';
    $elementOptions = $this->getElementsToChoose();
    $element['actions']['elements'] = [
      '#type' => 'select',
      '#title' => $this->t('Editor elements'),
      '#options' => $elementOptions,
      '#default_value' => array_key_first($elementOptions),
    ];

    $element['actions']['add'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#submit' => [[static::class, 'addElement']],
      '#ajax' => [
        'callback' => [static::class, 'addElementCallback'],
        'wrapper' => $wrapper,
        'event' => 'click',
      ],
      '#limit_validation_errors' => [],
    ];

    return $element;
  }

  /**
   * The handler for "Add element" submit.
   */
  public static function addElement(array &$form, FormStateInterface $formState): void {
    $parents = [
      $formState->get('drupal_editor_field'),
      0,
      'actions',
      'elements',
    ];
    $input = NestedArray::getValue($formState->getUserInput(), $parents);
    $elements = $formState->get('drupal_editor_elements') ?? [];
    $elements[] = [
      'type' => $input,
      'values' => [],
    ];
    $formState->set('drupal_editor_elements', $elements);
    $formState->setRebuild();
  }

  /**
   * The ajax callback handler for "Add element" submit.
   *
   * @param array $form
   *   The renderable form.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   *
   * @return array
   *   The form element.
   */
  public static function addElementCallback(array &$form, FormStateInterface $formState): array {
    return $form[$formState->get('drupal_editor_field')]['widget'][0]['values'];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      unset($value['actions']);
      $value['value'] = $value['values'] ?? [];
      unset($value['values']);
    }

    return $values;
  }

  /**
   * Returns options value for "select" form elements.
   *
   * @return array
   *   The options value.
   */
  protected function getElementsToChoose(): array {
    $elements = [];

    foreach ($this->drupalEditorManager->getDefinitions() as $id => $elementDef) {
      $elements[$id] = $elementDef['label'];
    }

    return $elements;
  }

  /**
   * Set field items value to form state.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function setDrupalEditorElements(FieldItemListInterface $items, FormStateInterface $formState): void {
    if ($formState->isRebuilding()) {
      return;
    }

    $values = $items->first()->toArray();
    $formState->set('drupal_editor_elements', $values);
  }

}
