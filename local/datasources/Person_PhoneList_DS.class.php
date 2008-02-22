<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.clear-health.clearhealth
 */
class Person_PhoneList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_PhoneList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function Person_PhoneList_DS($person_id) {
		$this->_personId = $person_id;
		
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
							pn.number_id,
							number,
							number_type,
							notes,
							active",
						'from' 	=> "
							person_number pn inner join number n on n.number_id = pn.number_id ",
						'where'	=> "pn.person_id = {$qPersonId}"
			,
			array(
				'number_type' => 'Type',
				'number' => 'Number',
				'notes' => 'Notes',
				'active' => 'active'
			))
		);
		
		//var_dump($this->preview());
		$this->registerFilter('number_type', array(&$this, '_numberTypeLookup'));
	}
	
	
	
	
	function _numberTypeLookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('number_type', $value);
	}
	
	
}
?>
