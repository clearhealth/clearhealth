<?php
/*****************************************************************************
*       TestDataController.php
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
 * Controller to generate test data
 */
class TestDataController extends WebVista_Controller_Action {

	public function generateTestSsDemoDataAction() {
		$basePath = Zend_Registry::get('basePath');
		$xmlFile = $basePath.'xml/ss-demo-data.xml';
		$xml = new SimpleXMLElement(file_get_contents($xmlFile));

		foreach ($xml as $key=>$value) {
			switch ($key) {
				case 'prescriber':
					$person = new Person();
					$person->active = 1;
					$person->lastName = (string)$value->PrescriberLastName;
					$person->firstName = (string)$value->PrescriberFirstName;
					$person->middleName = (string)$value->PrescriberMiddleName;
					$person->suffix = (string)$value->PrescriberNameSuffix;
					//$person->initials = (string)$value->PrescriberNamePrefix;

					$provider = new Provider();
					$provider->person = $person;
					$provider->sureScriptsSPI = (string)$value->SPI;
					$provider->deaNumber = (string)$value->DEANumber;
					$provider->stateLicenseNumber = (string)$value->StateLicenseNumber;
					$provider->persist();

					//ProviderSpecialty, ClinicName

					$phone = new PhoneNumber();
					$phone->personId = $provider->personId;
					$phone->name = 'Primary';
					$phone->type = 4;
					$phone->number = str_replace('-','',(string)$value->PrescriberPhone);
					$phone->persist();

					$address = new Address();
					$address->personId = $provider->personId;
					$address->name = 'Main';
					$address->type = 4;
					$address->active = 1;
					$address->line1 = (string)$value->ClinicAddressLine1;
					$address->line2 = (string)$value->ClinicAddressLine2;
					$address->city = (string)$value->ClinicCity;
					$address->state = (string)$value->ClinicState;
					$address->postalCode = (string)$value->ClinicZip;
					$address->persist();
					break;
				case 'pharmacy':
					$pharmacy = new Pharmacy();
					$pharmacy->NCPDPID = (string)$value->NCPDPID;
					/*$pharmacy-> = (string)$value->ReferenceQualifier;
					$pharmacy-> = (string)$value->PharmacistLastName;
					$pharmacy-> = (string)$value->PharmacistFirstName;
					$pharmacy-> = (string)$value->PharmacistMiddleName;
					$pharmacy-> = (string)$value->PharmacistNamePrefix;
					$pharmacy-> = (string)$value->PharmacistNameSuffix;*/
					$pharmacy->StoreName = (string)$value->PharmacyName;
					$pharmacy->AddressLine1 = (string)$value->PharmacyAddressLine1;
					$pharmacy->City = (string)$value->PharmacyCity;
					$pharmacy->State = (string)$value->PharmacyState;
					$pharmacy->Zip = (string)$value->PharmacyZip;
					$pharmacy->PhonePrimary = (string)$value->PharmacyPhone;
					$pharmacy->persist();
					break;
				case 'patient':
					$person = new Person();
					$person->active = 1;
					$person->lastName = (string)$value->PatientLastName;
					$person->firstName = (string)$value->PatientFirstName;
					$person->middleName = (string)$value->PatientMiddleName;
					//$person->initials = (string)$value->PatientNamePrefix;
					$person->identifier = (string)$value->PatientSS;

					$address = new Address();
					$address->name = 'Main';
					$address->type = 4;
					$address->active = 1;
					$address->line1 = (string)$value->PatientAddressLine1;
					$address->line2 = (string)$value->PatientAddressLine2;
					$address->city = (string)$value->PatientCity;
					$address->state = (string)$value->PatientState;
					$address->postalCode = (string)$value->PatientZip;

					$patient = new Patient();
					$patient->person = $person;
					$patient->homeAddress = $address;
					$patient->persist();

					$phone = new PhoneNumber();
					$phone->personId = $patient->personId;
					$phone->name = 'Primary';
					$phone->type = 4;
					$phone->number = str_replace('-','',(string)$value->PatientPhone);
					$phone->persist();
					break;
				case 'medication':
					break;
			}
		}

		echo 'Done';
		die;
	}

	public function generateExamPreferencesAction() {
		Enumeration::generatePatientExamPreferencesEnum();
		Enumeration::generateExamResultPreferencesEnum();
		Enumeration::generateExamOtherPreferencesEnum();
		echo 'Done';
		die;
	}

	public function generateImmunizationPreferencesAction() {
		Enumeration::generateImmunizationPreferencesEnum(true);
		echo 'Done';
		die;
	}

	public function generateTestEnumDataAction() {
		Enumeration::generateVitalUnitsEnum();
		echo 'Done';
		die;
	}

	public function addressEnumAction() {
		Enumeration::generateCountriesEnum();
		Enumeration::generateStatesEnum();
		echo 'Done.';
		die;
	}

	public function medicationEnumAction() {
		Enumeration::generateMedicationPreferencesEnum();
		echo 'Done.';
		die;
	}

	public function init() {
		ini_set('max_execution_time',999999);
	}

	public function indexAction() {
		// TODO: sequence calls to all test data generators

		// Rooms
		$ctr = $this->_persistORM('Practice','practice.xml');
		$msg = $ctr.' practices loaded.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';
		$ctr = $this->_persistORM('Building','buildings.xml');

		$msg = $ctr.' buildings loaded.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';

		$ctr = $this->_persistORM('Room','rooms.xml');
		$msg = $ctr.' rooms loaded.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';

		// Providers
		$ctr = $this->_persistORM('Provider','providers.xml');
		$msg = $ctr.' providers loaded.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';

		// Patients
		$ctr = $this->_persistORM('Patient','patients.xml');
		$msg = $ctr.' patients loaded.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';

		$msg = 'generating schedules...';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg;
		$this->_generateSchedules();
		$msg = ' done.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';

		$msg = 'generating appointments...';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg;
		$this->_generateAppointments();
		$msg = ' done.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';


		$msg = 'loading additional data...';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';
		$basePath = Zend_Registry::get('basePath');
		$sqlDir = $basePath.'/sql';

		$dir = new DirectoryIterator($basePath.DIRECTORY_SEPARATOR.'sql');
		$dbName = Zend_Registry::get('config')->database->params->dbname;
		foreach ($dir as $file) {
			if (!$file->isDot()) {
				$cmd = 'mysql -u root '.$dbName.' < '.$file->getPathname();
				$msg = ' executing command '.$cmd;
				trigger_error($msg,E_USER_NOTICE);
				echo $msg.'<br />';
				exec($cmd);
			}
		}
		$msg = ' done.';
		trigger_error($msg,E_USER_NOTICE);
		echo $msg.'<br />';
		flush();
		die;
	}

	public function makeScheduleAction() {
		$this->_generateSchedules();
		echo 'Done.';
		die;
	}

	protected function _generateSchedules() {
		// get all rooms, used in random schedule
		//$this->_generateRooms();

		$facilityIterator = new FacilityIterator(); // used the FacilityIterator rather than the Room model
		$facilityIterator->setFilter(array('Room'));
		$rows = array();
		foreach ($facilityIterator as $room) {
			$rows[] = $room;
		}
		$dates = $this->_generateDays();

		// generate random schedules for each current provider
		$providerIterator = Provider::getIter();
		foreach ($providerIterator as $provider) {
			$rows[] = $provider;
		}

		foreach ($rows as $row) {
			$title = '';
			$roomId = 0;
			$providerId = 0;
			if ($row instanceof Room) {
				$roomId = (int)$row->id;
				$title = $row->name;
			}
			else if ($row instanceof Provider) {
				$providerId = (int)$row->personId;
				$title = $row->person->firstName;
			}
			$title .= "'s Event";

			// 08:00 - 12:00; 13:00 - 17:00
			// TODO: create a random time starts from 07:00 to 17:00
			$times = array();
			$time = array();
			$time['start'] = '08:00';
			$time['end'] = '12:00';
			$times[] = $time;
			$time = array();
			$time['start'] = '13:00';
			$time['end'] = '17:00';
			$times[] = $time;

			foreach ($dates as $date) {
				foreach ($times as $time) {
					$scheduleEvent = new ScheduleEvent();
					// disable cascadePersist, we only generate schedule event
					$this->_setORMPersistMode($scheduleEvent);

					//$scheduleEvent->scheduleEventId = 0; // must be set to 0 to add new schedule
					//$scheduleEvent->scheduleCode = ''; // leave as empty
					//$scheduleEvent->scheduleId = 0; // leave as empty
					$scheduleEvent->title = $title;
					$scheduleEvent->roomId = $roomId;
					$scheduleEvent->providerId = $providerId;

					$scheduleEvent->start = $date.' '.$time['start'];
					$scheduleEvent->end = $date.' '.$time['end'];
					$scheduleEvent->persist();
				}
			}
		}
	}

	protected function _generateRooms() {
		$ctr = $this->_persistORM('Room','rooms.xml');
		return $ctr;
	}

	public function makeAppointmentAction() {
		$this->_generateAppointments();
		echo 'Done';
		exit;
	}

	protected function _generateAppointments() {
		$patients = array();
		$patient = new Patient();
		$patientIterator = $patient->getIterator();
		foreach ($patientIterator as $row) {
			$patients[] = $row->personId;
		}
		$patientLen = count($patients) - 1;

		$startingDate = strtotime(date('Y-m-d'));
		$endingDate = strtotime('+1 month',$startingDate);

		$titles = array('Sick','Followup','Cough','Runny Nose','Lab Only','Annual');
		$titleLen = count($titles) - 1;
		$dates = $this->_generateDays();

		$providerIterator = new ProviderIterator();
		foreach ($providerIterator as $provider) {
			$providerId = $provider->personId;
			foreach ($dates as $date) {

				$timeSlots = array();
				echo 'current:'.$date.' time:'.calcTS().' memory:'.(memory_get_usage()/1024/1024).'<br/>';
				$hour = 8;
				while($hour < 18) {
					if (rand(0,1) == 1) {
						continue;
					}

					$plus = rand(0,2)*.25;
					$length = rand(1,4)*.25;

					if ($hour >= 12 && $hour < 13) { // lunch time
						$hour = 13;
					}

					$start = $date.' '.formatTime($hour);
					$hour += $length;

					if ($hour >= 12 && $hour < 13) { // lunch time
						$hour = 12;
					}
					$end = $date.' '.formatTime($hour);

					$timeSlots[] = array('start'=>$start,'end'=>$end);
				}

				$rows = array();
				$timeSlotLen = count($timeSlots) - 1;
				if ($timeSlotLen > $patientLen) {
					// patients are less than time slots
					$slots = $timeSlots;
					foreach ($patients as $patientId) {
						$row = array();
						shuffle($slots);
						$slot = array_shift($slots);
						$row['start'] = $slot['start'];
						$row['end'] = $slot['end'];
						$row['patientId'] = $patientId;
						$rows[] = $row;
					}
				}
				else {
					// patients are greater than time slots
					$patientIds = $patients;
					foreach ($timeSlots as $slot) {
						$row = array();
						$row['start'] = $slot['start'];
						$row['end'] = $slot['end'];
						shuffle($patientIds);
						$patientId = array_shift($patientIds);
						$row['patientId'] = $patientId;
						$rows[] = $row;
					}
				}

				foreach ($rows as $row) {
					$app = new Appointment();
					// disable cascadePersist, we only generate appointments
					$this->_setORMPersistMode($app);
					$app->patientId = $row['patientId'];
					$app->start = $row['start'];
					$app->end = $row['end'];
					$title = $titles[rand(0,$titleLen)];
					$app->title = $title;
					$app->providerId = $providerId;
					$app->createdDate = date('Y-m-d H:i:s');
					$app->persist();
				}
			}
		}
	}

	protected function _generateDays() {
		$days = array();
		$dayStart = strtotime(date('Y-m-d'));
		// currently, temporarily set to 1 month
		$dayEnd = strtotime('+1 month',$dayStart);
		// deduct 1 day just to sync the PHP time and MySQL time
		$dayStart = strtotime('-1 day',$dayStart);
		while ($dayStart < $dayEnd) {
			$dayStart = strtotime('+1 day',$dayStart);
			$days[] = date('Y-m-d', $dayStart);
		}
		return $days;
	}

	public function makePatientAction() {
		$loadProvider = (int)$this->_getParam('loadProvider');
		if ($loadProvider) {
			$ctr = $this->_persistORM('Provider','providers.xml');
			echo $ctr.' providers loaded.<br />';
		}

		$ctr = $this->_persistORM('Patient','patients.xml');
		echo $ctr.' patients loaded.<br />';
		echo 'Done';
		exit;
	}

	public function makeProviderAction() {
		$ctr = $this->_persistORM('Provider','providers.xml');
		echo $ctr.' providers loaded.<br />';
		echo 'Done.';
		die;
	}

	public function generateVaClassAction() {
		$file = 'http://www.pbm.va.gov/natform/vaclass.htm';
		$html = file_get_contents($file);
		preg_match_all("/<td[^>]*>(.*?)<\\/td>/si", $html, $matches);
		$contents = $matches[0];
		$start = false;
		$stop = false;
		$ctr = null;
		$arr = array();
		$data = array();
		foreach ($contents as $content) {
			if (stripos($content,'<table') !== false) {
				if (stripos($content,'VA Class') !== false) {
					$start = true;
				}
				else if ($start) {
					break;
				}
			}
			if ($start) {
				if ($ctr !== null) {
					if ($ctr % 2 === 0) {
						$arr['class'] = trim(strip_tags($content));
					}
					else {
						$arr['category'] = trim(strip_tags($content));
					}
					if (count($arr) == 2) {
						$data[] = $arr;
						$arr = array();
					}
					$ctr++;
				}
				if (stripos($content,'VA Category') !== false) {
					$ctr = 0;
				}
			}
		}

		foreach ($data as $row) {
			$drugCode = new DrugCodeClass();
			$drugCode->code = $row['class'];
			$drugCode->textShort = $row['category'];
			$drugCode->persist();
		}
		echo 'Done';
		die;
	}

	protected function _persistORM($ormClass,$xmlFile) {
		$xml2orm = new XML2ORM();
		return $xml2orm->persistORM($ormClass,$xmlFile);
	}

}

function formatTime($hour) {
	$mins = $hour * 60;
	$time = date('H:i:s',strtotime("2006-10-16 + $mins mins"));
	return $time;
}
