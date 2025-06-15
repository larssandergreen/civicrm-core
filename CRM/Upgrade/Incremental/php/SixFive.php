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
 * Upgrade logic for the 6.5.x series.
 *
 * Each minor version in the series is handled by either a `6.5.x.mysql.tpl` file,
 * or a function in this class named `upgrade_6_5_x`.
 * If only a .tpl file exists for a version, it will be run automatically.
 * If the function exists, it must explicitly add the 'runSql' task if there is a corresponding .mysql.tpl.
 *
 * This class may also implement `setPreUpgradeMessage()` and `setPostUpgradeMessage()` functions.
 */
class CRM_Upgrade_Incremental_php_SixFive extends CRM_Upgrade_Incremental_Base {

  /**
   * Upgrade step; adds tasks including 'runSql'.
   *
   * @param string $rev
   *   The version number matching this function name
   */
  public function upgrade_6_5_alpha1($rev): void {
    $this->addTask(ts('Upgrade DB to %1: SQL', [1 => $rev]), 'runSql', $rev);
    $this->addTask('Update UF Group table to remove FK for add_to_group_id', 'removeUFGroupFK');
    $this->addTask('Update UF Group table to allow multiple values in add_to_group_id', 'alterSchemaField', 'UFGroup', 'add_to_group_id', [
      'title' => ts('Add Contact To Group IDs'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'EntityRef',
      'description' => ts('List of groups to add contacts to'),
      'serialize' => CRM_Core_DAO::SERIALIZE_SEPARATOR_BOOKEND,
      'input_attrs' => [
        'label' => ts('Add Contact To Groups'),
        'multiple' => TRUE,
      ],
      'pseudoconstant' => [
        'table' => 'civicrm_group',
        'key_column' => 'id',
        'name_column' => 'name',
        'label_column' => 'title',
        'prefetch' => 'disabled',
      ],
    ]);
    $this->addTask('Update UF Group table to serialize add_to_group_id values', 'serializeAddToGroupId');
  }

  public static function removeUFGroupFK(): bool {
    CRM_Core_BAO_SchemaHandler::safeRemoveFK('civicrm_uf_group', 'FK_civicrm_uf_group_add_to_group_id');
    return TRUE;
  }

  public static function serializeAddToGroupId(): bool {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_uf_group
      SET add_to_group_id = CONCAT(%1,add_to_group_id,%1)
      WHERE add_to_group_id IS NOT NULL AND LEFT(add_to_group_id,1) <> %1', [
        1 => [CRM_Core_DAO::VALUE_SEPARATOR, 'String'],
      ]);
    return TRUE;
  }

}
