<?php
/**
 * Object Relational Persistence Mapping Class for table: tags_storables
 *
 * @package com.uversainc.docsmart
 *
 * @todo Since this only exists as a sub-part of the Storable, it should be renamed to StorableTag
 *     or it should be moved to a completely generic Tag ordo with an external_type and external_id.
 * @todo prefix with DocSmart
 */
class TagStorable extends ORDataObject {

	/**#@+
	 * Fields of table: tags_storables mapped to class members
	 */
	var $storable_id		= '';
	var $tag_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'tags_storables';

	/**
	 * Primary Key
	 */
	var $_key = 'tag_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'TagStorable';

	/**
	 * Handle instantiation
	 */
	function TagStorable() {
		parent::ORDataObject();
	}

	/**
	 * Bulk delete tags assigned to storable from the db
	 *
	 * @param array $idList
	 */
	function bulkDrop($idList = array(), $storableId = null) {
		if(count($idList) == 0 || !isset($storableId)) {
			return;
		}
		$sql = "DELETE FROM ".$this->_table." WHERE ".$this->_key." IN (".implode(",",$idList).") AND storable_id=".$storableId;
		$this->dbHelper->execute($sql);
	}
	
}
?>
