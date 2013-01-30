<?php
/*****************************************************************************
*       PatientVisitType.php
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


class PatientVisitType extends WebVista_Model_ORM {

	protected $patientVisitTypeId;
	protected $visitId;
	protected $providerId;
	protected $patientId;
	protected $isPrimary;
	protected $dateTime;

	protected $_primaryKeys = array('patientVisitTypeId');
	protected $_table = 'patientVisitTypes';

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		return parent::persist();
	}

	public function populateWithIds() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('visitId = ?',(int)$this->visitId)
				->where('providerId = ?',(int)$this->providerId)
				->where('patientId = ?',(int)$this->patientId)
				->limit(1);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public function resetPrimaryProvider() {
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'UPDATE `'.$this->_table.'`
				SET isPrimary = 0
				WHERE visitId = '.(int)$this->visitId.' AND patientId = '.(int)$this->patientId;
		return $db->query($sql);
	}

}
