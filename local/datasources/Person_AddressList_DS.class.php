<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.clear-health.clearhealth
 */
class Person_AddressList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_AddressList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function Person_AddressList_DS($person_id) {
		$this->_personId = $person_id;
		
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
							t.person_id,
							a.address_id,
							pa.address_type,
							a.line1,
							a.line2,
							a.city,
							a.postal_code",
						'from' 	=> "
							person AS t
							INNER JOIN person_address AS pa ON (pa.person_id=t.person_id)
							INNER JOIN address AS a USING(address_id)", 
						'where'	=> "t.person_id = {$qPersonId}"
			,
			array(
				'address_type' => 'Type',
				'line1' => 'Address',
				'line2' => 'Address2',
				'city' => 'City',
				'postal_code' => 'Zip'
			))
		);
		
		//var_dump($this->preview());
		$this->registerFilter('address_type', array(&$this, '_addressTypeLookup'));
		$this->registerFilter('line1', array(&$this, '_twoLineFormatting'));
	}
	
	
	function _twoLineFormatting($value, $row) {
		if (!isset($row['line2']) || empty($row['line2'])) {
			return $value;
		}
		
		return $value . ', ' . $row['line2'];
	}
	
	
	function _addressTypeLookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('address_type', $value);
	}
	
	
}
?>
