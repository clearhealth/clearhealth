<?php

require_once(dirname(__FILE__) . "/config.php");

/**
 * class ORDataObject
 *
 */

class ORDataObject {
	var $_prefix;
	var $_table;
	var $_db;
	var $_sequence_name;
	var $_populated = false;
	
	function ORDataObject($db = NULL, $prefix = NULL) {
                                                                                
    	if ($db != NULL) {
          $this->_db = $db;
        }
        else {
          $db = $GLOBALS['frame']['adodb']['db'];
          if (is_object($db) && is_a($db,"adoconnection")) {
            $this->_db = $db;
          }
        }
        
        if ($prefix != NULL) {
          $this->_prefix = $prefix;
        }
				else {
					$pre = $GLOBALS['frame']['config']['db_prefix'];
						if (!empty($pre)) {
							$this->_prefix = $GLOBALS['frame']['config']['db_prefix'];
						}
				}

				$this->_sequence_name = $this->_prefix . $this->_sequence_name;
	}
	
	function persist() {
		$sql = "REPLACE INTO " . $this->_prefix . $this->_table . " SET ";
		//echo "<br><br>";
		$fields = $this->_list_fields();
		$db = $this->_db;
		$pkeys = $db->MetaPrimaryKeys($this->_prefix . $this->_table);

		foreach ($fields as $field) {
			$func = "get_" . $field;
			//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
			if (is_callable(array($this,$func))) {
				$val = call_user_func(array($this,$func));
				if (in_array($field,$pkeys)  && empty($val)) {
					$last_id = $db->GenID($this->_prefix."sequences");
					call_user_func(array(&$this,"set_".$field),$last_id);
					$val = $last_id;
				}

				if (isset($val)) {
					//echo "s: $field to: $val <br>";
					$sql .= " `" . $field . "` = '" . mysql_real_escape_string(strval($val)) ."',";
				}
			}
		}

		if (strrpos($sql,",") == (strlen($sql) -1)) {
				$sql = substr($sql,0,(strlen($sql) -1));
		}

		//echo "<br>sql is: " . $sql . "<br /><br>";
		$db->execute($sql);
		return true;
	}

	function populate($id = "id") {
		$sql = "SELECT * from " . $this->_prefix  . $this->_table . " WHERE $id = '" . mysql_real_escape_string(strval($this->id))  . "'";
		$db = $this->_db;
		$results = $db->Execute($sql);
		if ($results && !$results->EOF) {
			$this->_populated = true;
			foreach ($results->fields as $field_name => $field) {
				$func = "set_" . $field_name;
				//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
				if (is_callable(array($this,$func))) {

					if (!empty($field)) {
						//echo "s: $field_name to: $field <br>";
						call_user_func(array(&$this,$func),$field);
						

					}
				}
			}
		}
	}

	function populate_array($results) {
		  if (is_array($results)) {
			foreach ($results as $field_name => $field) {
				$func = "set_" . $field_name;
				//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
				if (is_callable(array($this,$func))) {

					if (!empty($field)) {
						//echo "s: $field_name to: $field <br>";
						call_user_func(array(&$this,$func),$field);

					}
				}
			}
		}
	}
	
	function _list_fields() {
		$sql = "SHOW COLUMNS FROM ". mysql_real_escape_string($this->_prefix . $this->_table);
        $res = $this->_db->Execute($sql);
        //or die("DB Error: " . $this->_db->ErrorMsg())
        $field_list = array();
        while(!$res->EOF) {
            $field_list[] = $res->fields['Field'];
					$res->MoveNext();
        }
		return $field_list;
	}
	
	function _execute($sql) {
      if (!empty($sql)) {
        if ($this->_db !=NULL) {
          $this->_db->SetFetchMode(ADODB_FETCH_ASSOC);
          $res = $this->_db->Execute($sql) or die("Error in query: $query. " . $this->_db->ErrorMsg());
          //$this->_db->SetFetchMode(ADODB_FETCH_NUM);
          return $res;
        }
        else {
          //log failed db error
          return false;
        }
      }
    }
	
	function _form_hidden_fields() {
		$field_array =  array();
		$field_string = "";
		$methods = $this->get_class_methods();
		foreach ($methods as $method) {
			if (substring($method,0,4) == "get_") {
				$field_array[substring($method,4)] = $$method();
				$field_string .= '<input type="hidden" name="'. substring($method,4) .'" value="'. $$method() .'">';	
			}	
		}	
		return $field_string;
	}
	
} // end of ORDataObject
?>
