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

/**
 * Upgrade logic for the 6.22.x series.
 *
 * Each minor version in the series is handled by either a `6.22.x.mysql.tpl` file,
 * or a function in this class named `upgrade_6_22_x`.
 * If only a .tpl file exists for a version, it will be run automatically.
 * If the function exists, it must explicitly add the 'runSql' task if there is a corresponding .mysql.tpl.
 *
 * This class may also implement `setPreUpgradeMessage()` and `setPostUpgradeMessage()` functions.
 */
class CRM_Upgrade_Incremental_php_SixTwentyTwo extends CRM_Upgrade_Incremental_Base {

  /**
   * Upgrade step; adds tasks including 'runSql'.
   *
   * @param string $rev
   *   The version number matching this function name
   */
  public function upgrade_6_22_alpha1($rev): void {
    $this->addTask(ts('Upgrade DB to %1: SQL', [1 => $rev]), 'runSql', $rev);
    $this->addTask('Keep accepting unscoped checksums on existing sites', 'acceptUnscopedChecksums');
  }

  /**
   * New installs reject unscoped checksums where a scope is required.
   * Existing sites keep accepting them, so links already sent keep working.
   *
   * @return bool
   */
  public static function acceptUnscopedChecksums(): bool {
    $domainIDs = \Civi\Api4\Domain::get(FALSE)->addSelect('id')->execute()->column('id');
    foreach ($domainIDs as $domainID) {
      Civi::settings($domainID)->set('checksum_accept_unscoped', TRUE);
    }
    return TRUE;
  }

}
