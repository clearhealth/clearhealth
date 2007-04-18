<?php
//require_once realpath(dirname(__FILE__) . '/../') . '/config.php';

/**
 * This is a pseudo-ORDO to handle abstracting the patient data.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class refInitiator extends ORDataObject 
{
	/**
	 * Stores all the properties of this pseudo-ORDO.
	 *
	 * @var array
	 * @access private
	 */
	var $_corral = array('full_name' => 'N/A');
	
	
	/**
	 * Stores the configuration for the query to get the patient data
	 *
	 * @var array
	 * @access private
	 */
	var $_config = array();
	
	var $date_of_birth = null;
	/**
	 * @todo set all the vars at the beginning so they work off of settings in 
	 *    local/config.php file so it can be more easily ported between CHLCare
	 *    and Clearhealth.
	 */
	function setup($user_id = 0) {
		// todo: abstract out
		$this->_config = array(
			'id' => 'u.user_id',
			'table' => array(
				chlUtility::chlCareTable('users') . ' AS u'
			),
			'columns' => array(
				'u.user_id',
				'CONCAT(u.first_name, " ", u.middle_name, " ", u.last_name) AS full_name'
			)
		);
		
		if ($user_id > 0) {
			$this->set('id', $user_id);
			$this->populate();
		}
	}
	
	
	function get($key) {
		if (!isset($this->_corral[$key])) {
			return "Unknown property: {$key}";
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
		if (is_array($row)) {
			foreach ($row as $key => $value) {
				$this->set($key, $value);
			}
		}
		
		$this->_isPopulated = true;
		return;
	}
	
	function persist() {
		return;
	}
}

