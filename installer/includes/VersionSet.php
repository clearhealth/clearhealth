<?php
/*
 * BaseSet class
 *
 * A type sensitive Set implementation for PHP
 * 
 */
require_once realpath(dirname(__FILE__)).'/FieldSet.php';
 
class VersionSet extends BaseSet{

	var $complete = null;
	
	var $fields;

	function VersionSet(){
		parent::BaseSet('Version');
		$this->fields = new FieldSet();
	}
	
	function validate(){
		$this->reset();
		while($version =& $this->get()){
			if(!$version->validate()){
				$this->_addError("Validation failed on version ".$version->getVersion().": ".$version->getErrorsHTML());
			}
		}	
	}

	function complete(){
		return $this->testsComplete() && $this->actionsComplete();	
	}
	
	function &getTestsForUpgrade($old_version){
		$tests = array();
		$this->reset();
		while($version =& $this->get()){
			if(version_compare($version->getVersion(), $old_version, '>')){
				$version->tests->reset();
				while($test =& $version->tests->get()){
					$tests[] =& $test;
				}
			}
		}
		
		return $tests;
	}

	function &getNextActionsForUpgrade($old_version){
		$actions = array();
		$this->reset();
		while($version =& $this->get()){
			if(version_compare($version->getVersion(), $old_version, '>')){
				$version->actions->reset();
				while($action =& $version->actions->get()){
					if(!$action->success()){
						if($action->allowGrouping()){
							$actions[] =& $action;
						}else{
							if(count($actions) == 0){
								$actions[] =& $action;
							}
							return $actions;
						}
					}
				}
			}
		}
		
		return $actions;
	}

	function &getFieldsForUpgrade(){
		$fields = array();
		$this->reset();
		$this->fields->reset();
		while($field =& $this->fields->get()){
			$fields[] =& $field;
		}
		
		return $fields;
	}

	function setFieldDefaultValue($name, $value){
		$field =& $this->getField($name);
		if($field != FALSE) $field->default_value = $value;
	}
	
	function testsComplete($old_version){
		$tests = array();
		$this->reset();
		while($version =& $this->get()){
			if(version_compare($version->getVersion(), $old_version, '>')){
				$version->tests->reset();
				while($test =& $version->tests->get()){
					if(!$test->success()){
						return FALSE;
					}
				}
			}
		}
		
		$return = TRUE;
		return $return;
	}

	function actionsComplete($old_version){
		$this->reset();
		while($version =& $this->get()){
			if(version_compare($version->getVersion(), $old_version, '>')){
				$version->actions->reset();
				while($action =& $version->actions->get()){
					if(!$action->success()){
						return FALSE;
					}
				}
			}
		}
		
		$return = TRUE;
		return $return;
	}

	function &getField($name){
		$this->fields->reset();
		while($field =& $this->fields->get()){
			if($field->name == $name){
				return $field;
			}
		}

		return false;		
	}

	function collectData($field_name, $field_label, $field_type, $default_value = ''){
		$this->fields->add(new DataField($field_name, $field_label, $field_type, $default_value));		
	}
}
?>
