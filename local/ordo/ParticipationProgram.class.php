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

	
}
?>
