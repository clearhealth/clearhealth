<?php
require_once CELLINI_ROOT."/controllers/Controller.class.php";

class CalendarController extends Controller {
	
	
	function CalendarController() {
		parent::Controller();	
		$this->assign('EVENT_DELETE_ACTION',Cellini::link('delete','location'));
		$this->assign('UPDATE_SCHEDULE_ACTION',Cellini::link('update_schedule','location'));
		$this->assign('SCHEDULE_LIST_ACTION',Cellini::link('schedule_list','location'));
		$this->assign('EDIT_APPOINTMENT_ACTION',Cellini::link('edit_appointment','location'));
	}	
	
	function build_day_increments($date ="",$start_hour = 0, $end_hour = 24,$header_time = false) {
		if (empty($date)){
			$date = date("Y-m-d",$date);	
		}
		else {
			$date = date("Y-m-d", strtotime($date));	
		}
		
		$base = 0;
		if (!$header_time) {
			$base = strtotime($date);	
		}
				
		$incs = array();
		$stime = $base + ($start_hour * 60 * 60);
		$loop_end = ($end_hour * 4) + 1;
		for($i=1;$i<$loop_end;$i++) {
			($i > 48) ? $period = $i - 48: $period = $i;
				
			$incs[$stime] = $period; 
			//add fifteen minutes, this is the default day increment
			$stime += 900;	
		}
		
		//print_r($incs);
		return $incs;	
	}
	
	function build_table_map_week($incs, $days_events) {
		$week_table_map['tablemap'] = array();
		$week_table_map['tablemap2'] = array();
		$week_table_map['maxcols'] = array();
		if (!is_array($days_events)) $days_events = array();
		foreach ($days_events as $day_ts => $events) {
			
			if (isset($incs[$day_ts]))  {
			$map = $this->build_table_map($incs[$day_ts], $events);
			$week_table_map['tablemap'][$day_ts] = $map['tablemap'];
			$week_table_map['tablemap2'][$day_ts] = $map['tablemap2'];
			$week_table_map['maxcols'][$day_ts] = $map['maxcols'];
			}
		}
		return $week_table_map;		
	}
	
	function build_table_map($incs, $events) {
		
		$map['tablemap'] = array();
		$map['tablemap2'] = array();
		$map['rowmap'] = array();
		$map['maxcols'] = 0;
		
		$times = array();
		$times2 = array();
		
		//print_r($events);
		//print_r($incs);
		
		if (!is_array($incs)) $incs = array();
		
		foreach ($incs as $inc => $inc_count) {
			if (!isset($times[$inc])) $times[$inc] = 0;
			if (!isset($times2[$inc])) $times2[$inc] = 0;
			
			if (isset($events[$inc])) {
				foreach ($events[$inc] as $ev) {
					$end_inc = $inc + ($ev['duration_increments'] * 900);
					
					for($i=$inc;$i<$end_inc;$i+=900) {
						if (!isset($times[$i])) {
							$times[$i] = 0;
						}
						$times[$i] += 1;
						//echo "ev: " . $ev['id'] . "<br>";
						if (!isset($times2[$ev['occurence_id']])) {
							$times2[$ev['occurence_id']] = 0;
						}
						$times2[$ev['occurence_id']] += 1;
						 
						//echo "evid: " . $ev['event_id'] . " ecic: " . $ev['duration_increments'] . " :: i: $i :: ec: $end_inc :: time: " . date("H:i:s",$i) . " :: etime: " . date("H:i:s",$end_inc) . ":: c: " . $times[$i] . "ev: " . $ev['notes'] . "<br>";
					}
				}
			}
		}
		
		$map['tablemap2'] = $times2;
		$map['tablemap'] = $times;
		
		$maxcols=0;
		$tk = array_keys($times);
		for($i=count($times)-1;$i>-1;$i--) {
			if ($times[$tk[$i]] > $maxcols) $maxcols = $times[$tk[$i]];
		}
		for($i=count($times)-1;$i>-1;$i--) {
			if ($times[$tk[$i]] == 0 ) {
				$times[$tk[$i]] = $maxcols;
			}
			else {
				//echo "time: " . $tk[$i] . " :: tm: ". $times[$tk[$i]] . ":: mc: $maxcols ::<br>";
				$times[$tk[$i]] = ($maxcols) - $times[$tk[$i]];
			}
		}
		
		$map['maxcols'] = $maxcols;
		return $map;
	}
	
	
}

?>
