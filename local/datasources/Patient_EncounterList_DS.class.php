<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

class Patient_EncounterList_DS extends Datasource_sql 
{
	var $_encounterReasons = null;
	
	
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Patient_EncounterList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	
	function Patient_EncounterList_DS($patient_id) {
		settype($patient_id,'int');

		$this->setup(Celini::dbInstance(), 
			array(
				'cols' 	=> '
					DATE_FORMAT(date_of_treatment,"' . DateObject::getFormat() . '") AS date_of_treatment, 
					encounter_reason,
					b.name building,
					CONCAT_WS(" ",p.first_name,p.last_name) AS treating_person,
					e.status, 
					encounter_id',
				'from' 	=> '
					encounter AS e
					LEFT JOIN buildings AS b ON(b.id = e.building_id)
					LEFT JOIN person AS p ON(e.treating_person_id = p.person_id)',
				'where' => " patient_id = $patient_id",
				'orderby' => 'date_of_treatment DESC'
			),
			array('date_of_treatment' => 'Date of Treatment','encounter_reason' => 'Reason', 'building' => 'Building', 'treating_person' => 'Treated By', 'status' => 'Status'/*,'encounter_id' => "Encounter Id"*/));

		$this->orderHints['building'] = 'b.name';
		$this->registerFilter('encounter_reason',array(&$this,'encounterReason'));
		//echo $ds->preview();
	}
	
	
	/**
	 * Load encounter_reason enum if necessary
	 *
	 * @access private
	 */
	function _lookupEncounterReasonList() {
		if (!is_null($this->_encounterReasons)) {
			return;
		}
		
		$enum = ORDataObject::factory('Enumeration');
		$this->_encounterReasons = $enum->get_enum_list('encounter_reason');
	}
	
	
	function encounterReason($id) {
		$this->_lookupEncounterReasonList();
		if (isset($this->_encounterReasons[$id])) {
			return $this->_encounterReasons[$id];
		}
	}
}

