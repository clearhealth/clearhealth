<?php
/*****************************************************************************
*       HandleLDB.php
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


/*
 * This class is currently used by daemons/handlerSMTP.php and MUST be invoked in CLI
 */

$error = '';
if (PHP_SAPI != 'cli') {
	$error = 'This MUST be called using PHP CLI';
	echo $error;
	file_put_contents('/tmp/ldb.log',"\n$error",FILE_APPEND);
	trigger_error($error,E_USER_NOTICE);
	die;
}


// error handler function
function HandleErrorHandler($errNo,$errStr,$errFile,$errLine) {
	$error = "[$errNo] $errStr [$errFile : $errLine]\n";
	file_put_contents('/tmp/ldb.log',"\n$error",FILE_APPEND);
	switch ($errNo) {
		case E_USER_ERROR:
			exit(1);
			break;
		case E_USER_WARNING:
		case E_USER_NOTICE:
	}

	/* Don't execute PHP internal error handler */
	return true;
}

$oldHandler = set_error_handler('HandleErrorHandler');
if (strlen($oldHandler) > 0) {
	set_error_handler($oldHandler);
}


define('APPLICATION_ENVIRONMENT','production');

class HandleLDB {

	protected static $_instance = null;
	protected $_paths = array();
	protected $messageFilename = '';

	public static function getInstance() {
        	if (null === self::$_instance) {
        		self::$_instance = new self();
			self::$_instance->init();
        	}
		return self::$_instance;
	}

	public function process($request) {
		if (preg_match('/QRD\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)\|(.*)/',$request,$matches) && isset($matches[7])) {
			$mrn = (int)$matches[7];
		}
		else {
			return $request; // temporarily return its own request
		}

		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patient')
				->joinUsing('person','person_id')
				->where('record_number = ?',$mrn);
		$ret = false;
		if ($patient = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}

		$message = 'MSH|^~\&|Doc Data|Elk Software|CARE360|ClearHealth|'.date('YmdHi').'+0000^S||ADT^A19|ControlId|T|2.3';
		$msa = array('MSA');
		for ($i = 1; $i <= 7; $i++) {
			$msa[$i] = '';
		}
		$msa[1] = 'AA';
		$msa[2] = '0001';
		$msa[6] = '0';
		if (!$ret) {
			// If no match is found for supplied record_number then return only MSH and MSA line, in MSA field 1 (acknowledgement code put the 2nd column, in MSA message field put 1st column data below):
			$msa[1] = 'AE';
			$msa[6] = '1';
			$msa[7] = 'No match found for search criteria';
			$message .= "\n".implode('|',$msa);
			file_put_contents('/tmp/ldb.log',"\n$message",FILE_APPEND);
			return $message;
		}
		$patient['person_id'] = (int)$patient['person_id'];

		$message .= "\n".implode('|',$msa);
		$qrd = array('QRD');
		for ($i = 1; $i <= 12; $i++) {
			$qrd[$i] = $matches[$i];
		}
		$qrd[1] = date('YmdHi').'+0000^S'; // Query Date/Time
		$message .= "\n".implode('|',$qrd);

		$pid = array('PID');
		for ($i = 1; $i <= 19; $i++) {
			$pid[$i] = '';
		}
		$pid[1] = '1'; // Set ID
		$pid[2] = $patient['person_id'].'^1'; // Patient ID^Company Number
		$pid[5] = $patient['last_name'].'^'.$patient['first_name'].'^'.substr($patient['middle_name'],0,1); // Patient Name, <lst name> ^ <first name> ^ <middle initial>
		$pid[7] = date('Ymd',strtotime($patient['date_of_birth'])); // Date of Birth, yyyymmdd
		$pid[8] = $patient['gender']; // Sex, M=Male / F=Female 
		$addressHomeType = 7;
		if ($row = $this->_getAddress($patient['person_id'],$addressHomeType)) {
			$pid[11] = $row['line1'].'^'.$row['line2'].'^'.$row['city'].'^'.$row['state'].'^'.$row['postal_code']; // Patient Address, <street address> ^ <other designation> ^ <city> ^ <state> ^ <zip> - <4 digit zip extension>
		}
		$phoneHomeType = 7;
		if ($row = $this->_getPhone($patient['person_id'],$phoneHomeType)) {
			$pid[13] = $row['number']; // Phone Number – Home, (999)999-9999 
		}
		$phoneBusinessType = 3;
		if ($row = $this->_getPhone($patient['person_id'],$phoneBusinessType)) {
			$pid[14] = $row['number']; // Phone Number - Business, (999)999-9999X9999
		}
		$pid[19] = $patient['identifier']; // Patient Social Security Number, 999-99-9999
		$message .= "\n".implode('|',$pid);

		// Patient Visit
		$pv = array('PV');
		for ($i = 1; $i <= 8; $i++) {
			$pv[$i] = '';
		}
		$sqlSelect = $db->select()
				->from('encounter',array('*','DATE_FORMAT(date_of_treatment,"%Y-%m-%d") as date_of_treatment'))
				->join('person', 'person.person_id = encounter.treating_person_id')
				->joinLeft('buildings', 'encounter.building_id = buildings.id', array('buildings.name as locationName'))
				->where("patient_id = ?", $patient['person_id']);
		if ($rows = $db->fetchAll($sqlSelect)) {
			$ctr = 1;
			foreach ($rows as $row) {
				// TODO: this needs to iron out
				$tmp = $pv;
				$tmp[0] .= $ctr;
				$tmp[1] = $ctr++; // Set ID, "0001"
				$tmp[2] = 'P';
				$tmp[7] = $row['treating_person_id'].'^'.$row['last_name'].'^'.$row['first_name'].'^'.substr($row['middle_name'],0,1).'^^^^^LOCAL'; /* Attending, Doctor, UPIN, NPI; 
					<Code>^<Last Name>^ <First Name>^<MI>^^^^^<Code Identifier>
					The <Code> field could be
						-Local doctor code
						-UPIN code
						-NPI code

					The <Code Identifier> could be
						-L or LOCAL
						-UPIN
						-NPI

					Each occurrence would be delimited by the tilde (~)

					2096^Daniels^Jack^A^^^^^LOCAL~G51331^Daniels^Jack^A^^^^^UPIN~A12345^Daniels^Jack^A^^^^^NPI */
				$tmp[8] = $row['treating_person_id'].'^'.$row['last_name'].'^'.$row['first_name'].'^'.substr($row['middle_name'],0,1); // Referring Doctor; <Doctor Code> ^ <Last Name>^<First Name>^ <MI> 
				$message .= "\n".implode('|',$tmp);
			}
		}

		// Diagnosis Information
		$dg = array('DG');
		for ($i = 1; $i <= 16; $i++) {
			$dg[$i] = '';
		}
		$sqlSelect = $db->select()
				->from('patientDiagnosis')
				->joinLeft('person','person.person_id=patientDiagnosis.providerId')
				->where('patientId = ?',$patient['person_id']);
		if ($rows = $db->fetchAll($sqlSelect)) {
			$ctr = 1;
			foreach ($rows as $row) {
				$tmp = $dg;
				$tmp[0] .= $ctr;
				$tmp[1] = $ctr; // Set ID
				$tmp[3] = $row['code']; // Diagnosis Code
				$tmp[4] = $row['diagnosis']; // Diagnosis Description
				$tmp[5] = date('Ymd',strtotime($row['dateTime'])).'^D'; // Diagnosis Date/Time, yyyymmdd^D
				$tmp[6] = 'A'; // Diagnosis/DRG Type, Parameter defined default value. A=Admitting; W=Working; F=Final.
				$tmp[16] = $row['providerId'].'^'.$row['last_name'].'^'.$row['first_name'].'^'.substr($row['middle_name'],0,1); // Diagnosing Clinician, Dr: Medic id ^Last^First^MI, Field 6 from Patient Screen, DR.
				$message .= "\n".implode('|',$tmp);
			}
		}

		// Guarantor Information
		$gt = array('GT');
		for ($i = 1; $i <= 7; $i++) {
			$gt[$i] = '';
		}
		$guarantors = array();

		// Insurance Information
		$in = array('IN');
		for ($i = 1; $i < 44; $i++) {
			$in[$i] = '';
		}

		$sqlSelect = $db->select()
				->from(array('ir'=>'insured_relationship'))
				->join(array('ip'=>'insurance_program'),'ip.insurance_program_id = ir.insurance_program_id')
				->join(array('c'=>'company'),'c.company_id = ip.company_id',array('c.name AS company_name'))
				->where('ir.person_id = ?',$patient['person_id']);
		$ctrGt = 1;
		if ($rows = $db->fetchAll($sqlSelect)) {
			$ctr = 1;
			foreach ($rows as $row) {
				$tmp = $in;
				$tmp[0] .= $ctr;
				$tmp[1] = $ctr++;
				$tmp[2] = $row['group_name']; // Insurance Plan ID, Insurance Plan Numeric Identifier
				$tmp[3] = $row['company_id']; // Insurance Company ID
				$tmp[4] = $row['company_name']; // Insurance Company Name

				$sqlSelect = $db->select()
						->from(array('ca'=>'company_address'))
						->join(array('a'=>'address'),'a.address_id = ca.address_id')
						->where('ca.company_id = ?',(int)$row['company_id']);
				if ($coRow = $db->fetchRow($sqlSelect)) {
					$tmp[5] = $coRow['line1'].'^'.$coRow['line2'].'^'.$coRow['city'].'^'.$coRow['state'].'^'.$coRow['postal_code']; // Insurance Company Address, <street address> ^ <other designation> ^ <city> ^ <state> ^ <zip>
				}
				$sqlSelect = $db->select()
						->from(array('cn'=>'company_number'))
						->join(array('n'=>'number'),'n.number_id = cn.number_id')
						->where('cn.company_id = ?',(int)$row['company_id']);
				if ($coRow = $db->fetchRow($sqlSelect)) {
					$tmp[7] = $coRow['number']; // Insurance Company Phone Number, (999)999-9999
				}
				$tmp[8] = $row['group_number']; // Group Number, Group Number of the Insured
				$tmp[12] = date('Ymd',strtotime($row['effective_start'])); // Plan Effective Date, yyyymmdd
				$tmp[13] = date('Ymd',strtotime($row['effective_end'])); // Plan Termination Date, yyyymmdd
				$tmp[15] = 'M'; // Plan Type, Insurance Type. Values are: B = Blue Cross/Blue Shield, M = Medicare, C = Medicaid, H = HMO, O = All other types

				$sqlSelect = $db->select()
						->from('person')
						->where('person_id = ?',(int)$row['subscriber_id']);
				if ($personRow = $db->fetchRow($sqlSelect)) {
					$tmp[16] = $personRow['last_name'].'^'.$personRow['first_name'].'^'.$personRow['middle_name'].'^'.$personRow['suffix']; // Name of Insured, <family name> ^ <given name> ^ <middle initial/name> ^ <suffix>
					$tmp[17] = 'SE'; //$row['subscriber_to_patient_relationship']; // Relationship to Insured, "SE" = Self, "SP" = Spouse, "CH" = Child, "OT" = Other
					$tmp[18] = date('Ymd',strtotime($personRow['date_of_birth'])); // Insured’s Date of Birth, yyyymmdd
					$addressHomeType = 7;
					if ($tmpRow = $this->_getAddress($personRow['person_id'],$addressHomeType)) {
						$tmp[19] = $tmpRow['line1'].'^'.$tmpRow['line2'].'^'.$tmpRow['city'].'^'.$tmpRow['state'].'^'.$tmpRow['postal_code']; // Insured’s Address, <street address> ^ <other designation> ^ <city> ^ <state> ^ <zip> - <4 digit zip extension.
					}
					$tmp[43] = $personRow['gender']; // Insured’s Sex, “M”=Male “F”=Female
					$tmp[44] = ''; // Insured’s Employer Address, <street address> ^ <other designation> ^ <city> ^ <state> ^ <zip>.

					$homePhone = '';
					$phoneHomeType = 7;
					if ($phone = $this->_getPhone($personRow['person_id'],$phoneHomeType)) {
						$homePhone = $phone['number'];
					}
					$workPhone = '';
					$phoneWorkType = 7;
					if ($phone = $this->_getPhone($personRow['person_id'],$phoneWorkType)) {
						$workPhone = $phone['number'];
					}
					// Guarantor Information
					$tmpgt = $gt;
					$tmpgt[0] .= $ctrGt; // Set ID, Counter for GT1 occurrence
					$tmpgt[1] = $ctrGt++; // Set ID, Counter for GT1 occurrence
					$tmpgt[3] = $personRow['last_name'].'^'.$personRow['first_name'].'^'.$personRow['middle_name']; // Guarantor Name, <family name> ^ <given name> ^ <middle initial/name>
					$tmpgt[5] = $tmp[19]; // Guarantor Address, <street address> ^ <other designation> ^ <city> ^ <state> ^ <zip>-<zip plus 4>
					$tmpgt[6] = $homePhone; // Guarantor Phone Number - Home, (999)999-9999
					$tmpgt[7] = $workPhone; // Guarantor Phone Number - Work, (999)999-9999X9999
					$guarantors[] = implode('|',$tmpgt);
				}
				$tmp[22] = '1'; // Coordination of Benefits Priority, “1” =Primary, “2” =Secondary, “3” =Tertiary
				$tmp[36] = $row['group_number']; // Policy Number, Policy Number of the Insured
				$message .= "\n".implode('|',$tmp);
			}
		}
		foreach ($guarantors as $guarantor) {
			$message .= "\n".$guarantor;
		}

		file_put_contents('/tmp/ldb.log',"\n$message",FILE_APPEND);
		return $message;
	}

	protected function _getPhone($personId,$type) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('number')
				->where('person_id = ?',(int)$personId)
				->where('type = ?',(int)$type);
		return $db->fetchRow($sqlSelect);
	}

	protected function _getAddress($personId,$type) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('address')
				->where('person_id = ?',(int)$personId)
				->where('type = ?',(int)$type);
		return $db->fetchRow($sqlSelect);
	}

	protected function getPath($key) {
		if (!isset($this->_paths['application'])) {
			$this->_paths['application'] = realpath(dirname(__FILE__) . '/..');
			$this->_paths['base'] = realpath(dirname(__FILE__) . '/../../');
			$this->_paths['library'] = $this->_paths['application'] . '/library';
			$this->_paths['models'] = $this->_paths['application'] . '/models';
			$this->_paths['controllers'] = $this->_paths['application'] . '/controllers';
		}
		$ret = null;
		if (isset($this->_paths[$key])) {
			$ret = $this->_paths[$key];
		}
		return $ret;
	}

	public function init() {
		file_put_contents('/tmp/ldb.log',"\ninit started",FILE_APPEND);
		error_reporting(E_ALL | E_STRICT);
		set_include_path($this->getPath('library') . PATH_SEPARATOR 
					. $this->getPath('models') . PATH_SEPARATOR
					. $this->getPath('controllers') . PATH_SEPARATOR
					. get_include_path());
		require_once 'Zend/Loader.php';
		Zend_Loader::registerAutoLoad();
		$config = new Zend_Config_Ini($this->getPath('application').'/config/app.ini',APPLICATION_ENVIRONMENT);
		Zend_Registry::set('config',$config);
		date_default_timezone_set($config->date->timezone);

		try {
			$dbConfig = $config->database;
			$dbAdapter = Zend_Db::factory($dbConfig);
			$dbAdapter->query("SET NAMES 'utf8'");
		}
		catch (Zend_Exception $e) {
			$error = $e->getMessage();
			file_put_contents('/tmp/ldb.log',"\n$error",FILE_APPEND);
			die($error);
		}
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter',$dbAdapter);

		file_put_contents('/tmp/ldb.log',"\ninit done",FILE_APPEND);
		return $this;
	}

	private function __construct() {}

	private function __clone() {}

}
