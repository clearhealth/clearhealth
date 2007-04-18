<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.uversainc.celini
 */
class EnumerationDefinition_List_DS extends Datasource_Sql {
	var $_internalName = 'EnumerationDefinition_List_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function EnumerationDefinition_List_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "ed.enumeration_id, ed.name, ed.title, ed.type, count(ev.enumeration_value_id) count",
				'from'    => "enumeration_definition ed
						left join enumeration_value ev using(enumeration_id)",
				'orderby' => 'title',
				'groupby' => 'ed.enumeration_id',
				'where'	  => '(ev.status = 1 or ev.status is null)',
			),
			array('title' => 'title','type'=>'Type','count'=>'# of Values'));
			$this->registerTemplate('title','<a href="'.Celini::link('edit','Enumeration',true,false).'id={$enumeration_id}">{$title}</a>');
	}
}
?>
