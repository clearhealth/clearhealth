<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.clear-health.celini
 */
class Person_ParticipationProgram_DS extends Datasource_Sql {
	
	var $primaryKey = 'person_participation_program_id';
	var $_internalName = 'Person_ParticipationProgram_List_DS';
	var $_widgetTypes = 0;

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Person_ParticipationProgram_DS($person_id) {
		$this->_personId = $person_id;

                $qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "*",
				'from'    => "person_participation_program p2pp
					     INNER JOIN participation_program pprog on pprog.participation_program_id = p2pp.participation_program_id",
				'orderby' => 'pprog.name',
				'where'   => ' p2pp.person_id = ' . $qPersonId
			),
			array('name' => 'Name', 'start' => 'Start', 'end' => 'End','expires'=> 'Expires','active'=>'Active'));
			$this->registerTemplate('name','<a href="'.substr(Celini::link('editConnect','ParticipationProgram',true,false),0,-1).'/{$person_program_id}">{$name}</a>');
			$this->registerFilter('expires',array(&$this,'_yesno'));
			$this->registerFilter('active',array(&$this,'_yesno'));

	}

        function _yesno($value) {
                $ret = "no";
		if ($value == 1) $ret = "yes";
		return $ret;
        }
}

?>
