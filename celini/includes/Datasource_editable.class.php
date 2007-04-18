<?php
/**
* An editabled datasource
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/

/**
 * Its based off the sql datasource
 */
$loader->requireOnce("includes/Datasource_scrollable.class.php");

class Datasource_editable extends Datasource_scrollable {

	var $primaryKeyField = "id";
	var $extra = array();
	var $meta = array('editableMap'=>array());
	var $object;

	/**
	 * Note this method expects the primary key to be an int
	 */
	function updateField($primaryKey,$field,$value,$passAlong = false) {
		settype($primaryKey,'int');
		$this->object = ORDataObject::factory(get_class($this->object),$primaryKey);
		$this->object->set($field,$value);

		foreach($this->extra as $key) {
			$this->object->set($key,$this->$key);
		}

		if ($passAlong) {
			foreach($passAlong as $key => $val) {
				$this->object->set($key,$val);
			}
		}

		return $this->object->persist();
	}

	/**
	 * Returns an object that says which columns are editable
	 */
	function getMeta() {
		$ret = new stdClass();
		foreach($this->meta as $group => $data) {
			$ret->$group = new stdClass();
			foreach($data as $key => $val) {
				$ret->$group->$key = $val;
			}
		}
		$ret->_updateKey = $this->primaryKeyField;
		return $ret;
	}

	function getSql() {
		$this->prepare();
		$this->rewind();
		return $this->currentSql;
	}
	
}
?>
