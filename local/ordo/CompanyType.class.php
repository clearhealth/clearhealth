<?php
/**
 * Object Relational Persistence Mapping Class for table: company_type
 *
 * @package	com.uversainc.clearhealth
 * @author	Uversa Inc.
 */
class CompanyType extends ORDataObject {

	/**#@+
	 * Fields of table: company_type mapped to class members
	 */
	var $company_id		= '';
	var $company_type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'company_type';

	/**
	 * Primary Key
	 */
	var $_key = 'company_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'CompanyType';

	/**
	 * Handle instantiation
	 */
	function CompanyType($db = NULL) {
		parent::ORDataObject($db);
	}

	function setup($id = 0) {
		$this->set('company_id', $id);
		if ($id > 0) {
			$this->populate();
		}
	}

	/**
	 * Pull data for this record from the database
	 */
	function populate() {
		parent::populate('company_id');
	}

	/**
	* Store data to the database
	*/
	function persist() {
		parent::persist();
	}
}
?>
