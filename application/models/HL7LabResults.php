<?php
/*****************************************************************************
*       HL7LabResults.php
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


require_once 'HL7.php';

class HL7LabResults extends HL7 {

	static $loincodes = array();

	public static function generate($patientId) { // v2.5.1
		$patientId = (int)$patientId;
		$ret = array();
		$msh = array();
		$msh['messageType'] = array('code'=>'ORU','eventType'=>'R01','structure'=>'ORU_R01');
		$ret[] = self::generateMSH($msh); // MSH
		$ret[] = self::generateSFT(); // SFT
		$ret[] = self::generatePID($patientId); // PID

		$filters = array('patientId'=>$patientId);
		$labResults = array();
		$labTests = array();
		$labOrderTests = array();
		$labsIterator = new LabsIterator();
		$labsIterator->setFilters($filters);
		foreach ($labsIterator as $lab) {
			// get the lab order
			$labTestId = (int)$lab->labTestId;
			if (!isset($labTests[$labTestId])) {
				$labTest = new LabTest();
				$labTest->labTestId = (int)$lab->labTestId;
				$labTest->populate();
				$labTests[$labTestId] = $labTest;
			}
			$labTest = $labTests[$labTestId];
			$orderId = (int)$labTest->labOrderId;
			if (!isset($labOrderTests[$orderId])) {
				$orderLabTest = new OrderLabTest();
				$orderLabTest->orderId = $orderId;
				$orderLabTest->populate();
				$labOrderTests[$orderId] = $orderLabTest;
			}
			$orderLabTest = $labOrderTests[$orderId];
			if (!isset($labResults[$orderId])) {
				$labResults[$orderId] = array();
				$labResults[$orderId]['results'] = array();
				$labResults[$orderId]['labTest'] = $labTest;
				$labResults[$orderId]['orderLabTest'] = $orderLabTest;
				$providerId = (int)$orderLabTest->order->providerId;
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$labResults[$orderId]['provider'] = $provider;
			}
			$labResults[$orderId]['results'][] = $lab;
		}

		foreach ($base->labResults as $orderId=>$value) {
			$ret[] = self::generateORC($value); // ORC
			$ret[] = self::generateOBR($value); // OBR
			foreach ($value['results'] as $result) {
				$data = array(
					'result'=>$result,
					'labTest'=>$value['labTest'],
					'orderLabTest'=>$value['orderLabTest'],
					'provider'=>$value['provider'],
				);
				$ret[] = self::generateOBX($data); // OBX
			}
			$ret[] = self::generateSPM($value); // SPM
		}

		/*$iterator = new LabsIterator();
		$iterator->setFilters(array('patientId'=>$patientId));
		foreach ($iterator as $lab) {
			$data = array();
			$data['lab'] = $lab;
			$orderLabTest = new OrderLabTest();
			$orderLabTest->orderId = $lab->labTest->labOrderId;
			$orderLabTest->populate();
			$data['orderLabTest'] = $orderLabTest;
			$loinc = new ProcedureCodesLOINC();
			$loinc->loincNum = $orderLabTest->labTest;
			$loinc->populate();
			$data['loinc'] = $loinc;
			$ret[] = self::generateOBR($data); // OBR
			$ret[] = self::generateOBX($data); // OBX
			$ret[] = self::generateSPM($data); // SPM
		}*/
		return implode("\r\n",$ret);
	}

	public static function generateMSH(Array $data,$raw=false) {
		$msh = parent::generateMSH($data,true);
		$msh[13] = ''; // empty
		$msh[14] = ''; // empty
		$msh[15] = ''; // empty
		$msh[16] = ''; // empty
		$msh[17] = ''; // empty
		$msh[18] = ''; // empty
		$msh[19] = ''; // empty
		$msh[20] = ''; // empty
		$msh21 = array(); // MSH‐21: Message Profile Identifier
		$msh21[] = 'PHLabReport-Ack'; // MSH‐21.1: Entity Identifier fixed PHLabReport-Ack::PHLabReport-NoAck
		$msh21[] = ''; // empty
		$msh21[] = '2.16.840.1.114222.4.10.3'; // MSH‐21.3: Universal ID
		$msh21[] = 'ISO'; // MSH‐21.4: Universal ID Type
		$msh[21] = implode('^',$msh21);
		if ($raw) return $msh;
		return implode('|',$msh);
	}

	public static function generateSFT(Array $data=array(),$raw=false) {
		$sft = array('SFT');
		$sft1 = array(); // SFT‐1: Software Vendor Organization
		$sft1[] = 'NIST Lab, Inc.'; // 'ClearHealth Inc.'; // SFT‐1.1: Organization Name
		$sft[1] = implode('^',$sft1);
		$sft[2] = '3.6.23'; // '3.1'; // SFT‐2: Software Certified Version or Release Number
		$sft[3] = 'A-1 Lab System'; // 'ClearHealth'; // SFT‐3: Software Product Name
		$sft[4] = '6742873-12'; // SFT‐4: Software Binary ID
		$sft[5] = ''; // empty
		$sft[6] = '20080303'; // '20101212'; // SFT‐6: Software Install Date
		if ($raw) return $sft;
		return implode('|',$sft);
	}

	public static function generateORC(Array $data=array(),$raw=false) {
		$orc = parent::generateORC(array(),true);
		$orc[2] = ''; // empty
		$orc[3] = ''; // empty
		$orc[4] = ''; // empty
		$orc[5] = ''; // empty
		$orc[6] = ''; // empty
		$orc[7] = ''; // empty
		$orc[8] = ''; // empty
		$orc[9] = ''; // empty
		$orc[10] = ''; // empty
		$orc[11] = ''; // empty
		$orc12 = array(); // ORC‐12: Ordering Provider
		$orc12[] = '1234'; // ORC‐12.1: ID Number
		$orc122 = array(); // ORC‐12.2: Family Name
		$orc122[] = 'Admit'; // ORC‐12.2.1: Surname
		$orc12[] = implode('^',$orc122);
		$orc12[] = 'Alan'; // ORC‐12.3: Given Name
		$orc12[] = ''; // empty
		$orc12[] = ''; // empty
		$orc12[] = ''; // empty
		$orc12[] = ''; // empty
		$orc12[] = ''; // empty
		$orc129 = array(); // ORC‐12.9: Assigning Authority
		$orc129[] = 'ABC Medical Center'; // ORC‐12.9.1: Namespace ID
		$orc129[] = '2.16.840.1.113883.19.4.6'; // ORC‐12.9.2: Universal ID
		$orc129[] = 'ISO'; // ORC‐12.9.3: Universal ID Type
		$orc12[] = implode('&',$orc129);
		$orc[12] = implode('^',$orc12);
		$orc[13] = ''; // empty
		$orc[14] = ''; // empty
		$orc[15] = ''; // empty
		$orc[16] = ''; // empty
		$orc[17] = ''; // empty
		$orc[18] = ''; // empty
		$orc[19] = ''; // empty
		$orc[20] = ''; // empty
		$orc21 = array(); // ORC‐21: Ordering Facility Name
		$orc21[] = 'Level Seven Healthcare'; // ORC‐21.1: Organization Name
		$orc21[] = 'L'; // ORC‐21.2: Organization Name Type Code
		$orc21[] = ''; // empty
		$orc21[] = ''; // empty
		$orc21[] = ''; // empty
		$orc216 = array(); // ORC‐21.6: Assigning Authority
		$orc216[] = 'ABC Medical Center'; // ORC‐21.6.1: Namespace ID
		$orc216[] = '2.16.840.1.113883.19.4.6'; // ORC‐21.6.2: Universal ID
		$orc216[] = 'ISO'; // ORC‐21.6.3: Universal ID Type
		$orc21[] = implode('&',$orc216);
		$orc21[] = 'XX'; // ORC‐21.7: Identifier Type Code
		$orc21[] = ''; // empty
		$orc21[] = ''; // empty
		$orc21[] = '1234'; // ORC‐21.10: Organization Identifier
		$orc[21] = implode('^',$orc21);
		$orc22 = array(); // ORC‐22: Ordering Facility Address
		$orc221 = array(); // ORC‐22.1; Street Address
		$orc221[] = '1005 Healthcare Drive'; // ORC‐22.1.1: Street or Mailing Address
		$orc22[] = implode('^',$orc221);
		$orc22[] = ''; // empty
		$orc22[] = 'Ann Arbor'; // ORC‐22.3: City
		$orc22[] = 'MI'; // ORC‐22.4: State or Province
		$orc22[] = '48103'; // ORC‐22.5: Zip Code
		$orc22[] = ''; // empty
		$orc22[] = 'B'; // ORC‐22.7: Address Type
		$orc[22] = implode('^',$orc22);
		$orc23 = array(); // ORC‐23: Ordering Facility Phone Number
		$orc23[] = ''; // empty
		$orc23[] = ''; // empty
		$orc23[] = ''; // empty
		$orc23[] = ''; // empty
		$orc23[] = ''; // empty
		$orc23[] = '734'; // ORC‐23.6: Area/City Code
		$orc23[] = '5553001'; // ORC‐23.7: Local Number
		$orc[23] = implode('^',$orc23);
		$orc24 = array(); // ORC‐24: Ordering Provider Address
		$orc241 = array(); // ORC‐24.1: Street Address
		$orc241[] = '4444 Healthcare Drive'; // ORC‐24.1.1: Street or Mailing Address
		$orc24[] = implode('^',$orc241);
		$orc24[] = ''; // empty
		$orc24[] = 'Ann Arbor'; // ORC‐24.3: City
		$orc24[] = 'MI'; // ORC‐24.4: State or Province
		$orc24[] = '48103'; // ORC‐24.5: Zip Code
		$orc24[] = ''; // empty
		$orc24[] = 'B'; // ORC‐24.7: Address Type
		$orc[24] = implode('^',$orc24);
		if ($raw) return $orc;
		return implode('|',$orc);
	}

	public static function generateOBR(Array $data,$raw=false) {
		static $obrCtr = 1;
		$loincNum = $data['orderLabTest']->labTest;
		if (!isset(self::$loincodes[$loincNum])) {
			$loinc = new ProcedureCodesLOINC();
			$loinc->loincNum = $loincNum;
			$loinc->populate();
			self::$loincodes[$loincNum] = $loinc;
		}
		if (!isset($loinc)) $loinc = self::$loincodes[$loincNum];
		$obr = array('OBR');
		$obr[1] = $obrCtr++; // OBR‐1: SetID‐OBR
		$obr[2] = ''; // empty
		$obr3 = array(); // OBR‐3: Filler Order Number
		$obr3[] = '9700123'; // OBR‐3.1: Entity Identifier (ST)
		$obr3[] = 'Lab'; // OBR‐3.2: Namespace ID (IS)
		$obr3[] = '2.16.840.1.113883.19.3.1.6'; // OBR‐3.3: Universal ID (ST)
		$obr3[] = 'ISO'; // OBR‐3.4: Universal ID Type
		$obr3 = array(); // temporarily set to an empty value
		$obr[3] = implode('^',$obr3);
		$obr4 = array(); // OBR‐4: Universal Service Identifier
		$obr4[] = $loincNum; // OBR‐4.1: Identifier (ST)
		// LOINC shortname
		$obr4[] = $loinc->shortname; // OBR‐4.2: Text (ST)
		$obr4[] = ''; // 'LN'; // OBR‐4.3: Name of Coding System (ID)
		$obr4[] = ''; // '3456543'; // OBR‐4.4: Alternate Identifier (ST)
		$obr4[] = ''; // 'Blood lead test'; // OBR‐4.5: Alternate Text (ST)
		$obr4[] = ''; // '99USI'; // OBR‐4.6: Name of Alternate Coding System
		$obr[4] = implode('^',$obr4);
		$obr[5] = ''; // empty
		$obr[6] = ''; // empty
		$obr7 = array();
		$obr7[] = date('YmdHiO',strtotime($data['labTest']->observationTime)); // OBR‐7.1: Observation Date/Time
		$obr[7] = implode('^',$obr7);
		$obr[8] = ''; // empty
		$obr[9] = ''; // empty
		$obr[10] = ''; // empty
		$obr[11] = ''; // empty
		$obr[12] = ''; // empty
		$obr[13] = ''; // 'Diarrhea'; // OBR‐13: Relevant Clinical Information
		$obr[14] = ''; // empty
		$obr[15] = ''; // empty
		$provider = $data['provider'];
		$obr16 = array(); // OBR‐16: Ordering Provider
		$obr16[] = (strlen($provider->deaNumber) > 0)?$provider->deaNumber:$provider->personId; // OBR‐16.1: ID Number
		$obr162 = array(); // OBR‐16.2: Family Name
		$obr162[] = $provider->person->lastName; // Surname
		$obr16[] = implode('^',$obr162);
		$obr16[] = $provider->person->firstName; // OBR‐16.3: Given Name
		$obr16[] = ''; // empty
		$obr16[] = ''; // empty
		$obr16[] = ''; // empty
		$obr16[] = ''; // empty
		$obr16[] = ''; // empty
		$obr169 = array(); // OBR‐16.9: Assigning Authority
		$obr169[] = 'ABC Medical Center'; // OBR‐16.9.1: Namespace ID
		$obr169[] = '2.16.840.1.113883.19.4.6'; // OBR‐16.9.2: Universal ID
		$obr169[] = 'ISO'; // OBR‐16.9.3: Universal ID Type
		$obr169 = array(); // temporarily set to an empty value
		$obr16[] = implode('&',$obr169);
		$obr[16] = implode('^',$obr16);
		$obr[17] = ''; // empty
		$obr[18] = ''; // empty
		$obr[19] = ''; // empty
		$obr[20] = ''; // empty
		$obr[21] = ''; // empty
		$obr22 = array();
		$obr22[] = '200808181800-0700'; // OBR‐22.1: Results Rpt/Status Chng ‐ Date/Time
		$obr[22] = implode('^',$obr22);
		$obr[23] = ''; // empty
		$obr[24] = ''; // empty
		$obr[25] = 'F'; // OBR‐25: Result Status
		$obr[26] = ''; // empty
		$obr[27] = ''; // empty
		$obr[28] = ''; // empty
		$obr[29] = ''; // empty
		$obr[30] = ''; // empty
		$obr31 = array(); // OBR‐31: Reason for Study
		$obr31[] = '787.91'; // OBR‐31.1: Identifier (ST)
		$obr31[] = 'DIARRHEA'; // OBR‐31.2: Text (ST)
		$obr31[] = 'I9CDX'; // OBR‐31.3: Name of Coding System (ID)
		$obr31 = array(); // temporarily set to an empty value
		$obr[31] = implode('^',$obr31);
		if ($raw) return $obr;
		return implode('|',$obr);
	}

	public static function generateOBX(Array $data,$raw=false) {
		static $obxCtr = 1;
		$loincNum = $data['orderLabTest']->labTest;
		if (!isset(self::$loincodes[$loincNum])) {
			$loinc = new ProcedureCodesLOINC();
			$loinc->loincNum = $loincNum;
			$loinc->populate();
			self::$loincodes[$loincNum] = $loinc;
		}
		if (!isset($loinc)) $loinc = self::$loincodes[$loincNum];
		$obx = array('OBX');
		$obx[1] = $obxCtr++; // OBX‐1: SetID–OBX
		//$obx[2] = 'NM'; // OBX‐2: Value Type
		$obx3 = array(); // OBX‐3: Observation ID
		$obx3[] = $loincNum; // OBX‐3.1: Identifier
		$obx3[] = $loinc->shortname; // OBX‐3.2: Text
		//$obx3[] = 'LN'; // OBX‐3.3: Coding System
		$obx[3] = implode('^',$obx3);
		$obx[4] = '1'; // OBX‐4: Observation Sub‐ID
		$obx[5] = $data['result']->value; // OBX‐5: Observation Value

		$units = $data['result']->units;
		$unitsOfMeasure = DataTables::$unitsOfMeasure;
		$tmp = strtoupper($units);
		$unitsText = isset($unitsOfMeasure[$tmp])?$unitsOfMeasure[$tmp]:'';

		$obx6 = array(); // OBX‐6: Units
		$obx6[] = $units; // OBX‐6.1: Identifier
		$obx6[] = $unitsText; // OBX‐6.2: Text
		$obx6[] = 'ISO+'; // 'UCUM'; // OBX‐6.3: Coding System
		if (strlen($units) > 0) $obx6 = array();
		$obx[6] = implode('^',$obx6);
		$obx[7] = $data['result']->referenceRange; // OBX‐7: References Range
		$obx[8] = $data['result']->abnormalFlag; // OBX‐8: Abnormal Flags/Susceptibility
		$obx[9] = ''; // empty
		$obx[10] = ''; // empty
		$obx[11] = $data['result']->resultStatus; // OBX‐11: Observation Result Status
		$obx[12] = ''; // empty
		$obx[13] = ''; // empty
		$obx14 = array();
		$obx14[] = date('YmdHiO',strtotime($data['result']->observationTime)); // OBX‐14.1: Date/Time of the Observation
		$obx[14] = implode('^',$obx14);
		$obx[15] = ''; // empty
		$obx[16] = ''; // empty
		$obx17 = array(); // OBX‐17: Observation Method
		$obx17[] = $data['result']->identifier; // OBX.17.1: Identifier
		$obx[17] = implode('^',$obx17);
		$obx[18] = ''; //  empty
		$obx19 = array();
		$obx19[] = date('YmdHiO',strtotime($data['result']->observationTime)); // OBX‐19.1: Date/Time of the Analysis
		$obx[19] = implode('^',$obx19); // 
		$obx[20] = ''; // empty
		$obx[21] = ''; // empty
		$obx[22] = ''; // empty
		$obx23 = array(); // OBX‐23: Performing Organization Name
		$obx23[] = 'Lab'; // OBX‐23.1: Organization Name
		$obx23[] = 'L'; // OBX‐23.2: Organization Name Type Code
		$obx23[] = ''; // empty
		$obx23[] = ''; // empty
		$obx23[] = ''; // empty
		$obx236 = array(); // OBX‐23.6: Assigning Authority
		$obx236[] = 'CLIA'; // OBX‐23.6.1: Namespace ID
		$obx236[] = '2.16.840.1.113883.19.4.6'; // OBX‐23.6.2: Universal ID
		$obx236[] = 'ISO'; // OBX‐23.6.3: Universal ID Type
		$obx236 = array(); // temporarily set to an empty value
		$obx23[] = implode('&',$obx236); // 
		$obx23[] = 'XX'; // OBX‐23.7: Identifier Type Code
		$obx23[] = ''; // empty
		$obx23[] = ''; // empty
		$obx23[] = '1236'; // OBX‐23.10: Organization Identifier
		$obx23 = array(); // temporarily set to an empty value
		$obx[23] = implode('^',$obx23);
		$obx24 = array(); // OBX‐24: Performing Organization Address
		$obx241 = array(); // OBX‐24.1: Street Address
		$obx241[] = '3434 Industrial Lane'; // OBX‐24.1.1: Street or Mailing Address
		$obx24[] = implode('^',$obx241);
		$obx24[] = ''; // empty
		$obx24[] = 'Ann Arbor'; // OBX‐24.3: City
		$obx24[] = 'MI'; // OBX‐24.4: State or Province
		$obx24[] = '48103'; // OBX‐24.5: Zip Code
		$obx24[] = ''; // empty
		$obx24[] = 'B'; // OBX‐24.7: Address Type
		$obx24 = array(); // temporarily set to empty value
		$obx[24] = implode('^',$obx24);
		if ($raw) return $obx;
		return implode('|',$obx);
	}

	public static function generateSPM(Array $data,$raw=false) {
		$spm = array('SPM');
		$spm[1] = ''; // empty
		$spm[2] = ''; // empty
		$spm[3] = ''; // empty
		$spm4 = array(); // SPM‐4: Specimen Type
		$spm4[] = ''; // '122554006'; // SPM‐4.1: Identifier
		$spm4[] = $data['orderLabTest']->specimen; // 'Capillary blood specimen'; // SPM‐4.2: Text
		$spm4[] = ''; // 'SCT'; // SPM‐4.3: Name of Coding System
		$spm4[] = ''; // 'BLDC'; // SPM‐4.4: Alternate Identifier
		$spm4[] = ''; // 'Blood capillary'; // SPM‐4.5: Alternate Text
		$spm4[] = ''; // 'HL70487'; // SPM‐4.6: Name of Alternate Coding System
		$spm4[] = ''; // '20080131'; // SPM‐4.7: Coding System Version ID
		$spm4[] = '2.5.1'; // SPM‐4.8: Alternate Coding System Version ID
		$spm[4] = implode('^',$spm4);
		if ($raw) return $spm;
		return implode('|',$spm);
	}

}
