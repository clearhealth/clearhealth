<?php
class CeliniAJAX {
	function initController() {
		$GLOBALS['loader']->requireOnce('includes/AJAXController.class.php');

		$instance = new AJAXController();
		$this->server->registerClass($instance,'Controller',$instance->ajaxMethods());
	}
}
?>
