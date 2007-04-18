<?php
$loader->requireOnce('includes/CalendarData.class.php');
$loader->requireOnce('includes/CalendarDescription.class.php');
$loader->requireOnce('controllers/C_Appointment.class.php');

class C_CalendarDisplay extends Controller {

	var $location;
	var $eventRender;
	var $ajaxHandler;
	var $calendarData;

	var $year;
	var $month;
	var $day;

	function C_CalendarDisplay() {
		$clniConfig = Celini::configInstance();
		$calConfig = $clniConfig->get('calendar');

		if(isset($calConfig['event_render'])){
			$GLOBALS['loader']->requireOnce('includes/'.$calConfig['event_render'].'.class.php');
			$this->eventRender =& new $calConfig['event_render'](); 
		}
		else{
			Celini::raiseError("You must configure an eventRender for the calendar module!");
		}
		if(isset($calConfig['ajax_handler'])){
			$GLOBALS['loader']->requireOnce('includes/'.$calConfig['ajax_handler'].'.class.php');
			$this->ajaxHandler =& new $calConfig['ajax_handler'](); 
		}
		else{
			Celini::raiseError("You must configure an ajax_handler for the calendar module!");
		}

		$this->calendarData =& CalendarData::getInstance();
		
		parent::Controller();

		$date = $this->GET->get('date');
		
		$session =& Celini::sessionInstance();
		if(empty($date)){
			$date = $session->get('calendar:selectedDay',date('Y-m-d'));
			$this->year = date('Y', strtotime($date));
			$this->month = date('n', strtotime($date));
			$this->day = date('d', strtotime($date));
		}else{
			$this->year = date('Y', strtotime($date));
			$this->month = date('n', strtotime($date));
			$this->day = date('d', strtotime($date));
		}
		$session->set('calendar:selectedDay',"$this->year-$this->month-$this->day");

		$head =& Celini::HTMLHeadInstance();
		$head->addExternalCss('calendar');
		$head->addNewJs('calendar_ajax', 'js/calendar_ajax.js');
		$head->addJs('clniPopup');
		$head->addJs('scriptaculous');
		$head->addJs('moofx');
		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'C_CalendarAJAXEvent';
		$ajax->stubs[] = 'CalendarData';
		$ajax->jsLibraries[] = array('Calendar','scriptaculous','moofx');

		$this->view->path = 'display';
		if(is_array($this->GET->getRaw('Filter'))) {
			foreach($this->GET->get('Filter') as $fname => $fvalue) {
				$this->calendarData->setFilter($fname,$fvalue);
			}
		}
		$this->view->assign('DAY_ACTION',Celini::link('Day'));
		$this->view->assign('WEEK_ACTION',Celini::link('Week'));
		$this->view->assign('MONTH_ACTION',Celini::link('Month'));

		$this->view->assign('showWeekLinks',$clniConfig->get('showCalendarWeekViewLinks',true));
	}
		
	function actionDefault() {
		return $this->actionMonth();
	}

	function actionDay() {
		$ts = _ts();

		$date = date('Y-m-d',strtotime("$this->year-$this->month-$this->day"));

		$cd =& new CalendarDescription('day');
		$cd->eventRenderer =& $this->eventRender;

		$config =& Celini::configInstance();

		$start = date('Y-m-d H:i:s', strtotime("$date ".substr('0'.$config->get('CalendarHourStart',7),-2).":00:00"));
		$end = date('Y-m-d H:i:s', strtotime("$date ".substr('0'.($config->get('CalendarHourStart',7)+$config->get('CalendarHourLength',13)),-2).":00:00"));

		list($startTime,$endTime) = $cd->setupFilters($this->calendarData,$start,$end);
		$cd->setStartDate($this->year,$this->month,$this->day);
		$dayIterator = $cd->getIterator();

		$this->view->assign_by_ref('description',$cd);
		$this->view->assign_by_ref('dayIterator',$dayIterator);

		$columns =& $this->calendarData->getColumns('day',$dayIterator);
		//var_dump($columns);

		$this->view->assign_by_ref('columns',$columns);

		$ndate = date("Y-m-d",strtotime($date.' +1 Day')); 
		$pdate = date("Y-m-d",strtotime($date.' -1 Day')); 
		$this->view->assign("DAY_NEXT_ACTION", Celini::link("Day") . "date=$ndate");
		$this->view->assign("DAY_PREV_ACTION", Celini::link("Day") . "date=$pdate");
		
		$this->view->assign('numColumns',count($columns));

		//var_dump($columns);

		$filter_html = $this->actionFilter();
		$this->assign('filter_html', $filter_html);
		$this->view->assign('extraDisplay',$this->calendarData->extraDisplay());
		$this->view->assign('sidebar',$this->calendarData->getSidebar());

		$head =& Celini::HTMLHeadInstance();
		$head->addInlineJs('var calendarInterval = '.$dayIterator->interval.';');
		
		$this->view->caching = true;
		// This part of the calendar should barely ever change.  Cache for an hour.
		$this->view->cache_lifetime = 3600;
		$cache_id = false;
		if(isset($this->calendarData->data_handler->cache_identifier)) {
			$cache_id=$this->calendarData->data_handler->cache_identifier;
		}
		$this->view->assign('timerow',
			$this->view->fetch('display/general_daytimerow.html',$cache_id)
		);

		// Cache all the header stuff from the day template
		$headhtml = $this->view->fetch('display/cache_dynhead.html',$cache_id);
		$head->addElement($headhtml);
		$this->view->assign('rescheduleFlag',$config->get('showRescheduleLink'));
		$this->view->caching = false;
		$ret = $this->view->render('day.html',$cache_id);
		return $ret;

//		$te = _ts();
//		$seconds = $te-$ts;
//		return "Rendered in $seconds<br>".$ret;
		
	}

	function actionMonth() {
		$cd = new CalendarDescription('month');
		$cd->eventRenderer =& $this->eventRender;
		$cd->setStartDate($this->year,$this->month,1);

		$monthIterator = $cd->getIterator();

		$this->calendarData->setFilter('start',date('Y-m-d H:i',$monthIterator->start));
		$this->calendarData->setFilter('end',date('Y-m-d H:i',$monthIterator->end));
		$cd->_eventSetup();
		
		$this->view->assign_by_ref('description',$cd);
		$this->view->assign_by_ref('cd',$this->calendarData);
		$this->view->assign_by_ref('monthIterator',$monthIterator);
		$this->view->assign('filters', $this->calendarData->getFilterHTML());
		$this->view->assign('extraDisplay',$this->calendarData->extraDisplay());
		
		$this->assign('filter_html', $this->actionFilter());	
		$this->assign("MONTH_PREV_ACTION", Celini::link("month") . "date=".date('Y-m-d',$monthIterator->getPrevMonth()));
		$this->assign("MONTH_NEXT_ACTION", Celini::link("month") . "date=".date('Y-m-d',$monthIterator->getNextMonth()));
		return $this->view->render('month.html');

	}
	
	function actionFilter(){
		$view = new clniView();
    	$view->template_dir = APP_ROOT.'/modules/calendar/local/templates/';
		
		$view->assign('filters', $this->calendarData->getFilterHTML());
		
		return $view->fetch('filter/general_filter.html');
	}
	
	function actionWeek() {
		$cd = new CalendarDescription('week');
		$cd->eventRenderer =& $this->eventRender;
		$cd->setStartDate($this->year,$this->month,$this->day);

		$weekIterator = $cd->getIterator();

		$this->calendarData->setFilter('start',date('Y-m-d H:i',$weekIterator->start));
		$this->calendarData->setFilter('end',date('Y-m-d H:i',$weekIterator->end));
		$caching = false;
		if(is_object($this->calendarData->data_handler)) {
			$caching = true;
			$this->view->caching = true;
			$this->view->cache_lifetime = 3600;
			$cache_id = md5($this->calendarData->data_handler->toWhere($this->calendarData->getFilters()));
			if($this->view->is_cached('display/general_week.html',$cache_id)) {
				return $this->view->fetch('display/general_week.html',$cache_id);
			}
			$this->view->caching = false;
		}
		$cd->_eventSetup();
		$this->view->assign('extraDisplay',$this->calendarData->extraDisplay());
		$this->assign('filter_html', $this->actionFilter());
		$this->view->assign('sidebar',$this->calendarData->getSidebar());
		
		$this->view->assign_by_ref('description',$cd);
		$this->view->assign_by_ref('weekIterator',$weekIterator);
		$this->assign('filter_html', $this->actionFilter());
		$this->assign("WEEK_PREV_ACTION", Celini::link("week") . "date=".date('Y-m-d',$weekIterator->getPrevWeek()));
		$this->assign("WEEK_NEXT_ACTION", Celini::link("week") . "date=".date('Y-m-d',$weekIterator->getNextWeek()));
		if($caching == true) {
			$this->view->caching = true;
			return $this->view->fetch('display/general_week.html',$cache_id);
		}
		return $this->view->render('week.html');
	}

	function actionClearFilters() {
		$this->calendarData->clearFilters();
	}

	function actionTest() {
		return $this->view->render('test.html');
	}

}
	function _ts() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
?>
