<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.uversainc.clearhealth
 */
class Person_RelatedAddressList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_RelatedAddressList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function Person_RelatedAddressList_DS($person_id) {
		$this->_personId = $person_id;
		
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array('union' =>
				array(
					array(
						'cols' 	=> "
							r.person_id,
							a.address_id,
							CONCAT_WS(' ', r.first_name, r.last_name) AS related_name,
							pa.address_type,
							a.line1,
							a.line2,
							a.city,
							a.postal_code",
						'from' 	=> "
							person_person AS t
							INNER JOIN person AS me ON (me.person_id = t.person_id)
							INNER JOIN person AS r ON (r.person_id = t.related_person_id)
							INNER JOIN person_address AS pa ON (pa.person_id=r.person_id)
							INNER JOIN address AS a USING(address_id)", 
						'where'	=> "t.person_id = {$qPersonId}"
					),
					array(
						'cols' 	=> "
							r.person_id,
							a.address_id,
							CONCAT_WS(' ', r.first_name, r.last_name) AS related_name,
							pa.address_type,
							a.line1,
							a.line2,
							a.city,
							a.postal_code",
						'from' 	=> "
							person_person AS t
							INNER JOIN person AS me ON (me.person_id = t.related_person_id) 
							INNER JOIN person AS r ON (r.person_id = t.person_id)
							INNER JOIN person_address AS pa ON (pa.person_id=r.person_id)
							INNER JOIN address AS a USING(address_id)", 
						'where'	=> "t.related_person_id = {$qPersonId}"
					)
				)
			),
			array(
				'related_name' => 'Patient',
				'address_type' => 'Type',
				'line1' => 'Address',
				'city' => 'City',
				'postal_code' => 'Zip',
				'action_select' => false
			)
		);
		
		//var_dump($this->preview());
		$this->registerFilter('address_type', array(&$this, '_addressTypeLookup'));
		$this->registerFilter('line1', array(&$this, '_twoLineFormatting'));
		$this->registerFilter('action_select', array(&$this, '_actionSelect'));
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
	
	
	var $_cursor = 0;
	function _actionSelect($v, $row) {
		$cursor = $this->_cursor;
		
		$return = "<input type='checkbox' name='relatedAddress[{$cursor}][address_id]' value='{$row['address_id']}' />";
		$this->_cursor++;
		return $return;
	}
}
?>
