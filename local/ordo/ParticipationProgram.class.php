<?php
/**
 * Object Relational Persistence Mapping Class for table: participation_program
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class ParticipationProgram extends ORDataObject {

	/**#@+
	 * Fields of table: participation_program mapped to class members
	 */
	var $participation_program_id		= '';
	var $class = 'basic';
	var $type = '';
	var $name		= '';
	var $description		= '';
	var $form_id = '';
	var $adhoc = 0;
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'participation_program';

	/**
	 * Primary Key
	 */
	var $_key = 'participation_program_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'ParticipationProgram';

	/**
	 * Handle instantiation
	 */
	function ParticipationProgram() {
		parent::ORDataObject();
	}
	
	function persist() {
		$sec =& $GLOBALS['security'];
                if($this->get('participation_program_id') < 1) {
                        parent::persist();
                        $id = $sec->add_object('resources','Participation Program - '.$this->get('name'),$this->get('participation_program_id'),15,0,'axo');
                        $sec->add_group_object(16,'resources',$this->get('participation_program_id'),'axo');
                } else {
                        $id = $sec->get_object_id($this->get('participation_program_id'),'participation_programs','axo');
                        $sec->edit_object($id,'resources','Participation Program - '.$this->get('name'),$this->get('id'),15,0,'axo');
                        parent::persist();
                }
	}

	
}
?>
