<?php
$loader->requireOnce('/datasources/Revisions_DS.class.php');

/**
 * Object Relational Persistence Mapping Class for table: storables
 *
 * @package com.clear-health.docsmart
 *
 * @todo Change "webdavname" to something like "label" as it isn't WebDAV specific.
 * @todo prefix with DocSmart
 */
class Storable extends ORDataObject {

	/**#@+
	 * Fields of table: storables mapped to class members
	 */
	var $storable_id		= '';
	var $type		= '';
	var $mimetype		= '';
	var $filename		= '';
	var $storage_type		= '';
	var $create_date = '';
	var $last_revision_id = 0;
	var $webdavname = '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'storables';

	/**
	 * Primary Key
	 */
	var $_key = 'storable_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Storable';

	/**
	 * Handle instantiation
	 */
	function Storable() {
		parent::ORDataObject();
	}
	
	/**
	 * Returns current revision
	 *
	 * @return Revision
	 */
	function getCurrentRevision() {
		if(!$this->last_revision_id) {
			return Celini::newOrdo('Revision');
		}
		$revision =& Celini::newOrdo('Revision', $this->last_revision_id);
		return $revision;
	}
	
	/**
	 * Save tag for specified storable
	 *
	 * @param array $data
	 * @return integer
	 */
	function setTag($data = array()) {
		if(!isset($data['storable_id'])) {
			return false;
		}
		$tag =& Celini::newOrdo('Tag');
		$tag->populate_array($data);
		$tag->populate("tag");
		$tag->persist();
		
		$tagStorable =& Celini::newOrdo('TagStorable');
		$tagStorable->populate_array(array('tag_id' => $tag->tag_id, 'storable_id' => $data['storable_id']));
		$tagStorable->persist();
		return $tag->tag_id;
	}

	/**
	 * Sets create data if it doesn't exist
	 *
	 */
	function persist() {
		if(!$this->create_date) {
			$this->create_date = date("Y-m-d H:i:s");
		}
		if(empty($this->webdavname)) {
			$this->webdavname = $this->filename;
		}
		parent::persist();
	}		

}
?>
