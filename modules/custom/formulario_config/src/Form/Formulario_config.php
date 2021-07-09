<?php

namespace Drupal\formulario_config\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Egulias\EmailValidator\EmailValidator;
use Drupal\user\UserAuthInterface;

class Formulario_config extends FormBase {
  
 
protected $emailValidator;

protected function currentUser() {
  return \Drupal::currentUser();
}

  public function getFormId(){
    return 'formulario_contacto';
  }
 /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
 
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $logged_in = \Drupal::currentUser()->isAuthenticated();
    $user_email = $user->getEmail();
    
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Indique el correo electrónico en el que recibirá los formularios de contacto.'),
    ];
 
       $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Correo electrónico'),
        '#description' => $this->t('Correo electrónico.'),
        '#required' => TRUE,
        '#disabled' => FALSE,
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }
    /**
   * Validate the title and the checkbox of the form
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

  }
    /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Display the results.
    
    // Call the Static Service Container wrapper
    // We should inject the messenger service, but its beyond the scope of this example.
   $messenger = \Drupal::messenger();
    $messenger->addMessage('Formulario enviado ');
   $messenger->addMessage('email: '.$form_state->getValue('email'));
  $form_state->setRedirect('<front>');
    
  } 
  


}

