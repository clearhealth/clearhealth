<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.uversainc.celini
 */
class Cronable_DS extends Datasource_Sql {
	
	var $primaryKey = 'cronable_id';
	var $_internalName = 'Cronable_List_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Cronable_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "c.cronable_id, c.label, c.minute, c.hour, c.day_of_month, c.month, c.day_of_week, c.year, c.at_time, c.controller, c.action",
				'from'    => "cronable c",
				'orderby' => 'c.label'
			),
			array('label' => 'Name', 'minute' => 'Minute', 'hour' => 'Hour', 'day_of_month' => 'Day_of_Month', 'day_of_week' => 'Day of Week', 'year' => 'Year', 'at_time' => 'At Time', 'controller' => 'Controller', 'action' => 'Action'));
			$this->registerTemplate('label','<a href="'.substr(Celini::link('edit','Cronable',true,false),0,-1).'/{$cronable_id}?">{$label}</a>');
	}
}
?>
