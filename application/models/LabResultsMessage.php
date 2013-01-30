<?php
/*****************************************************************************
*       LabResultsMessage.php
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


class LabResultsMessage {

	protected static $patient = null;
	protected static $provider = null;
	protected static $providerBuilding = null;

	public static function generate($patient,$provider) {
		if (!$patient instanceof Patient) {
			$patientId = (int)$patient;
			$patient = new Patient();
			$patient->personId = $patientId;
			$patient->populate();
		}
		self::$patient = $patient;
		if (!$provider instanceof Provider) {
			$providerId = (int)$provider;
			$provider = new Provider();
			$provider->personId = $providerId;
			$provider->populate();
		}
		self::$provider = $provider;
		$building = Building::getBuildingDefaultLocation($provider->personId);
		self::$providerBuilding = $building;
		$data = array();
		$data[] = self::generateMSH(array('buildingName'=>$building->name));
		$data[] = self::generateSFT();
		$data[] = self::generatePID($patient);

		$order = new OrderLabTest();
		$iterator = $order->getIteratorByPersonId($patient->personId);
		foreach ($iterator as $orderLabTest) {
			$data[] = self::generateORC($orderLabTest->order->provider);
			$result = array();
			$result['orderLabTest'] = $orderLabTest;
			$loinc = new ProcedureCodesLOINC();
			$loinc->loincNum = $orderLabTest->labTest;
			$loinc->populate();
			$result['loinc'] = $loinc;
			$orderId = (int)$orderLabTest->orderId;
			$labTest = new LabTest();
			$labTest->populateByLabOrderId($orderId);
			$result['labTest'] = $labTest;
			$data[] = self::generateOBR($result);
			$labsIterator = new LabsIterator();
			$labsIterator->setFilters(array('orderId'=>$orderId));
			foreach ($labsIterator as $row) {
				$result['result'] = $row;
				$data[] = self::generateOBX($result);
			}
			$data[] = self::generateSPM($orderLabTest->collectionSample);
		}
		return implode("\n",$data);
	}

	public static function generateMSH(Array $data) {
		return 'MSH|^~\&|ClearHealth^2.16.840.1.113883.3.72.7.1^HL7|'.$data['buildingName'].'^2.16.840.1.113883.3.72.7.2^HL7|PH Application^2.16.840.1.113883.3.72.7.3^HL7|PH Facility^2.16.840.1.113883.3.72.7.4^HL7|20101105150246||ORU^R01^ORU_R01|NIST-101105150245914|P|2.5.1|||||||||PHLabReport-Ack^^2.16.840.1.114222.4.10.3^ISO';
	}

	public static function generateSFT() {
		return 'SFT|ClearHealth, Inc.|3.2.0|ClearHealth|6742873-12||20101103';
	}

	public static function generatePID($patient) {
		if (!$patient instanceof Patient) {
			$patientId = (int)$patient;
			$patient = new Patient();
			$patient->personId = (int)$patientId;
			$patient->populate();
		}
		$patientId = (int)$patient->personId;
		$person = $patient->person;
		$statistics = PatientStatisticsDefinition::getPatientStatistics($patientId);
		$raceCode = '';
		$race = 'Unknown';
		if (isset($statistics['Race'])) $race = $statistics['Race'];
		if (isset($statistics['race'])) $race = $statistics['race'];
		if (strlen($statistics['Race']) > 0) {
			$race = $statistics['Race'];
			foreach (PatientStatisticsDefinition::listRaceCodes() as $key=>$value) {
				if (strtolower($value) == strtolower($race)) {
					$raceCode = $key;
					break;
				}
			}
		}
		$addr = new Address();
		foreach ($addr->getIteratorByPersonId($patient->personId) as $address) {
			break;
		}
		$phoneHome = '';
		$phoneBusiness = '';
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $patient->personId;
		foreach ($phoneNumber->phoneNumbers as $phone) {
			if ($phoneHome == '' && $phone['type'] == 'HP') {
				$phoneHome = $phone['number'];
			}
			if ($phoneBusiness == '' && $phone['type'] == 'TE') {
				$phoneBusiness = $phone['number'];
			}
		}
		if ($phoneHome) $phone = $phoneHome;
		if ($phoneBusiness) $phone = $phoneBusiness;
		if (is_array($phone)) $phone = $phone['number'];
		if (substr($phone,0,1) == 1) $phone = substr($phone,1);
		$areaCode = substr($phone,0,3);
		$localNumber = substr($phone,3);

		$ethnic = 'Unknown';
		if (isset($statistics['Ethnicity'])) $ethnic = $statistics['Ethnicity'];
		if (isset($statistics['ethnicity'])) $ethnic = $statistics['ethnicity'];
		$ethnicId = strtoupper(substr($ethnic,0,1));
		if ($ethnicId != 'H' && $ethnicId != 'N' && $ethnicId != 'U') $ethnicId = 'U';
		return 'PID|||'.$patient->recordNumber.'^^^MPI&2.16.840.1.113883.19.3.2.1&ISO^MR||'.$person->lastName.'^'.$person->firstName.'||'.date('Ymd',strtotime($person->dateOfBirth)).'|'.$person->gender.'||'.$raceCode.'^'.$race.'^HL70005|'.$address->line1.'^^'.$address->city.'^'.$address->state.'^'.$address->zipCode.'^USA^M||^PRN^^^^'.$areaCode.'^'.$localNumber.'|||||||||'.$ethnicId.'^'.$ethnic.'^HL70189';
	}

	public static function generateORC($provider) {
		// test#5 does not have this segment
		if (!$provider instanceof Provider) {
			$providerId = (int)$provider;
			$provider = new Provider();
			$provider->personId = (int)$providerId;
			$provider->populate();
		}
		$building = Building::getBuildingDefaultLocation($provider->personId);
		//$practice = self::$providerBuilding->practice;
		$practice = $building->practice;
		$practiceAddr = $practice->primaryAddress;
		if (!$practiceAddr->addressId > 0) $practiceAddr = $practice->secondaryAddress;
		$practicePhone = $practice->mainPhone;
		if (!$practicePhone->numberId > 0) $practicePhone = $practice->secondaryPhone;
		$phone = PhoneNumber::autoFixNumber($practicePhone->number);
		if (substr($phone,0,1) == 1) $phone = substr($phone,1);
		$areaCode = substr($phone,0,3);
		$localNumber = substr($phone,3);
		$addr = new Address();
		foreach ($addr->getIteratorByPersonId($provider->personId) as $providerAddr) {
			break;
		}
		if (!isset($providerAddr)) $providerAddr = new Address();
		$orc = 'ORC|RE|||||||||||';
		if ($provider->personId > 0) {
			$providerId = (strlen($provider->identifier) > 0)?$provider->identifier:$provider->personId;
			$orc .= $providerId.'^'.$provider->person->lastName.'^'.$provider->person->firstName.'^^^^^^'.$building->name.'&2.16.840.1.113883.19.4.6&ISO';
		}
		$orc .= '|||||||||'.$practice->name.'^L^^^^'.$building->name.'&2.16.840.1.113883.19.4.6&ISO^XX^^^'.$practice->identifier.'|'.$practiceAddr->line1.'^^'.$practiceAddr->city.'^'.$practiceAddr->state.'^'.$practiceAddr->zipCode.'^^B|^^^^^'.$areaCode.'^'.$localNumber;
		if ($providerAddr->addressId > 0) {
			$orc .= '|'.$providerAddr->line1.'^^'.$providerAddr->city.'^'.$providerAddr->state.'^'.$providerAddr->zipCode.'^^B';
		}
		return $orc;
	}

	public static function generateOBR(Array $data) {
		static $obrCtr = 1;
		$orderLabTest = $data['orderLabTest'];
		$labTest = $data['labTest'];
		$loinc = $data['loinc'];
		$obr = 'OBR|'.$obrCtr++.'||'.$labTest->filerOrderNum.'^Lab^2.16.840.1.113883.19.3.1.6^ISO|';
		$altIdentifier = '3456543';
		$obr .= $loinc->loincNum.'^'.$loinc->shortname.'^LN^'.$altIdentifier.'^'.$labTest->service.'^99USI|||';
		// concatenated diagnoses ,
		$diagnoses = array();
		$iterator = new PatientDiagnosisIterator();
		$iterator->setFilters(array('patientId'=>(int)$orderLabTest->order->patientId));
		foreach ($iterator as $pd) {
			$diagnoses[$pd->code] = $pd->diagnosis;
		}
		$relevantClinicalInfo = implode(',',$diagnoses);
		$obr .= date('YmdHiO',strtotime($labTest->observationTime)).'||||||'.$relevantClinicalInfo.'|||';
		$provider = $orderLabTest->order->provider;
		$providerId = (strlen($provider->identifier) > 0)?$provider->identifier:$provider->personId;
		$building = Building::getBuildingDefaultLocation($provider->personId);
		if ($provider->personId > 0) {
			$obr .= $providerId.'^'.$provider->person->lastName.'^'.$provider->person->firstName.'^^^^^^'.$building->name.'&2.16.840.1.113883.19.4.6&ISO';
		}
		$obr .= '||||||'.date('YmdHiO',strtotime($labTest->reportTime)).'|||'.$labTest->status.'||||||';

		$reasonForStudy = array();
		foreach ($diagnoses as $code=>$description) {
			$codeSystem = (strpos($code,'.') === false)?'SCT':'I9CDX';
			$reasonForStudy[] = $code.'^'.$description.'^'.$codeSystem;
		}
		$obr .= implode('~',$reasonForStudy);
		return $obr;
	}

	public static function generateOBX(Array $data) {
		static $obxCtr = 1;
		$result = $data['result'];
		$orderLabTest = $data['orderLabTest'];
		$labTest = $data['labTest'];
		$loinc = $data['loinc'];
		$valueType = is_numeric($result->value)?'NM':'ST';
		$obx = 'OBX|'.$obxCtr++.'|'.$valueType.'|'.$loinc->loincNum.'^'.$loinc->shortname.'^LN|1|';
		$obx .= $result->value.'|';

		$UCUM = array(
			'ug/dl'=>'micro-gram per deci-liter',
			'iu/ml'=>'international units per mililiter',
		);
		$units = $result->units;
		$unit = strtolower($units);
		if (isset($UCUM[$unit])) $obx .= $units.'^'.$UCUM[$unit].'^UCUM';
		$obx .= '|'.$result->referenceRange.'|'.$result->abnormalFlag.'|||';
		//$obx .= $result->resultStatus.'|||'.date('YmdHiO',strtotime($result->observationTime)).'|||||'.date('YmdHiO',strtotime($result->observationTime)).'||||Lab^L^^^^CLIA&2.16.840.1.113883.19.4.6&ISO^XX^^^1236|3434 Industrial Lane^^Ann Arbor^MI^48103^^B';
		$obx .= $result->resultStatus.'|||'.date('YmdHiO',strtotime($result->observationTime)).'|||||'.date('YmdHiO',strtotime($result->observationTime));
		$performingOrg = $result->cliaPerformingOrg;
		if (strlen($performingOrg) > 0) $obx .= '||||'.$result->cliaPerformingOrg;
		return $obx;
	}

	public static function generateSPM($code,$description='') {
		$specimens = array(
			'BLDC'=>array(
				'identifier'=>'122554006',
				'text' => 'Capillary blood specimen',
				'nameCodingSystem'=>'SCT', // HL70396
				'altIdentifier'=>'BLDC',
				'altText'=>'Blood capillary',
				'nameAltCodingSystem'=>'HL70070', // HL70396
				'codingSystemVersion'=>'20080131',
			),
			'BLDV'=>array(
				'identifier'=>'122555007',
				'text' => 'Venous blood specimen',
				'nameCodingSystem'=>'SCT', // HL70396
				'altIdentifier'=>'BLDV',
				'altText'=>'Blood venous',
				'nameAltCodingSystem'=>'HL70070', // HL70396
				'codingSystemVersion'=>'20080131',
			),
			'STL'=>array(
				'identifier'=>'119339001',
				'text' => 'Stool specimen',
				'nameCodingSystem'=>'SCT', // HL70396
				'altIdentifier'=>'STL',
				'altText'=>'Stool',
				'nameAltCodingSystem'=>'HL70070', // HL70396
				'codingSystemVersion'=>'20080131',
			),
		);
		$code = strtoupper($code);
		if (!isset($specimens[$code])) return;
		$specimen = $specimens[$code];
		return 'SPM||||'.$specimen['identifier'].'^'.$specimen['text'].'^'.$specimen['nameCodingSystem'].'^'.$specimen['altIdentifier'].'^'.$specimen['altText'].'^'.$specimen['nameAltCodingSystem'].'^'.$specimen['codingSystemVersion'].'^2.5.1';
	}

}
