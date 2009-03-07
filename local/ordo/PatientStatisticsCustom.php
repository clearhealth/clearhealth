<?php
/**
 * @package	com.clearhealth
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('ordo/MergeDecorator.class.php');
/**#@-*/

class PatientStatisticsCustom extends MergeDecorator {
	var $_table = 'patient_statistics_custom';
	var $_key = 'person_id';
	var $_internalName='PatientStatisticsCustom';

	var $person_id = '';
	var $is_research_patient = '';
	var $research_patient_status = '';
	var $nurse_tracking_research_patient = '';
	var $informed_consent_signed = '';
	var $is_bio_banking = '';
	var $bio_bank_lab_draw = '';
	var $green_chart = '';
	var $hospital_chair = '';
	var $nqri_code = '';
	var $research_stem_cell = '';
	var $research_business_office = '';

	function PatientStatisticsCCI($db = null) {
		$this->ORDataObject($db);
		$this->merge('patient_statistics',ORDataObject::factory('PatientStatisticsNormal'));
	}

	function setup($id = 0) {
		$this->patient_statistics->set('id',$id);
		$this->set('id',$id);
		if ($id > 0) {
			$this->populate();
		}
	}

	function getEthnicityList() {
		return $this->patient_statistics->getEthnicityList();
	}
	function getRaceList() {
		return $this->patient_statistics->getRaceList();
	}
	function getLanguageList() {
		return $this->patient_statistics->getLanguageList();
	}
	function getMigrantStatusList() {
		return $this->patient_statistics->getMigrantStatusList();
	}
	function getIncomeList() {
		return $this->patient_statistics->getIncomeList();
	}

	/**
	 * Persist the data
	 */
	function persist() {
		$ret = $this->mergePersist('person_id');
		if ($this->get('id') == 0) {
			$this->set('id',$this->patient_statistics->get('id'));
		}
		if (parent::persist()) {
			return true;
		}
		return $ret;
	}

	/**
	 * Load the data from the db
	 */
	function populate() {
		parent::populate('person_id');
		$this->mergePopulate('person_id');
	}

}
