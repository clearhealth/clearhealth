<?php

/**
 * Serves as an object for returning an array of visit_id's and the date that
 * they occurred as tied to a specific ORDO.
 *
 * This will generally be handed a {@link refPatient}, but can be handed 
 * anything that'll answer back to get('id').
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class refVisitList
{
	/**#@+
	 * @access private
	 */
	var $_db = null;
	var $_corral = array();
	var $_config = array();
	var $_latest = array();
	var $_external = null;
	/**#@-*/
	
	/**
	 * Handle instantiation
	 *
	 * @param object  A ORDO (pseudo) that has an ID.  Generally 
	 *    {@link refPatient}
	 */
	function refVisitList(&$ordo) {
		$this->_external =& $ordo;
		$this->_db =& new clniDB();
		
		// todo: load these settings from config file
		$this->_config['table'] = "encounter";
		$this->_config['id'] = 'patient_id';
		$this->_config['columns'] = array(
			'encounter_id AS id',
			'DATE_FORMAT(date_of_treatment, "%W, ' . DateObject::getFormat() . '") AS date',
			'date_of_treatment'
		);
	}
	
	/**
	 * Return an array of visit_id => visit_date for this ORDO's id.
	 *
	 * @return array
	 */
	function toArray() {
		if (count($this->_corral) == 0) {
			$this->_initArray('_corral');
		}
		return $this->_corral;
	}
	
	/**
	 * Returns the id => date for the latest visit
	 *
	 * @return array
	 */
	function latestVisit() {
		if (count($this->_latest) == 0) {
			$this->_initArray('_latest');
		}
		return $this->_latest;
	}
	
	/** 
	 * Initialize an array with the results of a visit list
	 *
	 * @param  string   The name of the array to initialize
	 * @access private
	 */
	function _initArray($which) {
		$sql = sprintf(
			'SELECT %s FROM %s WHERE %s = "%d" ORDER BY `date_of_treatment` DESC LIMIT 1%s',
			implode(', ', $this->_config['columns']),
			$this->_config['table'],
			$this->_config['id'],
			$this->_external->get('id'),
			$which == '_corral' ? ',100000' : '');
		//var_dump($sql);
		$results = $this->_db->execute($sql);
		while ($results && !$results->EOF) {
			$this->{$which}[$results->fields['id']] = $results->fields['date'];
			$results->moveNext();
		}
	}
}

