<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class refPracticeList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refPracticeList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	function refPracticeList_DS() {
		$this->__construct();
	}
	
	/**
	 * Handle initialization of DS
	 */
	function __construct() {
		settype($patient_id,'int');

		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => 'refPractice_id,
				           name,
						   assign_by,
						   IF(status = 0, "Active", "Inactive") AS status',
				'from' => 'refpractice AS r'
			),
			array(
				'name' => 'Practice Name',
				'status' => 'Status'
			));
		
		
		$this->registerFilter('name', array(&$this, '_addLinkToList'));
	}
	
	function _addLinkToList($value, $rowValues) {
		$passAlong = '';
		if (isset($_GET['u'])) {
			$passAlong = "u=$_GET[u]";
		}
		return '<a href="'.Celini::link('edit/' . $rowValues['refPractice_id'],'refpractice', 'main').$passAlong.'">' . $value .'</a>';
	}
}

