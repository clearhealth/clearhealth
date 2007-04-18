<?php
/**
 * Object Relational Persistence Mapping Class for table: enumeration_value_practice
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: enumeration_value_practice
 *
 * @package	com.uversainc.Celini
 */
class EnumerationValueRefProgram extends ORDataObject {

	/**#@+
	 * Fields of table: enumeration_value_practice mapped to class members
	 */
	var $enumeration_value_id = '';
	var $refprogram_id        = '';
	/**#@-*/

	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_table = 'enumeration_value_refprogram';
	var $_key = 'enumeration_value_id';
	/**#@-*/
	
	
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EnumerationValuePractice() {
		parent::ORDataObject();
		$this->_sequence_name = 'sequences';	
	}
}
?>
