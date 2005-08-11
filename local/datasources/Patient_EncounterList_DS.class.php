<?php

class Patient_EncounterList_DS extends Datasource_sql 
{
	var $_encounterReasons = null;
	
	function Patient_EncounterList_DS($patient_id) {
		settype($patient_id,'int');

		$this->setup(Cellini::dbInstance(), 
			array(
				'cols' 	=> sprintf("date_format(date_of_treatment,'%s') AS date_of_treatment, encounter_reason, b.name building, concat_ws(' ',p.first_name,p.last_name) treating_person, status, encounter_id", DateObject::getFormat()),
				'from' 	=> "encounter e left join buildings b on b.id = e.building_id left join person p on e.treating_person_id = p.person_id",
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
		if (!is_null($this->_subscriberRelationships)) {
			return;
		}
		
		ORDataObject::factory_include('Enumeration');
		$enumArray = Enumeration::loadEnum('encounter_reason',false);
		$this->_encounterReasons = array_flip($enumArray);
	}
	
	
	function encounterReason($id) {
		$this->_lookupEncounterReasonList();
		if (isset($this->_encounterReasons[$id])) {
			return $this->_encounterReasons[$id];
		}
	}
}

