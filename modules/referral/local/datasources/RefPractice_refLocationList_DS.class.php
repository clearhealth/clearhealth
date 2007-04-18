<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class Refpractice_refLocationList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refPracticeLocationList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * Handle initialization of DS
	 */
	function Refpractice_refLocationList_DS($refpractice_id) {
		settype($patient_id,'int');

		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => 'refPracticeLocation_id,
                           refPractice_id,
				           address1,
						   concat(city, ", ", state) AS city_state,
						   zipcode',
				'from' => 'refPracticeLocation',
				'where' => 'refPractice_id = ' . (int)$refpractice_id
			),
			array(
				'address1' => 'Address',
				'city_state' => 'City, State',
				'zipcode' => 'Zipcode'
				));
		
		
		$this->registerFilter('address1', array(&$this, '_addLinkToList'));
	}
	
	function _addLinkToList($value, $rowValues) {
		$actionURL = Celini::link('edit/' . $rowValues['refPractice_id'], 'refpractice', 'main') 
			. 'pl_id=' . $rowValues['refPracticeLocation_id'];
		return '<a href="' . $actionURL . '">' . $value . '</a>';
	}
}

