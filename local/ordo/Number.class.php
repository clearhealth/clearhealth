<?php

/**
*	This class is a data model object for representation of phone number information.
*
*/
class Number extends ORDataObject {

	var $number_id = '';
	var $number_type ='';
	var $notes = '';
	var $number = '';
	var $active = 1;

	var $groups = array();

	var $_parent = false;
	var $_relation = "person_phone";
	var $_fkey = "person_id";
	var $_table = 'number';
	var $_key = 'number_id';
	var $_internalName='Number';
	
	var $storage_metadata = array(
		'int' => array(),
		'string' => array(),
		'date' => array(),
		'text' => array(
			'dnc_note' => ''
		)
	);

	function setup($id = 0,$parent=false,$parent_type="person") {
		if ($id !== 0) {
			$this->set('id',$id);
			$this->populate();
		}

		if ($parent !== false) {
			if (!is_array($parent)) {
				$parent = array($parent => array($parent_type."_id"=>$parent));
			}
			$this->_parent = $parent;
		}
	}

	function persist() {
		parent::persist();
		if ($this->_parent !== false) {
			$phones = $this->_db->getAssoc("select number_id,number_id phone from $this->_relation where number_id =".(int)$this->get('id'));
			foreach($phones as $phone) {
				if (!isset($this->_parent[$phone])) {
					// delete
					$this->_execute("delete from $this->_relation where number_id=".(int)$this->get('id')
					." and $this->_fkey = $phone");
				}
			}
			foreach($this->_parent as $id => $val) {
				if (!isset($phones[$id])) {
					// add
					$sql = "replace into $this->_relation values(".(int)$id.",".(int)$this->get('id').")";
					$this->_execute($sql);
				}
			}
		}
	}

	/**
	* Delete this record
	*/
	function drop()
	{
		$this->_execute("delete from {$this->_prefix}$this->_relation where number_id = ". (int)$this->get('id'));
		$this->_execute("delete from {$this->_prefix}$this->_table where number_id = ". (int)$this->get('id'));
	}

	function numberList($parent_id) {
		$this->_phone_numbers = array();
		$sql ="select pn.number_id, number, notes, number_type, active, st.value dnc_note
			from $this->_table pn inner join $this->_relation using(number_id) 
			left join storage_text st on pn.number_id = st.foreign_key and st.value_key = 'dnc_note' 
			where $this->_fkey = ".(int)$parent_id;
		$res = $this->_execute($sql);

		$lookup = $this->getTypeList();
		$numbers = array();
		while($res && !$res->EOF) {
			$res->fields['number_type'] = $lookup[$res->fields['number_type']];
			$pos = strpos($res->fields['notes'],"\n");
			if (!$pos) {
				$pos = strlen($res->fields['notes']);
			}
			$res->fields['notes'] = substr($res->fields['notes'],0,$pos);

			$numbers[$res->fields['number_id']] = $res->fields;
			$res->MoveNext();
		}
		return $numbers;
	}

	/**#@+
	*	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
	*/
	
	function get_number_type() {
		return $this->number_type;
	}
	function set_number_type($type) {
		$this->number_type = $type;
	}
	
	function get_notes() {
		return $this->notes;
	}
	function set_notes($notes) {
		$this->notes = $notes;
	}
	
	function get_number() {
		return $this->number;
	}
	function set_number($n) {
		$this->number = $n;
	}
	
	function getTypeList() {
		$list = $this->_load_enum('number_type',true);
		return array_flip($list);
	}

	function get_dnc() {
		return $this->active;
	}
	function set_dnc($flag) {
		$this->active = $flag;
	}
}
?>
