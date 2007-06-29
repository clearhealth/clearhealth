<?php
$loader->requireOnce('lib/PEAR/Image/Color.php');
class ClearhealthCalendarData {
	var $filters;
	var $schedules = null;
	var $events = null;
	var $interval = 900;
	var $showEventsOn = array('day'=>true,'week'=>true,'month'=>false);
	var $cache_identifier = null;

	var $currentPractice;

	function ClearhealthCalendarData() {
		$userProfile =& Celini::getCurrentUserProfile();
		$this->cache_identifier = $userProfile->getCurrentPracticeId();
		$this->currentPractice = $userProfile->getCurrentPracticeId();

		$GLOBALS['loader']->requireOnce('includes/PracticeConfig.class.php');
		$pconfig =& Celini::configInstance('practice');
		$increment = $pconfig->get('CalendarIncrement',900);
		$this->filters['user'] = array('type' => 'Multiselect', 'label' => 'Provider', 'params' => array('type' => 'form','insertBlank'=>true,'size'=>5));
		$this->filters['building'] = array('type' => 'Multiselect','label'=>'Building','params'=>array('type'=>'form','insertBlank'=>true,'size'=>5));
		$this->filters['start'] = array('type' => 'DateTime', 'label' => 'Start Date', 'params' => array('hidden'=>true));
		$this->filters['starttime'] = array('type' => 'Time', 'label' => 'Start Time', 'params' => array('increment'=>$increment/60));
		$this->filters['end'] = array('type' => 'DateTime', 'label' => 'End Date', 'params' => array('hidden'=>true));
		$this->filters['endtime'] = array('type' => 'Time', 'label' => 'End Time', 'params' => array('increment'=>$increment/60));
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
	 * @todo: move somewhere else
	 *
	 */
	function extraDisplay(&$filters) {
		$view =& new clniView();
		$db =& new clniDb();
		$values = array();
		$html  = '';
		foreach($filters as $filter){
			if(isset($filter->params['hidden']) && $filter->params['hidden'] == true) continue;
			$html.= $filter->getHTML($this->getFilterOptions($filter->getName()));
		}
		$view->assign('filter_html',$html);
		foreach($this->filters as $key => $filter) {
			$value = $filters[$key]->getValue();
			if (is_null($value)) {
				continue;
			}
			switch($key) {
				case 'starttime':
				case 'endtime':
					$values[$filter['label']] = "$value[hour]:$value[minute]:$value[second] $value[ap]";
					break;
				case 'user':
					if (count($value) > 0) {
						$sql = "select username from user where person_id in (".implode(',',$value).")";
						$values[$filter['label']] = implode(', ',(array)$db->getCol($sql));
					}
					break;
				case 'patient':
					if ($value['value'] != '') {
						$values[$filter['label']] = $value['value'];
					}
					break;
				case 'building':
					if (count($value) > 0) {
						$sql = "select name from buildings where id in (".implode(',',$value).") order by name";
						$values[$filter['label']] = implode(', ',(array)$db->getCol($sql));
					}
					break;
				default:
					$values[$filter['label']] = $value;
			}
		}
		$view->assign('filters',$values);

		$profile =& Celini::getCurrentUserProfile();
		$practice =& Celini::newOrdo('Practice',$profile->getCurrentPracticeId());
		$view->assign_by_ref('practice',$practice);

		$config =& Celini::configInstance('practice');
		$view->assign_by_ref('config',$config);

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
				$options = $user->valueList('fullName');
				return $options;

			case 'building':
				$userProfile =& Celini::getCurrentUserProfile();
				$options = $userProfile->getBuildingNameList(true);
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
			$time= $filters['starttime']->getValue();
			$time = date('H:i:s',mktime($time['ap']=='AM' ? ($time['hour']=='12' ? '00' : $time['hour']) : $time['hour']+12,$time['minute'],$time['second']));

			if($forevent == false) {
				$criteriaArray[] = "event.start >= ".$db->quote($filters['start']->getValue().' '.$time);
			} else {
				$criteriaArray[] = "aevent.start >= ".$db->quote($filters['start']->getValue().' '.$time);
			}
		}
		
		if(isset($filters['end']) && !is_null($filters['end']->getValue())) {
			$time= $filters['endtime']->getValue();
			$time = date('H:i:s',mktime($time['ap']=='AM' ? ($time['hour']=='12' ? '00' : $time['hour']) : $time['hour']+12,$time['minute'],$time['second']));
			if($forevent == false) {
				$criteriaArray[] = "event.start <= ".$db->quote($filters['end']->getValue().' '.$time);
			} else {
				$criteriaArray[] = "aevent.start <= ".$db->quote($filters['end']->getValue().' '.$time);
			}
		}
		if(isset($filters['user']) && count($filters['user']->getValue()) > 0) {
			$string = array();
			foreach($filters['user']->getValue() as $uid) {
				$string[] = "u.person_id = ".$db->quote($uid);
			}
			$criteriaArray[] = '('.implode(' OR ',$string).')';
		}
		
		// Add building criteria
		$string = array();
		$profile =& Celini::getCurrentUserProfile();
		if(count($filters['building']->getValue()) == 0 && !is_null($profile->getDefaultLocationId())) {
			// We've just entered the calendar, so set the default building
			$room =& Celini::newORDO('Room',$profile->getDefaultLocationId());
			$filters['building']->setValue(array($room->get('building_id')));
		} elseif(count($filters['building']->getValue()) == 0) {
			// Probably a superadmin.  Just pop them on the first building available.
			$r =& Celini::newORDO('Room');
			$rooms = $r->valuelist('current');
			if(count($rooms) > 0) {
				$keys = array_keys($rooms);
				$r =& Celini::newORDO('Room',$keys[0]);
				$filters['building']->setValue(array($r->get('building_id')));
			}
		}
		if (count($filters['building']->getValue()) > 0) {
			foreach($filters['building']->getValue() as $uid) {
				$string[] = "b.id = ".$db->quote($uid);
			}
			$criteriaArray[] = '('.implode(' OR ',$string).')';
		}

		// hide canceled appointments
		$config = Celini::configInstance();
		if ($config->get('hideCanceledAppointment',false)) {
			if ($forevent) {
				$criteriaArray[] = "a.appointment_code != 'CAN'";
			}
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
	 * @todo: document where used or remove
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
		$finder->addJoin('INNER JOIN appointment ON(event.event_id=appointment.event_id)');
		$finder->addGroupBy('event.event_id');
		$finder->addOrderBy('event.start ASC');
		return $finder;
	}
	
	function _setFinderRelationships(&$finder,$filters) {
		if(isset($filters['user']) && count($filters['user']->getValue()) > 0) {
			$finder->addJoin("LEFT JOIN relationship relprov ON(relprov.child_type='CalendarEvent' AND relprov.child_id=event.event_id AND relprov.parent_type='Provider')");
			$where = '(appointment.provider_id IN ('.implode(',',$filters['user']->getValue()).') OR relprov.parent_id IN ('.implode(',',$filters['user']->getValue()).'))';
			$finder->addCriteria($where);
		}
		if(isset($filters['patient'])) {
			$pid = $filters['patient']->getValue();
			if(is_array($pid)) {
				$pid = $pid['value'];
			}
			if($pid > 0 || !empty($pid)) {
				$patient =& Celini::newORDO('Patient',$filters['patient']->getValue());
				$finder->addChild($patient);
			}
		}
	}
	
	function _setFinderCriteria(&$finder,$filters) {
		$criteriaArray = array();
		$db =& Celini::dbInstance();
		if(isset($filters['start']) && !is_null($filters['start']->getValue())) {
			$finder->addCriteria("event.start >= ".$db->quote(date('Y-m-d H:i:s',strtotime($filters['start']->getValue()))));
		}
		if(isset($filters['end']) && !is_null($filters['end']->getValue())) {
			$finder->addCriteria("event.start <= ".$db->quote(date('Y-m-d H:i:s',strtotime($filters['end']->getValue()))));
		}
	}


	function &getSchedules(&$filters) {
		if(is_null($this->schedules)) {
			$this->schedules =& $this->providerSchedules($filters);
		}
		return $this->schedules;
	}

	/**
	 *  Basic information about providers
	 *
	 *  @see getScheduleList
	 */
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
	 *  Basic information about rooms
	 */
	function roomData() {
		$db = new clniDb();
		$sql = "SELECT
				r.id,
				color,
				r.name nickname,
				concat(b.name,'->',r.name) AS name
			from
				rooms r
				inner join buildings b on r.building_id = b.id
			";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['id']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Provide an array of schedules and the html to use as their headings
	 */
	function &getScheduleList(&$filters) {
		$config = Celini::configInstance();
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
		$rdata = $this->roomData();

		$ret = array();
		foreach($s as $providerId => $schedules) {
			if (count($schedules) == 0) {
				if(!in_array($providerId,$map)) {
					continue;
				}
			}
			$data = array('id'=>$providerId,'color'=>'','nickname'=>'','name'=>'');
			if (isset($pdata[$providerId])) {
				$data = $pdata[$providerId];
				$isRoom = 0;
			}
			else {
				if (isset($rdata[$providerId])) {
					$data = $rdata[$providerId];
				}
				$isRoom = 1;
			}
			$color = empty($data['color']) ? 'F0F0F0' : $data['color'];

			$ic = new Image_Color();
			$ic->setColors($color,$color);

			$ic->changeLightness(-20);
			$border = $ic->_returnColor($ic->color1);
			$ic->changeLightness(40);
			$background = $ic->_returnColor($ic->color1);
			$font = $ic->getTextColor($color);
			$labelextra = '';
			if(!$isRoom && $config->get('showCalendarWeekViewLinks',true)) {
				$labelextra = "<a align='right' href='".Celini::link('week').urlencode("Filter[user][]")."=$providerId'><img height=15 width=15 src='".Celini::link('week_on.gif','images',false)."'></a>";
			}
			$ret[$providerId] = array(
				'color' => $color,
				'borderColor' => $border,
				'backColor' => $background,
				'fontColor' => $font,
				'label' => $data['name'],
				'isRoom'    => $isRoom,
				'schedules' => $schedules,
				'labelextra' => $labelextra
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
		if (!empty($where)) {
			$where = ' and '.$where;
		}

		$profile =& Celini::getCurrentUserProfile();
		$practice_id = EnforceType::int($profile->getCurrentPracticeId());

		$sql = "SELECT 
				event.event_id, 
				provider.person_id as provider_id 
			FROM 
				`event` AS event
				inner join appointment a on a.event_id = event.event_id
				left join provider on a.provider_id = provider.person_id
				left join person on a.provider_id = person.person_id
				LEFT JOIN user AS u ON u.person_id= provider.person_id
				LEFT JOIN rooms r ON r.id=a.room_id
				LEFT JOIN buildings b ON r.building_id=b.id
			WHERE 
				(ifnull(b.practice_id,person.primary_practice_id) = $practice_id)
				$where
			GROUP BY event.event_id
				";
		return $db->getAssoc($sql);
	}

	/**
	 *  Get the previous appointments for a provider and a given time
	 *
	 *  @return array
	 */
	function prevAppointments($providerId,$start,$end) {
		$db = new clniDb();
		$s = $db->quote(date('Y-m-d H:i:s',strtotime($start.' -15min')));
		$e = $db->quote(date('Y-m-d H:i:s',strtotime($end)));
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

	/**
	 *  Get the appoinments in a given timeblock
	 */
	function appointmentsOverlapping($start,$end) {

		$format = TimeObject::getFormat();
		$db = new clniDb();
		$s = $db->quote(date('Y-m-d H:i:s',strtotime($start)));
		$e = $db->quote(date('Y-m-d H:i:s',strtotime($end)));
		$config = Celini::configInstance();
		$hideCan = '';
                if ($config->get('hideCanceledAppointment',false)) {
                	$hideCan = " and a.appointment_code != 'CAN' ";
                }


		$sql = "SELECT
				event.*,
				a.*,
				date_format(start,'$format') startTime,
				date_format(end,'$format') endTime,
				r.name room,
				r.number_seats roomMax,
				concat(p.last_name,', ',p.first_name,' #',pat.record_number) patientName,
				concat(pr.last_name,', ',pr.first_name,' (',pu.username,')') providerName
				,GROUP_CONCAT(ab.person_id ORDER BY ab.appointment_breakdown_id) breakdown_providers
				,GROUP_CONCAT(CONCAT(bkdnpr.last_name,',',bkdnpr.first_name,' (',bkdnu.username,')')) breakdown_providernames
			FROM
				event
				inner join appointment a on a.event_id = event.event_id
				left join rooms r on a.room_id = r.id
				left join person p on a.patient_id = p.person_id
				left join patient pat on a.patient_id = pat.person_id
				left join person pr on a.provider_id = pr.person_id
				left join user pu on pu.person_id = pr.person_id
				left join appointment_breakdown ab ON(ab.appointment_id=a.appointment_id)
				left join person bkdnpr ON(ab.person_id=bkdnpr.person_id)
				LEFT JOIN user bkdnu ON(bkdnpr.person_id=bkdnu.person_id)
			WHERE
				((event.end > $s and event.end <= $e) or
				(event.start >= $s and event.start < $e))
				$hideCan
			GROUP BY
				a.appointment_id
			";
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
		return $this->_schedules($where,$date);
	}
	
	/**
	 * Returns array[provider_id][start] = array('label','start','end')
	 *
	 * @todo rename this has been hacked so it works with room and provider scehdules
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
		$date = $filters['start']->getValue();
		
		$return = $this->_schedules($where,$date);
		return $return;
	}

	function &_schedules($where,$date) {
		$cache_id = $date.'-'.md5($where.$this->currentPractice);
		$view = new clniView();
		$view->caching = true;
		$view->cache_lifetime = 3600;
		if($view->is_cached('calendar/cache_providerschedules.html',$cache_id)) {
			$out = unserialize($view->fetch('calendar/cache_providerschedules.html',$cache_id));
			return $out;
		}
		$db = new clniDb();
		$sql = "
			SELECT p.person_id 
			FROM 
				provider
				INNER JOIN person p USING(person_id)
			ORDER BY p.last_name ASC, p.first_name ASC";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['person_id']] = array();
			$res->MoveNext();
		}
		$sql = "
			SELECT r.id 
			FROM 
				rooms r
				INNER JOIN buildings b ON(b.id = r.building_id)
			ORDER BY b.name ASC, r.name ASC";
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			$ret[$res->fields['id']] = array();
			$res->MoveNext();
		}

		$profile =& Celini::getCurrentUserProfile();
		$practice_id = EnforceType::int($profile->getCurrentPracticeId());
		
		$sql = "
			SELECT 
				event.event_id,
				s.title,
				eg.title group_title,
				UNIX_TIMESTAMP(event.start) AS start,
				UNIX_TIMESTAMP(event.end) AS end, 
				if(s.provider_id=0,r.id,s.provider_id) provider_id, /* this is a hack for room schedules */
				u.nickname,
				r.name AS roomname,
				r.id room_id,
				se.event_id as schedule_event_id
			FROM 
				`event` AS event
				INNER JOIN schedule_event se ON se.event_id=event.event_id
				INNER JOIN event_group eg ON eg.event_group_id=se.event_group_id
				INNER JOIN schedule s ON eg.schedule_id=s.schedule_id
				LEFT JOIN rooms r ON r.id=eg.room_id
				LEFT JOIN buildings b ON r.building_id=b.id
				LEFT JOIN user u ON s.provider_id = u.person_id
				LEFT JOIN person provider on s.provider_id = provider.person_id
			WHERE 
				(ifnull(b.practice_id,provider.primary_practice_id) = $practice_id) $where
				AND (s.schedule_code != 'ADM')
			ORDER BY 
			 	event.start";
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			if(!isset($ret[$res->fields['provider_id']])) {
				$ret[$res->fields['provider_id']] = array();
			}
			$break = ($res->fields['end'] - $res->fields['start']) <= 900 ? '&nbsp;' : '<br />';
			$display = !empty($res->fields['group_title']) ? $res->fields['group_title'].$break.$res->fields['nickname'] : $res->fields['nickname'];
			$ret[$res->fields['provider_id']][$res->fields['start']] = array(
				'label'=>$res->fields['title'],
				'start'=>$res->fields['start'],
				'end'=>$res->fields['end'],
				'display'=>$display,
				'schedule_event_id'=>$res->fields['schedule_event_id'],
				'room_id'=>$res->fields['room_id']
			);
			$res->MoveNext();
		}
		$view->assign('scheduledata',serialize($ret));
		$view->cache_lifetime = 3600;
		$view->caching = false;
		$x = $view->fetch('calendar/cache_providerschedules.html',$cache_id);
		$view->caching = false;
		return $ret;
	}

	function roomsWithSchedules($where) {
		$db = new clniDb();

		$ret = array();
		$profile =& Celini::getCurrentUserProfile();
		$practice_id = EnforceType::int($profile->getCurrentPracticeId());
		
		$sql = "
			SELECT 
				r.id room_id,
				r.id id
			FROM 
				`event` AS aevent
				INNER JOIN schedule_event se ON se.event_id=aevent.event_id
				INNER JOIN event_group eg ON eg.event_group_id=se.event_group_id
				INNER JOIN schedule s ON eg.schedule_id=s.schedule_id
				
				INNER JOIN rooms r ON r.id=eg.room_id
				INNER JOIN buildings b ON r.building_id=b.id
			WHERE 
				s.provider_id = 0 and
				b.practice_id = $practice_id $where
			 ";
		return $db->getAssoc($sql);
	}

	/**
	 * Returns array of provider and room Appointments
	 * array[provider_id]['events'][start_ts][event_id] = array('html','start','end')
	 * array[room_id]['isroom']=0
	 * array[room_id]['events'][start_ts][event_id] = array('html','start','end')
	 * array[room_id]['isroom']=1
	 *
	 * @param array $filters
	 * @param string $renderType Specifies how the appointment will be rendered.  'day' will have edit links
	 * @param int $period Number of seconds in an iteration
	 * @return array
	 */
	function providerEvents(&$filters,$renderType = 0,$period = 900) {
		$db = new clniDb();
		$e =& Celini::newORDO('CalendarEvent');
		$where = $this->toWhere($filters);
		if(!empty($where)) {
			$where = ' AND ('.$where.')';
		}
		$profile =& Celini::getCurrentUserProfile();
		$practice_id = EnforceType::int($profile->getCurrentPracticeId());
 		
		$view = new clniView();
 		$view->caching = true;
 		$view->cache_lifetime = 3600; // One hour
		$cache_id = $filters['start']->getValue().'-'.md5($where.'_'.$practice_id);
		if($view->is_cached('calendar/cache_providerevents.html',$cache_id)) {
			$entries = $view->fetch('calendar/cache_providerevents.html',$cache_id);
			$entries = unserialize($entries);
	 		$view->caching = false;
			return $entries;
		}
 		$view->caching = false;
		

		$sql = "
			SELECT 
				a.appointment_id,
				aevent.event_id,
				IF(schedule_code = 'PS',1,IF(s.schedule_id IS NULL AND a.patient_id = 0,2,0)) AS schedule_sort,
				provider.person_id provider_id,
				UNIX_TIMESTAMP(aevent.start) AS start_ts,
				UNIX_TIMESTAMP(aevent.end) AS end_ts,
				rm.id room_id
			FROM 
				`event` AS aevent 
				INNER JOIN appointment AS a ON aevent.event_id = a.event_id
				LEFT JOIN event_group eg ON eg.event_group_id=a.event_group_id
				LEFT JOIN schedule s ON eg.schedule_id=s.schedule_id
				LEFT JOIN rooms AS rm ON rm.id = a.room_id
				LEFT JOIN buildings AS b ON b.id = rm.building_id 
				LEFT JOIN provider ON a.provider_id = provider.person_id
				LEFT JOIN person p ON a.provider_id = p.person_id
				LEFT JOIN user AS u ON u.person_id= provider.person_id
				LEFT JOIN patient ON a.patient_id=patient.person_id
			WHERE 
				(s.schedule_code != 'NS' OR s.schedule_code IS NULL OR s.schedule_code = '') 
				AND (ifnull(b.practice_id,p.primary_practice_id) = $practice_id)
				$where
			GROUP BY aevent.event_id 
			ORDER BY
				schedule_sort DESC,
				aevent.start,
				aevent.end
				";

		// If there's no schedule and no patient, it's an ADMIN event
		

		// get a list of rooms with schedules
		$rooms = $this->roomsWithSchedules($where);

		$res = $db->execute($sql);
		$ret = array();
		// Create the html output here so we don't have to haul around this huge array.
		$eventrender =& new CalendarEventRender();

		while($res && !$res->EOF) {
			$providerId = $res->fields['provider_id'];
			$startTs = $res->fields['start_ts'];
			$roomId = $res->fields['room_id'];
			$eventId = $res->fields['event_id'];

			if(!isset($ret[$providerId])) {
				$ret[$providerId] = array();
				$ret[$providerId]['isRoom'] = 0;
				$ret[$providerId]['events'] = array();
			}
			if(!isset($ret[$providerId]['events'][$startTs])) {
				$ret[$providerId]['events'][$startTs] = array();
			}

			$ret[$providerId]['events'][$startTs][$eventId] = array();

			$html = $eventrender->render($res->fields,$renderType);
			$ret[$providerId]['events'][$startTs][$eventId]['html'] = $html;
			$ret[$providerId]['events'][$startTs][$eventId]['start'] = $res->fields['start_ts'];
			$ret[$providerId]['events'][$startTs][$eventId]['end'] = $res->fields['end_ts'];

			// add room based columns if there is a room schedule
			if (isset($rooms[$roomId])) {
				if(!isset($ret[$roomId])) {
					$ret[$roomId] = array();
					$ret[$roomId]['isRoom'] = 1;
					$ret[$roomId]['events'] = array();
				}
				if (!isset($ret[$roomId]['events'][$startTs])) {
					$ret[$roomId]['events'][$startTs] = array();
				}
				$ret[$roomId]['events'][$startTs][$eventId]['html'] = $html;
				$ret[$roomId]['events'][$startTs][$eventId]['start'] = $res->fields['start_ts'];
				$ret[$roomId]['events'][$startTs][$eventId]['end'] = $res->fields['end_ts'];
			}
			
			$res->MoveNext();
		}
		$view->assign('providerEvents',serialize($ret));
 		$view->caching = true;
		$x = $view->fetch('calendar/cache_providerevents.html',$cache_id);		
		$view->caching = false;
		return $ret;
	}

	function _conflictCheck($events,$event) {
		$conflicts = array();
		//var_dump('EventId: '.$event['event_id'].' Start: '.date('H:i',$event['start_ts']).'End: '.date('H:i',$event['eventend_ts']));
		foreach($events as $check) {
			//var_dump('Start: '.date('H:i',$check['start_ts']).'End: '.date('H:i',$check['eventend_ts']));
			if (
				$event['start_ts'] > $check['start_ts'] && $event['start_ts'] < $check['eventend_ts'] ||
				$event['eventend_ts'] > $check['start_ts'] && $event['eventend_ts'] < $check['eventend_ts'] ||

				$check['start_ts'] > $event['start_ts'] && $check['start_ts'] < $event['eventend_ts'] ||
				$check['eventend_ts'] > $event['start_ts'] && $check['eventend_ts'] < $event['eventend_ts'] 

				|| ($check['start_ts'] == $event['start_ts'] && $check['event_id'] != $event['event_id']) 
				|| ($check['eventend_ts'] == $event['eventend_ts'] && $check['event_id'] != $event['event_id'])
			) {
				$conflicts[] = $check['event_id'];
			}
		}
		//var_dump($conflicts);
		return $conflicts;
	}

	function _confSorter($a,$b) {
		if ($a['created_date'] > $b['created_date']) {
			return 1;
		}
		else if ($a['created_date'] < $b['created_date']) {
			return -1;
		}
		//var_dump('got here');
		return 0;
	}
	
	/**
	 * Return array of conflicting event ids and the event they conflict
	 * (start after current-event-start but before current-event-end)
	 *
	 * @param array $events
	 * array[provider_id]['events'][start_ts][event_id] = array('html','start','end')
	 * array[room_id]['isroom']=0
	 * array[room_id]['events'][start_ts][event_id] = array('html','start','end')
	 * array[room_id]['isroom']=1
	 * @param array $eventids
	 */
	function getConflictingEvents($events) {
		$profile =& Celini::getCurrentUserProfile();
		$practice_id = EnforceType::int($profile->getCurrentPracticeId());
		
		$eventids = array(0);
		foreach($events as $array) {
			foreach($array['events'] as $start) {
				foreach($start as $eid=>$eventsb) {
					$eventids[] = $eid;
				}
			}
		}
		$eventids = implode(',',$eventids);
		$db = new clniDb();
		$sql = "SELECT 
				IF(schedule_code = 'PS',1,0) AS schedule_sort,
				UNIX_TIMESTAMP(event.start) start_ts,
				UNIX_TIMESTAMP(event.end) eventend_ts,
				event.event_id AS event_id, 
				ea.provider_id,
				ea.room_id,
				UNIX_TIMESTAMP(ea.created_date) created_date
			FROM 
				`event`
				INNER JOIN appointment ea on `event`.event_id = ea.event_id
				LEFT JOIN event_group eg ON eg.event_group_id=ea.event_group_id
				LEFT JOIN schedule s ON eg.schedule_id=s.schedule_id
			WHERE 
			event.event_id IN($eventids)
			 ORDER BY 
			 	ea.created_date, event.start, event.event_id";
//echo $sql;

		$res = $db->execute($sql);
		$conflicts = array();
		$starts = array();

		$roomAppts = array();
		$providerAppts = array();

		while($res && !$res->EOF) {
			$start = $res->fields['start_ts'];
			$rid = $res->fields['room_id'];
			$pid = $res->fields['provider_id'];//  > 0 ? $res->fields['provider_id'] : $res->fields['room_id'];
			$eid = $res->fields['event_id'];

			$starts[$rid][$eid] = $res->fields['start_ts'];
			$starts[$pid][$eid] = $res->fields['start_ts'];

			if ($rid > 0) {
				$roomAppts[$rid][$eid] = $res->fields;
			}

			if ($pid > 0) {
				$providerAppts[$pid][$eid] = $res->fields;
			}

			$res->MoveNext();
		}

		foreach($providerAppts as $pid => $events) {
			foreach($events as $eid => $event) {
				$confs = $this->_conflictCheck($events,$event);
				if (count($confs) > 0) {
					foreach($confs as $cid) {
						$conflicts[$pid][$eid][$cid] = $events[$cid];

						$conflicts[$pid][$eid][$cid]['conflict_event_id'] = $eid;
						$conflicts[$pid][$eid][$cid]['conflict_ts'] = $event['start_ts'];
						$conflicts[$pid][$eid][$cid]['end_ts'] = $event['eventend_ts'];
					}
				}
			}
		}

		foreach($roomAppts as $rid => $events) {
			foreach($events as $eid => $event) {
				$confs = $this->_conflictCheck($events,$event);
				if (count($confs) > 0) {
					foreach($confs as $cid) {
						$conflicts[$rid][$eid][$cid] = $events[$cid];

						$conflicts[$rid][$eid][$cid]['conflict_event_id'] = $eid;
						$conflicts[$rid][$eid][$cid]['conflict_ts'] = $event['start_ts'];
						$conflicts[$rid][$eid][$cid]['end_ts'] = $event['eventend_ts'];
					}
				}
			}
		}

		// sort conflicts
		foreach($conflicts as $id => $confs) {
			foreach($confs as $tid => $tmp) {
				uasort($conflicts[$id][$tid],array($this,'_confSorter'));
			}
		}
		
		// calc start/end times for overlap blocks
		
		// This code for provider AND room schedule conflicts
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
							// event start is inside current block
							if ($event['start_ts'] >= $block['start'] && $event['start_ts'] <= $block['end']) {
								$inBlock = true;
								break;
							}
							// event end is inside current block
							if ($event['eventend_ts'] >= $block['start'] && $event['eventend_ts'] <= $block['end']) {
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
			foreach($blocks[$pid] as $blockId => $block) {
				if (isset($block['count'])) {
					$blocks[$pid][$blockId]['count'] = array_sum($block['count']);
				}
				else {
					unset($blocks[$pid][$blockId]);
					continue;
				}
				// Check for duplicate blocks
				foreach($blocks[$pid] as $bblockId=>$bblock) {
					if($bblockId > $blockId && $block['start'] == $bblock['start'] && $block['end'] == $bblock['end']) {
						if(isset($bblock['count']) && is_array($bblock['count'])) {
							$blocks[$pid][$blockId]['count'] += array_sum($bblock['count']);
						} elseif(isset($bblock['count'])) {
							$blocks[$pid][$blockId] += $bblock['count'];
						}
						unset($blocks[$pid][$bblockId]);
					}
				}
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
		$view =& new clniView();
		$view->caching = true;
		// Cache for 15 minutes
		$view->cache_lifetime = 900;
		$cache_id = $filters['start']->getValue().'-'.md5($this->toWhere($filters).$this->currentPractice);
		$this->cache_identifier = $cache_id;

		if($view->is_cached('calendar/cache_column.html',$cache_id)) {
			$columns = $view->fetch('calendar/cache_column.html',$cache_id);
			$columns = unserialize($columns);
			return $columns;
		}

		if(is_null($this->events)) {
			$a = $this->providerEvents($filters,$renderType,$dayIterator->interval);
			$this->events =& $a;
		} else {
			$a =& $this->events;
		}

		$columns = $dayIterator->parent->getScheduleList();
		list($conflicts,$conflictColumns,$conflictBlocks) = $this->getConflictingEvents($a);
		$eventmap = $dayIterator->parent->eventScheduleMap;

		// Let's build this thing!

		$count = 0;
		// Provider_id may also be room_id for this loop
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
				$columns[$provider_id]['conflictColumnCount'] = count($conflictColumns[$provider_id]);
			}
			if (isset($conflictBlocks[$provider_id])) {
				$columns[$provider_id]['conflictBlocks'] = $conflictBlocks[$provider_id];
			}
			// now create the pre-columns (the appointment-dragger)
			$view->assign_by_ref('dayIterator',$dayIterator);
			$room_id = 0;
			$view->caching = false;
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

				if (isset($columns[$provider_id]['schedules'][$ts]['room_id'])) {
					$room_id = $columns[$provider_id]['schedules'][$ts]['room_id'];
				}

				$view->assign('id','st-'.$dayIterator->getTimestamp().'-'.$provider_id.'-'.$room_id);
				$dayIterator->next();
				$nextTime = $dayIterator->getTime();
				$dayIterator->previous();
				$view->assign('title',$dayIterator->getTime().' '.$display);
				$columns[$provider_id]['precol'][$ts] = $view->fetch('calendar/general_precolumn.html');
			}
		}
		$view->caching = true;
		$view->cache_lifetime = 900;
		$view->assign('colinfo',serialize($columns));
		$x = $view->fetch('calendar/cache_column.html',$cache_id);
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
	 * Returns sidebar html
	 * @todo This should probably be moved to a renderer class
	 *
	 */
	function &getSidebar() {
		$GLOBALS['loader']->requireOnce('controllers/C_Appointment.class.php');
		$appt =& new C_Appointment();
		$appt->uiMode = 'popup';
		$sidebar = $appt->actionEdit();
		return $sidebar;
	}
}
?>
