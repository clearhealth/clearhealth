<?php
/**
 * Object Relational Persistence Mapping Class for table: building_program_identifier
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: building_program_identifier
 *
 * @package	com.uversainc.freestand
 */
class BuildingProgramIdentifier extends ORDataObject {

	/**#@+
	 * Fields of table: building_program_identifier mapped to class members
	 */
	var $building_id	= '';
	var $program_id		= '';
	var $identifier		= '';
	var $x12_sender_id  = '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function BuildingProgramIdentifier($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'building_program_identifier';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Building_program_identifier with this
	 */
	function setup($building_id = 0, $program_id = 0) {
		if ($building_id > 0 && $program_id > 0) {
			$this->set('building_id',$building_id);
			$this->set('program_id',$program_id);
			$this->populate();
		}
	}

	function getBuildingList() {
		$b =& ORDataOBject::factory('Building');
		return $b->getBuildingList();
	}

	
	/**
	 * @todo This needs to be pulled out of this ORDO and made into its own DS.
	 */
	function &getDs($company_id) {
		settype($company_id,'int');
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "p.name program, b.name building, i.identifier, i.building_id, i.program_id, i.x12_sender_id",
				'from' 	=> "$this->_table i inner join buildings b on b.id = i.building_id inner join insurance_program p on i.program_id = p.insurance_program_id",
				'where' => " p.company_id = $company_id"
			),
			array('program' => 'Program Name','building' => 'Building', 'identifier' => 'Identifier', 'x12_sender_id' => 'E-Billing Sender ID'));

		return $ds;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate(true);
	}

	/**#@+
	 * Getters and Setters for Table: building_program_identifier
	 */
	/**#@-*/
}
?>
