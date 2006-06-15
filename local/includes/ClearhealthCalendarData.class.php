<?php
$loader->requireOnce('lib/PEAR/Image/Color.php');
class ClearhealthCalendarData {
	var $filters;
	var $schedules = null;
	var $events = null;
	var $interval = 900;

	function ClearhealthCalendarData() {
		$GLOBALS['loader']->requireOnce('includes/PracticeConfig.class.php');
		$pconfig =& Celini::configInstance('practice');
		$increment = $pconfig->get('CalendarIncrement',900);
		$this->filters['start'] = array('type' => 'DateTime', 'label' => 'Start Date', 'params' => array('hidden'=>true));
		$this->filters['end'] = array('type' => 'DateTime', 'label' => 'End Date', 'params' => array('hidden'=>true));
		$this->filters['starttime'] = array('type' => 'Time', 'label' => 'Start Time', 'params' => array('increment'=>$increment/60));
		$this->filters['endtime'] = array('type' => 'Time', 'label' => 'End Time', 'params' => array('increment'=>$increment/60));
		$this->filters['user'] = array('type' => 'Multiselect', 'label' => 'Provider', 'params' => array('type' => 'form','insertBlank'=>true));
		$this->filters['patient'] = array('type' => 'Suggest', 'label' => 'Patient', 'params' => array('jsfunc'=>'patientSuggest','person'=>true) );
	}
	
	function getConfig() {
		$GLOBALS['loader']->requireOnce('includes/PracticeConfig.class.php');
		$pconfig =& Celini::configInstance('practice');
		$config = array();
		$config['increment'] = $pconfig->get('CalendarIncrement',900);
		$this->interval = $config['increment'];
		$config['hour_start'] = $pconfig->get('CalendarHourStart',8);
		$config['hour_length'] = $pconfig->get('CalendarHourLength',12);
		return $config;
	}
    
	/**
	 * This function will output any extra items to render on the calendar
	 * Most likely popups, buttons, etc
	 *
	 */
	function extraDisplay() {
		$view =& new clniView();
		return $view->fetch('calendar/general_extradisplay.html');
	}

	function getFilterTypes(){
		return $this->filters;
	}
    
	function getFilterOptions($filter_name){
		$options = array();
		switch ($filter_name){
			case 'user':
				$user =& Celini::newORDO('Provider');
				$options = $user->valueList('username');
				return $options;
			case 'patient':
				$db = Celini::dbInstance();
				$sql = "SELECT person.person_id, CONCAT(last_name,' ',first_name) AS pname FROM patient INNER JOIN person ON patient.person_id = person.person_id WHERE 1 ORDER BY last_name ASC, first_name ASC";
				$result = $db->Execute($sql);
				if(!$result){
					Celini::raiseError($db->ErrorMsg());
				}
				$options = $result->GetAssoc();
				return $options;
			default:
				return FALSE;
		}
	}

	var $_toWhereEvent = false;
	var $_toWhereSched = false;
	function toWhere(&$filters,$forevent=true){
		if ($forevent == true && $this->_toWhereEvent) {
			return $this->_toWhereEvent;
		}
		if ($forevent == false && $this->_toWhereSched) {
			return $this->_toWhereSched;
		}

		$criteriaArray = array();
		$db =& Celini::dbInstance();
		if(isset($filters['start']) && !is_null($filters['start']->getValue())) {
			if($forevent == false) {
		       	$criteriaArray[] = "UNIX_TIMESTAMP(event.start) >= ".$db->quote(strtotime($filters['start']->getValue()));
			} else {
				$time= $filters['starttime']->getValue();
				$time = date('H:i:s',mktime($time['ap']=='AM' ? ($time['hour']=='12' ? '00' : $time['hour']) : $time['hour']+12,$time['minute'],$time['second']));
		       	$criteriaArray[] = "aevent.start >= ".$db->quote($filters['start']->getValue().' '.$time);
			}
		}
		
		if(isset($filters['end']) && !is_null($filters['end']->getValue())) {
			if($forevent == false) {
				$criteriaArray[] = "UNIX_TIMESTAMP(event.start) <= ".$db->quote(strtotime($filters['end']->getValue()));
			} else {
				$time= $filters['endtime']->getValue();
				$time = date('H:i:s',mktime($time['ap']=='AM' ? $time['hour'] : $time['hour']+12,$time['minute'],$time['second']));
		       	$criteriaArray[] = "aevent.start <= ".$db->quote($filters['end']->getValue().' '.$time);
			}
		}
		if(isset($filters['user']) && count($filters['user']->getValue()) > 0) {
			$string = array();
			foreach($filters['user']->getValue() as $uid) {
				$string[] = "u.user_id = ".$db->quote($uid);
			}
			$criteriaArray[] = '('.implode(' OR ',$string).')';
		}
		if($forevent == true && isset($filters['patient']) && $filters['patient']->getValue() > 0) {
			$patientfilter = $filters['patient']->getValue();
			if($patientfilter['id'] != '') {
				$criteriaArray[] = 'patient.person_id = '.$db->quote($patientfilter['id']);
			}
		}
		$out = implode(' AND ',$criteriaArray);
		if($forevent) {
			$this->_toWhereEvent = $out;
		} else {
			$this->_toWhereSched = $out;
		}
		return $out;
	}
	
	/**
	 * This creates a where statement to find events that are not in the 
	 * current scope (starting before day-start or after day-end)
	 *
	 * @param array $filters
	 * @return string
	 */
	function toAntiWhere(&$filters){
		$criteriaArray = array();
		$db =& Celini::dbInstance();
		if(isset($filters['start']) && !is_null($filters['start']->getValue())) {
		       	$criteriaArray[] = "UNIX_TIMESTAMP(event.start) < ".$db->quote(strtotime($filters['start']->getValue()));
		}
		if(isset($filters['end']) && !is_null($filters['end']->getValue())) {
			$criteriaArray[] = "UNIX_TIMESTAMP(event.start) > ".$db->quote(strtotime($filters['start']->getValue()));
		}
		if(isset($filters['user']) && count($filters['user']->getValue()) > 0) {
			$string = array();
			foreach($filters['user']->getValue() as $uid) {
				$string[] = "provider.person_id = ".$db->quote($uid);
			}
			$criteriaArray[] = '('.implode(' OR ',$string).')';
		}
		if(isset($filters['patient']) && $filters['patient']->getValue() > 0) {
			$patientfilter = $filters['patient']->getValue();
			if($patientfilter['id'] != '') {
				$criteriaArray[] = 'patient.person_id = '.$db->quote($patientfilter['id']);
			}
		}
		$out = implode(' AND ',$criteriaArray);
		
		return $out;
	}

	function &getFinder(&$filters,$ordoName = 'CalendarEvent') {
		$event =& Celini::newORDO($ordoName);
		$finder =& $event->relationshipFinder();
		$this->_setFinderCriteria($finder,$filters);
		$this->_setFinderRelationships($finder,$filters);
		return $finder;
	}
	
	function _setFinderRelationships(&$finder,$filters) {
		if(isset($filters['user']) && count($filters['user']->getValue()) > 0) {
			foreach($filters['user']->getValue() as $uid) {
				$user =& Celini::newORDO('Provider',$uid);
				$finder->addParent($user);
			}
		}
		if(isset($filters['patient']) && $filters['patient']->getValue() > 0) {
			$patient =& Celini::newORDO('Patient',$filters['patient']->getValue());
			$finder->addChild($patient);
		}
	}
	
	function _setFinderCriteria(&$finder,$filters) {
		$criteriaArray = array();
		$db =& Celini::dbInstance();
		if(isset($filters['start']) && !is_null($filters['start']->getValue())) {
			$finder->addCriteria("UNIX_TIMESTAMP(event.start) >= ".$db->quote(strtotime($filters['start']->getValue())));
		}
		if(isset($filters['end']) && !is_null($filters['end']->getValue())) {
			$finder->addCriteria("UNIX_TIMESTAMP(event.start) <= ".$db->quote(strtotime($filters['end']->getValue())));
		}
	}


	function &getSchedules(&$filters) {
		if(is_null($this->schedules)) {
			$this->schedules =& $this->providerSchedules($filters);
		}
		return $this->schedules;
	}

	function providerData() {
		$db = new clniDb();
		$sql = "SELECT
				user.person_id,
				color,
				nickname,
				CONCAT(p.last_name,', ',p.first_name) AS name
			from
				user
				INNER JOIN provider using(person_id)
				INNER JOIN person p ON user.person_id=p.person_id
			";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['person_id']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Provide an array of schedules and the html to use as their headings
	 */
	function &getScheduleList(&$filters) {
		if(is_null($this->schedules)) {
			$s = $this->providerSchedules($filters);
			$this->schedules =& $s;
		} else {
			$s =& $this->schedules;
		}
		$map = $this->eventProviderMap($filters);
		if (!is_array($map)) {
			$map = array();
		}
		$pdata = $this->providerData();
		$ret = array();
		foreach($s as $providerId => $schedules) {
			if (count($schedules) == 0) {
				if(!in_array($providerId,$map)) {
					continue;
				}
			}
			$color = $pdata[$providerId]['color'];

			$ic = new Image_Color();
			$ic->setColors($color,$color);

			$ic->changeLightness(-20);
			$border = $ic->_returnColor($ic->color1);
			$ic->changeLightness(40);
			$background = $ic->_returnColor($ic->color1);
			$font = $ic->getTextColor($color);
			$ret[$providerId] = array(
				'color' => $color,
				'borderColor' => $border,
				'backColor' => $background,
				'fontColor' => $font,
				'label' => $pdata[$providerId]['name'],
				'schedules' => $schedules
			);
			$head =& Celini::HTMLHeadInstance();
			$head->addInlineCss(".calendarEvent$providerId { background-color: #$background; }");
		}
		return $ret;
	}

	/**
	 * Returns an array of $event_id=>$provider_id(person_id)
	 *
	 * @param array $filters
	 * @return array
	 */
	function eventProviderMap(&$filters) {
		$db = new clniDb();
		$where = $this->toWhere($filters,false);
		$sql = "SELECT 
				event.event_id, 
				provider.person_id as provider_id 
			FROM 
				`event` AS event
				inner join appointment a on a.event_id = event.event_id
				left join provider on a.provider_id = provider.person_id
				LEFT JOIN user AS u ON u.person_id= provider.person_id
			WHERE 
				$where
			GROUP BY event.event_id
				";
		return $db->getAssoc($sql);
	}

	/**
	 *  Get the next appointments for a provider and a given time
	 *
	 *  @return array
	 */
	function prevAppointments($providerId,$start,$end) {
		$db = new clniDb();
		$s = $db->quote(date('Y-m-d H:i:s',strtotime($start.' -15min')));
		$e = $db->quote($end);
		$p = EnforceType::int($providerId);
		$sql = "SELECT
				event.*,
				a.*
			FROM
				event
				inner join appointment a on a.event_id = event.event_id
			WHERE
				event.end >= $s and
				event.start <= $e and
				a.provider_id = $p";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}

	function scheduleByProviderDay($providerId,$date) {
		$p = Enforcetype::int($providerId);
		$d = "'".date('Y-m-d',strtotime($date))."'";
		$where = "and provider.person_id = $p and date_format(event.start,'%Y-%m-%d') = $d";
		return $this->_providerSchedules($where);
	}
	
	/**
	 * Returns array[provider_id][start] = array('label','start','end')
	 *
	 * @param array $filters
	 * @return array
	 */
	function &providerSchedules(&$filters) {
		$ret = array();
		$where = $this->toWhere($filters,false);
		if(!empty($where)) {
			$where = ' AND ('.$where.')';
		}
		return $this->_providerSchedules($where);
	}

	function &_providerSchedules($where) {
		$db = new clniDb();
		$sql = "SELECT person_id FROM provider";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['person_id']] = array();
			$res->MoveNext();
		}
		
		$sql = "
			SELECT 
				event.event_id,
				event.title,
				UNIX_TIMESTAMP(event.start) AS start,
				UNIX_TIMESTAMP(event.end) AS end, 
				s.provider_id,
				u.username,
				r.name AS roomname
			FROM 
				`event` AS event
				INNER JOIN schedule_event se ON se.event_id=event.event_id
				INNER JOIN event_group eg ON eg.event_group_id=se.event_group_id
				INNER JOIN schedule s ON eg.schedule_id=s.schedule_id
				
				LEFT JOIN rooms r ON r.id=eg.room_id
				LEFT JOIN user u ON s.provider_id = u.person_id
			WHERE 
				1 $where
			ORDER BY 
			 	event.start";
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			if(!isset($ret[$res->fields['provider_id']])) {
				$ret[$res->fields['provider_id']] = array();
			}
			$ret[$res->fields['provider_id']][$res->fields['start']] = array(
				'label'=>$res->fields['title'],
				'start'=>$res->fields['start'],
				'end'=>$res->fields['end'],
				'display'=>!empty($res->fields['roomname']) ? $res->fields['roomname'].'<br />'.$res->fields['username'] : $res->fields['username']
			);
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Returns array of provider Appointments
	 * array[provider_id][] = array('label','start','end')
	 *
	 * @param array $filters
	 * @param string $renderType Specifies how the appointment will be rendered.  'day' will have edit links
	 * @param int $period Number of seconds in an iteration
	 * @return array
	 * @todo Should we add the html for these events here?
	 */
	function &providerEvents(&$filters,$renderType = 0,$period = 900) {
		$db = new clniDb();
		$e =& Celini::newORDO('CalendarEvent');
		$where = $this->toWhere($filters);
		if(!empty($where)) {
			$where = ' AND ('.$where.')';
		}
		$sql = "
			SELECT 
				a.appointment_id,
				aevent.event_id,
				IF(schedule_code = 'PS',1,IF(s.schedule_id IS NULL AND a.patient_id = 0,2,0)) AS schedule_sort,
				provider.person_id provider_id,
				UNIX_TIMESTAMP(aevent.start) AS start_ts,
				UNIX_TIMESTAMP(aevent.end) AS end_ts
			FROM 
				`event` AS aevent 
				INNER JOIN appointment AS a ON aevent.event_id = a.event_id
				LEFT JOIN event_group eg ON eg.event_group_id=a.event_group_id
				LEFT JOIN schedule s ON eg.schedule_id=s.schedule_id
				LEFT JOIN rooms AS rm ON rm.id = a.room_id
				LEFT JOIN buildings AS b ON b.id = rm.building_id 
				LEFT JOIN provider ON a.provider_id = provider.person_id
				LEFT JOIN user AS u ON u.person_id= provider.person_id
				LEFT JOIN patient ON a.patient_id=patient.person_id
			WHERE 
				(s.schedule_code != 'NS' OR s.schedule_code IS NULL OR s.schedule_code = '') 
				$where
			GROUP BY aevent.event_id 
			ORDER BY
				schedule_sort DESC,
				aevent.start,
				aevent.end
				";
		/**
		 * If there's no schedule and no patient, it's an ADMIN event
		 */

		$res = $db->execute($sql);
		$ret = array();
		// Create the html output here so we don't have to haul around this huge array.
		$eventrender =& new CalendarEventRender();

		while($res && !$res->EOF) {
			if(!isset($ret[$res->fields['provider_id']])) {
				$ret[$res->fields['provider_id']] = array();
				$ret[$res->fields['provider_id']]['events'] = array();
			}
			if(!isset($ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']])) {
				$ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']] = array();
			}
			$ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']][$res->fields['event_id']] = array();

			$ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']][$res->fields['event_id']]['html'] = $eventrender->render($res->fields,$renderType);
			$ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']][$res->fields['event_id']]['start'] = $res->fields['start_ts'];
			$ret[$res->fields['provider_id']]['events'][$res->fields['start_ts']][$res->fields['event_id']]['end'] = $res->fields['end_ts'];
			
			$res->MoveNext();
		}
		return $ret;
	}
	
	/**
	 * Return array of conflicting event ids and the event they conflict
	 * (start after current-event-start but before current-event-end)
	 *
	 * @param array $filters
	 */
	function getConflictingEvents(&$filters) {
		if(is_null($filters['start']->getValue())) {
			$filters['start']->setValue(date('Y-m-d'));
		}
		$db = new clniDb();
		$where = $this->toWhere($filters);
		if(!empty($where)) {
			$where = ' AND ('.$where.')';
		}
		$sql = "SELECT 
				IF(schedule_code = 'PS',1,IF(s.schedule_id IS NULL AND (ec.patient_id = 0),2,0)) AS schedule_sort,
				UNIX_TIMESTAMP(event.start) start_ts,
				UNIX_TIMESTAMP(c.start) conflict_ts,
				UNIX_TIMESTAMP(c.end) end_ts,
				c.event_id AS conflict_event_id, 
				event.event_id AS event_id, 
				ea.provider_id
			FROM 
				event,
				event as c 
				INNER JOIN `event` AS aevent ON event.event_id=aevent.event_id
				INNER JOIN appointment ea on event.event_id = ea.event_id
				INNER JOIN appointment ec on c.event_id = ec.event_id
				LEFT JOIN event_group eg ON ea.event_group_id=eg.event_group_id
				LEFT JOIN schedule s ON eg.schedule_id=s.schedule_id
				LEFT JOIN provider ON ea.provider_id = provider.person_id
				LEFT JOIN user u ON u.person_id = provider.person_id
				LEFT JOIN patient ON ea.patient_id=patient.person_id OR ec.patient_id=patient.person_id
			WHERE 
			( (c.start >= event.start AND c.start < event.end) or (event.start >= c.start AND  event.start < c.end) )
			and ea.provider_id = ec.provider_id
			$where and c.event_id != event.event_id
			 ORDER BY 
			 	ec.created_date, event.start, c.start, c.event_id";
		$res = $db->execute($sql);
		$conflicts = array();
		$starts = array();

		while($res && !$res->EOF) {
			$start = $res->fields['start_ts'];
			$pid = $res->fields['provider_id'];

			if (!isset($conflicts[$pid][$res->fields['conflict_event_id']])) {
				$conflicts[$pid][$res->fields['conflict_event_id']] = array();
			}

			$conflicts[$pid][$res->fields['conflict_event_id']][$res->fields['event_id']] = $res->fields;
			$starts[$pid][$res->fields['conflict_event_id']] = $res->fields['conflict_ts'];
			$res->MoveNext();
		}
		// calc start/end times for overlap blocks
		$blocks = array();
		foreach($conflicts as $pid => $conflict) {
			foreach($conflict as $events) {
				foreach($events as $event) {
					$inBlock = false;
					if(isset($blocks[$pid])) {
						foreach($blocks[$pid] as $blockId => $block) {
							// event start is inside current block
							if ($event['conflict_ts'] >= $block['start'] && $event['conflict_ts'] <= $block['end']) {
								$inBlock = true;
								break;
							}
							// event end is inside current block
							if ($event['end_ts'] >= $block['start'] && $event['end_ts'] <= $block['end']) {
								$inBlock = true;
								break;
							}
							// block start is inside current event
							if ($block['start'] >= $event['conflict_ts'] && $block['end'] <= $event['end_ts']) {
								$inBlock = true;
								break;
							}
							// block end is inside current event
							if ($block['end'] >= $event['conflict_ts'] && $block['end'] <= $event['end_ts']) {
								$inBlock = true;
								break;
							}

						}
					}
					if ($inBlock) {
						$blocks[$pid][$blockId]['count'][$event['conflict_event_id']] = 1;
						if ($blocks[$pid][$blockId]['start'] > $event['conflict_ts']) {
							$blocks[$pid][$blockId]['start'] = $event['conflict_ts'];
							$blocks[$pid][$blockId]['sort'] = $event['schedule_sort'];
						}
						if ($blocks[$pid][$blockId]['end'] < $event['end_ts']) {
							$blocks[$pid][$blockId]['end'] = $event['end_ts'];
							$blocks[$pid][$blockId]['sort'] = $event['schedule_sort'];
						}
					}
					else {
						$blocks[$pid][] = array('start'=>$event['conflict_ts'],'end'=>$event['end_ts']);
					}
				}
			}
		}
		foreach($blocks as $pid => $col) {
			foreach($col as $blockId => $block) {
				$blocks[$pid][$blockId]['count'] = array_sum($block['count']);
			}
		}
		$conflictData = $conflicts;
		// just a list of which columns we have
		$columns = array();
		$unsets = array();
		// build columns using the rest of the items
		foreach($conflicts as $pid => $conflict) {
			$columns[$pid] = array();

			foreach($conflict as $parent => $events) {
				$current = 0;
				while(isset($conflicts[$pid][$parent])) {
					if (!isset($columns[$pid][$current])) {
						$columns[$pid][$current] = array();
					}

					$add = true;
					foreach($events as $id => $row) {
						if(isset($columns[$pid][$current][$id])) {
							$add = false;
							$current++;
							break;
						}
					}
					if ($add) {
						if($row['schedule_sort'] == 2) {
							$c = $columns[$pid];
							$columns[$pid]= array();
							$columns[$pid][] = array($parent=>array('column'=>0,'start_ts'=>$starts[$pid][$parent]));
							foreach($c as $key=>$cb) {
								if(!empty($cb)) {
									$columns[$pid][] = $cb;
								}
							}
						} else {
							$columns[$pid][$current][$parent] = array( 'column' => $current, 'start_ts' => $starts[$pid][$parent]);
						}
						$conflictData[$pid][$parent] = true;
						unset($conflicts[$pid][$parent]);
					}
				}
			}
		}
		$conflicts = $conflictData;
		//$columns[$pid][$colId] = $colId;
		foreach($columns as $pid => $column) {
			foreach($column[0] as $id => $data) {
				unset($conflicts[$pid][$id]);
			}
			unset($columns[$pid][0]);
		}

		return array($conflicts,$columns,$blocks);
	}
	
	/**
	 * Return the super-array
	 * @see CalendarData->getColumns()
	 *
	 */
	function &getColumns(&$filters,$renderType,&$dayIterator) {
		if(is_null($this->events)) {
			$a = $this->providerEvents($filters,$renderType,$dayIterator->interval);
			$this->events =& $a;
		} else {
			$a =& $this->events;
		}
		$columns = $dayIterator->parent->getScheduleList();
		list($conflicts,$conflictColumns,$conflictBlocks) = $this->getConflictingEvents($filters);

		
		$eventmap = $dayIterator->parent->eventScheduleMap;

		// Let's build this thing!
		$view =& new clniView();

		$count = 0;
		foreach($columns as $provider_id => $col) {
			$columns[$provider_id]['eventmap'] =& $eventmap[$provider_id];
			$columns[$provider_id]['index'] = $count++;
			if(isset($this->events[$provider_id])) {
				foreach($this->events[$provider_id] as $ts => $events) {
					$columns[$provider_id][$ts] =& $this->events[$provider_id][$ts];
				}
			}
			if (isset($conflicts[$provider_id])) {
				$columns[$provider_id]['conflicts'] = $conflicts[$provider_id];
			}
			$columns[$provider_id]['conflictCount'] = 0;
			if (isset($conflictColumns[$provider_id])) {
				$columns[$provider_id]['conflictColumns'] = $conflictColumns[$provider_id];
				$columns[$provider_id]['conflictCount'] = count($conflictColumns[$provider_id]);
			}
			if (isset($conflictBlocks[$provider_id])) {
				$columns[$provider_id]['conflictBlocks'] = $conflictBlocks[$provider_id];
			}
			// now create the pre-columns (the appointment-dragger)
			$view->assign_by_ref('dayIterator',$dayIterator);
			
			for($dayIterator->rewind(); $dayIterator->valid(); $dayIterator->next()) {
				$ts =$dayIterator->getTimestamp();
				$view->assign('timestamp',$ts);
				$view->assign('schedules',$columns[$provider_id]['schedules']);
				if(!isset($columns[$provider_id]['precol'])) {
					$columns[$provider_id]['precol'] = array();
				}
				$display = '';
				$schedid = $this->_timeInSchedule($ts,$col['schedules']);
				if ($schedid > 0) {
					$columns[$provider_id]['inSchedule'][$ts] = true;
					$view->assign('color',$columns[$provider_id]['color']);
					$display = $col['schedules'][$schedid]['display'];
				}

				$view->assign('id','st-'.$dayIterator->getTimestamp().'-'.$provider_id);
				$dayIterator->next();
				$nextTime = $dayIterator->getTime();
				$dayIterator->previous();
				$view->assign('title',$dayIterator->getTime().' - '.$nextTime.' '.$display);
				$columns[$provider_id]['precol'][$ts] = $view->fetch('calendar/general_precolumn.html');
			}
		}
		return $columns;
		
	}

	function _timeInSchedule($timestamp,$schedules) {
		if (count($schedules) > 0) {
			foreach($schedules as $key=>$schedule) {
				if ($timestamp >= $schedule['start'] && $timestamp <= $schedule['end']) {
					return $key;
				}
			}
		}
		return false;
	}
	
	
	/**
	 * Returns arrays of schedule-events which overlap
	 *
	 * @param unknown_type $filters
	 */
	function providerMultiSchedule(&$filters) {
		
	}
	
	function providerMultiEvent(&$filters) {
		
	}
	
	/**
	 * How many columns should the header span?
	 *
	 * @return int
	 */
	function getHeaderColspan() {
		return count($this->scheduleList)*2+1;
	}
	
	/**
	 * Returns sidebar html
	 * @todo This should probably be 
	 *
	 */
	function &getSidebar() {
		$GLOBALS['loader']->requireOnce('controllers/C_Appointment.class.php');
		$appt =& new C_Appointment();
		$appt->uiMode = 'popup';
		$sidebar = $appt->actionEdit();
		return $sidebar;
	}


	function _accountBalanceSql($patients) {
		$patients = implode(',',$patients);
		return
		'
		select
			feeData.patient_id,
                        charge,
                        (ifnull(credit,0.00) + ifnull(coPay.amount,0.00)) credit,
			(charge - (ifnull(credit,0.00)+ifnull(coPay.amount,0.00))) balance
		from
			/* Fee total */
			(
			select
				e.patient_id,
				sum(cd.fee) charge
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			group by
				e.patient_id
			) feeData
			left join
			/* Payment totals */
			(
			select
				e.patient_id,
				(sum(pl.paid) + sum(pl.writeoff)) credit
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join payment p on cc.claim_id = p.foreign_id
				inner join payment_claimline pl on p.payment_id = pl.payment_id
			group by
				e.patient_id
			) paymentData on feeData.patient_id = paymentData.patient_id
                        left join
                        /* Co-Pay Totals */
                        (
                        select
                            p.foreign_id patient_id,
                            sum(p.amount) amount
                        from
                            payment p
                        where encounter_id = 0
                        group by
                            p.foreign_id
                        ) coPay on feeData.patient_id = coPay.patient_id
		where
			feeData.patient_id in('.$patients.')';
	}
	
}
?>