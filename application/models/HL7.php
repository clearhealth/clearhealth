<?php
/*****************************************************************************
*       HL7.php
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


class HL7 {

	public static function generateHL72XML($data) {
		$hl7 = new HL7XML($data);
		$hl7->parse();
		return $hl7->xml->asXML();
	}

	public static function generateMSH(Array $data,$raw=false) {
		$msh = array('MSH');
		//$msh[1] = '|'; // MSH-1: Field Separator fixed value
		$msh[2] = '^~\&'; // MSH-2: Encoding Characters fixed value
		$msh3 = array(); // MSH-3: Sending Application
		$msh3[] = 'ClearHealth'; // 'ClearHealth'; // MSH-3.1: Namespace ID
		//$msh3[] = '2.16.840.1.113883.3.72.7.1'; // MSH-3.2: Universal ID
		//$msh3[] = 'HL7'; // MSH-3.3: Universal ID Type
		$msh[3] = implode('^',$msh3);
		$msh4 = array(); // MSH-4: Sending Facility
		$msh4[] = 'ClearHealth Facility'; // 'ClearHealth'; // MSH-4.1: Namespace ID
		//$msh4[] = '2.16.840.1.113883.3.72.7.2'; // MSH-4.2: Universal ID
		//$msh4[] = 'HL7'; // MSH-4.3: Universal ID Type
		$msh[4] = implode('^',$msh4);
		$msh5 = array(); // MSH-5: Receiving Application
		$msh5[] = 'PH Application'; // MSH-5.1: Namespace ID
		$msh5[] = '2.16.840.1.113883.3.72.7.3'; // MSH-5.2: Universal ID
		$msh5[] = 'HL7'; // MSH-5.3: Universal ID Type
		$msh[5] = implode('^',$msh5);
		$msh6 = array(); // MSH-6: Receiving Facility
		$msh6[] = 'PH Facility'; // MSH-6.1: Namespace ID
		$msh6[] = '2.16.840.1.113883.3.72.7.4'; // MSH-6.2: Universal ID
		$msh6[] = 'HL7'; // MSH-6.3: Universal ID Type
		$msh[6] = implode('^',$msh6);
		$msh7 = array();
		$msh7[] = date('YmdHis'); // Date/Time of Message
		$msh[7] = implode('^',$msh7);
		$msh[8] = '';
		$msh9 = array(); // MSH-9: Message Type
		$msh9[] = $data['messageType']['code']; // MSH-9.1: Message Code fixed value
		$msh9[] = $data['messageType']['eventType']; // MSH-9.2: Event Type fixed value
		$msh9[] = $data['messageType']['structure']; // MSH-9.3: Message Structure fixed value
		$msh[9] = implode('^',$msh9);
		$msh[10] = 'NIST-101105150245914'; // MSH-10: Message Control ID
		$msh11 = array();
		$msh11[] = 'P'; // MSH-11.1: Processing ID
		$msh[11] = implode('^',$msh11);
		$msh12 = array();
		$msh12[] = '2.5.1'; // MSH12.1: Version ID fixed value
		$msh[12] = implode('^',$msh12);
		if ($raw) return $msh;
		return implode('|',$msh);
	}

	public static function generatePID($patientId,$raw=false) {
		$patientId = (int)$patientId;
		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();

		// Patient Statistics
		$statistics = PatientStatisticsDefinition::getPatientStatistics($patientId);
		$pid = array('PID');
		$pid[1] = ''; // empty
		$pid[2] = ''; // empty
		$pid3 = array(); // PID‐3: Patient Identifier List
		$pid3[1] = $patient->recordNumber; // PID‐3.1: ID Number
		$pid3[2] = ''; // empty
		$pid3[3] = ''; // empty
		$pid34 = array(); // PID-3.4: Assigning Authority
		$pid34[] = 'MPI'; // PID-3.4.1: Namespace ID
		$pid34[] = '2.16.840.1.113883.19.3.2.1'; // PID-3.4.2: Universal ID
		$pid34[] = 'ISO'; // PID-3.4.3: Universal ID Type
		$pid3[4] = implode('&',$pid34);
		$pid3[5] = 'MR'; // PID-3.5: ID Number Type
		$pid[3] = implode('^',$pid3);
		$pid[4] = ''; // empty
		$pid5 = array();
		$pid51 = array(); // PID-5.1: Family Name
		$pid51[] = $patient->lastName; // PID-5.1.1: Surname
		$pid5[] = implode('^',$pid51);
		$pid5[] = $patient->firstName; // PID-5.2: Given Name
		$pid[5] = implode('^',$pid5);
		$pid[6] = ''; // empty
		$pid7 = array();
		$pid7[] = date('Ymd',strtotime($patient->dateOfBirth)); // PID-7.1: Date of Birth
		$pid[7] = implode('^',$pid7);
		$pid[8] = $patient->gender; // PID-8: Administrative Sex
		$pid[9] = ''; // empty

		$race = '';
		$raceCode = 'HL70005';
		if (isset($statistics['Race'])) $race = $statistics['Race'];
		if (isset($statistics['race'])) $race = $statistics['race'];
		$raceId = '';
		foreach (explode(' ',$race) as $val) {
			$raceId .= strtoupper(substr($val,0,1));
		}
		$pid10 = array(); // PID-10: Race
		$pid10[] = $raceId; // PID-10.1: Identifier
		$pid10[] = $race; // PID-10.2: Text
		$pid10[] = $raceCode; // PID-10.3: Name of Coding System
		$pid[10] = implode('^',$pid10);

		$addr = new Address();
		foreach ($addr->getIteratorByPersonId($patientId) as $address) {
			break;
		}
		$street = $address->line1;
		if (strlen($address->line2) > 0) $street .= ' '.$address->line2;
		$pid11 = array(); // PID-11: Patient Address 
		$pid111 = array(); // PID-11.1: Street Address
		$pid111[] = $street; // PID-11.1.1: Street or Mailing Address
		$pid11[] = implode('^',$pid111); // PID-11.1: Street Address
		$pid11[] = ''; // empty
		$pid11[] = $address->city; // PID-11.3: City
		$pid11[] = $address->state; // PID-11.4: State
		$pid11[] = $address->zipCode; // PID-11.5: Zip Code
		$pid11[] = 'USA'; // PID-11.6: Country
		$pid11[] = substr($address->type,0,1); // PID-11.7: Address Type
		$pid[11] = implode('^',$pid11);
		$pid[12] = ''; // empty
		$pid13 = array(); // PID-13: Phone Number-Home
		$pid13[] = ''; // empty
		$pid13[] = 'PRN'; // PID-13.2: Telecommunication Use Code = PRN?
		$pid13[] = ''; // empty
		$pid13[] = ''; // empty
		$pid13[] = ''; // empty

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
		$pid13[] = $areaCode; // PID-13.6: Area/City Code
		$pid13[] = $localNumber; // PID-13.7: Local Number
		$pid[13] = implode('^',$pid13);
		$pid[14] = ''; // empty
		$pid[15] = ''; // empty
		$pid[16] = ''; // empty
		$pid[17] = ''; // empty
		$pid[18] = ''; // empty
		$pid[19] = ''; // empty
		$pid[20] = ''; // empty
		$pid[21] = ''; // empty
		$ethnic = 'Unknown';
		$ethnicCode = 'HL70189';
		if (isset($statistics['Ethnicity'])) $ethnic = $statistics['Ethnicity'];
		if (isset($statistics['ethnicity'])) $ethnic = $statistics['ethnicity'];
		$ethnicId = substr($ethnic,0,1);
		if ($ethnicId != 'H' && $ethnicId != 'N' && $ethnicId != 'U') $ethnicId = 'U';
		$pid22 = array(); // PID-22: Ethnic Group
		$pid22[] = $ethnicId; // PID-22.1: Identifier (H, N, U)
		$pid22[] = $ethnic; // PID-22.2: Text
		$pid22[] = $ethnicCode; // PID-22.3: Name of Coding System
		$pid[22] = implode('^',$pid22);
		if ($raw) return $pid;
		return implode('|',$pid);
	}

	public static function generateORC(Array $data=array(),$raw=false) {
		$orc = array('ORC');
		$orc[1] = 'RE';
		if ($raw) return $orc;
		return implode('|',$orc);
	}

}
