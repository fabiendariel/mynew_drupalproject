<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Attemp to get the fully loaded node object of the viewed page
    $node = \Drupal::routeMatch()->getParameter('node');

    if ( !(is_null($node)) ){
      $nid = $node->id();
    }
    else {
      $nid = 0;
    }
    // Establish the $form render array.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t("We will send updates to the email adress you provide"),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $submitted_email = $form_state->getValue('email');
    if ( !(\Drupal::service('email.validator')->isValid($submitted_email)) ) {
      $form_state->setErrorByName('email', $this->t('%mail is not a valid email', ['%mail' => $submitted_email]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage(t("The form is working! You entered @entry", ['@entry' => $submitted_email ]));
    try {
      // Initiate variables to save

      // Get current user ID
      $uid = \Drupal::currentUser()->id();
      
      // Demonstration for how to load a full user object of the current user
      $full_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

      // Obtain values as entered into the form
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email'); 

      $current_time = \Drupal::time()->getRequestTime();
      
      // Save the values to the database

      // Start ti build a query builder object $query
      $query = \Drupal::database()->insert('rsvplist');

      // Specify the fileds
      $query->fields([
        'uid',
        'nid',
        'email',
        'created',
      ]);

      // Set the values
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
      ]);

      // Execute the query
      $query->execute();

      // Display a success message
      \Drupal::messenger()->addMessage(t("Thanks you for your RSVP!"));
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addError(t("Unable to save your RSVP"));
    }
  }
}
