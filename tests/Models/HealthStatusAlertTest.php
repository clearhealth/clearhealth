<?php
/*****************************************************************************
*       HealthStatusAlertTest.php
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
 * DataIntegrationDatasource
 */
require_once 'DataIntegrationDatasource.php';

/**
 * DataIntegrationTemplate
 */
require_once 'DataIntegrationTemplate.php';

/**
 * Handler
 */
require_once 'Handler.php';

/**
 * HealthStatusHandlerPatient
 */
require_once 'HealthStatusHandlerPatient.php';

/**
 * HealthStatusAlert
 */
require_once 'HealthStatusAlert.php';

/**
 * Processingd
 */
require_once 'Processingd.php';

/**
 * ProcessHSA
 */
require_once 'ProcessHSA.php';

class Models_HealthStatusAlertTest extends Models_TableModels {

	protected $_keyValues = array('message'=>'Test Message',
				      'status'=>'Test Status',
				      'personId'=>1234,
				      'handlerId'=>5678,);
	protected $_assertMatches = array('message'=>'Test Message');
	protected $_assertTableName = 'healthStatusAlerts'; // value MUST be the same as $_table

	public function testTetanusShots() {
		$this->_objects = HealthStatusHandler::generateTestTetanus();
		$objects = array();
		$timeTrigger = date('h:i A',strtotime('-10 minutes'));
		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessHSA($timeTrigger));
		$process->startProcessing(false);

		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlert->populateByHandlerPatientId($this->_objects['healthStatusHandler']->healthStatusHandlerId,$this->_objects['patient']->personId);
		$objects['healthStatusAlert'] = $healthStatusAlert;

		$this->_cleanUpObjects($objects);

		$this->assertTrue((strlen($healthStatusAlert->status) > 0),'No alert created');
		$this->assertEquals($healthStatusAlert->status,'active','Alert is not active');
		$this->assertEquals(date('Y-m-d',strtotime($healthStatusAlert->dateDue)),date('Y-m-d',strtotime('+1 month')),'Due date is invalid');
	}

	public function testTetanusShotsWithoutAlert() {
		$this->_objects = HealthStatusHandler::generateTestTetanus();
		$objects = array();
		$timeTrigger = date('h:i A',strtotime('+10 minutes'));
		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessHSA($timeTrigger));
		$process->startProcessing(false);

		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlert->populateByHandlerPatientId($this->_objects['healthStatusHandler']->healthStatusHandlerId,$this->_objects['patient']->personId);
		$objects['healthStatusAlert'] = $healthStatusAlert;

		$this->_cleanUpObjects($objects);

		$this->assertTrue((!strlen($healthStatusAlert->status) > 0),'Alert has been created');
	}

	public function testTetanusShotsDoublePost() {
		$this->_objects = HealthStatusHandler::generateTestTetanus();
		$objects = array();
		$timeTrigger = date('h:i A',strtotime('-10 minutes'));
		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess(new ProcessHSA($timeTrigger));
		// first call
		$process->startProcessing(false);
		// second call
		$process->startProcessing(false);

		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlertIterator = $healthStatusAlert->getIteratorByPatientId($this->_objects['patient']->personId);
		$ctr = 0;
		foreach ($healthStatusAlertIterator as $alert) {
			$objects['healthStatusAlert'.$ctr++] = $alert;
		}

		$this->_cleanUpObjects($objects);

		$this->assertNotEquals($ctr,2,'Two alerts created');
	}

	public function testTetanusShotsFulfill() {
		$this->_objects = HealthStatusHandler::generateTestTetanus();
		$objects = array();
		$timeTrigger = date('h:i A',strtotime('-10 minutes'));
		$processHSA = new ProcessHSA($timeTrigger);
		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess($processHSA);
		$process->startProcessing(false);

		$date = date('m/d/Y',strtotime('+5 weeks')); // 1 month and 1 week
		$processHSA->setCurrentDate($date);
		$process->clearProcesses();
		$process->addProcess($processHSA);
		$process->startProcessing(false);

		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlert->populateByHandlerPatientId($this->_objects['healthStatusHandler']->healthStatusHandlerId,$this->_objects['patient']->personId);
		$objects['healthStatusAlert'] = $healthStatusAlert;

		$this->_cleanUpObjects($objects);

		$this->assertTrue((strlen($healthStatusAlert->status) > 0),'No alert created');
		$this->assertEquals($healthStatusAlert->status,'fulfilled','Alert is not fulfilled');
		$this->assertEquals(date('Y-m-d',strtotime($healthStatusAlert->dateDue)),date('Y-m-d',strtotime('+1 month')),'Due date is invalid');
	}

	public function testTetanusShotsFulfillWithAudit() {
		$this->_objects = HealthStatusHandler::generateTestTetanus();
		$objects = array();
		$timeTrigger = date('h:i A',strtotime('-10 minutes'));
		$processHSA = new ProcessHSA($timeTrigger);
		$process = Processingd::getInstance();
		$process->clearProcesses();
		$process->addProcess($processHSA);
		$process->startProcessing(false);

		$audit = new Audit();
		$audit->_ormPersist = true;
		$audit->objectClass = get_class($this->_objects['medication']);
		$audit->objectId = $this->_objects['medication']->medicationId;
		$audit->dateTime = date('Y-m-d H:i:s');
		$audit->type = WebVista_Model_ORM::REPLACE;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->persist();
		$objects['audit'] = $audit;

		$time = date('h:i A',strtotime('-2 minutes')); // advance the time to 2 minutes due to fast processing
		$processHSA->setCurrentTime($time);
		$process->clearProcesses();
		$process->addProcess($processHSA);
		$process->startProcessing(false);

		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlert->populateByHandlerPatientId($this->_objects['healthStatusHandler']->healthStatusHandlerId,$this->_objects['patient']->personId);
		$objects['healthStatusAlert'] = $healthStatusAlert;

		$this->_cleanUpObjects($objects);

		$this->assertTrue((strlen($healthStatusAlert->status) > 0),'No alert created');
		$this->assertEquals($healthStatusAlert->status,'fulfilled','Alert is not fulfilled');
		$this->assertEquals(date('Y-m-d',strtotime($healthStatusAlert->dateDue)),date('Y-m-d',strtotime('+1 month')),'Due date is invalid');
	}

}

