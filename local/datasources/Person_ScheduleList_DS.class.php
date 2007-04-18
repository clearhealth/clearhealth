<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.uversainc.clearhealth
 */
class Person_ScheduleList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_ScheduleList_DS';
	
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
	
	function Person_ScheduleList_DS($personId,$roomId,$start,$end) {
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
						GROUP_CONCAT(ev.event_id) as event_ids,
						MIN(ev.event_id) as event_id_1, 
						MAX(ev.event_id) as event_id_2, 
						
						DATE_FORMAT(ev.start,'%m/%d/%Y %a') as day, 
						DATE_FORMAT(MIN(ev.start),'%r') as start1, 
						DATE_FORMAT(MIN(ev.end),'%r') as end1, 
						CASE WHEN COUNT(ev.start) >1 THEN DATE_FORMAT(MAX(ev.start),'%r') END as start2, 
						CASE WHEN COUNT(ev.start) > 1 THEN DATE_FORMAT(MAX(ev.end),'%r') END as end2,
						CASE WHEN COUNT(ev.start) > 2 THEN 'complex' END as note
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
						and eg.room_id = {$qRoomId} ",
						'groupby' =>
						"DATE_FORMAT(ev.start,'%Y-%m-%d')"
			),
			array(
				'day' => 'Day',
				'start1' => 'Start 1',
				'end1' => 'End 1',
				'start2' => 'Start 2',
				'end2' => 'End 2',
				'note' => 'Note'
			)
		);
		
		//var_dump($this->preview());
	}
	
	
}
?>
