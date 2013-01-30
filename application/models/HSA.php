<?php

class HSA {

	public static function generalPractice(HealthStatusHandler $handler,$patientId,Audit $audit) {
		$patientId = (int)$patientId;
		if (!$patientId > 0) {
			trigger_error('Empty patientId');
			return;
		}
		if (!$handler->timeframe) $handler->timeframe = date('Y-m-d');
		self::_checkDiabetes($handler,$patientId,$audit);
		self::_checkAsthma($handler,$patientId,$audit);
		self::_checkHypertension($handler,$patientId,$audit);
		self::_checkLabLDL($handler,$patientId,$audit);
		self::_checkMedication($handler,$patientId,$audit);
	}

	protected static function createHSA(HealthStatusHandler $handler,$patientId,$message) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		// create an alert
		$healthStatusAlert = new HealthStatusAlert();
		$sqlSelect = $db->select()
				->from($healthStatusAlert->_table)
				->where('personId = ?',(int)$patientId)
				->where("status = 'active'")
				->where('message LIKE ?','%'.(string)$message.'%')
				->where('healthStatusHandlerId = ?',(int)$handler->healthStatusHandlerId)
				->limit(1);
		if ($db->fetchRow($sqlSelect)) return $ret;

		$healthStatusAlert->message = $message;
		$healthStatusAlert->status = 'active';
		$healthStatusAlert->personId = $patientId;
		$healthStatusAlert->healthStatusHandlerId = $handler->healthStatusHandlerId;
		$healthStatusAlert->dateDue = date('Y-m-d H:i:s',strtotime($handler->timeframe));
		$healthStatusAlert->persist();
		return true;
	}

	protected static function _checkDiabetes(HealthStatusHandler $handler,$patientId,Audit $audit) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;

		$diabetesCodes = array(
			// ICD9
			'250', '250.0', '250.00', '250.01', '250.02', '250.03', '250.10',
			'250.11', '250.12', '250.13', '250.20', '250.21', '250.22', '250.23',
			'250.30', '250.31', '250.32', '250.33', '250.4', '250.40', '250.41',
			'250.42', '250.43', '250.50', '250.51', '250.52', '250.53', '250.60',
			'250.61', '250.62', '250.63', '250.7', '250.70', '250.71', '250.72',
			'250.73', '250.8', '250.80', '250.81', '250.82', '250.83', '250.9',
			'250.90', '250.91', '250.92', '250.93', '357.2', '362.0', '362.01',
			'362.02', '362.03', '362.04', '362.05', '362.06', '362.07', '366.41',
			'648.0', '648.00', '648.01', '648.02', '648.03', '648.04', 
			// SNOMED-CT
			'111552007', '111558006', '11530004', '123763000', '127013003', '127014009', '190321005',
			'190328004', '190330002', '190331003', '190336008', '190353001', '190361006', '190368000',
			'190369008', '190371008', '190372001', '190383005', '190389009', '190390000', '190392008',
			'190406000', '190407009', '190410002', '190411003', '190412005', '190416001', '190417004',
			'190418009', '190419001', '190422004', '193184006', '197605007', '198609003', '199223000',
			'199227004', '199229001', '199230006', '199231005', '199234002', '201250006', '201251005',
			'201252003', '23045005', '230572002', '230577008', '237599002', '237600004', '237601000',
			'237604008', '237613005', '237618001', '237619009', '237627000', '25907005', '26298008',
			'267379000', '267380002', '2751001', '275918005', '28032008', '28453007', '290002008',
			'309426007', '310387003', '311366001', '312912001', '313435000', '313436004', '314537004',
			'314771006', '314772004', '314893005', '314902007', '314903002', '33559001', '34140002',
			'359611005', '359638003', '359642000', '360546002', '371087003', '38542009', '39058009',
			'39181008', '408539000', '408540003', '413183008', '414890007', '414906009', '420414003',
			'420422005', '421750000', '421847006', '421895002', '422183001', '422228004', '422275004',
			'423263001', '424736006', '424989000', '425159004', '425442003', '426705001', '426875007',
			'427089005', '428896009', '42954008', '44054006', '4627003', '46635009', '50620007',
			'51002006', '5368009', '54181000', '57886004', '59079001', '5969009', '70694009',
			'73211009', '74263009', '75524006', '75682002', '76751001', '81531005', '81830002',
			'8801005', '91352004', '9859006', 
		);
		$diabetestCodeList = self::_formatCodeList($diabetesCodes);

		// check for diabetes diagnosis codes OR patient is on avandia OR fasting blood sugar >= 128 mg/ml
		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$diabetestCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$diabetestCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diabetestCodeList['generic'])."))",
			),
			array(
				'join'=>'INNER JOIN medications ON medications.personId = patient.person_id',
				'where'=>"medications.description LIKE '%avandia%'",
			),
			array( // 14771-0 = fasting blood
				'join'=>'INNER JOIN orders ON orders.patientId = patient.person_id
					INNER JOIN orderLabTests ON orderLabTests.orderId = orders.orderId
					INNER JOIN lab_test ON lab_test.lab_order_id = orders.orderId
					INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id',
				'where'=>"orderLabTests.labTest = '14771-0' AND lab_result.value >= 128",
			),
		);
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				{$lookup['join']}
				WHERE patient.person_id = {$patientId} AND {$lookup['where']}
				LIMIT 1";
			//trigger_error($sql);
			if ($row = $db->fetchRow($sql)) { // has diabetes
				// check if patient don't have "Diabetes Education: Control Your Blood Sugar"
				$sql = "SELECT `code`
					FROM patientEducations
					WHERE patientId = {$patientId} AND code = 'CYBS'
					LIMIT 1";
				if (!$db->fetchRow($sql)) {
					// create an alert
					$message = 'Patient Conditions may warrant education: Diabetes Education: Controlling Your Blood Sugar';
					self::createHSA($handler,$patientId,$message);
					$ret = true;
				}
				break;
			}
		}
		return $ret;
	}

	protected static function _checkAsthma(HealthStatusHandler $handler,$patientId) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;

		$asthmaCodes = array(
			// ICD9
			'493', '493.0', '493.00', '493.01', '493.02', '493.1', '493.10',
			'493.11', '493.12', '493.2', '493.20', '493.21', '493.22', '493.8',
			'493.81', '493.82', '493.9', '493.90', '493.91', '493.92',
			// SNOMED
			'11641008', '12428000', '13151001', '195949008', '195967001',
			'195977004', '195979001', '196013003', '225057002', '233672007',
			'233678006', '233679003', '233681001', '233683003', '233685005',
			'233688007', '266361008', '266364000', '281239006', '30352005',
			'304527002', '31387002', '370218001', '370219009', '370220003',
			'370221004', '389145006', '405944004', '407674008', '409663006',
			'423889005', '424199006', '424643009', '425969006', '426656000',
			'426979002', '427295004', '427354000', '427603009', '427679007',
			'442025000', '55570000', '56968009', '57546000', '59327009',
			'59786004', '63088003', '67415000', '85761009', '91340006',
			'92807009', '93432008',
		);
		$asthmaCodeList = self::_formatCodeList($asthmaCodes);

		// check for asthma diagnosis codes OR patient is on azmacort OR sputum culture = no organisms present
		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$asthmaCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$asthmaCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$asthmaCodeList['generic'])."))",
			),
			array(
				'join'=>'INNER JOIN medications ON medications.personId = patient.person_id',
				'where'=>"medications.description LIKE '%azmacort%'",
			),
			array(
				'join'=>'INNER JOIN orders ON orders.patientId = patient.person_id
					INNER JOIN orderLabTests ON orderLabTests.orderId = orders.orderId
					INNER JOIN lab_test ON lab_test.lab_order_id = orders.orderId
					INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id',
				'where'=>"orderLabTests.labTest IN ('52973-5','592-6','6409-7','6602-7') AND lab_result.value = 'no organisms present'",
			),
		);
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				{$lookup['join']}
				WHERE patient.person_id = {$patientId} AND {$lookup['where']}
				LIMIT 1";
			//trigger_error($sql);
			if ($row = $db->fetchRow($sql)) { // has diabetes
				// check if patient don't have "Asthma Education: Learn to manage Asthma"
				$sql = "SELECT `code`
					FROM patientEducations
					WHERE patientId = {$patientId} AND code = 'LTMA'
					LIMIT 1";
				if (!$db->fetchRow($sql)) {
					// create alert
					$message = 'Patient conditions may warrant education: Asthma Education: Learn to manage Asthma';
					self::createHSA($handler,$patientId,$message);
					$ret = true;
				}
				break;
			}
		}
		return $ret;
	}

	public static function patientEducation(HealthStatusHandler $handler,PatientEducation $edu,Audit $audit) {
		$db = Zend_Registry::get('dbAdapter');
		list($code,$patientId) = explode(';',$audit->objectId);
		$alert = new HealthStatusAlert();
		$sqlSelect = $db->select()
				->from($alert->_table)
				->where('personId = ?',$patientId)
				->where("status = 'active'");
		if ($code = 'CYBS') {
			$sqlSelect->where('message LIKE ?','%Diabetes Education: Controlling Your Blood Sugar%');
		}
		else if ($code = 'LTMA') {
			$sqlSelect->where('message LIKE ?','%Asthma Education: Learn to manage Asthma%');
		}
		else {
			return;
		}

		$iterator = $alert->getIterator($sqlSelect);
		foreach ($iterator as $hsa) {
			$hsa->status = 'fulfilled';
			$hsa->persist();
		}
	}

	protected static function _formatCodeList(Array $codes) {
		$genericCodeList = array();
		$codeList = array();
		foreach ($codes as $code) {
			$genericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$codeList[] = "'$code'";
		}
		return array('generic'=>$genericCodeList,'code'=>$codeList);
	}

	protected static function _checkHypertension(HealthStatusHandler $handler,$patientId,$audit) {
		$ret = false;
		if (!($audit->objectClass == 'PatientDiagnosis' || $audit->objectClass == 'ProblemList' || $audit->objectClass == 'ClinicalNotes')) return $ret;

		$db = Zend_Registry::get('dbAdapter');

		$hypertensionCodes = array(
			// ICD9
			'401.0', '401.1', '401.9', '402.00', '402.01', '402.10', '402.11',
			'402.90', '402.91', '403.00', '403.01', '403.10', '403.11', '403.90',
			'403.91', '404.00', '404.01', '404.02', '404.03', '404.10', '404.11',
			'404.12', '404.13', '404.90', '404.91', '404.92', '404.93',
		);
		$hypertensionCodeList = self::_formatCodeList($hypertensionCodes);

		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$hypertensionCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$hypertensionCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$hypertensionCodeList['generic'])."))",
			),
		);
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				{$lookup['join']}
				WHERE patient.person_id = {$patientId} AND {$lookup['where']}
				LIMIT 1";
			//trigger_error($sql);
			if ($row = $db->fetchRow($sql)) { // hypertension diagnosed
				// create an alert
				$message = 'Patient may be suitable for treatment of hypertension with a diuretic such as Hydrochlorothiazide';
				self::createHSA($handler,$patientId,$message);
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	protected static function _checkLabLDL(HealthStatusHandler $handler,$patientId,$audit) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$diagnosisCodes = array(
			// ICD9
			'272.0',
		);
		$diagnosisCodeList = self::_formatCodeList($diagnosisCodes);

		// if patient has LDL result > 160 AND/OR patient is diagnosed with 272.0 "Patient may benefit from hypercholesteremia drug such as Lipitor"
		$lookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$diagnosisCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$diagnosisCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagnosisCodeList['generic'])."))",
			),
			array(
				'join'=>'INNER JOIN orders ON orders.patientId = patient.person_id
					INNER JOIN orderLabTests ON orderLabTests.orderId = orders.orderId
					INNER JOIN lab_test ON lab_test.lab_order_id = orders.orderId
					INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id',
				'where'=>"lab_result.description LIKE '%LDL%' AND lab_result.value > 160",
			),
		);
		foreach ($lookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				{$lookup['join']}
				WHERE patient.person_id = {$patientId} AND {$lookup['where']}
				LIMIT 1";
			//trigger_error($sql);
			if ($row = $db->fetchRow($sql)) { // hypertension diagnosed
				// create an alert
				$message = 'Patient may benefit from hypercholesteremia drug such as Lipitor';
				self::createHSA($handler,$patientId,$message);
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	protected static function _checkMedication(HealthStatusHandler $handler,$patientId,$audit) {
		$ret = false;
		if ($audit->objectClass != 'Medication') return $ret;
		$db = Zend_Registry::get('dbAdapter');

		$medication = new Medication();
		$medication->medicationId = (int)$audit->objectId;
		$medication->populate();
		$s = $medication->description;
		if (preg_match('/avandia/i',$s)) {
			$message = 'Patient may be suitable for referral to Diabetes Counseling';
		}
		else if (preg_match('/hydrochlorothiazide/i',$s)) {
			$message = 'Patient may be suitable for treatment of hypertension with a diuretic such as Hydrochlorothiazide';
		}
		else if (preg_match('/lipitor/i',$s)) {
			$message = 'Patient may benefit from hypercholesteremia drug such as Lipitor';
			$sqlSelect = $db->select()
					->from('chmed.basemed24',array('md5','vaclass'))
					->where('pkey = ?',$medication->pkey);
			$hasAllergy = false;
			if ($row = $db->fetchRow($sqlSelect)) {
				$personId = (int)$patientId;
				$md5 = $row['md5'];
				$vaclass = $row['vaclass'];
				// check for allergy interactions
				do {
					// regular allergies search
					$interactionIterator = new BaseMed24InteractionIterator();
					$interactionIterator->setFilters(array('personId'=>$personId,'md5'=>$md5));
					$regularAllergies = $interactionIterator->toJsonArray('hipaa_ndc',array('tradename','fda_drugname','notice'));
					$tmpArray = $regularAllergies;
					$regularAllergies = array();
					foreach ($tmpArray as $key=>$value) {
						$hasAllergy = true;
						break 2;
					}

					// drug class search
					$patientAllergyIterator = new PatientAllergyIterator();
					$patientAllergyIterator->setFilters(array('patientId'=>$personId,'enteredInError'=>0,'drugAllergy'=>$vaclass,'reactionType'=>'Drug Class Allergy'));
					$drugClassAllergies = array();
					foreach($patientAllergyIterator as $allergy) {
						$hasAllergy = true;
						break 2;
					}

					// specific drug search
					$patientAllergyIterator->setFilters(array('patientId'=>$personId,'enteredInError'=>0,'drugAllergy'=>$md5,'reactionType'=>'Specific Drug Allergy'));
					$specificDrugAllergies = array();
					foreach($patientAllergyIterator as $allergy) {
						$hasAllergy = true;
						break 2;
					}
				} while(false);
			}
			if ($hasAllergy) $message .= '. NOTE: Consult patients allergies: Lipitor/Atorvastatin';
		}
		else {
			return $ret;
		}
		self::createHSA($handler,$patientId,$message);
		return true;
	}

}

