<?php
/*
 * DataField
 *
 * This class represents a piece of information
 * that will need to be collected from the user.
 */
class DataField{

	var $name;
	
	var $label;
	
	var $type;
	
	var $value;
	
	var $default_value;

	var $completed;
	
	function DataField($name, $label, $type, $default_value = ''){
		$this->name = $name;
		$this->label = $label;
		$this->type = $type;
		$this->default_value = $default_value;
	}
	
	/*
	 * This method returns the HTML used to get
	 * the data from the user.
	 */
	function getHTML($smarty){
		if($this->type == 'text'){
			$smarty->assign_by_ref('field', $this);
			$output = $smarty->fetch(Installer::getTemplatePath('field_text.tpl'));
			$smarty->clear_assign('field');	
		}else{
			$output = "Unsupported DataField Type $type";
		}
		
		return $output;
	}
	
	function saveField(){
		if(isset($_REQUEST['DATA_FIELDS'][$this->name])){
			$this->value = 	$_REQUEST['DATA_FIELDS'][$this->name];
			$this->completed = true;
		}
	}
}
?>
