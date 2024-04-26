<?php

/**
 * @file
 * Provide site administrators with a list of all the RSVP List signups
 * so they know who is attending their events
 */

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class ReportController extends ControllerBase {

  /**
   * Gets and returns all RSVPs for all nodes
   * These are returned as an associative array, with each row
   * containing the username, the node title and email of RSVP
   *
   * @return array|null
   */
  protected function load() {
    try {
      $database = \Drupal::database();
      $select_query = $database->select('rsvplist', 'r');

      // Join the user table to get creator's username
      $select_query->join('users_fileds_data', 'u', 'r.uid = u.uid');
      // Join the node table to get the event's name
      $select_query->join('node_field_data', 'n', 'n.nid = r.nid');

      // Select these specific fileds for the output
      $select_query->addField('u', 'name', 'username');
      $select_query->addField('n', 'title');
      $select_query->addField('u', 'email');

      $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      return $entries;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Create the RSVPList report page
   *
   * @return array
   */
  public function report()
  {
    $content = [];

    $content['message'] = [
      '#markup' => $this->t('Below is a list of all Event RSVPs including username, email address and the name of the event they will attending'),
    ];
    $headers = [
      $this->t('Username'),
      $this->t('Event'),
      $this->t('Email'),
    ];

    $table_rows = $this->load();

    $content['table'] = [
      '#title' => 'table',
      '#header' => $headers,
      '#rows' => $table_rows,
      '#empty' => $this->t("No entries available"),
    ];

    $content['#cache']['max-age'] = 0;

    return $content;
  }
}