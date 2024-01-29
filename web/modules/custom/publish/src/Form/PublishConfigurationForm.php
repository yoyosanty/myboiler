<?php

namespace Drupal\publish\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures publish settings.
 */
class PublishConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'publish_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'publish.admin_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('publish.admin_settings');
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the higher environment to Publish content.'),
      '#default_value' => $config->get('url'),
    ];
    $form['state'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the state to push data to this environment.'),
      '#default_value' => $config->get('state'),
    ];
    $form['archive_state'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the state to archive data to this environment.'),
      '#default_value' => $config->get('archive_state'),
    ];
    $form['auth'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enter Auth details'),
    ];
    $form['auth']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the username.'),
      '#default_value' => $config->get('username'),
    ];
    $form['auth']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Enter the password.'),
      '#default_value' => $config->get('password'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('publish.admin_settings')
      ->set('url', $values['url'])
      ->set('state', $values['state'])
      ->set('archive_state', $values['archive_state'])
      ->set('username', $values['username'])
      ->set('password', $values['password'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
