<?php
class AJAXController {
	function dispatchAction($controller,$action,$args) {
		$argarray=array();
		if(is_string($args)){
			parse_str($args,$argarray);
		} 
		elseif(is_array($args)) {
			$argarray=$args;
		}
		else if(is_object($args)) {
			foreach($args as $key => $val) {
				$argarray[$key] = $val;
			}
		}
		
		$d = new dispatcher();
		$d->stringOutput = false;
		$a = new DispatcherAction();
		$a->wrapper = false;
		$a->controller = $controller;
		$a->action = $action;
		$a->get = $argarray;

		$post =& Celini::filteredPost();

		foreach($argarray as $key => $val) {
			$post->set($key,$val);
			$_REQUEST[$key] = $val;
		}

		return $d->dispatch($a);
	}

	function ajaxMethods() {
		return array('dispatchAction');
	}
}
?>
