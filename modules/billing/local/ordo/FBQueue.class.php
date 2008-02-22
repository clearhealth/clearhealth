<?php
/**
 * Object Relational Persistence Mapping Class for table: fbqueue
 *
 * @package	com.clear-health.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class FBQueue extends ORDataObject {

	/**#@+
	 * Fields of table: fbqueue mapped to class members
	 */
	var $queue_id		= '';
	var $name		= '';
	var $max_items		= '';
	var $num_items		= '';
	var $ids		= array();
	/**#@-*/

	/**
	 * DB Table
	 */
	var $_table = 'fbqueue';

	/**
	 * Primary Key
	 */
	var $_key = 'queue_id';

	/**
	 * Handle instantiation
	 */
	function FBQueue() {
		parent::ORDataObject();
	}

	
	function get_ids() {
		if ($this->_inPersist) {
			return serialize($this->ids);
		}
		return $this->ids;
	}

	function set_ids($ids) {
		if (!is_array($ids)) {
			$ids = unserialize($ids);
		}
		$this->ids = $ids;
	}

	function getQueueArray() {
		$sql = "select queue_id queueId, queue_id id, name, max_items maxItems, num_items numItems, ids  from ".$this->tableName();
		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			$res->fields['ids'] = unserialize($res->fields['ids']);
			$ret[$res->fields['queueId']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}

	function set_maxItems($items) {
		$this->max_items = $items;
	}
}
?>
