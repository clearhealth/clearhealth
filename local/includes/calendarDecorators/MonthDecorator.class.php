<?php

if (!defined("CALENDAR_ROOT")) {
	define("CALENDAR_ROOT",dirname(__FILE__) . "/Calendar/");
}

require_once CALENDAR_ROOT .'Month/Weekdays.php';
require_once CALENDAR_ROOT .'Day.php';
require_once CALENDAR_ROOT .'Decorator.php';

	
	
	// Decorate a Month with methods to improve formatting
class MonthDecorator extends Calendar_Decorator {
    
   	/**
   	* @param Calendar_Month
   	*/
   	function MonthDecorator(& $Month) {
       	parent::Calendar_Decorator($Month);
   	}
   
   	/**
   	* Override the prevMonth method to format the output
   	*/
   	function prevMonth() {
       	$prevStamp = parent::prevMonth(TRUE);
       	// Build the URL for the previous month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=month&year='.date('Y',$prevStamp).'&month='.date('n',$prevStamp).'&day='.date('j',$prevStamp);
   	}
   
   	/**
   	* Override the thisMonth method to format the output
   	*/
   	function thisMonth() {
   	    $thisStamp = parent::thisMonth(TRUE);
   	    // A human readable string from this month
   	    return date('F Y',$thisStamp);
   	}
   
   	/**
   	* Override the nextMonth method to format the output
   	*/
   	function nextMonth() {
       	$nextStamp = parent::nextMonth(TRUE);
       	// Build the URL for next month
       	return $_SERVER['PHP_SELF'].'?main&calendar&action=month&year='.date('Y',$nextStamp).'&month='.date('n',$nextStamp).'&day='.date('j',$nextStamp);

   	}
}
?>
