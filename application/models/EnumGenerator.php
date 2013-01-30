<?php
/*****************************************************************************
*       EnumGenerator.php
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


class EnumGenerator {

	public static function generateTestData($force = false) {
		self::generateContactPreferencesEnum($force);
		self::generateImmunizationPreferencesEnum($force);
		self::generateTeamPreferencesEnum($force);
		self::generateHSAPreferencesEnum($force);
		self::generateReasonPreferencesEnum($force);
		self::generateProcedurePreferencesEnum($force);
		self::generateEducationPreferencesEnum($force);
		self::generateExamResultPreferencesEnum($force);
		self::generateExamOtherPreferencesEnum($force);
		self::generateMedicationPreferencesEnum($force);
		self::generateColorPreferencesEnum($force);
		self::generateFacilitiesEnum($force);
		self::generateMenuEnum($force);
		self::generateDemographicsPreferencesEnum($force);
		self::generateGeographyPreferencesEnum($force);
		self::generateCalendarPreferencesEnum($force);
		self::generateClinicalPreferencesEnum($force);
		self::generatePaymentTypesEnum($force);
		self::generateCodingPreferencesEnum($force);
		self::generateFacilityCodesEnum($force);
		self::generateIdentifierTypesEnum($force);
		self::generateDiscountTypesEnum($force);
		self::generateImagingPreferencesEnum($force);
		self::generateLabTestPreferencesEnum($force);
		self::generateInsurancePreferencesEnum($force);
		self::generateGenericNoteTemplateEnum($force);
		self::generateTextOnlyTypesEnum($force);
		self::generateEobAdjustmentTypesEnum($force);
		self::generateChargeTypesEnum($force);
	}

	public static function generateDemographicsPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Demographics';
			$key = 'DEMOGRAPH';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'height' => array('key' => 'HT', 'name' => 'Height', 'active' => 1, 'guid' => '050cb1f7-9df7-4a2d-857c-86f614dbd70a', 'data' => array(
					'inch' => array('key' => 'IN', 'name' => 'Inches', 'active' => 1, 'guid' => '890f57df-90f6-4f09-bc84-247ef84e5c59'),
					'cm' => array('key' => 'CM', 'name' => 'Centimeter', 'active' => 0, 'guid' => '002b02c2-4301-4da7-80d0-c225886acb13'),
				)),
				'weight' => array('key' => 'WT', 'name' => 'Weight', 'active' => 1, 'guid' => 'ee1019a3-6f62-4eac-aaf3-29cad35458be', 'data' => array(
					'pound' => array('key' => 'LB', 'name' => 'Pounds', 'active' => 1, 'guid' => '24e12952-dcc4-48a4-810d-ea315a0a1da1'),
					'kg' => array('key' => 'KG', 'name' => 'Kilograms', 'active' => 0, 'guid' => '143b9af4-909a-41ea-a05f-cb242c4f4076'),
				)),
				'temperature' => array('key' => 'TEMP', 'name' => 'Temperature', 'active' => 1, 'guid' => '9ba7dcf4-6b81-4061-b8aa-65302c0b0ff7', 'data' => array(
					'fahrenheit' => array('key' => 'F', 'name' => 'Fahrenheit', 'active' => 1, 'guid' => '3205f735-ce62-4867-ae0d-ff42786b2f17'),
					'celcius' => array('key' => 'C', 'name' => 'Celcius', 'active' => 0, 'guid' => '69589e30-e848-4f15-8801-c9ff2c35ac7c'),
				)),
				'marital' => array('key' => 'MSTATUS', 'name' => 'Marital Status', 'active' => 1, 'guid' => '262041c5-a0f8-4665-b564-d821d48664b5', 'data' => array(
					'accompanied' => array('key' => 'ACCOMP', 'name' => 'Accompanied', 'active' => 1, 'guid' => 'f32d628e-569d-4fe7-a5c5-d187d76481ea'),
					'divorced' => array('key' => 'DIVORCED', 'name' => 'Divorced', 'active' => 1, 'guid' => '206b5628-9f2e-4793-ae09-1f5e3546ec4b'),
					'married' => array('key' => 'MARRIED', 'name' => 'Married', 'active' => 1, 'guid' => 'd99356b6-37f5-4189-a0b0-ce88fe4413b5'),
					'notspec' => array('key' => 'NOTSPEC', 'name' => 'Not Specified', 'active' => 1, 'guid' => '4d080eac-e381-4e18-ac5b-df0009dc7d19'),
					'separated' => array('key' => 'SEPARATED', 'name' => 'Separated', 'active' => 1, 'guid' => '2422888a-72dd-4bb7-ba80-7e04c3a2d6a1'),
					'single' => array('key' => 'SINGLE', 'name' => 'Single', 'active' => 1, 'guid' => 'cbc78468-ce05-4ed3-9b9f-841478ce898f'),
					'unknown' => array('key' => 'UNKNOWN', 'name' => 'Unknown', 'active' => 1, 'guid' => '6e00691e-fb77-42fa-922b-d627722d8ac7'),
					'widowed' => array('key' => 'WIDOWED', 'name' => 'Widowed', 'active' => 1, 'guid' => '92f8d824-8733-44f2-a4b7-6354ffe38bec'),
				)),
				'confidentiality' => array('key' => 'CONFIDENT', 'name' => 'Confidentiality', 'active' => 1, 'guid' => '6ee1982a-da8f-413d-acf9-28cd974413f8', 'data' => array(
					'nosr' => array('key' => 'NOSR', 'name' => 'No Special Restrictions', 'active' => 1, 'guid' => 'c9ec5e4f-3fc9-4c6c-ac71-e5d7dec06fd5'),
					'basiconfi' => array('key' => 'BASICCONFI', 'name' => 'Basic Confidentiality', 'active' => 1, 'guid' => 'f83448df-098a-423c-97f0-d5b857f04a22'),
					'familyPlanning' => array('key' => 'FAMILYPLAN', 'name' => 'Family Planning', 'active' => 1, 'guid' => '4a3d6137-7d49-44ec-903e-c4733e8fea5a'),
					'diseaseCon' => array('key' => 'DISEASECON', 'name' => 'Disease Confidentiality', 'active' => 1, 'guid' => '23847063-41bb-4bbb-8815-f2c85bdac6b9'),
					'diseaseFPC' => array('key' => 'DISEASEFPC', 'name' => 'Disease and Family Planning Confidentiality', 'active' => 1, 'guid' => 'a07ac157-f3f4-43a5-b174-f2afb30972ba'),
					'extremeCon' => array('key' => 'EXTREMECON', 'name' => 'Extreme Confidentiality', 'active' => 1, 'guid' => 'e6d61fbe-12b7-4168-bf20-40ad9b8ef779'),
				)),
				'gender' => array('key' => 'G', 'name' => 'Gender', 'active' => 1, 'guid' => '50defb03-238b-4368-8ec6-90443bec4116', 'data' => array(
					'male' => array('key' => 'M', 'name' => 'Male', 'active' => 1, 'guid' => '08c03472-ed8e-4abd-b39f-da55555d5a29'),
					'female' => array('key' => 'F', 'name' => 'Female', 'active' => 1, 'guid' => '45432165-571a-4ad7-bd27-6f294d9550b5'),
					'unknown' => array('key' => 'U', 'name' => 'Unknown', 'active' => 1, 'guid' => '0fedcdcf-7493-4cff-aaab-02970dae04e3'),
				)),
				'race' => array('key' => 'RACE', 'name' => 'Race', 'active' => 1, 'guid' => '5c0ef400-96c2-42c0-9001-12f1c6714c15', 'data' => array(
					array('key' => 'A', 'name' => 'Asian', 'active' => 1, 'guid' => 'a104556b-ee11-475f-856d-54cafbea720a'),
					array('key' => 'N', 'name' => 'Native Hawaiian', 'active' => 1, 'guid' => '34989969-9f1c-439e-8ab6-93ac44367fd1'),
					array('key' => 'P', 'name' => 'Other Pacific Islander', 'active' => 1, 'guid' => '9b867492-98d4-42c1-bde7-dc7df657419d'),
					array('key' => 'B', 'name' => 'Black / African American', 'active' => 1, 'guid' => '109f9caf-3fe1-493d-aeef-16076d79a08b'),
					array('key' => 'I', 'name' => 'American Indian / Alaska Native', 'active' => 1, 'guid' => 'c44c9482-84ea-4e46-bf21-4b269fe59e11'),
					array('key' => 'W', 'name' => 'White', 'active' => 1, 'guid' => '0dabb9f2-6cf5-4ef0-acee-9a657aa47045'),
					array('key' => 'M', 'name' => 'More than one race', 'active' => 1, 'guid' => '379acee8-baf5-42bc-a26b-151b5c5b8af0'),
					array('key' => 'E', 'name' => 'Unreported / Refused to Report', 'active' => 1, 'guid' => 'f087b164-1e8a-4d86-9774-e209b7dfb911'),
				)),
				'ethnicity' => array('key' => 'ETHNICITY', 'name' => 'Ethnicity', 'active' => 1, 'guid' => 'f50951df-90ee-42ad-b08a-1a7c8c5122c4', 'data' => array(
					array('key' => '1', 'name' => 'Hispanic/Latino', 'active' => 1, 'guid' => '5b90ddd4-8794-4d65-9237-1c2eebcb3537'),
					array('key' => '2', 'name' => 'Not Hispanic/Latino', 'active' => 1, 'guid' => '6f993301-ec4d-4945-a320-ccdd6bb048f9'),
					array('key' => '3', 'name' => 'Unreported / Refused to Report', 'active' => 1, 'guid' => '5ecc0088-f9f5-49d8-9fe4-fc4d51917fda'),
				)),
				'language' => array('key' => 'LANGUAGE', 'name' => 'Language', 'active' => 1, 'guid' => 'e452bcc4-a0f2-4f2f-bb95-66d670983748', 'data' => array(
					array('key' => 'ENGLISH', 'name' => 'English', 'active' => 1, 'guid' => '9512b3ea-1210-4b7b-a75a-f2047f3a2775'),
					array('key' => 'SPANISH', 'name' => 'Spanish', 'active' => 1, 'guid' => '20f08cf7-b573-49f8-b62b-0bad24d317d8'),
					array('key' => 'CHINESE', 'name' => 'Chinese', 'active' => 1, 'guid' => '45ce96c7-c241-4792-96e1-1434a4c74f27'),
					array('key' => 'JAPANESE', 'name' => 'Japanese', 'active' => 1, 'guid' => '8ef93020-0381-4ddc-a05e-ce44d95aa813'),
					array('key' => 'KOREAN', 'name' => 'Korean', 'active' => 1, 'guid' => '9848c766-295b-417e-9d02-c8e62e275880'),
					array('key' => 'PORTUGUESE', 'name' => 'Portuguese', 'active' => 1, 'guid' => '93a00654-a2e6-4a1f-80e9-5da28448981c'),
					array('key' => 'RUSSIAN', 'name' => 'Russian', 'active' => 1, 'guid' => 'f870f5a5-e54c-47a2-b9c6-cc7e631fb7a5'),
					array('key' => 'SIGN_LANG', 'name' => 'Sign Language', 'active' => 1, 'guid' => '0c77dd57-5c58-4595-b9b8-3795c010d956'),
					array('key' => 'VIETNAMESE', 'name' => 'Vietnamese', 'active' => 1, 'guid' => '0a79fe88-af3d-4ee8-8eb7-e8cb1c27c585'),
					array('key' => 'TAGALOG', 'name' => 'Tagalog', 'active' => 1, 'guid' => 'ae383284-facc-4b38-97d6-0d831664ddea'),
					array('key' => 'PUNJABI', 'name' => 'Punjabi', 'active' => 1, 'guid' => 'ac3ab282-231e-482d-8dce-2d04cc47f37f'),
					array('key' => 'HINDUSTANI', 'name' => 'Hindustani', 'active' => 1, 'guid' => '998ab60e-3f2e-4eda-b0cb-444a4ce284ca'),
					array('key' => 'ARMENIAN', 'name' => 'Armenian', 'active' => 1, 'guid' => 'a5024c20-7661-4404-9866-a4cfb8e32b5d'),
					array('key' => 'ARABIC', 'name' => 'Arabic', 'active' => 1, 'guid' => 'c1ef9422-2ac2-431a-b681-21e141f25db5'),
					array('key' => 'LAOTIAN', 'name' => 'Laotian', 'active' => 1, 'guid' => '00da3f22-67bc-4429-9a4c-806d5744b5f5'),
					array('key' => 'HMONG', 'name' => 'Hmong', 'active' => 1, 'guid' => '212d9021-afe6-4891-9812-d56843f01729'),
					array('key' => 'CAMBODIAN', 'name' => 'Cambodian', 'active' => 1, 'guid' => '53e39301-5998-4f09-914e-7c7421f255dd'),
					array('key' => 'FINNISH', 'name' => 'Finnish', 'active' => 1, 'guid' => 'e1d302bc-01f4-4bae-9c7f-80f31f017fac'),
					array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => 'a6661d4f-92cc-4b82-96b0-9f1689696a44'),
				)),
				'educationLevel' => array('key' => 'EDUC_LEVEL', 'name' => 'Education Level', 'active' => 1, 'guid' => 'a6661d4f-92cc-4b82-96b0-9f1689696a44', 'data' => array(
					array('key' => 'UNKNOWN', 'name' => 'Unknown', 'active' => 1, 'guid' => 'f737387e-599a-46dd-ab4c-7c0aadd13584'),
					array('key' => 'NONE-ILLIT', 'name' => 'None-illiterate', 'active' => 1, 'guid' => '92bfcada-ed34-4044-92ef-13d4159e9323'),
					array('key' => 'SOME_ELEM', 'name' => 'Some Elementary Education', 'active' => 1, 'guid' => '32551954-6c18-472d-8ecc-70e1d70f00a2'),
					array('key' => 'SOME_MID', 'name' => 'Some Middle School', 'active' => 1, 'guid' => '58fb8a74-4e2a-45dd-8b89-9b2dbf2b6f04'),
					array('key' => 'SOME_HIGH', 'name' => 'Some High School', 'active' => 1, 'guid' => 'eb5acb09-9ddf-4885-9364-e99e09cb3439'),
					array('key' => 'HIGHSCHOOL', 'name' => 'High School Degree', 'active' => 1, 'guid' => '538b0d13-53f3-4226-aa68-b2888f648dd2'),
					array('key' => 'TECHSCHOOL', 'name' => 'Vocational/Tech School', 'active' => 1, 'guid' => 'fd89f00c-bbc0-4eb4-8cb2-14da21fe551b'),
					array('key' => 'COLLEGE', 'name' => 'Some College', 'active' => 1, 'guid' => 'c1d862f3-d1e9-4bad-9023-c9a9d265a3fa'),
					array('key' => 'ASSOC_DEG', 'name' => 'Associates Degree', 'active' => 1, 'guid' => '52110bf3-91c6-4d3f-a264-425296ba9c38'),
					array('key' => 'BACHELORS', 'name' => 'Bachelors Degree', 'active' => 1, 'guid' => 'd34110cf-f939-4613-9f48-2dc287f57644'),
					array('key' => 'POST_GRAD', 'name' => 'Post Grad College', 'active' => 1, 'guid' => 'b38794fd-efca-4849-97f3-6d63f8ec49b4'),
					array('key' => 'MASTERS', 'name' => 'Masters Degree', 'active' => 1, 'guid' => '84bfe1c2-4886-4c18-8300-2efed7fbb985'),
					array('key' => 'ADVANCED', 'name' => 'Advanced Degree', 'active' => 1, 'guid' => 'cb052923-6500-47e0-ad86-2d789d271ebe'),
					array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => 'e280299d-3cc7-45c4-badd-02a0a47b625e'),
				)),
				'migrantStatus' => array('key' => 'MIG_STAT', 'name' => 'Migrant Status', 'active' => 1, 'guid' => '916a01eb-c140-4a64-a0a8-235a5e31d56e', 'data' => array(
					array('key' => 'MIGRANT', 'name' => 'Migrant Worker', 'active' => 1, 'guid' => 'b0f7fad4-30f4-41fb-b1f8-aec3fbf7a23a'),
					array('key' => 'SEASONAL', 'name' => 'Seasonal Worker', 'active' => 1, 'guid' => '7a1cfada-f7e4-4f17-a03f-b05f324eabd6'),
					array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => '797b624c-5faa-4a4e-95a6-ae7a0e5bb7da'),
				)),
				'income' => array('key' => 'INCOME', 'name' => 'Income', 'active' => 1, 'guid' => 'f98a8288-60c3-4c8d-a695-af4c1b183bf4', 'data' => array(
					array('key' => 'BELOW100', 'name' => '100% and below of Poverty', 'active' => 1, 'guid' => '24c7b86c-df14-4aea-ba77-1f7b171988bd'),
					array('key' => 'BET101-150', 'name' => '101-150% of Poverty', 'active' => 1, 'guid' => '0db029bf-454c-4ecf-b36a-f45b6d1fcf4d'),
					array('key' => 'BET151-200', 'name' => '151-200% of Poverty', 'active' => 1, 'guid' => '10c45862-3d03-4cb4-903c-9efd33fe3500'),
					array('key' => 'OVER200', 'name' => 'Over 200% of Poverty', 'active' => 1, 'guid' => 'b3c363e7-2070-46d8-8ce7-fe58f8fe04aa'),
					array('key' => 'UNKNOWN', 'name' => 'Unknown', 'active' => 1, 'guid' => '5ab4a027-eabd-4dd6-b5de-3e102f5540ab'),
				)),
				'employmentStatus' => array('key' => 'EMP_STATUS', 'name' => 'Employment Status', 'active' => 1, 'guid' => 'a14c35ce-3ac7-4283-9343-290f74da330d', 'data' => array(
					array('key' => 'EMPLOYED', 'name' => 'Employed', 'active' => 1, 'guid' => 'da7effc9-4ea2-49d1-973a-de2ef96cc39f'),
					array('key' => 'UNEMPLOYED', 'name' => 'Unemployed', 'active' => 1, 'guid' => '3ff2f365-d370-4097-b825-7c0f51bef1f3'),
					array('key' => 'UNKNOWN', 'name' => 'Unknown', 'active' => 1, 'guid' => '43bc63aa-79aa-48c3-a141-dded6bf62d3a'),
				)),
			);

			$level = array();
			$level['guid'] = '0ad33a7d-8b52-4cc3-bbe0-62a22dc5590a';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array();
			$data[] = $level;

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateGeographyPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Geography';
			$key = 'GEOGRAPH';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'countries' => array('key' => 'COUNTRIES', 'name' => 'Countries', 'active' => 1, 'guid' => '26efee04-703b-470c-af73-d84dcb834d2c', 'data' => array(
					array('key' => 'AFG', 'name' => 'Afghanistan', 'active' => 1, 'guid' => '59079755-0cb5-4272-a23a-3c238388c427'),
					array('key' => 'ALB', 'name' => 'Albania', 'active' => 1, 'guid' => 'f441d4a3-aa89-4d1d-87f3-25700c4d1eaf'),
					array('key' => 'DZA', 'name' => 'Algeria', 'active' => 1, 'guid' => 'd570a0e4-578a-41af-90b0-aeff3383b193'),
					array('key' => 'ASM', 'name' => 'American Samoa', 'active' => 1, 'guid' => 'beec919e-d98a-441c-b953-07db61a9b91d'),
					array('key' => 'AND', 'name' => 'Andorra', 'active' => 1, 'guid' => '8e120ea1-f6fb-47d4-9801-30402a912a5f'),
					array('key' => 'AGO', 'name' => 'Angola', 'active' => 1, 'guid' => '4d999913-b94a-4aea-89b3-fa80d9c320cb'),
					array('key' => 'AIA', 'name' => 'Anguilla', 'active' => 1, 'guid' => '361e7264-b860-4d38-a3e7-a5c77bf124a1'),
					array('key' => 'ATA', 'name' => 'Antarctica', 'active' => 1, 'guid' => 'cec64974-3e91-4d95-bf8a-e6234ceaa182'),
					array('key' => 'ATG', 'name' => 'Antigua and Barbuda', 'active' => 1, 'guid' => 'bbed9bfc-931f-45ea-924a-46876a66aa90'),
					array('key' => 'ARG', 'name' => 'Argentina', 'active' => 1, 'guid' => '8fde6a26-6b8a-49c1-8644-2e2be1fa06e7'),
					array('key' => 'ARM', 'name' => 'Armenia', 'active' => 1, 'guid' => '50cd2232-ef12-463a-b907-4730a2537bee'),
					array('key' => 'ABW', 'name' => 'Aruba', 'active' => 1, 'guid' => '995a082e-d492-4d82-b0e9-081a2c1ad571'),
					array('key' => 'AUS', 'name' => 'Australia', 'active' => 1, 'guid' => '60127330-6f79-422f-bbf0-3a7b6e3bcd48'),
					array('key' => 'AUT', 'name' => 'Austria', 'active' => 1, 'guid' => '0a820d72-3598-4334-8af9-6899b24d7857'),
					array('key' => 'AZE', 'name' => 'Azerbaijan', 'active' => 1, 'guid' => 'a98fa643-3ade-4409-84ae-2cfda4698b74'),
					array('key' => 'BHS', 'name' => 'Bahamas', 'active' => 1, 'guid' => '71878d10-8746-49a0-a85e-d530e9cf79a6'),
					array('key' => 'BHR', 'name' => 'Bahrain', 'active' => 1, 'guid' => '0a687c35-d4ba-49ec-9c7c-e3c0c2e2a33b'),
					array('key' => 'BGD', 'name' => 'Bangladesh', 'active' => 1, 'guid' => '1e50ad80-cd51-429b-8df0-52674842878a'),
					array('key' => 'BRB', 'name' => 'Barbados', 'active' => 1, 'guid' => '98ef6373-8ef3-4694-9b94-1a9d6de7ef71'),
					array('key' => 'BLR', 'name' => 'Belarus', 'active' => 1, 'guid' => '61793a0d-35cc-4a4d-906c-1f85f47bdb3e'),
					array('key' => 'BEL', 'name' => 'Belgium', 'active' => 1, 'guid' => 'c13ff74b-5771-4303-9339-06c998e9157f'),
					array('key' => 'BLZ', 'name' => 'Belize', 'active' => 1, 'guid' => '1e1b5f88-84bc-443c-91c4-7578d2d41645'),
					array('key' => 'BEN', 'name' => 'Benin', 'active' => 1, 'guid' => '81ea7a21-1b1d-479b-9e45-f9455337c6d1'),
					array('key' => 'BMU', 'name' => 'Bermuda', 'active' => 1, 'guid' => 'e6c26c20-df0c-41a4-a4a5-d90b6ab018e3'),
					array('key' => 'BTN', 'name' => 'Bhutan', 'active' => 1, 'guid' => '3879739e-ac0e-488c-951c-a1ad4f22921c'),
					array('key' => 'BOL', 'name' => 'Bolivia', 'active' => 1, 'guid' => '141aa705-90c2-41c2-b92b-170c569b771c'),
					array('key' => 'BIH', 'name' => 'Bosnia and Herzegovina', 'active' => 1, 'guid' => 'f04afea9-285f-40d6-82c2-f88f069b682d'),
					array('key' => 'BWA', 'name' => 'Botswana', 'active' => 1, 'guid' => 'ddefacfc-c6a5-4962-9609-7e21aca058b9'),
					array('key' => 'BVT', 'name' => 'Bouvet Island', 'active' => 1, 'guid' => 'e31d667f-8cee-4aa9-b344-7f7247b12057'),
					array('key' => 'BRA', 'name' => 'Brazil', 'active' => 1, 'guid' => '18111feb-c002-42dc-9656-8777d15230d5'),
					array('key' => 'IOT', 'name' => 'British Indian Ocean Territory', 'active' => 1, 'guid' => '06337256-6669-4e45-b041-d5ffe4b9d694'),
					array('key' => 'BRN', 'name' => 'Brunei Darussalam', 'active' => 1, 'guid' => '8d9d0232-e22a-4222-9470-2981259e3104'),
					array('key' => 'BGR', 'name' => 'Bulgaria', 'active' => 1, 'guid' => '030a96e9-f771-443f-bfb1-c2886ad1d7e2'),
					array('key' => 'BFA', 'name' => 'Burkina Faso', 'active' => 1, 'guid' => 'f805d211-c37c-419f-bce0-c078e0067622'),
					array('key' => 'BDI', 'name' => 'Burundi', 'active' => 1, 'guid' => 'b379bf6b-324b-4aae-ac9b-71ea90d138dc'),
					array('key' => 'KHM', 'name' => 'Cambodia', 'active' => 1, 'guid' => '2209f775-ec5e-4732-aa03-84f051a3d01e'),
					array('key' => 'CMR', 'name' => 'Cameroon', 'active' => 1, 'guid' => '0764c7e2-112f-4911-805a-ae3d57180cb8'),
					array('key' => 'CAN', 'name' => 'Canada', 'active' => 1, 'guid' => '8b4b4df0-3032-485d-b4b3-c3499fe1b867'),
					array('key' => 'CPV', 'name' => 'Cape Verde', 'active' => 1, 'guid' => 'c0ddd36b-7635-461c-8865-3bd90c2326b5'),
					array('key' => 'CYM', 'name' => 'Cayman Islands', 'active' => 1, 'guid' => '474ff376-174f-4d0a-bb1d-17dacca4220c'),
					array('key' => 'CAF', 'name' => 'Central African Republic', 'active' => 1, 'guid' => 'b278f200-c589-47e1-8e4e-eb4c9061cb83'),
					array('key' => 'TCD', 'name' => 'Chad', 'active' => 1, 'guid' => '219c6ebf-14b2-41e2-85f7-fcde9830c3a4'),
					array('key' => 'CHL', 'name' => 'Chile', 'active' => 1, 'guid' => '1852306e-4cd6-44dd-9b42-261b7b10cc21'),
					array('key' => 'CHN', 'name' => 'China', 'active' => 1, 'guid' => 'e5718f62-4e6a-406e-b28f-9a288cd62e77'),
					array('key' => 'CXR', 'name' => 'Christmas Island', 'active' => 1, 'guid' => '255b84cf-bb3b-4f59-89a4-46d84eb82c22'),
					array('key' => 'CCK', 'name' => 'Cocos (Keeling) Islands', 'active' => 1, 'guid' => 'e1d679d0-cf2d-458e-ba1f-a65bfd2280d8'),
					array('key' => 'COL', 'name' => 'Colombia', 'active' => 1, 'guid' => 'e3cf9fb1-fd22-469f-bb98-1438caee921f'),
					array('key' => 'COM', 'name' => 'Comoros', 'active' => 1, 'guid' => '4d1f6b9a-da3d-4e29-ba7f-3c8e3f8c9d9b'),
					array('key' => 'COG', 'name' => 'Congo', 'active' => 1, 'guid' => '20e0b416-e05a-4c9a-aed3-4dbe16d018cd'),
					array('key' => 'COD', 'name' => 'Congo, the Democratic Republic of the', 'active' => 1, 'guid' => '0aae6f84-d23a-4c49-9526-9366ba92fe4e'),
					array('key' => 'COK', 'name' => 'Cook Islands', 'active' => 1, 'guid' => 'd3bd4805-69cf-4947-86e7-4e50d4cb792d'),
					array('key' => 'CRI', 'name' => 'Costa Rica', 'active' => 1, 'guid' => 'b650b1c8-cceb-4d29-b700-ec0114c8318a'),
					array('key' => 'CIV', 'name' => 'Cote D\'Ivoire', 'active' => 1, 'guid' => 'c513fa33-82aa-4ea9-997e-2f6a3ef847a3'),
					array('key' => 'HRV', 'name' => 'Croatia', 'active' => 1, 'guid' => 'a7abe37d-ff7c-44a5-93db-f327c1e2b0a2'),
					array('key' => 'CUB', 'name' => 'Cuba', 'active' => 1, 'guid' => '8e7d2b1e-a947-4ce0-bcbb-32f8ec0ac005'),
					array('key' => 'CYP', 'name' => 'Cyprus', 'active' => 1, 'guid' => '4d0ef80d-5924-4b4d-be7c-5b99c79c97e1'),
					array('key' => 'CZE', 'name' => 'Czech Republic', 'active' => 1, 'guid' => '8e590715-a55a-41a0-91ed-3a492b140a60'),
					array('key' => 'DNK', 'name' => 'Denmark', 'active' => 1, 'guid' => '60af0e28-32ae-411b-91c7-e363d2be50ac'),
					array('key' => 'DJI', 'name' => 'Djibouti', 'active' => 1, 'guid' => '89d38463-cdf7-4fc3-a7c2-a9a3e7081fbd'),
					array('key' => 'DMA', 'name' => 'Dominica', 'active' => 1, 'guid' => '90ddde4c-2c23-4a42-81ef-aac79d01f122'),
					array('key' => 'DOM', 'name' => 'Dominican Republic', 'active' => 1, 'guid' => '91e97f1f-03a4-413d-9e72-ae8cdc151256'),
					array('key' => 'ECU', 'name' => 'Ecuador', 'active' => 1, 'guid' => 'd255498a-d6a5-4a6c-840c-27f26e549e51'),
					array('key' => 'EGY', 'name' => 'Egypt', 'active' => 1, 'guid' => '11820701-d0aa-46b6-b599-de8ca6c6d36a'),
					array('key' => 'SLV', 'name' => 'El Salvador', 'active' => 1, 'guid' => 'aa9aa50a-9922-4a69-9a18-6776fd2d8197'),
					array('key' => 'GNQ', 'name' => 'Equatorial Guinea', 'active' => 1, 'guid' => 'd4c25883-0730-4d9f-8019-37a7ecdda403'),
					array('key' => 'ERI', 'name' => 'Eritrea', 'active' => 1, 'guid' => '7b379e45-4290-401d-a595-add28c719b2e'),
					array('key' => 'EST', 'name' => 'Estonia', 'active' => 1, 'guid' => '454ce628-c018-4101-a714-8bda06596a93'),
					array('key' => 'ETH', 'name' => 'Ethiopia', 'active' => 1, 'guid' => '030c26ac-cbb3-4ec0-8d1c-05267f4d987a'),
					array('key' => 'FLK', 'name' => 'Falkland Islands (Malvinas)', 'active' => 1, 'guid' => 'ad475921-80ca-4bec-9cd8-10f10bc1aecf'),
					array('key' => 'FRO', 'name' => 'Faroe Islands', 'active' => 1, 'guid' => '7138532c-b5f8-47de-9eae-3d078e56fc0d'),
					array('key' => 'FJI', 'name' => 'Fiji', 'active' => 1, 'guid' => 'ce65b2c7-8f76-44b2-9988-e75c86124bb0'),
					array('key' => 'FIN', 'name' => 'Finland', 'active' => 1, 'guid' => '699cf3c9-dcde-4c8e-9366-413f3a8f6018'),
					array('key' => 'FRA', 'name' => 'France', 'active' => 1, 'guid' => '21367e82-8de7-4852-a24a-a794b76e54e4'),
					array('key' => 'GUF', 'name' => 'French Guiana', 'active' => 1, 'guid' => '3ba60b80-e873-43cd-ba54-4737223b9369'),
					array('key' => 'PYF', 'name' => 'French Polynesia', 'active' => 1, 'guid' => '34e062db-f494-47cb-87ce-a62bd84d36a5'),
					array('key' => 'ATF', 'name' => 'French Southern Territories', 'active' => 1, 'guid' => 'f098f9e3-4204-4bf2-a281-0b1684fc3016'),
					array('key' => 'GAB', 'name' => 'Gabon', 'active' => 1, 'guid' => '13147b36-afb7-40a3-a875-4df2b3e3c87b'),
					array('key' => 'GMB', 'name' => 'Gambia', 'active' => 1, 'guid' => '461c5cef-2458-4a58-b497-61a5ce29423e'),
					array('key' => 'GEO', 'name' => 'Georgia', 'active' => 1, 'guid' => '1f5e929e-788a-4046-8712-b7a9a0f8df14'),
					array('key' => 'DEU', 'name' => 'Germany', 'active' => 1, 'guid' => '256c4d50-600d-4e15-b3dc-ad4a6a97fc4c'),
					array('key' => 'GHA', 'name' => 'Ghana', 'active' => 1, 'guid' => '5c0e1cc0-9148-4104-861a-0c25ba98e39f'),
					array('key' => 'GIB', 'name' => 'Gibraltar', 'active' => 1, 'guid' => '7bb6f5a3-4352-4a24-997f-d5e29a0bc929'),
					array('key' => 'GRC', 'name' => 'Greece', 'active' => 1, 'guid' => '29496d8c-6578-4c31-81fa-4b7a277904ae'),
					array('key' => 'GRL', 'name' => 'Greenland', 'active' => 1, 'guid' => '9960e854-698b-4ca4-b299-8796189034b8'),
					array('key' => 'GRD', 'name' => 'Grenada', 'active' => 1, 'guid' => '1aa0e3d4-d9ed-49b9-adea-112dc9f5f80d'),
					array('key' => 'GLP', 'name' => 'Guadeloupe', 'active' => 1, 'guid' => '1a7990f0-f4ca-4b74-bdee-64d130f157d7'),
					array('key' => 'GUM', 'name' => 'Guam', 'active' => 1, 'guid' => '5071075a-ad7b-4f10-b3cf-d16a8d437223'),
					array('key' => 'GTM', 'name' => 'Guatemala', 'active' => 1, 'guid' => '4a56c1ea-25a7-485a-88d6-54360b22f87a'),
					array('key' => 'GIN', 'name' => 'Guinea', 'active' => 1, 'guid' => 'a316a10e-053d-483b-a3e4-7ef746347cf8'),
					array('key' => 'GNB', 'name' => 'Guinea-Bissau', 'active' => 1, 'guid' => 'ec785d71-d628-4164-adc7-24b9af595ac9'),
					array('key' => 'GUY', 'name' => 'Guyana', 'active' => 1, 'guid' => 'b9951b6f-d6d0-4093-8601-7049500a6ea4'),
					array('key' => 'HTI', 'name' => 'Haiti', 'active' => 1, 'guid' => '406b24a4-e3a0-442c-807f-56e6d8ab5eb0'),
					array('key' => 'HMD', 'name' => 'Heard Island and Mcdonald Islands', 'active' => 1, 'guid' => 'a2d63cdb-5473-4f8b-8ece-f968d2af5552'),
					array('key' => 'VAT', 'name' => 'Holy See (Vatican City State)', 'active' => 1, 'guid' => 'd128ad6f-c3e1-4bc2-b7ae-93d736f521e0'),
					array('key' => 'HND', 'name' => 'Honduras', 'active' => 1, 'guid' => '987eaf69-2243-4ba0-9d6c-9d634191a7e9'),
					array('key' => 'HKG', 'name' => 'Hong Kong', 'active' => 1, 'guid' => 'd9e94401-358d-4b07-a21b-9b5b175436b4'),
					array('key' => 'HUN', 'name' => 'Hungary', 'active' => 1, 'guid' => '75f5f7a4-f86a-4b5c-a5a5-2601aaa9bbf2'),
					array('key' => 'ISL', 'name' => 'Iceland', 'active' => 1, 'guid' => 'a760af6a-0a0b-49e2-bd63-ee454f7c93af'),
					array('key' => 'IND', 'name' => 'India', 'active' => 1, 'guid' => '72c50461-b8f6-48d8-a80d-5081d127e1a4'),
					array('key' => 'IDN', 'name' => 'Indonesia', 'active' => 1, 'guid' => 'b637b9bb-b66e-4db1-9636-899140baabd6'),
					array('key' => 'IRN', 'name' => 'Iran, Islamic Republic of', 'active' => 1, 'guid' => '6b2ea029-e97a-4ba2-a94d-a02e074986e8'),
					array('key' => 'IRQ', 'name' => 'Iraq', 'active' => 1, 'guid' => '68ed3cc1-1a70-449d-9382-1a6b3d91e687'),
					array('key' => 'IRL', 'name' => 'Ireland', 'active' => 1, 'guid' => '0aee7705-d0e1-4f52-9717-129f3b13b8de'),
					array('key' => 'ISR', 'name' => 'Israel', 'active' => 1, 'guid' => 'a0f00f25-f05a-4f44-997c-a63bd501a033'),
					array('key' => 'ITA', 'name' => 'Italy', 'active' => 1, 'guid' => '74f3bb3a-c20f-42fc-910a-ad37e37b26ab'),
					array('key' => 'JAM', 'name' => 'Jamaica', 'active' => 1, 'guid' => '5ae3293f-874a-4236-b02c-4f936dcbd5da'),
					array('key' => 'JPN', 'name' => 'Japan', 'active' => 1, 'guid' => 'b3463578-aba7-4398-8d56-d9f489cd57a2'),
					array('key' => 'JOR', 'name' => 'Jordan', 'active' => 1, 'guid' => 'a2c7b192-7458-470f-86b1-6276d763bc91'),
					array('key' => 'KAZ', 'name' => 'Kazakhstan', 'active' => 1, 'guid' => 'efcb3f83-6682-4252-b1f6-4904242d49db'),
					array('key' => 'KEN', 'name' => 'Kenya', 'active' => 1, 'guid' => 'a781e5e6-61f4-4e74-9f1f-d1afca1f3b9b'),
					array('key' => 'KIR', 'name' => 'Kiribati', 'active' => 1, 'guid' => 'b27ac789-f5f8-43d2-afbc-90615c373807'),
					array('key' => 'PRK', 'name' => 'Korea, Democratic People\'s Republic of', 'active' => 1, 'guid' => 'bb2ec30a-6ce5-4eec-9140-b1fee7dc4ede'),
					array('key' => 'KOR', 'name' => 'Korea, Republic of', 'active' => 1, 'guid' => '5d46e5cb-334b-4320-a5fd-41ab72ecdba5'),
					array('key' => 'KWT', 'name' => 'Kuwait', 'active' => 1, 'guid' => '2f1de267-142c-4b44-8456-de5bd5be4e0a'),
					array('key' => 'KGZ', 'name' => 'Kyrgyzstan', 'active' => 1, 'guid' => '9306f965-dab1-4d49-909b-83ce44beec16'),
					array('key' => 'LAO', 'name' => 'Lao People\'s Democratic Republic', 'active' => 1, 'guid' => '5fb27190-a139-4f76-8391-4d5335549051'),
					array('key' => 'LVA', 'name' => 'Latvia', 'active' => 1, 'guid' => '8e94bdf2-edb9-4bb8-b2e6-c760466706a6'),
					array('key' => 'LBN', 'name' => 'Lebanon', 'active' => 1, 'guid' => 'c9b3e7ee-e3a7-45ab-a42d-b4958fc73a47'),
					array('key' => 'LSO', 'name' => 'Lesotho', 'active' => 1, 'guid' => 'b79277cd-c9cf-4b93-ac12-0ea2c314a7d7'),
					array('key' => 'LBR', 'name' => 'Liberia', 'active' => 1, 'guid' => 'd3adc442-064f-40da-8b81-efd7857493ec'),
					array('key' => 'LBY', 'name' => 'Libyan Arab Jamahiriya', 'active' => 1, 'guid' => 'ef0e69f7-d57c-4bd3-943c-12aa20cfd4a2'),
					array('key' => 'LIE', 'name' => 'Liechtenstein', 'active' => 1, 'guid' => 'bf0936e4-472b-4914-8838-54475a089b69'),
					array('key' => 'LTU', 'name' => 'Lithuania', 'active' => 1, 'guid' => 'b211166d-f926-44a2-bcf6-10fb1f2ce61e'),
					array('key' => 'LUX', 'name' => 'Luxembourg', 'active' => 1, 'guid' => 'ad36240f-d856-424d-919a-c1bc20683587'),
					array('key' => 'MAC', 'name' => 'Macao', 'active' => 1, 'guid' => 'f4596815-e19e-4b7b-9084-c82ea7aa9db3'),
					array('key' => 'MKD', 'name' => 'Macedonia, the Former Yugoslav Republic of', 'active' => 1, 'guid' => 'bf85d7c8-5f7c-4b02-9428-be20b731aaff'),
					array('key' => 'MDG', 'name' => 'Madagascar', 'active' => 1, 'guid' => '54f82771-2189-4ee2-a7d4-469830d64b70'),
					array('key' => 'MWI', 'name' => 'Malawi', 'active' => 1, 'guid' => '58a0f404-af02-49c2-a4d8-af14395c08e4'),
					array('key' => 'MYS', 'name' => 'Malaysia', 'active' => 1, 'guid' => '25ca855d-f809-40a9-9f7c-f434c36aac2c'),
					array('key' => 'MDV', 'name' => 'Maldives', 'active' => 1, 'guid' => 'f750d787-db0e-45b5-83ac-9586d1cc2bfd'),
					array('key' => 'MLI', 'name' => 'Mali', 'active' => 1, 'guid' => 'a897911f-e844-4dbc-b6b2-b627465bddc0'),
					array('key' => 'MLT', 'name' => 'Malta', 'active' => 1, 'guid' => '6ebccdfb-0b2b-4d47-b257-2578cbcf2051'),
					array('key' => 'MHL', 'name' => 'Marshall Islands', 'active' => 1, 'guid' => '8acbcd15-6d93-4184-aa4e-18123a5b4095'),
					array('key' => 'MTQ', 'name' => 'Martinique', 'active' => 1, 'guid' => 'ad2fad19-ab4e-4f29-ab2f-49e87adbac87'),
					array('key' => 'MRT', 'name' => 'Mauritania', 'active' => 1, 'guid' => '552a99de-28bb-49c0-a69f-60c9c779184a'),
					array('key' => 'MUS', 'name' => 'Mauritius', 'active' => 1, 'guid' => 'e1d5d72c-b369-48cc-bc12-b86d79c65db8'),
					array('key' => 'MYT', 'name' => 'Mayotte', 'active' => 1, 'guid' => 'a7847483-2ac9-4b04-8388-67dfefd8944b'),
					array('key' => 'MEX', 'name' => 'Mexico', 'active' => 1, 'guid' => '70ad5611-56a8-4969-8b6f-356cc6907887'),
					array('key' => 'FSM', 'name' => 'Micronesia, Federated States of', 'active' => 1, 'guid' => 'c35c7284-ecf4-417f-a8c9-ceea9d5594b8'),
					array('key' => 'MDA', 'name' => 'Moldova, Republic of', 'active' => 1, 'guid' => '7dd318cb-6dcc-43b1-a8df-d8ed8a74c988'),
					array('key' => 'MCO', 'name' => 'Monaco', 'active' => 1, 'guid' => 'b9570610-6ebe-46c7-8c4b-8115820005ec'),
					array('key' => 'MNG', 'name' => 'Mongolia', 'active' => 1, 'guid' => '5de2ada1-90ed-42ce-a06d-60cfec8f8cad'),
					array('key' => 'MSR', 'name' => 'Montserrat', 'active' => 1, 'guid' => '5095c252-f88e-46d5-a71c-ddc60d8618d5'),
					array('key' => 'MAR', 'name' => 'Morocco', 'active' => 1, 'guid' => 'c197ed91-1dfc-4a69-bdc8-8b50c094c2b0'),
					array('key' => 'MOZ', 'name' => 'Mozambique', 'active' => 1, 'guid' => 'bacf3b99-1731-4671-8d6b-659b389ee8fc'),
					array('key' => 'MMR', 'name' => 'Myanmar', 'active' => 1, 'guid' => '9e16a796-e4d0-40c5-955a-38c940c2cf35'),
					array('key' => 'NAM', 'name' => 'Namibia', 'active' => 1, 'guid' => '5eeab97a-c385-4875-9e63-de087e8d6e56'),
					array('key' => 'NRU', 'name' => 'Nauru', 'active' => 1, 'guid' => '90477e7c-984e-4f3c-b7fb-8aa054b2ab59'),
					array('key' => 'NPL', 'name' => 'Nepal', 'active' => 1, 'guid' => 'cf182319-5ebd-416d-aaa2-30b72ce49404'),
					array('key' => 'NLD', 'name' => 'Netherlands', 'active' => 1, 'guid' => 'e8a94c68-be33-456d-a7cd-d305e2febf50'),
					array('key' => 'ANT', 'name' => 'Netherlands Antilles', 'active' => 1, 'guid' => '6df8407a-2f71-4b62-a2e4-fae401e9e256'),
					array('key' => 'NCL', 'name' => 'New Caledonia', 'active' => 1, 'guid' => '2b984304-a5a1-4bb1-8fbd-4427abb1389a'),
					array('key' => 'NZL', 'name' => 'New Zealand', 'active' => 1, 'guid' => '1c56836b-f902-4a98-8c98-9ad4d660757f'),
					array('key' => 'NIC', 'name' => 'Nicaragua', 'active' => 1, 'guid' => 'c60d12b3-e550-4f4e-ae4c-7354fccc51d9'),
					array('key' => 'NER', 'name' => 'Niger', 'active' => 1, 'guid' => '76a1169e-7fe7-4d89-a3ef-9d409e2d9157'),
					array('key' => 'NGA', 'name' => 'Nigeria', 'active' => 1, 'guid' => '5b53f414-aea5-4059-bc5e-a13678686a61'),
					array('key' => 'NIU', 'name' => 'Niue', 'active' => 1, 'guid' => '9c6450df-31ff-4bd6-959b-94ae49188318'),
					array('key' => 'NFK', 'name' => 'Norfolk Island', 'active' => 1, 'guid' => '7beada37-6ee5-47d0-b8ae-b1bd8386b3d1'),
					array('key' => 'MNP', 'name' => 'Northern Mariana Islands', 'active' => 1, 'guid' => '688ee0fa-f600-4981-b2db-4ab8601e45cf'),
					array('key' => 'NOR', 'name' => 'Norway', 'active' => 1, 'guid' => 'e60e596d-fddc-4878-b486-094bc408e26e'),
					array('key' => 'OMN', 'name' => 'Oman', 'active' => 1, 'guid' => 'dc5b36e9-ba65-4aa1-91d1-c00fc70fad5f'),
					array('key' => 'PAK', 'name' => 'Pakistan', 'active' => 1, 'guid' => '2a9e9d26-6189-410f-855e-fa295262f9dd'),
					array('key' => 'PLW', 'name' => 'Palau', 'active' => 1, 'guid' => 'ec135921-b5cc-470d-a1f2-eb8c92774756'),
					array('key' => 'PSE', 'name' => 'Palestinian Territory, Occupied', 'active' => 1, 'guid' => '14cbeb6b-81af-494c-8679-6347bedb1e2a'),
					array('key' => 'PAN', 'name' => 'Panama', 'active' => 1, 'guid' => '4781047a-72ce-4600-80e9-c0590ffc3bfc'),
					array('key' => 'PNG', 'name' => 'Papua New Guinea', 'active' => 1, 'guid' => '1dbd8e4c-d28e-4f67-9142-5c4f8ffc150b'),
					array('key' => 'PRY', 'name' => 'Paraguay', 'active' => 1, 'guid' => 'aabfb14b-aaf4-4384-8146-0aaa9d3f6312'),
					array('key' => 'PER', 'name' => 'Peru', 'active' => 1, 'guid' => 'fb4995c9-c8ff-4a42-9239-32908f1e1b12'),
					array('key' => 'PHL', 'name' => 'Philippines', 'active' => 1, 'guid' => '0a6acf26-de60-4152-babb-3751f2d63327'),
					array('key' => 'PCN', 'name' => 'Pitcairn', 'active' => 1, 'guid' => '13941a18-ff98-4cec-b9c0-236fd797fa35'),
					array('key' => 'POL', 'name' => 'Poland', 'active' => 1, 'guid' => '7e1a8a20-f9e3-4d67-8887-602f420f6172'),
					array('key' => 'PRT', 'name' => 'Portugal', 'active' => 1, 'guid' => '29cb3aa5-ac95-410f-a2ae-5af4e1e582f6'),
					array('key' => 'PRI', 'name' => 'Puerto Rico', 'active' => 1, 'guid' => 'feaae5a2-f927-4410-ae35-9dbbdd4c4e08'),
					array('key' => 'QAT', 'name' => 'Qatar', 'active' => 1, 'guid' => '6598ca07-ca58-4486-ad33-36ab007bbc0b'),
					array('key' => 'REU', 'name' => 'Reunion', 'active' => 1, 'guid' => '4cad5120-2f41-4736-92d9-04928a3cf34d'),
					array('key' => 'ROM', 'name' => 'Romania', 'active' => 1, 'guid' => '19afa441-f620-48a7-a1c2-78adfdafd884'),
					array('key' => 'RUS', 'name' => 'Russian Federation', 'active' => 1, 'guid' => '3552db8b-f83f-4ebd-af2c-ced58c46c00d'),
					array('key' => 'RWA', 'name' => 'Rwanda', 'active' => 1, 'guid' => 'cfe94f13-4db8-4340-9686-30d67cb114de'),
					array('key' => 'SHN', 'name' => 'Saint Helena', 'active' => 1, 'guid' => '62e7a732-8fb5-4cca-a632-d11f34b918fb'),
					array('key' => 'KNA', 'name' => 'Saint Kitts and Nevis', 'active' => 1, 'guid' => 'eb15399c-21eb-44cd-a9f7-22ad8892c424'),
					array('key' => 'LCA', 'name' => 'Saint Lucia', 'active' => 1, 'guid' => '0d0ee797-85af-4f5d-af8a-4b78e2c0e05b'),
					array('key' => 'SPM', 'name' => 'Saint Pierre and Miquelon', 'active' => 1, 'guid' => '7bf1bf8d-4fda-477b-9550-75edac6acdbf'),
					array('key' => 'VCT', 'name' => 'Saint Vincent and the Grenadines', 'active' => 1, 'guid' => 'b3d581a1-65bc-4c24-8126-2c11b7673fd9'),
					array('key' => 'WSM', 'name' => 'Samoa', 'active' => 1, 'guid' => '0b6d7503-0718-4fd3-a439-b1394a159df7'),
					array('key' => 'SMR', 'name' => 'San Marino', 'active' => 1, 'guid' => '28b778ba-f543-44b2-8f77-88f92ccbc7d6'),
					array('key' => 'STP', 'name' => 'Sao Tome and Principe', 'active' => 1, 'guid' => '7e5b8fb4-40fa-45a9-9196-9d7770a5ec5b'),
					array('key' => 'SAU', 'name' => 'Saudi Arabia', 'active' => 1, 'guid' => '7a59b7fd-2aca-4761-89c4-1ec363486d7e'),
					array('key' => 'SEN', 'name' => 'Senegal', 'active' => 1, 'guid' => 'ad6acbad-3043-4c06-82df-6fb765463fb3'),
					array('key' => 'SCG', 'name' => 'Serbia and Montenegro', 'active' => 1, 'guid' => 'b92bb4cf-931d-4dc3-a947-d43fb6271366'),
					array('key' => 'SYC', 'name' => 'Seychelles', 'active' => 1, 'guid' => '1024ace5-9192-4c04-8a72-fd0a37667877'),
					array('key' => 'SLE', 'name' => 'Sierra Leone', 'active' => 1, 'guid' => '85f982d5-f899-4707-8d2e-66359e2e6383'),
					array('key' => 'SGP', 'name' => 'Singapore', 'active' => 1, 'guid' => 'cd9c4592-e49f-4ba6-9b9a-a1fa6d3fdfd8'),
					array('key' => 'SVK', 'name' => 'Slovakia', 'active' => 1, 'guid' => '4580e435-5b2c-47c0-874e-9cf60a495be1'),
					array('key' => 'SVN', 'name' => 'Slovenia', 'active' => 1, 'guid' => '0e1a1c1a-f66e-4ea8-8dfd-cb62a015e66c'),
					array('key' => 'SLB', 'name' => 'Solomon Islands', 'active' => 1, 'guid' => '1b279a86-7e78-4d51-bf91-167382a18d4b'),
					array('key' => 'SOM', 'name' => 'Somalia', 'active' => 1, 'guid' => '6ac35ee4-92c6-4c8c-97b3-84f18041128a'),
					array('key' => 'ZAF', 'name' => 'South Africa', 'active' => 1, 'guid' => '5018fcc0-a6c3-46f3-b79b-7b4a20665436'),
					array('key' => 'SGS', 'name' => 'South Georgia and the South Sandwich Islands', 'active' => 1, 'guid' => '30055ebf-d266-4010-bed0-9267de111e75'),
					array('key' => 'ESP', 'name' => 'Spain', 'active' => 1, 'guid' => 'bf9fb9e4-c468-4308-b77d-39c7b9d743fc'),
					array('key' => 'LKA', 'name' => 'Sri Lanka', 'active' => 1, 'guid' => '5a8b7704-2867-4f64-a596-e38fcbe22f9c'),
					array('key' => 'SDN', 'name' => 'Sudan', 'active' => 1, 'guid' => '74521351-b11e-4be1-b02e-5c419d6bb960'),
					array('key' => 'SUR', 'name' => 'Suriname', 'active' => 1, 'guid' => '9ae29198-561b-4221-9d43-570bce26b986'),
					array('key' => 'SJM', 'name' => 'Svalbard and Jan Mayen', 'active' => 1, 'guid' => '1018e0d9-9217-47fb-b910-da30f9352327'),
					array('key' => 'SWZ', 'name' => 'Swaziland', 'active' => 1, 'guid' => 'b6ac0a07-b198-4bed-b01a-f40b9156b691'),
					array('key' => 'SWE', 'name' => 'Sweden', 'active' => 1, 'guid' => '9a48b2ae-31a6-4a01-834c-ba21b092ebcf'),
					array('key' => 'CHE', 'name' => 'Switzerland', 'active' => 1, 'guid' => '42e0cab8-ba23-492b-9e74-ae7f037704c2'),
					array('key' => 'SYR', 'name' => 'Syrian Arab Republic', 'active' => 1, 'guid' => 'd740e179-2cbe-4254-a9af-5729561e4b65'),
					array('key' => 'TWN', 'name' => 'Taiwan, Province of China', 'active' => 1, 'guid' => '770d4e58-219e-4ac4-a249-186d4b6e2b7c'),
					array('key' => 'TJK', 'name' => 'Tajikistan', 'active' => 1, 'guid' => '4c73aa4d-641f-4420-a6cb-1f5a6a0d537d'),
					array('key' => 'TZA', 'name' => 'Tanzania, United Republic of', 'active' => 1, 'guid' => 'bc99aaaf-78ac-4a74-ade1-863ce2c9d579'),
					array('key' => 'THA', 'name' => 'Thailand', 'active' => 1, 'guid' => '4d4be004-22e8-420b-9636-5d3cb3a02d85'),
					array('key' => 'TLS', 'name' => 'Timor-Leste', 'active' => 1, 'guid' => 'b7f238ba-1c63-4d93-98f0-df2963742135'),
					array('key' => 'TGO', 'name' => 'Togo', 'active' => 1, 'guid' => '6d702890-967d-48b8-898d-8b3d68b87e17'),
					array('key' => 'TKL', 'name' => 'Tokelau', 'active' => 1, 'guid' => '0d449d29-9a54-4a37-b07e-e3ec3a5a2aae'),
					array('key' => 'TON', 'name' => 'Tonga', 'active' => 1, 'guid' => 'e74574b0-444d-449a-9a71-e9d4ba8001ba'),
					array('key' => 'TTO', 'name' => 'Trinidad and Tobago', 'active' => 1, 'guid' => 'dc28ddf8-9745-477f-a256-9d6a349bdfc6'),
					array('key' => 'TUN', 'name' => 'Tunisia', 'active' => 1, 'guid' => '8cbeb09a-6156-4283-a8ac-bf4e1558bc7a'),
					array('key' => 'TUR', 'name' => 'Turkey', 'active' => 1, 'guid' => 'c493dfd8-546f-4730-b21f-5aa677cd9a31'),
					array('key' => 'TKM', 'name' => 'Turkmenistan', 'active' => 1, 'guid' => 'af2a3b89-0b70-4e6d-9891-c8cdceedd901'),
					array('key' => 'TCA', 'name' => 'Turks and Caicos Islands', 'active' => 1, 'guid' => '248773c9-afd0-4ce0-99c1-288e6aa1704b'),
					array('key' => 'TUV', 'name' => 'Tuvalu', 'active' => 1, 'guid' => 'bd8e43f4-e95e-4434-91fa-6893f6ddfe88'),
					array('key' => 'UGA', 'name' => 'Uganda', 'active' => 1, 'guid' => '26762ca6-a3fb-49c0-8d8e-c3eb7cf9b6ca'),
					array('key' => 'UKR', 'name' => 'Ukraine', 'active' => 1, 'guid' => '75a96410-804b-4f86-bed1-81fecbbcffa4'),
					array('key' => 'ARE', 'name' => 'United Arab Emirates', 'active' => 1, 'guid' => '0ff7cf53-0e2e-4ef9-9c57-74c53aacc0e4'),
					array('key' => 'GBR', 'name' => 'United Kingdom', 'active' => 1, 'guid' => '74534910-11d2-4baa-b18e-bc8bf955efb7'),
					array('key' => 'USA', 'name' => 'United States', 'active' => 1, 'guid' => 'abfb9a5e-4aec-4427-9aa2-32024ccf45fb'),
					array('key' => 'UMI', 'name' => 'United States Minor Outlying Islands', 'active' => 1, 'guid' => '0e34b404-9c95-4b11-ba82-90f41cdd8879'),
					array('key' => 'URY', 'name' => 'Uruguay', 'active' => 1, 'guid' => '412eb4f6-f245-45f2-be4a-bc31ba5d3286'),
					array('key' => 'UZB', 'name' => 'Uzbekistan', 'active' => 1, 'guid' => 'd6ba8567-72d2-4ced-9d33-8c21b588db30'),
					array('key' => 'VUT', 'name' => 'Vanuatu', 'active' => 1, 'guid' => '3bc4061d-4e57-4ca1-9382-5aaa041dc8e4'),
					array('key' => 'VEN', 'name' => 'Venezuela', 'active' => 1, 'guid' => '231b6ac9-50ef-460f-a39c-b97b4163e06a'),
					array('key' => 'VNM', 'name' => 'Viet Nam', 'active' => 1, 'guid' => '313dc28f-d2d7-454f-a6d7-5cc4d62c7cc1'),
					array('key' => 'VGB', 'name' => 'Virgin Islands, British', 'active' => 1, 'guid' => '680d1aec-5468-4f43-a339-a5553b4b7226'),
					array('key' => 'VIR', 'name' => 'Virgin Islands, U.s.', 'active' => 1, 'guid' => '8f4c1448-ff3b-4424-aa6e-c455c65d330c'),
					array('key' => 'WLF', 'name' => 'Wallis and Futuna', 'active' => 1, 'guid' => '2a0d077f-3cec-4e43-bf5a-aa5733046948'),
					array('key' => 'ESH', 'name' => 'Western Sahara', 'active' => 1, 'guid' => 'd41a099a-2254-44b2-b927-be095d4081fe'),
					array('key' => 'YEM', 'name' => 'Yemen', 'active' => 1, 'guid' => 'e6d71581-c8b0-4963-a8da-7358f0f99844'),
					array('key' => 'ZMB', 'name' => 'Zambia', 'active' => 1, 'guid' => 'b67897b3-759d-414c-ad14-3b85f8def51c'),
					array('key' => 'ZWE', 'name' => 'Zimbabwe', 'active' => 1, 'guid' => '8ad65878-a163-497f-b76b-5f60eba8f703'),
				)),
				'states' => array('key' => 'STATES', 'name' => 'States', 'active' => 1, 'guid' => '0c73c914-fa06-49e2-bee4-bfc401c7eb7d', 'data' => array(
					array('key' => 'AA', 'name' => 'Armed Forces Americas (except Canada)', 'active' => 1, 'guid' => 'ecd227a6-1638-4f07-90f8-1929a9a6003e'),
					array('key' => 'AE', 'name' => 'Armed Forces Africa', 'active' => 1, 'guid' => '1df59871-bf1d-47fa-b090-f274d07b15fb'),
					array('key' => 'AE', 'name' => 'Armed Forces Canada', 'active' => 1, 'guid' => '596f01e1-6e06-4eb2-9947-d8b55f0538af'),
					array('key' => 'AE', 'name' => 'Armed Forces Europe', 'active' => 1, 'guid' => '1901a4db-6503-43e7-8e67-b5259fef2287'),
					array('key' => 'AE', 'name' => 'Armed Forces Middle East', 'active' => 1, 'guid' => 'dc2bb09f-3794-49af-a5f1-c1fbf664a9ba'),
					array('key' => 'AK', 'name' => 'Alaska', 'active' => 1, 'guid' => '8555a23f-6ff4-4e25-a865-92b2c5e4d54b'),
					array('key' => 'AL', 'name' => 'Alabama', 'active' => 1, 'guid' => '75c7e26d-9574-42d9-a6cd-5042a8df4a24'),
					array('key' => 'AP', 'name' => 'Armed Forces Pacific', 'active' => 1, 'guid' => 'ca253f55-c68f-4e4d-9433-ff3bbc783492'),
					array('key' => 'AR', 'name' => 'Arkansas', 'active' => 1, 'guid' => '2668d703-fb3a-42e1-8316-196e749643e3'),
					array('key' => 'AS', 'name' => 'American Samoa', 'active' => 1, 'guid' => 'b976a76e-d770-4328-8e5f-8f7572df25ad'),
					array('key' => 'AZ', 'name' => 'Arizona', 'active' => 1, 'guid' => 'dc76211c-ba63-4cc1-adb5-2531048f09ac'),
					array('key' => 'CA', 'name' => 'California', 'active' => 1, 'guid' => '9839ba0b-f83c-4ac1-9074-66de738db898'),
					array('key' => 'CO', 'name' => 'Colorado', 'active' => 1, 'guid' => 'a5b6ba9c-830e-4c1b-857a-c95ac2f6e513'),
					array('key' => 'CT', 'name' => 'Connecticut', 'active' => 1, 'guid' => '7d1d9e6b-44e3-4f34-a1bd-de49226256cc'),
					array('key' => 'DC', 'name' => 'District of Columbia', 'active' => 1, 'guid' => 'ca8143ed-df49-4b46-a945-4648800f708a'),
					array('key' => 'DE', 'name' => 'Delaware', 'active' => 1, 'guid' => '6d1495f2-69ff-4132-bda6-a52832232fef'),
					array('key' => 'FL', 'name' => 'Florida', 'active' => 1, 'guid' => '266c9390-ed5e-4d4d-b5cd-2e8b0c0fe7ef'),
					array('key' => 'FM', 'name' => 'Federated States of Micronesia', 'active' => 1, 'guid' => '27ff4766-e719-45ba-8c7c-740049da927b'),
					array('key' => 'GA', 'name' => 'Georgia', 'active' => 1, 'guid' => '80dcf632-1722-4c61-8ad6-91476d47afe9'),
					array('key' => 'GU', 'name' => 'Guam', 'active' => 1, 'guid' => 'dd85bb58-137d-49d6-9f71-f218c9aaa757'),
					array('key' => 'HI', 'name' => 'Hawaii', 'active' => 1, 'guid' => '2b196db5-9475-47b1-bf60-5d932379e802'),
					array('key' => 'IA', 'name' => 'Iowa', 'active' => 1, 'guid' => '01dff972-d03d-4639-a12f-650870061039'),
					array('key' => 'ID', 'name' => 'Idaho', 'active' => 1, 'guid' => 'bba2640d-8986-4340-904e-c9e35e672a4c'),
					array('key' => 'IL', 'name' => 'Illinois', 'active' => 1, 'guid' => 'a84b0832-f0e7-450b-b7f8-f7ed906e3c2c'),
					array('key' => 'IN', 'name' => 'Indiana', 'active' => 1, 'guid' => '046bcc91-d4f6-4e38-9287-44267342b5dc'),
					array('key' => 'KS', 'name' => 'Kansas', 'active' => 1, 'guid' => 'a3af4c8e-47a1-4ae9-841d-ba85b669896b'),
					array('key' => 'KY', 'name' => 'Kentucky', 'active' => 1, 'guid' => '0e7dea81-34c3-4c11-b7a8-bb489a2f9b6b'),
					array('key' => 'LA', 'name' => 'Louisiana', 'active' => 1, 'guid' => 'c16dafd0-60b3-445b-a28f-ebf4ba1192bc'),
					array('key' => 'MA', 'name' => 'Massachusetts', 'active' => 1, 'guid' => '59108687-e09b-4091-9b26-9cbbee31c4d7'),
					array('key' => 'MD', 'name' => 'Maryland', 'active' => 1, 'guid' => '6f66ee30-d42d-4d1c-b7a2-e3facfd3c022'),
					array('key' => 'ME', 'name' => 'Maine', 'active' => 1, 'guid' => 'c707581f-50b5-4c58-bf8d-4635d4bbdae8'),
					array('key' => 'MH', 'name' => 'Marshall Islands', 'active' => 1, 'guid' => '4ff754cc-edf0-4320-aaec-29baa45fe427'),
					array('key' => 'MI', 'name' => 'Michigan', 'active' => 1, 'guid' => '189f978f-77d0-41c5-a839-00f1aa400eb2'),
					array('key' => 'MN', 'name' => 'Minnesota', 'active' => 1, 'guid' => '8d0895cf-eb35-42dd-86fa-06c2cb560857'),
					array('key' => 'MO', 'name' => 'Missouri', 'active' => 1, 'guid' => 'a7f06fe9-b508-407e-b0bb-12c00981118d'),
					array('key' => 'MP', 'name' => 'Northern Mariana Islands', 'active' => 1, 'guid' => '4335fbf2-1fef-4eaa-b512-fc93b00193fa'),
					array('key' => 'MS', 'name' => 'Mississippi', 'active' => 1, 'guid' => 'cc0c52a8-0058-45ec-98c4-9294a5b779b5'),
					array('key' => 'MT', 'name' => 'Montana', 'active' => 1, 'guid' => '6bf42f31-e470-4e24-af1c-40c957ca5994'),
					array('key' => 'NC', 'name' => 'North Carolina', 'active' => 1, 'guid' => '030f318b-fb1a-470e-962e-11ca14ece4c7'),
					array('key' => 'ND', 'name' => 'North Dakota', 'active' => 1, 'guid' => '190c7ab6-4611-4b6b-a49f-6088a62c7f6b'),
					array('key' => 'NE', 'name' => 'Nebraska', 'active' => 1, 'guid' => '74d0f5da-442b-499e-bd5d-3579bb6cab4a'),
					array('key' => 'NH', 'name' => 'New Hampshire', 'active' => 1, 'guid' => '1ec4a525-2db9-484f-81c1-fa46c9b8260d'),
					array('key' => 'NJ', 'name' => 'New Jersey', 'active' => 1, 'guid' => '7a692a3a-c4fd-4160-8a74-24648d6f678f'),
					array('key' => 'NM', 'name' => 'New Mexico', 'active' => 1, 'guid' => '018a67b4-dbbd-4479-9b8f-af519ce60cd8'),
					array('key' => 'NV', 'name' => 'Nevada', 'active' => 1, 'guid' => '0b099cdb-3609-4752-9172-5dd79c770228'),
					array('key' => 'NY', 'name' => 'New York', 'active' => 1, 'guid' => 'e537e863-d78c-45d4-89dc-d1cde90dac62'),
					array('key' => 'OH', 'name' => 'Ohio', 'active' => 1, 'guid' => '8de11b34-a77e-41a0-8bc1-aff9acb902fc'),
					array('key' => 'OK', 'name' => 'Oklahoma', 'active' => 1, 'guid' => '4dbf9dec-8ccd-4933-832d-80e3274b161b'),
					array('key' => 'OR', 'name' => 'Oregon', 'active' => 1, 'guid' => '521bc7c8-e43c-4fd1-bd81-ef06ae12e27d'),
					array('key' => 'PA', 'name' => 'Pennsylvania', 'active' => 1, 'guid' => '9807feba-1e90-484d-b98c-f33c14065ce4'),
					array('key' => 'PR', 'name' => 'Puerto Rico', 'active' => 1, 'guid' => 'b957391c-3aba-49a8-b938-2f1f350ec1ff'),
					array('key' => 'PW', 'name' => 'Palau', 'active' => 1, 'guid' => '34734f7e-8ef3-44af-ae9c-ee9480aa06d3'),
					array('key' => 'RI', 'name' => 'Rhode Island', 'active' => 1, 'guid' => '2c9354c3-0956-4e1e-b83e-0008bdc19c14'),
					array('key' => 'SC', 'name' => 'South Carolina', 'active' => 1, 'guid' => '9960b0f7-e10b-4f5d-8f64-80d3f31a53b1'),
					array('key' => 'SD', 'name' => 'South Dakota', 'active' => 1, 'guid' => '23a1960b-146e-473b-8167-886c5fa5850b'),
					array('key' => 'TN', 'name' => 'Tennessee', 'active' => 1, 'guid' => '524b08fa-c3a1-473a-8b00-043b7c5915bc'),
					array('key' => 'TX', 'name' => 'Texas', 'active' => 1, 'guid' => '6b38c039-62a2-4adf-86ce-f4f58e4f900e'),
					array('key' => 'UT', 'name' => 'Utah', 'active' => 1, 'guid' => 'dbd7a7d0-15e5-4f71-9762-679753d03733'),
					array('key' => 'VA', 'name' => 'Virginia', 'active' => 1, 'guid' => '069dd1ad-fa70-4828-944b-9a257ad0ed5c'),
					array('key' => 'VI', 'name' => 'Virgin Islands', 'active' => 1, 'guid' => 'a0e6f9eb-2183-47b0-b14a-861bf4b87027'),
					array('key' => 'VT', 'name' => 'Vermont', 'active' => 1, 'guid' => 'efbae062-3be8-4b45-807c-92e7ee39631b'),
					array('key' => 'WA', 'name' => 'Washington', 'active' => 1, 'guid' => '2ac1cb96-c23d-43f5-b984-c4d340a06ded'),
					array('key' => 'WI', 'name' => 'Wisconsin', 'active' => 1, 'guid' => '8aae244a-c391-4cb3-9d14-0624edb061e6'),
					array('key' => 'WV', 'name' => 'West Virginia', 'active' => 1, 'guid' => '6232af34-b958-401b-b304-37c3972db138'),
					array('key' => 'WY', 'name' => 'Wyoming', 'active' => 1, 'guid' => '8b8a26bf-94e7-494e-a4c0-a3246c85f080'),
				)),
			);

			// top level
			$level = array();
			$level['guid'] = '4d9de535-d18d-4948-a714-787b848524fa';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array();
			$data[] = $level;

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateCalendarPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Calendar';
			$key = 'CALENDAR';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'appointment' => array('key' => 'APP_REASON', 'name' => AppointmentTemplate::ENUM_PARENT_NAME, 'active' => 1, 'guid' => 'cb4390f6-a334-4dbd-9edc-5637265b776b', 'data' => array(
					'provider' => array('key' => 'PROVIDER', 'name' => 'Provider', 'active' => 1, 'guid' => '82084f77-65a1-466a-b5f8-63e3eb38af9a'),
					'specialist' => array('key' => 'SPECIALIST', 'name' => 'Specialist', 'active' => 1, 'guid' => 'ca0e5f81-7105-4250-abed-8ee45c51b5e3'),
					'medicalPhone' => array('key' => 'MEDPHONE', 'name' => 'Medical Phone', 'active' => 1, 'guid' => '8dd85952-3be8-4b7e-b153-01678f8b571f'),
					'medicalPU' => array('key' => 'MEDPU', 'name' => 'Medication PU', 'active' => 1, 'guid' => '27cf00da-f8c0-4859-9205-63b9e056edf9'),
					'education' => array('key' => 'EDUCATION', 'name' => 'Education', 'active' => 1, 'guid' => '23190974-896c-4dfa-b6db-3a8072aa6ca0'),
					'eligibility' => array('key' => 'ELIG', 'name' => 'Eligibility', 'active' => 1, 'guid' => 'b9c4fb2f-5ddd-48e1-b733-44f7be127069'),
					'blockedTime' => array('key' => 'BLOCKTIME', 'name' => 'Blocked Time', 'active' => 1, 'guid' => '7d6486a3-9655-44a3-b5ed-ad95da0cea7c'),
				)),
			);

			$appointmentTemplate = new AppointmentTemplate();
			foreach ($enums['appointment']['data'] as $k=>$item) {
				$appointmentTemplate->appointmentTemplateId = 0;
				$appointmentTemplate->name = $item['name'];
				$appointmentTemplate->persist();

				$enums['appointment']['data'][$k]['ormClass'] = 'AppointmentTemplate';
				$enums['appointment']['data'][$k]['ormEditMethod'] = 'ormEditMethod';
				$enums['appointment']['data'][$k]['ormId'] = $appointmentTemplate->appointmentTemplateId;
			}

			// top level
			$level = array();
			$level['guid'] = 'e46d5343-18de-459a-9fa4-0dc46ab0c41c';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateClinicalPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Clinical';
			$key = 'CLINICAL';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'allergies' => array('key' => 'ALLERGIES', 'name' => 'Allergies', 'active' => 1, 'guid' => 'a08abbd2-1a70-491a-9f3f-eb6b41146872', 'data' => array(
					'symptom' => array('key' => 'SYMPTOM', 'name' => PatientAllergy::ENUM_SYMPTOM_PARENT_NAME, 'active' => 1, 'guid' => '5e4efa33-5d27-4caf-bc1c-b36d533fd97f', 'data' => array(
						array('key' => 'AGITATION', 'name' => 'AGITATION', 'active' => 1, 'guid' => 'e33e04fa-7159-4342-b332-0be5f01d46c1'),
						array('key' => 'AGRANULOC', 'name' => 'AGRANULOCYTOSIS', 'active' => 1, 'guid' => 'd3387a01-e7df-4005-957b-91461e128cfa'),
						array('key' => 'ALOPECIA', 'name' => 'ALOPECIA', 'active' => 1, 'guid' => '51d13556-27d8-47cd-bc77-573ca5a14f52'),
						array('key' => 'ANAPHYL', 'name' => 'ANAPHYLAXIS', 'active' => 1, 'guid' => '9769b199-6b56-4260-8d14-5d6c91b81751'),
						array('key' => 'ANEMIA', 'name' => 'ANEMIA', 'active' => 1, 'guid' => '63328e89-432a-473e-8715-097741317be4'),
						array('key' => 'ANOREXIA', 'name' => 'ANOREXIA', 'active' => 1, 'guid' => 'a683f225-1fe1-4281-8268-554e4cd3d7c4'),
						array('key' => 'ANXIETY', 'name' => 'ANXIETY', 'active' => 1, 'guid' => 'c54a24d2-d7db-4872-b569-cc9b528188a3'),
						array('key' => 'APNEA', 'name' => 'APNEA', 'active' => 1, 'guid' => 'edb5a487-da23-4341-99b3-8d403d1f7567'),
						array('key' => 'APPETITE', 'name' => 'APPETITE,INCREASED', 'active' => 1, 'guid' => '4e406011-d648-4b4f-ae91-ec0866f7f016'),
						array('key' => 'ARRHYTHMIA', 'name' => 'ARRHYTHMIA', 'active' => 1, 'guid' => '3c7ade80-e180-4051-a528-256f1c8d7a5b'),
						array('key' => 'ASTHENIA', 'name' => 'ASTHENIA', 'active' => 1, 'guid' => 'a677e528-bafa-494b-a842-78845b535ebc'),
						array('key' => 'ASTHMA', 'name' => 'ASTHMA', 'active' => 1, 'guid' => '5d61c25a-2189-4404-ae15-ba123106de6c'),
						array('key' => 'ATAXIA', 'name' => 'ATAXIA', 'active' => 1, 'guid' => '358661be-39bd-4927-8d1a-50b0cf4ffef8'),
						array('key' => 'ATHETOSIS', 'name' => 'ATHETOSIS', 'active' => 1, 'guid' => '49de7b67-fe12-4833-91aa-ea9f0d285f6a'),
						array('key' => 'BRACHY', 'name' => 'BRACHYCARDIA', 'active' => 1, 'guid' => 'f0f021e0-cc62-4270-88da-1f0838a1b266'),
						array('key' => 'BREASTENG', 'name' => 'BREAST ENGORGEMENT', 'active' => 1, 'guid' => '27bc1849-a51d-4495-bda6-4b0f8e39b0be'),
						array('key' => 'BRONCHO', 'name' => 'BRONCHOSPASM', 'active' => 1, 'guid' => 'd0b04376-b5d7-4fab-b88e-c0579ec33d39'),
						array('key' => 'CARDIAC', 'name' => 'CARDIAC ARREST', 'active' => 1, 'guid' => '558ff42b-7407-4c14-a289-cc1a5d66b5e0'),
						array('key' => 'CHESTPAIN', 'name' => 'CHEST PAIN', 'active' => 1, 'guid' => '81448a0f-8f49-4481-a052-389187667f54'),
						array('key' => 'CHILLS', 'name' => 'CHILLS', 'active' => 1, 'guid' => '85761984-3571-4526-af11-876d93b3f859'),
						array('key' => 'COMA', 'name' => 'COMA', 'active' => 1, 'guid' => 'f75d8aa7-180e-49dc-b8ba-fc37c2a47d2a'),
						array('key' => 'CONFUSION', 'name' => 'CONFUSION', 'active' => 1, 'guid' => 'b9a00422-7457-481d-99a2-55a4e4532234'),
						array('key' => 'CONGESTION', 'name' => 'CONGESTION,NASAL', 'active' => 1, 'guid' => '1cf3a038-6eeb-43c5-b2bf-92647f67c41f'),
						array('key' => 'CONJUNCT', 'name' => 'CONJUNCTIVAL CONGESTION', 'active' => 1, 'guid' => 'e496ab41-8fa7-4b26-a272-4830ad0e7ff9'),
						array('key' => 'CONSTI', 'name' => 'CONSTIPATION', 'active' => 1, 'guid' => '0c633c18-97dd-4978-868e-df31d8a87fd0'),
						array('key' => 'COUGHING', 'name' => 'COUGHING', 'active' => 1, 'guid' => '52f5b451-8619-4428-9b29-914f734a966a'),
						array('key' => 'DEAFNESS', 'name' => 'DEAFNESS', 'active' => 1, 'guid' => 'd869ff05-86b2-4fc8-84ee-b80b6819db46'),
						array('key' => 'DELERIUM', 'name' => 'DELERIUM', 'active' => 1, 'guid' => '7f46d30c-5a55-47bf-a31b-6240c7daabe5'),
						array('key' => 'DELUSION', 'name' => 'DELUSION', 'active' => 1, 'guid' => 'ac5142a5-7afa-4d58-87bf-9c65cb0c88b5'),
						array('key' => 'DEPRESSION', 'name' => 'DEPRESSION', 'active' => 1, 'guid' => '5c96c0fc-22e8-47d3-8150-eed7f3e5488d'),
						array('key' => 'MENTALDEP', 'name' => 'DEPRESSION,MENTAL', 'active' => 1, 'guid' => 'd3e79ee1-bd45-4b52-931b-95dd20f92a50'),
						array('key' => 'POSTICTAL', 'name' => 'DEPRESSION,POSTICTAL', 'active' => 1, 'guid' => 'a44de67a-0687-479a-a86a-387a779c4607'),
						array('key' => 'DERMATITIS', 'name' => 'DERMATITIS', 'active' => 1, 'guid' => '68663a80-b468-49de-802a-863b62b1f819'),
						array('key' => 'CONTACTDER', 'name' => 'DERMATITIS,CONTACT', 'active' => 1, 'guid' => '769aae3c-a24e-4b97-8e1f-fe9a3a8d36f0'),
						array('key' => 'PHOTOALLER', 'name' => 'DERMATITIS,PHOTOALLERGENIC', 'active' => 1, 'guid' => '809701ae-1008-4fb2-a73f-e809551e1712'),
						array('key' => 'DIAPHORES', 'name' => 'DIAPHORESIS', 'active' => 1, 'guid' => '3fe196a3-011e-448e-b470-9d1a30ca7bb8'),
						array('key' => 'DIARRHEA', 'name' => 'DIARRHEA', 'active' => 1, 'guid' => 'cb5c9662-b84e-471e-95b9-f86531c8cd53'),
						array('key' => 'DIPLOPIA', 'name' => 'DIPLOPIA', 'active' => 1, 'guid' => '8dbabab1-7cdd-43cd-a540-953509602d74'),
						array('key' => 'DISTURB', 'name' => 'DISTURBED COORDINATION', 'active' => 1, 'guid' => '2319f319-23b4-47e6-9fbb-b0bf29c36d35'),
						array('key' => 'DIZZINESS', 'name' => 'DIZZINESS', 'active' => 1, 'guid' => '28813ec3-07fb-4424-a0e1-f45ec2d1581c'),
						array('key' => 'DREAMING', 'name' => 'DREAMING,INCREASED', 'active' => 1, 'guid' => 'f1af66fc-16b7-4196-9882-5d06d8e255af'),
						array('key' => 'DROWSINESS', 'name' => 'DROWSINESS', 'active' => 1, 'guid' => 'a7af6e1e-55eb-4a02-8259-808f45d8ac27'),
						array('key' => 'DRYMOUTH', 'name' => 'DRY MOUTH', 'active' => 1, 'guid' => 'cf1b8d8d-e807-4d49-94f6-25c8f8e934bd'),
						array('key' => 'DRYNOSE', 'name' => 'DRY NOSE', 'active' => 1, 'guid' => '39fce240-43d2-4f39-a592-5974840a42d9'),
						array('key' => 'DRYTHROAT', 'name' => 'DRY THROAT', 'active' => 1, 'guid' => '37eafc95-454a-4aa4-81be-cfcb619123d0'),
						array('key' => 'DYSPNEA', 'name' => 'DYSPNEA', 'active' => 1, 'guid' => 'f105129e-7eeb-4709-9c86-ba3512607c03'),
						array('key' => 'DYSURIA', 'name' => 'DYSURIA', 'active' => 1, 'guid' => 'b0622d48-035d-49a5-8c36-38df17438500'),
						array('key' => 'ECCHYMOSIS', 'name' => 'ECCHYMOSIS', 'active' => 1, 'guid' => '49d2dce3-0fd8-4a20-8811-e0f7bc4489dc'),
						array('key' => 'ECGCHANGES', 'name' => 'ECG CHANGES', 'active' => 1, 'guid' => '58bf888e-31d6-4112-989e-c32ed80caf47'),
						array('key' => 'ECZEMA', 'name' => 'ECZEMA', 'active' => 1, 'guid' => '9ea40421-96be-4017-ab4a-ee63fe278259'),
						array('key' => 'EDEMA', 'name' => 'EDEMA', 'active' => 1, 'guid' => '2773e96e-0446-44be-98ec-2d81ab3f2802'),
						array('key' => 'EPIGASTRIC', 'name' => 'EPIGASTRIC DISTRESS', 'active' => 1, 'guid' => 'a41a3909-0c33-4e6a-866e-3100a6142f5f'),
						array('key' => 'EPISTAXIS', 'name' => 'EPISTAXIS', 'active' => 1, 'guid' => '7efbc6f5-e593-4642-92b6-6e7ed8c5eb32'),
						array('key' => 'ERYTHEMA', 'name' => 'ERYTHEMA', 'active' => 1, 'guid' => 'c6ff3e26-9f58-4adc-bf94-519563a22f37'),
						array('key' => 'EUPHORIA', 'name' => 'EUPHORIA', 'active' => 1, 'guid' => 'a73e1a19-e8a7-441a-b74d-712f44276b6b'),
						array('key' => 'EXCITATION', 'name' => 'EXCITATION', 'active' => 1, 'guid' => '9e02208f-e9c2-4983-8742-832c15fe9d20'),
						array('key' => 'EXTRASYS', 'name' => 'EXTRASYSTOLE', 'active' => 1, 'guid' => '9d089876-ce86-4665-8002-6ca5194e1d6b'),
						array('key' => 'FACEFLUSH', 'name' => 'FACE FLUSHED', 'active' => 1, 'guid' => '5655c678-3517-4e5a-bebe-0165c38912fb'),
						array('key' => 'DYSKINESIA', 'name' => 'FACIAL DYSKINESIA', 'active' => 1, 'guid' => 'df06eec5-05b7-4ecd-9197-1bbd42c78e60'),
						array('key' => 'FAINTNESS', 'name' => 'FAINTNESS', 'active' => 1, 'guid' => 'a59a0e2f-6eea-4620-98e8-68a5590d0c2c'),
						array('key' => 'FATIGUE', 'name' => 'FATIGUE', 'active' => 1, 'guid' => 'ad32805f-14db-43b1-8cf2-d0408901eea1'),
						array('key' => 'FEELWARMTH', 'name' => 'FEELING OF WARMTH', 'active' => 1, 'guid' => 'ad33e3c9-4e77-451f-9a6f-c923d92701a6'),
						array('key' => 'FEVER', 'name' => 'FEVER', 'active' => 1, 'guid' => '5015b71e-f9d9-48af-a79e-b0e36b682d40'),
						array('key' => 'GALACTOR', 'name' => 'GALACTORRHEA', 'active' => 1, 'guid' => 'e0c02fb1-7cf2-42a6-8fca-da40c3e9510a'),
						array('key' => 'GENRASH', 'name' => 'GENERALIZED RASH', 'active' => 1, 'guid' => '6eae9369-e651-4235-9ee6-c5e2bd1f0390'),
						array('key' => 'GIREACTION', 'name' => 'GI REACTION', 'active' => 1, 'guid' => '68f1223d-1fb8-4d3e-8e0d-7bc264257119'),
						array('key' => 'GLAUCOMA', 'name' => 'GLAUCOMA', 'active' => 1, 'guid' => '0566d19c-623a-44f4-aa13-aa75531c6cb4'),
						array('key' => 'GYNECOMA', 'name' => 'GYNECOMASTIA', 'active' => 1, 'guid' => '0bb3456c-4efd-41d6-825b-927709ce43eb'),
						array('key' => 'HALLUCIN', 'name' => 'HALLUCINATIONS', 'active' => 1, 'guid' => '4425c166-488f-43b3-8c53-028ae00b5d68'),
						array('key' => 'HEADACHE', 'name' => 'HEADACHE', 'active' => 1, 'guid' => '1dc774b9-8a6e-4ad4-9120-357145d1e6fa'),
						array('key' => 'HEARTBLOCK', 'name' => 'HEART BLOCK', 'active' => 1, 'guid' => 'b58e5f18-81cf-4ac1-b0db-27f98b984c9b'),
						array('key' => 'HEMATURIA', 'name' => 'HEMATURIA', 'active' => 1, 'guid' => '4c7c0865-09fa-474f-8af4-ccdf7318e8c8'),
						array('key' => 'HEMOGLOBIN', 'name' => 'HEMOGLOBIN,INCREASED', 'active' => 1, 'guid' => 'a3f6c207-37fd-4a8c-ae49-93561ecdb365'),
						array('key' => 'HIVES', 'name' => 'HIVES', 'active' => 1, 'guid' => '85ab9163-b6d3-4397-9503-7e6c60c1c8bb'),
						array('key' => 'HYPERSENSE', 'name' => 'HYPERSENSITIVITY', 'active' => 1, 'guid' => 'b3c7d804-9cd3-4f78-b199-6b6a2051c5d0'),
						array('key' => 'HYPERTENSE', 'name' => 'HYPERTENSION', 'active' => 1, 'guid' => '756e3d6b-3c26-457d-bab3-a0f7627edc81'),
						array('key' => 'HYPOTENSE', 'name' => 'HYPOTENSION', 'active' => 1, 'guid' => '84c4f714-cb12-4ca9-bd0e-20e20ea6abd7'),
						array('key' => 'IMPAIREREC', 'name' => 'IMPAIRMENT OF ERECTION', 'active' => 1, 'guid' => '3c14cb5b-6b83-4b22-95ac-5146fc68f8f3'),
						array('key' => 'IMPOTENCE', 'name' => 'IMPOTENCE', 'active' => 1, 'guid' => '812c866b-72b9-41d7-87cf-89f2f6f3f771'),
						array('key' => 'PENILEEREC', 'name' => 'INAPPROPRIATE PENILE ERECTION', 'active' => 1, 'guid' => '42617f7b-7d58-42e4-a468-760dec0de23e'),
						array('key' => 'INSOMNIA', 'name' => 'INSOMNIA', 'active' => 1, 'guid' => '29113a01-7fd9-4e2e-8ccc-1174cd8cfc7f'),
						array('key' => 'IRRITABILI', 'name' => 'IRRITABILITY', 'active' => 1, 'guid' => '7283ee74-6c04-4ae9-9894-509b4580aba5'),
						array('key' => 'ITCHING', 'name' => 'ITCHING,WATERING EYES', 'active' => 1, 'guid' => '58d79e15-f311-494a-9c94-db2fbdf06fcb'),
						array('key' => 'JUNCRHYTHM', 'name' => 'JUNCTIONAL RHYTHM', 'active' => 1, 'guid' => '695d132a-5e28-4ac9-bd6b-b8a58944c1cf'),
						array('key' => 'LABYRINTH', 'name' => 'LABYRINTHITIS,ACUTE', 'active' => 1, 'guid' => '67bdf8d5-b89b-4b57-aee6-e2747cd5305a'),
						array('key' => 'LACRIM', 'name' => 'LACRIMATION', 'active' => 1, 'guid' => '8fe4aa68-7ee3-4436-a026-cf420a54a11a'),
						array('key' => 'LDHINC', 'name' => 'LDH,INCREASED', 'active' => 1, 'guid' => '151165f8-c26f-4219-861b-79f2cb483a2d'),
						array('key' => 'LETHARGY', 'name' => 'LETHARGY', 'active' => 1, 'guid' => '3846f391-95b1-4e05-a0bc-838e0f08d5ef'),
						array('key' => 'LEUKOCYTE', 'name' => 'LEUKOCYTE COUNT,DECREASED', 'active' => 1, 'guid' => '6dc47ca3-8b0f-496a-af74-705e730ac6d1'),
						array('key' => 'LIBIDODEC', 'name' => 'LIBIDO,DECREASED', 'active' => 1, 'guid' => '87b11bb0-8aef-4670-a820-b0268e3b5a4f'),
						array('key' => 'LIBIDOINC', 'name' => 'LIBIDO,INCREASED', 'active' => 1, 'guid' => '45bf23a2-3fab-401c-b8b1-eddc2a0a74c5'),
						array('key' => 'MIOSIS', 'name' => 'MIOSIS', 'active' => 1, 'guid' => '6b223e21-45be-4f9c-91cc-931451325c28'),
						array('key' => 'MYOCARDIAL', 'name' => 'MYOCARDIAL INFARCTION', 'active' => 1, 'guid' => 'd33e3402-74a5-4e08-b63b-02d5670ef305'),
						array('key' => 'NAUSEA', 'name' => 'NAUSEA,VOMITING', 'active' => 1, 'guid' => '26b4f217-8f69-4198-926f-4334a7e5429d'),
						array('key' => 'NERVOUSNES', 'name' => 'NERVOUSNESS,AGITATION', 'active' => 1, 'guid' => '325c2470-d076-456f-ac42-8b5d64a48abd'),
						array('key' => 'NEUTROPHIL', 'name' => 'NEUTROPHIL COUNT,DECREASED', 'active' => 1, 'guid' => 'd593f1b6-db2d-42bf-b6ca-98a81cb7e0ae'),
						array('key' => 'NIGHTMARES', 'name' => 'NIGHTMARES', 'active' => 1, 'guid' => 'baf19a22-fa18-4bd0-aeef-a11d6acdba9b'),
						array('key' => 'OPTICATROP', 'name' => 'OPTIC ATROPHY', 'active' => 1, 'guid' => 'ab0eb531-74be-4698-87d4-0eb2914816b7'),
						array('key' => 'ORGASMINH', 'name' => 'ORGASM,INHIBITED', 'active' => 1, 'guid' => '83f28d1e-ef4a-4509-b646-80e6aa6c1cb5'),
						array('key' => 'ORONASAL', 'name' => 'ORONASALPHARYNGEAL IRRITATION', 'active' => 1, 'guid' => '1a71f784-3d5e-4b2d-8d42-9fc972dfc0bd'),
						array('key' => 'PAINJOINT', 'name' => 'PAIN,JOINT', 'active' => 1, 'guid' => '5f94d154-de53-4e7c-a818-daf3c41aad65'),
						array('key' => 'PALPITATE', 'name' => 'PALPITATIONS', 'active' => 1, 'guid' => 'b54e506b-eef9-46d5-9b66-07681d2c3226'),
						array('key' => 'PANCYTOPEN', 'name' => 'PANCYTOPENIA', 'active' => 1, 'guid' => 'a57ec518-088b-43ca-b7b3-894cc5cabb26'),
						array('key' => 'PARESTHES', 'name' => 'PARESTHESIA', 'active' => 1, 'guid' => '55442aad-f868-4b30-b5cf-20138982cb34'),
						array('key' => 'PARKINSON', 'name' => 'PARKINSONIAN-LIKE SYNDROME', 'active' => 1, 'guid' => '6d63be7a-6a2a-48f7-a448-8eb9ee5376c7'),
						array('key' => 'PHOTOSEN', 'name' => 'PHOTOSENSITIVITY', 'active' => 1, 'guid' => '41c6a3c2-b75d-4d65-a608-f1487f0e77ac'),
						array('key' => 'POSSREACT', 'name' => 'POSSIBLE REACTION', 'active' => 1, 'guid' => 'bae64648-7109-44fd-94d6-0a817fbe51fa'),
						array('key' => 'PRIAPISM', 'name' => 'PRIAPISM', 'active' => 1, 'guid' => 'c98aa887-91c4-4645-8ee6-da4490150ca6'),
						array('key' => 'PROPENEREC', 'name' => 'PROLONGED PENILE ERECTION', 'active' => 1, 'guid' => '34c5a1e8-6102-4dc3-ab95-c85e70dd2e8d'),
						array('key' => 'PRURITIS', 'name' => 'PRURITIS', 'active' => 1, 'guid' => '6047faba-2911-421b-b548-98da61087dfc'),
						array('key' => 'PTOSIS', 'name' => 'PTOSIS', 'active' => 1, 'guid' => '4448ea08-7985-4297-a030-875424dd8644'),
						array('key' => 'PURPURA', 'name' => 'PURPURA', 'active' => 1, 'guid' => 'd3155510-fa2f-4bab-b3aa-95a12a44cb9f'),
						array('key' => 'RALES', 'name' => 'RALES', 'active' => 1, 'guid' => 'e3af50e8-1c31-45d0-a89d-d50713225d03'),
						array('key' => 'RASH', 'name' => 'RASH', 'active' => 1, 'guid' => 'a6152b88-73cf-4f77-827c-f2b2f256a872'),
						array('key' => 'RASHPAPULA', 'name' => 'RASH,PAPULAR', 'active' => 1, 'guid' => '2e9fbb64-4076-4fc7-8a5a-a8323223289f'),
						array('key' => 'RESPIDIST', 'name' => 'RESPIRATORY DISTRESS', 'active' => 1, 'guid' => 'b82f1ffd-39e8-4840-afe3-979cc0a96b67'),
						array('key' => 'RETROEJAC', 'name' => 'RETROGRADE EJACULATION', 'active' => 1, 'guid' => 'b1aa64f5-35a0-4de6-a6de-35ce539ab9ec'),
						array('key' => 'RHINITIS', 'name' => 'RHINITIS', 'active' => 1, 'guid' => '569b9907-477d-4728-b46d-ff2ca536baf7'),
						array('key' => 'RHINORRHEA', 'name' => 'RHINORRHEA', 'active' => 1, 'guid' => 'b21d97a6-61fa-4dbd-bc46-923419669f2a'),
						array('key' => 'RHONCHUS', 'name' => 'RHONCHUS', 'active' => 1, 'guid' => 'db6905e6-717b-4385-9d41-aad07765aef2'),
						array('key' => 'STCHANGES', 'name' => 'S-T CHANGES,TRANSIENT', 'active' => 1, 'guid' => 'a74931ab-0eeb-456f-84ae-1e6561e4c63a'),
						array('key' => 'SEIZURES', 'name' => 'SEIZURES', 'active' => 1, 'guid' => 'd7928c4f-a009-4998-8335-61b3ff1882a5'),
						array('key' => 'SEIZURESTC', 'name' => 'SEIZURES,TONIC-CLONIC', 'active' => 1, 'guid' => '36831f0f-7cda-4431-8c0c-e7ff7ccd4f2f'),
						array('key' => 'SELFDEPRE', 'name' => 'SELF-DEPRECATION', 'active' => 1, 'guid' => '77ed1474-f5b0-4554-a944-94244b457e73'),
						array('key' => 'SEVERERASH', 'name' => 'SEVERE RASH', 'active' => 1, 'guid' => 'a4e56fe2-a0ec-43dc-b338-203c780692fd'),
						array('key' => 'SHORTBREAT', 'name' => 'SHORTNESS OF BREATH', 'active' => 1, 'guid' => '375c6a4a-855e-4d05-a3bc-2de663964fa8'),
						array('key' => 'SINUS', 'name' => 'SINUS BRACHYCARDIA', 'active' => 1, 'guid' => 'bc05bb94-1022-4fea-8bed-786191857ec3'),
						array('key' => 'SNEEZING', 'name' => 'SNEEZING', 'active' => 1, 'guid' => '126cd5ea-417c-4a98-a44a-96bf1a1ce25d'),
						array('key' => 'SOMNOLENCE', 'name' => 'SOMNOLENCE', 'active' => 1, 'guid' => '13ac9593-7f11-478b-896e-02a40c2b81bc'),
						array('key' => 'SPEECHDIS', 'name' => 'SPEECH DISORDER', 'active' => 1, 'guid' => '40149a1a-cf03-4233-ad05-aefc3db129cf'),
						array('key' => 'SWELLING', 'name' => 'SWELLING (NON-SPECIFIC)', 'active' => 1, 'guid' => 'c3407d51-2e0d-41c2-81cd-050b6029fb81'),
						array('key' => 'SWELLEYES', 'name' => 'SWELLING-EYES', 'active' => 1, 'guid' => '302d54c6-ab23-4e90-99a5-7f101752deff'),
						array('key' => 'SWELLLIPS', 'name' => 'SWELLING-LIPS', 'active' => 1, 'guid' => 'd7cb0fcf-c106-4047-beae-762a522f226f'),
						array('key' => 'SWELLTHRO', 'name' => 'SWELLING-THROAT', 'active' => 1, 'guid' => '464c43b9-a092-45c8-b814-da92a11e3280'),
						array('key' => 'SYNCOPE', 'name' => 'SYNCOPE', 'active' => 1, 'guid' => '98d1d8b5-eb00-4311-95d6-914eea5cfbe9'),
						array('key' => 'TACHYCARD', 'name' => 'TACHYCARDIA', 'active' => 1, 'guid' => '64693c8d-19e4-44d8-a1ed-0cfcadb7f450'),
						array('key' => 'THROMBOCYT', 'name' => 'THROMBOCYTOPENIA', 'active' => 1, 'guid' => 'f2ce7236-2dae-4616-a808-47bbf999cb48'),
						array('key' => 'TREMORS', 'name' => 'TREMORS', 'active' => 1, 'guid' => '2c72d4d9-770a-473b-bd00-ae480dfc1ef2'),
						array('key' => 'URINARYFLO', 'name' => 'URINARY FLOW,DELAYED', 'active' => 1, 'guid' => 'bc898ac0-ed29-434d-83b8-9ac1ca948af1'),
						array('key' => 'URINARYFRE', 'name' => 'URINARY FREQUENCY', 'active' => 1, 'guid' => '6be1ee6a-27ae-4048-a03e-3405ce593b4c'),
						array('key' => 'URINARYFI', 'name' => 'URINARY FREQUENCY,INCREASED', 'active' => 1, 'guid' => 'e758771d-9462-4e2a-acdb-b627ea245f06'),
						array('key' => 'URINARYRET', 'name' => 'URINARY RETENTION', 'active' => 1, 'guid' => '4971fe4b-f4b5-47a3-94c6-a04d7bc85047'),
						array('key' => 'URTICARIA', 'name' => 'URTICARIA', 'active' => 1, 'guid' => '5b9d3c61-6817-484c-8866-1d297ae98703'),
						array('key' => 'UVEITIS', 'name' => 'UVEITIS', 'active' => 1, 'guid' => 'bee7a122-7ab2-4a7b-91c2-ada764d5d38d'),
						array('key' => 'VERTIGO', 'name' => 'VERTIGO', 'active' => 1, 'guid' => '79255840-4814-4992-af89-7d3e74669d8f'),
						array('key' => 'VISIONBLUR', 'name' => 'VISION,BLURRED', 'active' => 1, 'guid' => 'a4afab67-c5be-4c35-bb87-29530b0fe9d5'),
						array('key' => 'VISUALDIST', 'name' => 'VISUAL DISTURBANCES', 'active' => 1, 'guid' => '912f78c0-c5cf-43bf-8633-28118d54c44b'),
						array('key' => 'VOMITING', 'name' => 'VOMITING', 'active' => 1, 'guid' => '40d9207f-dea7-4af3-8566-31cf1011caeb'),
						array('key' => 'WEAKNESS', 'name' => 'WEAKNESS', 'active' => 1, 'guid' => 'af97f76d-5b4b-4571-a6a2-98e0abb7df1c'),
						array('key' => 'WEIGHTGAIN', 'name' => 'WEIGHT GAIN', 'active' => 1, 'guid' => '47b2ac4c-8cc4-4efa-8faa-018c0bdb053c'),
						array('key' => 'WHEEZING', 'name' => 'WHEEZING', 'active' => 1, 'guid' => '15e6c847-4d7c-4fe2-a00c-20f109d28248'),
					)),
					'severity' => array('key' => 'SEVERITY', 'name' => PatientAllergy::ENUM_SEVERITY_PARENT_NAME, 'active' => 1, 'guid' => 'a5b080cb-2bac-4dfe-a87e-4805abb2b353', 'data' => array(
						'mild' => array('key' => 'MILD', 'name' => 'Mild', 'active' => 1, 'guid' => '74cc3b3f-04e8-4252-9f8a-63701d0eb106'),
						'moderate' => array('key' => 'MODERATE', 'name' => 'Moderate', 'active' => 1, 'guid' => '2ef8862e-ecf9-4280-8ee7-118918d5a35c'),
					)),
					'reactionType' => array('key' => 'REACTYPE', 'name' => PatientAllergy::ENUM_REACTION_TYPE_PARENT_NAME, 'active' => 1, 'guid' => '05ba0f9e-dc5d-49bc-972d-fc0b0d5509f6', 'data' => array(
						'allergy' => array('key' => 'ALLERGY', 'name' => 'Allergy', 'active' => 1, 'guid' => '6d0c6924-1a5d-45bf-8b41-6ae21cfdf3b2'),
						'pharma' => array('key' => 'PHARMA', 'name' => 'Pharmacological', 'active' => 1, 'guid' => '2297aad6-43b6-44e2-ae45-bb02743789c8'),
						'unknown' => array('key' => 'UNKNOWN', 'name' => 'Unknown', 'active' => 1, 'guid' => '5b211c88-1c9c-4721-a27e-d53acb82c213'),
						'drugClass' => array('key' => 'DRUGCLASS', 'name' => 'Drug Class Allergy', 'active' => 1, 'guid' => '8fa5215c-268b-4a77-be32-9f0479d6e17b'),
						'specDrug' => array('key' => 'SPECDRUG', 'name' => 'Specific Drug Allergy', 'active' => 1, 'guid' => '7ba4f8dd-64d0-432f-8f14-b136c82cc55b'),
					)),
				)),
			);

			$level = array();
			$level['guid'] = '4db7079d-5c31-4f3a-8280-470bd6918329';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateContactPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Contact Preferences';
			$key = 'CONTACT';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'phoneTypes' => array('key' => 'PHONETYPES', 'name' => 'Phone Types', 'active' => 1, 'guid' => 'dd7ee20b-8faa-4518-9120-600ab7e2f331', 'data' => array(
					array('key' => 'HOME', 'name' => 'Home', 'active' => 1, 'guid' => 'f675419e-8e61-422e-917a-80a05cb92270'),
					array('key' => 'WORK', 'name' => 'Work', 'active' => 1, 'guid' => '7a3fc33f-f103-4754-81e3-8e07e19bd10d'),
					array('key' => 'BILL', 'name' => 'Billing', 'active' => 1, 'guid' => '94a1d7c1-e59c-4597-8423-e379f88fd3ef'),
					array('key' => 'MOB', 'name' => 'Mobile', 'active' => 1, 'guid' => 'a2ad6a92-55c5-4506-87e1-b323640dcb26'),
					array('key' => 'EMER', 'name' => 'Emergency', 'active' => 1, 'guid' => '47b2ec07-d740-40d3-b78d-6635a81c8464'),
					array('key' => 'FAX', 'name' => 'Fax', 'active' => 1, 'guid' => 'c3bc4d7d-4d64-42b6-b402-99aafadef65b'),
					array('key' => 'EMPL', 'name' => 'Employer', 'active' => 1, 'guid' => '380575c1-9a42-4c4c-ba7d-9f507fc04d55'),
				)),
				'addrTypes' => array('key' => 'ADDRTYPES', 'name' => 'Address Types', 'active' => 1, 'guid' => 'e0e29c42-abd1-4dae-bc17-3f8b8213d9e7', 'data' => array(
					array('key' => 'HOME', 'name' => 'Home', 'active' => 1, 'guid' => '4f30ace1-b14b-470b-b4a0-edb940a755a0'),
					array('key' => 'EMPL', 'name' => 'Employer', 'active' => 1, 'guid' => '2218c87d-3e16-4dfa-a3e8-872590c692de'),
					array('key' => 'BILL', 'name' => 'Billing', 'active' => 1, 'guid' => '5a77bac0-38ad-43a0-971b-9dc5f3dab18c'),
					array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => '7e46573a-d16a-4d58-9e7d-9bab0d634dba'),
					array('key' => 'MAIN', 'name' => 'Main', 'active' => 1, 'guid' => '69b9f7ca-c17c-4487-954c-efdb0b4e1eb5'),
					array('key' => 'SEC', 'name' => 'Secondary', 'active' => 1, 'guid' => 'fc1fe90f-a0f2-4006-a034-34f070512664'),
				)),
			);

			$level = array();
			$level['guid'] = '5923eb68-bdf9-4556-ab30-87c06f1abde9';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateMenuEnum($force = false) {
		$ret = false;
		do {
			$name = 'Menu';
			$key = 'MENU';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'file' => array('key' => 'FILE', 'name' => 'File', 'active' => 1, 'guid' => '2fa74ea6-27f3-402d-a236-88b0405f0ae6', 'data' => array(
					'addPatient' => array('key' => 'ADDPATIENT', 'name' => 'Add Patient', 'active' => 1, 'guid' => 'eed5c430-b4b2-4f73-9e05-e5a619af711e'),
					'selectPatient' => array('key' => 'SELPATIENT', 'name' => 'Select Patient', 'active' => 1, 'guid' => '56fb669b-e235-4ef2-9068-117f0b9adf8c'),
					'reviewSignChanges' => array('key' => 'RSC', 'name' => 'Review / Sign Changes', 'active' => 1, 'guid' => '8d994911-30f2-42aa-afe0-21740a1b32c2'),
					'changePassword' => array('key' => 'CHANGEPW', 'name' => 'Change Password', 'active' => 1, 'guid' => 'd47b2b1a-916b-40ee-9a99-392e695a3819'),
					'editSigningKey' => array('key' => 'SIGNINGKEY', 'name' => 'Edit Signing Key', 'active' => 1, 'guid' => '8a5f6413-fdac-450f-841e-15ef5aceb2eb'),
					'myPreferences' => array('key' => 'MYPREF', 'name' => 'My Preferences', 'active' => 1, 'guid' => '33a1cd9e-18d2-4e04-86c9-26b189811a01'),
					'quit' => array('key' => 'QUIT', 'name' => 'Quit', 'active' => 1, 'guid' => 'c4bd0198-671b-4dd2-b3ac-dfce2fa31cc1'),
				)),
				'action' => array('key' => 'ACTION', 'name' => 'Action', 'active' => 1, 'guid' => '377126cf-02b9-4697-a65d-7c4236bf55d8', 'data' => array(
					'addVitals' => array('key' => 'ADDVITALS', 'name' => 'Add Vitals', 'active' => 1, 'guid' => '7f04b6ba-9f16-44f6-bc75-85808310dadb'),
					'print' => array('key' => 'PRINT', 'name' => 'Print', 'active' => 1, 'guid' => '90de6df2-2916-4851-8447-9447fcb11c13', 'data' => array(
						'flowSheet' => array('key' => 'FLOWSHEET', 'name' => 'Flow Sheet', 'active' => 1, 'guid' => '9decd97b-8462-4b89-89fc-991f53765e38'),
					)),
					'manageSchedule' => array('key' => 'MANSCHED', 'name' => 'Manage Schedules', 'active' => 1, 'guid' => '78dd7937-c0c6-407f-848f-192ebac2ac86'),
					'generalEncryption' => array('key' => 'GENCRYPT', 'name' => 'General Encryption', 'active' => 1, 'guid' => '29949f5c-eec0-4567-a030-d2f2c79e734e'),
					'export' => array('key' => 'EXPORT', 'name' => 'Export', 'active' => 1, 'guid' => '26ef871f-e164-4788-bb1d-ce25cfff9bad', 'data' => array(
						'cqm' => array('key' => 'CQM', 'name' => 'CQM', 'active' => 0, 'guid' => 'f906f8b6-1320-40c7-b6ee-f64bab1e3a56'),
						'hl7Immunizations' => array('key' => 'HL7IMM', 'name' => 'HL7 Immunizations', 'active' => 1, 'guid' => 'c65dd3de-5da6-4486-84b7-5c3be7dfd845'),
						'hl7LabTest' => array('key' => 'HL7LABTEST', 'name' => 'HL7 LabTest Results', 'active' => 1, 'guid' => '94127ad8-886e-440b-8f4a-ed3670c00267'),
						'publicHealth' => array('key' => 'PUBHEALTH', 'name' => 'Public Health', 'active' => 1, 'guid' => '9e078b42-74c6-45fa-8bdb-0401186972f6'),
					)),
					'auditLogViewer' => array('key' => 'AUDITLOG', 'name' => 'Audit Log Viewer', 'active' => 1, 'guid' => 'fe6dac7f-25b7-45a8-bfb0-a2b500a575bb'),
					'ccd' => array('key' => 'CCD', 'name' => 'CCD', 'active' => 1, 'guid' => '40a3a3a8-aabd-4029-8155-ec4ce17ee421', 'data' => array(
						'viewHL7CCD' => array('key' => 'VIEWHL7CCD', 'name' => 'View HL7 CCD/ASTM CCR', 'active' => 1, 'guid' => '67ee9875-73fa-462c-be97-50912b968aa3'),
						'allXML' => array('key' => 'ALLXML', 'name' => 'All XML', 'active' => 1, 'guid' => 'ad7f189e-d7f0-45c8-a906-94daf3fe2b52'),
						'visitXML' => array('key' => 'VISITXML', 'name' => 'Visit XML', 'active' => 1, 'guid' => '0d803438-e895-4407-8773-5af6c368965d'),
						'allView' => array('key' => 'ALLVIEW', 'name' => 'All View', 'active' => 1, 'guid' => '4c86f48a-cea7-4fea-a183-52cb435589b6'),
						'visitView' => array('key' => 'VISITVIEW', 'name' => 'Visit View', 'active' => 1, 'guid' => '657e81f6-0f62-4c48-8f0f-321826ea7d63'),
						'allPrint' => array('key' => 'ALLPRINT', 'name' => 'All Print', 'active' => 1, 'guid' => '65b98451-db43-4819-83c8-186a05a4fae0'),
						'visitPrint' => array('key' => 'VISITPRINT', 'name' => 'Visit Print', 'active' => 1, 'guid' => '719883f1-f552-4327-9e8b-7e89d7203a7d'),
					)),
					'patientList' => array('key' => 'PATLISTS', 'name' => 'Patient Lists', 'active' => 1, 'guid' => '5ca70027-1e5c-4257-a136-dca0524b2115'),
					'patientReminders' => array('key' => 'PATREMIND', 'name' => 'Patient Reminders', 'active' => 1, 'guid' => 'a47b7c3d-f23c-46b5-9919-77a5ffbf6dc8'),
					'emergencyAccess' => array('key' => 'EMERACCESS', 'name' => 'Emergency Access', 'active' => 1, 'guid' => '9625119d-eb1e-4c00-bf8d-3fc0ec5ad607'),
					'importLabHL7' => array('key' => 'IMPORTLAB', 'name' => 'Import Lab HL7', 'active' => 1, 'guid' => 'd8776b4a-743b-4d4c-81d1-9eaa23471972'),
					'appointmentHistory' => array('key' => 'APPHISTORY', 'name' => 'Appointment History', 'active' => 1, 'guid' => 'ad390c48-e73b-4834-aa14-f54a72a5dc95'),
					'patientAccount' => array('key' => 'PATIENTACC', 'name' => 'Patient Account', 'active' => 1, 'guid' => '8c13cad0-ff49-442b-94d3-f9169a0d4e85'),
					'unallocatedPayment' => array('key' => 'UNALLOCPAY', 'name' => 'Unallocated Payment', 'active' => 1, 'guid' => 'c6e2259b-b659-44c2-8bd4-33ecd7f4da1a'),
					'manualJournal' => array('key' => 'MANJOURNAL', 'name' => 'Manual Journal', 'active' => 1, 'guid' => '2087ff6d-0140-478c-8ff2-57f6b0429151'),
				)),
			);

			$level = array();
			$level['guid'] = '33fb13cb-577f-4a00-8765-b4a5334434c0';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['ormClass'] = 'MenuItem';
			$level['ormEditMethod'] = 'ormEditMethod';
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);

			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);

			$menu = new MenuItem();
			$menu->siteSection = 'All';
			$menu->type = 'freeform';
			$menu->active = 1;
			$menu->title = $enumeration->name;
			$menu->displayOrder = 0;
			$menu->parentId = 0;
			$menu->persist();

			$enumeration->ormId = $menu->menuId;
			$enumeration->persist();
			self::_generateMenuEnumerationTree($enumeration);

			$ret = true;
		} while(false);
		return $ret;
	}

	protected static function _generateMenuEnumerationTree(Enumeration $enumeration) {
		static $enumerationIds = array();
		$enumerationId = $enumeration->enumerationId;
		$enumerationsClosure = new EnumerationsClosure();
		$descendants = $enumerationsClosure->getEnumerationTreeById($enumerationId);
		$displayOrder = 0;
		foreach ($descendants as $enum) {
			if (isset($enumerationIds[$enum->enumerationId])) {
				continue;
			}
			$enumerationIds[$enum->enumerationId] = true;
			$displayOrder += 10;
			$menu = new MenuItem();
			$menu->siteSection = 'All';
			$menu->type = 'freeform';
			$menu->active = 1;
			$menu->title = $enum->name;
			//$menu->displayOrder = $displayOrder;
			$menu->displayOrder = $enum->enumerationId; // temporarily set displayOrder using the enumerationId
			$menu->parentId = $enumerationId;
			$menu->persist();

			$enum->ormId = $menu->menuId;
			$enum->persist();

			if ($enumerationId != $enum->enumerationId) { // prevents infinite loop
				self::_generateMenuEnumerationTree($enum);
			}
		}
	}

	public static function generateImmunizationPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientImmunization::ENUM_PARENT_NAME;
			$key = 'IP';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'series' => array('key' => 'series', 'name' => PatientImmunization::ENUM_SERIES_NAME, 'active' => 1, 'guid' => '36f25558-15e0-48a1-beb0-e3f697e79db0', 'data' => array(
					array('key' => 'partComp', 'name' => 'Partially complete', 'active' => 1, 'guid' => '218de21f-8abd-4516-a3e0-264cddbb7cea'),
					array('key' => 'complete', 'name' => 'Complete', 'active' => 1, 'guid' => '4e780c3b-b0ba-4f5d-b808-fafc378077ca'),
					array('key' => 'booster', 'name' => 'Booster', 'active' => 1, 'guid' => '454bddfc-ee88-4508-ba74-55f78b31c786'),
					array('key' => 'series1', 'name' => 'Series 1', 'active' => 1, 'guid' => '77e36a5a-ab90-4f5a-be6e-207d9fc9a67c'),
					array('key' => 'series2', 'name' => 'Series 2', 'active' => 1, 'guid' => 'f42770b1-c660-4886-a8d6-6dad658b78d0'),
					array('key' => 'series3', 'name' => 'Series 3', 'active' => 1, 'guid' => '16497c75-b137-4ab6-a1ae-d489ff90770f'),
					array('key' => 'series4', 'name' => 'Series 4', 'active' => 1, 'guid' => '84a4e29b-3d4c-42a2-b00c-1c25c63b1270'),
					array('key' => 'series5', 'name' => 'Series 5', 'active' => 1, 'guid' => 'a3a8b989-f55a-4195-9aaf-ca3fc2966115'),
					array('key' => 'series6', 'name' => 'Series 6', 'active' => 1, 'guid' => '79804260-160c-42b3-b87c-f58e33a368d3'),
					array('key' => 'series7', 'name' => 'Series 7', 'active' => 1, 'guid' => '63ccf298-e609-48d9-afda-3853e315e867'),
					array('key' => 'series8', 'name' => 'Series 8', 'active' => 1, 'guid' => '86e2e28d-0ec4-4d8c-ba75-16a39236915f'),
					array('key' => 'PR', 'name' => 'Patient Refused', 'active' => 1, 'guid' => 'ee56e4df-8849-4943-b403-dd4c942cf358'),
					array('key' => 'MU', 'name' => 'Medically Unnecessary', 'active' => 1, 'guid' => '428ed526-7c1e-4aeb-ad46-3657429aa1fd'),
					array('key' => 'NA', 'name' => 'Not Applicable', 'active' => 1, 'guid' => '0c3b8250-6713-46b6-beee-dceabf944064'),
				)),
				'section' => array('key' => 'section', 'name' => PatientImmunization::ENUM_SECTION_NAME, 'active' => 1, 'guid' => '17d2351c-ef39-4f8a-9b8a-896b116a5c14', 'data' => array(
					'other' => array('key' => 'other', 'name' => PatientImmunization::ENUM_SECTION_OTHER_NAME, 'active' => 1, 'guid' => '0a212a50-d9f8-412a-8109-3bc981461f3e', 'data' => array(
						array('key' => '82', 'name' => 'adenovirus, NOS', 'active' => 1, 'guid' => 'a69ef8f1-aca5-45fd-bdfa-6f917184d969', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '54', 'name' => 'adenovirus, type 4', 'active' => 1, 'guid' => '4ee8cc1b-046d-42c8-9206-27df873c5357', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '55', 'name' => 'adenovirus, type 7', 'active' => 1, 'guid' => '9f7edaf1-bdd8-4fa1-ab93-2d420e7e167e', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '24', 'name' => 'anthrax', 'active' => 1, 'guid' => '7db0557c-21ee-4536-b24f-726cf42fac09', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '19', 'name' => 'BCG', 'active' => 1, 'guid' => '64d6049a-b82d-448e-9cba-a29c5fa2dc5c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '27', 'name' => 'botulinum antitoxin', 'active' => 1, 'guid' => '1d5a3305-e6a5-4810-bfda-71bef4050a0a', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '26', 'name' => 'cholera', 'active' => 1, 'guid' => '0e871576-4778-4f42-afa3-52547830ec0f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '29', 'name' => 'CMVIG', 'active' => 1, 'guid' => '6e9d2abf-4566-48e1-84cd-71764874664b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '56', 'name' => 'dengue fever', 'active' => 1, 'guid' => 'adb6bd7c-e5a6-460f-ba51-de0d279779f2', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '12', 'name' => 'diphtheria antitoxin', 'active' => 1, 'guid' => '00437eae-0de6-4c4a-ae6d-339cb4f88d4f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '28', 'name' => 'DT (pediatric)', 'active' => 1, 'guid' => '1f572d3d-9835-43c2-8116-9721af6c7058', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '20', 'name' => 'DTaP', 'active' => 1, 'guid' => '7d906e0c-ce02-4753-8e99-d0e5d87591f7', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '106', 'name' => 'DTaP, 5 pertussis antigens', 'active' => 1, 'guid' => 'ef7f1a13-0448-4233-a47a-fb087897c63e', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '107', 'name' => 'DTaP, NOS', 'active' => 1, 'guid' => 'b4a91a21-70c6-4dcc-bd63-3a49a5d9554c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '110', 'name' => 'DTaP-Hep B-IPV', 'active' => 1, 'guid' => '74b271bf-1209-4e4c-889e-722eb4e7ce68', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '50', 'name' => 'DTaP-Hib', 'active' => 1, 'guid' => 'ab9f7e5e-2edd-4669-becb-e0144f17608c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '120', 'name' => 'DTaP-Hib-IPV', 'active' => 1, 'guid' => 'd62e7b40-55f2-48b6-9506-56f8d047c6af', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '01', 'name' => 'DTP', 'active' => 1, 'guid' => '6a2c27ae-413f-4733-ba7e-876c9e2bdd9b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '22', 'name' => 'DTP-Hib', 'active' => 1, 'guid' => '9b8d12c5-4b22-4f00-9400-fb090b4375d9', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '102', 'name' => 'DTP-Hib-Hep B', 'active' => 1, 'guid' => '6206e1c9-a37e-4035-8fbc-ba12a9792676', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '57', 'name' => 'hantavirus', 'active' => 1, 'guid' => '1f5a64e3-91b7-4ae5-92d3-8eb4d0563336', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '30', 'name' => 'HBIG', 'active' => 1, 'guid' => '3faf8ea4-bd68-4db1-94c0-8744ec5b7ff1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '52', 'name' => 'Hep A, adult', 'active' => 1, 'guid' => 'eb544526-1df8-4205-b9b7-f5f97da7175c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '85', 'name' => 'Hep A, NOS', 'active' => 1, 'guid' => '3f9a47f2-977f-45bd-aca9-ccce0ba8b655', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '31', 'name' => 'Hep A, pediatric, NOS', 'active' => 1, 'guid' => 'fe089882-de7f-49ab-b3c9-4179b6e63787', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod', 'data' => array(
							array('key' => '83', 'name' => 'Hep A, ped/adol, 2 dose', 'active' => 1, 'guid' => '8b6a843c-9963-4f98-a784-320ef03dd588', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
							array('key' => '84', 'name' => 'Hep A, ped/adol, 3 dose', 'active' => 1, 'guid' => '46f7460a-b267-42f1-942f-ee558ad679d9', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						)),
						array('key' => '104', 'name' => 'Hep A-Hep B', 'active' => 1, 'guid' => 'a6a164d0-942c-470f-ba47-df32428e08e3', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '08', 'name' => 'Hep B, adolescent or pediatric', 'active' => 1, 'guid' => '27167a48-2d54-4ce5-8484-8254070c56f8', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '42', 'name' => 'Hep B, adolescent/high risk infant', 'active' => 1, 'guid' => 'f48874f6-9dc0-487f-8175-eb29ce505df7', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '43', 'name' => 'Hep B, adult4', 'active' => 1, 'guid' => '64dd4c21-7622-4b75-a75e-8a79fb7549f8', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '44', 'name' => 'Hep B, dialysis', 'active' => 1, 'guid' => 'ca7bd4ab-bb7d-426d-9401-5cdeec8d3c1b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '45', 'name' => 'Hep B, NOS', 'active' => 1, 'guid' => 'b12ff4e7-9f34-4397-bb27-d9bfd538bb27', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '58', 'name' => 'Hep C', 'active' => 1, 'guid' => '7fcb2309-4bc1-4c3b-8291-5ff3ef56c160', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '59', 'name' => 'Hep E', 'active' => 1, 'guid' => '88d1395d-2afa-4f3c-a74f-39f3880afaf6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '60', 'name' => 'herpes simplex 2', 'active' => 1, 'guid' => '47571542-e0c0-4ec8-bd7f-dae5f52d58c9', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '47', 'name' => 'Hib (HbOC)', 'active' => 1, 'guid' => '97d16761-6fb7-43c6-92d3-8bc9625dc68f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '46', 'name' => 'Hib (PRP-D)', 'active' => 1, 'guid' => '4bb1aafe-65db-4a2f-95a6-1905ef5b9623', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '49', 'name' => 'Hib (PRP-OMP)', 'active' => 1, 'guid' => '76bf65ac-1b54-43ec-a673-a90417b7be94', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '48', 'name' => 'Hib (PRP-T)', 'active' => 1, 'guid' => '6bff8d8a-d3cc-4395-93f1-734ae6a0ee3f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '17', 'name' => 'Hib, NOS', 'active' => 1, 'guid' => '58b34ec3-1da1-4948-bc93-b0ffa63eac27', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '51', 'name' => 'Hib-Hep B', 'active' => 1, 'guid' => '26737169-185c-4028-af0c-bbd53d832330', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '61', 'name' => 'HIV', 'active' => 1, 'guid' => '78f73c2f-6e1d-4d22-aee6-eb10a8b7c22c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '118', 'name' => 'HPV, bivalent', 'active' => 1, 'guid' => '5b6e1655-52a2-4a09-a0d3-23bfa7f42f12', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '62', 'name' => 'HPV, quadrivalent', 'active' => 1, 'guid' => '2b7ce721-ba80-4915-a889-8cc99d54975e', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '86', 'name' => 'IG', 'active' => 1, 'guid' => '62cd7074-f8b6-4152-9442-7a50ae2d2aeb', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '14', 'name' => 'IG, NOS', 'active' => 1, 'guid' => '39e268f9-b778-462f-a7cd-7dbe69dc920a', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '87', 'name' => 'IGIV', 'active' => 1, 'guid' => 'c5890edf-5cb7-479e-8d55-93675f10ac3b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '123', 'name' => 'influenza, 1203 ', 'active' => 1, 'guid' => '7ff66f08-e816-43f7-86b9-009c48310bdb', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '111', 'name' => 'influenza, live, intranasal', 'active' => 1, 'guid' => '5d8b26d3-6cb6-4db6-a37c-c2e0932d7211', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '88', 'name' => 'influenza, NOS', 'active' => 1, 'guid' => '510ea00b-6424-417b-8512-9d5de35f897b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '15', 'name' => 'influenza, split (incl. purified surface antigen)', 'active' => 1, 'guid' => '57739f4d-dbd0-40b2-aa29-c990b657e8fa', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '16', 'name' => 'influenza, whole', 'active' => 1, 'guid' => '9b544233-b047-4a73-8651-f7d233a2a6b6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '10', 'name' => 'IPV', 'active' => 1, 'guid' => '7de7bf5d-473c-4fe4-8cea-c17bd476583d', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '39', 'name' => 'Japanese encephalitis', 'active' => 1, 'guid' => '93f36dab-0df5-460b-b3b6-15c9da2c2c0c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '63', 'name' => 'Junin virus', 'active' => 1, 'guid' => '70615fbb-40fd-4fb4-a7ae-b35af449ab95', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '64', 'name' => 'leishmaniasis', 'active' => 1, 'guid' => 'b12f741e-c320-450c-b947-2a132ae4c51f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '65', 'name' => 'leprosy', 'active' => 1, 'guid' => 'ffc018a5-a7be-4e3d-9b2a-f8f2df637606', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '66', 'name' => 'Lyme disease', 'active' => 1, 'guid' => '1b15c74d-5272-41f2-a592-d157e6f46d8f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '04', 'name' => 'M/R', 'active' => 1, 'guid' => '6849bf56-a264-468a-bbb3-3573507959c3', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '67', 'name' => 'malaria', 'active' => 1, 'guid' => '49eccab2-dba3-4b94-9e4e-cc9ea5057fb1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '05', 'name' => 'measles', 'active' => 1, 'guid' => 'cf20d29c-16b3-4c48-a487-74158f420c1d', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '68', 'name' => 'melanoma ', 'active' => 1, 'guid' => '6140c19a-b3ef-4555-a5a5-363ad613ad87', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '32', 'name' => 'meningococcal', 'active' => 1, 'guid' => '30c594a4-3b54-452f-b98e-1a671e8e1616', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '114', 'name' => 'meningococcal A,C,Y,W-135 diphtheria conjugate', 'active' => 1, 'guid' => 'e0f6799f-72b9-4e61-a9cc-3ed29ea103e6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '103', 'name' => 'meningococcal C conjugate', 'active' => 1, 'guid' => '10f0df45-53bd-46f9-a50a-fe9a759df2a2', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '108', 'name' => 'meningococcal, NOS', 'active' => 1, 'guid' => 'c2e97a28-8e92-40d0-9dab-58a5aa6c0ed4', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '03', 'name' => 'MMR', 'active' => 1, 'guid' => 'abba6d7e-013f-4dcd-8e6b-91d7f69de552', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '94', 'name' => 'MMRV', 'active' => 1, 'guid' => '6138ba95-3d34-42a5-8c0c-8ed75d07a82e', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '07', 'name' => 'mumps', 'active' => 1, 'guid' => '89814b26-e018-4ab0-8554-23706af10b52', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '998', 'name' => 'no vaccine administered', 'active' => 1, 'guid' => '592ed340-cfdf-4dc5-b0a5-a57d9dc6dbd1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '02', 'name' => 'OPV', 'active' => 1, 'guid' => '633439bd-72ad-4fdc-b691-74e86df1d604', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '69', 'name' => 'parainfluenza-3', 'active' => 1, 'guid' => '943d6eec-850a-4d8f-aad8-c5f8fdb607d4', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '11', 'name' => 'pertussis', 'active' => 1, 'guid' => 'a9bc6fc3-cc01-489e-b4d0-cc28b6bf39c7', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '23', 'name' => 'plague ', 'active' => 1, 'guid' => 'e58f78c2-c3a8-4726-8e6e-f3b37215e796', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '33', 'name' => 'pneumococcal', 'active' => 1, 'guid' => 'f8de5282-486e-416f-b800-416d2ad89f43', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '100', 'name' => 'pneumococcal conjugate', 'active' => 1, 'guid' => '33af758a-25a7-4fd5-8486-a739fa99b4df', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '109', 'name' => 'pneumococcal, NOS', 'active' => 1, 'guid' => 'd20c4699-8d8f-49f6-878a-94843367db3c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '89', 'name' => 'polio, NOS', 'active' => 1, 'guid' => '7da1050b-13da-4c53-800b-3d6e6e2f45af', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '70', 'name' => 'Q fever', 'active' => 1, 'guid' => '6f6d6761-8dcb-4288-a98c-0e724545b4a0', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '40', 'name' => 'rabies, intradermal injection', 'active' => 1, 'guid' => 'b1d93958-f86c-43fa-81e9-460180d582d8', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '18', 'name' => 'rabies, intramuscular injection', 'active' => 1, 'guid' => 'ed621bd0-cea3-4dee-a852-1e9cafe9db1a', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '90', 'name' => 'rabies, NOS', 'active' => 1, 'guid' => '8c5bf681-736f-4da4-ae45-258f28321e6f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '99', 'name' => 'RESERVED - do not use3', 'active' => 1, 'guid' => 'ba77ec34-c36e-41c5-ae08-508562fedb77', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '72', 'name' => 'rheumatic fever', 'active' => 1, 'guid' => '7331fa44-9e06-42d9-9f3c-aeeb9261abea', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '73', 'name' => 'Rift Valley fever', 'active' => 1, 'guid' => 'ed1d5035-5a2e-4fb4-abd8-7144c3c947cf', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '34', 'name' => 'RIG', 'active' => 1, 'guid' => 'c2842699-7783-4faa-a646-632378741176', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '119', 'name' => 'rotavirus, monovalent', 'active' => 1, 'guid' => '0314a884-7aae-4b1a-8833-adfb530816d6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '122', 'name' => 'rotavirus, NOS', 'active' => 1, 'guid' => 'cacba082-7ee6-46df-8fe1-46628f1a4a91', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '116', 'name' => 'rotavirus, pentavalent', 'active' => 1, 'guid' => 'fedcd09b-42a7-4644-aeab-c7f2caa83212', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '74', 'name' => 'rotavirus, tetravalent', 'active' => 1, 'guid' => '2180a272-3d29-4e1f-b375-d7f8ea583cfe', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '71', 'name' => 'RSV-IGIV', 'active' => 1, 'guid' => 'a4f4865c-c9a7-4b30-9575-d5b6f4960dfb', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '93', 'name' => 'RSV-MAb', 'active' => 1, 'guid' => '23daf135-4a2f-4ee4-838d-6dc873bfcfd3', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '06', 'name' => 'rubella', 'active' => 1, 'guid' => '6e2bacfd-9f04-40ee-9741-21a2f0cd81b1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '38', 'name' => 'rubella/mumps', 'active' => 1, 'guid' => '7a15629b-8025-49fc-b7c2-f67e096348c2', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '76', 'name' => 'Staphylococcus bacterio lysate', 'active' => 1, 'guid' => '040bd669-082b-4c03-99f1-5f6cf7bcc06f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '113', 'name' => 'Td (adult)', 'active' => 1, 'guid' => '45de53ee-ee5f-420e-bffa-800b5605fb9d', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '09', 'name' => 'Td (adult)', 'active' => 1, 'guid' => '45de53ee-ee5f-420e-bffa-800b5605fb9d', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '115', 'name' => 'Tdap', 'active' => 1, 'guid' => 'dc7d7b23-036d-4603-a632-69b1b20504eb', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '35', 'name' => 'tetanus toxoid', 'active' => 1, 'guid' => '8bbfa037-067c-499c-9cfd-505903cf8608', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '112', 'name' => 'tetanus toxoid, NOS', 'active' => 1, 'guid' => 'ae07524c-b0a5-47b7-ac8a-fb38033441bf', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '77', 'name' => 'tick-borne encephalitis', 'active' => 1, 'guid' => 'cbb22013-9876-48c6-a718-d48565f36b81', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '13', 'name' => 'TIG', 'active' => 1, 'guid' => '18bf1781-b57c-4349-964b-ed96e19bcf9c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '98', 'name' => 'TST, NOS', 'active' => 1, 'guid' => '6897649f-824b-445b-8af4-aeffa38a2136', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '95', 'name' => 'TST-OT tine test', 'active' => 1, 'guid' => '99116ca7-bd3e-4388-81b3-5e0b7c461b1f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '96', 'name' => 'TST-PPD intradermal', 'active' => 1, 'guid' => 'e0b21b2e-e0ea-4272-87cc-c7d3d1931de6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '97', 'name' => 'TST-PPD tine test', 'active' => 1, 'guid' => '36cd498f-b1ad-4428-88e6-aa17182fc3e8', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '78', 'name' => 'tularemia vaccine', 'active' => 1, 'guid' => '30346940-6708-4af8-a8e8-85155fe3747b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '91', 'name' => 'typhoid, NOS', 'active' => 1, 'guid' => '33c3497f-ec78-4188-bf4d-eee1802d7af1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '25', 'name' => 'typhoid, oral', 'active' => 1, 'guid' => '2eeffb22-901a-41c7-8eeb-b124bf6189a5', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '41', 'name' => 'typhoid, parenteral', 'active' => 1, 'guid' => '7fbff2cd-240f-49f2-afc4-e984127a86b6', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '53', 'name' => 'typhoid, parenteral, AKD (U.S. military)', 'active' => 1, 'guid' => '6b0fb3e2-a115-4ac7-bb9c-b85a22c147a3', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '101', 'name' => 'typhoid, ViCPs', 'active' => 1, 'guid' => '9f618282-2ed0-4d1d-ab8e-b628c39b3cb7', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '999', 'name' => 'unknown', 'active' => 1, 'guid' => 'a7121a45-ae3b-47de-b27a-3c9cdd4b06f1', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '75', 'name' => 'vaccinia (smallpox)', 'active' => 1, 'guid' => '0dab0f86-4a34-4856-8c48-145e355e500b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '105', 'name' => 'vaccinia (smallpox) diluted', 'active' => 1, 'guid' => 'b12b6322-11de-4300-941c-85ac095fd3d0', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '79', 'name' => 'vaccinia immune globulin', 'active' => 1, 'guid' => '44df0fb5-a1e4-4f63-a3cd-3211b7671a4f', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '21', 'name' => 'varicella ', 'active' => 1, 'guid' => '7becbb7d-902b-4c97-921d-a04003933be3', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '81', 'name' => 'VEE, inactivated', 'active' => 1, 'guid' => '4c013954-ca0d-4c1b-872f-d64dc1782c6b', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '80', 'name' => 'VEE, live', 'active' => 1, 'guid' => '59b329d7-e476-4299-9daa-dcaf73f8bcff', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '92', 'name' => 'VEE, NOS', 'active' => 1, 'guid' => '55c1d663-f706-4926-93dc-81447a000a39', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '36', 'name' => 'VZIG', 'active' => 1, 'guid' => '5a8067ce-c8cf-49a1-885f-3b659595aa51', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '117', 'name' => 'VZIG (IND)', 'active' => 1, 'guid' => '477a5f70-95a9-4c7d-8049-03bd984e1276', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '37', 'name' => 'yellow fever', 'active' => 1, 'guid' => '7b06a6a2-806a-456d-8775-7af6fc8c04c4', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
						array('key' => '121', 'name' => 'zoster', 'active' => 1, 'guid' => 'b417b6c6-95df-4a6d-8848-9af7015f226c', 'ormClass' => 'ImmunizationInventory', 'ormEditMethod' => 'ormEditMethod'),
					)),
					'common' => array('key' => 'common', 'name' => PatientImmunization::ENUM_SECTION_COMMON_NAME, 'active' => 1, 'guid' => 'd5e910d0-8ebe-4f14-aa95-e8be0d1689aa'),
				)),
				'reaction' => array('key' => 'reaction', 'name' => PatientImmunization::ENUM_REACTION_NAME, 'active' => 1, 'guid' => 'f2f28fce-59e8-404e-8900-d44ec0f433a6', 'data' => array(
					array('key' => 'FV', 'name' => 'Fever', 'active' => 1, 'guid' => '271766fc-d188-4963-bffe-6796b08b464b'),
					array('key' => 'IR', 'name' => 'Irritability', 'active' => 1, 'guid' => 'af646175-01d9-4edb-989e-856e7b691ec2'),
					array('key' => 'LRS', 'name' => 'Local reaction or swelling', 'active' => 1, 'guid' => 'c1960596-6264-4541-a505-20ba90babb6a'),
					array('key' => 'VM', 'name' => 'Vomiting', 'active' => 1, 'guid' => '8f8b75ab-f9bf-4669-b7a9-2b94c9ed6939'),
				)),
				'bodySite' => array('key' => 'bodySite', 'name' => PatientImmunization::ENUM_BODY_SITE_NAME, 'active' => 1, 'guid' => '2bd9a4a9-c44b-4581-9190-abd4521a3eef', 'data' => array(
					array('key' => 'BE', 'name' => 'Bilateral Ears', 'active' => 1, 'guid' => '817fad17-9204-4605-84ba-ee5bdaf397c8'),
					array('key' => 'LVL', 'name' => 'Left Vastus Lateralis', 'active' => 1, 'guid' => '992e7649-6325-4d3f-a1c5-4e2e6017cdd2'),
					array('key' => 'OU', 'name' => 'Bilateral Eyes', 'active' => 1, 'guid' => '2a7c3566-8154-4d05-bc38-d28009152cdf'),
					array('key' => 'NB', 'name' => 'Nebulized', 'active' => 1, 'guid' => '0fe768a5-47bc-486b-b18d-9a538eef4601'),
					array('key' => 'BN', 'name' => 'Bilateral Nares', 'active' => 1, 'guid' => '0846a5ae-56e8-49bd-b2b8-e7c91ff076a8'),
					array('key' => 'PA', 'name' => 'Perianal', 'active' => 1, 'guid' => 'a5d57924-bd8c-4288-a665-4758790542d8'),
					array('key' => 'BU', 'name' => 'Buttock', 'active' => 1, 'guid' => '744b3b4d-f0a0-452b-bd41-342bbed9c9b5'),
					array('key' => 'PERIN', 'name' => 'Perineal', 'active' => 1, 'guid' => '26829293-4e39-49f0-9e67-726f482e03d6'),
					array('key' => 'CT', 'name' => 'Chest Tube', 'active' => 1, 'guid' => '8165c43b-e4f8-4d67-bf6f-498151ca4555'),
					array('key' => 'RA', 'name' => 'Right Arm', 'active' => 1, 'guid' => '41788bf0-0168-4a15-9f1a-21958c0feb4b'),
					array('key' => 'LA', 'name' => 'Left Arm', 'active' => 1, 'guid' => 'b6d86eea-14e8-4217-a7d8-a35303413fb4'),
					array('key' => 'RAC', 'name' => 'Right Anterior Chest', 'active' => 1, 'guid' => 'c1b4bc0c-6053-4c20-aab4-dc9a12543e84'),
					array('key' => 'LAC', 'name' => 'Left Anterior Chest', 'active' => 1, 'guid' => 'a9c43a58-9793-4c71-bad8-01e28a8740e6'),
					array('key' => 'RACF', 'name' => 'Right Antecubital Fossa', 'active' => 1, 'guid' => '2e316cb7-7014-4ae0-acfc-9d52ed053d86'),
					array('key' => 'LACF', 'name' => 'Left Antecubital Fossa', 'active' => 1, 'guid' => '1036690d-3109-4058-b139-fa38795ee6f0'),
					array('key' => 'RD', 'name' => 'Right Deltoid', 'active' => 1, 'guid' => '95d95cef-942d-40c2-8909-80937939b5b1'),
					array('key' => 'LD', 'name' => 'Left Deltoid', 'active' => 1, 'guid' => '61e6108c-3dc5-4a3c-8d2e-02a31cc1338c'),
					array('key' => 'RE', 'name' => 'Right Ear', 'active' => 1, 'guid' => 'e99ee63e-e44e-4470-a0e2-daf562e00aa5'),
					array('key' => 'LE', 'name' => 'Left Ear', 'active' => 1, 'guid' => '513260f6-34f2-4fa5-a3ba-293651fc7117'),
					array('key' => 'REJ', 'name' => 'Right External Jugular', 'active' => 1, 'guid' => 'f551439b-d638-40ee-8803-73f896e8b35e'),
					array('key' => 'LEJ', 'name' => 'Left External Jugular', 'active' => 1, 'guid' => 'b39abab8-6c86-409a-90b3-4e87729b33a7'),
					array('key' => 'OD', 'name' => 'Right Eye', 'active' => 1, 'guid' => '73024d8a-75d7-479d-bdbd-a7f9f61e8582'),
					array('key' => 'OS', 'name' => 'Left Eye', 'active' => 1, 'guid' => 'e8fad4ac-b3a2-4dbe-aa62-a182c511e5d2'),
					array('key' => 'RF', 'name' => 'Right Foot', 'active' => 1, 'guid' => '1b9dc027-6038-4245-ab4d-b84166a7e851'),
					array('key' => 'LF', 'name' => 'Left Foot', 'active' => 1, 'guid' => 'a6dc3419-ee2c-4e4d-a6a2-ca5bdcded4a0'),
					array('key' => 'RG', 'name' => 'Right Gluteus Medius', 'active' => 1, 'guid' => 'd8791777-6219-4eda-a9d7-e1171eb4aa5a'),
					array('key' => 'LG', 'name' => 'Left Gluteus Medius', 'active' => 1, 'guid' => 'f51c256a-7cfb-4815-a68c-b3baf9186de6'),
					array('key' => 'RH', 'name' => 'Right Hand', 'active' => 1, 'guid' => 'b1e9ed45-815a-4c8f-85af-16ed40b7ae74'),
					array('key' => 'LH', 'name' => 'Left Hand', 'active' => 1, 'guid' => 'ab69504d-f2b2-4f22-bb08-20b2d5108d39'),
					array('key' => 'RIJ', 'name' => 'Right Internal Jugular', 'active' => 1, 'guid' => '34ceb997-22bd-47c4-a6ae-a072ec573e1b'),
					array('key' => 'LIJ', 'name' => 'Left Internal Jugular', 'active' => 1, 'guid' => 'f18f9da5-c38a-4393-8f3d-39215772f1b1'),
					array('key' => 'RLAQ', 'name' => 'Rt Lower Abd Quadrant', 'active' => 1, 'guid' => 'bf61a5bb-0ed4-4699-b6b2-aea0a209d1c2'),
					array('key' => 'LLAQ', 'name' => 'Left Lower Abd Quadrant', 'active' => 1, 'guid' => 'b22540f3-3577-4a69-aa59-7559294cb24d'),
					array('key' => 'RLFA', 'name' => 'Right Lower Forearm', 'active' => 1, 'guid' => '9cd3e2de-221c-480e-b0e5-a8c7cde4cb47'),
					array('key' => 'LLFA', 'name' => 'Left Lower Forearm', 'active' => 1, 'guid' => '306b597f-fb0c-497f-98b8-edfa9d12d5a8'),
					array('key' => 'RMFA', 'name' => 'Right Mid Forearm', 'active' => 1, 'guid' => 'e0bc5e05-a634-4071-8413-bbdc41dfbcb4'),
					array('key' => 'LMFA', 'name' => 'Left Mid Forearm', 'active' => 1, 'guid' => '1437a1b4-ce58-4960-af4b-817612bfe996'),
					array('key' => 'RN', 'name' => 'Right Naris', 'active' => 1, 'guid' => '40a6af4e-f6a7-457f-92ff-c08228492fe9'),
					array('key' => 'LN', 'name' => 'Left Naris', 'active' => 1, 'guid' => 'f72a9489-3451-418c-8e1a-6d333acde0fc'),
					array('key' => 'RPC', 'name' => 'Right Posterior Chest', 'active' => 1, 'guid' => 'da0ddc8d-f5e6-44f7-b92d-bf2faed31461'),
					array('key' => 'LPC', 'name' => 'Left Posterior Chest', 'active' => 1, 'guid' => '7f12beb0-24a7-4ced-ba88-50df1e6026dd'),
					array('key' => 'RSC', 'name' => 'Right Subclavian', 'active' => 1, 'guid' => '3842f3ea-adb2-4f56-b92e-fbf5012e27d7'),
					array('key' => 'LSC', 'name' => 'Left Subclavian', 'active' => 1, 'guid' => '704f3b7c-1b61-43fa-a990-81aeea5808eb'),
					array('key' => 'RT', 'name' => 'Right Thigh', 'active' => 1, 'guid' => '5bc22828-6d87-44c0-be70-ae489cf14ca8'),
					array('key' => 'LT', 'name' => 'Left Thigh', 'active' => 1, 'guid' => 'eec46a09-a81a-416a-b506-76f0ca7b170e'),
					array('key' => 'RUA', 'name' => 'Right Upper Arm', 'active' => 1, 'guid' => '3ce2d4ec-a5c7-4994-88c8-8fef7ac78381'),
					array('key' => 'LUA', 'name' => 'Left Upper Arm', 'active' => 1, 'guid' => 'fd91391a-3c4a-4cda-8f2f-c6a5dae78a1f'),
					array('key' => 'RUAQ', 'name' => 'Right Upper Abd Quadrant', 'active' => 1, 'guid' => '5f1bb656-6bcb-463e-b617-cf2fe3b9e875'),
					array('key' => 'LUAQ', 'name' => 'Left Upper Abd Quadrant', 'active' => 1, 'guid' => 'f3357ad4-1d32-4a3a-842b-00aba0f5fbb6'),
					array('key' => 'RUFA', 'name' => 'Right Upper Forearm', 'active' => 1, 'guid' => '9562c0a3-a780-472d-9bf7-26601f416056'),
					array('key' => 'LUFA', 'name' => 'Left Upper Forearm', 'active' => 1, 'guid' => 'eba8c5ca-d69b-48f3-95c2-c3d488e7bc32'),
					array('key' => 'RVL', 'name' => 'Right Vastus Lateralis', 'active' => 1, 'guid' => '514ddda0-acaf-472c-8ca3-f609b7f3e088'),
					array('key' => 'LVG', 'name' => 'Left Ventragluteal', 'active' => 1, 'guid' => '5f4c075e-d331-45cf-8ce5-91b2467473b6'),
					array('key' => 'RVG', 'name' => 'Right Ventragluteal', 'active' => 1, 'guid' => 'ff8c68c4-a595-4ce0-aadf-d44b82fc83cb'),
				)),
				'adminRoute' => array('key' => 'adminRoute', 'name' => PatientImmunization::ENUM_ADMINISTRATION_ROUTE_NAME, 'active' => 1, 'guid' => '81f906be-7ec4-4be3-926c-119878b772f8', 'data' => array(
					array('key' => 'AP', 'name' => 'Apply Externally', 'active' => 1, 'guid' => 'ac394c80-9f00-4fe1-a249-8037994bcf35'),
					array('key' => 'MM', 'name' => 'Mucous Membrane', 'active' => 1, 'guid' => 'a8b0f062-dcd2-409a-963d-0e426c5b427f'),
					array('key' => 'B', 'name' => 'Buccal', 'active' => 1, 'guid' => '01357f20-2afe-4ce6-8f00-bfb1e8cd6c07'),
					array('key' => 'NS', 'name' => 'Nasal', 'active' => 1, 'guid' => '6c583497-a24b-49e7-9261-202592e158b4'),
					array('key' => 'DT', 'name' => 'Dental', 'active' => 1, 'guid' => '529b0ffa-53f7-4f8f-be80-2b0e2418fc15'),
					array('key' => 'NG', 'name' => 'Nasogastric', 'active' => 1, 'guid' => '7ad9d50c-900b-473c-a8cf-7bee228f5218'),
					array('key' => 'EP', 'name' => 'Epidural', 'active' => 1, 'guid' => '54b2cbe3-debb-408c-a07a-ebb886f37458'),
					array('key' => 'NP', 'name' => 'Nasal Prongs', 'active' => 1, 'guid' => 'cbe5b44d-0883-4837-b8f0-e5d1567642d8'),
					array('key' => 'ET', 'name' => 'Endotrachial Tube', 'active' => 1, 'guid' => '65b31340-19f9-4dcb-b650-49e28e346c22'),
					array('key' => 'NT', 'name' => 'Nasotrachial Tube', 'active' => 1, 'guid' => 'cd65c684-9a09-445e-86f5-a77d102f45f5'),
					array('key' => 'GTT', 'name' => 'Gastrostomy Tube', 'active' => 1, 'guid' => '240272c3-605b-471d-bf04-41b5b3ca4785'),
					array('key' => 'OP', 'name' => 'Ophthalmic', 'active' => 1, 'guid' => '0774ea5c-a480-497c-ad01-67479ab4f307'),
					array('key' => 'GU', 'name' => 'GU Irrigant', 'active' => 1, 'guid' => '9027f986-baa1-4475-bfa7-566bb76b24ec'),
					array('key' => 'OT', 'name' => 'Otic', 'active' => 1, 'guid' => '2a83bf73-8246-4b9f-acb2-9fb161f06b01'),
					array('key' => 'IMR', 'name' => 'Immerse (Soak) Body Part', 'active' => 1, 'guid' => '402ad1f5-3938-4f54-881c-5b01afaaf983'),
					array('key' => 'OTH', 'name' => 'Other/Miscellaneous', 'active' => 1, 'guid' => 'f2b5b92d-69de-461c-9ad8-9baa146d4985'),
					array('key' => 'IA', 'name' => 'Intra-arterial', 'active' => 1, 'guid' => '8b8ed442-2c10-4939-9f23-9105a1eb514f'),
					array('key' => 'PF', 'name' => 'Perfusion', 'active' => 1, 'guid' => 'ca1b1f65-b991-499d-bad3-8e28f8bf8044'),
					array('key' => 'IB', 'name' => 'Intrabursal', 'active' => 1, 'guid' => '7efa6d17-a554-4701-b4e0-ba7e0b676fd0'),
					array('key' => 'PO', 'name' => 'Oral', 'active' => 1, 'guid' => '0919bf99-1809-452f-8e96-0d65c9a6a1ff'),
					array('key' => 'IC', 'name' => 'Intracardiac', 'active' => 1, 'guid' => '006799c8-a683-44ba-b1f5-d6c526d11873'),
					array('key' => 'PR', 'name' => 'Rectal', 'active' => 1, 'guid' => '5c5a4bf1-7f6c-4d3d-a190-ee5e354bdbb9'),
					array('key' => 'ICV', 'name' => 'Intracervical (uterus)', 'active' => 1, 'guid' => '5694bdf5-f905-4196-b3a2-80e6e58e4cdd'),
					array('key' => 'RM', 'name' => 'Rebreather Mask', 'active' => 1, 'guid' => '6e896602-2d9a-41bc-9de5-147aa2e85d7c'),
					array('key' => 'ID', 'name' => 'Intradermal', 'active' => 1, 'guid' => 'adb5593c-84a2-43a7-accd-e073d51a6cd8'),
					array('key' => 'SD', 'name' => 'Soaked Dressing', 'active' => 1, 'guid' => '881e62bd-702a-4e6e-9221-d683acf70a04'),
					array('key' => 'IH', 'name' => 'Inhalation', 'active' => 1, 'guid' => '6ecf7fb1-7bce-4b84-83d6-5c5fad224640'),
					array('key' => 'SC', 'name' => 'Subcutaneous', 'active' => 1, 'guid' => 'd68d4a12-df09-4993-91f0-b73f028cdeb0'),
					array('key' => 'IHA', 'name' => 'Intrahepatic Artery', 'active' => 1, 'guid' => '74e1dceb-5a23-47a0-95dc-3cfeb5b7f9df'),
					array('key' => 'SL', 'name' => 'Sublingual', 'active' => 1, 'guid' => '0a527e0a-76bb-41eb-8782-3017fb328775'),
					array('key' => 'IM', 'name' => 'Intramuscular', 'active' => 1, 'guid' => '85ad9ae0-ef7c-4edc-8faf-c891444cce75'),
					array('key' => 'TP', 'name' => 'Topical', 'active' => 1, 'guid' => '0dc5e45a-8718-42d7-b5b0-c5d1e9608eb6'),
					array('key' => 'IN', 'name' => 'Intranasal', 'active' => 1, 'guid' => '8241722b-7ef2-4f57-b749-58cab1b8b0eb'),
					array('key' => 'TRA', 'name' => 'Tracheostomy', 'active' => 1, 'guid' => 'e7b7ce60-3758-45a5-8b7b-d3e48da9616f'),
					array('key' => 'IO', 'name' => 'Intraocular', 'active' => 1, 'guid' => '62fda0e6-6373-4a24-8a59-6df4317789f5'),
					array('key' => 'TD', 'name' => 'Transdermal', 'active' => 1, 'guid' => '683d6ea4-f752-45db-a65d-b5eff1570a23'),
					array('key' => 'IP', 'name' => 'Intraperitoneal', 'active' => 1, 'guid' => '4cab7879-e4d8-4e84-bb1f-5d4cf132e1ea'),
					array('key' => 'TL', 'name' => 'Translingual', 'active' => 1, 'guid' => '73dfdc99-bdc4-4aa9-bfe5-37ec606f3dc9'),
					array('key' => 'IS', 'name' => 'Intrasynovial', 'active' => 1, 'guid' => '8d84567b-ee17-4733-81d5-6f1130eb69aa'),
					array('key' => 'UR', 'name' => 'Urethral', 'active' => 1, 'guid' => '253fc295-82a5-412f-b78e-b71409e8705e'),
					array('key' => 'IT', 'name' => 'Intrathecal', 'active' => 1, 'guid' => 'e858045d-89ee-4c04-b636-ca8aaba495eb'),
					array('key' => 'VG', 'name' => 'Vaginal', 'active' => 1, 'guid' => 'f3de7a2c-84d4-4f47-9b05-935d39795816'),
					array('key' => 'IU', 'name' => 'Intrauterine', 'active' => 1, 'guid' => 'ccd31159-c27e-4922-a38c-1d95a5a7975a'),
					array('key' => 'VM', 'name' => 'Ventimask', 'active' => 1, 'guid' => '5e966808-6d1c-4529-8b96-81f1903c51ec'),
					array('key' => 'IV', 'name' => 'Intravenous', 'active' => 1, 'guid' => '6c382cec-1cd9-441a-aaea-eb38e101bc9c'),
					array('key' => 'WND', 'name' => 'Wound', 'active' => 1, 'guid' => 'b446b8d1-ea0f-4934-9585-ca624e562693'),
					array('key' => 'MTH', 'name' => 'Mouth/Throat', 'active' => 1, 'guid' => '43516e51-bfdf-480b-b155-c351d4771552'),
				)),
			);

			$level = array();
			$level['guid'] = 'bde63462-e977-491c-8fd1-a7773a8ce890';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateTeamPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = TeamMember::ENUM_PARENT_NAME;
			$key = 'TP';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'blueTeam' => array('key' => 'BLUE', 'name' => 'Blue', 'active' => 1, 'guid' => '26d1a11f-0edf-4a4e-82f8-db04f8317beb', 'data' => array(
					array('key' => 'ATTENDING', 'name' => 'Attending', 'active' => 1, 'guid' => '04d8f888-1187-4887-b44d-9e49abad78b7', 'data' => array(
						array('key' => 'NURSE', 'name' => 'Nurse', 'active' => 1, 'guid' => '5a786290-dd79-40a7-8025-7354d876a1f8'),
						array('key' => 'PA', 'name' => 'Physician Assistant', 'active' => 1, 'guid' => 'aae532ad-f47e-441a-ad6a-fc358ef43bec'),
						array('key' => 'NP1', 'name' => 'Nurse Practitioner', 'active' => 1, 'guid' => 'f7d6ceb6-349c-40ac-ad0b-e9c5f08b36e5'),
						array('key' => 'NP2', 'name' => 'Nurse Practitioner', 'active' => 1, 'guid' => 'd924972e-dc79-4922-ae96-50fcb22a5cae'),
					)),
				)),
			);

			$level = array();
			$level['guid'] = 'bf3a3c1e-f1a6-4af0-a734-f03234e0eeb1';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['ormClass'] = 'TeamMember';
			$level['ormEditMethod'] = 'ormEditMethod';
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateHSAPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = HealthStatusAlert::ENUM_PARENT_NAME;
			$key = 'HP';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'LSA', 'name' => 'Lab Status Alerts', 'active' => 1, 'guid' => '085a0163-9c6a-4af4-9b01-24bd135c0088'),
				array('key' => 'VSA', 'name' => 'Vitals Status Alerts', 'active' => 1, 'guid' => '2094150c-83b1-498f-a058-c8f2af382262'),
				array('key' => 'NSA', 'name' => 'Note Status Alerts', 'active' => 1, 'guid' => 'e49ad008-36c6-45ff-92cf-8715c867840a'),
			);

			$level = array();
			$level['guid'] = 'e4c12dae-e1f7-4c7a-a6e5-175ce4fb3412';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateReasonPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientNote::ENUM_REASON_PARENT_NAME;
			$key = 'RP';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'RPCB', 'name' => 'Call Back', 'active' => 1, 'guid' => '5f223291-2456-4f6f-9d2a-a3762bf6b654'),
				array('key' => 'RPCP', 'name' => 'Check Progress', 'active' => 1, 'guid' => '763e2c00-0e78-4d41-a9d9-db9770fc8a2a'),
				array('key' => 'RPC', 'name' => 'Converted', 'active' => 1, 'guid' => '37dff69c-bc41-4743-a19e-d5e65ce47bb8'),
				array('key' => 'RPRT', 'name' => 'Repeat Test', 'active' => 1, 'guid' => '5c356d89-f9c4-48f0-9b11-b97f5b51bb21'),
				array('key' => 'RPO', 'name' => 'Other', 'active' => 1, 'guid' => '8f882780-4b00-49b2-ba69-5fefd6904ec7'),
				array('key' => 'RPNA', 'name' => 'N/A', 'active' => 1, 'guid' => '892bc671-0361-4996-af6a-8d65d5b209d0'),
			);

			$level = array();
			$level['guid'] = 'b1ff20ff-bd6d-41f1-b4f2-d1e8ce4299f0';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateProcedurePreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientProcedure::ENUM_PARENT_NAME;
			$key = 'ProcPref';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'GIPROC', 'name' => 'GI PROCEDURES', 'active' => 1, 'guid' => '9cd715cc-a030-4440-a305-d08f47899cfb'),
				array('key' => 'COLONOSCOP', 'name' => 'COLONOSCOPY', 'active' => 1, 'guid' => 'd7c1831e-768a-4728-b3e0-02650238bc32'),
			);

			$level = array();
			$level['guid'] = '2f017585-a889-49a2-8f52-e01e58e540ca';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateEducationPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientEducation::ENUM_EDUC_PARENT_NAME;
			$key = 'EduPref';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				'section' => array('key' => 'SECTION', 'name' => PatientEducation::ENUM_EDUC_SECTION_NAME, 'active' => 1, 'guid' => '09d0f1e5-715d-4c87-9ead-8169cf8ebf13', 'data' => array(
					'other' => array('key' => 'OTHER', 'name' => PatientEducation::ENUM_EDUC_SECTION_OTHER_NAME, 'active' => 1, 'guid' => 'ad3cfda0-b93b-4bec-ab7b-d69b4caaa1fd', 'data' => array(
						array('key' => 'HFA', 'name' => 'HF ACTIVITY', 'active' => 1, 'guid' => 'a6ad54bf-0b62-4f27-a723-d85968dfa899'),
						array('key' => 'HFD', 'name' => 'HF DIET', 'active' => 1, 'guid' => 'f6e870af-a874-43fe-890b-f00908558d34'),
						array('key' => 'HFDM', 'name' => 'HF DISCHARGE MEDS', 'active' => 1, 'guid' => 'de847c7c-efa8-4fe5-aa74-5677b05ec199'),
						array('key' => 'HFF', 'name' => 'HF FOLLOWUP', 'active' => 1, 'guid' => '261dd2c8-d476-48a8-8b2c-478f6756ea27'),
						array('key' => 'HFS', 'name' => 'HF SYMPTOMS', 'active' => 1, 'guid' => '8e110ae8-c302-43be-852a-1c1d319e04d4'),
					)),
					'common' => array('key' => 'COMMON', 'name' => PatientEducation::ENUM_EDUC_SECTION_COMMON_NAME, 'active' => 1, 'guid' => 'c58d0def-0dfc-4e64-9765-aa3962c2f7f8', 'data' => array(
						array('key' => 'HYPER', 'name' => 'Hypertension', 'active' => 1, 'guid' => '24c2c962-86e5-462d-ae4d-7d78d6d2ca64'),
						array('key' => 'AN', 'name' => 'Adolescent Nutrition', 'active' => 1, 'guid' => '94c62e5e-2351-4562-acb9-5fea1ef9ccf8'),
						array('key' => 'PN', 'name' => 'Pediatric Nutrition', 'active' => 1, 'guid' => 'f815f930-922c-4526-8c5f-a79d787e7726'),
						array('key' => 'APA', 'name' => 'Adolescent Physical Activity', 'active' => 1, 'guid' => 'f0de10b7-0a30-47dc-ae55-9b2a0980baa6'),
						array('key' => 'PPA', 'name' => 'Pediatric Physical Activity', 'active' => 1, 'guid' => '596c776c-08ae-4a27-96a4-46b96e5bbbba'),
					)),
				)),
				'level' => array('key' => 'LEVEL', 'name' => PatientEducation::ENUM_EDUC_LEVEL_NAME, 'active' => 1, 'guid' => 'e23beb46-4534-4a1d-88d7-175c3c55171e', 'data' => array(
					array('key' => 'POOR', 'name' => 'Poor', 'active' => 1, 'guid' => 'f5a283fe-e617-4aa1-b422-0abfedd2bf89'),
					array('key' => 'FAIR', 'name' => 'Fair', 'active' => 1, 'guid' => '35b9d1ca-f40e-4968-bb5b-b15b8b481ff8'),
					array('key' => 'GOOD', 'name' => 'Good', 'active' => 1, 'guid' => 'c4f1b8a8-49ba-4212-892b-4418b62f7dfc'),
					array('key' => 'GNA', 'name' => 'Group-no assessment', 'active' => 1, 'guid' => 'b5a37abd-69cb-480e-9c92-3b4110620cd1'),
					array('key' => 'REFUSED', 'name' => 'Refused', 'active' => 1, 'guid' => 'cf97b57c-506f-44dd-8770-00bbf76f5128'),
				)),
			);

			$level = array();
			$level['guid'] = '3cef009a-9562-4355-91ef-bfec93244027';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateExamResultPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientExam::ENUM_RESULT_PARENT_NAME;
			$key = 'Exam_Res';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'ABNORMAL', 'name' => 'Abnormal', 'active' => 1, 'guid' => '9bf75109-4660-4b5f-8209-e3227bce347f'),
				array('key' => 'NORMAL', 'name' => 'Normal', 'active' => 1, 'guid' => '3960a4b6-1b2f-4284-bf86-9d24aa6c67d1'),
				array('key' => 'REFUSED', 'name' => 'Refused', 'active' => 1, 'guid' => 'e46d5e04-1ccd-4c2d-a9f6-c73c99c9b784'),
				array('key' => 'MU', 'name' => 'Medically Unnecessary', 'active' => 1, 'guid' => '7d164c60-e590-41c2-be09-a54876a0ed63'),
				array('key' => 'NA', 'name' => 'Not Applicable', 'active' => 1, 'guid' => '9700af77-8d0e-4843-bdbd-3a5be73f8b10'),
			);

			$level = array();
			$level['guid'] = '18422748-862b-428e-91e3-145ec3d57f5c';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateExamOtherPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = PatientExam::ENUM_OTHER_PARENT_NAME;
			$key = 'Exam_Other';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'EXAMABD', 'name' => 'ABDOMEN EXAM', 'active' => 1, 'guid' => '7ad89c31-b522-4c8b-8bb6-d6ce74d214fc'),
				array('key' => 'EXAMAMS', 'name' => 'AUDIOMETRIC SCREENING', 'active' => 1, 'guid' => 'e4374dce-22d4-4745-b9f6-7b1779b6290d'),
				array('key' => 'EXAMAMT', 'name' => 'AUDIOMETRIC THRESHOLD', 'active' => 1, 'guid' => 'df9c19a4-dafe-49fb-8170-60fe404958cb'),
				array('key' => 'EXAMBREAST', 'name' => 'BREAST EXAM', 'active' => 1, 'guid' => '10996c24-5a76-4573-9450-7ea6a277901c'),
				array('key' => 'EXAMCHEST', 'name' => 'CHEST EXAM', 'active' => 1, 'guid' => '1b096999-31c0-40b6-a5ce-b7985df4ec30'),
				array('key' => 'EXAMPHYS', 'name' => 'Physical Exam', 'active' => 1, 'guid' => '07987ba7-fe86-4302-9e6d-1b2aa6bae59c'),
			);

			$level = array();
			$level['guid'] = '770f3bad-f41d-492a-9dcf-db8631c9f471';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateMedicationPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = Medication::ENUM_PARENT_NAME;
			$key = 'MED_PREF';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'ADM_SCHED', 'name' => Medication::ENUM_ADMIN_SCHED, 'active' => 1, 'guid' => 'd9c17d1b-8826-4f16-8cfc-fdca53a28c56', 'data' => array(
					array('key' => 'BID', 'name' => 'twice per day', 'active' => 1, 'guid' => '568fc969-9d30-4080-b073-dfa251a2f59f'),
					array('key' => 'TID', 'name' => 'three times per day', 'active' => 1, 'guid' => 'b9f9ae03-4f9c-472f-8145-cf3b0ea71a47'),
					array('key' => 'MO-WE-FR', 'name' => 'once on monday, once on wednesday, once on friday', 'active' => 1, 'guid' => 'c7390092-7e8a-46c3-b91a-ce0a994ae328'),
					array('key' => 'NOW', 'name' => 'right now', 'active' => 1, 'guid' => 'cd25d187-2803-4ebb-ba61-7e52f6e10a13'),
					array('key' => 'ONCE', 'name' => 'one time', 'active' => 1, 'guid' => '2d0fbb67-c7b1-4b7c-afd8-5e062c90dedc'),
					array('key' => 'Q12H', 'name' => 'every 12 hours', 'active' => 1, 'guid' => 'f85053cd-c7be-4620-b838-ebd95b59525c'),
					array('key' => 'Q24H', 'name' => 'every 24 hours', 'active' => 1, 'guid' => 'df7ac22c-f17e-4409-afd6-e220f8b2ac5f'),
					array('key' => 'Q2H', 'name' => 'every 2 hours', 'active' => 1, 'guid' => 'b2ee68e0-8b3d-4a23-afe7-93ad6492273f'),
					array('key' => 'Q3H', 'name' => 'every 3 hours', 'active' => 1, 'guid' => '69f90ed8-5551-4000-a4fa-0e7fc1a8ae5e'),
					array('key' => 'Q4H', 'name' => 'every 4 hours', 'active' => 1, 'guid' => '6297f4e0-96e0-4869-870a-c03ec5a738aa'),
					array('key' => 'Q6H', 'name' => 'every 6 hours', 'active' => 1, 'guid' => '0444e2a2-ebdc-4eb5-8d17-a3009ab7e1fa'),
					array('key' => 'Q8H', 'name' => 'every 8 hours', 'active' => 1, 'guid' => '73b036d5-9641-4a4a-8005-70b048cde9a8'),
					array('key' => 'Q5MIN', 'name' => 'every 5 minutes', 'active' => 1, 'guid' => '31f3d346-0f43-4a6f-bf74-5546f74e1e5f'),
					array('key' => 'QDAY', 'name' => 'once per day', 'active' => 1, 'guid' => 'f342105e-eb93-4057-8120-660b58c086df'),
				)),
			);

			$level = array();
			$level['guid'] = '29e219fb-95ff-4c5b-8c5b-691f2ee065df';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateColorPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = Room::ENUM_COLORS_NAME;
			$key = 'colors';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => '#FFF8DC', 'name' => 'Cornsilk', 'active' => 1, 'guid' => '6252df95-129a-4c61-9670-1b8eb362a40d'),
				array('key' => '#FAEBD7', 'name' => 'Antiquewhite', 'active' => 1, 'guid' => 'b6941bbc-e61a-4d47-866e-f5ce46d6cf43'),
				array('key' => '#FFF5EE', 'name' => 'Seashell', 'active' => 1, 'guid' => '42afd25f-20a4-4db1-82be-79ef2dece84b'),
				array('key' => '#FAF0E6', 'name' => 'Linen', 'active' => 1, 'guid' => '4abe6e8c-4e23-4794-92c0-4807e66135b8'),
				array('key' => '#FFFFF0', 'name' => 'Ivory', 'active' => 1, 'guid' => '034273ff-47fb-437b-a8a3-c8a2773fca1d'),
				array('key' => '#FFFAF0', 'name' => 'Floralwhite', 'active' => 1, 'guid' => 'd418d4cf-d98a-47a1-975a-10f0903250d0'),
				array('key' => '#FFFAFA', 'name' => 'Snow', 'active' => 1, 'guid' => '746c41e7-fd4d-40e2-84df-91dd2eaf9c35'),
				array('key' => '#F0FFFF', 'name' => 'Azure', 'active' => 1, 'guid' => '681e73e8-c939-4ae3-8bfd-45882cd5f2b9'),
				array('key' => '#F5FFFA', 'name' => 'Mintcream', 'active' => 1, 'guid' => '08994040-0d8b-48fe-a4ab-5dbd638d9fa2'),
				array('key' => '#F8F8FF', 'name' => 'Ghostwhite', 'active' => 1, 'guid' => '12938a56-611b-459a-a3dc-c6771ff58053'),
				array('key' => '#F0FFF0', 'name' => 'Honeydew', 'active' => 1, 'guid' => '62dc90bd-c268-41cd-a8a0-670e6f13a3ea'),
				array('key' => '#F0F8FF', 'name' => 'Aliceblue', 'active' => 1, 'guid' => '79905de0-20cc-49ae-b743-31f15ee9f343'),
				array('key' => '#F5F5DC', 'name' => 'Beige', 'active' => 1, 'guid' => '05a1d654-c7ab-4724-8e0a-0bbc873904b0'),
				array('key' => '#FDF5E6', 'name' => 'Oldlace', 'active' => 1, 'guid' => '7315e6ce-cc8c-43d8-899c-56074ded2fd6'),
				array('key' => '#FFE4C4', 'name' => 'Bisque', 'active' => 1, 'guid' => '725a860b-b06b-4f8f-a7a0-b5db71c8e820'),
				array('key' => '#FFE4B5', 'name' => 'Moccasin', 'active' => 1, 'guid' => '89fa0884-cfff-40ea-830a-4ac81284211e'),
				array('key' => '#F5DEB3', 'name' => 'Wheat', 'active' => 1, 'guid' => 'f4dcca5e-c701-4bcd-9223-540263c6b63d'),
				array('key' => '#FFDEAD', 'name' => 'Navajowhite', 'active' => 1, 'guid' => '0665a199-01d4-4948-9919-60db6cfc26d3'),
				array('key' => '#FFEBCD', 'name' => 'Blanchedalmond', 'active' => 1, 'guid' => '2dc5794d-ca86-4c72-84bf-4d6b8b38a128'),
				array('key' => '#D2B48C', 'name' => 'Tan', 'active' => 1, 'guid' => 'a16b64d1-a8f8-4b95-816d-24b9b8705400'),
				array('key' => '#FFE4E1', 'name' => 'Mistyrose', 'active' => 1, 'guid' => '6fc0603d-9019-4598-a753-0fed2646c0f3'),
				array('key' => '#FFF0F5', 'name' => 'Lavenderblush', 'active' => 1, 'guid' => 'ed3a9496-9694-4094-944a-c69ffffe577c'),
				array('key' => '#E6E6FA', 'name' => 'Lavender', 'active' => 1, 'guid' => '5483230b-eec3-4598-a384-742c1271eec7'),
				array('key' => '#87CEFA', 'name' => 'Lightskyblue', 'active' => 1, 'guid' => '15b1c781-233b-4fdf-ad0e-ca0b48dfaf1f'),
				array('key' => '#87CEEB', 'name' => 'Skyblue', 'active' => 1, 'guid' => 'd3343152-c318-4a3d-879c-edddedeb4648'),
				array('key' => '#00BFFF', 'name' => 'Deepskyblue', 'active' => 1, 'guid' => 'e487f64d-173d-441a-a968-4f8997e4eca6'),
				array('key' => '#7FFFD4', 'name' => 'Aquamarine', 'active' => 1, 'guid' => '5447f87c-1fd4-43a1-8f68-5e558dd95e7a'),
				array('key' => '#6495ED', 'name' => 'Cornflowerblue', 'active' => 1, 'guid' => '6f4ef15a-831d-4e23-ab43-fcc8cadac22d'),
				array('key' => '#E9967A', 'name' => 'Darksalmon', 'active' => 1, 'guid' => '43f00373-53b2-4282-9115-9b14f88738c7'),
				array('key' => '#FFA07A', 'name' => 'Lightsalmon', 'active' => 1, 'guid' => 'bcc57daf-4b9d-4b32-bd3a-780c77f259ff'),
				array('key' => '#B0E0E6', 'name' => 'Powderblue', 'active' => 1, 'guid' => 'f1bb8afb-eb16-49c8-a29d-b79783bb5a9d'),
			);

			$level = array();
			$level['guid'] = 'f45a4d8e-ea55-40ba-9f48-07ac00acca43';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateFacilitiesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Facilities';
			$key = 'FACILITIES';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$level = array();
			$level['guid'] = '7bf4739b-0d15-455b-85a8-cdeb886daff6';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generatePaymentTypesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Payment Types';
			$key = 'PAY_TYPES';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'VISA', 'name' => 'Visa', 'active' => 1, 'guid' => 'cfc36cc6-76b0-4ea0-a6de-7e8d5217f06a'),
				array('key' => 'MASTERCARD', 'name' => 'MasterCard', 'active' => 1, 'guid' => '1665c4b5-0f91-4c86-b7e6-317ad95efe07'),
				array('key' => 'AMEX', 'name' => 'AMEX', 'active' => 1, 'guid' => '7c29ff7b-966d-428a-8ce5-037a36e73576'),
				array('key' => 'CHECK', 'name' => 'Check', 'active' => 1, 'guid' => '2c7dbe2b-0f24-420f-b950-ba6a024367c4'),
				array('key' => 'CASH', 'name' => 'Cash', 'active' => 1, 'guid' => 'b9eb5d9f-0c95-4ad3-9e51-62a0452083da'),
				array('key' => 'REMITTANCE', 'name' => 'Remittance', 'active' => 1, 'guid' => 'f353bced-e140-4af4-bfd2-deedf575b244'),
				array('key' => 'CORRECTION', 'name' => 'Correction Payment', 'active' => 1, 'guid' => 'fd36d634-6e0b-41c4-bac5-b650a1d5b6d8'),
				array('key' => 'LABPAYMENT', 'name' => 'Labs Payment', 'active' => 1, 'guid' => '9ab537c4-198d-44c1-b5d6-7f60d49bc5c4'),
				array('key' => 'MEDPAYMENT', 'name' => 'Medication Payment', 'active' => 1, 'guid' => '02232de4-014f-4d26-b4eb-717c0e09e607'),
				array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => '6b5a30ba-3f8b-4c00-9a6a-be5f72694c51'),
				array('key' => 'VISITPAY', 'name' => 'Visit Payment', 'active' => 1, 'guid' => 'af075c09-a5dc-4ad4-8ddf-9dd7312472e2'),
				array('key' => 'DISCOUNT', 'name' => 'Discount', 'active' => 1, 'guid' => '704a29e4-029a-4c69-a441-a7c32ba74ee4'),
			);

			$level = array();
			$level['guid'] = 'd1d9039a-a21b-4dfb-b6fa-ec5f41331682';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateCodingPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Coding Preferences';
			$key = 'CODINGPREF';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'VISITYPE', 'name' => 'Visit Type Sections', 'active' => 1, 'guid' => '9eb793f8-1d5d-4ed5-959d-1e238361e00a', 'ormClass' => 'Visit', 'ormEditMethod' => 'ormVisitTypeEditMethod', 'data' => array(
					array('key' => 'NEWPATIENT', 'name' => 'New Patient', 'active' => 1, 'guid' => 'ebc41ebe-dd6b-4b78-97a7-63298ddef675', 'ormClass' => 'Visit', 'ormEditMethod' => 'ormVisitTypeEditMethod'),
					array('key' => 'ESTPATIENT', 'name' => 'Established Patient', 'active' => 1, 'guid' => '519b2620-b893-4bac-8d46-7daefd69aa1e', 'ormClass' => 'Visit', 'ormEditMethod' => 'ormVisitTypeEditMethod'),
					array('key' => 'CONSULT', 'name' => 'Consultations', 'active' => 1, 'guid' => 'd2ba49ec-f2b6-4183-8495-c9c1f8386414', 'ormClass' => 'Visit', 'ormEditMethod' => 'ormVisitTypeEditMethod'),
				)),
				array('key' => 'PROCEDURE', 'name' => 'Procedure Sections', 'active' => 1, 'guid' => '8e6a2456-1710-46be-a018-2afb0ec2829f', 'ormClass' => 'ProcedureCodesCPT', 'ormEditMethod' => 'ormEditMethod'),
				array('key' => 'DIAGNOSIS', 'name' => 'Diagnosis Sections', 'active' => 1, 'guid' => 'fac51e51-95fd-485e-a8f3-62e1228057ad', 'ormClass' => 'DiagnosisCodesICD', 'ormEditMethod' => 'ormEditMethod'),
				array('key' => 'PROC_MOD', 'name' => 'Procedure Modifiers', 'active' => 1, 'guid' => '2b15d494-dce4-4d27-89b5-ddd6f6fc1439', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod', 'data' => array(
					array('key' => '21', 'name' => 'Prolonged E/M services', 'active' => 1, 'guid' => 'd446165b-a3d3-420b-9a06-3e27157c93b1', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '22', 'name' => 'Unusual procedural services', 'active' => 1, 'guid' => '1a84e436-d261-490e-8df1-f9b42300f1d7', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '23', 'name' => 'A procedure that usually requires either no anesthesia', 'active' => 1, 'guid' => '252f7fe3-7342-4bcc-9ff0-7c9b8ceb1cba', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '24', 'name' => 'Unrelated E/M service', 'active' => 1, 'guid' => '00ceaa2a-ec9d-434e-84e5-f1f1398acd0f', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '25', 'name' => 'Significant and separately identifiable E/M services', 'active' => 1, 'guid' => '86d41d14-c49e-46de-8ed1-e97b32d01a74', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '26', 'name' => 'Some procedures have a combined professional and technical component', 'active' => 1, 'guid' => 'e1f8c2da-f3b9-492f-b120-d9a3cc60ceaf', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '32', 'name' => 'Preauthorization procedure', 'active' => 1, 'guid' => 'b220cd4b-f0d4-40be-b3ab-984d2f95f82b', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '50', 'name' => 'Bilateral procedures', 'active' => 1, 'guid' => '4b46ab73-8244-449c-b090-decab022f221', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '51', 'name' => 'Multiple procedures', 'active' => 1, 'guid' => '72cdbab2-1a1d-4d41-87ae-57a995393c83', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '52', 'name' => 'Reduced services', 'active' => 1, 'guid' => '383a4ce7-9b59-485a-8a68-71e655cef73d', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '53', 'name' => 'Discontinued procedure', 'active' => 1, 'guid' => '87e7543c-8569-4c27-b755-2ffae1759715', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '54', 'name' => 'Surgical care only', 'active' => 1, 'guid' => '2dc17176-22ba-43f7-ac65-730156f362d7', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '55', 'name' => 'Postoperative management only', 'active' => 1, 'guid' => '8897fe57-6be2-4ac0-b850-f4ae7fba5ddd', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '56', 'name' => 'Preoperative management only', 'active' => 1, 'guid' => '1d7dda18-1199-4284-bd02-fe0cdf789795', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '57', 'name' => 'Decision for surgery', 'active' => 1, 'guid' => '2730f7b4-b4c0-42cf-9afb-a7b4484428ab', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '58', 'name' => 'Staged or related procedure or service', 'active' => 1, 'guid' => '9b4a5fbd-e752-4e93-95f9-b2dc21a1c45f', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '59', 'name' => 'Distinct procedural service', 'active' => 1, 'guid' => '0a27711e-7806-4b98-b9e8-a60fd6a54309', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '62', 'name' => 'Two surgeons', 'active' => 1, 'guid' => 'cd205567-2d1c-4040-b8ee-1c40d1ba1ac2', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '63', 'name' => 'Procedure performed on infants less than 4kg.', 'active' => 1, 'guid' => '18bdcdb2-3eba-4ead-bbf2-80e9d39ce98f', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '79', 'name' => 'Unrelated procedure or service', 'active' => 1, 'guid' => 'a2695f53-ad13-4036-85ea-6f0bc5b0c1bc', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '81', 'name' => 'Minimum assist surgeon', 'active' => 1, 'guid' => '6b98b909-9f15-4f54-b1fe-596f2c0262c6', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '82', 'name' => 'Assistant surgeon when qualified resident surgeon is not available', 'active' => 1, 'guid' => '0df38af4-eceb-4792-b6a4-61d9692038d1', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '90', 'name' => 'Reference/outside laboratory', 'active' => 1, 'guid' => 'f4e47e05-e0b4-4003-a5fe-87ddab39fae9', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '91', 'name' => 'Repeat clinical diagnostic test', 'active' => 1, 'guid' => 'd79ae5c8-c618-455d-9a7f-0075608d2658', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
					array('key' => '99', 'name' => 'Multiple modifiers', 'active' => 1, 'guid' => '2056a929-5670-48e4-8c48-1ffeb7dfc13f', 'ormClass' => '', 'ormEditMethod' => 'ormEditMethod'),
				)),
			);

			$level = array();
			$level['guid'] = 'ab377de7-8ea7-4912-a27b-2f9749499204';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['ormClass'] = 'Visit';
			$level['ormEditMethod'] = 'ormEditMethod';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateFacilityCodesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Facility Codes';
			$key = 'FACIL_CODES';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => '11', 'name' => 'Office', 'active' => 1, 'guid' => '97e73bc4-eb06-4f66-8fb9-67e6974cc2f6'),
				array('key' => '12', 'name' => 'Home', 'active' => 1, 'guid' => '6f87512e-05bc-4144-bf2a-6be063d3e6b8'),
				array('key' => '21', 'name' => 'Inpatient Hospital', 'active' => 1, 'guid' => '1b46d32b-6383-4359-bc37-2bac4d258529'),
				array('key' => '22', 'name' => 'Outpatient Hospital', 'active' => 1, 'guid' => '2506384a-50ae-410b-9cc5-876f26226b7c'),
				array('key' => '23', 'name' => 'Emergency Room - Hospital', 'active' => 1, 'guid' => '64b119ab-495f-4629-87f2-33aa7e000578'),
				array('key' => '24', 'name' => 'Ambulatory Surgical Center', 'active' => 1, 'guid' => '32363b83-4119-4db5-82e4-af3468983419'),
				array('key' => '25', 'name' => 'Birthing Center', 'active' => 1, 'guid' => 'c1c92a9c-14a1-4b90-ad14-b710b13ace85'),
				array('key' => '26', 'name' => 'Military Treatment Facility', 'active' => 1, 'guid' => 'bf869590-b482-47d7-8669-33c7bd3caea6'),
				array('key' => '31', 'name' => 'Skilled Nursing Facility', 'active' => 1, 'guid' => '477762b4-148b-44c7-a869-6ace256a248c'),
				array('key' => '32', 'name' => 'Nursing Facility', 'active' => 1, 'guid' => 'c5c3df6c-4c72-4638-9702-e686322c0a18'),
				array('key' => '33', 'name' => 'Custodial Care Facility', 'active' => 1, 'guid' => '074acacf-3340-483e-ae57-ad65776b5a5e'),
				array('key' => '34', 'name' => 'Hospice', 'active' => 1, 'guid' => '815d94b4-0407-4c78-9168-8f9b266a75cf'),
				array('key' => '41', 'name' => 'Ambulance - Land', 'active' => 1, 'guid' => '59e87b4b-4975-4110-9436-a751cfa31f0b'),
				array('key' => '42', 'name' => 'Ambulance - Air or Water', 'active' => 1, 'guid' => '78c196b9-c2c1-4281-84f7-1e0da0dc6a67'),
				array('key' => '50', 'name' => 'Federally Qualified Health Center', 'active' => 1, 'guid' => '8305dfc2-941d-45dd-a929-a1d84f25bcf6'),
				array('key' => '51', 'name' => 'Inpatient Psychiatric Facility', 'active' => 1, 'guid' => '3fea6917-c5ed-41ed-b2f9-c743bc504cc4'),
				array('key' => '52', 'name' => 'Psychiatric Facility Partial Hospitalization', 'active' => 1, 'guid' => '3f18955f-a3bd-4f22-a86c-6d5539070258'),
				array('key' => '53', 'name' => 'Community Mental Health Center', 'active' => 1, 'guid' => '90d55f5e-d81d-442d-a925-d58467bdb6c7'),
				array('key' => '54', 'name' => 'Intermediate Care Facility/Mentally Retarded', 'active' => 1, 'guid' => 'eb3b94a8-baef-48de-80bb-20ba9465a4af'),
				array('key' => '55', 'name' => 'Residential Substance Abuse Treatment Facility', 'active' => 1, 'guid' => 'c9f8d5de-8e08-46a1-8bae-231783a295a2'),
				array('key' => '56', 'name' => 'Psychiatric Residential Treatment Center', 'active' => 1, 'guid' => '8ba47649-3cbf-49b5-ac7e-9663e53a9795'),
				array('key' => '60', 'name' => 'Mass Immunization Center', 'active' => 1, 'guid' => '905992e4-e172-4bc3-8bb3-240b81a44b78'),
				array('key' => '61', 'name' => 'Comprehensive Inpatient Rehabilitation Facility', 'active' => 1, 'guid' => '4706d13f-bca8-476f-b217-cecf0ee56318'),
				array('key' => '62', 'name' => 'Comprehensive Outpatient Rehabilitation Facility', 'active' => 1, 'guid' => 'd0014445-262a-4dc7-b41c-c88ba5dad2ab'),
				array('key' => '65', 'name' => 'End Stage Renal Disease Treatment Facility', 'active' => 1, 'guid' => '121fdadb-2caf-468c-9f79-5aaa947d38ac'),
				array('key' => '71', 'name' => 'State or Local Public Health Clinic', 'active' => 1, 'guid' => 'a214f437-c072-4197-978b-d919471d427e'),
				array('key' => '72', 'name' => 'Rural Health Clinic', 'active' => 1, 'guid' => '0a981977-c060-4258-a679-f64a2a3c03b7'),
				array('key' => '81', 'name' => 'Independent Laboratory', 'active' => 1, 'guid' => '34fb5d8f-c068-4a81-8740-504b4b114f1a'),
				array('key' => '99', 'name' => 'Other Unlisted Facility', 'active' => 1, 'guid' => '9865976d-a8b0-4811-9161-8d583df8e0ad'),
			);


			$level = array();
			$level['guid'] = '22fb4e1e-a37a-4e7a-9dae-8e220ba939e8';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateIdentifierTypesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Identifier Type';
			$key = 'IDENTIFIER';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => '4f69f0ee-8a9f-4789-a9d5-6fcd4406f8c0'),
				array('key' => 'SSN', 'name' => 'SSN', 'active' => 1, 'guid' => '59086c0f-6666-4ac4-8008-e199e9da1310'),
				array('key' => 'EIN', 'name' => 'EIN', 'active' => 1, 'guid' => 'd30e11a0-8600-414d-847f-ab69061e2c62'),
				array('key' => 'NPI', 'name' => 'NPI', 'active' => 1, 'guid' => '2e2f5558-83bb-421d-be62-d1e5aaf1bb95'),
				array('key' => 'UPIN', 'name' => 'UPIN', 'active' => 1, 'guid' => '116ec1bf-01bc-4f60-b11a-b17432a802c1'),
				array('key' => 'OTHER_MRN', 'name' => 'Other MRN', 'active' => 1, 'guid' => '3ed75316-99b8-4dbf-916d-95184a890260'),
				array('key' => 'DL', 'name' => 'DL', 'active' => 1, 'guid' => '1063b140-3759-4cb5-8e50-a0bc19d59ef7'),
			);


			$level = array();
			$level['guid'] = '8c200e66-f97e-40e9-9e39-f102ad2c6c31';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateDiscountTypesEnum($force = false) {
		$ret = false;
		do {
			$name = DiscountTable::DISCOUNT_TYPE_ENUM_NAME;
			$key = DiscountTable::DISCOUNT_TYPE_ENUM_KEY;
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => DiscountTable::DISCOUNT_TYPE_FLAT_VISIT, 'name' => 'Flat Rate Fee for Visit', 'active' => 1, 'guid' => '57233584-7719-44db-9ff8-8f8ba97e9cd2'),
				array('key' => DiscountTable::DISCOUNT_TYPE_FLAT_CODE, 'name' => 'Flat Rate Fee for Code', 'active' => 1, 'guid' => '0f4e6978-ae24-4859-bc90-4b14c08eda0f'),
				array('key' => DiscountTable::DISCOUNT_TYPE_PERC_VISIT, 'name' => 'Percentage Discount on Visit', 'active' => 1, 'guid' => '44f4daca-8caa-42d5-a15c-487de47c0a0b'),
				array('key' => DiscountTable::DISCOUNT_TYPE_PERC_CODE, 'name' => 'Percentage Discount on Code', 'active' => 1, 'guid' => 'c4c5c67a-cc3d-4873-b697-0e3905e9f2a0'),
			);


			$level = array();
			$level['guid'] = '31c0815d-0e7f-4f0a-9100-c910505259a6';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateImagingPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = OrderImaging::IMAGING_ENUM_NAME;
			$key = OrderImaging::IMAGING_ENUM_KEY;
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$types = array(
				/*array('key'=>'','name'=>'','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'','data'=>array(
						array('key'=>'','name'=>'','active'=>1,'guid'=>'','data'=>array(
							array('key'=>'COMMENTS','name'=>'Comments','active'=>1,'guid'=>'','data'=>array(
							))
						)),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'','data'=>array(
						array('key'=>'','name'=>'','active'=>1,'guid'=>''),
					)),
				)),*/
				array('key'=>'ANI','name'=>'ANGIO/NEURO/INTERVENTIONAL','active'=>1,'guid'=>'6978545c-9e72-44c3-bc30-dce298fd2684','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'0c523ca1-5ff7-447b-9748-2abf998dbf2f','data'=>array(
						array('key'=>'1','name'=>'ANGIO ADRENAL BILAT SELECT S&I','active'=>1,'guid'=>'a374577b-6376-422e-8a31-2c418b31b4ff'),
						array('key'=>'2','name'=>'ANGIO ADRENAL UNILAT SELECT S&I','active'=>1,'guid'=>'5215712d-80ae-4628-a55d-0e34487bc567'),
						array('key'=>'3','name'=>'ANGIO BRACHIAL RETROGRADE S&I','active'=>1,'guid'=>'7c19f49d-43f0-448c-88bf-e1eea7c2df2c'),
						array('key'=>'4','name'=>'ANGIO CAROTID CEREBRAL BILAT S&I','active'=>1,'guid'=>'7ae00c3c-2ec5-410b-b147-fc235b6b3d5c'),
						array('key'=>'5','name'=>'ANGIO CAROTID CEREBRAL SELECT EXT UNILAT S&I','active'=>1,'guid'=>'22f7b746-b64a-40ff-bd07-1537b270266d'),
						array('key'=>'6','name'=>'ANGIO CAROTID CEREBRAL UNILAT S&I','active'=>1,'guid'=>'3c2007f4-a945-4122-98d5-ae07ebaf51d4'),
						array('key'=>'7','name'=>'ANGIO CAROTID CERVICAL BILAT S&I','active'=>1,'guid'=>'ff8351a3-e3e8-446a-883c-b4e2fe8db7f4'),
						array('key'=>'8','name'=>'ANGIO CAROTID CERVICAL UNILAT S&I','active'=>1,'guid'=>'99e184ea-7cf5-4e64-85c4-09fc7ff064e8'),
						array('key'=>'9','name'=>'ANGIO CAROTID EXT BILAT SELECT S&I','active'=>1,'guid'=>'b239a765-822e-442b-b590-a8b9f9b2376b'),
						array('key'=>'10','name'=>'ANGIO CERVICOCEREBRAL CATH S&I','active'=>1,'guid'=>'2b13952c-2549-4f17-8c18-6d9ce01e3291'),
						array('key'=>'11','name'=>'ANGIO CORPORA CAVERNOSOGRAM S&I','active'=>1,'guid'=>'cc941df8-128e-4bb7-8775-d64ad054c6eb'),
						array('key'=>'12','name'=>'ANGIO EXTREMITY BILAT S&I','active'=>1,'guid'=>'b4cecf2b-b3b9-4f5e-a283-6de971c9188b'),
						array('key'=>'13','name'=>'ANGIO EXTREMITY UNILAT S&I','active'=>1,'guid'=>'3eeedf26-05db-4555-9f97-1d498b1be7ac'),
						array('key'=>'14','name'=>'ANGIO MAMMARY INTERNAL S&I','active'=>1,'guid'=>'a210c192-25a4-4f72-8c13-f28fa9e3a9b5'),
						array('key'=>'15','name'=>'ANGIO PELVIC SELECT OR SUPRASELECT S&I','active'=>1,'guid'=>'66e115a1-2d6e-451b-a340-ae2557e6efba'),
						array('key'=>'16','name'=>'ANGIO PULMONARY BILAT SELECT S&I','active'=>1,'guid'=>'ac1f35e4-0e1b-4407-bdd3-86e4eec5f1bf'),
						array('key'=>'17','name'=>'ANGIO PULMONARY NONSELECT CATH S&I','active'=>1,'guid'=>'6b7395c9-5cdb-4561-aae7-06bb48f5d7f4'),
						array('key'=>'18','name'=>'ANGIO PULMONARY UNILAT SELECT S&I','active'=>1,'guid'=>'21681122-0996-44b2-8076-775a4912def4'),
						array('key'=>'19','name'=>'ANGIO RENAL BILAT SELECT S&I','active'=>1,'guid'=>'4a502e94-17cf-439f-af68-701df599d893'),
						array('key'=>'20','name'=>'ANGIO RENAL UNILAT SELECT S&I','active'=>1,'guid'=>'97d558d5-65e5-49b2-9c6b-0dff941ac791'),
						array('key'=>'21','name'=>'ANGIO SPINAL SELECT S&I','active'=>1,'guid'=>'da6facb1-9363-4ee7-b859-fb42b4fc1dab'),
						array('key'=>'22','name'=>'ANGIO THRU EXISTING CATH FOR FOLLOWUP','active'=>1,'guid'=>'e718d4c5-370a-42cb-a0bf-e6ddbafa54f4'),
						array('key'=>'23','name'=>'ANGIO VERTEBRAL S&I','active'=>1,'guid'=>'fdb59632-d590-4863-a4e6-d80efb299792'),
						array('key'=>'24','name'=>'ANGIO VISCERAL SELECT OR SUPRASELECT S&I','active'=>1,'guid'=>'b68992ff-a048-4853-a6bd-3c60c14106bf'),
						array('key'=>'25','name'=>'AORTO ABD TRANS L W/SERIAL FILMS S&I','active'=>1,'guid'=>'c6f4183b-a4f1-4e4b-abbb-8320285312a3'),
						array('key'=>'26','name'=>'AORTOGRAM THORACIC W/O SERIAL FILMS S&I','active'=>1,'guid'=>'707e17e2-5ec6-490e-a1b9-0e265d7f39e7'),
						array('key'=>'27','name'=>'AORTOGRAM THORACIC W/SERIAL FILMS S&I','active'=>1,'guid'=>'f5a2f6fb-c300-41e2-b04d-877de57bb096'),
						array('key'=>'28','name'=>'CHANGE OF PERC DRAIN CATH S&I','active'=>1,'guid'=>'7a2d41f7-f920-4333-ba66-7c8ae4829af4'),
						array('key'=>'29','name'=>'PERCUT CATH RENAL PELVIS FOR DRAIN S&I','active'=>1,'guid'=>'3eb6721d-b6fd-4d94-9451-48e4ac7bfd11'),
						array('key'=>'30','name'=>'PERCUT CATH URETER FOR DRAIN S&I','active'=>1,'guid'=>'10e80280-802b-489d-aa06-575243aca5c7'),
						array('key'=>'31','name'=>'PERCUT INT & EXT CATH DRAIN OR STENT S&I','active'=>1,'guid'=>'0af31a51-8edc-4744-a2d9-5e71bb174d1a'),
						array('key'=>'32','name'=>'PERCUT REMOVAL OF GALL STONE CP','active'=>1,'guid'=>'5a4c850d-c63e-499e-8c15-80d89f66376d'),
						array('key'=>'33','name'=>'PERCUT TRANSHEP BIL DRAIN S&I','active'=>1,'guid'=>'f0237367-91f8-494c-bc99-4bc16eb98e40'),
						array('key'=>'34','name'=>'PERCUT TRANSHEP PORTOGRAM W HEMODYNAM S&I','active'=>1,'guid'=>'7f48ce4d-c213-4682-bf98-faa4ad224935'),
						array('key'=>'35','name'=>'PERCUT TRANSHEP PORTOGRAM W/O HEMODYNAM S&I','active'=>1,'guid'=>'97b3910f-88dd-4ee0-bcb0-c955c6081bce'),
						array('key'=>'36','name'=>'PLACEMENT OF LONG GI TUBE','active'=>1,'guid'=>'fd59f457-0fd1-44ed-b1eb-d80fa5c3710f'),
						array('key'=>'37','name'=>'VENOUS SAMPLE BY CATH W/O ANGIO CP','active'=>1,'guid'=>'804d8299-703b-4693-841d-7d304f31e2ba'),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'bbe3caa2-6a03-4ef7-a9bc-43103e6550cf','data'=>array(
					)),
				)),
				array('key'=>'CTSCAN','name'=>'CT SCAN','active'=>1,'guid'=>'ff176d11-9867-4ad5-bb73-6bba3d82ffb6','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'108af5f4-9910-4911-aaae-a28121ca0d13','data'=>array(
						array('key'=>'1','name'=>'BIOPSY OF LIVER, NEEDLE PERCUTANEOUS','active'=>1,'guid'=>'552809a3-3b14-4445-a534-95900cf89da1'),
						array('key'=>'2','name'=>'BIOPSY, LUNG OR MEDIASTINUM PERCUTANEOUS NEEDLE','active'=>1,'guid'=>'0b50cf3f-8783-46a4-9117-a719c05aab29'),
						array('key'=>'3','name'=>'CT ABDOMEN W&W/O CONT','active'=>1,'guid'=>'97d01d9e-600e-4152-8833-dd5d169875e7','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'c0df394f-5cc4-48ac-ad05-e056a1abe22e'),
						)),
						array('key'=>'4','name'=>'CT ABDOMEN W/CONT','active'=>1,'guid'=>'0eafa118-30ad-470c-9872-22969f8400e4','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'f3e63bb1-739a-4e51-ba38-1491ff3cb563'),
						)),
						array('key'=>'5','name'=>'CT ABDOMEN W/O CONT','active'=>1,'guid'=>'1c5b6bf6-343a-4b3d-8c62-dc8ae88725b9'),
						array('key'=>'6','name'=>'CT CERVICAL SPINE W/CONT','active'=>1,'guid'=>'95e81da0-c9c1-4569-89c5-bb2cb7ccb1c5','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'e8f27821-4779-4a17-86dd-ffb1dffee746'),
						)),
						array('key'=>'7','name'=>'CT CERVICAL SPINE W/O CONT','active'=>1,'guid'=>'9aa1b1dd-959e-41e2-bb54-2bff4e2b6d8e'),
						array('key'=>'8','name'=>'CT FOR PLACEMENT OF RX FIELDS','active'=>1,'guid'=>'39b48741-b195-46ac-8d6e-e0a75132e3b4'),
						array('key'=>'9','name'=>'CT GUIDANCE FOR NEEDLE BIOPSY S&I','active'=>1,'guid'=>'a4f8ede2-86d9-4b4e-aebc-fcfc67541bfa'),
						array('key'=>'10','name'=>'CT GUIDED-NEEDLE PLACEMENT','active'=>1,'guid'=>'44372a70-27fd-4637-8263-1fd75daf95b8','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient over 60 years old must have a Creatinine, PTT/PT/INR & CBC with differential within 30 days prior to exam.  Requires a prep, please refer to the appropriate exam prep information sheet.','active'=>1,'guid'=>'d9cbcf54-bd98-415a-9d7d-b3c080d57cc5'),
						)),
						array('key'=>'11','name'=>'CT HEAD W&WO CONT','active'=>1,'guid'=>'f0ab4646-d3be-45c1-91d1-f01c90ae4990','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'26469703-2d30-4a5f-86b2-0dbf8f477cb5'),
						)),
						array('key'=>'12','name'=>'CT HEAD W/IV CONT','active'=>1,'guid'=>'f4c2965d-4d27-4a3e-a64a-d6b3014ae253','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'ab6c9aa8-8f3b-49af-84cc-3a5cd100df1f'),
						)),
						array('key'=>'13','name'=>'CT HEAD W/O CONT','active'=>1,'guid'=>'aae5fbd5-3a6e-44b2-b19d-f6623981c73c'),
						array('key'=>'14','name'=>'CT LOWER EXTREMITY W&W/O CONT','active'=>1,'guid'=>'da433349-75ca-4d6b-8afc-52767da11c4d','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'087808fa-18da-499a-917a-9a963b947e86'),
						)),
						array('key'=>'15','name'=>'CT LOWER EXTREMITY W/CONT','active'=>1,'guid'=>'9a8e32d2-16a8-4c29-9d53-c304e825642a'),
						array('key'=>'16','name'=>'CT LOWER EXTREMITY W/O CONT','active'=>1,'guid'=>'1bfd1d00-5cde-4773-ac88-7821d0cbddf9'),
						array('key'=>'17','name'=>'CT LUMBAR SPINE W/CONT','active'=>1,'guid'=>'2285d52a-0d53-43a0-b48c-248d5da8beb4','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'a62e55e5-4dd3-46f1-9000-236936fb6bfc'),
						)),
						array('key'=>'18','name'=>'CT LUMBAR SPINE W/O CONT','active'=>1,'guid'=>'c24e2863-27df-498d-a114-1ef4f6bf7558'),
						array('key'=>'19','name'=>'CT MAXILLOFACIAL W&W/O CONT','active'=>1,'guid'=>'d06ae2fb-4bb4-4093-b899-95b7ee8fa4ad','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'157fa862-b8e3-42a5-8ae8-4d8a62ad69d7'),
						)),
						array('key'=>'20','name'=>'CT MAXILLOFACIAL W/CONT','active'=>1,'guid'=>'7b3ddafb-aad1-4108-be2e-3d45ae7e4164'),
						array('key'=>'21','name'=>'CT MAXILLOFACIAL W/O CONT','active'=>1,'guid'=>'8d6c0a9a-1f9b-429a-b034-9fb84c85d575'),
						array('key'=>'22','name'=>'CT NECK SOFT TISSUE W&W/O CONT','active'=>1,'guid'=>'c6d055b0-f07a-4d1f-ae59-776c86e50891','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'11c24dd3-13ec-4cf3-94b2-71ee57839f5b'),
						)),
						array('key'=>'23','name'=>'CT NECK SOFT TISSUE W/CONT','active'=>1,'guid'=>'ee2ff01a-da7c-4b1b-9a0a-bfa8447f6869','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'ee2fdf85-8b3b-4e33-9b7f-5f622c3268da'),
						)),
						array('key'=>'24','name'=>'CT NECK SOFT TISSUE W/O CONT','active'=>1,'guid'=>'54c13cb1-2597-4cad-832e-84999f0bef85'),
						array('key'=>'25','name'=>'CT ORBIT P FOS OR TEMP BONE W/&W/O CONT','active'=>1,'guid'=>'d4a36151-1ba1-4d12-8fbc-6fa5efcafa36','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'b57cf661-0f73-4bc5-bc3f-8be62e9330e8'),
						)),
						array('key'=>'26','name'=>'CT ORBIT SELLA P FOS OR TEMP BONE W/CONT','active'=>1,'guid'=>'b63bb10b-bce8-4d27-a412-5586375c0e3a','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'3b76edbe-5b8c-42b8-8c18-52074c9808e1'),
						)),
						array('key'=>'27','name'=>'CT ORBIT SELLA P FOS OR TEMP BONE W/O CONT','active'=>1,'guid'=>'765f4e92-466e-4423-9f54-dc6a5efdfcca'),
						array('key'=>'28','name'=>'CT PELVIS W&W/O CONT','active'=>1,'guid'=>'6a5a6d86-55d1-4a53-88ed-c25cefc6c8d3','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'0cffc723-be2d-4ba6-86ae-47ba1c5c846b'),
						)),
						array('key'=>'29','name'=>'CT PELVIS W/CONT','active'=>1,'guid'=>'68b26565-11a9-4424-859c-cf68b4b2e990','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'bf7ac94c-2882-48a1-9846-1a3462277428'),
						)),
						array('key'=>'30','name'=>'CT PELVIS W/O CONT','active'=>1,'guid'=>'b93ddac1-beae-4712-a933-e3972b2f0da6','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'000afa31-cecb-420a-abb7-43cb8ef06977'),
						)),
						array('key'=>'31','name'=>'CT SINUS W/O CONT','active'=>1,'guid'=>'0f438d1b-3fdf-41d4-82ef-d97f1580f27e'),
						array('key'=>'32','name'=>'CT THORACIC SPINE W/CONT','active'=>1,'guid'=>'2dcdbbea-2863-4100-b93f-23dc5573a138','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'821d34bb-fb14-4159-8b16-50757bfa2478'),
						)),
						array('key'=>'33','name'=>'CT THORACIC SPINE W/O CONT','active'=>1,'guid'=>'81fd2229-9bbc-49f2-a1c7-45efb8c7e667','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'3cc031ef-8dd4-4168-b16a-f67ac3eeef7e'),
						)),
						array('key'=>'34','name'=>'CT THORAX W&W/O CONT','active'=>1,'guid'=>'94a07979-d599-4552-b219-0498d7f2492e','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'013029a0-edfe-4383-b8fd-7d499d6e9e3c'),
						)),
						array('key'=>'35','name'=>'CT THORAX W/CONT','active'=>1,'guid'=>'b9a74c69-2efe-4cb6-aedf-1982ebadda92','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'e50a243c-5848-4d96-8c03-5e68c870b57b'),
						)),
						array('key'=>'36','name'=>'CT THORAX W/O CONT','active'=>1,'guid'=>'323e2802-a22a-47ae-9293-34294f7c9681'),
						array('key'=>'37','name'=>'CT UPPER EXTREMITY W&W/O CONT','active'=>1,'guid'=>'11a69692-7463-43cd-914f-46a3efdc12de','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient must have Creatinine w/i 30 days prior to CT Scan.  Diabetic patients taking metformin (Glucophage) must not take morning dose on day of exam and stay off Glucophage 48 hrs after exam.  Patient should have light breakfast (cereal, coffee) day of exam.','active'=>1,'guid'=>'bb1353de-eb2b-4822-964e-10fef152e595'),
						)),
						array('key'=>'38','name'=>'CT UPPER EXTREMITY W/CONT','active'=>1,'guid'=>'191c1ffe-734e-458d-bd05-452171315198'),
						array('key'=>'39','name'=>'CT UPPER EXTREMITY W/O CONT','active'=>1,'guid'=>'1cfe62bc-503a-4851-a306-77b0b07f9e0f'),
						array('key'=>'40','name'=>'FINE NEEDLE ASPIRATION W/IMAGING GUIDANCE','active'=>1,'guid'=>'b022a13d-d433-4cfb-88cb-a4e8419a3000'),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'5caaa5ce-2938-44b2-a1a4-f6e91d091e56','data'=>array(
						array('key'=>'RIGHT','name'=>'RIGHT','active'=>1,'guid'=>'3a43bd01-4fa5-4872-a4ef-d52b66b5fc72'),
					)),
				)),
				array('key'=>'MAMMOGRAPH','name'=>'MAMMOGRAPHY','active'=>1,'guid'=>'eb01e704-fecc-4cb7-872a-55e76c4b279d','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'ec8bfbda-474b-4fe4-83a1-e690fa1d9b9b','data'=>array(
						array('key'=>'1','name'=>'MAMMARY NODULE OR CALCIFICATION LOCALIZATION','active'=>1,'guid'=>'579e1984-9c72-4416-aece-6deb77c85b49'),
						array('key'=>'2','name'=>'MAMMO GUIDED-NEEDLE PLACEMENT, BREAST','active'=>1,'guid'=>'540d5616-a526-4c67-859f-abc4a574bf01'),
						array('key'=>'3','name'=>'MAMMOGRAM BILAT','active'=>1,'guid'=>'d7a89439-48f6-49a8-aea2-4df3d0ed6417'),
						array('key'=>'4','name'=>'MAMMOGRAM SCREENING BILAT','active'=>1,'guid'=>'297cda20-28fd-4708-a88d-0a94c1bf6f0b','data'=>array(
							array('key'=>'COMMENTS','name'=>'Patient is required to request prior mammogram films from outside facility to be sent to Medsphere Hospital.  Patient should not wear any powders, lotions or deodorants.','active'=>1,'guid'=>'0ef0741a-5a35-4be5-a3d1-b50f69f71afb'),
						)),
						array('key'=>'5','name'=>'MAMMOGRAM UNILAT','active'=>1,'guid'=>'4a1038a9-ba04-4304-b6ce-001216ebc25c'),
						array('key'=>'6','name'=>'PREOP PLACEMENT NEEDLE LOC WIRE, BREAST','active'=>1,'guid'=>'d0e71ed6-d1d0-4c17-b98a-470365f1cc43'),
						array('key'=>'7','name'=>'PREOP PLACEMENT NEEDLE WIRE, BREAST EACH ADDL LESION','active'=>1,'guid'=>'d6b2307e-64a3-4dc6-b8dd-3f9edb4689b2'),
						array('key'=>'8','name'=>'RADIOLOGICAL EXAM, SURGICAL SPECIMEN','active'=>1,'guid'=>'e45dda5f-9e0a-4ee7-aa36-86a07c13c70b'),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'5a327162-2e35-4e8e-8d79-27395b50bc7f','data'=>array(
						array('key'=>'LEFT','name'=>'LEFT','active'=>1,'guid'=>'d63b976a-45a6-4434-b298-586646863ee5'),
					)),
				)),
				array('key'=>'MRI','name'=>'MAGNETIC RESONANCE IMAGING','active'=>1,'guid'=>'eadccdc4-c3a5-4bf2-9bfd-7c437da3595e','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'f4447ebc-1baf-4f9a-8284-66d99f1302cf','data'=>array(
						array('key'=>'1','name'=>'MRA ABDOMEN W OR W/O CONTRAST','active'=>1,'guid'=>'e0684898-ad54-4255-9c85-cda000b25323','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'f59a1fbc-9185-4bef-be1b-a94201292abc'),
						)),
						array('key'=>'2','name'=>'MRA ARM, UPPER EXT W OR W/O CO','active'=>1,'guid'=>'fc3f4550-277f-4d85-8095-d0e2bd3d5274'),
						array('key'=>'3','name'=>'MRA CHEST W OR W/O CONTRAST','active'=>1,'guid'=>'41d99d84-9fe9-4dfc-8399-4ec9250919a6'),
						array('key'=>'4','name'=>'MRA HEAD W/O & W/CONTRAST','active'=>1,'guid'=>'65663734-d429-48c4-a2bb-07d4d8fee9eb'),
						array('key'=>'5','name'=>'MRA HEAD WITH CONTRAST','active'=>1,'guid'=>'48ca089a-edfc-4432-aeb7-ebfad111f27c'),
						array('key'=>'6','name'=>'MRA HEAD WITHOUT CONTRAST','active'=>1,'guid'=>'e0d5ad7c-9591-415b-ba2b-62bdb8afd7bd'),
						array('key'=>'7','name'=>'MRA LEG, LOWER EXT W OR W/O CO','active'=>1,'guid'=>'d07de242-c44d-43b3-9c9b-3061b6755343'),
						array('key'=>'8','name'=>'MRA NECK W/O & W/CONTRAST','active'=>1,'guid'=>'7a79272b-cf7f-4e28-9d1e-216a794a7f28'),
						array('key'=>'9','name'=>'MRA NECK WITH CONTRAST','active'=>1,'guid'=>'c9b239c4-af40-476f-bfd0-a0a2b8d9f38e'),
						array('key'=>'10','name'=>'MRA NECK WITHOUT CONTRAST','active'=>1,'guid'=>'7806309a-0deb-4936-a483-126d3430690f'),
						array('key'=>'11','name'=>'MRA PELVIS W OR W/O CONTRAST','active'=>1,'guid'=>'6726b38c-2258-4f42-84c8-3a8a4936650c'),
						array('key'=>'12','name'=>'MRA SPINE W OR W/O CONTRAST','active'=>1,'guid'=>'f920d7a5-ca9f-4d54-b839-74cbfe3ad02d'),
						array('key'=>'13','name'=>'MRI ABDOMEN W/CONTRAST','active'=>1,'guid'=>'4b1e6855-d971-482e-9712-d81c2f43bc62','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'56be0cea-8b81-4e22-b0b8-10d962a38af3'),
						)),
						array('key'=>'14','name'=>'MRI ABDOMEN W/O & W/CONTRAST','active'=>1,'guid'=>'ce170f47-43d5-4609-ac25-c3d21197477e','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'55fa1146-de94-4ae6-aa7a-d0c5983365dc'),
						)),
						array('key'=>'15','name'=>'MRI ABDOMEN W/O CONTRAST','active'=>1,'guid'=>'0ed5b24a-2138-4cb3-9816-cfe09a2d1404','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'949dd9e9-d5b0-42c7-8b3c-82ba17e249f4'),
						)),
						array('key'=>'16','name'=>'MRI BRAIN (W BRAIN STEM) W/CONT','active'=>1,'guid'=>'edfd5a78-09bc-4498-9220-040bf965e904','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'1ef81ce2-32e5-4cec-b202-378e32edaeff'),
						)),
						array('key'=>'17','name'=>'MRI BRAIN W/O & W/CONTRAST','active'=>1,'guid'=>'41cf4268-5a9a-448e-8aa4-b52c64bc5496','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'3323fc3a-04ae-4261-9ab8-0592fbf6e217'),
						)),
						array('key'=>'18','name'=>'MRI BRAIN W/O CONTRAST','active'=>1,'guid'=>'b3c12614-e159-4295-8652-57c7d8a70cdd','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'50fbf4a8-97f1-4dec-8432-4bf07b85da5d'),
						)),
						array('key'=>'19','name'=>'MRI BRAIN W/O DYE','active'=>1,'guid'=>'7518eadf-056e-4280-b72d-2827db5f7193'),
						array('key'=>'20','name'=>'MRI CERVICAL SPINE W/CONTRAST','active'=>1,'guid'=>'438c0288-2501-428d-a6ac-08e393e715e9','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'341ef53e-0d41-4778-b0cc-4caa0f7c8092'),
						)),
						array('key'=>'21','name'=>'MRI CERVICAL SPINE W/O & W/CONT','active'=>1,'guid'=>'083de439-2c20-4fa7-912e-70b3056a1b2b','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'4aee01c2-be8b-4593-998e-ad4e4373dfca'),
						)),
						array('key'=>'22','name'=>'MRI CERVICAL SPINE W/O CONTRAST','active'=>1,'guid'=>'defe3d42-2efd-42d4-b28b-e657fa1e8e27','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'3cdb185d-8421-4fed-b8d0-5f41cd64cfdc'),
						)),
						array('key'=>'23','name'=>'MRI CHEST W/CONTRAST','active'=>1,'guid'=>'29cba965-5018-4649-9eeb-86884c250493','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'85df2598-4f6e-4b5b-8cf1-eb7d029dcfc6'),
						)),
						array('key'=>'24','name'=>'MRI CHEST W/O & W/CONTRAST','active'=>1,'guid'=>'2dc2b938-c968-4af9-acae-00dc21b4bb74','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'38fd8e11-f4ca-449b-80d6-9d395b460c84'),
						)),
						array('key'=>'25','name'=>'MRI CHEST W/O CONTRAST','active'=>1,'guid'=>'781f39d4-e6da-4742-bf40-714faa93d7ce','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'1563eb01-a87e-40ed-8bf7-cb38aaea7946'),
						)),
						array('key'=>'26','name'=>'MRI FACE W/O CONTRAST','active'=>1,'guid'=>'5fee68ef-3e09-4ae3-a37e-36ee975ba1ee','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'43495c73-2804-4502-bfcb-d4fe0ddda217'),
						)),
						array('key'=>'27','name'=>'MRI LOWER EXT, ANY JOINT W/O CONTRAST','active'=>1,'guid'=>'99fe669c-b0bb-4dfe-b0a4-57e687d2feff','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'b39115c5-9bcc-4119-9055-ff5468b5a245'),
						)),
						array('key'=>'28','name'=>'MRI LOWER EXT, NOT JOINT W/O & W CONTRAST','active'=>1,'guid'=>'fddff970-e77c-4172-b571-9ef00d74ae7e','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'309f602e-31c9-4d43-b1ce-b1e578adfcea'),
						)),
						array('key'=>'29','name'=>'MRI LUMBAR SPINE W/CONTRAST','active'=>1,'guid'=>'9e70c854-e1d1-4c50-9760-f084cd2eaf0d','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'579782eb-4a27-4496-835f-11dfe934b033'),
						)),
						array('key'=>'30','name'=>'MRI LUMBAR SPINE W/O & W/CONTRAST','active'=>1,'guid'=>'5a12c88c-cd04-4aab-ac75-ace626d91c2b','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'b354a0c2-f28f-43ac-85f7-f6d98b01d3da'),
						)),
						array('key'=>'31','name'=>'MRI LUMBAR SPINE W/O CONTRAST','active'=>1,'guid'=>'01346055-2a82-4caa-8711-10e7802c63da','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'58e62493-a37c-44e3-9c67-9199d0b7415e'),
						)),
						array('key'=>'32','name'=>'MRI ORBIT, FACE & NECK W/CONTRAST','active'=>1,'guid'=>'39451e82-f646-4efb-939e-2488b9fe87dd','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'1d5ba873-345c-464a-8c1a-2a038ae1eabc'),
						)),
						array('key'=>'33','name'=>'MRI ORBIT, FACE & NECK W/O & W/CONTRAST','active'=>1,'guid'=>'b4aea69f-b2af-49cf-8876-5695e236aa64','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'1d90509e-9865-46e5-aa2c-08d287493eb1'),
						)),
						array('key'=>'34','name'=>'MRI PELVIS W/CONTRAST','active'=>1,'guid'=>'f83d35af-bf4c-4156-ad76-0f87d0933725','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'e7a6665c-c717-42cd-821a-6069c2ae7596'),
						)),
						array('key'=>'35','name'=>'MRI PELVIS W/O & W/CONTRAST','active'=>1,'guid'=>'6f99f9a1-bea0-426f-aba6-7c6bb7a4324f','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'13089a45-8d12-470c-9728-8c77b453aee3'),
						)),
						array('key'=>'36','name'=>'MRI PELVIS W/O CONTRAST','active'=>1,'guid'=>'95a73fd3-c316-443f-a865-bce465d3f05f','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'2df66bef-9aff-41f1-9806-a2920f421af6'),
						)),
						array('key'=>'37','name'=>'MRI TEMPOROMANDIBULAR JNT','active'=>1,'guid'=>'e3e1cb5e-3406-45b8-9857-258d9f2a996c','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'aa5a1f9b-4acd-4365-ae40-79c13a3cf04c'),
						)),
						array('key'=>'38','name'=>'MRI THORACIC SPINE W/CONTRAST','active'=>1,'guid'=>'efe15ba8-8ce8-4518-a9d3-626771706cac','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'ae43d42f-de74-49d1-931d-c557422b0d39'),
						)),
						array('key'=>'39','name'=>'MRI THORACIC SPINE W/O & W/CONTRAST','active'=>1,'guid'=>'fb21fb0a-810e-46da-94f4-7e29d3b7dc36','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'69bfb60d-5471-44ef-bc57-12aaf6553200'),
						)),
						array('key'=>'40','name'=>'MRI THORACIC SPINE W/O CONTRAST','active'=>1,'guid'=>'c1bcf828-60b7-4e0c-a6a2-3d2eaec72d92','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'58605b4c-cd0b-40c9-a84b-1362700c7de3'),
						)),
						array('key'=>'41','name'=>'MRI UPPER EXT, ANY JOINT W/O CONTRAST','active'=>1,'guid'=>'38cd7d8b-2b06-4f10-926d-45e66aa3c293','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'b9b96474-122d-47ce-af80-bb3e7aa78239'),
						)),
						array('key'=>'42','name'=>'MRI UPPER EXT, NOT JOINT W/O & W/CONTRAST','active'=>1,'guid'=>'8ad0218a-7176-4d45-b163-03797f0f7465','data'=>array(
							array('key'=>'COMMENTS','name'=>'MRI SCREENING CHECKLIST MUST BE COMPLETED BY ORDERING PHYSICIAN PRIOR TO SCHEDULING MRI','active'=>1,'guid'=>'d36b8074-7dcc-446a-b0a4-1259f3f083e7'),
						)),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'5684b760-f57f-40e9-a0f8-5461dc2b8356','data'=>array(
					)),
				)),
				array('key'=>'NUCLEARMED','name'=>'NUCLEAR MEDICINE','active'=>1,'guid'=>'a1cf26d5-6515-43e6-81ab-2d1febbd37e8','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'9457cfcb-1524-49d6-aaad-167fb12e5c69','data'=>array(
						array('key'=>'1','name'=>'NM STRESS THALLIUM HEART STUDY','active'=>1,'guid'=>'1fef5102-d089-459c-a61c-7f6525d4debb','data'=>array(
							array('key'=>'COMMENTS','name'=>'Dietary & Medication restrictions.  Please refer to the appropriate exam prep information.  Patient needs to obtain cardiolite Prep sheet 24 hours prior to exam date.','active'=>1,'guid'=>'e0382e27-fcce-4754-8224-b1af33430e06'),
						)),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'f8ee5625-fea8-462f-81ee-3fbd4e70a4f6','data'=>array(
					)),
				)),
				array('key'=>'RADIOLOGY','name'=>'GENERAL RADIOLOGY','active'=>1,'guid'=>'6f0b5781-0bf9-4fb6-abcc-17573a43fc01','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'9b25207e-bd14-45f7-b87e-028b92da3a82','data'=>array(
						array('key'=>'1','name'=>'ABDOMEN 1 VIEW','active'=>1,'guid'=>'5dab1eea-27cd-466a-b224-7b488344cca2'),
						array('key'=>'2','name'=>'ABDOMEN 2 VIEWS','active'=>1,'guid'=>'36d4961a-53da-4ced-97d1-d246d0d8a67b'),
						array('key'=>'3','name'=>'ABDOMEN 3 OR MORE VIEWS','active'=>1,'guid'=>'302ea2e9-1a7e-4bab-8416-c9b78ae31a53'),
						array('key'=>'4','name'=>'ABDOMEN MIN 3 VIEWS+CHEST','active'=>1,'guid'=>'c5aceee1-7975-40a6-81e3-7af6e33ebc6d'),
						array('key'=>'5','name'=>'ACROMIOCLAVICULAR J BILAT','active'=>1,'guid'=>'1b8fee21-cef3-42f7-b686-f1a728d9ac41'),
						array('key'=>'6','name'=>'ANKLE 2 VIEWS','active'=>1,'guid'=>'71b61bd4-557f-4657-9abc-43832a752d4c'),
						array('key'=>'7','name'=>'ANKLE 3 OR MORE VIEWS','active'=>1,'guid'=>'ed7f62e5-26f4-4b24-9621-849e41799dba'),
						array('key'=>'8','name'=>'ARTHROGRAM ANKLE S&I','active'=>1,'guid'=>'6a81697c-0fee-4c2b-90b3-9fd3d7a2873e'),
						array('key'=>'9','name'=>'ARTHROGRAM ELBOW S&I','active'=>1,'guid'=>'31b269ab-8589-4d23-96e5-6bba59f472ac'),
						array('key'=>'10','name'=>'ARTHROGRAM HIP S&I','active'=>1,'guid'=>'9dbb98ed-ea82-442f-b21c-320e8b74d377'),
						array('key'=>'11','name'=>'ARTHROGRAM KNEE S&I','active'=>1,'guid'=>'9687d0fc-fe5f-44f7-826c-0b954a2549e6'),
						array('key'=>'12','name'=>'ARTHROGRAM SHOULDER S&I','active'=>1,'guid'=>'d788b0f1-38c5-4186-b507-7f094bce0a28'),
						array('key'=>'13','name'=>'ARTHROGRAM TM JOINT CONT S&I','active'=>1,'guid'=>'09d514f5-23cf-48d7-93aa-696f82f26a21'),
						array('key'=>'14','name'=>'ARTHROGRAM WRIST S&I','active'=>1,'guid'=>'f971ca12-bfec-4753-91c8-2a792911e7bc'),
						array('key'=>'15','name'=>'BONE AGE','active'=>1,'guid'=>'94e6fb5c-c3f5-4e8e-ae2d-50f1d4479e90'),
						array('key'=>'16','name'=>'BONE DENSITY STUDY','active'=>1,'guid'=>'bc688a47-6989-4983-bee3-5eec0155d121'),
						array('key'=>'17','name'=>'BONE LENGTH EXAM','active'=>1,'guid'=>'670c328f-36cf-428e-9cd8-e2a68a0f0572'),
						array('key'=>'18','name'=>'BONE SURV COMP (INCL APPENDIC SKEL)','active'=>1,'guid'=>'b8c1a945-7141-43d0-8bae-ac00b902b6a0'),
						array('key'=>'19','name'=>'BONE SURV LMTD (E.G. METASTATIC)','active'=>1,'guid'=>'2190f703-3b92-46cf-b214-6f79b68264c8'),
						array('key'=>'20','name'=>'BONE SURVEY INFANT','active'=>1,'guid'=>'780174eb-bf1d-4849-903e-91562b5f3304'),
						array('key'=>'21','name'=>'BRONCHOGRAM BILAT S&I','active'=>1,'guid'=>'51467143-e1c3-4e9f-9941-7b317f5e38e1'),
						array('key'=>'22','name'=>'BRONCHOGRAM UNILAT S&I','active'=>1,'guid'=>'87840ad0-b857-4ff7-85ef-e634b646ca79'),
						array('key'=>'23','name'=>'CALCANEOUS 2 VIEWS','active'=>1,'guid'=>'5155cb60-3b7e-4016-be7b-0a1edf36f3ca'),
						array('key'=>'24','name'=>'CEPHALOGRAM ORTHODONTIC','active'=>1,'guid'=>'a356c69e-db53-4908-a6ad-ce1a43493336'),
						array('key'=>'25','name'=>'CHEST 2 VIEWS PA&LAT','active'=>1,'guid'=>'8e907353-81da-4124-b431-2d463217319e'),
						array('key'=>'26','name'=>'CHEST 4 VIEWS','active'=>1,'guid'=>'aab6a15c-1309-414f-8b8d-6de4d3f57aa1'),
						array('key'=>'27','name'=>'CHEST APICAL LORDOTIC','active'=>1,'guid'=>'d3c05201-7a0d-433e-b78a-c620b839f5c5'),
						array('key'=>'28','name'=>'CHEST INCLUDE FLUORO','active'=>1,'guid'=>'702284d5-0d4f-45b2-9cf3-48454ac1c6ea'),
						array('key'=>'29','name'=>'CHEST OBLIQUE PROJECTIONS','active'=>1,'guid'=>'484f5b50-ddae-4f2c-b333-1a223881e44f'),
						array('key'=>'30','name'=>'CHEST SINGLE VIEW','active'=>1,'guid'=>'8dd806f2-cf0d-4a4a-83f4-7fa5d3a72fb9'),
						array('key'=>'31','name'=>'CHEST SPECIAL (LDECUB ETC)','active'=>1,'guid'=>'9c093fc8-a51e-4358-a2c9-a3f14875d518'),
						array('key'=>'32','name'=>'CHEST STEREO PA','active'=>1,'guid'=>'5ca5b93b-d5f0-4482-8959-33f0ea3d83a0'),
						array('key'=>'33','name'=>'CHOLANGIOGRAM OPERATIVE','active'=>1,'guid'=>'274e7026-77e2-4b95-b504-5d5b3942e584'),
						array('key'=>'34','name'=>'CHOLANGIOGRAM PERC S&I','active'=>1,'guid'=>'f74f3e55-7722-4898-a55b-dc940f129803'),
						array('key'=>'35','name'=>'CHOLANGIOGRAM T-TUBE','active'=>1,'guid'=>'73c5e314-c432-4ccb-bd7b-311fa473b075'),
						array('key'=>'36','name'=>'CHOLANGIOGRAPHY ADDTL SET IN SURGERY','active'=>1,'guid'=>'c33bb4c0-c1d2-434a-8a31-396870e89c7c'),
						array('key'=>'37','name'=>'CHOLECYSTOGRAM ORAL CONT','active'=>1,'guid'=>'7406a36d-72b1-417a-b911-f9a5ee96b51f'),
						array('key'=>'38','name'=>'CISTERNOGRAM POS CONT S&I','active'=>1,'guid'=>'5fa1d5e7-9b00-465c-ba18-75bca9bf0e20'),
						array('key'=>'39','name'=>'CLAVICLE','active'=>1,'guid'=>'8d7bfd78-38e7-42c3-8197-1de6e2f6611a'),
						array('key'=>'40','name'=>'COLON AIR CONTRAST','active'=>1,'guid'=>'b8d97e5e-dcf0-45eb-9990-f423d2198194'),
						array('key'=>'41','name'=>'COLON BARIUM ENEMA','active'=>1,'guid'=>'84c7163a-79d7-4fcd-a7a7-dfccdf518d11'),
						array('key'=>'42','name'=>'CONSULTATION OF OUTSIDE FILMS W/REPORT','active'=>1,'guid'=>'d5515bea-1311-4d00-a3f6-ec4ad0c42713'),
						array('key'=>'43','name'=>'CYSTOGRAM MIN 3 VIEWS S&I','active'=>1,'guid'=>'6b5dde19-024d-4c64-8479-5df15c64291d'),
						array('key'=>'44','name'=>'DACROCYSTOGRAM S&I','active'=>1,'guid'=>'b93af712-7d55-414c-a05c-8ac07c9012d6'),
						array('key'=>'45','name'=>'DISCOGRAM CERVICAL S&I','active'=>1,'guid'=>'88d56faa-46d5-43c9-8ef8-342da013cf96'),
						array('key'=>'46','name'=>'DISCOGRAM LUMBAR S&I','active'=>1,'guid'=>'de149693-60f7-4f5d-b6f5-db109e884dee'),
						array('key'=>'47','name'=>'DUODENOGRAPHY HYPOTONIC','active'=>1,'guid'=>'7e5463c7-a205-4a65-bfcf-31bf7cdc15ec'),
						array('key'=>'48','name'=>'ELBOW 2 VIEWS','active'=>1,'guid'=>'284c1e84-a82b-4dd6-86c6-874bd8011ac2'),
						array('key'=>'49','name'=>'ELBOW 3 OR MORE VIEWS','active'=>1,'guid'=>'3b45d97d-fc15-4352-87b7-b7adad5ab37a'),
						array('key'=>'50','name'=>'ENDOSCOPIC CATH BIL & PANC DUCTS S&I','active'=>1,'guid'=>'14a5eced-3eb5-4853-a3c6-12039695862f'),
						array('key'=>'51','name'=>'ENDOSCOPIC CATH BIL DUCTS S&I','active'=>1,'guid'=>'3adc1fd1-a5b8-4dd5-9aae-93d5657a757c'),
						array('key'=>'52','name'=>'ENDOSCOPIC CATH PANC DUCTS S&I','active'=>1,'guid'=>'0fcde572-aeea-4d17-bcd4-63598b4b4052'),
						array('key'=>'53','name'=>'EPIDIDYMOGRAM OR VASICULOGRAM S&I','active'=>1,'guid'=>'a486b3ea-2636-468d-bd6c-87bbd66926bc'),
						array('key'=>'54','name'=>'ESOPHAGUS','active'=>1,'guid'=>'e5527a52-ac3a-4a2c-97bf-3d9750dfa5a1'),
						array('key'=>'55','name'=>'ESOPHAGUS PHARYNX/CERVICAL','active'=>1,'guid'=>'2e4f2015-5618-47e8-bdd4-319b4b08fcf8'),
						array('key'=>'56','name'=>'ESOPHAGUS RAPID SEQUENCE FILMS','active'=>1,'guid'=>'83eb2b1b-2897-48fe-aa95-5965fad3750e'),
						array('key'=>'57','name'=>'EYE DETECTION FOREIGN BODY','active'=>1,'guid'=>'0a0446a3-3776-4d00-a913-3f7d8af42ccd'),
						array('key'=>'58','name'=>'FACIAL BONES LESS THAN 3 VIEWS','active'=>1,'guid'=>'a75f3421-6fe7-4b95-95d2-78dd31b134a0'),
						array('key'=>'59','name'=>'FEMUR 2 VIEWS','active'=>1,'guid'=>'60a2b063-49de-44bb-b553-35b7d1b8f1f3'),
						array('key'=>'60','name'=>'FINGER(S) 2 OR MORE VIEWS','active'=>1,'guid'=>'84647108-cdef-4038-8595-04cfbefa8329'),
						array('key'=>'61','name'=>'FISTULOGRAM OR SINOGRAM S&I','active'=>1,'guid'=>'05069aeb-0880-4b0a-ae18-cc93528bde10'),
						array('key'=>'62','name'=>'FLURO ABDOM(SEPARATE PROCEDURE)','active'=>1,'guid'=>'1a0c27fe-4d9b-424c-b256-65b6a9ee1015'),
						array('key'=>'63','name'=>'FLURO CHEST(SEPARATE PROCEDURE)','active'=>1,'guid'=>'adc685f3-aa6f-4a93-bd9f-31061b3097c6'),
						array('key'=>'64','name'=>'FOOT 2 VIEWS','active'=>1,'guid'=>'957f7cb6-da9e-4c20-b811-88625bd65c5f'),
						array('key'=>'65','name'=>'FOOT 3 OR MORE VIEWS','active'=>1,'guid'=>'e73bc218-035a-4da1-901e-ffb0bdac82c9'),
						array('key'=>'66','name'=>'FOREARM 2 VIEWS','active'=>1,'guid'=>'cc89f171-b9b3-4cfc-80bc-783af33d87cc'),
						array('key'=>'67','name'=>'HAND 1 OR 2 VIEWS','active'=>1,'guid'=>'48156047-0cc3-4519-8363-44258022a702'),
						array('key'=>'68','name'=>'HAND 3 OR MORE VIEWS','active'=>1,'guid'=>'c20d34e8-2ea4-42f0-9abf-8d0e026dfd12'),
						array('key'=>'69','name'=>'HIP 1 VIEW','active'=>1,'guid'=>'0421a03e-3f1f-4fe8-b99c-833089e48d3e'),
						array('key'=>'70','name'=>'HIP 2 OR MORE VIEWS','active'=>1,'guid'=>'6f8a7b89-3304-4628-b04f-0ce01430f24f'),
						array('key'=>'71','name'=>'HIP OPERATIVE 4 OR LESS STUDIES','active'=>1,'guid'=>'31a21f74-522b-4f52-af6b-e409f0413edb'),
						array('key'=>'72','name'=>'HIPS BILATERAL 4 OR MORE VIEWS','active'=>1,'guid'=>'69a017a1-ec04-4bea-8ea5-bcd1feed398c'),
						array('key'=>'73','name'=>'HUMERUS 2 OR MORE VIEWS','active'=>1,'guid'=>'c89db262-46d9-44c0-9057-21910099edf0'),
						array('key'=>'74','name'=>'HYSTEROSALPINGOGRAM S&I','active'=>1,'guid'=>'a7e88fbb-076f-47f7-88c5-eabdb1a03042'),
						array('key'=>'75','name'=>'INJECTION PROC FOR MYELOGRAPHY AND CT, SPINAL','active'=>1,'guid'=>'24f6e2dd-1e32-4308-a5c6-96886f1ab6d6'),
						array('key'=>'76','name'=>'JOINT SURV SING VIEW 1 OR MORE JOINTS','active'=>1,'guid'=>'96b67f8d-9eca-4d19-9062-7103c746d44c'),
						array('key'=>'77','name'=>'KNEE 2 VIEWS','active'=>1,'guid'=>'10184f4d-0733-4145-9c57-bc3ca3586ea1'),
						array('key'=>'78','name'=>'KNEE 3 VIEWS','active'=>1,'guid'=>'ac22b108-b62d-42bc-aa64-48ef08849805'),
						array('key'=>'79','name'=>'KNEE 4 OR MORE VIEWS','active'=>1,'guid'=>'0aa8445e-26a4-4584-bfce-2e20bb362330'),
						array('key'=>'80','name'=>'KNEES, BOTH, STANDING','active'=>1,'guid'=>'e73d65ae-bf91-41b9-866f-9c5db4e0419b'),
						array('key'=>'81','name'=>'KUB <ABDOMEN 1 VIEW>','active'=>1,'guid'=>'9b4a7f04-d309-46e7-89a5-8a1937b7cb8f'),
						array('key'=>'82','name'=>'LARYNGOGRAM CONT S&I','active'=>1,'guid'=>'5a0fe3b9-4aa9-48db-b25e-1b91d262e9bd'),
						array('key'=>'83','name'=>'LARYNX OR PHARNYX INCLUDING FLUORO','active'=>1,'guid'=>'3f728b1c-4101-4b50-82b0-30e56d58cb37'),
						array('key'=>'84','name'=>'LOWER EXTREMITY INFANT','active'=>1,'guid'=>'033d69cd-e616-4f00-9cf3-a22edabb5b16'),
						array('key'=>'85','name'=>'LYMPHANGIOGRAM EXTREMITY BILAT S&I','active'=>1,'guid'=>'49ce7a9c-4916-4601-8410-6fb0f292381e'),
						array('key'=>'86','name'=>'LYMPHANGIOGRAM EXTREMITY UNILAT S&I','active'=>1,'guid'=>'218f373a-48e2-4288-bcab-fb36b4e05234'),
						array('key'=>'87','name'=>'LYMPHANGIOGRAM PELVIC/ABD BILAT S&I','active'=>1,'guid'=>'0b4cda01-5bb4-4152-a0c0-fd18b01bbec4'),
						array('key'=>'88','name'=>'LYMPHANGIOGRAM PELVIC/ABD UNILAT S&I','active'=>1,'guid'=>'93fb17d9-7712-4d43-becb-6168d120e429'),
						array('key'=>'89','name'=>'MAMMARY DUCTOGRAM BILAT S&I','active'=>1,'guid'=>'7dde99f1-ce49-430b-8e2f-9ffee34e7bd6'),
						array('key'=>'90','name'=>'MAMMARY DUCTOGRAM UNILAT S&I','active'=>1,'guid'=>'84f9d2aa-5438-484e-aca2-8f334a96be0e'),
						array('key'=>'91','name'=>'MANDIBLE 4 OR MORE VIEWS','active'=>1,'guid'=>'082c2d2d-2aca-428c-abc9-3fe81f35486e'),
						array('key'=>'92','name'=>'MANDIBLE LESS THAN 4 VIEWS','active'=>1,'guid'=>'7c4a2e7d-aebf-446c-abc9-49b83d226f51'),
						array('key'=>'93','name'=>'MASTOIDS 3 OR MORE VIEWS/SIDE','active'=>1,'guid'=>'804e5e29-199c-42e0-aefa-3052826f4be7'),
						array('key'=>'94','name'=>'MASTOIDS LESS THAN 3 VIEWS/SIDE','active'=>1,'guid'=>'bec6d4d6-3b81-4828-825a-411658512813'),
						array('key'=>'95','name'=>'MYELOGRAM CERVICAL S&I','active'=>1,'guid'=>'ab181647-158f-403c-8915-bbb00ea81815'),
						array('key'=>'96','name'=>'MYELOGRAM ENTIRE SPINE S&I','active'=>1,'guid'=>'f695fcb9-8569-476b-ac58-9b4d93c8b259'),
						array('key'=>'97','name'=>'MYELOGRAM LUMBAR S&I','active'=>1,'guid'=>'80797e7e-4233-4c8f-b8d6-7e1a01c5e45b'),
						array('key'=>'98','name'=>'MYELOGRAM POST FOSSA S&I','active'=>1,'guid'=>'4c3f38a5-16e5-46c4-a72b-9ddfc03af2ea'),
						array('key'=>'99','name'=>'MYELOGRAM THORACIC S&I','active'=>1,'guid'=>'0da797f4-ac1b-4086-b713-19171ade460a'),
						array('key'=>'100','name'=>'MYELOGRAM-CERVICAL','active'=>1,'guid'=>'a220fbc6-a48c-4cb2-ad35-2b91dc016652'),
						array('key'=>'101','name'=>'MYELOGRAM-LUMBAR','active'=>1,'guid'=>'4302fb27-af5c-41cd-9304-cd325375eaa9'),
						array('key'=>'102','name'=>'MYELOGRAM-THORACIC','active'=>1,'guid'=>'0db0ca3f-4423-485f-9f57-806c70d66c0f'),
						array('key'=>'103','name'=>'NASAL BONES MIN 3 VIEWS','active'=>1,'guid'=>'d383ca67-a705-4fbc-87aa-ad3bddd4f7c0'),
						array('key'=>'104','name'=>'NECK SOFT TISSUE','active'=>1,'guid'=>'1c2eb7b8-0f94-4b4c-a0d0-d61adb07a03d'),
						array('key'=>'105','name'=>'NON-INVAS.,UPPER EXT. ART.','active'=>1,'guid'=>'01aca8ac-1b42-467b-b34f-6883bede5875'),
						array('key'=>'106','name'=>'OPTIC FORAMINA','active'=>1,'guid'=>'7699bbf5-24c5-48b4-b8a0-e7322e5376db'),
						array('key'=>'107','name'=>'ORBIT MIN 4 VIEWS','active'=>1,'guid'=>'d9e30428-36e7-464d-8093-7ed9dbcf724d'),
						array('key'=>'108','name'=>'PACEMAKER FLUORO & FILMS S&I','active'=>1,'guid'=>'40dd5adf-6cb3-4eeb-9d70-7be8ee75dc38'),
						array('key'=>'109','name'=>'PANOREX','active'=>1,'guid'=>'b52732fc-617a-4c35-b317-f3f8846089ba'),
						array('key'=>'110','name'=>'PELVIMETRY','active'=>1,'guid'=>'843db8c8-80d2-4df6-ae3c-f189caa7a808'),
						array('key'=>'111','name'=>'PELVIS & HIPS CHILD 2 OR MORE VIEWS','active'=>1,'guid'=>'ef07c462-471f-4ebf-8858-76e50263d7cf'),
						array('key'=>'112','name'=>'PELVIS 1 VIEW','active'=>1,'guid'=>'a1eb8739-9465-4d6a-8d88-134b5e2d733d'),
						array('key'=>'113','name'=>'PELVIS 3 OR MORE VIEWS','active'=>1,'guid'=>'21ec241f-cdcb-452f-81be-21834037dfc6'),
						array('key'=>'114','name'=>'RENAL CYST STUDY PERC S&I','active'=>1,'guid'=>'a3290fac-06ae-4775-8af8-0eed5e18bff7'),
						array('key'=>'115','name'=>'RIBS BILAT 3 OR MORE VIEWS','active'=>1,'guid'=>'e349357f-b3db-4844-b76e-bd5ca8025c73'),
						array('key'=>'116','name'=>'RIBS BILAT+CHEST 4 OR MORE VIEWS','active'=>1,'guid'=>'ae03ce03-7fa6-42f2-8e6e-4675ed856314'),
						array('key'=>'117','name'=>'RIBS UNILAT 2 VIEWS','active'=>1,'guid'=>'d0266300-e902-496b-8492-098c3210c6f9'),
						array('key'=>'118','name'=>'RIBS UNILAT+CHEST 3 OR MORE VIEWS','active'=>1,'guid'=>'49bc5780-fb0f-453c-8ead-2246161ab278'),
						array('key'=>'119','name'=>'SALIVARY GLAND FOR STONE','active'=>1,'guid'=>'3225a71e-edb7-4be1-8dde-faed24f8154a'),
						array('key'=>'120','name'=>'SCAPULA','active'=>1,'guid'=>'500efc6c-50a8-4709-809d-93d09c218b7b'),
						array('key'=>'121','name'=>'SELLA TURCICA','active'=>1,'guid'=>'71fec17c-b44a-498f-b118-1de8db070b7e'),
						array('key'=>'122','name'=>'SHOULDER 1 VIEW','active'=>1,'guid'=>'69f77848-1670-4eb3-97f4-544a38c79a99'),
						array('key'=>'123','name'=>'SHOULDER 2 OR MORE VIEWS','active'=>1,'guid'=>'ffb30762-9629-47b6-9804-8f592b9ab1d7'),
						array('key'=>'124','name'=>'SIALOGRAM S&I','active'=>1,'guid'=>'c97e61ed-385c-47a7-8279-b035b385d342'),
						array('key'=>'125','name'=>'SINUSES 3 OR MORE VIEWS','active'=>1,'guid'=>'c31d21a4-55c7-4103-804f-3dd5d5411476'),
						array('key'=>'126','name'=>'SINUSES MIN 2 VIEWS','active'=>1,'guid'=>'734068bb-c039-4433-b072-5743ca438685'),
						array('key'=>'127','name'=>'SKULL 4 OR MORE VIEWS','active'=>1,'guid'=>'56d172ab-3ef8-4bd0-8361-267c47f080b7'),
						array('key'=>'128','name'=>'SKULL LESS THAN 4 VIEWS','active'=>1,'guid'=>'b2c60408-b3bb-478d-9d13-5b00b33a0772'),
						array('key'=>'129','name'=>'SMALL BOWEL MULT FILMS','active'=>1,'guid'=>'9367ecb3-d0a5-4365-9906-04f01f79b4b6'),
						array('key'=>'130','name'=>'SPINE CERVICAL MIN 2 VIEWS','active'=>1,'guid'=>'09ecdb00-8042-4868-9436-0e24039ef964'),
						array('key'=>'131','name'=>'SPINE CERVICAL MIN 4 VIEWS','active'=>1,'guid'=>'cf50455b-6553-4cd9-bea3-b61697ae642f'),
						array('key'=>'132','name'=>'SPINE CERVICAL MIN 6 VIEWS','active'=>1,'guid'=>'7fd58436-f57a-401e-be5d-3578b8b72b28'),
						array('key'=>'133','name'=>'SPINE ENTIRE AP&LAT','active'=>1,'guid'=>'1622f7df-b92a-4167-b24f-fd9e2dacdfbf'),
						array('key'=>'134','name'=>'SPINE LS BENDING MIN 4 VIEWS','active'=>1,'guid'=>'1b263491-ff33-48a7-a19b-de7a279a51a0'),
						array('key'=>'135','name'=>'SPINE LUMBOSACRAL MIN 2 VIEWS','active'=>1,'guid'=>'4f84e2a7-1949-4550-a0ad-d9137b2bda04'),
						array('key'=>'136','name'=>'SPINE LUMBOSACRAL MIN 4 VIEWS','active'=>1,'guid'=>'7246772f-394a-47c0-ad8a-70f9743556cf'),
						array('key'=>'137','name'=>'SPINE LUMBOSACRAL MIN 6 VIEWS','active'=>1,'guid'=>'f6e16257-c04f-447f-96a7-d8891aac1c5a'),
						array('key'=>'138','name'=>'SPINE SACRUM & COCCYX MIN 2 VIEWS','active'=>1,'guid'=>'d9302a52-3610-4bd6-a58e-aab99229d68e'),
						array('key'=>'139','name'=>'SPINE SCOLIOSIS EXAM MIN 2 VIEWS','active'=>1,'guid'=>'6694e101-2fff-4a82-afde-039f1c925dce'),
						array('key'=>'140','name'=>'SPINE SI JOINTS 1 OR 2 VIEWS','active'=>1,'guid'=>'5fd5e373-24d0-4d49-838a-ea12cab1688f'),
						array('key'=>'141','name'=>'SPINE SI JOINTS 3 OR MORE VIEWS','active'=>1,'guid'=>'2befadf3-8256-495a-9bfe-94536f6d4a4a'),
						array('key'=>'142','name'=>'SPINE SINGLE VIEW','active'=>1,'guid'=>'bbb0096d-fde3-4e2e-ac5f-afabd7185a82'),
						array('key'=>'143','name'=>'SPINE THORACIC 2 VIEWS','active'=>1,'guid'=>'094e11e8-f4de-4c1c-8f50-f388a546d721'),
						array('key'=>'144','name'=>'SPINE THORACIC 4 OR MORE VIEWS','active'=>1,'guid'=>'32260261-da0d-44bc-b44d-7dcbfc76ee66'),
						array('key'=>'145','name'=>'SPINE THORACIC AP&LAT&SWIM VIEWS','active'=>1,'guid'=>'b35ffc9f-4982-41fa-8948-6ae416108423'),
						array('key'=>'146','name'=>'SPINE THORACOLUMBAR 2 VIEWS','active'=>1,'guid'=>'5e81823e-81fd-4762-951f-1c16f39691ad'),
						array('key'=>'147','name'=>'STERNOCLAV JOINT MIN 3 VIEWS','active'=>1,'guid'=>'014051cb-2d0c-40ac-be20-e43724cfa2f8'),
						array('key'=>'148','name'=>'STERNUM 2 OR MORE VIEWS','active'=>1,'guid'=>'fc8c476e-7005-4298-b9b7-0920b62d20c6'),
						array('key'=>'149','name'=>'SUBTRACTION IN CONJUNCTION W CONT STUDIES','active'=>1,'guid'=>'1b64010f-9189-43e1-931d-4f41e414073f'),
						array('key'=>'150','name'=>'TEETH FULL MOUTH','active'=>1,'guid'=>'15430d0b-2d0b-4086-87e5-8f2b8f9cffd2'),
						array('key'=>'151','name'=>'TEETH PARTIAL EXAM','active'=>1,'guid'=>'6dbab94d-f989-434b-a944-d0cc62f702dc'),
						array('key'=>'152','name'=>'TEETH SINGLE VIEW','active'=>1,'guid'=>'e25d1964-c157-40ed-abf0-03ceca27db3e'),
						array('key'=>'153','name'=>'TIBIA & FIBULA 2 VIEWS','active'=>1,'guid'=>'d7f1d3b9-c74e-4e3b-ba0d-3c514240d373'),
						array('key'=>'154','name'=>'TM JOINT UNILAT O&C MOUTH','active'=>1,'guid'=>'7e22064f-36a5-40ff-ba9f-084f47f39eed'),
						array('key'=>'155','name'=>'TM JOINTS BILAT O&C MOUTH','active'=>1,'guid'=>'0651d403-8af4-4449-bf1b-f8881c03299f'),
						array('key'=>'156','name'=>'TOE(S) 2 OR MORE VIEWS','active'=>1,'guid'=>'69d21d75-1684-43e6-8cec-5bce665b527c'),
						array('key'=>'157','name'=>'TOMOGRAM COMPLEX MOTION BILAT','active'=>1,'guid'=>'fa0c48ca-ac50-4d22-9276-c18985009d75'),
						array('key'=>'158','name'=>'TOMOGRAM COMPLEX MOTION UNILAT','active'=>1,'guid'=>'d161e13e-cd30-4b91-8f28-122de66468f9'),
						array('key'=>'159','name'=>'TOMOGRAM OTHER THAN KIDNEY','active'=>1,'guid'=>'8427a9b3-f7ce-4b34-a538-679c58aa696a'),
						array('key'=>'160','name'=>'TRANSCATH BIOPSY S&I','active'=>1,'guid'=>'b54b4956-576f-4414-bb65-3ee485f5234c'),
						array('key'=>'161','name'=>'TRANSCATH EMBOLIZATION W/ANGIO S&I','active'=>1,'guid'=>'1f422c04-5a8b-4f2e-abb5-888630984d6c'),
						array('key'=>'162','name'=>'TRANSCATH INFUSION W/ANGIO S&I','active'=>1,'guid'=>'0199b93c-ae1a-4c52-a3b3-61e36c63e247'),
						array('key'=>'163','name'=>'TRANSCATH RETRIEV FRACTURED INTRAVASC CATH','active'=>1,'guid'=>'69001e8a-88d4-4aa6-a878-cb9ceb3d2090'),
						array('key'=>'164','name'=>'TRANSCATH VASC OCCL PERM W/ANGIO CP','active'=>1,'guid'=>'c1ecc1c2-388d-46dc-933d-fc505ecb4b5a'),
						array('key'=>'165','name'=>'ugi <UPPER GI + SMALL BOWEL>','active'=>1,'guid'=>'426b4174-41f4-4fac-b2f6-2456f82e0f33'),
						array('key'=>'166','name'=>'UNLISTED RADIOLOGIC PROCEDURE','active'=>1,'guid'=>'41f13097-64e2-4a6a-8210-d9f09f52ad8f'),
						array('key'=>'167','name'=>'UPPER EXTREMITY INFANT','active'=>1,'guid'=>'2fc77621-ddee-490b-a477-425d953c79ff'),
						array('key'=>'168','name'=>'UPPER GI + SMALL BOWEL','active'=>1,'guid'=>'c024a5e8-4ac3-4360-9855-f27353da4eb5'),
						array('key'=>'169','name'=>'UPPER GI AIR CONT W/O KUB','active'=>1,'guid'=>'0f035cb6-0bd1-4cb3-a47c-f0482c2f4cad'),
						array('key'=>'170','name'=>'UPPER GI AIR CONT W/SMALL BOWEL','active'=>1,'guid'=>'196f5197-25b1-4e56-ab26-a0ef4ab94c61'),
						array('key'=>'171','name'=>'UPPER GI AIR CONT WITH KUB','active'=>1,'guid'=>'dfc83633-5be0-4149-ae30-5e0b0e83f58d'),
						array('key'=>'172','name'=>'UPPER GI W/O KUB','active'=>1,'guid'=>'5cd6e2d7-1e6c-4c26-a8d9-c520aae200e7'),
						array('key'=>'173','name'=>'UPPER GI WITH KUB','active'=>1,'guid'=>'a8c2e474-aeb5-43dd-926a-0dc6d629b301'),
						array('key'=>'174','name'=>'URETHROCYSTOGRAM RETROGRADE S&I','active'=>1,'guid'=>'aacd4267-a075-44d2-b4d1-de62ac61d61e'),
						array('key'=>'175','name'=>'URETHROCYSTOGRAM VOIDING S&I','active'=>1,'guid'=>'d8dcc9aa-7fb2-4aa6-9421-175b04a5b7fb'),
						array('key'=>'176','name'=>'UROGRAM ANTEGRADE (INCLUDE LOOPOGRAM) S&I','active'=>1,'guid'=>'5494e0c3-16ea-447c-bb32-d0d377daff64'),
						array('key'=>'177','name'=>'UROGRAM INTRAVENOUS','active'=>1,'guid'=>'98f39b45-8149-4b5c-8ba8-d64c348ce2de'),
						array('key'=>'178','name'=>'UROGRAM IV DRIP INFUSION','active'=>1,'guid'=>'3ef5873a-7229-4290-8eb8-a478910635fd'),
						array('key'=>'179','name'=>'UROGRAM IV W NEPHROTOMOGRAMS','active'=>1,'guid'=>'47c1dc83-357f-496c-b6ff-4395d0f30ce1'),
						array('key'=>'180','name'=>'UROGRAM RETROGRADE','active'=>1,'guid'=>'3d478746-e2c2-4b2b-8c67-f90e867dfbe1'),
						array('key'=>'181','name'=>'VENOGRAM ADRENAL BILAT SELECT S&I','active'=>1,'guid'=>'c8b88644-bb43-4774-8b80-7c0de2942bb7'),
						array('key'=>'182','name'=>'VENOGRAM ADRENAL UNILAT SELECT S&I','active'=>1,'guid'=>'7774a354-f27c-4af7-a881-99ae55bee1d6'),
						array('key'=>'183','name'=>'VENOGRAM CAVA INF W/SERIAL FILMS S&I','active'=>1,'guid'=>'ab51be4b-a998-42c6-9d54-f73a835bab6b'),
						array('key'=>'184','name'=>'VENOGRAM CAVA SUP W/SERIAL FILMS S&I','active'=>1,'guid'=>'83546457-4514-4888-820b-37b345710507'),
						array('key'=>'185','name'=>'VENOGRAM EPIDURAL S&I','active'=>1,'guid'=>'83e5e7b8-c088-48e4-bf38-b880c1dd8646'),
						array('key'=>'186','name'=>'VENOGRAM EXTREMITY BILAT S&I','active'=>1,'guid'=>'4430a93a-18aa-4e41-a0b0-572845218542'),
						array('key'=>'187','name'=>'VENOGRAM EXTREMITY UNILAT S&I','active'=>1,'guid'=>'80eb005c-77fe-4d80-adf9-6d98afe23908'),
						array('key'=>'188','name'=>'VENOGRAM HEPAT WEDGE OR FREE W/HEMODYNAM S&I','active'=>1,'guid'=>'99025695-6d78-467a-9fad-a781dc52b286'),
						array('key'=>'189','name'=>'VENOGRAM HEPAT WEDGE OR FREE W/O HEMODYNAM S&I','active'=>1,'guid'=>'6c0e8b8c-432a-49e0-8ee4-7d703f91e460'),
						array('key'=>'190','name'=>'VENOGRAM ORBITAL S&I','active'=>1,'guid'=>'e00fb2a0-1f63-4e3e-a831-8bd27519882f'),
						array('key'=>'191','name'=>'VENOGRAM RENAL BILAT SELECT S&I','active'=>1,'guid'=>'fe2179df-105b-4a34-b833-91aff623cb3f'),
						array('key'=>'192','name'=>'VENOGRAM RENAL UNILAT SELECT S&I','active'=>1,'guid'=>'483fde35-70f0-4b83-9243-52d846a82dc7'),
						array('key'=>'193','name'=>'VENOGRAM SAGITTAL SINUS S&I','active'=>1,'guid'=>'80c8c9b5-bd37-439e-9686-42e2c2f8d5d1'),
						array('key'=>'194','name'=>'VENOGRAM SINUS OR JUGULAR CATH S&I','active'=>1,'guid'=>'e42cbb9c-67ae-4dc8-9afb-15ad7715e49b'),
						array('key'=>'195','name'=>'VENOGRAM SPLENOPORTOGRAM S&I','active'=>1,'guid'=>'44eea726-49b2-4c51-9726-9e44b72e9d58'),
						array('key'=>'196','name'=>'WRIST 2 VIEWS','active'=>1,'guid'=>'18b3d9fd-df0c-4872-9f19-ef417fa49af0'),
						array('key'=>'197','name'=>'WRIST 3 OR MORE VIEWS','active'=>1,'guid'=>'1c271c4e-c61c-4ba2-9ad5-96721ec1033b'),
						array('key'=>'198','name'=>'XEROGRAPHY','active'=>1,'guid'=>'e346ae7e-fc9b-43b7-822d-d468654b25b2'),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'1337a2da-cef0-4230-acfc-c60493ed69d6','data'=>array(
						array('key'=>'BILATEXAM','name'=>'BILATERAL EXAM','active'=>1,'guid'=>'ad616ffc-66d7-47e1-97df-0f738d4a018a'),
						array('key'=>'PORTABLE','name'=>'PORTABLE EXAM','active'=>1,'guid'=>'fb65661b-72fb-44bd-b0c9-ce837d78da48'),
						array('key'=>'OREXAM','name'=>'OPERATING ROOM EXAM','active'=>1,'guid'=>'13d448c2-c527-4b9d-a430-1d8adc10e177'),
						array('key'=>'LEFT','name'=>'LEFT','active'=>1,'guid'=>'a447b228-6eb0-4074-98a0-7f25f00421ab'),
						array('key'=>'RIGHT','name'=>'RIGHT','active'=>1,'guid'=>'724d7e90-ac10-458a-b8a2-22b0dbe6b3ca'),
						array('key'=>'OBLIQUE','name'=>'OBLIQUE','active'=>1,'guid'=>'a75e6409-52a5-453b-8781-6afacf3c584d'),
						array('key'=>'SUNRISE','name'=>'SUNRISE','active'=>1,'guid'=>'1132449f-d413-4249-9e06-0f369e002308'),
						array('key'=>'SWIMMERS','name'=>'SWIMMERS','active'=>1,'guid'=>'3989ca09-8440-45ff-bf3c-c1e8061ed038'),
						array('key'=>'WATERS','name'=>'WATERS','active'=>1,'guid'=>'c0f9240c-8bd2-48db-896b-672b45d3244d'),
						array('key'=>'PA','name'=>'PA','active'=>1,'guid'=>'e370339f-65c4-46b4-a1cf-1648457757b1'),
						array('key'=>'LAT','name'=>'LAT','active'=>1,'guid'=>'f6cffee9-c5d0-4896-abdb-582cf422a0ce'),
						array('key'=>'AP','name'=>'AP','active'=>1,'guid'=>'c56ce322-e38d-4474-ba22-ad71f8550490'),
					)),
				)),
				array('key'=>'ULTRASOUND','name'=>'ULTRASOUND','active'=>1,'guid'=>'db7ab5af-564a-42ad-a815-e95b3ae87155','data'=>array(
					array('key'=>'PROCEDURES','name'=>'Procedures','active'=>1,'guid'=>'00c97387-ed16-4ad3-ad4f-fa0b06312ba9','data'=>array(
						array('key'=>'1','name'=>'AMNIO <ULTRASOUND-GUIDED AMNIOCENTESIS>','active'=>1,'guid'=>'e3cb0a3f-6074-471a-bba9-8da36b00f95c'),
						array('key'=>'2','name'=>'AMNIOCENTESIS, DIAGNOSTIC','active'=>1,'guid'=>'b38fbc3d-7a6c-4a00-bf00-491242515442'),
						array('key'=>'3','name'=>'BIOPSY OF LIVER, NEEDLE, PERCUTANEOUS','active'=>1,'guid'=>'e0a1dd5f-388d-4a6f-bf26-473a2eb6cd9a'),
						array('key'=>'4','name'=>'BIOPSY OF LUNG OR MEDIASTINUM, PERCUTANEOUS NEEDLE','active'=>1,'guid'=>'57ab7361-a8fc-4c37-984b-d32c053413da'),
						array('key'=>'5','name'=>'DUPLEX CAROTID BILATERAL','active'=>1,'guid'=>'ddfd5886-569a-4d21-b518-b09bc30a6750'),
						array('key'=>'6','name'=>'DUPLEX CAROTID UNILATERAL OR LTD','active'=>1,'guid'=>'b126bb23-bb6b-4793-8be2-8cdb769acb7c'),
						array('key'=>'7','name'=>'DUPLEX SCAN LOWER EXT ARTERY BILAT','active'=>1,'guid'=>'169414bc-f12b-4961-a70e-158776e29c24'),
						array('key'=>'8','name'=>'DUPLEX SCAN LOWER EXT ARTERY UNILAT','active'=>1,'guid'=>'9eb1b393-2c06-4ce7-9dc6-ce3d3d1c29e1'),
						array('key'=>'9','name'=>'DUPLEX SCAN UPPER EXT ARTERY COMPLET BILAT','active'=>1,'guid'=>'c25815e2-0ab3-41af-ac47-47c96cc1289c'),
						array('key'=>'10','name'=>'DUPLEX SCAN UPPER EXT ARTERY UNILAT','active'=>1,'guid'=>'70c49190-069c-4543-944c-48ad3b756085'),
						array('key'=>'11','name'=>'ECHOENCEPHALOGRAM B-SCAN &/OR REALTIME','active'=>1,'guid'=>'c05ecaa8-0c7f-4c43-9c77-ee6e70f6aeb4'),
						array('key'=>'12','name'=>'ECHOENCEPHALOGRAM COMPLETE','active'=>1,'guid'=>'48500161-3343-46c3-ac54-1cb263a945a5'),
						array('key'=>'13','name'=>'ECHOGRAM ABDOMEN COMPLETE','active'=>1,'guid'=>'816baaf9-0052-4eb1-941e-d930fd87fc33'),
						array('key'=>'14','name'=>'ECHOGRAM ABDOMEN LTD','active'=>1,'guid'=>'b297d600-a6a8-4767-b377-358ceca0633e'),
						array('key'=>'15','name'=>'ECHOGRAM AMNIOCENTESIS S&I','active'=>1,'guid'=>'784c02c7-5fa8-493a-b636-893e33da9eab'),
						array('key'=>'16','name'=>'ECHOGRAM BREAST A-MODE','active'=>1,'guid'=>'0db2d97f-3d8b-4ac7-8386-401dc3e74beb'),
						array('key'=>'17','name'=>'ECHOGRAM BREAST B-SCAN &/OR REAL TIME','active'=>1,'guid'=>'f453a006-5d6c-4a21-9ba3-f37f58fe4ecd'),
						array('key'=>'18','name'=>'ECHOGRAM CHEST B-SCAN','active'=>1,'guid'=>'bf23be59-abc3-440e-abc3-4c26feaa2197'),
						array('key'=>'19','name'=>'ECHOGRAM CONTACT B-SCAN','active'=>1,'guid'=>'664d48a2-761e-462d-a939-e6ba303fe72a'),
						array('key'=>'20','name'=>'ECHOGRAM EXTREMITY B-SCAN &/OR REAL TIME W/IMAG','active'=>1,'guid'=>'451fc10d-140b-46e8-b09c-caf641d66a9f'),
						array('key'=>'21','name'=>'ECHOGRAM EYE BIOMETRY A-MODE','active'=>1,'guid'=>'5d4473b5-d54b-4b7e-8c6e-92dbec379b6a'),
						array('key'=>'22','name'=>'ECHOGRAM EYE FB LOCALIZATION','active'=>1,'guid'=>'ae1bb2ac-8415-4b68-b065-d50d3f8165a1'),
						array('key'=>'23','name'=>'ECHOGRAM FOLLOWUP (SPECIFY)','active'=>1,'guid'=>'58026598-fd75-4da7-8c8d-9cab27ed43c1'),
						array('key'=>'24','name'=>'ECHOGRAM NEEDLE BIOPSY S&I','active'=>1,'guid'=>'cec71db6-c7ec-4dda-8c0a-9152b6c1f0e3'),
						array('key'=>'25','name'=>'ECHOGRAM OPHTHALMIC SPECTRAL ANALYSIS A-MODE','active'=>1,'guid'=>'c9712bc9-97bc-44ed-adf3-9698a9c10e1d'),
						array('key'=>'26','name'=>'ECHOGRAM OTHER UNLISTED','active'=>1,'guid'=>'a3bf8f37-c820-4262-847d-038f27384eba'),
						array('key'=>'27','name'=>'ECHOGRAM PELVIC B-SCAN &/OR REAL TIME W/IMAGING','active'=>1,'guid'=>'6e6712d2-4aec-45dc-8ef3-a9e7eadf7748'),
						array('key'=>'28','name'=>'ECHOGRAM PELVIC COMPLETE','active'=>1,'guid'=>'e0fa4948-06ff-46a3-8c7e-e237a91a5d25'),
						array('key'=>'29','name'=>'ECHOGRAM PELVIC LIMITED','active'=>1,'guid'=>'d40b35bd-c71a-4a1b-8cb0-499003f06aca'),
						array('key'=>'30','name'=>'ECHOGRAM PERICARDIOCENTESIS S&I','active'=>1,'guid'=>'51cb2293-934d-4560-b55a-5ddbfa44f3f8'),
						array('key'=>'31','name'=>'ECHOGRAM RETROPERITONEAL COMPLETE','active'=>1,'guid'=>'a63d8339-6d31-430d-ac89-6daa5788502a'),
						array('key'=>'32','name'=>'ECHOGRAM RETROPERITONEAL LIMITED','active'=>1,'guid'=>'b579d06c-838b-41ed-95c2-6f477d169793'),
						array('key'=>'33','name'=>'ECHOGRAM RX FIELDS B-SCAN','active'=>1,'guid'=>'67bfc51b-1e2b-4c3c-a80a-a4f859e17cbc'),
						array('key'=>'34','name'=>'ECHOGRAM SCROTUM','active'=>1,'guid'=>'5e34ec84-8056-4212-a462-2a7e0144f084'),
						array('key'=>'35','name'=>'ECHOGRAM SOFT TISSUE OF NECK','active'=>1,'guid'=>'36beda79-feb0-41fd-ac34-b9ad839776c6'),
						array('key'=>'36','name'=>'FINE NEEDLE ASPIRATION WITH IMAGING GUIDANCE','active'=>1,'guid'=>'078c65ad-9199-49b7-88fa-866c2a13b2c8'),
						array('key'=>'37','name'=>'ULTRASOUND ABDOMEN, COMPLETE','active'=>1,'guid'=>'dd908577-d663-4f4b-8000-e2e8e26fa826'),
						array('key'=>'38','name'=>'ULTRASOUND ABDOMEN, LIMITED (SINGLE ORGAN, QUADRANT, F/U)','active'=>1,'guid'=>'451bb327-1714-4390-9412-632377815b34'),
						array('key'=>'39','name'=>'ULTRASOUND BIOPHYSICAL PROFILE W/O NON-STRESS TESTING','active'=>1,'guid'=>'c670e04d-bfcd-4499-bb53-4b888af2c6c5'),
						array('key'=>'40','name'=>'ULTRASOUND ENDOVAGINAL (NON-OB)','active'=>1,'guid'=>'50a27f9a-c23f-4d1d-895b-e4e8abd3b033'),
						array('key'=>'41','name'=>'ULTRASOUND PELVIS, NON-OB','active'=>1,'guid'=>'ed51b0ae-fa8b-4773-b4b6-0e061b1e7e6a'),
						array('key'=>'42','name'=>'ULTRASOUND SCROTUM AND CONTENTS (PROSTATE)','active'=>1,'guid'=>'8b29e194-fa98-4247-93fc-5c5cffb7a938'),
						array('key'=>'43','name'=>'ULTRASOUND SOFT TISSUE NECK, THYROID, PARATHYROID, PAROTID)','active'=>1,'guid'=>'250d6fd0-71d7-43c1-9f51-0d37b62a6ea5'),
						array('key'=>'44','name'=>'ULTRASOUND, BREAST(S)','active'=>1,'guid'=>'ae8267fc-e0de-47aa-9729-5084354d109b'),
						array('key'=>'45','name'=>'ULTRASOUND, EXTREMITY, NON-VASCULAR','active'=>1,'guid'=>'515a94b2-8e39-49fe-aea7-b2e0dafe881d'),
						array('key'=>'46','name'=>'ULTRASOUND, PREGNANT UTERUS <14 WKS, SINGLE FETUS','active'=>1,'guid'=>'1ac1e51d-b966-45a8-9136-c22f41515691'),
						array('key'=>'47','name'=>'ULTRASOUND, PREGNANT UTERUS >/=14 WKS, SINGLE FETUS','active'=>1,'guid'=>'9f320b17-1038-4cb6-9944-b9287409288c'),
						array('key'=>'48','name'=>'ULTRASOUND, PREGNANT UTERUS LIMITED,FETUS(S)','active'=>1,'guid'=>'54532e4d-831d-444e-aba5-3a329165e6c5'),
						array('key'=>'49','name'=>'ULTRASOUND, PREGNANT UTERUS, TRANSVAGINAL','active'=>1,'guid'=>'ddd42d15-5669-4b68-9352-e0693d804648'),
						array('key'=>'50','name'=>'ULTRASOUND, RETROPERITONEAL (EG, RENAL,AORTA,NODES)','active'=>1,'guid'=>'ae87b333-3270-4cc6-8b05-6b41fb62bd05'),
						array('key'=>'51','name'=>'ULTRASOUND-GUIDED AMNIOCENTESIS','active'=>1,'guid'=>'f8980f1c-1071-4fa2-b132-056896d49b4f'),
						array('key'=>'52','name'=>'ULTRASOUND-GUIDED NEEDLE PLACEMENT','active'=>1,'guid'=>'36445943-fa19-4112-bcb5-0c74050b473f'),
						array('key'=>'53','name'=>'VENOUS DOPPLER, BILATERAL','active'=>1,'guid'=>'4179bf4a-8f75-4263-a8f0-3fd9106fb3d6'),
						array('key'=>'54','name'=>'VENOUS DOPPLER, UNILATERAL','active'=>1,'guid'=>'b41ba0c6-2f16-4662-aee8-0d2338182661'),
					)),
					array('key'=>'MODIFIERS','name'=>'Modifiers','active'=>1,'guid'=>'6c353f6e-2f23-4083-b8e8-08d66545fa23','data'=>array(
						array('key'=>'LEFT','name'=>'LEFT','active'=>1,'guid'=>'7d364cb9-65d9-49f8-85e3-5bbc41642865'),
					)),
				)),
			);

			$categories = array(
				array('key'=>'ONSITE','name'=>'ONSITE','active'=>1,'guid'=>'c0fbb671-7c8d-4275-bb3c-d02d7b169012'),
				array('key'=>'OUTPATIENT','name'=>'OUTPATIENT','active'=>1,'guid'=>'a0ea036f-8d54-4f38-b02d-748d74cbdb3e'),
				array('key'=>'EMPLOYEE','name'=>'EMPLOYEE','active'=>1,'guid'=>'1782845d-07a2-4b2a-ada2-d3ebd5941d1c'),
				array('key'=>'CONTRACT','name'=>'CONTRACT','active'=>1,'guid'=>'96cc589a-51c1-4a62-bb52-117087dcd363'),
				array('key'=>'SHARING','name'=>'SHARING','active'=>1,'guid'=>'aa1ae4e6-f56a-4f04-857b-682fc42b82f7'),
				array('key'=>'RESEARCH','name'=>'RESEARCH','active'=>1,'guid'=>'5b3a43c8-9aa4-4a1f-b13f-4fbed977fc75'),
			);

			$urgencies = array(
				array('key'=>'ASAP','name'=>'ASAP','active'=>1,'guid'=>'fe986120-94fd-4447-a076-74395fb281d5'),
				array('key'=>'ROUTINE','name'=>'ROUTINE','active'=>1,'guid'=>'12344fa8-e200-43e2-a222-213e27279bfb'),
				array('key'=>'STAT','name'=>'STAT','active'=>1,'guid'=>'3d2f7f51-ad1f-43cc-b6e1-4e14c4ad0713'),
			);

			$transports = array(
				array('key'=>'AMBULATORY','name'=>'AMBULATORY','active'=>1,'guid'=>'975918bb-da4a-4a6c-a388-6ac3df17bf7b'),
				array('key'=>'PORTABLE','name'=>'PORTABLE','active'=>1,'guid'=>'7f1fe2e9-f363-4529-884d-da1ea9bd3824'),
				array('key'=>'STRETCHER','name'=>'STRETCHER','active'=>1,'guid'=>'d93002b6-5717-4961-add3-3abdd794e8fe'),
				array('key'=>'WHEELCHAIR','name'=>'WHEELCHAIR','active'=>1,'guid'=>'8fe55247-5173-4da7-9b21-97c0f238daac'),
			);

			$pregnants = array(
				array('key'=>'YES','name'=>'Yes','active'=>1,'guid'=>'c1aeeaaf-5635-4631-a31e-4924f128a8db'),
				array('key'=>'NO','name'=>'No','active'=>1,'guid'=>'a8612443-d95e-4008-b7e3-50b6bd04ad22'),
				array('key'=>'UNKNOWN','name'=>'Unknown','active'=>1,'guid'=>'f4e93486-2210-4129-89bd-e8b4d9f9f4f3'),
			);

			$enums = array(
				array('key'=>OrderImaging::IMAGING_TYPES_ENUM_KEY,'name'=>OrderImaging::IMAGING_TYPES_ENUM_NAME,'active'=>1,'guid'=>'5572d265-c69b-4bba-9a15-b7bc45ae0ded','data'=>$types),
				array('key'=>OrderImaging::IMAGING_CATEGORIES_ENUM_KEY,'name'=>OrderImaging::IMAGING_CATEGORIES_ENUM_NAME,'active'=>1,'guid'=>'96fdbd0e-4eff-44de-a026-3fdda6580986','data'=>$categories),
				array('key'=>OrderImaging::IMAGING_URGENCIES_ENUM_KEY,'name'=>OrderImaging::IMAGING_URGENCIES_ENUM_NAME,'active'=>1,'guid'=>'88379ad5-af9c-44ce-a4b0-2182a3c288e0','data'=>$urgencies),
				array('key'=>OrderImaging::IMAGING_TRANSPORTS_ENUM_KEY,'name'=>OrderImaging::IMAGING_TRANSPORTS_ENUM_NAME,'active'=>1,'guid'=>'72da39bb-08f0-4ccd-bf2b-4185083f9077','data'=>$transports),
				array('key'=>OrderImaging::IMAGING_PREGNANTS_ENUM_KEY,'name'=>OrderImaging::IMAGING_PREGNANTS_ENUM_NAME,'active'=>1,'guid'=>'72da39bb-08f0-4ccd-bf2b-4185083f9077','data'=>$pregnants),
			);

			$level = array();
			$level['guid'] = 'e52c099c-0ceb-453f-899b-8549832928d5';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateLabTestPreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = OrderLabTest::LAB_ENUM_NAME;
			$key = OrderLabTest::LAB_ENUM_KEY;
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$types = array(
				//array('key'=>'','name'=>'','active'=>1,'guid'=>''),
				array('key'=>'1','name'=>'1,25-DIHYDROXYVIT D3','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'APPEARANCE
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'2','name'=>'1/2HR LTT','active'=>1,'guid'=>''),
				array('key'=>'3','name'=>'11-DEOXYCORTISOL','active'=>1,'guid'=>''),
				array('key'=>'4','name'=>'17-HYDROXYCORTICOSTEROIDS','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'SALMONELLA H ANTIGEN
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'5','name'=>'17-HYDROXYPROGESTERONE','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'SALMONELLA AGGLUTINATION
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'6','name'=>'17-KETOGENIC STEROIDS','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'TRICHINELLA AGGLUTINATION
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'7','name'=>'17-KETOSTEROIDS,TOTAL','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'TRYPSIN
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'8','name'=>'1HR LTT','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'RESP SYNCTIAL VIRUS TITER
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'9','name'=>'25 OH VITAMIN D','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'ALCOHOL PROFILE
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'10','name'=>'2HR LTT','active'=>1,'guid'=>''),
				array('key'=>'11','name'=>'3HR LTT','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'ROCKY MTN SPOTTED FV. TITER
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'12','name'=>'5\' NUCLEOTIDASE','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'BLOOD GASES
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),
				array('key'=>'13','name'=>'5HIAA	<URINE 5HIAA>','active'=>1,'guid'=>''),
				array('key'=>'14','name'=>'6Hr.GTT','active'=>1,'guid'=>''),
				array('key'=>'15','name'=>'6Hr.GTT (URINE)','active'=>1,'guid'=>''),
				array('key'=>'16','name'=>'A&N	<AMITRIPTYLINE & NORTRIPTYLINE>','active'=>1,'guid'=>'','data'=>array(
					array('key'=>'INFO','name'=>'AMOEBIC IHA ANTIBODY
Highest urgency allowed: ASAP','active'=>1,'guid'=>''),
				)),

				array('key'=>'17','name'=>'HCT','active'=>1,'guid'=>''),
				array('key'=>'18','name'=>'HEMOGLOBIN A1C','active'=>1,'guid'=>''),
				array('key'=>'19','name'=>'RETICULOCYTE','active'=>1,'guid'=>''),
				array('key'=>'20','name'=>'IRON STUDIES PROFILE','active'=>1,'guid'=>''),
				array('key'=>'21','name'=>'ACETAMINOPHEN','active'=>1,'guid'=>''),
				array('key'=>'22','name'=>'CARBAMAZEPINE','active'=>1,'guid'=>''),
				array('key'=>'23','name'=>'DIGOXIN','active'=>1,'guid'=>''),
				array('key'=>'24','name'=>'DILANTIN','active'=>1,'guid'=>''),
				array('key'=>'25','name'=>'LITHIUM','active'=>1,'guid'=>''),
				array('key'=>'26','name'=>'PHENOBARBITAL','active'=>1,'guid'=>''),
				array('key'=>'27','name'=>'QUINIDINE','active'=>1,'guid'=>''),
				array('key'=>'28','name'=>'THEOPHYLLINE','active'=>1,'guid'=>''),
				array('key'=>'29','name'=>'ZZVALPROIC ACID','active'=>1,'guid'=>''),
				array('key'=>'30','name'=>'CORTISOL','active'=>1,'guid'=>''),
				array('key'=>'31','name'=>'CHEM 7','active'=>1,'guid'=>''),
				array('key'=>'32','name'=>'CHEM 11','active'=>1,'guid'=>''),
				array('key'=>'33','name'=>'COMP. METABOLIC PROFILE','active'=>1,'guid'=>''),
				array('key'=>'34','name'=>'CARDIAC PROFILE','active'=>1,'guid'=>''),
				array('key'=>'35','name'=>'ZZHEPATIC FUNCTION PANEL','active'=>1,'guid'=>''),
				array('key'=>'36','name'=>'ALBUMIN','active'=>1,'guid'=>''),
				array('key'=>'37','name'=>'AMMONIA','active'=>1,'guid'=>''),
				array('key'=>'38','name'=>'LIPASE','active'=>1,'guid'=>''),
				array('key'=>'39','name'=>'MAGNESIUM','active'=>1,'guid'=>''),
				array('key'=>'40','name'=>'PO4','active'=>1,'guid'=>''),
				array('key'=>'41','name'=>'POTASSIUM','active'=>1,'guid'=>''),
				array('key'=>'42','name'=>'AMYLASE','active'=>1,'guid'=>''),
				array('key'=>'43','name'=>'HEPATITIS B CORE IGM AB','active'=>1,'guid'=>''),
				array('key'=>'44','name'=>'HEPATITIS B SURFACE ANTIGEN','active'=>1,'guid'=>''),
				array('key'=>'45','name'=>'HEPATITIS B SURFACE ANTIBODY','active'=>1,'guid'=>''),
				array('key'=>'46','name'=>'HEPATITIS C AB','active'=>1,'guid'=>''),
				array('key'=>'47','name'=>'RPR','active'=>1,'guid'=>''),
				array('key'=>'48','name'=>'ANA','active'=>1,'guid'=>''),
				array('key'=>'49','name'=>'ANTI MITOCHONDRIAL','active'=>1,'guid'=>''),
				array('key'=>'50','name'=>'COMPLEMENT C3','active'=>1,'guid'=>''),
				array('key'=>'51','name'=>'COMPLEMENT C4','active'=>1,'guid'=>''),
				array('key'=>'52','name'=>'RHEUMATOID FACTOR','active'=>1,'guid'=>''),
				array('key'=>'53','name'=>'ESR','active'=>1,'guid'=>''),
				array('key'=>'54','name'=>'CULTURE & SUSCEPTIBILITY','active'=>1,'guid'=>''),
			);

			$collectionSamples = array(
				array('key'=>'1','name'=>'ABSCESS','active'=>1,'guid'=>''),
				array('key'=>'2','name'=>'AMNIOCENTESIS','active'=>1,'guid'=>''),
				array('key'=>'3','name'=>'ANAEROBIC','active'=>1,'guid'=>''),
				array('key'=>'4','name'=>'ARTERIAL','active'=>1,'guid'=>''),
				array('key'=>'5','name'=>'ARTERIAL BLOOD','active'=>1,'guid'=>''),
				array('key'=>'6','name'=>'ASPIRATE','active'=>1,'guid'=>''),
				array('key'=>'7','name'=>'BACTEC BOTTLE','active'=>1,'guid'=>''),
				array('key'=>'8','name'=>'BIOPSY','active'=>1,'guid'=>''),
				array('key'=>'9','name'=>'BLOOD (GENERAL)','active'=>1,'guid'=>''),
				array('key'=>'10','name'=>'BLOOD (BLUE)','active'=>1,'guid'=>''),
				array('key'=>'11','name'=>'BLOOD (SPC BLUE2ML)','active'=>1,'guid'=>''),
				array('key'=>'12','name'=>'BLOOD (LAVENDER)','active'=>1,'guid'=>''),
				array('key'=>'13','name'=>'BLOOD (BLACK TOP)','active'=>1,'guid'=>''),
				array('key'=>'14','name'=>'BLOOD (MARBLED TOP)','active'=>1,'guid'=>''),
				array('key'=>'15','name'=>'BLOOD (GRAY)','active'=>1,'guid'=>''),
				array('key'=>'16','name'=>'BLOOD (LAVENDER)','active'=>1,'guid'=>''),
				array('key'=>'17','name'=>'BLOOD-GEN (GENERAL)','active'=>1,'guid'=>''),
				array('key'=>'18','name'=>'BLOOD-GRAY (GRAY)','active'=>1,'guid'=>''),
				array('key'=>'19','name'=>'BLUE-PL (BLUE)','active'=>1,'guid'=>''),
				array('key'=>'20','name'=>'BLUEX2PL (BLUE)','active'=>1,'guid'=>''),
				array('key'=>'21','name'=>'BONE','active'=>1,'guid'=>''),
				array('key'=>'22','name'=>'BONE MARROW','active'=>1,'guid'=>''),
				array('key'=>'23','name'=>'BOTH URINE & BLOOD','active'=>1,'guid'=>''),
				array('key'=>'24','name'=>'BRUSH','active'=>1,'guid'=>''),
				array('key'=>'25','name'=>'CELLOPHANE TAPE','active'=>1,'guid'=>''),
				array('key'=>'26','name'=>'CHANCRE','active'=>1,'guid'=>''),
				array('key'=>'27','name'=>'CSF','active'=>1,'guid'=>''),
				array('key'=>'28','name'=>'DECUBITUS','active'=>1,'guid'=>''),
				array('key'=>'29','name'=>'DIALYSATE','active'=>1,'guid'=>''),
				array('key'=>'30','name'=>'EXUDATE','active'=>1,'guid'=>''),
				array('key'=>'31','name'=>'FLUID','active'=>1,'guid'=>''),
				array('key'=>'32','name'=>'FLUID-PLE','active'=>1,'guid'=>''),
				array('key'=>'33','name'=>'FLUID-SYN','active'=>1,'guid'=>''),
				array('key'=>'34','name'=>'GENPROBE SWAB','active'=>1,'guid'=>''),
				array('key'=>'35','name'=>'GRAY-WB (GRAY)','active'=>1,'guid'=>''),
				array('key'=>'36','name'=>'GRAYX4PL (GRAY)','active'=>1,'guid'=>''),
				array('key'=>'37','name'=>'GREEN-PL (GREEN)','active'=>1,'guid'=>''),
				array('key'=>'38','name'=>'GREEN-WB (GREEN)','active'=>1,'guid'=>''),
				array('key'=>'39','name'=>'HAIR','active'=>1,'guid'=>''),
				array('key'=>'40','name'=>'KILLIT AMPULE','active'=>1,'guid'=>''),
				array('key'=>'41','name'=>'NAIL','active'=>1,'guid'=>''),
				array('key'=>'42','name'=>'NASOPHARYNGEAL','active'=>1,'guid'=>''),
				array('key'=>'43','name'=>'PERICARDIAL','active'=>1,'guid'=>''),
				array('key'=>'44','name'=>'PERITONEAL','active'=>1,'guid'=>''),
				array('key'=>'45','name'=>'PLACENTA','active'=>1,'guid'=>''),
				array('key'=>'46','name'=>'PLASMA (GREEN)','active'=>1,'guid'=>''),
				array('key'=>'47','name'=>'PLEURAL','active'=>1,'guid'=>''),
				array('key'=>'48','name'=>'PURP/TIG (PURP/TIG)','active'=>1,'guid'=>''),
				array('key'=>'49','name'=>'PURPLE-PL (PURPLE)','active'=>1,'guid'=>''),
				array('key'=>'50','name'=>'PURPLE-WB (PURPLE)','active'=>1,'guid'=>''),
				array('key'=>'51','name'=>'ROYAL-PL (ROYAL)','active'=>1,'guid'=>''),
				array('key'=>'52','name'=>'ROYAL-WB (ROYAL)','active'=>1,'guid'=>''),
				array('key'=>'53','name'=>'SECRETIONS','active'=>1,'guid'=>''),
				array('key'=>'54','name'=>'SEE-REF','active'=>1,'guid'=>''),
				array('key'=>'55','name'=>'SEMINAL FLUID','active'=>1,'guid'=>''),
				array('key'=>'56','name'=>'SER/CSF','active'=>1,'guid'=>''),
				array('key'=>'57','name'=>'SERUM (RED TOP)','active'=>1,'guid'=>''),
				array('key'=>'58','name'=>'SKIN','active'=>1,'guid'=>''),
				array('key'=>'59','name'=>'SKINTEST','active'=>1,'guid'=>''),
				array('key'=>'60','name'=>'SPORE STRIP','active'=>1,'guid'=>''),
				array('key'=>'61','name'=>'SPUTUM','active'=>1,'guid'=>''),
				array('key'=>'62','name'=>'STONE(CALCULUS)','active'=>1,'guid'=>''),
				array('key'=>'63','name'=>'STOOL','active'=>1,'guid'=>''),
				array('key'=>'64','name'=>'SUTURE','active'=>1,'guid'=>''),
				array('key'=>'65','name'=>'SWAB','active'=>1,'guid'=>''),
				array('key'=>'66','name'=>'SWAB-MINI','active'=>1,'guid'=>''),
				array('key'=>'67','name'=>'SYNOVIAL','active'=>1,'guid'=>''),
				array('key'=>'68','name'=>'THROAT SWAB (CULTURETTE)','active'=>1,'guid'=>''),
				array('key'=>'69','name'=>'TIGER (MARBLED TOP)','active'=>1,'guid'=>''),
				array('key'=>'70','name'=>'TISSUE','active'=>1,'guid'=>''),
				array('key'=>'71','name'=>'UNKNOWN','active'=>1,'guid'=>''),
				array('key'=>'72','name'=>'UR-24HR','active'=>1,'guid'=>''),
				array('key'=>'73','name'=>'URINE','active'=>1,'guid'=>''),
				array('key'=>'74','name'=>'URINE CATH','active'=>1,'guid'=>''),
				array('key'=>'75','name'=>'URINE MID-STREAM','active'=>1,'guid'=>''),
				array('key'=>'76','name'=>'UR-RANDOM','active'=>1,'guid'=>''),
				array('key'=>'77','name'=>'VAGINAL','active'=>1,'guid'=>''),
				array('key'=>'78','name'=>'VALVE','active'=>1,'guid'=>''),
				array('key'=>'79','name'=>'WATER','active'=>1,'guid'=>''),
				array('key'=>'80','name'=>'WOUND','active'=>1,'guid'=>''),
				array('key'=>'81','name'=>'YELLOW (ACD)','active'=>1,'guid'=>''),
			);

			$specimens = array(
				array('key'=>'BLOOD','name'=>'BLOOD','active'=>1,'guid'=>''),
				array('key'=>'BRONCHIAL ','name'=>'BRONCHIAL WASHING CYTOLOGIC MATERIAL','active'=>1,'guid'=>''),
				array('key'=>'LEFTLUNG','name'=>'LEFT UPPER LOBE OF LUNG','active'=>1,'guid'=>''),
				array('key'=>'PLASMA','name'=>'PLASMA','active'=>1,'guid'=>''),
				array('key'=>'SERUM','name'=>'SERUM','active'=>1,'guid'=>''),
				array('key'=>'SPUTUM','name'=>'SPUTUM','active'=>1,'guid'=>''),
				array('key'=>'PHARYNX','name'=>'PHARYNX','active'=>1,'guid'=>''),
				array('key'=>'URINE','name'=>'URINE','active'=>1,'guid'=>''),
				array('key'=>'OTHER','name'=>'Other','active'=>1,'guid'=>''),
			);

			$urgencies = array(
				array('key'=>'ASAP','name'=>'ASAP','active'=>1,'guid'=>''),
				array('key'=>'PRE-OP','name'=>'PRE-OP','active'=>1,'guid'=>''),
				array('key'=>'ROUTINE','name'=>'ROUTINE','active'=>1,'guid'=>''),
				array('key'=>'STAT','name'=>'STAT','active'=>1,'guid'=>''),
			);

			$collectionTypes = array(
				array('key'=>'LC','name'=>'Lab Collect','active'=>1,'guid'=>''),
				array('key'=>'WC','name'=>'Ward Collect','active'=>1,'guid'=>''),
				array('key'=>'SPL','name'=>'Send Patient to Lab','active'=>1,'guid'=>''),
				array('key'=>'IC','name'=>'Immediate Collect','active'=>1,'guid'=>''),
			);

			$schedules = array(
				array('key'=>'ONCE','name'=>'ONCE','active'=>1,'guid'=>''),
				array('key'=>'Q12','name'=>'Q12 HR','active'=>1,'guid'=>''),
				array('key'=>'Q2','name'=>'Q2 HR','active'=>1,'guid'=>''),
				array('key'=>'Q2D','name'=>'Q2D','active'=>1,'guid'=>''),
				array('key'=>'Q2W','name'=>'Q2W','active'=>1,'guid'=>''),
				array('key'=>'Q4','name'=>'Q4 HR','active'=>1,'guid'=>''),
				array('key'=>'Q6','name'=>'Q6 HR','active'=>1,'guid'=>''),
				array('key'=>'QAM','name'=>'QAM','active'=>1,'guid'=>''),
				array('key'=>'QM','name'=>'QM','active'=>1,'guid'=>''),
				array('key'=>'QW','name'=>'QW','active'=>1,'guid'=>''),
			);

			$enums = array(
				array('key'=>OrderLabTest::LAB_TYPES_ENUM_KEY,'name'=>OrderLabTest::LAB_TYPES_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$types),
				array('key'=>OrderLabTest::LAB_COLLECTION_SAMPLES_ENUM_KEY,'name'=>OrderLabTest::LAB_COLLECTION_SAMPLES_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$collectionSamples),
				array('key'=>OrderLabTest::LAB_SPECIMENS_ENUM_KEY,'name'=>OrderLabTest::LAB_SPECIMENS_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$specimens),
				array('key'=>OrderLabTest::LAB_URGENCIES_ENUM_KEY,'name'=>OrderLabTest::LAB_URGENCIES_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$urgencies),
				array('key'=>OrderLabTest::LAB_COLLECTION_TYPES_ENUM_KEY,'name'=>OrderLabTest::LAB_COLLECTION_TYPES_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$collectionTypes),
				array('key'=>OrderLabTest::LAB_SCHEDULES_ENUM_KEY,'name'=>OrderLabTest::LAB_SCHEDULES_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$schedules),
			);


			$level = array();
			$level['guid'] = '45db7e28-e5fe-4fac-b73f-d4da99b619f7';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateInsurancePreferencesEnum($force = false) {
		$ret = false;
		do {
			$name = InsuranceProgram::INSURANCE_ENUM_NAME;
			$key = InsuranceProgram::INSURANCE_ENUM_KEY;
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$assignings = array(
				array('key'=>'1','name'=>'A - Assigned','active'=>1,'guid'=>'0bb5b24d-34b9-467c-b37f-d8a3233b4266'),
				array('key'=>'2','name'=>'B - Assigned Lab Services Only','active'=>1,'guid'=>'7643b4a8-6f1d-41e5-a4e3-a24025e1763f'),
				array('key'=>'3','name'=>'C - Not Assigned','active'=>1,'guid'=>'7918f432-5282-4354-990a-17da4ca6a4a1'),
				array('key'=>'4','name'=>'P - Assignment Refused','active'=>1,'guid'=>'14d15452-7654-42b2-8bd4-e2559ca5a242'),
			);

			$subscribers = array(
				array('key'=>'01','name'=>'Spouse','active'=>1,'guid'=>'27ad996f-6ad7-4e41-8642-c4fcc7c49d87'),
				array('key'=>'04','name'=>'Grandfather or Grandmother','active'=>1,'guid'=>'48a413aa-4ff7-43a3-9bcd-8cb53364b8f4'),
				array('key'=>'05','name'=>'Grandson or Granddaughter','active'=>1,'guid'=>'1ad98eb4-cf49-4784-9b2e-c34155a69880'),
				array('key'=>'07','name'=>'Nephew or Niece','active'=>1,'guid'=>'00cf611e-37c7-4205-8ed1-8cbfd8ed95b0'),
				array('key'=>'10','name'=>'Foster Child','active'=>1,'guid'=>'785f6508-660a-4e6d-9e35-6ec77ea954c8'),
				array('key'=>'15','name'=>'Ward','active'=>1,'guid'=>'47b28ed5-81e3-4761-aea9-89e48b10f37e'),
				array('key'=>'17','name'=>'Stepson or Stepdaughter','active'=>1,'guid'=>'09102aff-1fed-4bed-828c-8eaa0226ac21'),
				array('key'=>'18','name'=>'Self','active'=>1,'guid'=>'94b7d031-78b4-4677-881b-5e1a3acd689d'),
				array('key'=>'19','name'=>'Child','active'=>1,'guid'=>'6d6410b1-a548-4b13-ae4b-795bf787c8a7'),
				array('key'=>'20','name'=>'Employee','active'=>1,'guid'=>'01010c49-dbdb-46be-b625-602030a03c6e'),
				array('key'=>'21','name'=>'Unknown','active'=>1,'guid'=>'7d5c2e9c-4ca0-4913-83bb-49a3d3bd0eb8'),
				array('key'=>'22','name'=>'Handicapped Dependent','active'=>1,'guid'=>'38035bec-a09e-4104-82bc-d2c90fa55547'),
				array('key'=>'23','name'=>'Sponsored Dependent','active'=>1,'guid'=>'342bd073-151e-4665-bdb6-98de534aba11'),
				array('key'=>'24','name'=>'Dependent of a Minor Dependent','active'=>1,'guid'=>'e341437c-f201-4188-b02a-02188e3aaf44'),
				array('key'=>'29','name'=>'Significant Other','active'=>1,'guid'=>'592e2259-b2c4-49dc-af0c-81c4d0460e62'),
				array('key'=>'32','name'=>'Mother','active'=>1,'guid'=>'37c456da-cac6-4347-bd34-0050a5dbc343'),
				array('key'=>'33','name'=>'Father','active'=>1,'guid'=>'7ec88635-09c9-4366-9909-87b5842550b9'),
				array('key'=>'36','name'=>'Emancipated Minor','active'=>1,'guid'=>'eb2920b4-692c-45d5-b018-3d476e51335b'),
				array('key'=>'39','name'=>'Organ Donor','active'=>1,'guid'=>'db322795-58f6-4338-921d-5b1fb5807d97'),
				array('key'=>'40','name'=>'Cadaver Donor','active'=>1,'guid'=>'4d76c60d-7d8a-47b2-b657-209669ec9a1e'),
				array('key'=>'41','name'=>'Injured Plaintiff','active'=>1,'guid'=>'b35a8c57-46a3-4c39-899c-ce906c9ea6c4'),
				array('key'=>'43','name'=>'Child Where Insured Has No Financial Responsibility','active'=>1,'guid'=>'543b281b-b8f4-4f19-901b-c8a09cee3df8'),
				array('key'=>'53','name'=>'Life Partner','active'=>1,'guid'=>'cc5f5df0-0c9a-4980-b242-677d7728b12c'),
				array('key'=>'G8','name'=>'Other Relationship','active'=>1,'guid'=>'8fe9f8fd-9f68-408b-bfaa-58df5b2ead5e'),
			);

			$payerTypes = array(
				array('key'=>'1','name'=>'medicare','active'=>1,'guid'=>'d7a332c5-c214-40ce-b44e-e47f6901fdf8'),
				array('key'=>'2','name'=>'champus','active'=>1,'guid'=>'006b9344-04c5-4b8e-8e59-df8b8c2a043c'),
				array('key'=>'3','name'=>'medical','active'=>1,'guid'=>'e3bf8fcc-418f-40f2-b37d-e7e083d4cc64'),
				array('key'=>'4','name'=>'private pay','active'=>1,'guid'=>'2ae1fc4e-f0db-457a-a215-267a4e1cc292'),
				array('key'=>'5','name'=>'feca','active'=>1,'guid'=>'65b89fa8-db6a-49fe-bde8-0ba86cb4058c'),
				array('key'=>'6','name'=>'medicaid','active'=>1,'guid'=>'dee85d96-6aab-461c-8fcc-4a3c0f242019'),
				array('key'=>'7','name'=>'champusva','active'=>1,'guid'=>'5bc77ec2-865f-4f32-ab3f-31fc17f68bbb'),
				array('key'=>'8','name'=>'otherhcfa','active'=>1,'guid'=>'17f044eb-947d-477d-a210-f1c65aed4fbe'),
				array('key'=>'9','name'=>'litigation','active'=>1,'guid'=>'220a25a1-0abe-4741-9ce0-f0d3ea794412'),
				array('key'=>'10','name'=>'private insurance','active'=>1,'guid'=>'f4ebd23a-3317-4d79-837b-07a241c8e08d'),
				array('key'=>'11','name'=>'MPC','active'=>1,'guid'=>'ef749c0d-3289-4d85-883a-00586427da18'),
				array('key'=>'12','name'=>'PCMI','active'=>1,'guid'=>'0571717d-367a-4a28-849f-edb9c25a2f19'),
				array('key'=>'13','name'=>'DCHCA','active'=>1,'guid'=>'2609a4ca-aa81-4bee-bb52-c04edba30ba8'),
				array('key'=>'14','name'=>'MCCP','active'=>1,'guid'=>'17607fb0-e7cf-4799-a747-15d75fe24efc'),
				array('key'=>'15','name'=>'CFK','active'=>1,'guid'=>'82794349-2026-466f-ba6d-b788b0cbe3af'),
				array('key'=>'16','name'=>'None','active'=>1,'guid'=>'3556a6c5-2a48-4ce7-a1d1-1914c938b518'),
			);

			$programTypes = array(
				//array('key'=>'','name'=>'','active'=>1,'guid'=>''),
			);

			$fundsSources = array(
				array('key'=>'1','name'=>'Patient','active'=>1,'guid'=>'93a98fb9-d3b7-4960-9f9e-c77232625a60'),
				array('key'=>'2','name'=>'Private Insurance','active'=>1,'guid'=>'760a12fc-2519-49d7-abbb-fb5c7eb702e7'),
				array('key'=>'3','name'=>'State Program','active'=>1,'guid'=>'4052ea1a-5c75-44dd-8197-d82d8b9d04e2'),
				array('key'=>'4','name'=>'Federal Program','active'=>1,'guid'=>'7c58cf4d-3acb-4a1c-8f1e-eaf1b490bc05'),
			);

			$enums = array(
				array('key'=>InsuranceProgram::INSURANCE_ASSIGNING_ENUM_KEY,'name'=>InsuranceProgram::INSURANCE_ASSIGNING_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$assignings),
				array('key'=>InsuranceProgram::INSURANCE_SUBSCRIBER_ENUM_KEY,'name'=>InsuranceProgram::INSURANCE_SUBSCRIBER_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$subscribers),
				array('key'=>InsuranceProgram::INSURANCE_PAYER_TYPE_ENUM_KEY,'name'=>InsuranceProgram::INSURANCE_PAYER_TYPE_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$payerTypes),
				array('key'=>InsuranceProgram::INSURANCE_PROGRAM_TYPE_ENUM_KEY,'name'=>InsuranceProgram::INSURANCE_PROGRAM_TYPE_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$programTypes),
				array('key'=>InsuranceProgram::INSURANCE_FUNDS_SOURCE_ENUM_KEY,'name'=>InsuranceProgram::INSURANCE_FUNDS_SOURCE_ENUM_NAME,'active'=>1,'guid'=>'','data'=>$fundsSources),
			);

			$level = array();
			$level['guid'] = '5d7c5ecc-9e64-4e9d-8c60-a55269b7bcf0';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateGenericNoteTemplateEnum($force = false) {
		$ret = false;
		do {
			$name = 'Generic Note Template';
			$key = 'GENOTEMPLT';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$level = array();
			$level['guid'] = '7bd7c051-3552-43ba-bd1d-f6c24c5f598c';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateTextOnlyTypesEnum($force = false) {
		$ret = false;
		do {
			$name = Order::TEXT_ONLY_TYPE_ENUM_NAME;
			$key = Order::TEXT_ONLY_TYPE_ENUM_KEY;
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key'=>'MISC','name'=>'Miscellaneous','active'=>1,'guid'=>'8ca20f67-7f0a-4786-9a63-6bc20451c775'),
				array('key'=>'DIETARY','name'=>'Dietary Consult','active'=>1,'guid'=>'871065e6-13ac-4cfd-b13b-a4549281dbc8'),
			);

			$level = array();
			$level['guid'] = 'd0ba0f8d-3697-4fcb-a551-289ce0638022';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateEobAdjustmentTypesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Eob Adjustment Types';
			$key = 'EOB_TYPES';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key'=>'1','name'=>'Deductible Amount','active'=>1,'guid'=>'5ee64e37-ac79-4a4c-b3a8-f9c0db9423af'),
				array('key'=>'2','name'=>'Coinsurance Amount','active'=>1,'guid'=>'0976b636-1862-4b73-8318-3d4b6f7d03d2'),
				array('key'=>'3','name'=>'Co-payment Amount','active'=>1,'guid'=>'05362c6e-b751-41da-b8d1-97d680e46b9e'),
				array('key'=>'4','name'=>'The procedure code is inconsistent with the modifier used or a required modifier is missing.','active'=>1,'guid'=>'f98204c4-3d26-419d-826c-20a0ca14f855'),
				array('key'=>'5','name'=>'The procedure code/bill type is inconsistent with the place of service.','active'=>1,'guid'=>'5449a251-990b-488c-9798-f2510d2ff791'),
				array('key'=>'6','name'=>'The procedure/revenue code is inconsistent with the patient\'s age.','active'=>1,'guid'=>'047972e3-8b7d-4be1-a327-bd7193b15125'),
				array('key'=>'7','name'=>'The procedure/revenue code is inconsistent with the patient\'s gender.','active'=>1,'guid'=>'baa58c20-f297-4180-a25a-7e0d141fd9ae'),
				array('key'=>'8','name'=>'The procedure code is inconsistent with the provider type/specialty (taxonomy).','active'=>1,'guid'=>'7a4b49f3-2cf7-413f-b6f3-f8d886635d18'),
				array('key'=>'9','name'=>'The diagnosis is inconsistent with the patient\'s age.','active'=>1,'guid'=>'97589107-b06b-4925-ae92-dcadd89434a2'),
				array('key'=>'10','name'=>'The diagnosis is inconsistent with the patient\'s gender.','active'=>1,'guid'=>'afbdda44-532a-4e8b-be21-b59a1037276d'),
				array('key'=>'11','name'=>'The diagnosis is inconsistent with the procedure.','active'=>1,'guid'=>'e96acde7-cd31-4373-b5cc-5f20c79069af'),
				array('key'=>'12','name'=>'The diagnosis is inconsistent with the provider type.','active'=>1,'guid'=>'e8ac5131-4de3-4ff5-9709-b0fa80a79164'),
				array('key'=>'13','name'=>'The date of death precedes the date of service.','active'=>1,'guid'=>'ca68e470-5e6e-4ac1-8543-4cd9b0a9d341'),
				array('key'=>'14','name'=>'The date of birth follows the date of service.','active'=>1,'guid'=>'6dc954d4-1b93-45cd-9b76-cb48aa7e78c2'),
				array('key'=>'15','name'=>'Payment adjusted because the submitted authorization number is missing','active'=>1,'guid'=>'69325efb-aa64-4c8e-baf6-3f04e4fbb481'),
				array('key'=>'16','name'=>'Claim/service lacks information which is needed for adjudication. Additional information is supplied using remittance advice remarks codes whenever appropriate','active'=>1,'guid'=>'80a6a101-e4b6-486d-928b-791bd41d95b9'),
				array('key'=>'17','name'=>'Payment adjusted because requested information was not provided or was insufficient/incomplete. Additional information is supplied using the remittance advice remarks codes whenever appropriate.','active'=>1,'guid'=>'20487461-58e7-41ba-b82a-3c15803770ae'),
				array('key'=>'18','name'=>'Duplicate claim/service.','active'=>1,'guid'=>'be183a0e-3672-4b34-a25c-5cdf1d1ea969'),
				array('key'=>'19','name'=>'Claim denied because this is a work-related injury/illness and thus the liability of the Worker\'s Compensation Carrier.','active'=>1,'guid'=>'ec7fdd65-925c-4678-a848-9af4584eee4a'),
				array('key'=>'20','name'=>'Claim denied because this injury/illness is covered by the liability carrier.','active'=>1,'guid'=>'81461964-18a0-436a-9a46-5758d12a0b8c'),
				array('key'=>'21','name'=>'Claim denied because this injury/illness is the liability of the no-fault carrier.','active'=>1,'guid'=>'3588fb7e-564c-4209-960e-4a7e36ea18c9'),
				array('key'=>'22','name'=>'Payment adjusted because this care may be covered by another payer per coordination of benefits.','active'=>1,'guid'=>'59a023e4-c7cd-4b46-a68d-28f9ceb163e7'),
				array('key'=>'23','name'=>'Payment adjusted due to the impact of prior payer(s) adjudication including payments and/or adjustments','active'=>1,'guid'=>'edc7f6fd-5ac3-4f2a-8e64-97d2d798a062'),
				array('key'=>'24','name'=>'Payment for charges adjusted. Charges are covered under a capitation agreement/managed care plan.','active'=>1,'guid'=>'ce59e907-2bfa-4e76-98e6-3aa05cd21430'),
				array('key'=>'25','name'=>'Payment denied. Your Stop loss deductible has not been met.','active'=>1,'guid'=>'0e6421c8-bd6e-4885-87a4-35333ee6d1bc'),
				array('key'=>'26','name'=>'Expenses incurred prior to coverage.','active'=>1,'guid'=>'c4edbc3a-1763-4c72-bd0c-4afd5ddd904e'),
				array('key'=>'27','name'=>'Expenses incurred after coverage terminated.','active'=>1,'guid'=>'c9acece8-7d07-48f2-b5ba-71a648d96c91'),
				array('key'=>'28','name'=>'Coverage not in effect at the time the service was provided.','active'=>1,'guid'=>'aaf0ca09-3187-486e-9a8e-1fe9bdf8b74d'),
				array('key'=>'29','name'=>'The time limit for filing has expired.','active'=>1,'guid'=>'9003d091-9eeb-494b-94f0-a570557da7a8'),
				array('key'=>'30','name'=>'Payment adjusted because the patient has not met the required eligibilit','active'=>1,'guid'=>'9176c429-0faf-41f1-9da4-3b1808c60e2e'),
				array('key'=>'31','name'=>'Claim denied as patient cannot be identified as our insured.','active'=>1,'guid'=>'ad3eea48-683b-47e9-87f4-5046a102fbfc'),
			);

			$level = array();
			$level['guid'] = '93f9b91c-9911-4038-a3dc-69522d5816f6';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	public static function generateChargeTypesEnum($force = false) {
		$ret = false;
		do {
			$name = 'Charge Types';
			$key = 'CHG_TYPES';
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName($name);
			// check for key existence
			if (strlen($enumeration->key) > 0 && $enumeration->key == $key) {
				if (!$force) {
					break;
				}
				$enumerationClosure = new EnumerationsClosure();
				$enumerationClosure->deleteEnumeration($enumeration->enumerationId);
			}

			$enums = array(
				array('key' => 'CORRCHARGE', 'name' => 'Correction Charge', 'active' => 1, 'guid' => '46c4a502-01d5-4de6-93fa-443d53b7b33d'),
				array('key' => 'LABCHARGE', 'name' => 'Labs Charge', 'active' => 1, 'guid' => '427b5de7-8e77-4019-8152-09d7edbe63c6'),
				array('key' => 'MEDCHARGE', 'name' => 'Medication Charge', 'active' => 1, 'guid' => '1a0b97d2-5a8f-471b-bb90-2ec2d1aacc7b'),
				array('key' => 'VISITCHG', 'name' => 'Visit Charge', 'active' => 1, 'guid' => 'b73106f0-fbd4-435f-94f3-2e2e02293aae'),
				array('key' => 'OTHER', 'name' => 'Other', 'active' => 1, 'guid' => 'b309495a-8f4c-4eb0-b558-a81cf79acf13'),
			);

			$level = array();
			$level['guid'] = '5ca94c8c-5198-4461-957b-fa568be9c79e';
			$level['key'] = $key;
			$level['name'] = $name;
			$level['category'] = 'System';
			$level['active'] = 1;
			$level['data'] = $enums;

			$data = array($level);

			self::_saveEnumeration($data);
			$ret = true;
		} while(false);
		return $ret;
	}

	protected static function _saveEnumeration($data,$parentId=0) {
		$enumerationsClosure = new EnumerationsClosure();
		foreach ($data as $item) {
			$item['key'] = strtoupper($item['key']); // make sure keys are all UPPERCASE
			$enumerationId = $enumerationsClosure->insertEnumeration($item,$parentId);
			if (isset($item['data'])) {
				self::_saveEnumeration($item['data'],$enumerationId);
			}
		}
	}

	public static function enumerationToJson($name) {
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		return $enumerationIterator->toJsonArray('enumerationId',array('name'));
	}

}
