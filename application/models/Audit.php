<?php
/*****************************************************************************
*       Audit.php
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


class Audit extends WebVista_Model_ORM {
	protected $auditId;
	protected $objectClass;
	protected $objectId;
	protected $userId;
	protected $patientId;
	protected $type;
	protected $message;
	protected $dateTime;
	protected $startProcessing;
	protected $endProcessing;
	protected $ipAddress;

	protected $_table = 'audits';
	protected $_primaryKeys = array('auditId');
	protected $_persistMode = WebVista_Model_ORM::INSERT;
	protected $_ormPersist = false;
	public static $_processedAudits = false;
	public static $_synchronousAudits = false;

	public function persist() {
		if (!strlen($this->ipAddress) > 0 && isset($_SERVER['REMOTE_ADDR'])) {
			$this->ipAddress = $_SERVER['REMOTE_ADDR'];
		}
		if (self::$_processedAudits) {
			$this->startProcessing = date('Y-m-d H:i:s');
			$this->endProcessing = date('Y-m-d H:i:s');
		}
		if (!$this->userId) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			if ($identity) $this->userId = $identity->personId;
		}
		if (self::$_synchronousAudits || $this->_ormPersist) {
			return parent::persist();
		}
		if ($this->shouldAudit()) {
			$sql = $this->toSQL();
			AuditLog::appendSql($sql);
		}
	}

	public function getIteratorByCurrentDate() {
		$currentDate = date('Y-m-d');
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('dateTime LIKE ?',$currentDate.'%')
			       ->order('dateTime');
		return $this->getIterator($dbSelect);
	}

	/* Using this method when you need to create an audit and audit values for a non ORM event.
	 * Audit query is being executed rather than returning an sql INSERT statements
	 */
	public static function persistManualAuditArray(Array $data) {
		$db = Zend_Registry::get('dbAdapter');
		$seqTable = Zend_Registry::get('config')->audit->sequence->table;
		if (!isset($data['auditId']) || !(int)$data['auditId'] > 0) {
			$data['auditId'] = WebVista_Model_ORM::nextSequenceId($seqTable);
		}
		$audit = array();
		$audit['auditId'] = (int)$data['auditId'];
		$audit['objectClass'] = isset($data['objectClass'])?$data['objectClass']:'';
		$audit['objectId'] = isset($data['objectId'])?$data['objectId']:'';
		$audit['userId'] = isset($data['userId'])?$data['userId']:'';
		$audit['patientId'] = isset($data['patientId'])?$data['patientId']:'';
		$audit['type'] = isset($data['type'])?$data['type']:WebVista_Model_ORM::REPLACE;
		$audit['message'] = isset($data['message'])?$data['message']:'';
		$audit['dateTime'] = isset($data['dateTime'])?$data['dateTime']:date('Y-m-d H:i:s');
		$audit['startProcessing'] = isset($data['startProcessing'])?$data['startProcessing']:'';
		$audit['endProcessing'] = isset($data['endProcessing'])?$data['endProcessing']:'';
		$audit['ipAddress'] = isset($data['ipAddress'])?$data['ipAddress']:'127.0.0.1';
		$db->insert('audits',$audit);

		if (isset($data['auditValues']) && is_array($data['auditValues'])) {
			foreach ($data['auditValues'] as $key=>$value) {
				if (is_array($value)) $value = serialize($value);
				$auditValue = array();
				$auditValue['auditValueId'] = WebVista_Model_ORM::nextSequenceId($seqTable);
				$auditValue['auditId'] = $audit['auditId'];
				$auditValue['key'] = $key;
				$auditValue['value'] = (string)$value;
				$db->insert('auditValues',$auditValue);
			}
		}
	}

	public function getIteratorByDateRange($dateStart,$dateEnd, $startTime = '00:00:00', $endTime = '23:59:59') {
		$start = date('Y-m-d',strtotime($dateStart));
		$end = date('Y-m-d',strtotime($dateEnd));
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
				->joinLeft('user', 'user.person_id = audits.userId')
				->joinLeft('person', 'person.person_id = audits.patientId')
			       ->where("dateTime >= '{$start} {$startTime}' AND dateTime <= '{$end} {$endTime}'")
			       ->order('dateTime DESC');
		return $this->getIterator($dbSelect);
	}

	public function getObjectType() {
		$ret = $this->message;
		if ($this->type == self::DELETE) {
			$ret = 'Deleted';
		}
		else if ($this->type != 0 && $this->objectId) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
				       ->from($this->_table,'auditId')
				       ->where('objectId = ?',(string)$this->objectId)
				       ->order('dateTime DESC');
                	$dbStmt = $db->query($dbSelect);
			$rowCount = $dbStmt->rowCount();
			if ($rowCount > 0) {
				if ($rowCount == 1) {
					$new = true;
				}
				else {
					for ($i = 0; $i < $rowCount; $i++) {
						$row = $dbStmt->fetch(null,null,$i);
						if ($i == 0 && $row['auditId'] == $this->auditId) {
							$new = true;
							break;
						}
						else if ($i != 0) {
							$new = false;
							break;
						}
					}
				}
				$ret = ($new)?'Created':'Modified';
			}
		}
		return $ret;
	}

}
