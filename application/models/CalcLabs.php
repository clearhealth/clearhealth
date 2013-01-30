<?php
/*****************************************************************************
*       CalcLabs.php
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

class CalcLabs {
	private $_calcLabResults = array();
	public $_patientId = 0;
	function calculateGFRResult() {
		$tmpArr = array();
                $tmpArr[] = date('Y-m-d H:i:s'); //observation time
                $tmpArr[] = 'GFR (CALC)'; //desc
	
		$gender = NSDR::populate($this->_patientId. "::com.clearhealth.person.displayGender");
		$crea = NSDR::populate($this->_patientId. "::com.clearhealth.labResults[populate(@description=CREA)]");
		$genderFactor = null;
		$creaValue = null;
		$personAge = null;
		$raceFactor = 1;
		switch ($gender[key($gender)]) {
			case 'M':
				$genderFactor = 1;
				break;
			case 'F':
				$genderFactor = 0.742;
				break;
		}
		if ((int)strtotime($crea['observation_time']) >= strtotime('now - 60 days') && strtolower($crea[key($crea)]['units']) == 'mg/dl') {
			$creaValue = $crea[key($crea)]['value'];
		}
		$person = new Person();
		$person->personId = $this->_patientId;
		$person->populate();
		if ($person->age > 0) {
			$personAge = $person->age;
		}
		$personStat = new PatientStatistics();
		$personStat->personId = $this->_patientId;
		$personStat->populate();
		if ($personStat->race == "AFAM") {
			$raceFactor = 1.210;
		}
		$gfrValue = "INC";
		if ($personAge > 0 && $creaValue > 0) {
			$gfrValue = "" . (int)round(pow($creaValue,-1.154)*pow($personAge,-0.203)*$genderFactor*$raceFactor*186);
		}
		trigger_error("gfr:: " . $gfrValue,E_USER_NOTICE);
		$tmpArr[] = $gfrValue; // lab value
                $tmpArr[] = 'mL/min/1.73 m2'; //units
                $tmpArr[] = ''; //ref range
                $tmpArr[] = ''; //abnormal
                $tmpArr[] = 'F'; //status
                $tmpArr[] = date('Y-m-d H:i:s').'::'.'0'; // observationTime::(boolean)normal; 0 = abnormal, 1 = normal
                $tmpArr[] = '0'; //sign
                //$this->_calcLabResults[uniqid()] = $tmpArr;
                $this->_calcLabResults[1] = $tmpArr; // temporarily set index to one(1) to be able to include in selected lab results
		return $tmpArr;
	}

	function getAllCalcLabsArray($patientId) {
		$this->_patientId = (int)$patientId;
		$this->calculateGFRResult();
		return $this->_calcLabResults;
	}
}
