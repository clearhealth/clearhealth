<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.uversainc.clearhealth
 */
class Person_ScheduleLinearList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_ScheduleLinearList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	var $_roomId = '';
	var $_start = '';
	var $_end = '';
	
	function Person_ScheduleLinearList_DS($personId,$roomId,$start,$end) {
		$this->_personId = $personId;
		$this->_roomId = $roomId;
		$this->_start = $start;
		$this->_end = $end;
		
		$qPersonId = clniDB::quote($personId);
		$qRoomId = clniDB::quote($roomId);
		$qStart = date('Y-m-d',strtotime($start));
		$qEnd = date('Y-m-d',strtotime($end));
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
						ev.event_id
							",
				'from' 	=> "
						person p 
						inner join schedule s on s.provider_id = p.person_id 
						inner join event_group eg on eg.schedule_id = s.schedule_id 
						inner join schedule_event se on se.event_group_id = eg.event_group_id 
						inner join event ev on ev.event_id = se.event_id

						",
				'where'	=> 
						"p.person_id = {$qPersonId} 
						and ev.start >= '{$qStart}' 
						and ev.end < '{$qEnd}'
						and eg.room_id = {$qRoomId} "
					),
			array('event_id' => 'event_id')
		);
		
		//var_dump($this->preview());
	}
	
	
}
?>
