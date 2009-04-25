<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty make_label modifier plugin
 *
 * Type:     modifier<br>
 * Name:     make_age<br>
 * Purpose:  Given a birthdate in sql format, this returns the age as a useful string. If the birthdate is under two years ago the age is given in months. If the birthdate is under 31 days ago the age is given in days. 
 * @param string
 * @return string
 */
function smarty_modifier_make_age($dob)
{
       if(strpos($dob,'-')){//SQL format
               list($year,$month,$day) = split("-",$dob);
       }else{//pretty print format
               list($month,$day,$year) = split("/",$dob);
       }
	$year=intval($year);	
	$month=intval($month);	
	$day=intval($day);
	$today = time();
	$birthday = mktime(0,0,0,$month, $day, $year); //Gets Unix timestamp for birthday
	
	$difference = $today-$birthday; //Calcuates Difference
	$days_old = floor($difference/60/60/24); //Calculates Days Old


	if($days_old > 730){// older than two years report in years, without numbers
		$years_old = floor($days_old/365);
		return($years_old);
	}else{
		
		if($days_old < 31) // if the baby is under a month old, list age in days
			return ($days_old." days");

		// otherwise list in months
		$months_old = floor($days_old/30);
		return($months_old. " months");

	}

}

?>
