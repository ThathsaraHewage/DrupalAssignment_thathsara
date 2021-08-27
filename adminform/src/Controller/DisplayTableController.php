<?php

namespace Drupal\adminform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class DisplayTableController
 * @package Drupal\mymodule\Controller
 */
class DisplayTableController extends ControllerBase
{

  public function index()
  {
    //create table header
    $header_table = array(
      'id' => t('ID'),
      'title' => t('Title'),
      'view' => t('Open & View'),
      'delete' => t('Delete'),
      'edit' => t('Edit'),
    );


    // get data from database
    $query = \Drupal::database()->select('mytable', 'm');
    $query->fields('m', ['id', 'title']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {
      $url_delete = Url::fromRoute('adminform.delete_form', ['id' => $data->id], []);
      $url_edit = Url::fromRoute('adminform.add_form', ['id' => $data->id], []);
      $url_view = Url::fromRoute('adminform.show_data', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
      $linkView = Link::fromTextAndUrl('View', $url_view);

      //get data
      $rows[] = array(
        'id' => $data->id,
        'title' => $data->title,
        'view' => $linkView,
        'delete' => $linkDelete,
        'edit' =>  $linkEdit,
      );

    }
    // render table
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found'),
    ];
    return $form;

  }

}