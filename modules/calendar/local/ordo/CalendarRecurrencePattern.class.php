<?php

$GLOBALS['loader']->requireOnce("ordo/ORDataObject.class.php");

/**
 * ORDO for recurrencepattern table
 */
 
class CalendarRecurrencePattern extends ORDataObject{
	
	/**
	 *	
	 *	@var int
	 */
	var $recurrence_pattern_id = '';

	/**
	 * Type of pattern based on recurrenceType ENUM
	 *	(day,week,month,year)
	 * @var string
	 */
	var $pattern_type = '';

	/**
	 * Number of days/weeks/months to repeat on
	 *
	 * @var int
	 */
	var $number = '';
	
	/**
	 * Day of week (using days ENUM) if used
	 * (0 - 6, Monday - Sunday)
	 * @var int
	 */
	var $weekday = '';
	
	/**
	 * Month of Year (using months ENUM) if used
	 * (01 - 12, January - December)
	 * @var int
	 */
	var $month = '';
	
	/**
	 * Day of month if used
	 *
	 * @var int
	 */
	var $monthday = '';
	
	/**
	 * String describing which week of month (First, Second, Third, Fourth, Last)
	 *
	 * @var string
	 */
	var $week_of_month = '';
	
	var $_table = 'recurrence_pattern';
	var $_key = 'recurrence_pattern_id';
	var $_internalName = 'CalendarRecurrencePattern';
	
	function CalendarRecurrencePattern($id = 0){
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}
	

	function &findPattern($data){
		$type=$data['pattern_type'];
		$number=$data['number'];
		$month=@$data['month'];
		$monthday=@$data['monthday'];
		$weekday=@$data['weekday'];
		$week_of_month=@$data['week_of_month'];

		$criteria = "$this->_table.pattern_type = ".$this->dbHelper->quote($type)." AND ".
			"$this->_table.number = ".$this->dbHelper->quote($number)." AND ".
			"$this->_table.weekday = ".$this->dbHelper->quote($weekday)." AND ".
			"$this->_table.month = ".$this->dbHelper->quote($month)." AND ".
			"$this->_table.week_of_month = ".$this->dbHelper->quote($week_of_month)." AND ".
			"$this->_table.monthday = ".$this->dbHelper->quote($monthday);
		$finder =& new ORDOFinder($this->name(),$criteria);
		$rps = $finder->find();
		if($rps->count() > 0){
			$rp =& $rps->current();
		} else {
			$rp = false;
		}
		return $rp;
	}

	/**
	 * Returns only the id of the event created
	 *
	 * @param string $date
	 * @param string $start_time
	 * @param string $end_time
	 * @param string $ordoName
	 * @param object $ordo
	 * @return int
	 */
	function _createEvent($date,$start_time,$end_time,$ordoName='CalendarEvent',$table) {
		$last_id = $this->dbHelper->nextId("sequences");
		$sql = "INSERT INTO $table (`event_id`,`start`,`end`) VALUES (" . $last_id . ", " .$this->dbHelper->quote(date('Y-m-d',strtotime($date)).' '.$start_time).",".$this->dbHelper->quote(date('Y-m-d',strtotime($date)).' '.$end_time).")";
		$result = $this->dbHelper->execute($sql);
		return $last_id;
	}
	
	/**
	 * Creates Events and returns array of Events created
	 *
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $start_time
	 * @param string $end_time
	 * @return array
	 */
	function createEvents($start_date,$end_date,$start_time,$end_time,$ordoName = 'CalendarEvent'){
		$ocs=array();
		$date=$start_date;
		$start_datets=strtotime($start_date);
		$end_datets=strtotime($end_date);
		$datets=strtotime($date);
		$ordo =& Celini::newORDO($ordoName);
		$table = $ordo->tableName();
		switch($this->get('pattern_type')){
			case 'day':
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
					$date=date('Y-m-d',strtotime("+".$this->get('number')." Day",strtotime($date)));
				}
				break;
			case 'monthday':
				$num=$this->get('number');
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
					list($year,$month,$day)=explode('-',$date);
					$checker=false;
					if($day < $this->get('monthday')){
						$day=$this->get('monthday');
						$checker=true;
					} else {
						while($checker==false){
							if($month+$num > 12){
								$month=$month+$num-12;
								$year++;
							} else {
								$month=$month+$num;
							}
							$checker=checkdate($month,$day,$year);
						}
					}
					$time=mktime(0,0,0,$month,$day,$year);
					$date=date('Y-m-d',$time);
				}
				break;
			case 'monthweek':
				$em=&Celini::enumManagerInstance();
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
					$tmpdate=false;
					$num=$this->get('number');
					list($year,$month,$day)=explode('-',$date);
					$day=1;
					while(!$tmpdate && mktime(0,0,0,$month,1,$year) < $end_datets){
						if($month+$num > 12){
							$month=$month+$num-12;
							$year++;
						} else {
							$month=$month+$num;
						}
						$datets=mktime(0,0,0,$month,1,$year);
						$time=mktime(0,0,0,$month,1,$year);
						$wd=$this->get('weekday');
						$weekday=$em->lookup('days_of_week',$wd);
						$wk=$this->get('week_of_month');
						if($wk=='Last'){
							// Try Fifth weekday
							if(date('m',strtotime("Fifth $weekday",$datets))==$month){
								$tmpdate=date('Y-m-d',strtotime("Fifth $weekday",$datets));
								// Try Fourth weekday
							} elseif(date('m',strtotime("Fourth $weekday",$datets))==$month){
								$tmpdate=date('Y-m-d',strtotime("Fourth $weekday",$datets));
							} elseif(date('m',strtotime("Third $weekday",$datets))==$month) {
								// Third weekday?
								$tmpdate=date('Y-m-d',strtotime("Third $weekday",$datets));
							}
						} elseif($wk=='Second') { // We have to do this because there is no 'Second' for strtotime
							$tmpdate=date('Y-m-d',strtotime("Third $weekday -7 Day",$datets));
						} else {
							$tmpdate=date('Y-m-d',strtotime("$wk $weekday",$datets));
						}
					}
					if(!$tmpdate){
						$tmpdate='3000-01-01';
					}
					$date=$tmpdate;
				}
				break;
			case 'yearmonthday':
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
					list($year,$month,$day)=explode('-',$date);
					$num=$this->get('number');
					$checker=false;
					if($day != $this->get('monthday') || (int)$month != (int)$this->get('month')){
						$day=(int)$this->get('monthday');
						if($month < $this->get('month')){
							$month=$this->get('month');
						} elseif($month > $this->get('month')){
							$month=$this->get('month');
							$year=$year+$num;
						}
						if(checkdate($month,$day,$year)){
							$date="$year-$month-$day";
							$checker=true;
						}
					}
					while($checker===false){
						$year=$year+$num;
						if(mktime(0,0,0,$month,$day,$year) > $end_datets){
							$date="$year-$month-$day";
							$checker=true;
						} else {
							$checker=checkdate($month,$day,$year);
						}
					}
					$date="$year-$month-$day";
				}
				break;
			case 'yearmonthweek':
				$em=&Celini::enumManagerInstance();
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
					$tmpdate=false;
					$num=$this->get('number');
					list($year,$month,$day)=explode('-',$date);
					$day=1;
					while(!$tmpdate && mktime(0,0,0,$month,1,$year) < $end_datets){
						$year=$year+$num;
						$month=$this->get('month');
						$datets=mktime(0,0,0,$month,1,$year);
						$time=mktime(0,0,0,$month,1,$year);
						$wd=$this->get('weekday');
						$weekday=$em->lookup('days_of_week',$wd);
						$wk=$this->get('week_of_month');
						if($wk=='Last'){
							// Try Fifth weekday
							if(date('m',strtotime("Fifth $weekday",$datets))==$month){
								$tmpdate=date('Y-m-d',strtotime("Fifth $weekday",$datets));
								// Try Fourth weekday
							} elseif(date('m',strtotime("Fourth $weekday",$datets))==$month){
								$tmpdate=date('Y-m-d',strtotime("Fourth $weekday",$datets));
								// Third weekday
							} else {
								$tmpdate=date('Y-m-d',strtotime("Third $weekday",$datets));
							}
						} elseif($wk=='Second') { // We have to do this because there is no 'Second' for strtotime
							$tmpdate=date('Y-m-d',strtotime("Third $weekday -7 Day",$datets));
						} else {
							$tmpdate=date('Y-m-d',strtotime("$wk $weekday",$datets));
						}
					}
					if(!$tmpdate){
						$tmpdate='3000-01-01';
					}
					$date=$tmpdate;
				}
				break;
			case 'dayweek':
				$em=&Celini::enumManagerInstance();
				$num=$this->get('number');
				$days = array();
				for($i = 7;$i > 0;$i--) {
					if($num & pow(2,$i)) {
						$days[$i] = $em->lookup('days_of_week',$i);;
					}
				}
				$wkday=(int)date('u',strtotime($date)) - 1;
				while(strtotime($date) >= $start_datets && strtotime($date) <= $end_datets){
					$datets = strtotime($date);
					$dayname = date('l',$datets);
					if(in_array($dayname,$days)) {
						if($datets <= $end_datets) {
							$ocs[]=$this->_createEvent($date,$start_time,$end_time,$ordoName,$table);
						}
					}
					$date = date('Y-m-d',strtotime("+1 day",$datets));
				}
				break;
		}
		return $ocs;
	}

	function getString(){
		switch($this->get('pattern_type')){
			case 'day':
				return "Every ".$this->get('number')." Days";
				break;
			case 'monthweek':
				$em=&Celini::enumManagerInstance();
				$wknum=$em->lookup('weeks_of_month',$this->get('week_of_month'));
				$wkday=$em->lookup('days_of_week',$this->get('weekday'));
				$x='Month';
				if($this->get('number') > 1){
					$x=$this->get('number')." Months";
				}
				return "The $wknum $wkday of Every $x";
				break;
			case 'monthday':
				$x='Month';
				if($this->get('number') > 1){
					$x=$this->get('number')." Months";
				}
				return "The ".$this->get('monthday')." of Every $x";
				break;
			case 'yearmonthweek':
				$em=&Celini::enumManagerInstance();
				$wknum=$em->lookup('weeks_of_month',$this->get('week_of_month'));
				$wkday=$em->lookup('days_of_week',$this->get('weekday'));
				$month=$em->lookup('months_of_year',$this->get('month'));
				$x='Year';
				if($this->get('number') > 1){
					$x=$this->get('number')." Years";
				}
				return "The $wknum $wkday of $month, Every $x";
				break;
			case 'yearmonthday':
				$em=&Celini::enumManagerInstance();
				$month=$em->lookup('months_of_year',$this->get('month'));
				$x='Year';
				if($this->get('number') > 1){
					$x=$this->get('number')." Years";
				}
				$mday=$this->get('monthday');
				return "The $mday of $month, Every $x";
				break;
			case 'dayweek':
				$em =& Celini::enumManagerInstance();
				$daysarray = $em->enumArray('days_of_week');
				$days = array();
				$num =& $this->get('number');
				foreach(array_keys($daysarray) as $i) {
					if($num & pow(2,$i)) {
						$days[] = $em->lookup('days_of_week',$i);
					}
				}
				return implode(', ',$days).' of every week';
				break;
			default:
				return 'Unknown recurrence pattern type!!!';
				break;
		}
	}

}

?>
