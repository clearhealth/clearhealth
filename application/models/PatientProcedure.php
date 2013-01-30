<?php
/*****************************************************************************
*       PatientProcedure.php
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


class PatientProcedure extends WebVista_Model_ORM {

	protected $patientProcedureId;
	protected $code;
	protected $patientId;
	protected $providerId;
	protected $visitId;
	protected $quantity;
	protected $procedure;
	protected $modifiers;
	protected $comments;
	protected $dateTime;
	protected $diagnosisCode1;
	protected $diagnosisCode2;
	protected $diagnosisCode3;
	protected $diagnosisCode4;
	protected $diagnosisCode5;
	protected $diagnosisCode6;
	protected $diagnosisCode7;
	protected $diagnosisCode8;
	protected $modifier1;
	protected $modifier2;
	protected $modifier3;
	protected $modifier4;

	protected $_primaryKeys = array('patientProcedureId');
	protected $_table = 'patientProcedures';

	const ENUM_PARENT_NAME = 'Procedure Preferences';

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		if (!$this->patientProcedureId > 0) $this->populateVisitDiagnoses();
		return parent::persist();
	}

	public function getIteratorByPatientId($patientId=null) {
		if ($patientId === null) $patientId = $this->patientId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('patientId = ?',(int)$patientId);
		return new PatientProcedureIterator($sqlSelect);
	}

	public function getId() {
		return $this->patientProcedureId;
	}

	public function setId($id) {
		$this->patientProcedureId = (int)$id;
	}

	public function getProcedureCode() {
		return $this->code;
	}

	public function setProcedureCode($code) {
		$this->code = $code;
	}

	public function setUnsetDiagnosis($code,$state) {
		return $this->_setUnsetDiagnosisModifier('diagnosisCode',8,$code,$state);
	}

	public function setUnsetModifier($code,$state) {
		return $this->_setUnsetDiagnosisModifier('modifier',4,$code,$state);
	}

	protected function _setUnsetDiagnosisModifier($prefix,$ctr,$code,$state) {
		$ret = false;
		for ($i = 1; $i <= $ctr; $i++) {
			$field = $prefix.$i;
			if ($state) { // add
				if (!strlen($this->$field) > 0) {
					$this->$field = $code;
					$ret = true;
					break;
				}
			}
			else { // remove
				if ($this->$field == $code) {
					$this->$field = '';
					$ret = true;
					break;
				}
			}
		}
		return $ret;
	}

	public function getBaseFee() {
		$claimLine = $this->getClaimLine();
		$baseFee = (float)$claimLine->baseFee;
		$units = (int)$claimLine->units;
		if ($units > 1) {
			$baseFee /= $units;
		}
		return (float)$baseFee;
	}

	public function getAdjustedFee() {
		return (float)$this->getClaimLine()->adjustedFee;
		$claimLine = $this->getClaimLine();
		$adjustedFee = (float)$claimLine->adjustedFee;
		$units = (int)$claimLine->units;
		if ($units > 1) {
			$adjustedFee /= $units;
		}
		return (float)$adjustedFee;
	}

	public function getClaimLine() {
		$claimLine = new ClaimLine();
		$claimLine->populateWithPatientProcedure($this,null);
		return $claimLine;
	}

	public function populateVisitDiagnoses() {
		$iterator = new PatientDiagnosisIterator();
		$iterator->setFilters(array('visitId'=>$this->visitId));
		foreach ($iterator as $patientDiagnosis) {
			$this->setUnsetDiagnosis($patientDiagnosis->code,true);
		}
	}

}
