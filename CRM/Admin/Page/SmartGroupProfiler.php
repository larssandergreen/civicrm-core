<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

 // What about smart groups that depend on other smart groups? Need to see how these are handled are they updated or do they use the cached version?
 // If use cached, then could cache all first so that they show the true time for each. If not, then just note it, but that would be crazy if not.

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * Page for displaying Administer CiviCRM Control Panel.
 */
class CRM_Admin_Page_SmartGroupProfiler extends CRM_Core_Page {

  /**
   * Run page.
   *
   * @return string
   */
  public function run() {
    $groups = \Civi\Api4\Group::get()
      ->addSelect('id', 'title')
      ->addWhere('saved_search_id', 'IS NOT EMPTY')
      ->addWhere('is_hidden', '=', FALSE)
      ->addWhere('is_active', '=', TRUE)
      ->execute();
    foreach ($groups as $group) {
      $startTime = microtime(TRUE);
      CRM_Contact_BAO_GroupContactCache::add([$group['id']]);
      $endTime = microtime(TRUE);
      $output[] = ['id' => $group['id'], 'title' => $group['title'], 'duration' => $endTime - $startTime];
    }
    $this->assign('rows', $output);
    return parent::run();
  }

}

