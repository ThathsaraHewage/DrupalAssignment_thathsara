<?php

namespace Drupal\adminform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

/**
 * Class MydataController
 * @package Drupal\mymodule\Controller
 */
class DataController extends ControllerBase
{

  /**
   * @return array
   */
  public function show($id)
  {

    $conn = Database::getConnection();

    $query = $conn->select('mytable', 'm')
      ->condition('id', $id)
      ->fields('m');
    $data = $query->execute()->fetchAssoc();
    $title = $data['title'];
    $file =File::load($data['fid']);
    $picture = $file->url();

    return [
      '#type' => 'markup',
      '#markup' => "<h1>$title</h1><br>
                    <img src='$picture' width='100' height='100' /> <br>"
    ];
  }

}