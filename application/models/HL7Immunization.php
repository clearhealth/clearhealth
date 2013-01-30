<?php
/*****************************************************************************
*       HL7Immunization.php
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


class HL7Immunization extends HL7 {

	public static function generate($patientId) {
		$patientId = (int)$patientId;
		$ret = array();
		$msh = array();
		$msh['messageType'] = array('code'=>'VXU','eventType'=>'V04','structure'=>'VXU_V04');
		$ret[] = self::generateMSH($msh); // MSH
		$ret[] = self::generatePID($patientId); // PID
		$iterator = new PatientImmunizationIterator();
		$iterator->setFilters(array('patientId'=>$patientId));
		foreach ($iterator as $immunization) {
			$ret[] = self::generateORC(); // 'ORC|RE';
			$ret[] = self::generateRXA($immunization); // RXA
		}
		return implode("\r\n",$ret);
	}

	public static function generateORC(Array $data=array(),$raw=false) {
		$orc = array('ORC');
		$orc[1] = 'RE';
		if ($raw) return $orc;
		return implode('|',$orc);
	}

	public static function generateRXA($patientImmunization/* can be object or id */,$raw=false) {
		// RXA|0|1|201004051600|201004051600|33^Pneumococcal Polysaccharide^CVX|0.5|ml^milliliter^ISO+||||||||1039A||MSD^Merck^MVX||||A
		if (!$patientImmunization instanceof PatientImmunization) {
			$patientImmunizationId = (int)$patientImmunization;
			$patientImmunization = new PatientImmunization();
			$patientImmunization->patientImmunizationId = $patientImmunizationId;
			$patientImmunization->populate();
		}
		$dateAdministration = date('YmdHi',strtotime($patientImmunization->dateAdministered));

		$immunizationInventory = new ImmunizationInventory();
		$immunizationInventory->immunizationId = (int)$patientImmunization->patientImmunizationId;
		$populated = false;
		if ($patientImmunization->lot > 0) {
			$immunizationInventory->immunizationInventoryId = (int)$patientImmunization->lot;
			$populated = $immunizationInventory->populate();
		}
		if (!$populated) {
			$immunizationInventory->populateByImmunizationId();
		}

		$rxa = array('RXA');
		$rxa[1] = 0; // RXA-1: Give Sub-ID Counter fixed value
		$rxa[2] = 1; // RXA-2: Administration Sub-ID Counter fixed value
		$rxa[3] = $dateAdministration; // RXA-3.1: Date/Time Start of Administration
		$rxa[4] = $dateAdministration; // RXA-4.1: Date/Time End of Administration 
		$rxa5 = array(); // RXA-5: Administered Code
		// code
		$rxa5[] = $patientImmunization->code; // RXA-5.1: Identifier
		// immunization
		$rxa5[] = $patientImmunization->immunization; // RXA-5.2: Text
		// CVX
		$rxa5[] = 'CVX'; // RXA-5.3: Name of Coding System fixed value (CVX or HL70292)
		$rxa[5] = implode('^',$rxa5);
		// amount
		$rxa[6] = $patientImmunization->amount; // RXA-6: Administered Amount
		$units = $patientImmunization->units;
		$unitsOfMeasure = DataTables::$unitsOfMeasure;
		$tmp = strtoupper($units);
		$unitsText = isset($unitsOfMeasure[$tmp])?$unitsOfMeasure[$tmp]:'';
		$rxa7 = array(); // RXA-7: Administered Units (ml, etc)
		$rxa7[] = $units; // RXA-7.1: Identifier
		$rxa7[] = $unitsText; // RXA-7.2: Text
		$codeName = (strlen($units) > 0)?'ISO+':'';
		$rxa7[] = $codeName; // RXA-7.3: Name of Coding System
		if ($codeName == '') $rxa7 = array();
		$rxa[7] = implode('^',$rxa7);
		$rxa[8] = '';
		$rxa[9] = '';
		$rxa[10] = '';
		$rxa[11] = '';
		$rxa[12] = '';
		$rxa[13] = '';
		$rxa[14] = '';
		// lot number
		$rxa[15] = $immunizationInventory->lotNumber; // RXA-15: Substance Lot Numbers
		$rxa[16] = '';
		$mvxCode = $immunizationInventory->mvxCode;
		$manufacturer = $immunizationInventory->manufacturer;
		$rxa17 = array(); // RXA-17: Substance Manufacturer Name
		// manufacturer
		$rxa17[] = $mvxCode; // RXA-17.1: Identifier
		// mvxCode
		$rxa17[] = $manufacturer; // RXA-17.2: Text
		$rxa17[] = 'MVX'; // RXA-17.3: Name of Coding System fixed value (MVX or HL70227)
		if (!strlen($mvxCode) > 0 && !strlen($manufacturer) > 0) $rxa17 = array();
		$rxa[17] = implode('^',$rxa17);
		$rxa[18] = '';
		$rxa[19] = '';
		$rxa[20] = '';
		$rxa[21] = 'A'; // RXA-21: Action Code fixed value
		if ($raw) return $rxa;
		return implode('|',$rxa);
	}

}
