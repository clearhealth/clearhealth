<?php
/*****************************************************************************
*       FeeSchedule.php
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


class FeeSchedule extends WebVista_Model_ORM {

	protected $guid;
	protected $name;
	protected $procedureCode;
	protected $fee;
	protected $modifier1;
	protected $modifier1fee;
	protected $modifier2;
	protected $modifier2fee;
	protected $modifier3;
	protected $modifier3fee;
	protected $modifier4;
	protected $modifier4fee;
	protected $insuranceProgramIds;
	protected $dateOfServiceStart;
	protected $dateOfServiceEnd;
	protected $mappedCode;
	protected $dateObsolete;

	protected $_table = 'feeSchedules';
	protected $_primaryKeys = array('guid','procedureCode');

	public function hasConflicts() {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$dateOfServiceStart = $db->quote($this->dateOfServiceStart);
		$dateOfServiceEnd = $db->quote($this->dateOfServiceEnd);
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid != '.$db->quote($this->guid))
				->where('('.$dateOfServiceStart.' >= dateOfServiceStart AND '.$dateOfServiceStart.' <= dateOfServiceEnd) OR ('.$dateOfServiceEnd.' >= dateOfServiceStart AND '.$dateOfServiceEnd.' <= dateOfServiceEnd)');
		$orWhere = array();
		foreach (explode(',',$this->insuranceProgramIds) as $ip) {
			$ip = (int)$ip;
			if (!$ip > 0) continue;
				$orWhere[] = "`insuranceProgramIds` LIKE '%{$ip}%'";
		}
		if (isset($orWhere[0])) {
			$sqlSelect->where(implode(' OR ',$orWhere));
		}
		$sqlSelect->limit(1);
		WebVista::debug($sqlSelect->__toString());
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function persist($single=true) {
		if ($single) return parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) {
			$sql = 'DELETE FROM `'.$this->_table.'` WHERE (`guid` = '.$db->quote($this->guid).')';
			$db->query($sql);
			return $this;
		}
		if (!strlen($this->guid) > 0) {
			$this->guid = str_replace('-','',NSDR::create_guid());
		}
		// Multiple fee schedule cannot be set for the same insurance programs for the same dates of service
		if ($this->hasConflicts()) {
			$error = __('Please choose different insurance programs or date of service.');
			throw new Exception($error);
		}

		if (!$this->dateObsolete || $this->dateObsolete == '0000-00-00') $this->dateObsolete = $this->dateOfServiceEnd;
		$updates = array();
		$fields = array();
		$values = array();
		$columns = array('name','guid','insuranceProgramIds','dateOfServiceStart','dateOfServiceEnd','procedureCode','fee','dateObsolete');
		foreach ($columns as $col) {
			$fields[$col] = '`'.$col.'`';
			$values[$col] = $db->quote($this->$col);
			$updates[$col] = $fields[$col].' = '.$values[$col];
		}
		$values['procedureCode'] = '`code`';
		unset($updates['guid']);
		unset($updates['procedureCode']);
		unset($updates['fee']);
		unset($updates['dateObsolete']);

		// name, guid, insuranceProgramIds, dateOfServiceStart, dateOfServiceEnd
		$sql = 'INSERT INTO `feeSchedules` ('.implode(', ',$fields).')
				SELECT '.implode(', ',$values).' FROM procedureCodesCPT
			ON DUPLICATE KEY UPDATE '.implode(', ',$updates);
		WebVista::debug($sql);
		$db->query($sql);
		return $this;
	}

	public function getIteratorByDistinctGuid() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->group('guid')
				->order('name');
		return $this->getIterator($sqlSelect);
	}

	public function getIteratorByGuid($guid=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($guid === null) {
			$guid = $this->guid;
		}
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid='.$db->quote($guid))
				->order('name');
		return $this->getIterator($sqlSelect);
	}

	public function getDateObsolete() {
		if ($this->dateObsolete == '0000-00-00') return '';
		return date('Y-m-d',strtotime($this->dateObsolete));
	}

	public function getModifiers() {
		$ret = array();
		if (strlen($this->modifier1) > 0) {
			$ret[] = $this->modifier1.' = '.$this->modifier1fee;
		}
		if (strlen($this->modifier2) > 0) {
			$ret[] = $this->modifier2.' = '.$this->modifier2fee;
		}
		if (strlen($this->modifier3) > 0) {
			$ret[] = $this->modifier3.' = '.$this->modifier3fee;
		}
		if (strlen($this->modifier4) > 0) {
			$ret[] = $this->modifier4.' = '.$this->modifier4fee;
		}
		return $ret;
	}

	public function populateByGuid($guid=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($guid === null) {
			$guid = $this->guid;
		}
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid='.$db->quote($guid))
				->limit(1);
		return $this->populateWithSql($sqlSelect);
	}

	public function updateGuid($oldGuid,$newGuid) {
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'UPDATE `'.$this->_table.'` SET `guid` = '.$db->quote($newGuid).' WHERE (`guid` = '.$db->quote($oldGuid).')';
		return $db->query($sql);
	}

	public function setDefaultFee($fee,$guid) {
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'UPDATE `'.$this->_table.'` SET `fee` = '.$db->quote($fee).' WHERE (`guid` = '.$db->quote($guid).')';
		return $db->query($sql);
	}

	public function getFeeScheduleId() {
		return $this->guid;
	}

	public function getIteratorByFilters($filter) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid = '.$db->quote($this->guid))
				->where('procedureCode LIKE '.$db->quote($filter))
				->order('name');
		return $this->getIterator($sqlSelect);
	}

	public static function checkFee($insuranceProgramId,$dateOfVisit,$code) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('procedureCode = '.$db->quote($code))
				->where($db->quote($dateOfVisit).' BETWEEN `dateOfServiceStart` AND `dateOfServiceEnd`')
				->where("insuranceProgramIds LIKE '%".(int)$insuranceProgramId."%'")
				->where('dateObsolete >= '.$db->quote($dateOfVisit))
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = $row;
		}
		return $ret;
	}

	public function populateByGuidCode($guid=null,$code=null) {
		if ($guid === null) $guid = $this->guid;
		if ($code === null) $code = $this->procedureCode;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid='.$db->quote($guid))
				->where('procedureCode='.$db->quote($code));
		return $this->populateWithSql($sqlSelect);
	}

}
