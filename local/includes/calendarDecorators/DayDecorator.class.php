<?php

if (!defined("CALENDAR_ROOT")) {
	define("CALENDAR_ROOT",dirname(__FILE__) . "/Calendar/");
}

require_once CALENDAR_ROOT .'Week.php';
require_once CALENDAR_ROOT .'Day.php';
require_once CALENDAR_ROOT .'Decorator.php';

	
	
	// Decorate a Day with methods to improve formatting
class DayDecorator extends Calendar_Decorator {
    
   	/**
   	* @param Calendar_Day
   	*/
   	function DayDecorator(& $Day) {
       	parent::Calendar_Decorator($Day);
   	}
   
   	/**
   	* Override the prevDay method to format the output
   	*/
   	function prevDay() {
       	$prevStamp = parent::prevDay(TRUE);
       	// Build the URL for the previous month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=day&year='.date('Y',$prevStamp).'&month='.date('n',$prevStamp).'&day='.date('j',$prevStamp);
   	}
   
   	/**
   	* Override the thisDay method to format the output
   	*/
   	function thisDay() {
   	    $thisStamp = parent::thisDay(TRUE);
   	    // A human readable string from this month
   	    return date('l, F j',$thisStamp);
   	}
   
   	/**
   	* Override the nextDay method to format the output
   	*/
   	function nextDay() {
       	$nextStamp = parent::nextDay(TRUE);
       	// Build the URL for next month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=day&year='.date('Y',$nextStamp).'&month='.date('n',$nextStamp).'&day='.date('j',$nextStamp);

   	}
}
?>
