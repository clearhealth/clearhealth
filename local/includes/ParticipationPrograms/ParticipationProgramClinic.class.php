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
		$this->popularArray($data);
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

	
}
?>
