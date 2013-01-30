<?php
/*****************************************************************************
*       PQRIRegistryXML.php
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


class PQRIRegistryXML {

	protected $measureNumber = 0;
	protected $denominator = 0;
	protected $numerator = 0;
	protected $exclusions = 0;
	protected $notMeet = 0;
	protected $percentage = 0;
	protected $visitDateStart;
	protected $visitDateEnd;
	protected $provider = null;
	protected $fileNumber = 1;
	protected $numberOfFiles = 1;

	public function __construct(Array $data) {
		$this->measureNumber = isset($data['measureNumber'])?(int)$data['measureNumber']:0;
		$this->denominator = isset($data['denominator'])?(int)$data['denominator']:0;
		$this->numerator = isset($data['numerator'])?(int)$data['numerator']:0;
		$this->exclusions = isset($data['exclusions'])?(int)$data['exclusions']:0;
		$this->percentage = isset($data['percentage'])?sprintf('%.2f',$data['percentage']):0;
		if (isset($data['visitDateStart'])) {
			$this->visitDateStart = date('m-d-Y',strtotime($data['visitDateStart']));
		}
		else {
			$this->visitDateStart = date('01-01-Y');
		}
		if (isset($data['visitDateEnd'])) {
			$this->visitDateEnd = date('m-d-Y',strtotime($data['visitDateEnd']));
		}
		else {
			$this->visitDateEnd = date('12-31-Y');
		}
		$provider = isset($data['provider'])?$data['provider']:'';
		if (!$data['provider'] instanceof Provider) {
			$providerId = (int)$provider;
			$provider = new Provider();
			$provider->personId = $providerId;
			$provider->populate();
		}
		$this->provider = $provider;
		if (isset($data['fileNumber'])) $this->fileNumber = (int)$data['fileNumber'];
		if (isset($data['numberOfFiles'])) $this->numberOfFiles = (int)$data['numberOfFiles'];
	}

	public function generate() {
		/* XML Element: <submission>
		 * Description: Describes the setting for which data is being submitted
		 * Valid Values: PQRI-REGISTRY
		 * Data Type: char(20)
		 * Required: yes
		 */
		$type = 'PQRI-REGISTRY';

		/* XML Element: <submission>
		 * Description: not exists on 2010, Describes the registry option to be used (Use the value PAYMENT to repesent PRODUCTION files and TEST to represent TEST files whose data will NOT be stored in Data Warehouse)
		 * Valid Values: PAYMENT,TEST
		 * Data Type: char(20)
		 * Required: yes
		 */
		$option = 'PAYMENT';

		/* XML Element: <submission>
		 * Description: 2.0 = 2009, 3.0 = 2010, The version of the file layout
		 * Valid Values: 2.0
		 * Data Type: char(20)
		 * Required: yes
		 */
		$version = '2.0';

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><submission type="'.$type.'" option="'.$option.'" version="'.$version.'" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="Registry_Payment.xsd" />');
		$this->generateFileAuditData($xml);
		$this->generateRegistry($xml);
		$this->generateMeasureGroup($xml);

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->loadXML($xml->asXML());
		return $doc->saveXML();
	}

	protected function generateFileAuditData(SimpleXMLElement $xml) {
		$fileAuditData = $xml->addChild('file-audit-data');

		/* XML Element: <create-date>
		 * Description: The month, day, and year the XML file was created.
		 * Valid Values: MM-DD-YYYY (Must be a valid date)
		 * Data Type: date(10)
		 * Required: yes
		 */
		$fileAuditData->addChild('create-date',date('m-d-Y'));

		/* XML Element: <create-time>
		 * Description: The hour and minutes representing the time the file was created.
		 * Valid Values: HH:MM (Military format with or without colon)
		 * Data Type: time(5)
		 * Required: yes
		 */
		$fileAuditData->addChild('create-time',date('H:i'));

		/* XML Element: <create-by>
		 * Description: The entity who created the file.
		 * Data Type: char(50)
		 * Required: yes
		 */
		$fileAuditData->addChild('create-by',$this->provider->displayName);

		/* XML Element: <version>
		 * Description: The version of the file being submitted.
		 * Data Type: char(20)
		 * Required: yes
		 */
		$fileAuditData->addChild('version','1.0');

		/* XML Element: <file-number>
		 * Description: The number of the file.
		 * Data Type: num(5)
		 * Required: yes
		 */
		$fileAuditData->addChild('file-number',$this->fileNumber);

		/* XML Element: <number-of-files>
		 * Description: Total number of files.
		 * Data Type: num(5)
		 * Required: yes
		 */
		$fileAuditData->addChild('number-of-files',$this->numberOfFiles);
		return $fileAuditData;
	}

	protected function generateRegistry(SimpleXMLElement $xml) {
		$registry = $xml->addChild('registry');
		$user = new User();
		$user->personId = (int)$this->provider->personId;
		$user->populateWithPersonId();
		$building = new Building();
		$building->buildingId = $user->defaultBuildingId;
		$building->populate();
		$practice = $building->practice;

		/* XML Element: <registry-name>
		 * Description: The registry name.
		 * Data Element: Registry Name
		 * Valid Values: Registry Name
		 * Data Type: char(100)
		 * Required: yes
		 */
		$registry->addChild('registry-name',$practice->name);

		/* XML Element: <registry-id>
		 * Description: Used to identify the registry. Use Registry's Corporate TIN number.
		 * Data Element: Registry ID
		 * Data Type: char(9)
		 * Required: yes
		 */
		$registry->addChild('registry-id',$practice->identifier);

		/* XML Element: <submission-method>
		 * Description: Submission Method: (A-F)
		 * 		A = 12 months, 80%, 3 or more measures
		 * 		B = 6 months, 80%, 3 or more measures
		 * 		C = 12 months, 30 consecutive, measure group
		 * 		E = 12 months, 80%, measure group
		 * 		F = 6 months, 80%, measure group
		 * 		2010
		 * 		A = 12 months, 80%, 3 or more measures
		 * 		B = 6 months, 80%, 3 or more measures
		 * 		G = 12 months, 30 patients, measure group
		 * 		H = 12 months, 80%, min. of 15 patients, measure group
		 * 		I = 6 months, 80%, min. of 8 patients, measure group
		 * 		Note: Limit one xml file to a single submission method
		 * Data Element: Submission Method
		 * Valid Values: A,B,C,E,F
		 * 		 (12 month reporting period = January 1, 2009 through December 31, 2009; 6 month reporting period = July 1, 2009 through December 31, 2009)
		 * 		2010
		 * 		 A,B,G,H,I
		 * 		 (12 month reporting period = January 1, 2010 through December 31, 2010; 6 month reporting period = July 1, 2010 through December 31, 2010)
		 * Data Type: char(1)
		 * Required: yes
		 */
		$registry->addChild('submission-method','A');
		return $registry;
	}

	protected function generateMeasureGroup(SimpleXMLElement $xml) {
		$measureGroup = $xml->addChild('measure-group');
		$measureGroupId = 'C';

		/* XML Element: <measure-group>
		 * Description: ID of the measure group.
		 * 		A = Diabetes Mellitis
		 * 		C = CKD (Chronic Kidney Disease)
		 * 		D = Preventive Care
		 * 		E = Perioperative Care
		 * 		F = Rheumatoid Arthritis
		 * 		G = Back Pain
		 * 		H = CABG (Coronary Artery Bypass Graft)
		 * 		X = Not Applicable
		 * 		2010 additions
		 * 		I = Hepatitis C
		 * 		J = IVD
		 * 		K = CAP
		 * 		L = HF
		 * 		M = CAD
		 * 		N = HIV/AIDS
		 * 		Note: If the submission-method is 'A' or 'B', then the 'ID' attribute of measure-group should be 'X'
		 * Valid Values: A,C,D,E,F,G,H,I,J,K,L,M,N,X
		 * Data Type: char(1)
		 * Required: yes
		 */
		$submissionMethod = $xml->registry->{'submission-method'};
		if ($submissionMethod == 'A' || $submissionMethod == 'B') {
			$measureGroupId = 'X';
		}
		$measureGroup->addAttribute('ID',$measureGroupId);

		$this->generateProvider($measureGroup,$measureGroupId);
		return $measureGroup;
	}

	protected function generateProvider(SimpleXMLElement $xml,$measureGroupId) {
		$provider = $xml->addChild('provider');

		/* XML Element: <npi>
		 * Description: National Provider Identifier as assigned by CMS
		 * Data Element: National Provider Identifier (NPI)
		 * Valid Values: 10 digit NPI Number
		 * Data Type: char(10)
		 * Required: yes
		 */
		$npi = ($this->provider->person->identifierType == 'NPI')?$this->provider->person->identifier:'';
		$provider->addChild('npi',$npi);

		/* XML Element: <tin>
		 * Description: The tax identification number for specific NPI.
		 * Data Element: Tax Identification Number (TIN)
		 * Valid Values: 9 digit Tax Identification Number
		 * Data Type: char(9)
		 * Required: yes
		 */
		$provider->addChild('tin',$this->provider->TIN);

		/* XML Element: <waiver-signed>
		 * Description: Participation waiver signed? A participation waiver indicates the eligible professional has given the registry permission to submit data on their behalf.
		 * Data Element: Waiver Signed
		 * Valid Values: Y,y
		 * Data Type: char(1)
		 * Required: yes
		 */
		$provider->addChild('waiver-signed','Y');

		/* XML Element: <encounter-from-date>
		 * Description: The month, day, and year of the first service encounter of the submission period ("From" date).
		 * Valid Values: MM-DD-YYYY (Must be a valid date)
		 * Data Type: date(10)
		 * Required: yes
		 */
		$provider->addChild('encounter-from-date',$this->visitDateStart);

		/* XML Element: <encounter-to-date>
		 * Description: The month, day, and year of the last service encounter of the submission period ("To" date).
		 * Valid Values: MM-DD-YYYY (Must be a valid date)
		 * Data Type: date(10)
		 * Required: yes
		 */
		$provider->addChild('encounter-to-date',$this->visitDateEnd);

		if ($measureGroupId != 'X') { // Note: If the measure-group 'ID' is 'X', do not include the <measure-group-stat> segment.
			$this->generateMeasureGroupStat($provider);
		}
		$this->generatePQRIMeasure($provider);
		return $provider;
	}

	protected function generateMeasureGroupStat(SimpleXMLElement $xml) {
		$measureGroupStat = $xml->addChild('measure-group-stat');

		/* XML Element: <ffs-patient-count>
		 * Description: Total number of Medicare Part B FFS patients seen for the PQRI measure group.
		 * Data Element: Patient count for the PQRI Measure Group
		 * Data Type: num(10)
		 * Required: Conditional; only required if measure-group 'ID' value is not 'X'
		 */
		$measureGroupStat->addChild('ffs-patient-count',2);

		/* XML Element: <group-reporting-rate-numerator>
		 * Description: Number of instances of reporting for all applicable measures within the measure group, for each eligible instance (reporting rate numerator).
		 * Data Element: Number Instances of Quality Service Performed per Eligible Instance
		 * Valid Values: Refer to PQRI Measure Group Specifications
		 * Data Type: num(10)
		 * Required: Conditional; only required if measure-group 'ID' value is not 'X'
		 */
		$measureGroupStat->addChild('group-reporting-rate-numerator','20');

		/* XML Element: <group-eligible-instances>
		 * Description: Number of eligible instances (reporting denominator) for the PQRI measure group.
		 * Data Element: Eligible instances for the PQRI Measure Group
		 * Valid Values: Refer to PQRI Measure Group Specifications
		 * Data Type: num(10)
		 * Required: Conditional; only required if measure-group 'ID' value is not 'X'
		 */
		$measureGroupStat->addChild('group-eligible-instances','30');

		/* XML Element: <group-reporting-rate>
		 * Description: Percentage of reporting (Reporting Rate Numerator/Reporting Denominator).
		 * Data Element: Reporting Rate
		 * Valid Values: 0.00-100.00
		 * Data Type: num(6)
		 * Required: Conditional; only required if measure-group 'ID' value is not 'X'
		 */
		$measureGroupStat->addChild('group-reporting-rate','100.00');
		return $measureGroupStat;
	}

	protected function generatePQRIMeasure(SimpleXMLElement $xml) {
		$pqriMeasure = $xml->addChild('pqri-measure');

		/* XML Element: <pqri-measure-number>
		 * Description: The PQRI measure number.
		 * Data Element: PQRI Measure Number
		 * Valid Values: Refer to PQRI Measure Specifications
		 * Data Type: num(3)
		 * Required: yes
		 */
		$pqriMeasure->addChild('pqri-measure-number',$this->measureNumber);

		// Available on 2010
		/* XML Element: <collection-method>
		 * Description: Method Registry collected data from Eligible Professionals.
		 * Data Element: Collection Method
		 * Valid Values: A = EHR
		 * 		 B = Claims
		 * 		 C = Practice Mgmt System
		 * 		 D = Web Based Tool
		 * Data Type: char(4)
		 * Required: yes
		 */
		$pqriMeasure->addChild('collection-method','A');

		/* XML Element: <eligible-instances>
		 * Description: Number of eligible instances (reporting denominator) for the PQRI measure.
		 * Data Element: Eligible instances for the PQRI Measure (2010: Eligible instances for the individual PQRI Measure)
		 * Valid Values: Refer to PQRI Measure Specifications
		 * Data Type: num(10)
		 * Required: yes
		 */
		$pqriMeasure->addChild('eligible-instances',$this->denominator);

		/* XML Element: <meets-performance-instances>
		 * Description: Number of instances of quality service performed (performance numerator).
		 * Data Element: Number Instances of Quality Service Performed
		 * Valid Values: Refer to PQRI Measure Specifications
		 * Data Type: num(10)
		 * Required: yes
		 */
		$pqriMeasure->addChild('meets-performance-instances',$this->numerator);

		/* XML Element: <performance-exclusion-instances>
		 * Description: Number of performance exclusions for the PQRI Measure
		 * Data Element: Performance Exclusions
		 * Valid Values: Refer to PQRI Measure Specifications
		 * Data Type: num(10)
		 * Required: yes
		 */
		$pqriMeasure->addChild('performance-exclusion-instances',$this->exclusions);

		/* XML Element: <performance-not-met-instances>
		 * Description: Number of instances which do not meet the performance criteria, even though reporting occurred.
		 * Data Element: Performance Not Met Instances
		 * Valid Values: Refer to PQRI Measure Specifications
		 * Data Type: num(10)
		 * Required: yes
		 */
		$pqriMeasure->addChild('performance-not-met-instances',$this->notMeet);

		/* XML Element: <reporting-rate>
		 * Description: Percentage of reporting (Performance Numerator + Performance Exclusions + Performance Not Met/Reporting Denominator).
		 * 		2010: Note: When the reporting-rate value is null use <reporting-rate xsi:nil="true"/> for this tag.
		 * Data Element: Reporting Rate
		 * Valid Values: 0.00-100.00
		 * Data Type: num(6)
		 * Required: yes (2010: Conditional; only required if measure- group 'ID' value is not 'X')
		 */
		$reportingRate = ($this->denominator > 0)?(($this->numerator + $this->exclusions + $this->notMeet) / $this->denominator) * 100:0;
		$pqriMeasure->addChild('reporting-rate',sprintf('%.2f',$reportingRate));

		/* XML Element: <performance-rate>
		 * Description: Percentage of performance (Performance Numerator/Reporting Numerator-Performance Exclusions).
		 * 		Note: When the performance-rate value is null use <performance-rate xsi:nil="true"/> for this tag.
		 * Data Element: Performance Rate
		 * Valid Values: 0.00-100.00
		 * Data Type: num(6)
		 * Required: Conditional; only required if performance-rate is not a null value
		 */
		$pqriMeasure->addChild('performance-rate',$this->percentage);
		return $pqriMeasure;
	}

}
/*$pqri = new PQRIRegistryXML();
$xml = $pqri->generate();
$doc = new DOMDocument();
$doc->formatOutput = true;
$doc->loadXML($xml->asXML());
echo $doc->saveXML();*/
