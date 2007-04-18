<?php
$loader->requireOnce('includes/FilterWidgetFactory.class.php');

class CalendarData {

	var $data_handler = false;
	
	var $available_filters = array();
	
	var $filter_options = array();
	
	var $filter_settings = array();

	var $_filterCache = false;
	
	function CalendarData() {
		if (isset($_GET['resetFilters']) && $_GET['resetFilters'] == 'default') {
			$this->clearFilters();
		}
		$clniConfig = Celini::configInstance();
		$calConfig = $clniConfig->get('calendar');
		if(isset($calConfig['data_handler'])){
			$GLOBALS['loader']->requireOnce('includes/'.$calConfig['data_handler'].'.class.php');
			$this->data_handler = &new $calConfig['data_handler'](); 
		}else{
			Celini::raiseError("You must configure a data_handler for the calendar module!");
		}
    	
		$this->available_filters = $this->data_handler->getFilterTypes();
	}
    
	function &getInstance() {
		if (!isset($GLOBALS['calendar']['CalendarData'])) {
			$GLOBALS['calendar']['CalendarData'] =& new CalendarData();
		}

		return $GLOBALS['calendar']['CalendarData'];
	}
	
	/**
	 * Return an associative array containing 
	 * increment, hour_start, hour_length
	 *
	 * @return array|false
	 */
	function getConfig() {
		if (method_exists($this->data_handler,'getConfig')) {
			return $this->data_handler->getConfig();
		}
		return false;
	}

	/**
	 * Returns ORDOCollection of ALL events (schedules, appointments, etc)
	 *
	 * @param array|null $filter_options
	 * @return ORDOCollection
	 */
	function &getEvents($filter_options = null){
		if(is_array($filter_options)){
			$this->clearFilters();
			foreach($filter_options as $filter => $value){
				$this->setFilter($filter, $value);
			}
		}
    	
		$where = $this->data_handler->toWhere($this->getFilters());
		$event =& Celini::newORDO('CalendarEvent');
		$finder =& $this->data_handler->getFinder($this->getFilters());
		$events = $finder->find();
		return $events;	
	}
    
	function setFilter($name, $value){
		$this->_filterCache = false;
		$filter = $this->getFilter($name);
		$filter->setValue($value);
		$_SESSION['calendar']['filter_settings'][$name] = $filter->getValue();
	}
    
	function clearFilters(){
		$this->_filterCache = false;
		$_SESSION['calendar']['filter_settings'] = array();
	}
    
	function getFilterValue($name){
		$filter = $this->getFilter($name);
		return $filter->getValue();
	}
    
	function removeFilterValue($name, $value){
		$this->_filterCache = false;
		$filter = $this->getFilter($name);
		$filter->removeValue($value);
		$_SESSION['calendar']['filter_settings'][$name] = $filter->getValue();
		return TRUE;
	}

	function &getFilters(){
		if ($this->_filterCache !== false) {
			return $this->_filterCache;
		}
		$filters = array();
		foreach($this->available_filters as $name => $filter){
			if (!isset($filter['params'])) {
				$filter['params'] = array();
			}
			$filters[$name] =& FilterWidgetFactory::newFilterWidget(
				$filter['type'], 
				$name, 
				$filter['label'], 
				$this->getFilterValue($name), 
				$filter['params']);
		}
		$this->_filterCache = $filters;
		
		return $filters;
	}
	
	function getFilter($name){
		if (!isset($this->available_filters[$name]['params'])) {
			$this->available_filters[$name]['params'] = array();
		}
		return FilterWidgetFactory::newFilterWidget(
			$this->available_filters[$name]['type'], 
			$name, 
			$this->available_filters[$name]['label'], 
			isset($_SESSION['calendar']['filter_settings'][$name]) ? $_SESSION['calendar']['filter_settings'][$name] :  null, 
			$this->available_filters[$name]['params']);
	}
    
	function getFilterHTML(){
		$filters =& $this->getFilters();
		$html  = '';
		foreach($filters as $filter){
			if(isset($filter->params['hidden']) && $filter->params['hidden'] == true) continue;
			$html .= $filter->getHTML($this->data_handler->getFilterOptions($filter->getName()));
		}
		return $html;
	}
	
	function getFilterSettingsHTML(){
		$filters =& $this->getFilters();
		$html  = '';
		foreach($filters as $filter){
			if(isset($this->hidden[$filter->getName()])) continue;
			$html .= $filter->getSettingsHTML();
		}
    	
		if(empty($html)) $html = 'No Current Filters';
    	
		return $html;
	}
	
	function &getFinder($filters) {
		return $this->data_handler->getFinder($filters);
	}

	function getAJAXMethods(){
		return array('getFilterSettings', 'setFilter', 'getFilter');
	}

	function getSchedules() {
		if (method_exists($this->data_handler,'getSchedules')) {
			return $this->data_handler->getSchedules($this->getFilters());
		}
		return false;
	}
	function getEventScheduleMap() {
		if (method_exists($this->data_handler,'eventScheduleMap')) {
			return $this->data_handler->eventScheduleMap($this->getFilters());
		}
		return false;
	}

	function getScheduleList() {
		if (method_exists($this->data_handler,'getScheduleList')) {
			return $this->data_handler->getScheduleList($this->getFilters());
		}
		return false;
	}
	
	/**
	 * Returns an array containing the number of columns a header should span
	 *
	 * @return unknown
	 */
	function getHeaderColspan() {
		if (method_exists($this->data_handler,'getHeaderColspan')) {
			return $this->data_handler->getHeaderColspan();
		}
		return 1;
	}
	
	/**
	 * This will return the super-array (used with day-view)
 	 * $columns = array(
	 * 	column_id => array(
	 * 		'label' => html for header cell,
	 * 		'headercolspan'=> colspan for header (in case we have extra cells, which we do),
	 * 		'conflicts' => array( // need to keep all of these to have them all handy
 	 * 			event_id => conflicting event_id
 	 * 		),
 	 * 		'conflictcols => array(
 	 * 		),
	 * 		'schedules' => array(
	 * 			label => array(
	 * 				'start' => timestamp,
	 * 				'stop' => timestamp,
	 * 				'rowspan' => int // not in use at this point
	 * 			)
	 * 		),
	 * 		'eventmap' => array( // map events to schedules
	 * 			event_id => schedulelabel
	 * 		),
	 * 		'events' => array(
	 * 			timestamp => array( // here's the iterators
	 * 				eid =>array( // this will be the events starting this iteration
	 * 					'html' => html for this event ,
	 * 					'rowspan' => rowspan for this event
	 * 				)
	 * 			)
	 * 		),
	 * 		'borderColor' => hexcolor to use for border if cell is in a schedule block,
	 * 		'backColor'	=> hexcolor to use for background if cell is in a schedule block,
	 * 		'precol' => array( // optional
	 * 			timestamp => html to use at the beginning of each column (like a drag-widget for choosing an amount of time),
	 * 		),
	 * 		'postcol' => array( // see precol
	 * 		),
	 * 		'colspan' => default colspan for cells (in cases of conflicts),
	 * 		'eventsbefore' => number of events occurring before the day start time,
	 * 		'eventsafter' => number of events occurring after day end time
	 * 	)
	 * )
	 */
	function &getColumns($renderType,&$dayIterator) {
		$columns = array();
		if(method_exists($this->data_handler,'getColumns')) {
			$columns = $this->data_handler->getColumns($this->getFilters(),$renderType,$dayIterator);
		} else {
			// TODO: put default calendar data creator here
		}
		//var_dump($columns);
		return $columns;
	}
	
	function &getSidebar() {
		$sidebar = false;
		if(method_exists($this->data_handler,'getSidebar')) {
			$sidebar =& $this->data_handler->getSidebar();
		}
		return $sidebar;
	}
	
	function extraDisplay() {
		if(method_exists($this->data_handler,'extraDisplay')) {
			$filters =& $this->getFilters();
			return $this->data_handler->extraDisplay($filters);
		}
		return '';
	}
	
}
?>
