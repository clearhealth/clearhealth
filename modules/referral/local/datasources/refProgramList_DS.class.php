<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class refProgramList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refProgramList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * Handle initialization of DS
	 */
	function refProgramList_DS() {
		parent::setup(Celini::dbInstance(), 
			array(
				'cols' => 'rprog.refprogram_id,
				           pprog.name',
				'from' => 'participation_program pprog inner join refprogram AS rprog on rprog.refprogram_id = pprog.participation_program_id',
				'where' => '',
				'groupby' => 'rprog.refprogram_id'
			),
			array('name' => 'Program Name'));
		$this->registerFilter('name', array(&$this, '_addLinkToList'));
	}
	
	function showMemberProgramsOnly() {
		$db =& new clniDB();
		$me =& Me::getInstance();
		$qExternalUserId = $db->quote($me->get_user_id());
		
		$this->_query['from'] .= ' INNER JOIN refuser AS ru USING(refprogram_id)';
		$this->_query['where'] = "ru.external_user_id = {$qExternalUserId} AND ru.deleted = 0";
	}
	
	function _addLinkToList($value, $rowValues) {
		return '<a href="'.Celini::link('edit/' . $rowValues['refprogram_id'],'refprogram', 'main').'">' . $value . '</a>';
	}
}

