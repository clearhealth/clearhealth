<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class Refpractice_refProviderList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'Refpractice_refProviderList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * Handle initialization of DS
	 */
	function Refpractice_refProviderList_DS($practice_id) {
		$practice_id = (int)$practice_id;
		
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => $practice_id . ' AS refpractice_id, 
				           refProvider_id,
				           concat(if(prefix != "", concat(prefix, " "), ""), first_name, " ", middle_name, " ", last_name) AS name,
						   direct_line',
				'from' => 'refprovider AS r',
				'where' => 'refpractice_id = "' . $practice_id . '"'
			),
			array(
				'name' => 'Provider Name',
				'direct_line' => 'Direct Phone Number'
			));
			
		$this->registerFilter('name', array(&$this, '_addLinkToList'));
	}
	
	function _addLinkToList($value, $rowValues) {
		$actionURL = Celini::link('edit/' . $rowValues['refpractice_id'], 'refpractice', 'main') 
			. 'provider_id=' . $rowValues['refProvider_id'];
		return '<a href="' . $actionURL . '">' . $value . '</a>';
	}
}

