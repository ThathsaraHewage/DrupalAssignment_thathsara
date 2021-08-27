<?php

namespace Drupal\adminform\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;


class AdminForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'adminform_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($_GET['id'])) {
      $query = $conn->select('mytable', 'm')
        ->condition('id', $_GET['id'])
        ->fields('m');
      $data = $query->execute()->fetchAssoc();
    }

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('title'),
      '#required' => true,
      '#size' => 60,
      '#default_value' => (isset($data['title'])) ? $data['title'] : '',
      '#maxlength' => 128,
      '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
    ];
    $form['picture'] = array(
      '#title' => t('picture'),
      '#description' => $this->t('Choose Icon gif png jpg jpeg'),
      '#type' => 'managed_file',
      '#required' => true,
      '#default_value' => (isset($data['fid'])) ? [$data['fid']] : [],
      '#upload_location' => 'public://images/',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg')),
    );
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('save'),
      '#buttom_type' => 'primary'
    ];
    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (is_numeric($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('Error, The Title must be a String !'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $picture = $form_state->getValue('picture');
    $data = array(
      'title' => $form_state->getValue('title'),
      'fid' => $picture[0],
    );

    // save file as Permanent
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();

    if (isset($_GET['id'])) {
      // update data in database
      \Drupal::database()->update('mytable')->fields($data)->condition('id', $_GET['id'])->execute();
    } else {
      // insert data to database
      \Drupal::database()->insert('mytable')->fields($data)->execute();
    }

    // show message and redirect to list page
    \Drupal::messenger()->addStatus('Data Saved Succesfully !');
    $url = new Url('adminform.display_data');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }


}