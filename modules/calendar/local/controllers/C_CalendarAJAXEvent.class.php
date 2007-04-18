<?php
$GLOBALS['loader']->requireOnce('includes/CalendarData.class.php');

class C_CalendarAJAXEvent extends Controller{

	var $ajax_handler;
	
    function C_CalendarAJAXEvent() {
		$clniConfig = Celini::configInstance();
		$calConfig = $clniConfig->get('calendar');
		if(isset($calConfig['ajax_handler'])){
			$GLOBALS['loader']->requireOnce('includes/'.$calConfig['ajax_handler'].'.class.php');
			$this->ajax_handler = &new $calConfig['ajax_handler'](); 
		}else{
			Celini::raiseError("You must configure an ajax_handler for the calendar module!");
		}
    	
    	parent::Controller();
    }
    
    function search_action(){
    	
    }
    
    function list_action(){
    	$start = Celini::filteredGet('start');
    	$end = Celini::filteredGet('end');
    	$view = Celini::filteredGet('view');	
    }
    
    function drop_action($event_id, $date){
    	$event =& ORDataObject::factory('CalendarEvent', $event_id);
    	$start = strtotime($event->get('start')) - strtotime(date('Y-m-d',strtotime($event->get('start'))));
    	$length = strtotime($event->get('end')) - strtotime($event->get('start'));
    	$start_ts = strtotime($date);
		$newstart = $start_ts + $start;
    	$event->set('start', date('Y-m-d H:i:s', $newstart));
    	$event->set('end', date('Y-m-d H:i:s', $newstart + $length));
    	$this->ajax_handler->drop($event, $start_ts);
    	$event->persist();
    }
    
    function update_filter($filter, $value){
    	$cd = CalendarData::getInstance();
    	$cd->setFilter($filter, $value);
    	$output['current_filters'] = $cd->getFilterSettingsHTML();
    	$output['filters'] = $cd->getFilterHTML();
    	
    	return $output;
    }
    
    function remove_filter($filter, $value){
    	$cd = CalendarData::getInstance();
    	$cd->removeFilterValue($filter, $value);
    	$output['current_filters'] = $cd->getFilterSettingsHTML();
    	$output['filters'] = $cd->getFilterHTML();
    	
    	return $output;
    }

    function get_current_filters(){
    	$cd = CalendarData::getInstance();
    	return $cd->getFilterSettingsHTML();
    }
    
    function get_filters(){
    	$cd = CalendarData::getInstance();
    	return $cd->getFilterHTML();
    }

}
?>
