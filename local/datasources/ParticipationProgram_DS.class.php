<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.clear-health.celini
 */
class ParticipationProgram_DS extends Datasource_Sql {
	
	var $primaryKey = 'participation_program_id';
	var $_internalName = 'ParticipationProgram_List_DS';
	var $_widgetTypes = 0;

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function ParticipationProgram_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "*",
				'from'    => "participation_program pprog",
				'orderby' => 'pprog.name',
				'where'   => ''
			),
			array('name' => 'Name', 'description' => 'Description'));
			$this->registerTemplate('name','<a href="'.substr(Celini::link('edit','ParticipationProgram',true,false),0,-1).'/{$participation_program_id}?">{$name}</a>');

	}

}
?>
