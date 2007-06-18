<?php
$loader->requireOnce('includes/ParticipationProgramAbstract.class.php');
/**
 * Object Relational Persistence Mapping Class for table: participation_program_basic
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class ParticipationProgramClinic extends ParticipationProgramAbstract {

	/**#@+
	 * Fields of table: participation_program_basic mapped to class members
	 */
	var $person_program_id		= '';
	var $eligibility	= '';
	var $initial_date	= '';
	var $recent_date	= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'participation_program_clinic';

	/**
	 * Primary Key
	 */
	var $_key = 'person_program_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'ParticipationProgramBasic';

	/**
	 * Handle instantiation
	 */
	function ParticipationProgramBasic() {
		parent::ORDataObject();
	}

	function actionOptions() {
		$c = new Controller();
		$c->view->path = APP_ROOT . "/user/participation_templates/";
		$c->view->assign("options",$this);
		$em =& Celini::enumManagerInstance();
                $c->view->assign_by_ref('em',$em);
		return $c->view->render("clinic.html");
        }

        function processOptions($data) {
		$this->populateArray($data);
		$this->persist();
        }

	function administrationLink($id) {
		return '';
	}

        function _createTables() {
         $sql = "
		CREATE TABLE IF NOT EXISTS `participation_program_clinic` (
		`person_program_id` BIGINT NOT NULL ,
		`eligibility` TINYINT NOT NULL ,
		PRIMARY KEY ( `person_program_id` )
		);
                ";
	 $this->dbHelper->execute($sql);
        }
	function set_initial_date($date) {
		$this->_setDate('initial_date',$date);
	}
	function set_recent_date($date) {
		$this->_setDate('recent_date',$date);
	}
	function get_initial_date() {
		return $this->_getDate('initial_date');
	}
	function get_recent_date() {
		return $this->_getDate('recent_date');
	}
	
}
?>
