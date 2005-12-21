<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_chronic_code
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: patient_chronic_code
 *
 * @package	com.uversainc.Celini
 */
class PatientChronicCode extends ORDataObject {

	/**#@+
	 * Fields of table: patient_chronic_code mapped to class members
	 */
	var $id		= '';
	var $chronic_care_code		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PatientChronicCode($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'patient_chronic_code';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('patient_id');
	}

	function setup($patientId=0,$chronicCode=0) {
		$this->set('patient_id',$patientId);
		$this->set('chronic_care_code',$chronicCode);
		if ($patientId > 0 && $chronicCode > 0) {
			$this->helper->populateFromDb($this);
		}
	}

	/**#@+
	 * Getters and Setters for Table: patient_chronic_code
	 */

	
	/**
	 * Getter for Primary Key: patient_id
	 */
	function get_patient_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: patient_id
	 */
	function set_patient_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function patientCodeArray($patientId,$includeAll) {
		EnforceType::int($patientId);
		$table = $this->tableName();

		$join = "left";
		if (!$includeAll) {
			$join = "inner";
		}
		$sql = "
		select
			ev.key, ev.value, (pcc.chronic_care_code is not null) status, ev.enumeration_value_id
		from
			enumeration_definition ed
			inner join enumeration_value ev on ed.enumeration_id = ev.enumeration_id and ev.status = 1
			$join join $table pcc on ev.key = pcc.chronic_care_code and pcc.patient_id = $patientId
		where
			ed.name = 'chronic_care_codes' and (pcc.patient_id = $patientId or pcc.patient_id is null)
		";

		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->moveNext();
		}
		return $ret;
	}

	function patientReportArray($patientId) {
		EnforceType::int($patientId);
		$table = $this->tableName();

		$sql = "
		select
			ev.value, ev.enumeration_value_id, rt.report_template_id, rt.report_id, mr.title
		from
			enumeration_definition ed
			inner join enumeration_value ev on ed.enumeration_id = ev.enumeration_id and ev.status = 1
			inner join $table pcc on ev.key = pcc.chronic_care_code
			inner join menu_report mr on mr.menu_id = ev.enumeration_value_id
			inner join report_templates rt on mr.report_template_id = rt.report_template_id
		where
			ed.name = 'chronic_care_codes' and $patientId 
		";

		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			if (!isset($ret[$res->fields['value']])) {
				$ret[$res->fields['value']] = array();
			}
			$ret[$res->fields['value']][] = $res->fields;
			$res->moveNext();
		}
		return $ret;
	}
}
?>
