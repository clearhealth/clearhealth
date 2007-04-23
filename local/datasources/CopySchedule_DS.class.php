<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.uversainc.clearhealth
 */
class CopySchedule_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'CopySchedule_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_eventIds = '';
	
	function CopySchedule_DS($eventIds) {
		
		$qEventIds = preg_replace('/[^0-9,]*/','',$eventIds);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
					start, 
					end, 
					s.title as schedule_title, 
					eg.title,
					schedule_code,
					provider_id,
					eg.room_id
					",
						'from' 	=> "
					schedule_event se 
					inner join event ev on ev.event_id = se.event_id 
					inner join event_group eg on eg.event_group_id = se.event_group_id 
					inner join schedule s on s.schedule_id = eg.schedule_id 
						",
						'where'	=> "ev.event_id IN ({$qEventIds})"
			),
			array(
			)
		);
		
		//var_dump($this->preview());
	}
	
}
?>
