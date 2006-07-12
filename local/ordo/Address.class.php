<?php

/**
*	This class is a data model object for representation of address information.
*
*/
class Address extends ORDataObject {

	var $id			= '';
	var $name		= '';
	var $line1		= '';
	var $line2		= '';
	var $city		= '';
	var $region		= '';
	var $province		= '';
	var $county		= '';
	var $state		= '';
	var $postal_code	= '';
	var $notes		= '';
	var $type		= '';
	var $_parent = false;
	var $storage_metadata = array(
		'int' => array('returned_mail'=> 0), 
		'date' => array(),
		'string' => array()
	);

	var $_internalName='Address';


	function Address($db = null) {
		parent::ORDataObject($db);
		$this->_table = "address";
		$this->_sequence_name = "sequences";
		$this->groups = array();
	}
	
	function setup($id = 0,$parent = false, $parent_type = "person") {
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

	/**
	* Pull data for this record from the database
	*/
	function populate() {
		parent::populate(true);

		if (isset($this->_relation)) {
			$sql = "select address_type from $this->_relation where address_id = ".(int)$this->id;
			$this->set('type', $this->_db->getOne($sql));
		}
	}

	/**
	* Store data to the database
	*/
	function persist() {
		parent::persist();

		if ($this->_parent !== false) {
			$addresses = $this->_db->getAssoc("select address_id,address_id $this->_table from $this->_relation where address_id =".(int)$this->id);
			foreach($addresses as $address) {
				if (!isset($this->_parent[$address])) {
					// delete
					$this->_execute("delete from $this->_relation where address_id =".(int)$this->id
					." and $this->_fkey = $address");
				}
			}
			foreach($this->_parent as $id => $val) {
				if (!isset($addresses[$id])) {
					// add
					$sql = "replace into $this->_relation values(".(int)$id.",".(int)$this->id.",".(int)$this->get('type').")";
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
		$this->_execute("delete from {$this->_prefix}$this->_relation where address_id = ". (int)$this->id);
		$this->_execute("delete from {$this->_prefix}$this->_table where address_id = ". (int)$this->id);
	}

	
	/**
	 * @todo refractor into a real datasoruce and use it's toArray() to make the array that's
	 *     expected here.
	 */
	function addressList($parent_id) {
		$sql ="
			select 
				*
			from 
				{$this->_table} 
				inner join {$this->_relation} using(address_id) 
			where 
				{$this->_fkey} = ".(int)$parent_id;
		$res = $this->_execute($sql);

		$countyl = $this->getCountyList();
		$statel = $this->getStateList();
		$typel = $this->getTypeList();

		$list = NULL;
		while($res && !$res->EOF) {
			$res->fields['county'] = $countyl[$res->fields['county']];
			$res->fields['state'] = $statel[$res->fields['state']];

			$res->fields['type'] = $typel[$res->fields['address_type']];

			$list[$res->fields['address_id']] = $res->fields;
			$res->MoveNext();
		}
		return $list;
	}

    /**#@+
    *	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
    */
    function get_address_id() {
	return $this->get('id');
    }
    function set_address_id($id) {
	    $this->set('id',$id);
    }

    function getCountyList() {
	    $list = $this->_load_enum('county',true);
	    return array_flip($list);
    }

	/**
	 * Accessor for raw state
	 *
	 * Use of $map is deprecated - call value('state') directly
	 *
	 * @return string
	 * @access protected
	 * @deprecated
	 */
    function get_state($map = false) {
	    if ($map) {
		    return $this->value('state');
	    }
    	return $this->state;	
    }
	
	function value_state() {
		$list = $this->getStateList();
		if (isset($list[$this->state])) {
			return $list[$this->state];
		}
		else {
			return $this->get('state');
		}
	}
    function get_stateInitials() {
    	return $this->value('state');
    }
    function get_printDisplay() {
	    $line1 = $this->get('line1');
	    $line2 = $this->get('line2');
	    if (!empty($line2)) {
		    $line2 = "<br>$line2";
	    }
	    $city = $this->get('city');
	    $state = $this->get('stateInitials');
	    $postal_code = $this->get('postal_code');
		$returned_mail = $this->get('returned_mail');
	    $ret = "<div class='address'>$line1\n$line2\n<br>$city, $state $postal_code</div>";
	    return $ret;
    }

    function getStateList() {
	    $list = $this->_load_enum('state',false);
	    return array_flip($list);
    }

    function getTypeList() {
	    $list = $this->_load_enum('address_type',true);
	    return array_flip($list);
    }


    var $_acache = false;
    var $_acache_state = false;
    /**
     * Return a text formated address from an id
     *
     * Uses a cache
     *
     * @todo figure out of this approach scales properly 
     */
	function lookup($id) {
	    if ($this->_acache == false) {
		    $res = $this->_execute("select * from $this->_table");
		    $this->_acache = array();
		    while(!$res->EOF) {
			    $this->_acache[$res->fields['address_id']] = $res->fields;
			    $res->moveNext();
		    }

		    $this->_acache_state = $this->getStateList();
	    }
	    if (isset($this->_acache[$id])) {
		    $row = $this->_acache[$id];
		    $state = "";
		    if (isseT($this->_acache_state[$row['state']])) {
			    $state = $this->_acache_state[$row['state']];
		    }
		    return "<div class='address'>$row[line1]\n$row[line2]\n<br>$row[city], $state $row[postal_code]</div>";
	    }
    }

	function lookupState($id) {
		if ($this->_acache_state === false) {
			$this->_acache_state = $this->getStateList();
		}
		if (isset($this->_acache_state[$id])) {
			return $this->_acache_state[$id];
		}
	}

	function toArray() {
		$fields = array('id','name','line1','line2','city','region','state','postal_code','returned_mail');
		$ret = array();
		foreach($fields as $field) {
			$ret[$field] = $this->get($field);
		}
		$ret['state'] = $this->lookupState($ret['state']);
		return $ret;
	}
} 
?>
