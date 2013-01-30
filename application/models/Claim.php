<?php
/*****************************************************************************
*       Claim.php
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


class Claim extends WebVista_Model_ORM {

	protected $claimId;
	protected $_table = 'claims';
	protected $_primaryKeys = array('claimId');

	public static function balanceOperators() {
		return array(
			'='=>'equal',
			'>'=>'greater than',
			'>='=>'greater than or equal',
			'<'=>'less than',
			'<='=>'less than or equal',
			'between'=>'between'
		);
	}

	public static function listOptions() {
		return array(
			'healthcloud'=>'Send to HealthCloud',
			'download4010A1'=>'Download 4010A1',
			//'download5010'=>'Download 5010',
			'CMS1500PDF'=>'CMS1500 PDF',
			'CMS1450PDF'=>'CMS1450 PDF',
			'previewStatements'=>'Preview Statements',
			'publishStatements'=>'Publish Statements',
		);
	}

	public static function render4010A1(ClaimFile $claimFile,Array $claimIds=null) {
		if ($claimIds == null) $claimIds = explode(',',$claimFile->claimIds);
		$claimFileId = (int)$claimFile->claimFileId;
		$claim = array(
			'claimId'=>$claimFileId,
			//'date_of_acute_manifestation'=>date('Ymd'),
			//'date_of_similar_onset'=>date('Ymd'),
		);

		if (isset($claimIds[0])) {
			$claimId = (int)$claimIds[0];
			$claimLine = new ClaimLine();
			$claimLine->populateByClaimId($claimId);
			$visit = new Visit();
			$visit->visitId = (int)$claimLine->visitId;
			$visit->populate();
			$practiceId = (int)$visit->practiceId;
		}
		else $practiceId = (int)$claimFile->user->person->primaryPracticeId;

		$practice = new Practice();
		$practice->practiceId = $practiceId;
		$practice->populate();
		$senderId = $practice->practiceId;
		if (strlen($senderId) < 2) $senderId = str_pad($senderId,2,'0',STR_PAD_LEFT);
		$phoneNumber = PhoneNumber::autoFixNumber($practice->mainPhone->number);
		$phoneLen = strlen($phoneNumber);
		if ($phoneLen  < 10) $phoneNumber = str_pad($phoneNumber,10,'0',STR_PAD_LEFT);
		else if ($phoneLen > 10) $phoneNumber = substr($phoneNumber,-10);
		$practiceData = array(
			'senderId'=>$senderId,
			//'x12_version'=>'004010X098A1',
			'name'=>$practice->name,
			'phoneNumber'=>$phoneNumber,
		);

		$ISA = array();
		list($dateNow,$timeNow) = explode(' ',date('ymd Hi'));
		$ISA['dateNow'] = $dateNow;
		$ISA['timeNow'] = $timeNow;
		$ISA['claim'] = $claim;
		$ISA['practice'] = $practiceData;

		$GS = array();
		list($dateNow,$timeNow) = explode(' ',date('Ymd Hi'));
		$GS['dateNow'] = $dateNow;
		$GS['timeNow'] = $timeNow;
		$GS['practice'] = $practiceData;
		$GS['claim'] = $claim;
		$GS['payer'] = array( //  payer type of insurance admin
			'identifier_type'=>'46',
		);

		$HL = array();
		foreach ($claimIds as $claimId) {
			$HL[] = self::_generate4010A1($claimId,$claim);
		}
		$data = array();
		$data['HL'] = $HL;

		$arr = array();
		$arr['ISA'] = $ISA;
		$arr['GS'] = $GS;
		$arr['data'] = $data;

		$basePath = Zend_Registry::get('basePath');
		$template = $basePath.'application/templates/x12_al_ens_unitedhealthcare.xsl';
		$templateXSLT = file_get_contents($template);
		$data = explode('~',TemplateXSLT::render($arr,$templateXSLT));
		return str_replace('SEGMENT_CTR',(count($data)-5),implode("~\r\n",$data));
	}

	protected static function _generate4010A1($claimId,Array $claim) {
		static $ctr = 0;
		static $visits = array();
		static $practices = array();
		static $insurancePrograms = array();
		static $providers = array();
		static $patients = array();

		$claimId = (int)$claimId;
		$claimLine = new ClaimLine();
		$claimLine->populateByClaimId($claimId);

		$visitId = (int)$claimLine->visitId;
		if (!isset($visits[$visitId])) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();
			$visits[$visitId] = $visit;
		}
		$visit = $visits[$visitId];
		$patientId = (int)$visit->patientId;
		if (!isset($patients[$patientId])) {
			$patient = new Patient();
			$patient->personId = $patientId;
			$patient->populate();
			$patients[$patientId] = $patient;
		}
		$patient = $patients[$patientId];

		$practiceId = (int)$visit->practiceId;
		if (!isset($practices[$practiceId])) {
			$practice = new Practice();
			$practice->practiceId = $practiceId;
			$practice->populate();
			$practices[$practiceId] = $practice;
		}
		$practice = $practices[$practiceId];

		$insuranceProgramId = (int)$visit->activePayerId;
		if (!isset($insurancePrograms[$insuranceProgramId])) {
			$insurance = new InsuranceProgram();
			$insurance->insuranceProgramId = $insuranceProgramId;
			$insurance->populate();
			$insurancePrograms[$insuranceProgramId] = $insurance;
		}
		$insuranceProgram = $insurancePrograms[$insuranceProgramId];

		$providerId = (int)$visit->treatingPersonId;
		if (!isset($providers[$providerId])) {
			$provider = new Provider();
			$provider->personId = $providerId;
			$provider->populate();
			$providers[$providerId] = $provider;
		}
		$provider = $providers[$providerId];
		$billAs = (int)$provider->billAs;
		if ($billAs > 0) {
			$providerId = $billAs;
			if (!isset($providers[$providerId])) {
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$providers[$providerId] = $provider;
			}
			$provider = $providers[$providerId];
		}

		$subscribers = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(InsuranceProgram::INSURANCE_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		foreach ($enumerationClosure->getAllDescendants($enumeration->enumerationId,1,true) as $enum) {
			$rowset = $enumerationClosure->getAllDescendants($enum->enumerationId,1,true);
			if ($enum->key == InsuranceProgram::INSURANCE_SUBSCRIBER_ENUM_KEY) {
				foreach ($rowset as $row) {
					$subscribers[$row->key] = $row->name;
				}
				break;
			}
		}

		$insuredRelationship = new InsuredRelationship();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($insuredRelationship->_table)
				->where('insurance_program_id = ?',(int)$insuranceProgram->insuranceProgramId)
				->where('person_id = ?',(int)$patientId)
				->where('active = 1')
				->order('program_order')
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) $insuredRelationship->populateWithArray($row);

		$subs = $insuredRelationship->subscriber;
		$subscriberAddr = $subs->address;
		$relationship = null;
		$relationshipCode = $insuredRelationship->subscriberToPatientRelationship;
		if (isset($subscribers[$relationshipCode])) $relationship = $subscribers[$relationshipCode];
		if ($relationship === null) {
			$relationship = 'Self';
			$relationshipCode = 18;
			$subs = new Person();
			$subs->personId = $insuredRelationship->personId;
			$subs->populate();
		}
		$subscriber = array(
			'id'=>(int)$subs->personId,
			'relationship_code'=>$relationshipCode,
			'group_number'=>$insuredRelationship->groupNumber,
			'group_name'=>$insuredRelationship->groupName,
			'relationship'=>$relationship,
			'last_name'=>$subs->lastName,
			'first_name'=>$subs->firstName,
			'middle_name'=>$subs->middleName,
			'address'=>array(
				'line1'=>$subscriberAddr->line1,
				'line2'=>$subscriberAddr->line2,
				'city'=>$subscriberAddr->city,
				'state'=>$subscriberAddr->state,
				'zip'=>$subscriberAddr->zipCode,
			),
			'date_of_birth'=>date('Ymd',strtotime($subs->dateOfBirth)),
			'gender'=>$subs->gender,
			'contract_type_code'=>'',
			'contract_amount'=>'',
			'contract_percent'=>'',
			'contract_code'=>'',
			'contract_discount_percent'=>'',
			'contract_version'=>'',
		);
		$practiceAddr = $practice->primaryAddress;
		$room = new Room();
		$room->roomId = (int)$visit->roomId;
		$room->populate();
		$facility = $room->building;
		$phoneNumber = PhoneNumber::autoFixNumber($practice->mainPhone->number);
		$phoneLen = strlen($phoneNumber);
		if ($phoneLen  < 10) $phoneNumber = str_pad($phoneNumber,10,'0',STR_PAD_LEFT);
		else if ($phoneLen > 10) $phoneNumber = substr($phoneNumber,-10);
		$identifierType = '';
		$identifier = $practice->identifier;
		if (strlen($identifier) > 0) $identifierType = 'XX';//24';
		$data = array(
			'hlCount'=>++$ctr,
		);
		$data['practice'] = array(
			'name'=>$practice->name,
			'identifier_type'=>$identifierType,
			'identifier'=>$identifier,
			'address'=>array(
				'line1'=>$practiceAddr->line1, // str_replace('#','num','')
				'line2'=>$practiceAddr->line2, // str_replace('#','num','')
				'city'=>$practiceAddr->city,
				'state'=>$practiceAddr->state,
				'zip'=>$practiceAddr->zipCode,
			),
			'phoneNumber'=>$phoneNumber,// regex_replace:"/[^0-9]/":""
		);
		$data['treating_facility'] = array(
			'identifier'=>$facility->identifier,
		);
		$dateOfTreatment = date('Ymd',strtotime($visit->dateOfTreatment));

		$payer2Id = $insuranceProgram->insuranceProgramId;
		if (strlen($payer2Id) < 2) $payer2Id = str_pad($payer2Id,2,'0',STR_PAD_LEFT);
		$identifierType = '';
		$identifier = $provider->person->identifier;
		if (strlen($identifier) > 0) $identifierType = 'XX'; //34';
		$claimData = array(
			'claim'=>$claim,
			'patient'=>array(
				'date_of_initial_treatment'=>$dateOfTreatment,
				//'date_of_last_visit'=>'',
				'date_of_onset'=>$dateOfTreatment,
				//'date_of_accident'=>'',
				//'date_of_last_menstrual_period'=>'',
				//'date_of_last_xray'=>'',
				//'date_of_hearing_vision_prescription'=>'',
				//'date_of_disability_begin'=>'',
				//'date_of_last_work'=>'',
				//'date_auth_return_to_work'=>'',
				//'date_of_admission'=>'',
				//'date_of_discharge'=>'',
				//'date_of_assumed_care'=>'',
				'comment_type'=>'',
				'comment'=>'',
			),
			'treating_facility'=>array(
				'facility_code'=>$facility->facilityCodeId,
				'name'=>$facility->name,
				'address'=>array(
					'line1'=>$facility->line1,
					'line2'=>$facility->line2,
					'city'=>$facility->city,
					'state'=>$facility->state,
					'zip'=>$facility->zipCode,
				),
			),
			'provider'=>array(
				'signature_on_file'=>'Y',
				'accepts_assignment'=>'A',

				'last_name'=>$provider->person->lastName,
				'first_name'=>$provider->person->firstName,
				'identifier_type'=>$identifierType,
				'identifier'=>$identifier,
				'identifier_2'=>'',
			),
			'billing_facility'=>array(
				'clia_number'=>'',
			),
			'subscriber'=>$subscriber,
			'clearing_house'=>array(
				'credit_max_amount'=>'',
				'repricing_method'=>'',
				'allowed_amount'=>'',
				'savings_amount'=>'',
				'identifier'=>'',
				'rate'=>'',
				'apg_code'=>'',
				'apg_amount'=>'',
				'reject_code'=>'',
				'compliance_code'=>'',
				'exception_code'=>'',
			),
			'referring_provider'=>array(
				'last_name'=>'',
				'first_name'=>'',
				'referral_type'=>'',
				'identifier_type'=>'',
				'identifier'=>'',
				'taxonomy_code'=>'',
			),
			'supervising_provider'=>array(
				'last_name'=>'',
				'first_name'=>'',
				'identifier_type'=>'',
				'identifier'=>'',
			),
			'payer2'=>array(
				'id'=>$payer2Id,
				'name'=>$insuranceProgram->name,
			),
		);

		$clm = array();
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		foreach ($iterator as $row) {
			$baseFee = (float)$row->baseFee;
			$adjustedFee = (float)$row->adjustedFee;
			$paid = (float)$row->paid;
			$billed = $row->totalMiscCharge;
			if ($baseFee > 0) $billed += $baseFee - $adjustedFee;
			$balance = abs($billed) - $paid;
			$clm[] = array(
				'claim'=>$claimData['claim'],
				'patient'=>$claimData['patient'],
				'claim_line'=>array(
					'amount'=>$balance,
					'diagnosis1'=>preg_replace('/[\.]/','',$row->diagnosisCode1),
					'diagnosis2'=>preg_replace('/[\.]/','',$row->diagnosisCode2),
					'diagnosis3'=>preg_replace('/[\.]/','',$row->diagnosisCode3),
					'diagnosis4'=>preg_replace('/[\.]/','',$row->diagnosisCode4),
					'diagnosis5'=>preg_replace('/[\.]/','',$row->diagnosisCode5),
					'diagnosis6'=>preg_replace('/[\.]/','',$row->diagnosisCode6),
					'diagnosis7'=>preg_replace('/[\.]/','',$row->diagnosisCode7),
					'diagnosis8'=>preg_replace('/[\.]/','',$row->diagnosisCode8), // all diagnoses must remove dot (.) = preg_replace('/[\.]/','')
					'procedure'=>$row->procedureCode,
					'modifier1'=>$row->modifier1,
					'modifier2'=>$row->modifier2,
					'modifier3'=>$row->modifier3,
					'modifier4'=>$row->modifier4,
					'units'=>str_replace('.00','',$row->units),
					'date_of_treatment'=>$dateOfTreatment,
					'clia_number'=>'',
				),
				'treating_facility'=>$claimData['treating_facility'],
				'provider'=>$claimData['provider'],
				'billing_facility'=>$claimData['billing_facility'],
				'subscriber'=>$subscriber,
				'clearing_house'=>$claimData['clearing_house'],
				'referring_provider'=>$claimData['referring_provider'],
				'supervising_provider'=>$claimData['supervising_provider'],
				'payer2'=>$claimData['payer2'],
			);
		}

		$hl2 = array();
		$hl2[] = array(
			'hlCount'=>$ctr,
			'hlCount2'=>++$ctr,
			'payer'=>array(
				'responsibility'=>'P',
			),
			'subscriber'=>$subscriber,
			'patient'=>array(
				//'date_of_death'=>'00/00/0000',
				'weight'=>'',
				'last_name'=>$patient->lastName,
				'first_name'=>$patient->firstName,
			),
			'responsible_party'=>array(
				'last_name'=>'',
				'first_name'=>'',
				'address'=>array(
					'line1'=>'',
					'line2'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
				),
			),
			'CLM'=>$clm,
		);

		$data['HL2'] = $hl2;
		return $data;
	}

	public static function render($type,Array $claimIds) {
		if (!strlen($type) > 0) $type = '4010A1';
		$method = 'render'.$type;
		if (!method_exists(self,$methodName)) $method = 'render4010A1';
		return self::_render($claimIds);
	}

}
