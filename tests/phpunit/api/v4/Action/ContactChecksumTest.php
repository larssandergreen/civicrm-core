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
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */


namespace api\v4\Action;

use api\v4\Api4TestBase;
use Civi\Api4\Contact;
use Civi\Test\TransactionalInterface;

/**
 * @group headless
 */
class ContactChecksumTest extends Api4TestBase implements TransactionalInterface {

  public function testGetChecksum(): void {
    $contact = Contact::create(FALSE)
      ->addValue('first_name', 'Check')
      ->addValue('last_name', 'Sum')
      ->addChain('cs', Contact::getChecksum()->setContactId('$id')->setTtl(500), 0)
      ->execute()
      ->first();

    $result = Contact::validateChecksum()
      ->setContactId($contact['id'])
      ->setChecksum($contact['cs']['checksum'])
      ->execute()
      ->first();

    $this->assertTrue($result['valid']);
  }

  public function testValidateChecksum(): void {
    $cid = Contact::create(FALSE)
      ->addValue('first_name', 'Checker')
      ->addValue('last_name', 'Sum')
      ->execute()
      ->first()['id'];

    $goodCs = \CRM_Contact_BAO_Contact_Utils::generateChecksum($cid, NULL, 500);
    $badCs = \CRM_Contact_BAO_Contact_Utils::generateChecksum($cid, strtotime('now - 1 week'), 1);

    $result1 = Contact::validateChecksum()
      ->setContactId($cid)
      ->setChecksum($goodCs)
      ->execute()
      ->first();
    $this->assertTrue($result1['valid']);

    $result2 = Contact::validateChecksum()
      ->setContactId($cid)
      ->setChecksum($badCs)
      ->execute()
      ->first();
    $this->assertFalse($result2['valid']);
  }

  public function testScopedChecksum(): void {
    $cid = $this->createTestRecord('Contact', ['first_name' => 'Scoped'])['id'];
    $scopedCs = Contact::getChecksum(FALSE)
      ->setContactId($cid)
      ->setScope('recur')
      ->execute()
      ->first()['checksum'];

    $validForScope = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($scopedCs)
      ->setScopes(['recur'])
      ->execute()
      ->first()['valid'];
    $this->assertTrue($validForScope);

    $validForListedScope = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($scopedCs)
      ->setScopes(['profile', 'recur'])
      ->execute()
      ->first()['valid'];
    $this->assertTrue($validForListedScope);

    $validForOtherScopes = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($scopedCs)
      ->setScopes(['profile', 'event'])
      ->setAcceptUnscoped(TRUE)
      ->execute()
      ->first()['valid'];
    $this->assertFalse($validForOtherScopes);

    $validWithoutScope = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($scopedCs)
      ->execute()
      ->first()['valid'];
    $this->assertFalse($validWithoutScope);
  }

  public function testValidateChecksumAcceptUnscoped(): void {
    $cid = $this->createTestRecord('Contact', ['first_name' => 'Unscoped'])['id'];
    $unscopedCs = Contact::getChecksum(FALSE)
      ->setContactId($cid)
      ->execute()
      ->first()['checksum'];

    $validWhenAccepted = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($unscopedCs)
      ->setScopes(['recur'])
      ->setAcceptUnscoped(TRUE)
      ->execute()
      ->first()['valid'];
    $this->assertTrue($validWhenAccepted);

    $validWhenRejected = Contact::validateChecksum(FALSE)
      ->setContactId($cid)
      ->setChecksum($unscopedCs)
      ->setScopes(['recur'])
      ->setAcceptUnscoped(FALSE)
      ->execute()
      ->first()['valid'];
    $this->assertFalse($validWhenRejected);
  }

  public function testValidateChecksumWithNullScopes(): void {
    $cid = $this->createTestRecord('Contact', ['first_name' => 'Unscoped'])['id'];
    $unscopedCs = Contact::getChecksum(FALSE)
      ->setContactId($cid)
      ->execute()
      ->first()['checksum'];

    $valid = civicrm_api4('Contact', 'validateChecksum', [
      'contactId' => $cid,
      'checksum' => $unscopedCs,
      'scopes' => NULL,
      'checkPermissions' => FALSE,
    ])->first()['valid'];
    $this->assertTrue($valid);
  }

}
