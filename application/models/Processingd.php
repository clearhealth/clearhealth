<?php
/*****************************************************************************
*       Processingd.php
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
 * Processing daemon
 * Process HL7 messages, user alerts and health status alerts
 */
class Processingd {

	protected static $_instance = null;
	protected $_sleepInterval = 30;
	protected $_processes = array();
	protected $_audits = array();

	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {}

	private function __clone() {}

	public function setSleepInterval($interval) {
		$this->_sleepInterval = (int)$interval;
	}

	public function getSleepInterval($interval) {
		$this->_sleepInterval = (int)$interval;
	}

	public function addProcess(ProcessAbstract $process) {
		$this->_processes[] = $process;
	}

	public function getProcess($key=null) {
		if ($key === null) {
			return $this->_processes;
		}
		$ret = null;
		if (array_key_exists($key,$this->_processes)) {
			$ret = $this->_processes[$key];
		}
		return $ret;
	}

	public function setProcess($key,ProcessAbstract $process) {
		if (array_key_exists($key,$this->_processes)) {
			$this->_processes[$key] = $process;
		}
	}

	public function setProcesses(Array $processes) {
		$this->_processes = $processes;
	}

	public function clearProcesses() {
		$this->_processes = array();
	}

	protected function _populateAudits() {
		$audit = new Audit();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($audit->_table)
			       ->where("startProcessing = '0000-00-00 00:00:00'")
			       ->orWhere("endProcessing = '0000-00-00 00:00:00'");
		$this->_audits = $audit->getIterator($dbSelect);
	}


	public function startProcessing($infinite = true) {

		$infinite = (bool)$infinite;
		do {
			$ctr = ePrescribe::pull();
			WebVista::log('ePrescribe messages received: '.$ctr);
			$this->_populateAudits();
			foreach ($this->_audits as $audit) {
				WebVista::log('start processing auditId:['.$audit->auditId.'], objectClass:['.$audit->objectClass.'], objectId:['.$audit->objectId.']');
				$audit->_persistMode = WebVista_Model_ORM::REPLACE;
				$audit->_ormPersist = true;
				if ($audit->startProcessing == '0000-00-00 00:00:00') {
					$audit->startProcessing = date('Y-m-d H:i:s');
					$audit->persist();
				}

				$processResult = true;
				foreach ($this->_processes as $process) {
					$process->preProcess($audit);
					$result = $process->process($audit);
					$process->postProcess($audit);
					$processResult &= $result;
					if ($result) {
						continue;
					}
				}

				if ($processResult) {
					$audit->endProcessing = date('Y-m-d H:i:s');
					$audit->persist();
				}
			}

			// invoke extra process that are not dependent on Audits
			foreach ($this->_processes as $process) {
				$process->extraProcess();
			}

			sleep($this->_sleepInterval);
		} while ($infinite);
	}

}
