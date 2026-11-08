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

namespace Civi\Api4\Action\Contact;

use Civi\Api4\Generic\Result;

/**
 * Generate a security checksum for anonymous access to CiviCRM.
 *
 * @method $this setContactId(int $cid) Set contact ID (required)
 * @method int getContactId() Get contact ID param
 * @method $this setChecksum(string $checksum) Set checksum param (required)
 * @method string getChecksum() Get checksum param
 * @method $this setScopes(array $scopes) Set scopes param
 * @method array getScopes() Get scopes param
 * @method $this setAcceptUnscoped(?bool $acceptUnscoped) Set acceptUnscoped param
 * @method bool getAcceptUnscoped() Get acceptUnscoped param
 */
class ValidateChecksum extends \Civi\Api4\Generic\AbstractAction {

  /**
   * ID of contact
   *
   * @var int
   * @required
   */
  protected $contactId;

  /**
   * Value of checksum
   *
   * @var string
   * @required
   */
  protected $checksum;

  /**
   * The checksum should be valid for one of these scopes. If empty, only unscoped checksums are valid.
   *
   * @var array
   */
  protected $scopes = [];

  /**
   * Whether an unscoped checksum is also valid when scopes are set. Defaults to the checksum_accept_unscoped setting.
   *
   * @var bool
   */
  protected $acceptUnscoped = NULL;

  /**
   * @param \Civi\Api4\Generic\Result $result
   */
  public function _run(Result $result) {
    $result[] = [
      'valid' => \CRM_Contact_BAO_Contact_Utils::validChecksum($this->contactId, $this->checksum, $this->scopes ?? [], $this->acceptUnscoped),
    ];
  }

}
