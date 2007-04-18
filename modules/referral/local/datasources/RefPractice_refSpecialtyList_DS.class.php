<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class Refpractice_refSpecialtyList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'Refpractice_refSpecialtyList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * Handle initialization of DS
	 */
	function Refpractice_refSpecialtyList_DS($refpractice_id) {
		$this->_em =& EnumManager::getInstance();
		
		$this->setup(
			Celini::dbInstance(), 
			array(
				'cols' => 'refpractice_id, refpractice_specialty_id, specialty, form',
				'from' => 'refpractice_specialty',
				'where' => 'refpractice_id = ' . (int)$refpractice_id
			),
			array(
				'specialty' => 'Specialty',
				'form' => 'Form'
			)
		);
		
		$this->registerFilter('specialty', array(&$this, '_enumValue'), 'refSpecialty');
	}
	
	function _enumValue($value, $rowValues, $fieldName) {
		$returnString = $this->_em->lookup($fieldName, $value);
		return $this->_addLinkToList($returnString, $rowValues);
	}

	function _addLinkToList($value, $rowValues) {
		$actionURL = Celini::link('edit/' . $rowValues['refpractice_id'], 'refpractice', 'main') 
			. 'ps_id=' . $rowValues['refpractice_specialty_id'];
		return '<a href="' . $actionURL . '">' . $value . '</a>';
	}
}

