<?php

require_once CELLINI_ROOT . '/includes/Datasource_sql.class.php';

class Person_NameHistoryList_DS extends Datasource_sql
{
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Person_NameHistoryList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	
	/**
	 * The external ID this datasource was instantiated with.  
	 *
	 * This should be considered read-only.
	 *
	 * @var null|int|string
	 * @access public
	 */
	var $external_id = null;
	
	function Person_NameHistoryList_DS($person_id) {
		settype($person_id,'int');
		$this->external_id = $person_id;
		
		$labels = array(
			'first_name'  => 'First Name', 
			'last_name'   => 'Last Name',
			'middle_name' => 'Middle Initial', 
			'update_date' => 'Date Changed');
		
		$this->setup(Cellini::dbInstance(),
			array(
				'cols' 	=> "first_name, last_name, middle_name, update_date",
				'from' 	=> "name_history AS nh",
				'where'	=> "person_id = {$person_id}",
				'orderby' => 'update_date DESC'

			),
			$labels);
	}
}

