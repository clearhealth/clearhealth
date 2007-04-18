<?php
/*
 * ReplaceString Class
 *
 * This action will replace all instances of a 
 * string with either a field entry or provided
 * string.
 */
class ReplaceString extends BaseAction {
	var $file_list;
	
	var $replacements;
	
	var $message;
		
	function ReplaceString($params){
		parent::BaseAction($params);
		
		$this->interactive = false;
		$this->grouping = true;
	}
	
	/*
	 * This function needs to be overriden in the implementing class
	 * and should return either TRUE or FALSE.
	 * 
	 * @var $params array Array of parameters needed for the specific implementation
	 */
	function perform(){
		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
		
		$sql_commands = array();
		foreach($this->file_list as $old_file => $new_file){
			if(!is_readable($old_file)){
				$this->result = INSTALLER_ACTION_FAIL;
				$this->result_message = "Could not read from file $old_file.";
				return $this->result;
			}
			
			list($markers, $replacements) = $this->getReplacementArrays();
			$file_contents = file($old_file);
			if(count($file_contents) > 0){
				$new_contents = preg_replace($markers, $replacements, $file_contents);
				
				// Write new file
				$file_handle = fopen($new_file, 'w+');
				if(!$file_handle){
					$this->result = INSTALLER_ACTION_FAIL;
					$this->result_message = "Could not write to file $new_file";
					return $this->result;	
				}
				foreach($new_contents as $line){
					fwrite($file_handle, $line);	
				}
				fclose($file_handle);
			}
		}
		
		$this->result_message = $this->message;
		$this->result = INSTALLER_ACTION_SUCCESS;
		return $this->result;
	}

	function getReplacementArrays(){
		$markers = array();
		$replacements = array();
		foreach($this->replacements as $replacement){
			$markers[] = '/'.$replacement->marker.'/';
			$replacements[] = $replacement->getReplacement();
		}
		return array($markers, $replacements);	
	}
			
	function prepareParameters(){
		$this->replacements = array();
		$engine =& $GLOBALS['INSTALLER']['ENGINE'];
		if(isset($this->params['fields'])){
			if(is_array($this->params['fields']) && count($this->params['fields']) > 0){
				foreach($this->params['fields'] as $marker => $field){
					if(!empty($marker)){
						$this->replacements[] =& new ReplaceString_Replacement($marker, $field);
					}
				}	
			}
		}

		if(isset($this->params['strings'])){
			if(is_array($this->params['strings']) && count($this->params['strings']) > 0){
				foreach($this->params['strings'] as $marker => $string){
					if(!empty($marker)){
						$this->replacements[] =& new ReplaceString_Replacement($marker, '',$string);
					}
				}	
			}
		}
		
		// Get file list
		if(!isset($this->params['files']) || !is_array($this->params['files']) || count($this->params['files']) == 0){
			$this->result_message = "You must provide a files parameter that is an array of the files to work on";
			return FALSE;
		}else{
			$this->file_list = $this->params['files'];			
		}		

		// Get success message
		if(isset($this->params['message']) && !empty($this->params['message'])){
			$this->message = $this->params['message'];
		}else{
			$this->message = "All files updated!";			
		}		
	}
}

class ReplaceString_Replacement {

	var $marker;
	
	var $field;
	
	var $string;
	
	var $value;
	
	function ReplaceString_Replacement($marker, $field = '', $string = ''){
		$this->marker = $marker;
		$this->field = $field;
		$this->string = $string;
	}
	
	function setupReplacement(){
		$this->value = '';
		
		if(!empty($this->field)){
			$engine =& $GLOBALS['INSTALLER']['ENGINE'];
			$field = $engine->getField($this->field);
			$this->value = $field->value;
		}else{
			$this->value = $this->string;	
		}
	}
	
	function getReplacement(){
		$this->setupReplacement();
		return $this->value;
	}
}
?>
