<?php
/*****************************************************************************
*       ScheduleManagerController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * Schedule Manager controller
 */
class ScheduleManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$template = new ScheduleTemplate();
		$templateIterator = $template->getIterator();
		$this->view->templates = $templateIterator->toArray('scheduleTemplateId','name');
		$this->render('index');
	}

	public function listScheduleAction() {
		$rows = array();
		$schedDate = date('Y-m-d',strtotime($this->_getParam('schedDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		if ($providerId > 0 || $roomId > 0) {
			$schedule = new Schedule();
			$schedule->providerId = $providerId;
			$schedule->roomId = $roomId;
			if (!$schedule->populateByProviderRoomId()) {
				// create a new schedule
				$schedule->title = 'Schedule for ';
				if ($providerId > 0) {
					$name = ucwords(strtolower($schedule->provider->lastName . ', ' . $schedule->provider->firstName)) . ' ' . $schedule->provider->suffix;
					$schedule->title .= $name;
				}
				else {
					$name = $schedule->room->building->practice->name.'->'.$schedule->room->building->name.'->'.$schedule->room->name;
					$schedule->title .= $name;
				}
				$schedule->persist();
			}
			else { // lookup the schedule days
				$weekDates = ScheduleEvent::computeWeekDates($schedDate);
				$dateStart = $weekDates['start'].' 00:00:00';
				$dateEnd = $weekDates['end'].' 23:59:59';

				$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$dateStart,$dateEnd);
				$data = array();
				$weekdays = array();
				while ($event = $stmt->fetch()) {
					$row = new ScheduleEvent();
					$row->populateWithArray($event);
					$startToTime = strtotime($row->start);
					$weekday = date('N',$startToTime);
					$data[$weekday][] = array('id'=>$row->scheduleEventId,'date'=>date('Y-m-d',$startToTime),'time'=>date('h:iA',$startToTime).'-'.date('h:iA',strtotime($row->end)),'buildingId'=>$row->buildingId,'ORM'=>$row);
					$weekdays[$weekday] = $weekday;
				}

				$maxLen = 0;
				foreach ($weekdays as $weekday) {
					$len = count($data[$weekday]);
					if ($len > $maxLen) {
						$maxLen = $len;
					}
				}

				$indexMonday = 1;
				$indexTuesday = 2;
				$indexWednesday = 3;
				$indexThursday = 4;
				$indexFriday = 5;
				$indexSaturday = 6;
				$indexSunday = 7;
				$indices = array($indexSunday,$indexMonday,$indexTuesday,$indexWednesday,$indexThursday,$indexFriday,$indexSaturday); // this MUST be in order based on grid columns arrangement
				$buildings = array();
				for ($i = 0; $i < $maxLen; $i++) {
					$row = array();
					$row['id'] = ($i + 1);

					$ctr = 0;
					foreach ($indices as $index) {
						$arr = array('data'=>'','userdata'=>'','buildingId'=>'','title'=>'');
						if (isset($data[$index][$i])) {
							$arr['data'] = $data[$index][$i]['time'];
							$arr['userdata'] = $data[$index][$i]['id'];
							$arr['buildingId'] = $data[$index][$i]['buildingId'];
							$arr['title'] = $data[$index][$i]['ORM']->title;
						}
						$buildingId = $arr['buildingId'];
						if (!isset($buildings[$buildingId])) {
							$buildings[$buildingId] = '';
							if ($buildingId != '') {
								$building = new Building();
								$building->buildingId = (int)$buildingId;
								$building->populate();
								if (strlen($building->name) > 0) {
									$buildings[$buildingId] = $building->practice->name.'->'.$building->name;
								}
							}
						}
						$row['data'][$ctr] = $arr['data'].'<br>'.$arr['title'].'<br>'.$buildings[$buildingId];
						$row['userdata'][$ctr] = $arr['userdata'];
						$row['userdata']['b'.$ctr] = $buildingId;
						$row['userdata']['t'.$ctr] = $arr['data'];
						$row['userdata']['tt'.$ctr] = $arr['title'];
						$ctr++;
					}
					$rows[] = $row;
				}
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function autoCompleteAction() {
        	$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9]/','',$match);
		$strMatch = $match;
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$db = Zend_Registry::get('dbAdapter');
		$match = $db->quote($match.'%');
		$sqlSelect = $db->select()
				->from('user')
				->joinUsing('person','person_id')
				->where('person.last_name LIKE '.$match)
				->orWhere('person.first_name LIKE '.$match)
				->orWhere('user.username LIKE '.$match)
				->order('person.last_name DESC')
				->order('person.first_name DESC');
		$userResults = $db->fetchAll($sqlSelect);
		foreach ($userResults as $row) {
			$name = ucwords(strtolower($row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1))) . ' ' . $row['suffix'] . ' (' . $row['username'] . ')'; 
			$matches['pid'.$row['person_id']] = $name;
		}

		$resources = array();
		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building', 'Room'));
		foreach($facilityIterator as $facilities) {
			$resources[$facilities['Room']->roomId] = $facilities['Practice']->name.'->'.$facilities['Building']->name.'->'.$facilities['Room']->name;
		}
		foreach ($resources as $key=>$value) {
			if (preg_match('/(.*)'.$strMatch.'(.*)/i',$value)) {
				$matches['rid'.$key] = $value;
			}
		}
	
        	$this->_helper->autoCompleteDojo($matches);
	}

	public function processEditAction() {
		$scheduleEventId = (int)$this->_getParam('scheduleEventId');
		$weekday = (int)$this->_getParam('weekday');
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$params = $this->_getParam('schedules');

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$date = $weekDates['start'];
		$weekday--;
		if ($weekday > 0) {
			$date = date('Y-m-d',strtotime("+{$weekday} days",strtotime($date)));
		}
		$params['start'] = $date.' '.date('H:i',strtotime($params['start']));
		$params['end'] = $date.' '.date('H:i',strtotime($params['end']));

		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->scheduleEventId = $scheduleEventId;
		$ret = $scheduleEvent->populate();
		$scheduleEvent->populateWithArray($params);
		if (!$ret) {
			$schedule = new Schedule();
			$schedule->providerId = $scheduleEvent->providerId;
			$schedule->roomId = $scheduleEvent->roomId;
			$schedule->populateByProviderRoomId();

			$scheduleEvent->scheduleId = $schedule->scheduleId;
			$scheduleEvent->scheduleEventId = 0;
			//$scheduleEvent->title = preg_replace('/^schedule /i','Event ',$schedule->title);
			//$scheduleEvent->scheduleCode = '';

		}
		$scheduleEvent->persist();
		$data = (int)$scheduleEvent->scheduleEventId;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getContextMenuAction() {
		header('Content-Type: text/xml');
		$this->render('get-context-menu');
	}

	public function processClearDayAction() {
		$weekday = (int)$this->_getParam('weekday') - 1;
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$date = $weekDates['start'];
		if ($weekday > 0) {
			$date = date('Y-m-d',strtotime("+{$weekday} days",strtotime($date)));
		}
		$start = $date.' 00:00:00';
		$end = $date.' 23:59:59';
		$this->_processClear($providerId,$roomId,$start,$end);

		$data = array();
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		while ($row = $stmt->fetch()) {
			$data[] = $this->_getEvent($row['scheduleEventId']);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processClearWeekAction() {
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$start = $weekDates['start'].' 00:00:00';
		$end = $weekDates['end'].' 23:59:59';

		$this->_processClear($providerId,$roomId,$start,$end);

		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _processClear($providerId,$roomId,$start,$end) {
		$schedule = new Schedule();
		$schedule->providerId = $providerId;
		$schedule->roomId = $roomId;
		$schedule->populateByProviderRoomId();

		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->scheduleId = $schedule->scheduleId;
		$scheduleEvent->providerId = $schedule->providerId;
		$scheduleEvent->roomId = $schedule->roomId;
		$scheduleEvent->start = $start;
		$scheduleEvent->end = $end;
		$scheduleEvent->deleteByDateRange();
	}

	public function processCopyAction() {
		$from = (int)$this->_getParam('from') - 1;
		$to = (int)$this->_getParam('to') - 1;
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));

		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');


		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$dateStart = date('Y-m-d',strtotime($weekDates['start']));

		$dateFrom = date('Y-m-d',strtotime("+{$from} days",strtotime($dateStart)));
		$dateTo = date('Y-m-d',strtotime("+{$to} days",strtotime($dateStart)));

		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->providerId = $providerId;
		$scheduleEvent->roomId = $roomId;
		$scheduleEvent->start = $dateTo.' 00:00:00';
		$scheduleEvent->end = $dateTo.' 23:59:59';
		$scheduleEvent->deleteByDateRange(); // remove any existing data

		$start = $dateFrom.' 00:00:00';
		$end = $dateFrom.' 23:59:59';
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		$strDateTo = strtotime($dateTo);
		while ($event = $stmt->fetch()) {
			$row = new ScheduleEvent();
			$row->populateWithArray($event);
			$row->scheduleEventId = 0;
			$row->start = $dateTo.' '.date('H:i',strtotime($row->start));
			$row->end = $dateTo.' '.date('H:i',strtotime($row->end));
			$row->persist();
		}

		$start = $dateTo.' 00:00:00';
		$end = $dateTo.' 23:59:59';
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		$data = array();
		while ($row = $stmt->fetch()) {
			$data[] = $this->_getEvent($row['scheduleEventId']);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSaveTemplateAction() {
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$templateName = preg_replace('/[^a-z_0-9\- ]+/i','',$this->_getParam('templateName'));

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$dateStart = date('Y-m-d',strtotime($weekDates['start']));
		$dateEnd = date('Y-m-d',strtotime($weekDates['end']));

		$start = $dateStart.' 00:00:00';
		$end = $dateEnd.' 23:59:59';
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);

		$strXML = '<'.ScheduleTemplate::XML_ROOT_TAG.'/>';
		$xml = new SimpleXMLElement($strXML);

		$parents = array();
		while ($event = $stmt->fetch()) {
			$row = new ScheduleEvent();
			$row->populateWithArray($event);
			$startToTime = strtotime($row->start);
			$weekday = date('l',$startToTime);
			if (!isset($parents[$weekday])) {
				$root = $xml->addChild($weekday);
				$block = $root->addChild('block');
				$parents[$weekday] = array('root'=>$root,'block'=>$block);
			}
			$block = $parents[$weekday]['block'];
			$block->addChild('start',date('H:i',$startToTime));
			$block->addChild('end',date('H:i',strtotime($row->end)));
		}
		$scheduleTemplate = new ScheduleTemplate();
		$scheduleTemplate->name = $templateName;
		$scheduleTemplate->description = $templateName;
		$scheduleTemplate->template = $xml->asXML();
		$scheduleTemplate->persist();
		$data = array();
		$data['id'] = (int)$scheduleTemplate->scheduleTemplateId;
		$data['name'] = $scheduleTemplate->name;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function loadTemplateAction() {
		$template = new ScheduleTemplate();
		$templateIterator = $template->getIterator();
		$this->view->templates = $templateIterator->toArray('scheduleTemplateId','name');
		$this->view->jsCallback = $this->_getParam('jsCallback');
		$this->render();
	}

	public function processLoadTemplateAction() {
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$scheduleTemplateId = (int)$this->_getParam('templateId');

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$dateStart = date('Y-m-d',strtotime($weekDates['start']));
		$dateEnd = date('Y-m-d',strtotime($weekDates['end']));

		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->providerId = $providerId;
		$scheduleEvent->roomId = $roomId;
		$scheduleEvent->start = $dateStart.' 00:00:00';
		$scheduleEvent->end = $dateEnd.' 23:59:59';
		$scheduleEvent->deleteByDateRange(); // remove any existing data for the whole week

		$scheduleTemplate = new ScheduleTemplate();
		$scheduleTemplate->scheduleTemplateId = $scheduleTemplateId;
		if ($scheduleTemplate->populate()) {
			try {
				$data = array();
				$xml = new SimpleXMLElement($scheduleTemplate->template);
				foreach ($xml as $weekday=>$element) {
					foreach ($element->block as $blocks) {
						$startEnd = array();
						$ctr = 0;
						foreach ($blocks->start as $key=>$values) {
							$startEnd[$ctr++]['start'] = (string)$values;
						}
						$ctr = 0;
						foreach ($blocks->end as $key=>$values) {
							$startEnd[$ctr++]['end'] = (string)$values;
						}
						$data[$weekday] = $startEnd;
					}
				}
				$dateStartToTime = strtotime($dateStart);
				$dateEndToTime = strtotime($dateEnd);
				do {
					$weekday = date('l',$dateStartToTime);
					if (isset($data[$weekday])) {
						$schedule = new Schedule();
						$schedule->providerId = $scheduleEvent->providerId;
						$schedule->roomId = $scheduleEvent->roomId;
						$schedule->populateByProviderRoomId();

						$date = date('Y-m-d',$dateStartToTime);
						$event = new ScheduleEvent();
						$event->providerId = $schedule->providerId;
						$event->roomId = $schedule->roomId;
						$event->scheduleId = $schedule->scheduleId;
						$event->title = preg_replace('/^schedule /i','Event ',$schedule->title);
						//$event->scheduleCode = '';
						foreach ($data[$weekday] as $row) {
							$event->scheduleEventId = 0;
							$event->start = $date.' '.date('H:i',strtotime($row['start']));
							$event->end = $date.' '.date('H:i',strtotime($row['end']));
							$event->persist();
						}
					}
					$dateStartToTime = strtotime('+1 day',$dateStartToTime);
				} while ($dateStartToTime < $dateEndToTime);
			}
			catch (Exception $e) {
				trigger_error('ERROR: '.$e->getMessage(),E_USER_NOTICE);
			}
		}

		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getUserDetailsAction() {
		$id = (int)$this->_getParam('id');
		$user = new User();
		$user->personId = $id;
		$user->populateWithPersonId();
		$data = $user->toArray();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function toolbarXmlAction() {
		header('Content-Type: text/xml');
		$this->render('toolbar-xml');
	}

	public function getEventAction() {
		$id = (int)$this->_getParam('id');
		$data = $this->_getEvent($id);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _getEvent($id) {
		static $buildings = array();
		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->scheduleEventId = $id;
		$data = array('data'=>'','userdata'=>'','buildingId'=>'');
		if ($scheduleEvent->populate()) {
			$buildingId = (int)$scheduleEvent->buildingId;
			if (!isset($buildings[$buildingId])) {
				$building = new Building();
				$building->buildingId = $buildingId;
				$building->populate();
				$buildingName = '';
				if (strlen($building->name) > 0) {
					$buildingName = $building->practice->name.'->'.$building->name;
				}
				$buildings[$buildingId] = $buildingName;
			}
			$data['time'] = date('h:iA',strtotime($scheduleEvent->start)).'-'.date('h:iA',strtotime($scheduleEvent->end));
			$data['data'] = $data['time'].'<br>'.$scheduleEvent->title.'<br>'.$buildings[$buildingId];
			$data['title'] = $scheduleEvent->title;
			$data['userdata'] = (int)$scheduleEvent->scheduleEventId;
			$data['buildingId'] = $buildingId;
		}
		return $data;
	}

	public function getScheduleHolidaysAction() {
		$date = date('Y-m-d',strtotime($this->_getParam('date')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$data = array('color' => '','rows'=>array());
		if ($providerId > 0 || $roomId > 0) {
			$schedule = new Schedule();
			$schedule->providerId = $providerId;
			$schedule->roomId = $roomId;
			if ($schedule->populateByProviderRoomId()) {
				$color = $schedule->room->color;
				if ($schedule->providerId > 0) {
					$color = $schedule->provider->color;
				}
				if (strlen($color) > 0 && substr($color,0,1) != '#') {
					$color = '#'.$color;
				}
				$data['color'] = $color;
			}
		}

		$weekDates = ScheduleEvent::computeWeekDates($date);
		$dateStart = $weekDates['start'];
		$dateEnd = $weekDates['end'];

		$dateStartToTime = strtotime($dateStart);
		$dateEndToTime = strtotime($dateEnd);

		$holidays = array();
		while (true) {
			$weekday = date('N',$dateStartToTime);
			$date = date('Y-m-d',$dateStartToTime);
			if (!isset($holidays[$weekday])) {
				$holidays[$weekday] = Holiday::isDayHoliday($date);
			}
			$cell = '';
			if ($holidays[$weekday]) {
				$cell = __('holiday');
				if (strlen($holidays[$weekday]['description']) > 0) {
					$cell .= ' ('.$holidays[$weekday]['description'].')';
				}
			}
			if (!isset($data['rows'][$weekday])) {
				$data['rows'][$weekday] = array();
			}
			$data['rows'][$weekday]['data'] = $cell;
			$data['rows'][$weekday]['date'] = $date;

			$dateStartToTime = strtotime('+1 day',$dateStartToTime);
			if ($dateStartToTime > $dateEndToTime) {
				break;
			}
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function repeatTemplateAction() {
		$date = date('Y-m-d',strtotime($this->_getParam('date')));
		$weekDates = ScheduleEvent::computeWeekDates($date);
		$this->view->dateStart = $weekDates['start'];
		$this->view->dateEnd = $weekDates['end'];
		$this->render();
	}

	public function processRepeatTemplateAction() {
		$dateStartFrom = date('Y-m-d',strtotime($this->_getParam('dateStartFrom')));
		$dateEndFrom = date('Y-m-d',strtotime($this->_getParam('dateEndFrom')));
		$dateStartTo = date('Y-m-d',strtotime($this->_getParam('dateStartTo')));
		$dateEndTo = date('Y-m-d',strtotime($this->_getParam('dateEndTo')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');

		$scheduleEvents = array();
		$start = $dateStartFrom.' 00:00:00';
		$end = $dateEndFrom.' 23:59:59';
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		while ($event = $stmt->fetch()) {
			$row = new ScheduleEvent();
			$row->populateWithArray($event);
			$key = date('Y-m-d',strtotime($row->start));
			if (!isset($scheduleEvents[$key])) {
				$scheduleEvents[$key] = array();
			}
			$scheduleEvents[$key][] = $row;
		}

		$start = $dateStartTo.' 00:00:00';
		$end = $dateEndTo.' 23:59:59';
		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->providerId = $providerId;
		$scheduleEvent->roomId = $roomId;
		$scheduleEvent->start = $start;
		$scheduleEvent->end = $end;
		$scheduleEvent->deleteByDateRange(); // remove any existing data

		if (count($scheduleEvents) > 0) {
			// get the difference
			$dateStartFromToTime = strtotime($dateStartFrom);
			$dateEndFromToTime = strtotime($dateEndFrom);
			$dateFromDiff = $dateEndFromToTime - $dateStartFromToTime;
			$dateDiff = 0;
			if ($dateFromDiff > 0) {
				$dateDiff = $dateFromDiff / (86400);
			}
			$dateStartToToTime = strtotime($dateStartTo);
			$dateEndToToTime = strtotime($dateEndTo);

			if ($dateDiff < 7) { // a week or less than; feb 1 is copied to every monday of feb8th - feb 31st, feb 2nd (which is a tuesday) to every tuesday, etc
				$datesCompleted = array();
				foreach ($scheduleEvents as $date=>$values) {
					$weekday = date('N',strtotime($date));
					$tmpToTime = $dateStartToToTime;
					while (true) {
						if ($tmpToTime > $dateEndToToTime) {
							break;
						}
						$toDate = date('Y-m-d',$tmpToTime);
						if (!isset($datesCompleted[$toDate]) && date('N',$tmpToTime) == $weekday) {
							$datesCompleted[$toDate] = true;
							foreach ($values as $row) {
								$row->start = $toDate.' '.date('H:i:s',strtotime($row->start));
								$row->end = $toDate.' '.date('H:i:s',strtotime($row->end));
								$row->scheduleEventId = 0;
								$row->providerId = $providerId;
								$row->roomId = $roomId;
								$row->persist();
							}
						}
						$tmpToTime = strtotime('+1 day',$tmpToTime);
					}
				}
			}
			else {
				// sync the first day of the copy from period to the first day of the copy to period
				$weekdayFrom = date('N',$dateStartFromToTime);
				$tmpToTime = $dateStartToToTime;
				while (true) {
					if ($tmpToTime > $dateEndToToTime || date('N',$tmpToTime) == $weekdayFrom) {
						break;
					}
					$tmpToTime = strtotime('+1 day',$tmpToTime);
				}


				$tmpStartFromToTime = $dateStartFromToTime;
				$newDateStartToToTime = $tmpToTime;
				while (true) {
					if ($newDateStartToToTime > $dateEndToToTime) {
						break;
					}
					if ($tmpStartFromToTime > $dateEndFromToTime) {
						$tmpStartFromToTime = $dateStartFromToTime;
					}
					$date = date('Y-m-d',$tmpStartFromToTime);
					if (isset($scheduleEvents[$date])) {
						$toDate = date('Y-m-d',$newDateStartToToTime);
						foreach ($scheduleEvents[$date] as $row) {
							$row->start = $toDate.' '.date('H:i:s',strtotime($row->start));
							$row->end = $toDate.' '.date('H:i:s',strtotime($row->end));
							$row->scheduleEventId = 0;
							$row->providerId = $providerId;
							$row->roomId = $roomId;
							$row->persist();
						}
					}
					$tmpStartFromToTime = strtotime('+1 day',$tmpStartFromToTime);
					$newDateStartToToTime = strtotime('+1 day',$newDateStartToToTime);
				}
			}
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function copyToUserAction() {
		$date = date('Y-m-d',strtotime($this->_getParam('date')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$header = 'Copying from ';
		if ($providerId > 0) {
			$person = new Person();
			$person->personId = $providerId;
			$person->populate();
			$header .= '['.$person->lastName.', '.$person->firstName.', '.$person->suffix.']';
		}
		else if ($roomId > 0) {
			$room = new Room();
			$room->roomId = $roomId;
			$room->populate();
			$header .= '['.$room->building->practice->name.'->'.$room->building->name.'->'.$room->name.']';
		}
		$this->view->header = $header;
		$weekDates = ScheduleEvent::computeWeekDates($date);
		$this->view->dateStart = $weekDates['start'];
		$this->view->dateEnd = $weekDates['end'];
		$this->render();
	}

	public function processCopyToUserRemoveAction() {
		$dateStart = date('Y-m-d',strtotime($this->_getParam('dateStart')));
		$dateEnd = date('Y-m-d',strtotime($this->_getParam('dateEnd')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');

		$start = $dateStart.' 00:00:00';
		$end = $dateEnd.' 23:59:59';
		$data = ScheduleEvent::getNumberOfEvents($providerId,$roomId,$start,$end);

		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->providerId = $providerId;
		$scheduleEvent->roomId = $roomId;
		$scheduleEvent->start = $start;
		$scheduleEvent->end = $end;
		$scheduleEvent->deleteByDateRange(); // remove any existing data

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _stmtScheduleEvents($providerId,$roomId,$start,$end) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('scheduleEvents')
				->where('providerId = ?',$providerId)
				->where('roomId = ?',$roomId)
				->where('start >= ?',$start)
				->where('end <= ?',$end)
				->order('start ASC');
		$stmt = $db->query($sqlSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $stmt;
	}

	public function processCopyToUserAddAction() {
		$dateStart = date('Y-m-d',strtotime($this->_getParam('dateStart')));
		$dateEnd = date('Y-m-d',strtotime($this->_getParam('dateEnd')));
		$providerIdTo = (int)$this->_getParam('providerIdTo');
		$roomIdTo = (int)$this->_getParam('roomIdTo');
		$providerIdFrom = (int)$this->_getParam('providerIdFrom');
		$roomIdFrom = (int)$this->_getParam('roomIdFrom');

		$start = $dateStart.' 00:00:00';
		$end = $dateEnd.' 23:59:59';
		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->providerId = $providerIdTo;
		$scheduleEvent->roomId = $roomIdTo;
		$scheduleEvent->start = $start;
		$scheduleEvent->end = $end;
		$scheduleEvent->deleteByDateRange(); // remove any existing data

		$data = 0;
		$providerId = $providerIdFrom;
		$roomId = $roomIdFrom;
		$start = $dateStart.' 00:00:00';
		$end = $dateEnd.' 23:59:59';
		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		while ($event = $stmt->fetch()) {
			$row = new ScheduleEvent();
			$row->populateWithArray($event);
			$row->scheduleEventId = 0;
			$row->providerId = $providerIdTo;
			$row->roomId = $roomIdTo;
			$row->persist();
			$data++;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listUserRoomAction() {
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$db = Zend_Registry::get('dbAdapter');

		$rows = array();
		// if every provider MUST have a corresponding user data, then the below query is not required
		$sqlSelect = $db->select()
				->from('user')
				->joinUsing('person','person_id')
				->order('person.last_name DESC')
				->order('person.first_name DESC');
		$userResults = $db->fetchAll($sqlSelect);
		foreach ($userResults as $row) {
			if ($row['person_id'] == $providerId) continue;
			$name = ucwords(strtolower($row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1))) . ' ' . $row['suffix'] . ' (' . $row['username'] . ')'; 
			$rows[] = array('id'=>'pid'.$row['person_id'],'data'=>array($name));
		}

		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building', 'Room'));
		foreach($facilityIterator as $facilities) {
			if ($facilities['Room']->roomId == $roomId) continue;
			$name = $facilities['Practice']->name.'->'.$facilities['Building']->name.'->'.$facilities['Room']->name;
			$rows[] = array('id'=>'rid'.$facilities['Room']->roomId,'data'=>array($name));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function getScheduleDataAction() {
		$dateStart = date('Y-m-d',strtotime($this->_getParam('dateStart')));
		$dateEnd = date('Y-m-d',strtotime($this->_getParam('dateEnd')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');

		$data = array();
		$start = $dateStart.' 00:00:00';
		$end = $dateEnd.' 23:59:59';
		$numberOfEvents = ScheduleEvent::getNumberOfEvents($providerId,$roomId,$start,$end);
		$data['numberOfEvents'] = $numberOfEvents;

		$title = '';
		if ($providerId > 0) {
			$person = new Person();
			$person->personId = $providerId;
			$person->populate();
			$title .= $person->lastName.', '.$person->firstName.', '.$person->suffix;
		}
		else if ($roomId > 0) {
			$room = new Room();
			$room->roomId = $roomId;
			$room->populate();
			$title .= $room->building->practice->name.'->'.$room->building->name.'->'.$room->name;
		}
		$data['title'] = $title;
		$data['dateStart'] = date('m/d/Y',strtotime($dateStart));;
		$data['dateEnd'] = date('m/d/Y',strtotime($dateEnd));;

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processRemoveScheduleAction() {
		$weekday = (int)$this->_getParam('weekday') - 1;
		$scheduleDate = date('Y-m-d',strtotime($this->_getParam('scheduleDate')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$scheduleEventId = (int)$this->_getParam('id');

		$weekDates = ScheduleEvent::computeWeekDates($scheduleDate);
		$date = $weekDates['start'];
		if ($weekday > 0) {
			$date = date('Y-m-d',strtotime("+{$weekday} days",strtotime($date)));
		}

		if ($scheduleEventId > 0) {
			$scheduleEvent = new ScheduleEvent();
			$scheduleEvent->scheduleEventId = $scheduleEventId;
			$scheduleEvent->setPersistMode(WebVista_Model_ORM::DELETE);
			$scheduleEvent->persist();
		}

		$start = $date.' 00:00:00';
		$end = $date.' 23:59:59';

		$stmt = $this->_stmtScheduleEvents($providerId,$roomId,$start,$end);
		$data = array();
		while ($row = $stmt->fetch()) {
			$data[] = $this->_getEvent($row['scheduleEventId']);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
