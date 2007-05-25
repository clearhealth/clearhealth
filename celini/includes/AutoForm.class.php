<?php
class AutoForm {
    var $header = '';
    var $footer = '</form>';
    var $object;
    var $hide   = array('id');
    var $labels = array();
	var $id;
	var $smarty;
	var $extra = array();
	var $addSubmit = true;
	var $createForm = true;
	var $generated_id = 999;

    function toHtml() {
		$ret = $this->header.="<table>";
		if (count($this->object->meta == 0)) {
			$this->object->populateMetaData();
		}
		
		$ret .= $this->inputFields($this->object, $this->extra);
		if (count($this->object->_merge) > 0) {
			foreach ($this->object->_merge as $objname) {
				$subobj = $this->object->get($objname);
				if (is_object($subobj)) {
					$subobj->populateMetaData();
					$ret .= $this->inputFields($subobj);
				}	
			}
		}
	
		$label = "Update";
		if ($this->object->get('id') == 0) {
			$label = "Add";
		}
		if ($this->addSubmit) {
			$ret .= "<tr><td></td><td><input type='submit' value='$label'></td></tr>\n";
		}
		$ret .= "</table>".$this->footer."\n";
		return $ret;
	}
	
	function generateId(){
		$this->generated_id = uniqid('gen');
	}
    
   	function inputFields($obj,$extra_meta = '') {
		$ret = "";
		$meta = $obj->meta; 

		
		if (is_array($extra_meta)) {
				$meta = array_merge($meta,$extra_meta);
		}
		foreach($meta as $field => $data) {
			$line = "";
			$array_prefix = "";
			if (strtolower(get_class($obj)) != strtolower($this->id)) {
				$array_prefix = "[" . strtolower(get_class($obj)) . "]";	
			}
			
			//the metahints has this as hide so make it a hidden field
			if  ($data->primary_key || array_search($field,$obj->metaHints['hide']) !== false) {
        			$ret .="<input type='hidden' name=\"" . $this->id . $array_prefix . "[" . $field . "]\" value=\"".$obj->get($field)."\">";
        			continue;
			}
        	
			$label = ucfirst(str_replace('_',' ',$field));
			$line .= "<tr>\n\t<td><label>$label:</label> </td>\n\t<td>";
			// these calls should really be to plugings for generating this type
			
			$inputID = $this->_createInputID($array_prefix, $field);
			$inputName = $this->_createInputName($array_prefix, $field);
			switch($data->mtype) {
				case "C":
				case "I":
				case "N":
					$line .="<input type='text' id='{$inputID}' name='{$inputName}' value=\"".$obj->get($field)."\">";
				break;
				case "X":
					$line .="<textarea id='{$inputID}' cols='45' rows='10' name='{$inputName}'>".$obj->get($field)."</textarea>";
				break;
				case "B":
					$line .="<input type='file' id='{$inputID}' name='{$inputName}' value=\"".$obj->get($field)."\">";
				break;
				case "D":
				case "T":
					$this->generateId();
					$line .= smarty_function_clni_input_date(array("name" => $this->id . $array_prefix . "[" . $field . "]\"", "value" => $obj->get($field),"id" => $this->id.$this->field.$this->generated_id), $this->smarty);
				break;
				default:
					$line .= $data->mtype;
				break;
			}
			$line .= "</td></tr>\n";
			if ($data->mtype !== 'T' || $data->type === 'datetime') {
				$ret .= $line;
			}
		}
		return $ret;
    }

	function addField($type,$label,$value) {
		$this->extra[$label] = new stdClass();

		switch($type) {
			case "file":
				$this->extra[$label]->mtype = "B";
				$this->extra[$label]->blob = 1;
				$this->extra[$label]->binary = true;
			break;
			case "input":
				$this->extra[$label]->mtype = "C";
			break;
		}
		$this->extra[$label]->type = $type;
		$this->extra[$label]->name = $label;
		$this->object->$label = $value;
	}
	
	
	/**
	 * Returns a formatted id attribute for a form element
	 *
	 * @param string
	 * @param string
	 * @return string
	 */
	function _createInputID($arrayPrefix, $fieldName) {
		return $this->id . $arrayPrefix . '__' . $fieldName;
	}
	
	
	/**
	 * Returns a formatted name attribute for a form element
	 *
	 * @param string
	 * @param string
	 * @return string
	 */
	function _createInputName($arrayPrefix, $fieldName) {
		return $this->id . $arrayPrefix . '[' . $fieldName . ']';
	}
}
?>
