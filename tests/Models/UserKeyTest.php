<?php
/*****************************************************************************
*       UserKeyTest.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

/**
 * Models_TableModels
 */
require_once 'TableModels.php';

/**
 * Person
 */
require_once 'Person.php';

/**
 * User
 */
require_once 'User.php';

/**
 * UserKey
 */
require_once 'UserKey.php';

class Models_UserKeyTest extends Models_ClinicalNoteAbstract {

	protected $_keyValues = array('userId'=>1234,
				      'privateKey'=>'Test Private Key',
				      'publicKey'=>'Test Public Key',
				      'iv'=>'Test IV',);
	protected $_assertMatches = array('privateKey'=>'Test Private Key');
	protected $_assertTableName = 'userKeys'; // value MUST be the same as $_table

	protected $_objects = array();

	public function setUp() {
		parent::setUp();
		$person = new Person();
		$person->last_name = 'Doe';
		$person->first_name = 'John';
		$person->middle_name = 'Dee';
		$person->active = 1;
		$person->persist();
		$this->_objects['person'] = $person;

		$username = 'guest';
		$password = 'password';
		$user = new User();
		$user->userId = $person->personId;
		$user->personId = $person->personId;
		$user->username = $username;
		$user->password = $password;
		$user->persist();
		$this->_objects['user'] = $user;

		$userKey = new UserKey();
		$userKey->userId = $user->userId;
		$userKey->generateKeys($password);
		$userKey->persist();
		$this->_objects['userKey'] = $userKey;
	}

	public function testValidKey() {
		$signature = 'password';
		$objects = array();

		$clinicalNote = new ClinicalNote();
		$clinicalNote->clinicalNoteDefinitionId = $this->_objects['noteDefinition']->clinicalNoteDefinitionId;
		$clinicalNote->personId = $this->_objects['person']->personId;
		$clinicalNote->persist();
		$objects['clinicalNote'] = $clinicalNote;

		$eSig = new ESignature();
		$eSig->signingUserId = $this->_objects['user']->personId;
		$eSig->objectClass = 'ClinicalNote';
		$eSig->objectId = $clinicalNote->clinicalNoteId;
		$eSig->summary = 'Test, One #10026 - Transcription Note **Signed**';
		$eSig->persist();
		$objects['eSig'] = $eSig;

		$esig = new ESignature();
		$esig->eSignatureId = (int)$eSig->eSignatureId;
		$esig->populate();
		$signedDate =  date('Y-m-d H:i:s');
		$esig->signedDateTime = $signedDate;
		$obj = new $esig->objectClass();
		$obj->documentId = $esig->objectId;
		$obj->eSignatureId = $esig->eSignatureId;
		try {
			$esig->sign($obj,$signature);
			$esig->persist();

			$obj->populate();
			$obj->eSignatureId = $esig->eSignatureId;
			$obj->persist();
		}
		catch (Exception $e) {
			$this->assertFalse(true,$e->getMessage());
		}

		$this->assertNotEquals($esig->signature,'');
		$this->_cleanUpObjects($objects);
	}

	public function testInValidKey() {
		$signature = 'invalid_password';
		$objects = array();

		$clinicalNote = new ClinicalNote();
		$clinicalNote->clinicalNoteDefinitionId = $this->_objects['noteDefinition']->clinicalNoteDefinitionId;
		$clinicalNote->personId = $this->_objects['person']->personId;
		$clinicalNote->persist();
		$objects['clinicalNote'] = $clinicalNote;

		$eSig = new ESignature();
		$eSig->signingUserId = $this->_objects['user']->personId;
		$eSig->objectClass = 'ClinicalNote';
		$eSig->objectId = $clinicalNote->clinicalNoteId;
		$eSig->summary = 'Test, One #10026 - Transcription Note **Signed**';
		$eSig->persist();
		$objects['eSig'] = $eSig;

		$esig = new ESignature();
		$esig->eSignatureId = (int)$eSig->eSignatureId;
		$esig->populate();
		$signedDate =  date('Y-m-d H:i:s');
		$esig->signedDateTime = $signedDate;
		$obj = new $esig->objectClass();
		$obj->documentId = $esig->objectId;
		$obj->eSignatureId = $esig->eSignatureId;
		try {
			$esig->sign($obj,$signature);
			$esig->persist();

			$obj->populate();
			$obj->eSignatureId = $esig->eSignatureId;
			$obj->persist();
		}
		catch (Exception $e) {
			$this->assertTrue(true,$e->getMessage());
		}

		$this->assertEquals($esig->signature,'');
		$this->_cleanUpObjects($objects);
	}

}

