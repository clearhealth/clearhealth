<?php
/**
 * Object Relational Persistence Mapping Class for table: widget_form
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class WidgetForm extends ORDataObject {

	/**#@+
	 * Fields of table: widget_form mapped to class members
	 */
	var $widget_form_id		= '';
	var $name = '';
	var $form_id		= '';
	var $type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'widget_form';

	/**
	 * Primary Key
	 */
	var $_key = 'widget_form_id';

	/**
	 * Handle instantiation
	 */
	/*function WidgetForm() {
		parent::ORDataObject();
	}*/
	
	/*function setup() {
		
	}*/

	
}
?>
