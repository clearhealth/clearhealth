<?php
/**
 * Object Relational Persistence Mapping Class for table: form_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Marek Handze <marek@rise.pl>
 */

/**
 * Object Relational Persistence Mapping Class for table: form_structure
 *
 * @package	com.uversainc.clearhealth
 */
class FormStructure extends ORDataObject {

	/**#@+
	 * Fields of table: form_data mapped to class members
	 */
	var $form_structure_id	= '';
	var $form_id		= '';
	var $field_name	= '';
	var $field_type		= '';
	/**#@-*/

	var $_table = 'form_structure';
	var $_internalName='FormStructure';

	/**
	 * 
	 * Primary key.
	 * 
	 * @var string
	 * 
	 */
	var $_key = 'form_structure_id';
    
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FormStructure($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	
/**
* Get name and type tags from form template file
*/
	function getFieldsList ($filename) {
		$this->clearFormStructure();
		
		$formTemplate = file($filename);
		for ($i=0; $i<(count ($formTemplate)); $i++)		{
			if(preg_match('/name="(.*)"/s', $formTemplate [$i], $matches)) {
				
				$code = $matches[1];
				$codes = explode ("\"", $code);
				$this->field_name = $codes[0];
				
				preg_match('/type="(.*)"/s', $formTemplate [$i], $matches);
				$code = $matches[1];
				$codes = explode ("\"", $code);
				$this->field_type = $codes[0];
				
				$this->put();
			}
		}
	}

/**
*	Put one field of structure in database
*/
	function put () {
	
		$sql = "INSERT INTO  " . $this->_table . " (form_structure_id, form_id, field_name, field_type)
					VALUES (" . $this->form_structure_id . ", " . $this->form_id . ", '" . $this->field_name . "', '" . $this->field_type . "')";
		$this->dbHelper->execute($sql);
	
	}
// clear structure of form before add new	
	function clearFormStructure () {
		$sql = "DELETE FROM " . $this->_table . " WHERE form_id = " . $this->form_id;
		$this->dbHelper->execute($sql);
		
	}
	
/**
* Return array with structure $ar [form_name.fieldname]=value
*/	
	function build_form_structure_array ($form_data_id) {
		
		$form =& Celini::newOrdo('Form',$this->form_id);
		$form_name = $form->system_name;
		
		$data =& Celini::newOrdo('FormData',$form_data_id);
		$allData = $data->allData();
		
		$ar = array();
		foreach ($allData as $field) {
			$ar['forms.'.$form_name.'.'.$field ['name']] = $field['value'];
		}
		
		return $ar;
	}
}

?>
