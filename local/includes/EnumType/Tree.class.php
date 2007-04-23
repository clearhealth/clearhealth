<?php
/**
 * Class the defines the default enumeration type
 */
class EnumType_Tree extends EnumType_Default{

	/**
	 * Sql table the data is stored in, also the name of the ordo used for updating
	 */
	var $table = 'enumeration_value';

	/**
	 * Name of the ORDO used to update
	 */
	var $ordo = 'EnumerationTreeValue';

	/**
	 * Field to use as the associative key
	 */
	var $assocKey = 'key';

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>15),
				'extra1' => false,
				'extra2' => false,
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean'),
				'depth' => array('label'=>'Depth','type'=>'depth'),
				'parent_id' => array('label'=>'Parent','type'=>'hidden')
			);

	/**
	 * Get an array of enum data
	 *
	 * @param  int $enumerationId
	 * @return array
	 */
	function enumData($enumerationId) {
		$enumerationId = EnforceType::int($enumerationId);
		$sql = "select * from {$this->table} where enumeration_id = $enumerationId order by sort, enumeration_value_id";
		$sql = "SELECT ev.* , CASE WHEN ev2.value IS NOT NULL THEN concat( ev2.value, '->', ev.value ) ELSE ev.value END AS value
			FROM {$this->table} ev
			LEFT JOIN enumeration_value ev2 ON ev2.enumeration_value_id = ev.parent_id
			WHERE ev.enumeration_id =$enumerationId
			ORDER BY ev.sort, ev.enumeration_value_id";
		//echo $sql;
		
		if ($this->editing) {	
		$sql = "select * from {$this->table} where enumeration_id = $enumerationId order by sort, enumeration_value_id";

		}
		//echo $sql;
		$db =& Celini::dbInstance();
		$res = $db->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->moveNext();
		}
		//var_dump($ret);exit;
		return $ret;
	}

	/**
	 * Update an enum value with an array of data
	 *
	 * @param	array	$data
	 */
	function update($data) {
		$id = 0;
		if (isset($data['enumeration_value_id'])) {
			$id = $data['enumeration_value_id'];
		}
		$ev =& ORDataObject::Factory($this->ordo,$id);
		$ev->populate_array($data);
		$ev->persist();
	}

	function widget() {
	}

	function jsWidget($name,$rowDef) {
		$GLOBALS['loader']->requireOnce('includes/clniType/'.$rowDef['type'].'.class.php');

		$class = 'clniType'.$rowDef['type'];
		$type = new $class();
		return $type->jsWidget($name);
	}
}
?>
