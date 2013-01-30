<?php
/*****************************************************************************
*       PatientAllergy.php
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


class PatientAllergy extends WebVista_Model_ORM {

	protected $patientAllergyId;
	protected $causativeAgent;
	protected $patientId;
	protected $observerId;
	protected $reactionType;
	protected $observed;
	protected $severity; // Severe, Moderate, Mild
	protected $dateTimeReaction;
	protected $dateTimeCreated;
	protected $symptoms;
	protected $comments;
	protected $noKnownAllergies;
	protected $enteredInError;
	protected $drugAllergy;
	protected $active;

	protected $_primaryKeys = array('patientAllergyId');
	protected $_table = 'patientAllergies';

	const ENUM_REACTION_TYPE_PARENT_NAME = 'Reaction Type Preferences';
	const ENUM_SEVERITY_PARENT_NAME = 'Severity Preferences';
	const ENUM_SYMPTOM_PARENT_NAME = 'Symptom Preferences';

	public function getIteratorByPatient($patientId = null,$enteredInError = 0,$noKnownAllergies = null,$active = null) {
		if ($patientId === null) {
			$patientId = $this->patientId;
		}
		$filters = array();
		$filters['patientId'] = (int)$patientId;
		$filters['enteredInError'] = (int)$enteredInError;
		if ($noKnownAllergies !== null) {
			$filters['noKnownAllergies'] = (int)$noKnownAllergies;
		}
		if ($active !== null) {
			$filters['active'] = (int)$active;
		}
		$iterator = new PatientAllergyIterator();
		$iterator->setFilters($filters);
		return $iterator;
	}

	public static function countAllergiesByPatientId($patientId) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = 0;
		$sql = 'SELECT COUNT(patientId) AS ctr FROM patientAllergies WHERE enteredInError = 0 AND patientId = '.(int)$patientId;
		if ($row = $db->fetchRow($sql)) {
			$ret = $row['ctr'];
		}
		return $ret;
	}

	public function populateByNoKnowAllergies($patientId = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($patientId === null) {
			$patientId = $this->patientId;
		}
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('patientId = ?',(int)$patientId)
				->where('noKnownAllergies = 1')
				->limit(1);
		//trigger_error($sqlSelect->__toString());
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public static function listMedicationAllergies($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patientAllergies',array('symptoms','reactionType','active','causativeAgent','dateTimeReaction'))
				->joinLeft('chmed.basemed24','chmed.basemed24.md5 = patientAllergies.drugAllergy',array('rxnorm_cuid'))
				->where('patientAllergies.active = 1')
				->where('patientAllergies.noKnownAllergies = 0')
				->group('patientAllergies.patientAllergyId');
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'dateRange':
					$dateRange = explode(';',$value);
					$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
					$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
					//$sqlSelect->where("patientAllergies.dateTimeCreated BETWEEN '{$start}' AND '{$end}'");
					$sqlSelect->where("patientAllergies.dateTimeReaction BETWEEN '{$start}' AND '{$end}'");
					break;
				case 'patientId':
					$sqlSelect->where('patientAllergies.patientId = ?',(int)$value);
					break;
			}
		}
		trigger_error($sqlSelect->__toString());
		$rows = array();
		$stmt = $db->query($sqlSelect);
		while ($row = $stmt->fetch()) {
			$rows[] = $row;
		}
		return $rows;
	}

}
