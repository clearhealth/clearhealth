<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment_template
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: appointment_template
 *
 * @package	com.uversainc.Celini
 */
class AppointmentTemplate extends ORDataObject {

	/**#@+
	 * Fields of table: appointment_template mapped to class members
	 */
	var $id		= '';
	var $name	= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function AppointmentTemplate($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'appointment_template';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('appointment_template_id');
	}

	/**#@+
	 * Getters and Setters for Table: appointment_template
	 */

	
	/**
	 * Getter for Primary Key: appointment_template_id
	 */
	function get_appointment_template_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: appointment_template_id
	 */
	function set_appointment_template_id($id)  {
		$this->id = $id;
	}

	function get_length() {
		$sql = "select sum(length) l from occurence_breakdown where occurence_id = ".$this->dbHelper->quote($this->get('id'));
		$res = $this->dbHelper->execute($sql);
		return $res->fields['l'];
	}

	/**#@-*/

	function breakdownArray() {
		$ob =& Celini::newOrdo('OccurenceBreakdown');
		$ret = $ob->breakdownArray($this->get('id'));
		return $ret;
	}

	/**
	 * This gives you an array that is the same as doing an fillTemplate and then a breakdownSum on the occurenceId without touchin the db
	 */
	function breakdownSum($users) {
		if (count($users) === 0) {
			return array();
		}
		$breakdowns = $this->breakdownArray();

		$ret = array();
		foreach($breakdowns as $breakdown) {
			if(isset($users[$breakdown['occurence_breakdown_id']])) {
				$user_id = $users[$breakdown['occurence_breakdown_id']];
				if (!isset($ret[$user_id])) {
					$ret[$user_id] = 0;
				}
				$ret[$user_id] += $breakdown['length'];
			}
			else {
				echo ('A provider is required for each slot of the appointment, press the back button select a provider for each slot');
				die();
			}
		}
		return $ret;
	}

	function fillTemplate($occurence_id,$users) {
		$breakdowns = $this->breakdownArray();

		$this->resetTemplate($occurence_id,$breakdowns);
		foreach($breakdowns as $key => $breakdown) {
			$b =& Celini::newOrdo('OccurenceBreakdown',array($occurence_id,$breakdown['index']),'ByIndex');
			$b->set('user_id',$users[$breakdown['occurence_breakdown_id']]);
			$b->set('offset',$breakdown['offset']);
			$b->set('length',$breakdown['length']);
			$b->set('title',$breakdown['title']);
			$b->persist();
		}
	}

	function resetTemplate($occurence_id,$breakdowns = false) {
		if ($breakdowns === false) {
			$breakdowns = $this->breakdownArray();
		}
		$sql = 'select count(*) c from occurence_breakdown where occurence_id = '.$this->dbHelper->quote($occurence_id);
		$res = $this->dbHelper->execute($sql);
		if ($res->fields['c'] != count($breakdowns)) {
			$sql = 'delete from occurence_breakdown where occurence_id = '.$this->dbHelper->quote($occurence_id);
			$this->dbHelper->execute($sql);
		}
	}
}
?>
