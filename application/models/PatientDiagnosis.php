<?php
/*****************************************************************************
*       PatientDiagnosis.php
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


class PatientDiagnosis extends WebVista_Model_ORM {

	protected $patientDiagnosisId;
	protected $code;
	protected $patientId;
	protected $providerId;
	protected $visitId;
	protected $dateTime;
	protected $addToProblemList;
	protected $isPrimary;
	protected $diagnosis;
	protected $comments;

	protected $_primaryKeys = array('patientDiagnosisId');
	protected $_table = 'patientDiagnosis';

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		if (!$this->patientDiagnosisId > 0) $this->updateVisitProcedures();
		return parent::persist();
	}

	public function populate() {
		return parent::populate();
		if ($this->code != $this->patientId) return parent::populate();
		$x = explode(';',$this->code);
		$this->code = $x[0];
		if (isset($x[1])) {
			$this->patientId = (int)$x[1];
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('code = ?',(string)$this->code)
				->where('patientId = ?',(string)$this->patientId)
				->limit(1);
		$ret = $this->populateWithSql($sqlSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

	public function getPersonId() {
		return $this->patientId;
	}

	public function updateVisitProcedures() {
		$iterator = new PatientProcedureIterator();
		$iterator->setFilters(array('visitId'=>$this->visitId));
		foreach ($iterator as $patientProcedure) {
			$patientProcedure->setUnsetDiagnosis($this->code,true);
			$patientProcedure->persist();
		}
	}

}
