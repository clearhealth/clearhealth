<?php
$loader->requireOnce('/datasources/Revisions_DS.class.php');
/**
 * Object Relational Persistence Mapping Class for table: revisions
 *
 * @package com.clear-health.docsmart
 *
 * @todo prefix with DocSmart
 */
class Revision extends ORDataObject {

	/**#@+
	 * Fields of table: revisions mapped to class members
	 */
	var $revision_id		= '';
	var $storable_id		= '';
	var $revision		= '';
	var $create_date		= '';
	var $user_id		= '';
	var $filesize = 0;
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'revisions';

	/**
	 * Primary Key
	 */
	var $_key = 'revision_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Revision';

	/**
	 * Handle instantiation
	 */
	function Revision() {
		parent::ORDataObject();
	}

	/**
	 * Calculates revision number and saves the revision to db
	 *
	 * @return integer
	 */
	function persist() {
		if(!$this->storable_id) {
			return false;
		}
		if(!$this->revision_id) {
			$revisions = new Revisions_DS("revisions.storable_id='".$this->storable_id."'");
			$this->revision = count($revisions->toArray()) + 1;
		}
		return parent::persist();
	}
	
	/**
	 * Drop revision without deletion relationship
	 *
	 * @return boolean
	 */
	function drop() {
		$sql = "DELETE FROM revisions WHERE revision_id = '". $this->revision_id."'";
		$this->_db->Execute($sql);
		$sql = "DELETE FROM revisions_db WHERE revision_id = '". $this->revision_id."'";
		$this->_db->Execute($sql);
		return true;
	}
}
?>
