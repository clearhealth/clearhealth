<?php
/*****************************************************************************
*       HL7Generator.php
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


class HL7Generator {

	protected static function _generateHL7XML($data) {
		$hl7 = new HL7XML($data);
		$hl7->parse();
		return $hl7->xml->asXML();
	}

	public static function generatePatient($patientId,$inXML=true) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','COUNT(*) AS ctr')
				->where("objectClass = 'Patient' AND objectId = ?",(int)$patientId);
		$new = true;
		if ($row = $db->fetchRow($sqlSelect)) {
			if ($row['ctr'] > 1) {
				$new = false;
			}
		}
		if ($new) {
			$ret = self::generatePatientNew($patientId,$inXML);
		}
		else {
			$ret = self::generatePatientUpdate($patientId,$inXML);
		}
		file_put_contents('/tmp/patient.xml',$ret);
		return $ret;
	}

	public static function generatePatientNew($patientId,$inXML=true) {
		$data = 'MSH|^~\&|||||ADT^A04'."\n".self::generatePatientSegments($patientId);
		file_put_contents('/tmp/patient-new.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generatePatientUpdate($patientId,$inXML=true) {
		$data = 'MSH|^~\&|||||ADT^A08'."\n".self::generatePatientSegments($patientId);
		file_put_contents('/tmp/patient-update.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generatePatientSegments($patientId) {
		return self::generatePID($patientId); // temporarily set as alias of generatePID()
	}

	public static function generatePID($patientId) {
		$patient = new Patient();
		$patient->personId = (int)$patientId;
		$patient->populate();

		$phoneHome = '';
		$phoneBusiness = '';
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $patient->personId;
		$phones = $phoneNumber->getPhoneNumbers(false);
		foreach ($phones as $phone) {
			if ($phoneHome == '' && $phone['type'] == 'HP') {
				$phoneHome = $phone['number'];
			}
			if ($phoneBusiness == '' && $phone['type'] == 'TE') {
				$phoneBusiness = $phone['number'];
			}
		}

		/* most efficient way to create PID?
		$patientName = $patient->person->lastName.'^'.$patient->person->firstName.'^'.strtoupper(substr($patient->person->middleName,0,1));
		$addr = $patient->homeAddress;
		$address = $addr->line1.'^'.$addr->line2.'^'.$addr->city.'^'.$addr->state.'^'.$addr->zipCode;
		// reference: http://www.med.mun.ca/tedhoekman/medinfo/hl7/ch300056.htm
		$data = array();
		$data[] = 'PID';
		$data[] = ''; // 1: Set ID
		$data[] = ''; // 2: Patient ID (External)
		$data[] = $patient->recordNumber; // 3: Patient ID (Internal)
		$data[] = ''; // 4: Alternate Patient ID
		$data[] = $patientName; // 5: Patient Name
		$data[] = ''; // 6: Mother's Maiden Name
		$data[] = date('Ymd',strtotime($patient->person->dateOfBirth)); // 7: Data/Time of Birth
		$data[] = $patient->person->gender; // 8: Sex
		$data[] = ''; // 9: Patient Alias
		$data[] = ''; // 10: Race
		$data[] = $address; // 11: Patient Address
		$data[] = ''; // 12: Country Code
		$data[] = $phoneHome; // 13: Phone Number (Home)
		$data[] = $phoneBusiness; // 14: Phone Number (Business)
		$data[] = ''; // 15: Primary Language
		$data[] = $patient->person->maritalStatus; // 16: Marital Status
		$data[] = ''; // 17: Religion
		$data[] = ''; // 18: Patient Account Number
		$data[] = $patient->person->identifier; // 19: Patient SSS Number
		*/

		$data = array();
		$data['mrn'] = $patient->recordNumber;
		$data['lastName'] = $patient->person->lastName;
		$data['firstName'] = $patient->person->firstName;
		$data['middleInitial'] = strtoupper(substr($patient->person->middleName,0,1));
		$data['dateOfBirth'] = date('Ymd',strtotime($patient->person->dateOfBirth));
		$data['gender'] = $patient->person->gender;
		$address = $patient->homeAddress; // 2.x
		// fall back for 3.x
		if (!$address->addressId > 0) {
			$address = new Address();
			$address->personId = $patient->personId;
			$addressIterator = $address->getIteratorByPersonId();
			foreach ($addressIterator as $address) {
				break; // retrieves the top address
			}
		}
		$data['addressLine1'] = $address->line1;
		$data['addressLine2'] = $address->line2;
		$data['addressCity'] = $address->city;
		$data['addressState'] = $address->state;
		$data['addressZip'] = $address->zipCode;
		$data['phoneHome'] = $phoneHome;
		$data['phoneBusiness'] = $phoneBusiness;
		$data['ssn'] = $patient->person->identifier;
		$statistics = PatientStatisticsDefinition::getPatientStatistics((int)$patient->personId);
		$data['race'] = '';
		if (isset($statistics['Race'])) $data['race'] = $statistics['Race'];
		if (isset($statistics['race'])) $data['race'] = $statistics['race'];

		return 'PID|1||'.$data['mrn'].'||'.$data['lastName'].'^'.$data['firstName'].'^'.$data['middleInitial'].'||'.$data['dateOfBirth'].'|'.$data['gender'].'||'.$data['race'].'|'.$data['addressLine1'].'^'.$data['addressLine2'].'^'.$data['addressCity'].'^'.$data['addressState'].'^'.$data['addressZip'].'||'.$data['phoneHome'].'|'.$data['phoneBusiness'].'|||||'.$data['ssn'];
	}

	public static function generatePV1($appointment) {
		if (!$appointment instanceOf Appointment) {
			$appointmentId = (int)$appointment;
			$appointment = new Appointment();
			$appointment->appointmentId = $appointmentId;
			$appointment->populate();
		}
		$providerId = (int)$appointment->providerId;
		$provider = new Provider();
		$provider->personId = $providerId;
		$provider->populate();
		$data = array();
		$data['drId'] = $provider->deaNumber;
		$data['drLastName'] = $provider->person->lastName;
		$data['drFirstName'] = $provider->person->firstName;
		$data['drMiddleInitial'] = strtoupper(substr($provider->person->middleName,0,1));
		return 'PV1|1|O|||||'.$data['drId'].'^'.$data['drLastName'].'^'.$data['drFirstName'].'^'.$data['drMiddleInitial'];
	}

	public static function generateAppointment($appointmentId,$inXML=true) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','COUNT(*) AS ctr')
				->where("objectClass = 'Appointment' AND objectId = ?",(int)$appointmentId);
		$new = true;
		if ($row = $db->fetchRow($sqlSelect)) {
			if ($row['ctr'] > 1) {
				$new = false;
			}
		}
		if ($new) {
			$ret = self::generateAppointmentNew($appointmentId,$inXML);
		}
		else {
			// check appointment if cancel or no show
			$appointment = new Appointment();
			$appointment->appointmentId = (int)$appointmentId;
			$appointment->populate();
			if ($appointment->appointmentCode == 'CAN') {
				$ret = self::generateAppointmentCancel($appointment,$inXML);
			}
			else if ($appointment->appointmentCode == 'NS') {
				$ret = self::generateAppointmentNoShow($appointment,$inXML);
			}
			else {
				$ret = self::generateAppointmentUpdate($appointment,$inXML);
			}
		}
		file_put_contents('/tmp/appointment.xml',$ret);
		return $ret;
	}

	public static function generateAppointmentNew($appointment,$inXML=true) {
		$data = 'MSH|^~\&|||||SIU^S12'."\n".self::generateAppointmentSegments($appointment);
		file_put_contents('/tmp/appointment-new.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generateAppointmentUpdate($appointment,$inXML=true) {
		$data = 'MSH|^~\&|||||SIU^S14'."\n".self::generateAppointmentSegments($appointment);
		file_put_contents('/tmp/appointment-update.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generateAppointmentCancel($appointment,$inXML=true) {
		$ret = array('MSH|^~\&|||||SIU^S15');
		$ret[] = self::generateSCH($appointment);
		$data = implode("\n",$ret);
		file_put_contents('/tmp/appointment-cancel.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generateAppointmentNoShow($appointment,$inXML=true) {
		$ret = array('MSH|^~\&|||||SIU^S26');
		$ret[] = self::generateSCH($appointment);
		$data = implode("\n",$ret);
		file_put_contents('/tmp/appointment-no-show.hl7',$data);
		if (!$inXML) return $data;
		return self::_generateHL7XML($data);
	}

	public static function generateAppointmentSegments($appointment) {
		if (!$appointment instanceOf Appointment) {
			$appointmentId = (int)$appointment;
			$appointment = new Appointment();
			$appointment->appointmentId = $appointmentId;
			$appointment->populate();
		}
		$ret = array();
		$ret[] = self::generateSCH($appointment->appointmentId);
		$ret[] = self::generatePV1($appointment);
		$ret[] = self::generatePID($appointment->patientId);
		$ret[] = self::generateAIL($appointment);
		return implode("\n",$ret);
	}

	public static function generateSCH($appointment) {
		$fillerStatusCodes = array(
			'1'=>'Booked',
			'2'=>'Cancelled',
			'3'=>'No Show',
			'4'=>'Complete',
			'5'=>'Overbook',
			'6'=>'Blocked',
			'7'=>'Deleted',
			'8'=>'Started',
			'9'=>'Pending',
			'10'=>'Waitlist',
			'11'=>'DC',
		);
		if (!$appointment instanceOf Appointment) {
			$appointmentId = (int)$appointment;
			$appointment = new Appointment();
			$appointment->appointmentId = $appointmentId;
			$appointment->populate();
		}

		$statusCode = 1; // Default: Booked
		// check appointment if cancel or no show
		if ($appointment->appointmentCode == 'CAN') {
			$statusCode = 2; // Cancelled
		}
		else if ($appointment->appointmentCode == 'NS') {
			$statusCode = 3; // No Show
		}

		// reference: http://www.med.mun.ca/tedhoekman/medinfo/hl7/ch100060.htm
		$data = array();
		$data['appointmentId'] = $appointment->appointmentId;
		$data['appointmentReasonIdentifier'] = $appointment->reason;
		$data['appointmentReasonText'] = $appointment->title;
		$data['quantity'] = '';
		$data['interval'] = '';
		$data['duration'] = '';
		$data['start'] = date('YmdHi',strtotime($appointment->start));
		$data['end'] = date('YmdHi',strtotime($appointment->end));
		$data['statusCode'] = $statusCode;
		return 'SCH|'.$data['appointmentId'].'||||||'.$data['appointmentReasonIdentifier'].'^'.$data['appointmentReasonText'].'||||'.$data['quantity'].'^'.$data['interval'].'^'.$data['duration'].'^'.$data['start'].'^'.$data['end'].'||||||||||||||'.$data['statusCode'].'^'.$fillerStatusCodes[$data['statusCode']];
	}

	public static function generateAIL($appointment) {
		if (!$appointment instanceOf Appointment) {
			$appointmentId = (int)$appointment;
			$appointment = new Appointment();
			$appointment->appointmentId = $appointmentId;
			$appointment->populate();
		}
		$room = new Room();
		$room->roomId = $appointment->roomId;
		$room->populate();
		return 'AIL|'.$appointment->appointmentId.'||'.$room->name;
	}


	// ADD/UPDATE PATIENT AND APPOINTMENT
	public static function patient($personId) {
		$newPatient = true;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','COUNT(*) AS ctr')
				->where("objectClass = 'Patient' AND objectId = ?",(int)$personId);
		if (($row = $db->fetchRow($sqlSelect)) && $row['ctr'] > 1) $newPatient = false;

		$data = self::_getPatientData($personId); 

		$subscribersEnum = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(InsuranceProgram::INSURANCE_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		foreach ($enumerationClosure->getAllDescendants($enumeration->enumerationId,1,true) as $enum) {
			$rowset = $enumerationClosure->getAllDescendants($enum->enumerationId,1,true);
			if ($enum->key != InsuranceProgram::INSURANCE_SUBSCRIBER_ENUM_KEY) continue;
			foreach ($rowset as $row) {
				$subscribersEnum[$row->key] = $row->name;
			}
		}

		$insuredRelationship = new InsuredRelationship();
		$sqlSelect = $db->select()
				->from($insuredRelationship->_table)
				->where('person_id = ?',$personId)
				->where('active = 1')
				->order('program_order');
		$insuredRelationshipIterator = $insuredRelationship->getIterator($sqlSelect);
		$subscribers = array();
		$insurances = array();
		$guarantors = array();
		foreach ($insuredRelationshipIterator as $item) {
			$subscriberId = (int)$item->subscriberId;
			$subscriberToPatientRelationship = $item->subscriberToPatientRelationship;
			if (!$subscriberId > 0) {
				$subscriberId = $personId;
				$subscriberToPatientRelationship = 'SELF';
			}
			if (isset($subscribersEnum[$subscriberToPatientRelationship])) $subscriberToPatientRelationship = $subscribersEnum[$subscriberToPatientRelationship];
			if (!isset($subscribers[$subscriberId])) {
				$patient = new Patient();
				$patient->personId = $subscriberId;
				$patient->populate();
				$subscribers[$subscriberId] = $patient;
			}

			$subscriber = self::_getPatientData($subscribers[$subscriberId],false);
			$subscriber['relation'] = strtoupper(substr($subscriberToPatientRelationship,0,1));

			$insuranceProgram = $item->insuranceProgram;
			$companyId = (int)$insuranceProgram->companyId;

			$phoneNumber = new PhoneNumber();
			$sqlSelect = $db->select()
					->from(array('cn'=>'company_number'))
					->join(array('n'=>'number'),'n.number_id = cn.number_id')
					->where('n.active = 1')
					->where('cn.company_id = ?',$companyId)
					->order('n.displayOrder')
					->limit(1);
			$phoneNumber->populateWithSql($sqlSelect->__toString());

			$insurance = array();
			$insurance['companyId'] = $companyId;
			$insurance['companyName'] = $insuranceProgram->company->name;
			$address = $insuranceProgram->address;
			$insurance['line1'] = $address->line1;
			$insurance['line2'] = $address->line2;
			$insurance['city'] = $address->city;
			$insurance['state'] = $address->state;
			$insurance['zip'] = $address->postalCode;
			$insurance['phoneNumber'] = $phoneNumber->number;
			$insurance['groupNumber'] = $item->groupName;
			$insurance['groupName'] = '';
			$insurance['policyNumber'] = $item->groupNumber;
			//$insurance['ssn'] = $insuranceProgram->payerIdentifier;

			$insurances[] = array(
				'subscriber'=>$subscriber,
				'insurance'=>$insurance,
			);
			$guarantors[] = $subscriber;
		}

		$adt = ($newPatient)?'A04':'A08';
		$t = microtime();
		$x = explode(' ',$t);
		$date = date('YmdHis',$x[1]);
		$messageId = $date.str_replace('.','0',$x[0]);
		$id = 0;

		$hl7Guarantors = array();
		foreach ($guarantors as $key=>$guarantor) {
			$ctr = $key + 1;
			$hl7Guarantors[] = "GT1|{$ctr}|{$guarantor['recordNumber']}|{$guarantor['lastName']}^{$guarantor['firstName']}^{$guarantor['middleName']}^{$guarantor['suffix']}^^||{$guarantor['line1']}^{$guarantor['line2']}^{$guarantor['city']}^{$guarantor['state']}^{$guarantor['zip']}|{$guarantor['homePhone']}|{$guarantor['businessPhone']}|{$guarantor['dateOfBirth']}|{$guarantor['gender']}||S|{$guarantor['ssn']}|";
		}

		$hl7Insurances = array();
		foreach ($insurances as $key=>$value) {
			$ctr = $key + 1;
			$subscriber = $value['subscriber'];
			$insurance = $value['insurance'];
			$hl7Insurances[] = "IN1|{$ctr}|{$insurance['companyId']}^{$insurance['companyName']}|{$insurance['companyId']}|{$insurance['companyName']}|{$insurance['line1']}^{$insurance['line2']}^{$insurance['city']}^{$insurance['state']}^{$insurance['zip']}||{$insurance['phoneNumber']}|{$insurance['groupNumber']}|{$insurance['groupName']}|||||||{$subscriber['lastName']}^{$subscriber['firstName']}^{$subscriber['middleName']}^{$subscriber['suffix']}^^|{$subscriber['relation']}|{$subscriber['dateOfBirth']}|{$subscriber['line1']}^{$subscriber['line2']}^{$subscriber['city']}^{$subscriber['state']}^{$subscriber['zip']}|Y||{$ctr}||||||||||||||{$insurance['policyNumber']}|0||||||{$subscriber['gender']}||||||{$subscriber['ssn']}|";
			if (!strlen($subscriber['ssn']) > 0) continue;
			$hl7Insurances[] = "IN2|{$ctr}|{$subscriber['ssn']}";
		}

		$patient = $data['patient'];
		$provider = $data['provider'];
		$hl7Message = array();
		$hl7Message[] = "MSH|^~\&|MedMgr|989801|aroeshl7_prod|HEST|{$date}||ADT^{$adt}|{$messageId}|P|2.3|{$id}|";
		$hl7Message[] = "EVN|{$adt}|{$date}|||38|";
		$hl7Message[] = self::_PID($patient);
		$hl7Message[] = self::_PV1($provider);
		foreach ($hl7Guarantors as $hl7Guarantor) $hl7Message[] = $hl7Guarantor;
		foreach ($hl7Insurances as $hl7Insurance) $hl7Message[] = $hl7Insurance;

		$separator = "\r\n";
		return implode($separator,$hl7Message);
	}

	protected static function _getPatientData($patient,$includeProvider=true,$providerId=null,$roomId=null) {
		$maritalStatusMap = array(
			'SEPARATED'=>'A',
			'DIVORCED'=>'D',
			'MARRIED'=>'M',
			'SINGLE'=>'S',
			'WIDOWED'=>'W',
		);
		if (!$patient instanceof Patient) {
			$patientId = (int)$patient;
			$patient = new Patient();
			$patient->personId = $patientId;
			$patient->populate();
		}
		$personId = (int)$patient->personId;
		$person = $patient->person;

		$maritalStatus = $person->maritalStatus;
		if (isset($maritalStatusMap[$maritalStatus])) $maritalStatus = $maritalStatusMap[$maritalStatus];

		$ethnicities = array();
		$ethnicities['1'] = 1;
		$ethnicities['Hispanic/Latino'] = 1;
		$ethnicities['2'] = 2;
		$ethnicities['Not Hispanic/Latino'] = 2;
		$ethnicities['3'] = 3;
		$ethnicities['Unreported / Refused to Report'] = 3;

		$races = array();
		$races['A'] = 'A';
		$races['Asian'] = 'A';
		$races['N'] = 'N';
		$races['Native Hawaiian'] = 'N';
		$races['P'] = 'P';
		$races['Other Pacific Islander'] = 'P';
		$races['B'] = 'B';
		$races['Black / African American'] = 'B';
		$races['I'] = 'I';
		$races['American Indian / Alaska Native'] = 'I';
		$races['W'] = 'C';
		$races['White'] = 'C';
		$races['M'] = 'M';
		$races['More than one race'] = 'M';
		$races['E'] = 'E';
		$races['Unreported / Refused to Report'] = 'E';

		$statistics = PatientStatisticsDefinition::getPatientStatistics($personId);
		$race = '';
		if (isset($statistics['Race'])) $race = $statistics['Race'];
		if (isset($statistics['race'])) $race = $statistics['race'];
		$race = isset($races[$race])?$races[$race]:'E';

		$ethnicity = '';
		if (isset($statistics['Ethnicity'])) $ethnicity = $statistics['Ethnicity'];
		if (isset($statistics['ethnicity'])) $ethnicity = $statistics['ethnicity'];
		$ethnicity = isset($ethnicities[$ethnicity])?$ethnicities[$ethnicity]:'3';

		$language = '';
		if (isset($statistics['Language'])) $language = $statistics['Language'];
		if (isset($statistics['language'])) $language = $statistics['language'];
		$language = ''; // temporarily set to empty

		$patientData = array();
		$patientData['recordNumber'] = $patient->recordNumber;
		$patientData['lastName'] = $person->lastName;
		$patientData['firstName'] = $person->firstName;
		$patientData['middleName'] = $person->middleName;
		$patientData['suffix'] = $person->suffix;
		$patientData['dateOfBirth'] = date('Ymd',strtotime($person->dateOfBirth));
		$patientData['gender'] = $person->gender;
		$patientData['race'] = $race;
		$patientData['ethnicity'] = $ethnicity;

		$address = $person->address;
		$patientData['line1'] = $address->line1;
		$patientData['line2'] = $address->line2;
		$patientData['city'] = $address->city;
		$patientData['state'] = $address->state;
		$patientData['zip'] = $address->postalCode;

		$homePhone = '';
		$businessPhone = '';
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $personId;
		$phones = $phoneNumber->getPhoneNumbers(false);
		foreach ($phones as $phone) {
			if ($homePhone == '' && $phone['type'] == 'HP') $homePhone = $phone['number'];
			if ($businessPhone == '' && $phone['type'] == 'TE') $businessPhone = $phone['number'];
			if ($homePhone != '' && $businessPhone != '') break;
		}

		$patientData['homePhone'] = $homePhone;
		$patientData['businessPhone'] = $businessPhone;
		$patientData['language'] = $language;
		$patientData['maritalStatus'] = $maritalStatus;
		$patientData['accountNumber'] = '';
		$patientData['ssn'] = ($patient->person->identifierType == 'SSN')?$patient->identifier:'';
		if (!$includeProvider) return $patientData;

		$data = array();
		$data['patient'] = $patientData;

		if ($providerId === null) $providerId = (int)$patient->defaultProvider;
		$provider = new Provider();
		$provider->personId = $providerId;
		$provider->populate();
		$provider->populate();
		$providerData = array();
		$providerData['id'] = $provider->personId;
		$providerData['lastName'] = $provider->person->lastName;
		$providerData['firstName'] = $provider->person->firstName;
		$providerData['middleName'] = $provider->person->middleName;
		$providerData['suffix'] = $provider->person->suffix;

		$room = new Room();
		if ($roomId !== null && $roomId > 0) {
			$room->roomId = (int)$roomId;
			$room->populate();
			$building = $room->building;
			$practice = $building->practice;
			//trigger_error('room: '.$roomId.':'.$room->name);
		}
		else {
			$practice = new Practice();
			$building = new Building();
			$practiceId = (int)$patient->person->primaryPracticeId;
			$practice->practiceId = $practiceId;
			$practice->populate();
			//trigger_error('primary practice: '.$practiceId.':'.$practice->name);
		}
		$providerData['practice'] = $practice->name;
		$providerData['building'] = $building->name;
		$providerData['room'] = $room->name;

		$data['provider'] = $providerData;
		return $data;
	}

	public static function appointment($appointmentId) {
		$appointmentId = (int)$appointmentId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','COUNT(*) AS ctr')
				->where("objectClass = 'Appointment' AND objectId = ?",$appointmentId);
		$newAppointment = true;
		if (($row = $db->fetchRow($sqlSelect)) && $row['ctr'] > 1) $newAppointment = false;
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();

		$siu = 'S12';
		$statusCode = 1; // Default: Booked
		if (!$newAppointment) {
			// check appointment if cancel or no show
			$siu = 'S14';
			$appointmentCode = $appointment->appointmentCode;
			if ($appointmentCode == 'CAN') {
				$siu = 'S15';
				$statusCode = 2;
			}
			else if ($appointmentCode == 'NS') {
				$siu = 'S26';
				$statusCode = 3;
			}
		}

		$t = microtime();
		$x = explode(' ',$t);
		$date = date('YmdHis',$x[1]);
		$messageId = $date.str_replace('.','0',$x[0]);
		$id = 0;


		$fillerStatusCodes = array(
			'1'=>'BOOKED',
			'2'=>'CANCELLED',
			'3'=>'NO SHOW',
			'4'=>'COMPLETE',
			'5'=>'OVERBOOK',
			'6'=>'BLOCKED',
			'7'=>'DELETED',
			'8'=>'STARTED',
			'9'=>'PENDING',
			'10'=>'WAITLIST',
			'11'=>'DC',
		);

		$appointmentTemplate = new AppointmentTemplate();
		$reasons = $appointmentTemplate->appointmentReasons;
		$data = array();
		$data['appointmentId'] = $appointment->appointmentId;
		$data['appointmentReasonIdentifier'] = $appointment->reason;
		$data['appointmentReasonText'] = isset($reasons[$appointment->reason])?$reasons[$appointment->reason]['name']:'';
		$start = strtotime($appointment->start);
		$end = strtotime($appointment->end);
		$time = ($end - $start) / 60;
		$unit = 'minute';
		if ($time >= 60) {
			$time /= 60;
			$unit = 'hour';
		}
		if ($time > 1) $unit .= 's';
		$data['durationTime'] = $time;
		$data['durationUnit'] = $unit;
		$data['start'] = date('YmdHi',$start);
		$data['end'] = date('YmdHi',$end);
		$data['statusCode'] = isset($fillerStatusCodes[$statusCode])?$fillerStatusCodes[$statusCode]:$statusCode;
		$data['title'] = $appointment->title;

		$personId = (int)$appointment->patientId;
		$providerId = (int)$appointment->providerId;
		$roomId = (int)$appointment->roomId;
		$patientData = self::_getPatientData($personId,true,$providerId,$roomId); 
		$patient = $patientData['patient'];
		$provider = $patientData['provider'];
		// TODO: identify SCH 72 AND PV1 second to the last segment
		$data['unknownId'] = ''; // sample value = 72
		$hl7Message = array();
		$hl7Message[] = "MSH|^~\&|MedMgr|989801|aroeshl7_prod|HEST|{$date}||SIU^{$siu}|{$messageId}|P|2.3|{$id}|";
		$hl7Message[] = self::_SCH($data);
		$hl7Message[] = "NTE|1||{$data['title']}|";
		$hl7Message[] = "NTE|2||\"\"|";
		$hl7Message[] = self::_PID($patient);
		$hl7Message[] = self::_PV1($provider);
		$hl7Message[] = "RGS|1|A|";
		$hl7Message[] = self::_AIL($provider);
		$hl7Message[] = self::_AIP($provider);

		$separator = "\r\n";
		return implode($separator,$hl7Message);
	}

	protected static function _PID(Array $data) {
		$pid = array('PID');
		$pid[1] = '';
		$pid[2] = $data['recordNumber']; // MRN
		$pid[3] = '';
		$pid[4] = ''; // Chart Tag
		$pid[5] = "{$data['lastName']}^{$data['firstName']}^{$data['middleName']}^{$data['suffix']}"; // Last Name, First Name, Middle Name, Suffix
		$pid[6] = '';
		$pid[7] = $data['dateOfBirth']; // DOB
		$pid[8] = $data['gender']; // Gender
		$pid[9] = '';
		$pid[10] = $data['race']; // Race
		$pid[11] = "{$data['line1']}^{$data['line2']}^{$data['city']}^{$data['state']}^{$data['zip']}"; // Line1, Line2, City, State, Zip Code
		$pid[12] = '';
		$pid[13] = $data['homePhone']; // Home Phone 13.1
		$pid[14] = $data['businessPhone']; // Office Phone 14.1
		$pid[15] = $data['language']; // Language - added by CH
		$pid[16] = $data['maritalStatus']; // Marital Status
		$pid[17] = '';
		$pid[18] = $data['recordNumber'];
		$pid[19] = $data['ssn']; // SSN
		$pid[20] = '';
		$pid[21] = '';
		$pid[22] = $data['ethnicity']; // Ethnicity
		$pid[23] = '';
		$pid[24] = '';
		$pid[25] = '';
		$pid[26] = '';
		$pid[27] = '';
		$pid[28] = '';
		$pid[29] = '';
		$pid[30] = 'N';
		$pid[31] = '';
		return implode('|',$pid);
	}

	protected static function _PV1(Array $data) {
		$pv1 = array('PV1');
		$pv1[1] = '';
		$pv1[2] = '';
		//$pv1[3] = "{$provider['practiceId']}^{$provider['room']}^^^^^{$provider['building']}^^{$provider['practice']}";
		$pv1[3] = "{$data['practice']}^{$data['building']}^{$data['room']}";
		$pv1[4] = '';
		$pv1[5] = '';
		$pv1[6] = '';
		$pv1[7] = "{$data['id']}^{$data['lastName']}^{$data['firstName']}^{$data['middleName']}^{$data['suffix']}^^";
		$pv1[8] = '';
		$pv1[9] = '';
		$pv1[10] = '';
		$pv1[11] = '';
		$pv1[12] = '';
		$pv1[13] = '';
		$pv1[14] = '';
		$pv1[15] = '';
		$pv1[16] = '';
		$pv1[17] = '';
		$pv1[18] = '';
		$pv1[19] = '';
		$pv1[20] = '';
		$pv1[21] = '';
		$pv1[22] = '';
		$pv1[23] = '';
		$pv1[24] = '';
		$pv1[25] = '';
		$pv1[26] = '';
		$pv1[27] = '';
		$pv1[28] = '';
		$pv1[29] = '';
		$pv1[30] = '';
		$pv1[31] = '';
		$pv1[32] = '';
		$pv1[33] = '';
		$pv1[34] = '';
		$pv1[35] = '';
		$pv1[36] = '';
		$pv1[37] = '';
		$pv1[38] = '';
		$pv1[39] = '';
		$pv1[40] = '';
		$pv1[41] = '';
		$pv1[42] = '';
		$pv1[43] = '';
		$pv1[44] = '';
		$pv1[45] = '';
		$pv1[46] = '';
		$pv1[47] = '';
		return implode('|',$pv1);
	}

	protected static function _SCH(Array $data) {
		$sch = array('SCH');
		$sch[1] = $data['appointmentId'];
		$sch[2] = '';
		$sch[3] = '';
		$sch[4] = '';
		$sch[5] = '';
		$sch[6] = 'Normal';
		$sch[7] = "{$data['appointmentReasonIdentifier']}^{$data['appointmentReasonText']}";
		/*If SCH.7.0 <> -1 then
			the appointment reason is formatted using the SCH.7.1 and NTE.3 fields

			Example:
			SCH.7.1 ROUTINE VISIT
			NTE.3 3 MONTH F/U VE

			Means the reason would be this: 
			ROUTINE VISIT 3 MONTH F/U VE


		If SCH.7.0 = -1, then
			the reason is formatted to “No patients for SCH.9 SCH.10 [NTE.3]”

			This is to handle the case of a NO PATIENTs record where the reason should mention when the time is blocked off.  Example of what that would look like would be:
			SCH.7.1 = No Patients
			SCH.9 = 105
			SCH.10 = minutes
			NTE.3 = provider meeting

			No Patients for 105 minutes [provider meeting ]

			The brackets will be removed if NTE.3 is empty.*/

		$sch[8] = $data['appointmentReasonIdentifier'];
		$sch[9] = $data['durationTime'];
		$sch[10] = $data['durationUnit'];
		$sch[11] = "^^^{$data['start']}^{$data['end']}";
		$sch[12] = '';
		$sch[13] = '';
		$sch[14] = '';
		$sch[15] = '';
		$sch[16] = $data['unknownId'];
		$sch[17] = '';
		$sch[18] = '';
		$sch[19] = '';
		$sch[20] = '';
		$sch[21] = '';
		$sch[22] = '';
		$sch[23] = '';
		$sch[24] = '';
		$sch[25] = $data['statusCode'];
		$sch[26] = '';
		return implode('|',$sch);
	}

	protected static function _AIL(Array $data) {
		$ail = array('AIL');
		$ail[1] = '1';
		$ail[2] = '';
		//$ail[3] = "^{$provider['room']}^^^^^{$provider['building']}^^{$provider['practice']}";
		$ail[3] = "{$data['practice']}^{$data['building']}^{$data['room']}";
		/* AIL.3.1. This is the unique id for the hospital location.  The associated name is then in AIL.3.9.
		NOTE: Then we use this location EXCEPT for when the provider record is equal to one of these 4 cases.  The provider is:
			NURSE,RIVERSIDE (med mgr id 13), 
			NURSE,CLENDENIN (med mgr id 52), or 
			NURSE,SISSONVILLE (med mgr id 53), 
			NURSE, CABIN CREEK (med mgr 8)

		In those cases, the hospital location is automatically set to RIVERSIDE HEALTH CENTER, CLENDENIN HEALTH CENTER, SISSONVILLE HEALTH CENTER, and CABIN CREEK HEALTH CENTER respectively.*/
		$ail[4] = '';
		return implode('|',$ail);
	}

	protected static function _AIP(Array $data) {
		$aip = array('AIP');
		$aip[1] = '1';
		$aip[2] = '';
		$aip[3] = "{$data['id']}^{$data['lastName']}^{$data['firstName']}^{$data['middleName']}^{$data['suffix']}^^";
		$aip[4] = '';
		return implode('|',$aip);
	}

}
