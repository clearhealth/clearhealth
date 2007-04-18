<?php

class FilterWidgetFactory {

	function FilterWidgetFactory() {
	}
    
	function &newFilterWidget($type, $name, $label, $value = null, $params = null){
		$class_name = 'Filter_'.$type;
		if (!class_exists($class_name)) {
			$GLOBALS['loader']->requireOnce('includes/'.$class_name.'.class.php');
		}
    
		$widget =& new $class_name($name, $label, $value, $params);
		return $widget;
	}
}
?>