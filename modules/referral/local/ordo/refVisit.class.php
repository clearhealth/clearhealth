<?php
/**
 * This is a pseudo-ORDO to handle abstracting the patient data.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class refVisit extends ORDataObject 
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
	
	var $date_of_visit = null;
	var $clinic_name = null;
	var $defaultValueValue = '';
	var $_unknownMessage = 'Not Set';
	
	/**
	 * @todo set all the vars at the beginning so they work off of settings in 
	 *    local/config.php file so it can be more easily ported between CHLCare
	 *    and Clearhealth.
	 */
	function setup($visit_id = 0) {
		// todo: abstract out
		$this->_config = array(
			'id' => 'e.encounter_id',
			'table' => array(
				'encounter AS e',
				'LEFT JOIN person AS p ON(e.treating_person_id = p.person_id)',
				'LEFT JOIN buildings AS c ON e.building_id = c.id'
			),
			'columns' => array(
				'e.encounter_id AS id',
				'e.date_of_treatment AS date_of_visit',
				'e.encounter_reason',
				'CONCAT(p.first_name, " ", p.middle_name, " ", p.last_name) AS provider_name',
				'c.name AS clinic_name',
			)
		);
		
		if ($visit_id > 0) {
			$this->set('id', $visit_id);
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
			$this->_corral['id']);
		$result = $this->dbHelper->execute($sql);
		$row = $result->fields;
		if (!is_array($row)) {
			return;
		}
		foreach ($row as $key => $value) {
			$this->set($key, $value);
		}
		
		$this->_populated = true;
		return;
	}
	
	
	
	function persist() {
		return;
	}
	
	function get_date_of_visit() {
		if (is_null($this->date_of_visit)) {
			$this->date_of_visit =& TimestampObject::create($this->_corral['date_of_visit']);
		}
		
		return $this->_getDate('date_of_visit');
	}
	
	function get_diagnoses() {
		return $this->_cleanUpStoredArray('diagnoses');
	}
	
	function get_procedures() {
		return $this->_cleanUpStoredArray('procedures');
	}
	
	/**
	 * Clean up the mess that is their procedures list
	 *
	 * This was loosely copied from visit_info.php.  They store the info in the
	 * DB as an imploded array() and apparently have a bug where the first entry
	 * can be empty.
	 *
	 * @access private
	 */
	function _cleanUpStoredArray($name) {
		$exploded = explode(':|:', $this->_corral[$name]);
		if ($exploded[0] = '') {
			array_shift($exploded);
		}
		
		return $exploded;
	}
	
	function get_id() {
		return $this->_corral['id'];
	}
	
	function value($key) {
		$value = parent::value($key);
		if ((!$this->isPopulated() && empty($value)) || $value == $this->_unknownMessage) {
			return $this->defaultValueValue;
		}
		return $value;
	}

}

