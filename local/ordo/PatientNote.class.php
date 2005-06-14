<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_note
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: patient_note
 *
 * @package	com.uversainc.clearhealth
 */
class PatientNote extends ORDataObject {

	/**#@+
	 * Fields of table: patient_note mapped to class members
	 */
	var $id			= '';
	var $patient_id		= '';
	var $user_id		= '';
	var $priority		= '';
	var $note_date		= '';
	var $note		= '';
	var $deprecated = '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PatientNote($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'patient_note';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Patient_note with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('patient_note_id');
	}

	/**#@+
	 * Getters and Setters for Table: patient_note
	 */

	
	/**
	 * Getter for Primary Key: patient_note_id
	 */
	function get_patient_note_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: patient_note_id
	 */
	function set_patient_note_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	/**
	* Deprecate this note
	*/
	function deprecate()
	{
		$this->_execute("UPDATE {$this->_prefix}$this->_table SET deprecated=1 WHERE patient_note_id = ". (int)$this->id);
	}

	function listNotes($patient_id,$deprecated) {
		$ds =& new Datasource_sql();

		if ($deprecated==0) {
		  $labels = array('priority' => 'P','note_date' => 'Date', 'username' => 'User', 'note' => 'Note','options'=>'Options');
		} else {
		  $labels = array('priority' => 'P','note_date' => 'Date', 'username' => 'User', 'note' => 'Note');
		}

		$ds->setup($this->_db,array(
				'cols' 	=> "priority, note_date, note, username, patient_note_id",
				'from' 	=> "$this->_table n left join user u on u.user_id = n.user_id",
				'where' => " patient_id = $patient_id AND deprecated=$deprecated",
			),
			$labels
		);

		$ds->addOrderRule('priority','DESC',0);
		$ds->addOrderRule('note_date','DESC',1);

		$ds->registerFilter('note',array($this,'multiLineFilter'));
		$ds->registerFilter('priority',array($this,'colorLineFilter'));
		$ds->template['options'] = "<a href='".Cellini::link('dashboard/depnote/'.$patient_id)."pnote_id={\$patient_note_id}&process=true'>Deprecate</a>";
		return $ds;
	}

	function priorityList() {
		return array(5=>5,4=>4,3=>3,2=>2,1=>1);
	}

	function multiLineFilter($content) {
		if (strstr($content,"\n")) {
			$pos = strpos($content,"\n");
			$line1 = trim(substr($content,0,$pos));
			$rest = trim(substr($content,($pos+1)));
			return "<pre><span style='border-bottom: dotted 1px blue;' onmouseover=\"this.parentNode.getElementsByTagName('div').item(0).style.display = 'block'; this.style.borderBottom = '';\">$line1</span><div style='display:none;'>$rest</div></pre>";
		}
		return $content;
	}

	function colorLineFilter($content) {
		switch($content) {
			case 5:
				$color = "red";
				break;
			case 4: 
				$color = "yellow";
				break;
			default:
				$color = "transparent";
				break;
		}

		return "<div style='background-color: $color; font-weight: bold; margin-left: -5px; text-align:  center;'>$content</div>";
	}
}
?>
