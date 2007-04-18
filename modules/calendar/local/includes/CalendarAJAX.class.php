<?php
class CalendarAJAX {

	function initC_CalendarAJAXEvent() {
		$GLOBALS['loader']->requireOnce('controllers/C_CalendarAJAXEvent.class.php');
		$controller =& new C_CalendarAJAXEvent();

		$this->server->registerClass($controller,'C_CalendarAJAXEvent');
	}
	
	function initCalendarData() {
		$GLOBALS['loader']->requireOnce('includes/CalendarData.class.php');
		$cd =& CalendarData::getInstance();

		$this->server->registerClass($cd,'CalendarData', $cd->getAJAXMethods());
	}
	
}
?>
