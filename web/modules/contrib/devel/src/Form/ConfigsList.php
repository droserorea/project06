<?php

namespace Drupal\devel\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form that displays all the config variables to edit them.
 */
class ConfigsList extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'devel_config_system_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $filter = '')
  {
    $form['filter'] = [
      '#type' => 'details',
      '#title' => $this->t('Filter variables'),
      '#attributes' => ['class' => ['container-inline']],
      '#open' => isset($filter) && trim($filter) != '',
    ];
    $form['filter']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Variable name'),
      '#title_display' => 'invisible',
      '#default_value' => $filter,
    ];
    $form['filter']['actions'] = ['#type' => 'actions'];
    $form['filter']['actions']['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];

    $header = [
      'name' => ['data' => $this->t('Name')],
      'edit' => ['data' => $this->t('Operations')],
    ];

    $rows = [];

    $destination = $this->getDestinationArray();

    // List all the variables filtered if any filter was provided.
    $names = $this->configFactory()->listAll($filter);

    foreach ($names as $config_name) {
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'url' => Url::fromRoute('devel.config_edit', ['config_name' => $config_name]),
        'query' => $destination,
      ];
      $rows[] = [
        'name' => $config_name,
        'operation' => ['data' => ['#type' => 'operations', '#links' => $operations]],
      ];
    }

    $form['variables'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No variables found'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $filter = $form_state->getValue('name');
    $form_state->setRedirectUrl(Url::FromRoute('devel.configs_list', ['filter' => Html::escape($filter)]));
  }

}
