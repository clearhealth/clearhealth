<?php
$loader->requireOnce('includes/chlUtility.class.php');
/**
 * This is a pseudo-ORDO to handle abstracting the patient data.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class refPatient extends ORDataObject 
{
	/**
	 * Stores all the properties of this pseudo-ORDO.
	 *
	 * @var array
	 * @access private
	 */
	var $_corral = array();
	
	
	/**
	 * Stores the configuration for the query to get the patient data
	 *
	 * @var array
	 * @access private
	 */
	var $_config = array();
	
	var $date_of_birth = null;
	
	var $_unknownMessage = 'n/a';
	
	/**
	 * @todo set all the vars at the beginning so they work off of settings in 
	 *    local/config.php file so it can be more easily ported between CHLCare
	 *    and Clearhealth.
	 */
	function setup($patient_id = 0) {
		// todo: abstract out
		$this->_config = array(
			'id' => 'p.patient_id',
			'table' => array(
				 chlUtility::chlCareTable('patients') . ' AS p',
				'JOIN ' . chlUtility::chlCareTable('patients_basic') . ' AS pb USING (patient_id)'
			),
			'columns' => array(
				'p.patient_id AS id',
				'first AS first_name',
				'last AS last_name',
				'middle AS middle_name',
				'CONCAT(p.first, " ", p.middle, " ", p.last) AS full_name',
				// this chl_id field doesn't currently have anything in it, so
				// I'm relying on the patient_id
				//'pb.chl_pid AS software_id', 
				'p.patient_id + 10000 AS chl_id',
				'pb.home_phone AS phone_number',
				'p.address_line1',
				'p.address_line2',
				'p.city',
				'p.state',
				'p.zip_code',
				'CONCAT(p.address_line1, IF(p.address_line2 IS NOT NULL, CONCAT("' . "\n" . '", p.address_line2), ""), "", p.city, ", ", p.state, ", ", p.zip_code) AS print_address',
				'p.ss_number',
				'p.sex',
				'p.date_of_birth'
			)
		);
		
		if ($patient_id > 0) {
			$this->set('id', $patient_id);
			$this->populate();
		}
	}
	
	
	function get($key) {
		if (!isset($this->_corral[$key])) {
			return $this->_unknownMessage;
		}
		
		$accessor = 'get_' . $key;
		if (method_exists($this, $accessor)) {
			return $this->$accessor();
		}
		
		return $this->_corral[$key];
	}
	
	function set($key, $value) {
		$this->_corral[$key] = $value;
	}
	
	function exists($key) {
		return isset($this->_corral[$key]);
	}
	
	
	function populate() {
		$sql = sprintf('SELECT %s FROM %s WHERE %s = "%d"',
			implode(', ', $this->_config['columns']),
			implode(' ', $this->_config['table']),
			$this->_config['id'],
			$this->get('id'));
		$result = $this->dbHelper->execute($sql);
		if (!$result || $result->EOF) {
			return;
		}
		$row = $result->fields;
		foreach ($row as $key => $value) {
			$this->set($key, $value);
		}
		
		$this->_isPopulated = true;
		return;
	}
	
	
	
	function persist() {
		return;
	}
	
	function get_date_of_birth() {
		if (is_null($this->date_of_birth)) {
			$this->date_of_birth =& TimestampObject::create($this->_corral['date_of_birth']);
		}
		
		return $this->_getDate('date_of_birth');
	}
	
	function get_print_address() {
		return nl2br($this->_corral['print_address']);
	}
	
	/**
	 * Returns ID for this object
	 *
	 * {@internal Must override the default or it gets caught in a loop}
	 */
	function get_id() {
		return $this->_corral['id'];
	}
}

