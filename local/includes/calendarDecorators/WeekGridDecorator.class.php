<?php

if (!defined("CALENDAR_ROOT")) {
	define("CALENDAR_ROOT",dirname(__FILE__) . "/Calendar/");
}

require_once CALENDAR_ROOT .'Week.php';
require_once CALENDAR_ROOT .'Day.php';
require_once CALENDAR_ROOT .'Decorator.php';

	
	
	// Decorate a Week with methods to improve formatting
class WeekGridDecorator extends Calendar_Decorator {
    
   	/**
   	* @param Calendar_Week
   	*/
   	function WeekGridDecorator(& $Week) {
       	parent::Calendar_Decorator($Week);
   	}
   
   	/**
   	* Override the prevWeek method to format the output
   	*/
   	function prevWeek() {
       	$prevStamp = parent::prevWeek(TRUE);
       	// Build the URL for the previous month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=week_grid&year='.date('Y',$prevStamp).'&month='.date('n',$prevStamp).'&day='.date('j',$prevStamp);
   	}
   
   	/**
   	* Override the thisWeek method to format the output
   	*/
   	function thisWeek() {
   	    $thisStamp = parent::thisWeek(TRUE);
   	    // A human readable string from this month
   	    return date('F Y',$thisStamp) . " Week " . date('W',$thisStamp);
   	}
   
   	/**
   	* Override the nextWeek method to format the output
   	*/
   	function nextWeek() {
       	$nextStamp = parent::nextWeek(TRUE);
       	// Build the URL for next month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=week_grid&year='.date('Y',$nextStamp).'&month='.date('n',$nextStamp).'&day='.date('j',$nextStamp);

   	}
}
?>
