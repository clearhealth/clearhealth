<?php
/*****************************************************************************
*       GeneralAlertTest.php
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
 * Patient
 */
require_once 'Patient.php';

/**
 * Audit
 */
require_once 'Audit.php';

/**
 * GeneralAlertHandler
 */
require_once 'GeneralAlertHandler.php';

/**
 * Processingd
 */
require_once 'Processingd.php';

/**
 * ProcessAlert
 */
require_once 'ProcessAlert.php';

/**
 * GeneralAlert
 */
require_once 'GeneralAlert.php';

class Models_GeneralAlertTest extends Models_ClinicalNoteAbstract {

	protected $_keyValues = array('message'=>'Test Message',
				      'urgency'=>'Test Urgency',
				      'status'=>1,);
	protected $_assertMatches = array('message'=>'Test Message');
	protected $_assertTableName = 'generalAlerts'; // value MUST be the same as $_table

	protected $_noteTemplate;
	protected $_noteDefinition;

	public function setUp() {
		parent::setUp();
		$this->_noteTemplate = $this->_objects['noteTemplate'];
		$this->_noteDefinition = $this->_objects['noteDefinition'];
	}

	public function tearDown() {
		$this->_objects['noteTemplate'] = $this->_noteTemplate;
		$this->_objects['noteDefinition'] = $this->_noteDefinition;
		parent::tearDown();
	}

	public function testUserLoggedOut() {
		$this->_objects = GeneralAlertHandler::generateUserLoggedOut();

		$objects = array();
		$db = Zend_Registry::get('dbAdapter');

		$audit = new Audit();
		$audit->_ormPersist = true;
		$audit->objectClass = 'Logout';
		$audit->objectId = 0;
		$audit->dateTime = date('Y-m-d H:i:s');
		$audit->type = WebVista_Model_ORM::REPLACE;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->persist();
		$objects['audit'] = $audit;

		$clinicalNote = new ClinicalNote();
		$clinicalNote->personId = $this->_objects['person']->person_id;
		$clinicalNote->visitId = 100;
		$clinicalNote->clinicalNoteDefinitionId = $this->_noteDefinition->clinicalNoteDefinitionId;
		$clinicalNote->dateTime = date('Y-m-d H:i:s');
		$clinicalNote->eSignatureId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$clinicalNote->persist();
		$objects['clinicalNote'] = $clinicalNote;

		$eSign = new ESignature();
		// cleanup all generalAlerts
		$db->query('DELETE FROM '.$eSign->_table);

		$eSign->dateTime = date('Y-m-d H:i:s');
		$eSign->signedDateTime = '0000-00-00 00:00:00';
		$eSign->signingUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$eSign->objectId = $clinicalNote->clinicalNoteId;
		$eSign->objectClass = get_class($clinicalNote);
		$eSign->summary = ' **Unsigned**';
		$eSign->persist();
		$objects['eSignature'] = $eSign;

		// cleanup all generalAlerts
		$generalAlert = new GeneralAlert();
		$db->query('DELETE FROM '.$generalAlert->_table);

		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessAlert());
		$process->startProcessing(false);

		$generalAlertIterator = $generalAlert->getIterator();
		$ctr = 0;
		foreach ($generalAlertIterator as $alert) {
			$objects['generalAlert'.$ctr++] = $alert;
		}
		$this->assertEquals($ctr,1,'No alert created even with signed items');

		$this->_cleanUpObjects($objects);
	}

	public function testSignedItem() {
		$this->_objects = GeneralAlertHandler::generateClinicalNoteHandler();

		$objects = array();
		$db = Zend_Registry::get('dbAdapter');

		$clinicalNote = new ClinicalNote();
		$clinicalNote->personId = $this->_objects['person']->person_id;
		$clinicalNote->visitId = 100;
		$clinicalNote->clinicalNoteDefinitionId = $this->_noteDefinition->clinicalNoteDefinitionId;
		$clinicalNote->dateTime = date('Y-m-d H:i:s');
		$clinicalNote->eSignatureId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$clinicalNote->persist();
		$objects['clinicalNote'] = $clinicalNote;

		$eSign = new ESignature();
		// cleanup all generalAlerts
		$db->query('DELETE FROM '.$eSign->_table);

		$eSign->dateTime = date('Y-m-d H:i:s');
		$eSign->signedDateTime = date('Y-m-d H:i:s');
		$eSign->signingUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$eSign->objectId = $clinicalNote->clinicalNoteId;
		$eSign->objectClass = get_class($clinicalNote);
		$eSign->summary = ' **Unsigned**';
		$eSign->persist();
		$objects['eSignature'] = $eSign;

		// cleanup all generalAlerts
		$generalAlert = new GeneralAlert();
		$db->query('DELETE FROM '.$generalAlert->_table);

		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessAlert());
		$process->startProcessing(false);

		$generalAlertIterator = $generalAlert->getIterator();
		$ctr = 0;
		foreach ($generalAlertIterator as $alert) {
			$objects['generalAlert'.$ctr++] = $alert;
		}
		$this->assertEquals($ctr,0,'Alert created even no unsigned items');

		$this->_cleanUpObjects($objects);
	}

	public function testUnsignedItem() {
		$this->_objects = GeneralAlertHandler::generateClinicalNoteHandler();

		$objects = array();
		$db = Zend_Registry::get('dbAdapter');

		$clinicalNote = new ClinicalNote();
		$clinicalNote->personId = $this->_objects['person']->person_id;
		$clinicalNote->visitId = 100;
		$clinicalNote->clinicalNoteDefinitionId = $this->_noteDefinition->clinicalNoteDefinitionId;
		$clinicalNote->dateTime = date('Y-m-d H:i:s');
		$clinicalNote->persist();
		$objects['clinicalNote'] = $clinicalNote;

		$eSign = new ESignature();
		// cleanup all generalAlerts
		$db->query('DELETE FROM '.$eSign->_table);

		$eSign->dateTime = date('Y-m-d H:i:s');
		$eSign->signedDateTime = '0000-00-00 00:00:00';
		$eSign->signingUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$eSign->objectId = $clinicalNote->clinicalNoteId;
		$eSign->objectClass = get_class($clinicalNote);
		$eSign->summary = ' **Unsigned**';
		$eSign->persist();
		$objects['eSignature'] = $eSign;

		// cleanup all generalAlerts
		$generalAlert = new GeneralAlert();
		$db->query('DELETE FROM '.$generalAlert->_table);

		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessAlert());
		$process->startProcessing(false);

		$generalAlertIterator = $generalAlert->getIterator();
		$ctr = 0;
		foreach ($generalAlertIterator as $alert) {
			$objects['generalAlert'.$ctr++] = $alert;
		}
		$this->assertEquals($ctr,1,'No alert created even with signed items');

		$this->_cleanUpObjects($objects);
	}

}
