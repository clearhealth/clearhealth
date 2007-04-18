<?php

class CalendarSchedule extends ORDataObject
{
	/**#@+
	 * Property of Schedule
	 *
	 * @access protected
	 */
	var $schedule_id = '';
	var $title = '';
	var $description_long = '';
	var $description_short = '';
	
	/**#@-*/
	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_key = 'schedule_id';
	var $_table = 'schedule';
	var $_internalName='Schedule';
		
	function CalendarSchedule() {
		parent::ORDataObject();
	}
	/**#@-*/

	function get_safe_description(){
		return htmlentities($this->get('description'));
	}
	
	/**
	 * Returns array or collection of events
	 *
	 * @return ORDOCollection|array
	 */
	function getEvents($returnArray = false,$eventType='CalendarEvent'){
		$event =& Celini::newORDO($eventType);
		$finder =& $event->relationshipFinder();
		$finder->addParent($this);
		$finder->setOrderBy('ORDER BY event.start');
		$events = $finder->find();
		if($returnArray == true) {
			$events = $events->toArray();
		}
		return $events;
	}

	
	function &getEvent($date,$eventType='CalendarEvent'){
		$db=& new clniDB();
		$matches=array();
		$matches[]=array('child',$this->name(),$this->get('id'));
		$criteria="event.start LIKE '".mysql_real_escape_string($date)."%'";
		$criteria.=" OR event.end LIKE '".mysql_real_escape_string($date)."%'";
		$ocs=&$this->getRelated($matches,$eventType,null,null,$criteria);
		if($oc=&$ocs->current() && $ocs->valid()){
			return $oc;
		} else {
			$x=false;
			return $x;
		}
	}
	
	function getRecurrences(){
		if($this->get('id')<1){
			return array();
		}
		$ocs=$this->getChildren('Recurrence');
		$ocs=$ocs->toArray();
		return $ocs;
	}

	
	/**#@-*/
	/**
	 * Returns a datasource of schedules related to an ordo
	 *
	 * @param ORDataObject $ordo
	 * @param id of ordo not to include in the datasource $noinclude
	 * @return unknown
	 */
	function &scheduleList(&$ordo,$noinclude='',$orderby='ORDER BY schedule.title') {
		$GLOBALS['loader']->requireOnce('includes/Datasource_array.class.php');
		$da=&new Datasource_array();
		$criteria='';
		if(!empty($noinclude)){
			if(is_array($noinclude)){
				$carray=array();
				foreach($noinclude as $noinc){
					$carray[]="schedule.schedule_id != '$noinc'";
				}
				$criteria.=implode(' AND ',$carray);
			} else {
				$criteria.="schedule.schedule_id != '$noinclude'";
			}
		}
		$schedules=$ordo->getChildren('Schedule',false,$criteria,$orderby);
		$earray=array();
		while($schedule=&$schedules->current() && $schedules->valid()){
			$events=$schedule->getChildren('CalendarEvent');
			$earray[]=$schedule->toArray();
			$count=count($earray)-1;
			$earray[$count]['title']="<a href='".Celini::link('Edit','Schedule')."id=".$earray[$count]['schedule_id']."'>".$earray[$count]['title']."</a>";
			$earray[$count]['events']=$events->count();
			$earray[$count]['options']="<a href='".Celini::link('Delete','Schedule')."id=".$earray[$count]['schedule_id']."' onClick='return confirm(\"Delete Schedule ".htmlentities($earray[$count]['title'])."?\");'>X</a>";
			$schedules->next();
		}
		$labels=array('title'=>'Title','events'=>'Events');
		$da->setup($labels,$earray);
		return $da;
	}
	
	function addEvent($data){
		$oc=&Celini::newORDO('CalendarEvent');
		if($data['start']=='00:00') $data['start']='';
		if($data['end']=='00:00') $data['end']='';
		$oc->populate_array($data);
		$oc->set('date',$data['odate']);
		$oc->set('start',$data['start']);
		$oc->persist();
		$this->setRelationship($oc,'child');
	}
	
	function deleteEvents($ids){
		foreach($ids as $id){
			$oc=&Celini::newORDO('CalendarEvent',$id);
			$oc->drop();
		}
	}
	
	function deleteRecurrences($ids){
		foreach($ids as $id){
			$oc=&Celini::newORDO('Recurrence',$id);
			$oc->drop();
		}
	}

	/**
	 * Creates the Recurrence, RecurrencePattern, and Events
	 *
	 * @param array $recdata
	 * @param array $rpdata
	 * @return ORDataObject|false
	 */
	function &createRecurrence($recdata,$rpdata){
		$rec=&Celini::newORDO('Recurrence');
		$rec->populate_array($recdata);
		$rec->persist();
		$this->setChild($rec);
		$rp=&$rec->createPattern($rpdata);
		if(!$rp) {
			$rec->drop();
			$return = false;
			return $return;
		}
		$ocs=$rec->createEvents($rp);
		foreach($ocs as $oc){
			$this->setChild($oc);
		}
		return $rec;
	}
	
	function drop(){
		$recs=&$this->getChildren('Recurrence');
		while($rec=&$recs->current && $recs->valid()){
			$rec->drop();
			$rec=&$recs->next();
		}
		$recs=&$this->getChildren('CalendarEvent');
		while($rec=&$recs->current && $recs->valid()){
			$rec->drop();
			$rec=&$recs->next();
		}
		parent::drop();
	}
	
	function get_future_events($eventType) {
		$e =& Celini::newORDO($eventType);
		$finder =& $e->relationshipFinder();
		$finder->addParent($this);
		$finder->addCriteria("DATE_FORMAT(event.start,'%Y-%m-%d' >= '".date('Y-m-d')."'");
		return $finder->find();
	}

}

