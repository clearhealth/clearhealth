<?php
/**
 * Class to run events setup in celini cron table, powered by a regular
 * scheduler such as system cron this script runs once per minute and will
 * execute anything in the MySQL Celini Cronables table for which it is linked.
 * 
 * The advantage of this over regular cron is that there is a UI for the user to
 * schedule items and Celini based PHP applications can register tasks for later
 * excution or general background execution. This is especially useful for large
 * reports which do not run well with set_timelimit(0).\
 * 
 * This should be installed in system cron to run as at least the web server,
 * our reccomended configuration is to run this as root but this will differ
 * based on many configuration parameters. Copy this script to a generally
 * accessible location such as /usr/bin .
 * 
 * Note location of your php binary and install with line in crontab: 
 * 59 *  * * *     root  /usr/bin/php /usr/bin/celini-cron.php
 * 
 * 
 * 
*/

set_time_limit(0);
ini_set("memory_limit", "256M");
ini_set("register_globals","Off");
ini_set("magic_quotes","Off");

//list one or more paths to celini based applications
//reference the directory without a trailing slash, example:
//		/var/www/html/clearhealth
$application_list = array (
			"/var/www/html"
); 
$GLOBAL['QUERY_STRING'] = "";
if (isset($_SERVER['HTTP_HOST'])) {
	echo "This script cannot be run through the web browser, it must be run as a system script.";
	exit(1);
}


session_start();
//file to log information to, supply complete path. File is created if it does not exist.

$logfile = "/var/log/celini-cron.log";

$f = "";
if (!file_exists($logfile) && is_writable(dirname($logfile))) {
	$f = fopen ($logfile, "w");		
}
else if (file_exists($logfile) && is_writable($logfile)) {
	$f = fopen ($logfile, "a");
}
else {
	echo logtime() . "\nCould not write to or create log file: " . $logfile . "\nThis script generally needs to be run as root or have sufficient permissions on the log file. Make sure path is correct.\n\n";
	exit;	
}

// Keep celini from unsetting the db data in config
$GLOBALS['no_bootstrap_unsets'] = true;
foreach($application_list as $application) {
	$found = require_once ($application . "/celini/bootstrap.php");
	if ($found) {
		fwrite ($f,logtime() . "Running scheduled tasks for: " . $application . "\n");
		$sql = "Select * from cronable";
		$sqlcon = mysql_connect($config['db_host'], $config['db_user'], $config['db_password']);
		mysql_select_db($config['db_name']);
		echo mysql_error();
		$result = mysql_query($sql);
		$t = time();
		while ($row = mysql_fetch_assoc($result)) {
			$lt = strtotime($row['last_run']);
			if ($row['at_time'] != "0000-00-00 00:00:00" && strtotime($row['at_time']) > 0) {
				//at time events only run once
				if ($lt > 0) {
					continue;
				}
				$at = strtotime($row['at_time']);
				if ($t >= $at) {
					//echo "run attime: "  . date("Y-m-d H:i:s") . " " .$row['at_time'] . "\n";
					run_cronable($row, $application);	
				}	
			} 
			else {
				$cron_string = $row['minute'] . " " . $row['hour'] . " " . $row['day_of_month']. " " . $row['month']. " " . $row['day_of_week']. " " . $row['year'];
				//echo "cstring" . $cron_string . "\n"; 
				$cp = new CronParser($cron_string);
				$cron_time = $cp->getLastRan();
				$run_time = mktime ( $cron_time[1] ,$cron_time[0],0 , $cron_time[3] ,$cron_time[2], $cron_time[5]);
				//echo "runtime: " . date("Y-m-d H:i:s", $run_time) . "\n";
				//echo "last time: " . $lt . "\n";
				if ($run_time > $lt || (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == "--run")) {
					fwrite ($f,logtime() . "Running: " . $row['controller'] . "->" . $row['action'] . "\n");
					//echo "running the cronable\n";
					run_cronable($row, $application);	
				}
				//else {
				//	echo "not running cron";	
				//}
			}
		}
		
	}
	else {	
		fwrite ($f,logtime() . "Could not load config file for: " . $application . "\n");
	}
	
}

fclose($f);

function logtime() {
	return date("M\tj H:i:s\t");	
}

function run_cronable($row, $application) {
	global $loader, $f;
	$sql = "update cronable set last_run = NOW() where cronable_id = " . intval($row['cronable_id']);
	mysql_query($sql);
	require_once ($application . "/local/config.php");
	require_once ($application . "/celini/bootstrap.php");
	$GLOBALS['config']['require_login'] = false;
	$GLOBALS['config']['autoAcl'] = false;
	$loader->requireOnce("controllers/Dispatcher.class.php");

	$action = new DispatcherAction();

	if (isset($row['wrapper'])) {
		$action->wrapper = $row['wrapper'];
	}
	else {
	    $action->wrapper = false;
	}
	$action->controller = $row['controller'];
	$action->action = $row['action'];
	parse_str($row['arguments'],$action->get);

	//fwrite ($f,logtime() . print_r($action->get,true));
	$_GET = $action->get;
    //$action->defaultValue = '';
                                                                                
    $d = new Dispatcher();
    $content = $d->dispatch($action);
	//fwrite ($f,logtime() . $content."\n");
    
    if (isset($_SESSION['CELINI_MESSAGES'])) {
	    foreach ($_SESSION['CELINI_MESSAGES'] as $message) {
		fwrite ($f,logtime() . "Message:" .  $message['title'] . "\n");
	    }
    }
    	fwrite ($f,logtime() . "Complete\n");
}

/*
 * Mick Sear, eCreate, May 2005
 * http://www.ecreate.co.uk
 * License: GPL
 * Version: 1.1
 */

 
 /*$cron  = "15,33,48 1-10 * * *";
 $cp = new CronParser($cron);
 echo "Cron $cron last ran on:";
 print_r($cp->getLastRan());
 echo nl2br($cp->getDebug());*/


 
 class CronParser{
 	
 	var $bits = Array(); //exploded String like 0 1 * * *
 	var $now= Array();	//Array of cron-style entries for time()
 	var $lastRan; 		//Timestamp of last ran time.
 	var $taken;
 	var $debug;
	
 	
 	function CronParser($string){
 		$tstart = microtime();
 		$this->debug("<b>Working on cron schedule: $string</b>");
 		$this->bits = @explode(" ", $string);
 		$this->getNow(); 		
 		$this->calcLastRan();
 		$tend = microtime();
 		$this->taken = $tend-$tstart;
 		$this->debug("Parsing $string taken ".$this->taken." seconds");
 		
 	}
 	
 	function getNow(){
		$t = strftime("%M,%H,%d,%m,%w,%Y", time()); //Get the values for now in a format we can use
		$this->now = explode(",", $t); //Make this an array
		//$this->debug($this->now);
 	}
 	
 	function getLastRan(){
 		return explode(",", strftime("%M,%H,%d,%m,%w,%Y", $this->lastRan)); //Get the values for now in a format we can use	
 	}
 	
 	function getDebug(){
 		return $this->debug;	
 	}
 	
	function debug($str){
		if (is_array($str)){
			$this->debug .= "\nArray: ";
			foreach($str as $k=>$v){
				$this->debug .= "$k=>$v, ";
			}
	
		} else {
			$this->debug .= "\n$str";
		}
		//echo nl2br($this->debug);
		
	} 	
 	

	function getExtremeMonth($extreme){

		if ($extreme == "END"){
			$year = $this->now[5] - 1;
		} else {
			$year = $this->now[5];
		}
		
		//Now determine start or end month in the last year
		if ($this->bits[3] == "*" && $extreme == "END"){//Check month format
			$month = 12;			
		} else if ($this->bits[3] == "*" && $extreme == "START"){
			$month = 1;
		} else {
			$months = $this->expand_ranges($this->bits[3]);
			if ($extreme == "END"){
				sort($months);
			} else {
				rsort($months);
			}
			$month = array_pop($months);
		}
		
		//Now determine the latest day in the specified month
		$day=$this->getExtremeOfMonth($month, $year, $extreme);
		$this->debug("Got day $day for $extreme of $month, $year");
		$hour = $this->getExtremeHour($extreme);
		$minute = $this->getExtremeMinute($extreme);
		
		return mktime($hour, $minute, 0, $month, $day, $year);
	}
	
	/**
	 * Assumes that value is not *, and creates an array of valid numbers that 
	 * the string represents.  Returns an array.
	 */
	function expand_ranges($str){
		//$this->debug("Expanding $str");
		if (strstr($str,  ",")){
			$tmp1 = explode(",", $str);
			//$this->debug($tmp1);
			$count = count($tmp1);
			for ($i=0;$i<$count;$i++){//Loop through each comma-separated value
				if (strstr($tmp1[$i],  "-")){ //If there's a range in this place, expand that too
					$tmp2 = explode("-", $tmp1[$i]);
					//$this->debug("Range:");
					//$this->debug($tmp2);
					for ($j=$tmp2[0];$j<=$tmp2[1];$j++){
						$ret[] = $j;
					}
				} else {//Otherwise, just add the value
					$ret[] = $tmp1[$i];
				}
			} 
		} else if (strstr($str,  "-")){//There might only be a range, no comma sep values at all.  Just loop these
			$range = explode("-", $str);
			for ($i=$range[0];$i<=$range[1];$i++){
				$ret[] = $i;
			}
		} else {//Otherwise, it's a single value
			$ret[] = $str;
		}
		//$this->debug($ret);
		return $ret;
	}
	
	/**
	 * Given a string representation of a set of weekdays, returns an array of
	 * possible dates.
	 */
	function getWeekDays($str, $month, $year){
		$daysInMonth = $this->daysinmonth($month, $year);
		
		if (strstr($str,  ",")){
			$tmp1 = explode(",", $str);
			$count = count($tmp1);
			for ($i=0;$i<$count;$i++){//Loop through each comma-separated value
				if (strstr($tmp1[$i],  "-")){ //If there's a range in this place, expand that too
					$tmp2 = explode("-", $tmp1[$i]);
					
					for ($j=$start;$j<=$tmp2[1];$j++){
						for ($n=1;$n<=$daysInMonth;$n++){
			 				if ($j == jddayofweek(gregoriantojd ( $month, $n, $year),0)){
			 					$ret[] = $n;
			 				}			 				
			 			}
					}
				} else {//Otherwise, just add the value
					for ($n=1;$n<=$daysInMonth;$n++){
	 					if ($tmp1[$i] == jddayofweek(gregoriantojd ( $month, $n, $year),0)){
	 						$ret[] = $n;
	 					}			 				
	 				}
				}
			} 
		} else if (strstr($str,  "-")){//There might only be a range, no comma sep values at all.  Just loop these
			$range = explode("-", $str);
			for ($i=$start;$i<=$range[1];$i++){
				for ($n=1;$n<=$daysInMonth;$n++){
	 				if ($i == jddayofweek(gregoriantojd ( $month, $n, $year),0)){
	 					$ret[] = $n;
	 				}			 				
	 			}
			}
		} else {//Otherwise, it's a single value
			for ($n=1;$n<=$daysInMonth;$n++){				
				if ($str == jddayofweek(gregoriantojd ( $month, $n, $year),0)){
					$ret[] = $n;
				}			 				
			}
		}
		
		return $ret;		
	}
	
 	function daysinmonth($month, $year){
       if(checkdate($month, 31, $year)) return 31;
       if(checkdate($month, 30, $year)) return 30;
       if(checkdate($month, 29, $year)) return 29;
       if(checkdate($month, 28, $year)) return 28;
       return 0; // error
   }	
   
   /**
    * Get the timestamp of the last ran time.
    */
   function calcLastRan(){
		$now = time();

		if ($now < $this->getExtremeMonth("START")){
			//The cron isn't due to have run this year yet.  Getting latest last year
			$this->debug("Last ran last year");
			$tsLatestLastYear = $this->getExtremeMonth("END");	
			
			$this->debug("Timestamp of latest scheduled time last year is ".$tsLatestLastYear);
			$this->lastRan = $tsLatestLastYear;
			
			$year = date("Y", $this->lastRan);
			$month = date("m", $this->lastRan);
			$day = date("d", $this->lastRan);
			$hour = date("h", $this->lastRan);
			$minute = date("i", $this->lastRan);		
			
			
		} else { //Cron was due to run this year.  Determine when it was last due
			$this->debug("Cron was due to run earlier this year");
	   		$year = $this->now[5];	   		
	   		
   			$arMonths = $this->expand_ranges($this->bits[3]);
   			if (!in_array($this->now[3], $arMonths) && $this->bits[3] != "*"){//Not due to run this month.  Get latest of last month
   				$this->debug("Cron was not due to run this month at all. This month is ".$this->now[3]);
   				$this->debug("Months array: ");
   				$this->debug($arMonths);
   				sort($arMonths);
				do{
					$month = array_pop($arMonths);
				} while($month > $this->now[3]);
				$day = $this->getExtremeOfMonth($month, $this->now[5], "END");
	   			$hour = $this->getExtremeHour("END");
	   			$minute = $this->getExtremeMinute("END");	
   			} else if ($now < $this->getExtremeOfMonth($this->now[3], $this->now[5], "START")){ //It's due in this month, but not yet.
   				$this->debug("It's due in this month, but not yet.");
   				sort($arMonths);
				do{
					$month = array_pop($arMonths);
				} while($month > $this->now[3]);
				$day = $this->getExtremeOfMonth($month, $this->now[5], "END");
	   			$hour = $this->getExtremeHour("END");
	   			$minute = $this->getExtremeMinute("END");
   			} else {//It has been due this month already
   				$this->debug("Cron has already been due to run this month (".$month = $this->now[3].")");
	   			$month = $this->now[3];		
	   			$this->debug("Getting days array");
	   			$days = $this->getDaysArray($this->now[3]);
	   			
	   			if (!in_array($this->now[2], $days)){
	   				$this->debug("Today not in the schedule.  Getting latest last due day");
	   				//No - Get latest last scheduled day   				
	   				sort($days);
	   				do{
	   					$day = array_pop($days);
	   				} while($day > $this->now[2]);
	   				
	   				$hour = $this->getExtremeHour("END");
	   				$minute = $this->getExtremeMinute("END");
	   				
	   			} else if($this->now[1] < $this->getExtremeHour("START")){//Not due to run today yet
	   				$this->debug("Cron due today, but not yet.  Getting latest on last day");
	   				sort($days);
	   				do{
	   					$day = array_pop($days);
	   				} while($day >= $this->now[2]);
	   				
	   				$hour = $this->getExtremeHour("END");
	   				$minute = $this->getExtremeMinute("END");
	   			} else {
	   				$this->debug("Cron has already been due to run today");
	   				$day = $this->now[2];
	   				//Yes - Check if this hour is in the schedule?
	   				
	   				$arHours = $this->expand_ranges($this->bits[1]);
	   				
	   				if (!in_array($this->now[1], $arHours) && $this->bits[1] != "*"){
	   					$this->debug("Cron not due in this hour, getting latest in last scheduled hour");
	   					//No - Get latest last hour
	   					sort($arHours);
	   					do{
	   						$hour = array_pop($arHours);
	   						//$this->debug("hour is $hour, now is ".$this->now[1]);
	   					} while($hour > $this->now[1]);
	   					
	   					$minute = $this->getExtremeMinute("END");
	   					
	   				} else if ($now < $this->getExtremeMinute("START") && $this->bits[1] != "*"){ //Not due to run this hour yet
	   					sort($arHours);
	   					do{
	   						$hour = array_pop($arHours);
	   					} while($hour >= $this->now[1]);
	   					$minute = $this->getExtremeMinute("END");
	   				} else {
	   					//Yes, it is supposed to have run this hour already - Get last minute
	   					$hour = $this->now[1];
	   					if ($this->bits[0] != "*"){
		   					$arMinutes = $this->expand_ranges($this->bits[0]);
		   					$this->debug($arMinutes);
		   					do{
		   						$minute = array_pop($arMinutes);		   						
		   					} while($minute >= $this->now[0]);
		   					
		   					//If the first time in the hour that the cron is due to run is later than now, return latest last hour
							if($minute > $this->now[1] || $minute == ""){
		   						$this->debug("Valid minute not set");
		   						$minute = $this->getExtremeMinute("END"); //The minute will always be the last valid minute in an hour
		   						//Get the last hour.
		   						if ($this->bits[1] == "*"){
		   							$hour = $this->now[1] - 1;
		   						} else {
			   						$arHours = $this->expand_ranges($this->bits[1]);
			   						$this->debug("Array of hours:");
			   						$this->debug($arHours);
			   						sort($arHours);
		   							do{
		   								$hour = array_pop($arHours);
		   							} while($hour >= $this->now[1]);
		   						}
		   					}
		   					
	   					} else {
	   						$minute = $this->now[0] -1; 
	   					}
	   				}  				
	   				
	   			}
	   			
	   		} 
		}
   		$this->debug("LAST RAN: $hour:$minute on $day/$month/$year");
   		$this->lastRan = mktime($hour, $minute, 0, $month, $day, $year);

   }
     
   function getExtremeOfMonth($month, $year, $extreme){
   		$daysInMonth = $this->daysinmonth($month, $year);
		if ($this->bits[2] == "*"){
			if ($this->bits[4] == "*"){//Is there a day range?
				//$this->debug("There were $daysInMonth days in $month, $year");
				if ($extreme == "END"){
					$day = $daysInMonth;
				} else {
					$day=1;
				}
			} else {//There's a day range.  Ignore the dateDay range and just get the list of possible weekday values.
				$days = $this->getWeekDays($this->bits[4],$month, $year);
				$this->debug($this->bits);
				$this->debug("Days array for ".$this->bits[4].", $month, $year:");
				$this->debug($days);
				if ($extreme == "END"){
					sort($days);
				} else {
					rsort($days);	
				}
				$day = array_pop($days);
			}
		} else {
			$days = $this->expand_ranges($this->bits[2]);
			if ($extreme == "END"){
				sort($days);
			} else {
				rsort($days);	
			}
			
			do {
				$day = array_pop($days);
			} while($day > $daysInMonth);
		}	
		//$this->debug("$extreme day is $day");
		return $day;
   }
     
   function getDaysArray($month){
   		$this->debug("Getting days for $month");
   		$days = array();
   		
   		if ($this->bits[4] != "*"){   			
   			$days = $this->getWeekDays($this->bits[4], $month, $this->now[5]);
   			$this->debug("Weekdays:");
   			$this->debug($days);
   		} 
		if ($this->bits[2] != "*" && $this->bits[4] == "*") {
			$days = $this->expand_ranges($this->bits[2]);
   		} 
   		if ($this->bits[2] == "*" && $this->bits[4] == "*"){
   			//Just return every day of the month
   			$daysinmonth = $this->daysinmonth($month, $this->now[5]);
   			$this->debug("Days in ".$month.", ".$this->now[5].": ".$daysinmonth);
   			for($i = 1;$i<=$daysinmonth;$i++){
   				$days[] = $i;
   			}
   		}
   		$this->debug($days);
   			
   		return $days;
   }
   
   function getExtremeHour($extreme){
   		if ($this->bits[1] == "*"){
			if ($extreme == "END"){
				$hour = 23;
			} else {
				$hour = 0;	
			}
		} else {
			$hours = $this->expand_ranges($this->bits[1]);
			if ($extreme == "END"){
				sort($hours);
			} else {
				rsort($hours);	
			}
			$hour = array_pop($hours);
		}
		//$this->debug("$extreme hour is $hour");
		return $hour;
   }
   
   function getExtremeMinute($extreme){
		if ($this->bits[0] == "*"){
			if ($extreme == "END"){
				$minute = 59;
			} else {
				$minute = 0;	
			}
		} else {
			$minutes = $this->expand_ranges($this->bits[0]);
			if ($extreme == "END"){
				sort($minutes);
			} else {
				rsort($minutes);	
			}
			$minute = array_pop($minutes);
		}
		//$this->debug("$extreme minute is $minute");
		return $minute;
   }

 }
?>
