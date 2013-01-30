<?php

class PDReportQuery {

	protected static function _strFill($str,$len,$type=0) {
		// $type: 0/default = LJBF, 1 = RJZF
		switch ($type) {
			case 1:
				$str = str_pad($str,$len,'0',STR_PAD_LEFT);
				break;
			default:
				$str = str_pad($str,$len);
				break;
		}
		return $str;
	}

	public static function generate2x($dateStart,$dateEnd,$buildingId=null,$practiceId=null) {
		$db = Zend_Registry::get('dbAdapter');
		$data = array();
		// codes.code_type: 2 = diagnosis, 3 procedure
		$sql = 'SELECT
				encounter.encounter_id, patientPerson.person_id AS "patient.patient_id",
				encounter.date_of_treatment AS "encounter.dateOfService",
				patient.record_number AS "patient.record_number",
				patientPerson.identifier AS "person.identifier",
				patientPerson.last_name AS "person.last_name",
				patientPerson.first_name AS "person.first_name",
				patientPerson.middle_name AS "person.middle_name",
				patientPerson.gender AS "person.gender",
				patientPerson.date_of_birth AS "person.date_of_birth",
				patientPerson.marital_status AS "person.marital_status",
				provider.state_license_number AS "provider.state_license_number",
				providerPerson.identifier AS "provider.identifier",
				patient_statistics.ethnicity AS "person.statistics.ethnicity",
				patient_statistics.family_size AS "person.statistics.family_size",
				patient_statistics.income AS "person.statistics.family_income",
				patient_statistics.monthly_income AS "person.statistics.montly_income",
				patient_statistics.employment_status AS "person.statistics.employment_type",
				patient_statistics.education_level AS "person.statistics.education_level",
				\'\' AS "person.statistics.state_of_birth",
				\'\' AS "person.statistics.country_of_birth",
				\'\' AS "person.statistics.country_of_birth_other",
				\'\' AS "person.statistics.county_of_birth",
				\'\' AS "person.statistics.mother_first_name",
				\'\' AS "person.statistics.birth_name",
				clearhealth_claim.total_billed AS "encounter.total_amount_billed",
				clearhealth_claim.total_paid AS "encounter.amount_paid",
				\'\' AS "person.first_address.zip",
				\'\' AS "person.first_address.line1",
				\'\' AS "person.first_address.state",
				\'\' AS "person.first_address.city",
				\'\' AS "person.first_number.number"
			FROM `encounter`
			INNER JOIN provider ON provider.person_id = encounter.treating_person_id
			INNER JOIN person AS providerPerson ON providerPerson.person_id = provider.person_id
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			LEFT JOIN patient_statistics ON patient_statistics.person_id=patient.person_id
			INNER JOIN person AS patientPerson ON patientPerson.person_id = patient.person_id
			LEFT JOIN clearhealth_claim ON clearhealth_claim.encounter_id = encounter.encounter_id
			WHERE encounter.date_of_treatment BETWEEN '.$db->quote(date('Y-m-d H:i:s',strtotime($dateStart))).' AND '.$db->quote(date('Y-m-d H:i:s',strtotime($dateEnd)));
		if ($buildingId !== null) {
			$sql .= ' AND encounter.building_id = '.$db->quote($buildingId);
		}
		$sql .= ' ORDER BY patientPerson.person_id';
		//file_put_contents('/tmp/sql.txt',$sql);
		if ($encounters = $db->fetchAll($sql)) {
			foreach ($encounters as $encounter) {
				// retrieves address
				$sql = 'SELECT
						address.postal_code AS "person.first_address.zip",
						address.line1 AS "person.first_address.line1",
						address.state AS "person.first_address.state",
						address.city AS "person.first_address.city"
					FROM address
					INNER JOIN person_address ON person_address.address_id = address.address_id
					WHERE person_address.person_id = '.$db->quote($encounter['patient.patient_id']).' LIMIT 1';
				if ($address = $db->fetchRow($sql)) {
					foreach ($address as $key=>$value) {
						$encounter[$key] = $value;
					}
				}
				// retrieves phone
				$sql = 'SELECT
						number.number AS "person.first_number.number"
					FROM number
					INNER JOIN person_number ON person_number.number_id = number.number_id
					WHERE person_number.person_id = '.$db->quote($encounter['patient.patient_id']).' LIMIT 1';
				if ($phone = $db->fetchRow($sql)) {
					foreach ($phone as $key=>$value) {
						$encounter[$key] = $value;
					}
				}

				// retrieves diagnoses and procedures
				$sql = "SELECT
						coding_data.fee, coding_data.units, coding_data.modifier, codes.code_type, codes.code
					FROM coding_data
					INNER JOIN codes ON codes.code_id = coding_data.code_id
					WHERE coding_data.foreign_id = '{$encounter['encounter_id']}' AND (codes.code_type = 2 OR codes.code_type = 3)";
				$claims = $db->fetchAll($sql);
				$diagnoses = array();
				$procedures = array();
				foreach ($claims as $claim) {
					if ($claim['code_type'] == 2) { // diagnosis
						$diagnoses[] = array('code'=>$claim['code']);
					}
					else { // procedure
						$procedures[] = array(
							'code'=>$claim['code'],
							'fee'=>$claim['fee'],
							'units'=>(int)$claim['units'],
							'modifiers'=>$claim['modifier']
						);
					}
				}
				/*for ($i = 0; $i < 3; $i++) {
					if (isset($diagnoses[$i])) continue;
				}
				for ($i = 0; $i < 7; $i++) {
					if (isset($procedures[$i])) continue;
					$procedures[] = array();
				}*/
				$encounter['diagnoses'] = $diagnoses;
				$encounter['procedures'] = $procedures;
				$data[$encounter['patient.patient_id']][] = $encounter;
			}
		}

		//file_put_contents('/tmp/data.txt',print_r($data,true));
		$ret = array();
		$identity = Zend_Auth::getInstance()->getIdentity();

		$siteCode = 13;
		$siteCodeId = self::_strFill(substr($siteCode,0,4),4,1);

		$mri = array();
		$mri[] = 'MRIHEADR';
		$mri[] = $siteCodeId;
		$submitterName = $identity->person->displayName;
		$mri[] = self::_strFill(substr($submitterName,0,30),30);
		$submissionDate = date('mdY');
		if (isset($data['submissionDate'])) {
			$submissionDate = date('mdY',strtotime($data['submissionDate']));
		}
		$mri[] = $submissionDate;
		$mri[] = $dateStart;
		$mri[] = $dateEnd;
		$submitterInitial = $identity->person->middleName;
		$mri[] = self::_strFill(substr($submitterInitial,0,8),8);
		$mri[] = self::_strFill('',166);
		$ret[] = implode('',$mri);
		foreach ($data as $patientId=>$rows) {
			$firstRow = false;
			foreach ($rows as $row) {
				$row['siteCodeId'] = $siteCodeId;
				if (!$firstRow) {
					$ret[] = self::_populatePD($row);
					$ret[] = self::_populateCD($row);
					$firstRow = true;
				}
				$ret[] = self::_populateOS($row);
			}
		}
		return implode("\n",$ret);
	}

	protected static function _populatePD($data) {
		$pd = array();
		$pd[] = 'PD';
		$pd[] = $data['siteCodeId'];
		// Member Identifier = patient.record_number 9L
		$pd[] = self::_strFill(substr($data['patient.record_number'],0,9),9);
		// Member SSN = person.identifier 9L
		$pd[] = self::_strFill(substr($data['person.identifier'],0,9),9);
		// Member Full Name = person.lastname, person.first_name person.middle_name (first letter only) 35L
		$pd[] = self::_strFill(substr($data['person.last_name'].', '.$data['person.first_name'].' '.substr($data['person.middle_name'],0,1),0,35),35);
		// Member Sex = person.gender (MFO) 1L
		$pd[] = substr($data['person.gender'],0,1);
		// Member Birth Date= person.date_of_birth 8
		$pd[] = date('mdY',strtotime($data['person.date_of_birth']));
		// Member Zip Code = person.first_address.zip 5L
		$pd[] = self::_strFill(substr($data['person.first_address.zip'],0,5),5);
		// Member Street Address = person.first_address.line1 35L
		$pd[] = self::_strFill(substr($data['person.first_address.line1'],0,35),35);
		// Member State = person.first_address.state 2L
		$pd[] = self::_strFill(substr($data['person.first_address.state'],0,2),2);
		// Blank 10
		$pd[] = self::_strFill('',10);
		// Member City = person.first_address.city 25L
		$pd[] = self::_strFill(substr($data['person.first_address.city'],0,25),25);
		// Member Ethnicity = person.statistics.ethnicity 2R
		$ethnicity = (isset($data['person.statistics.ethnicity']))?$data['person.statistics.ethnicity']:'';
		$pd[] = self::_strFill(substr($ethnicity,0,2),2,1);
		// Family Size = person.statistics.family_size 2R
		$familySize = (isset($data['person.statistics.family_size']))?$data['person.statistics.family_size']:'';
		$pd[] = self::_strFill(substr($familySize,0,2),2,1);
		// Family Monthly Income = person.statistics.family_income 4L
		$familyIncome = (isset($data['person.statistics.family_income']))?$data['person.statistics.family_income']:'';
		$pd[] = self::_strFill(substr($familyIncome,0,4),4);
		// Family Source of Income = person.statitics.montly_income 2R
		$montlyIncome = (isset($data['person.statistics.montly_income']))?$data['person.statistics.montly_income']:'';
		$pd[] = self::_strFill(substr($montlyIncome,0,2),2,1);
		// Type of Employment = person.employer_address.name 2R
		$employmentType = (isset($data['person.statistics.employment_type']))?$data['person.statistics.employment_type']:'';
		$pd[] = self::_strFill(substr($employmentType,0,2),2,1);
		// Member Education Level = person.statistics.education_level 2R
		$educationLevel = (isset($data['person.statistics.education_level']))?$data['person.statistics.education_level']:'';
		$pd[] = self::_strFill(substr($educationLevel,0,2),2,1);
		// Member ELA Status under IRCA = default to blank of no-value equivalent 1
		$pd[] = self::_strFill('',1);
		// Member Alien ID Number= default to blank of no-value equivalent 9L
		$pd[] = self::_strFill('',9);
		// Member Alien Section Number= default to blank of no-value equivalent 1L
		$pd[] = self::_strFill('',1);
		// Member Alien Effective Date= default to blank of no-value equivalent 8
		$pd[] = self::_strFill('',8);
		// Member Alien Expiration Date= default to blank of no-value equivalent 8
		$pd[] = self::_strFill('',8);
		// Member Alien Documentation Type= default to blank of no-value equivalent 1L
		$pd[] = self::_strFill('',1);
		// Member Marital Status= person.marital status 2R
		$pd[] = self::_strFill(substr($data['person.marital_status'],0,2),2,1);
		// Member Home Phone Number = person.first_number.number 12L
		$pd[] = self::_strFill(substr($data['person.first_number.number'],0,12),12);
		// Blank 39
		$pd[] = self::_strFill('',39);
		return implode('',$pd);
	}

	protected static function _populateCD($data) {
		$cd = array();
		$cd[] = 'CD';
		// Submitter Site Code Number 4
		$cd[] = $data['siteCodeId'];
		// Member Identifier 9L
		$cd[] = self::_strFill(substr($data['patient.record_number'],0,9),9);
		// Member SSN 9L
		$cd[] = self::_strFill(substr($data['person.identifier'],0,9),9);
		// Member State of Birth 2L
		$stateOfBirth = (isset($data['person.statistics.state_of_birth']))?$data['person.statistics.state_of_birth']:'';
		$cd[] = self::_strFill(substr($stateOfBirth,0,2),2);
		// Member Country of Birth 2L
		$countryOfBirth = (isset($data['person.statistics.country_of_birth']))?$data['person.statistics.country_of_birth']:'';
		$cd[] = self::_strFill(substr($countryOfBirth,0,2),2);
		// Member Country of Birth - Other 20L
		$countryOfBirthOther = (isset($data['person.statistics.country_of_birth_other']))?$data['person.statistics.country_of_birth_other']:'';
		$cd[] = self::_strFill(substr($countryOfBirthOther,0,20),20);
		// Member County of Birth 2R
		$countyOfBirth = (isset($data['person.statistics.county_of_birth']))?$data['person.statistics.county_of_birth']:'';
		$cd[] = self::_strFill(substr($countyOfBirth,0,2),2,1);
		// Member's Mother's First Name 30L
		$motherFirstName = (isset($data['person.statistics.mother_first_name']))?$data['person.statistics.mother_first_name']:'';
		$cd[] = self::_strFill(substr($motherFirstName,0,30),30);
		// Member Birth Name (Last, First, MI) 35L
		$birthName = $data['person.last_name'].', '.$data['person.first_name'].' '.substr($data['person.middle_name'],0,1);
		if (isset($data['person.statistics.birth_name'])) $birthName = $data['person.statistics.birth_name'];
		$cd[] = self::_strFill(substr($birthName,0,35),35);
		// Blank 125
		$cd[] = self::_strFill('',125);
		return implode('',$cd);
	}

	protected static function _populateOS($data) {
		$os = array();
		$os[] = 'OS';
		$os[] = '6016'.$data['siteCodeId'];
		// Member Identifier 9L
		$os[] = self::_strFill(substr($data['patient.record_number'],0,9),9);
		// Member SSN 9L
		$os[] = self::_strFill(substr($data['person.identifier'],0,9),9);
		// Physician License Number 9L
		$os[] = self::_strFill(substr($data['provider.state_license_number'],0,9),9);
		// Encounter Date
		$os[] = date('mdY',strtotime($data['encounter.dateOfService']));
		// Type of Service 2R
		/*
		1 = Primary Care, 2 = Specialty Care, 3 = Medical Supplies, 4 = Ambulatory Surgery,
		5 = Dental Health, 6 = Detoxification, 7 = Home Health, 8 = Laboratory, 9 = Optometry,
		10 = Pharmacy, 11 = Podiatry, 12 = Radiology, 98 = Other
		*/
		$os[] = '01';
		// Provider Number 10L
		// Provider numbers are unique identifiers assigned by the AmeriChoice Claims Supervisor -- Loni Chun @ 858.495.1351
		$os[] = self::_strFill(substr($data['provider.identifier'],0,10),10);
		// Vendor Tax ID Number 11L
		$os[] = self::_strFill('',11);
		// Total Amount Billed 5R
		$os[] = self::_strFill(substr($data['encounter.total_amount_billed'],0,5),5,1);
		// Billed-To Code 2R
		/*
		1 = Medi-Cal, 2 = Medicare, 3 = CHDP, 4 = Private Insurance, 5 = Sliding Fee, 6 = OFP
		7 = Self-Pay, 8 = Section 17000, 9 = Dual/Crossover, 10 = State, 98 = Other
		*/
		$os[] = '05';
		// Amount Paid 5R
		// in cents no punctuation
		$os[] = self::_strFill(substr($data['encounter.amount_paid'],0,5),5,1);
		// Payer Source 2R
		// Same format with Billed-To Code
		$os[] = '05';
		// County Cost 5R
		// Same format with Amount Paid
		$os[] = self::_strFill(substr('',0,5),5,1);
		$diagnoses = isset($data['diagnoses'])?$data['diagnoses']:array();
		for ($i = 0; $i < 3; $i++) {
			$diagnosis = isset($diagnoses[$i])?$diagnoses[$i]:array();
			$diagnosisCode = isset($diagnosis['code'])?$diagnosis['code']:'';
			// Diagnosis 6L
			$os[] = self::_strFill(substr($diagnosisCode,0,6),6);
		}

		// Employment Injury 1
		$os[] = self::_strFill(substr('N',0,1),1);
		// Auto Injury 1
		$os[] = self::_strFill(substr('N',0,1),1);
		// Other Accident Injury 1
		$os[] = self::_strFill(substr('N',0,1),1);
		// ER Disposition 2L
		$os[] = '97';
		// Service Setting 2L
		$os[] = '03';
		// Blank 8
		$os[] = self::_strFill('',8);
		$procedures = isset($data['procedures'])?$data['procedures']:array();
		for ($i = 0; $i < 7; $i++) {
			$procedure = isset($procedures[$i])?$procedures[$i]:array();
			$procedureCode = isset($procedure['code'])?$procedure['code']:'';
			// Procedure Code #1 7L
			$os[] = self::_strFill(substr($procedureCode,0,7),7);
			$amountBilled = isset($procedure['fee'])?$procedure['fee']:'';
			// Amount Billed 5R
			// Same format with Amount Paid
			$os[] = self::_strFill(substr($amountBilled,0,5),5,1);
			$units = isset($procedure['units'])?$procedure['units']:'';
			if (strlen($procedureCode) > 0 && !strlen($units) > 0) $units = 1;
			// Units 2R
			// The Units field represents the number of services billed; this field will default to "1" if no entry is present.
			$os[] = self::_strFill(substr($units,0,2),2,1);
			$modifiers = isset($procedure['modifiers'])?$procedure['modifiers']:'';
			// Modifier 2L
			$os[] = self::_strFill(substr($modifiers,0,2),2);
		}

		// Blank 8
		$os[] = self::_strFill('',8);
		return implode('',$os);
	}

}

