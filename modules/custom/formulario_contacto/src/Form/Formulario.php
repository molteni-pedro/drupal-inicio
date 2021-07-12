<?php

namespace Drupal\formulario_contacto\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Egulias\EmailValidator\EmailValidator;


class Formulario extends FormBase {
  
protected $mailManager;
protected $languageManager;
protected $emailValidator;

public function __construct(MailManagerInterface $mail_manager,
  LanguageManagerInterface $language_manager,
  EmailValidator $email_validator) {
  $this->mailManager = $mail_manager;
  $this->languageManager = $language_manager;
  $this->emailValidator = $email_validator;
}
public static function create(ContainerInterface $container) {
return new static(
  $container->get('plugin.manager.mail'),
  $container->get('language_manager'),
  $container->get('email.validator')
  );
}
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
    
    $form['consejo'] = [
      '#type' => 'item',
      '#markup' => $this->t('Para conctactar con nosotros, por favor rellene el siguiente formulario.'),
    ];
    if($logged_in=="1"){
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Correo electrónico'),
        '#description' => $this->t('Correo electrónico.'),
        '#required' => TRUE,
        '#default_value' => $user_email,
        '#disabled' => TRUE,
    ];
    }else{
       $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Correo electrónico'),
        '#description' => $this->t('Correo electrónico.'),
        '#required' => TRUE,
        '#disabled' => FALSE,

    ];
    }
    
    
    $form['asunto'] = [
      '#type' => 'select',
      '#title' => $this->t('¿Que tipo de ayuda necesita?'),
      '#options' => [
        '1' => $this
          ->t('Contacto simple'),
        '2' => $this
          ->t('Contacto complejo'),
      ],
      '#ajax' => [
        'callback' => '::myAjaxCallback', // 
      //'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => 'edit-adjunto', 
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ]
    ];
    $form['adjunto'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'edit-adjunto'],
      ];
      
    $form['descripcion'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Descripcion'),
      '#description' => $this->t('¿En que podemos ayudarle?.'),
      '#required' => TRUE,

    ];
     

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }

    /**
   *
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
   
    $descripcion = $form_state->getValue('descripcion');
    $asunto = $form_state->getValue('asunto');

    if (strlen($descripcion) < 15) {
     
      $form_state->setErrorByName('descripcion', $this->t('La descripcion debe tener al menos 30 caracteres.'));
    }

    if (empty($asunto)){
      // Set an error for the form element with a key of "accept".
      $form_state->setErrorByName('asunto', $this->t('Debe indicar el asunto'));
    }


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
    
      $form_values = $form_state->cleanValues()->getValues();
      $module = 'formulario_contacto';
      $key = 'contact_message';
      $to = 'manolo_25vs@hotmail.com';
      $params = $form_values;
      $language_code = $this->languageManager->getDefaultLanguage()->getId();
      $send_now = TRUE;
      $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, NULL, $send_now);
      if ($result['result'] == TRUE) {
      drupal_set_message($this->t('Your message has been sent.'));
      } else {
      drupal_set_message($this->t('There was a problem sending your messageand it was not sent.'), 'error');
      }
      
      $form_state->setRedirect('<front>');

  } 
  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
   
    $asunto = $form_state->getValue('asunto');
    
   
    if($asunto=="2"){
    
      $form['adjunto'] = array(
       '#type' => 'file',
       '#title' => t('Archivo adjunto'),
       '#attributes' => ['id' => 'edit-adjunto'],  
     
       );
      
      }
      
    
        return $form['adjunto'];
    
      
    
    }
 

}

