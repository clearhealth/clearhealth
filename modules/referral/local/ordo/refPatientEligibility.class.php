<?php

class refPatientEligibility extends ORDataObject
{
	/**#@+
	 * A property of this ORDO
	 *
	 * @access protected
	 */
	var $refpatient_eligibility_id = '';
	
	/**
	 * The default eligibility setting.
	 *
	 * This should match one of the keys from refEligibility 
	 *
	 * @var int
	 */
	var $eligibility = '3';
	var $eligible_thru = '';
	var $federal_poverty_level = '';
	var $patient_id = '';
	var $refprogram_id = '';
	/**#@-*/
	
	var $_table = 'refpatient_eligibility';
	
	var $_enumList = array(
		'federal_poverty_level' => 'federal_poverty_level'
	);
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', $id);
			$this->populate();
		}
	}
	
	function setupByProgramAndPatient($program_id, $patient_id) {
		$this->set('refprogram_id', $program_id);
		$this->set('patient_id', $patient_id);
		
		$sql = sprintf('SELECT * FROM %s WHERE refprogram_id = %d AND patient_id = %d',
			$this->_table,
			$program_id,
			$patient_id);
		$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
	}
	
	
	function populate() {
		parent::populate('refpatient_eligibility_id');
	}
	
	function get_id() {
		return $this->get('refpatient_eligibility_id');
	}
	
	function set_id($value) {
		$this->set('refpatient_eligibility_id', $value);
	}
	
	function get_eligible_thru() {
		return $this->_getDate('eligible_thru');
	}
	
	function set_eligible_thru($value) {
		$this->_setDate('eligible_thru', $value);
	}
	
	/**
	 * Generic setter for {@link $federal_poverty_level}
	 *
	 * Required because FPL can be null and set() doesn't know how to handle that.
	 *
	 * @access  protected
	 */
	function set_federal_poverty_level($value) {
		$this->federal_poverty_level = $value;
	}
}
