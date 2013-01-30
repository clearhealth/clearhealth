<?php
/*****************************************************************************
*       Provider.php
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


class Provider extends WebVista_Model_ORM {
	protected $person_id;
	protected $person;
	protected $state_license_number;
	protected $clia_number;
	protected $dea_number;
	protected $bill_as;
	protected $report_as;
	protected $routing_station;
	protected $color;
	protected $sureScriptsSPI;
	protected $specialty;
	protected $dateActiveStart;
	protected $dateActiveEnd;
	protected $serviceLevel;

	protected $_table = "provider";
	protected $_primaryKeys = array("person_id");
	protected $_legacyORMNaming = true;

	function __construct() {
		$this->person = new Person();
                parent::__construct();
        }

	public function getDateActiveStartZ() {
		return gmdate("Y-m-d\TH:i:s.0",strtotime($this->dateActiveStart)).'Z';
	}

	public function getDateActiveEndZ() {
		return gmdate("Y-m-d\TH:i:s.0",strtotime($this->dateActiveEnd)).'Z';
	}

	static public function getIter() {
		$provider = new Provider();
                $db = Zend_Registry::get('dbAdapter');
                $provSelect = $db->select()
                        ->from('provider')
			->joinUsing('person','person_id')
			->order('person.last_name')
			->order('person.first_name');
                $iter = $provider->getIterator($provSelect);
		//trigger_error($provSelect, E_USER_NOTICE);
                return $iter;
        }

	static public function getArray($key = "person_id", $value = "optionName") {
                $iter = Provider::getIter();
                return $iter->toArray($key, $value);

        }	

	function getIterator($provSelect = null) {
		return new ProviderIterator($provSelect);
	}

	function getOptionName() {
		return $this->person->getDisplayName();
	}

	function getPersonId() {
		return $this->person_id;
	}

	public function setPerson_id($key) {
		$this->setPersonId($key);
	}

	function setPersonId($key) {
		if ($this->person->person_id > 0 && (int)$key != $this->person_id) {
			$person = new Person();
			unset($this->person);
			$this->person = $person;
		}
		$this->person_id = (int)$key;
		$this->person->person_id = (int)$key;
	}

	function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->person->ORMFields())) {
			return $this->person->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->person->__get($key))) {
			return $this->person->__get($key);
		}
		return parent::__get($key);
	}

	public static function getListSpecialties() {
		$specialties = array();

		$ama = array(); // AM = American Medical Association
		$ama[] = array('code'=>'AS','description'=>__('Abdominal Surgery'));
		$ama[] = array('code'=>'ADM','description'=>__('Addiction Medicine'));
		$ama[] = array('code'=>'ADP','description'=>__('Addiction Psychiatry'));
		$ama[] = array('code'=>'AMI','description'=>__('Adolescent Medicine (Internal Medicine)'));
		$ama[] = array('code'=>'ADL','description'=>__('Adolescent Medicine (Pediatrics)'));
		$ama[] = array('code'=>'OAR','description'=>__('Adult Reconstructive Orthopedics'));
		$ama[] = array('code'=>'AM','description'=>__('Aerospace Medicine'));
		$ama[] = array('code'=>'A','description'=>__('Allergy'));
		$ama[] = array('code'=>'AI','description'=>__('Allergy and Immunology'));
		$ama[] = array('code'=>'PTH','description'=>__('Anatomic and Clinical Pathology'));
		$ama[] = array('code'=>'ATP','description'=>__('Anatomic Pathology'));
		$ama[] = array('code'=>'AN','description'=>__('Anesthesiology'));
		$ama[] = array('code'=>'BBK','description'=>__('Blood Banking/Transfusion Medicine'));
		$ama[] = array('code'=>'CTS','description'=>__('Cardiothoracic Surgery'));
		$ama[] = array('code'=>'CD','description'=>__('Cardiovascular Disease'));
		$ama[] = array('code'=>'PCH','description'=>__('Chemical Pathology'));
		$ama[] = array('code'=>'CHP','description'=>__('Child and Adolescent Psychiatry'));
		$ama[] = array('code'=>'CHN','description'=>__('Child Neurology'));
		$ama[] = array('code'=>'PLI','description'=>__('Clinical and Laboratory Immunology (Pediatrics)'));
		$ama[] = array('code'=>'DDL','description'=>__('Clinical and Laboratory DermatologicalImmunology'));
		$ama[] = array('code'=>'ALI','description'=>__('Clinical and Laboratory Immunology(Allergy and Immunology)'));
		$ama[] = array('code'=>'ILI','description'=>__('Clinical and Laboratory Immunology (Internal Medicine)'));
		$ama[] = array('code'=>'CBG','description'=>__('Clinical Biochemical Genetics'));
		$ama[] = array('code'=>'ICE','description'=>__('Clinical Cardiac Electrophysiology'));
		$ama[] = array('code'=>'CCG','description'=>__('Clinical Cytogentics'));
		$ama[] = array('code'=>'CG','description'=>__('Clinical Genetics'));
		$ama[] = array('code'=>'CMG','description'=>__('Clinical Molecular Genetics'));
		$ama[] = array('code'=>'CN','description'=>__('Clinical Neurophysiology'));
		$ama[] = array('code'=>'CLP','description'=>__('Clinical Pathology'));
		$ama[] = array('code'=>'PA','description'=>__('Clinical Pharmacology'));
		$ama[] = array('code'=>'CRS','description'=>__('Colon and Rectal Surgery'));
		$ama[] = array('code'=>'CCA','description'=>__('Critical Care Medicine (Anesthesiology)'));
		$ama[] = array('code'=>'CCM','description'=>__('Critical Care Medicine (Internal Medicine)'));
		$ama[] = array('code'=>'NCC','description'=>__('Critical Care Medicine (Neurological Surgery)'));
		$ama[] = array('code'=>'OCC','description'=>__('Critical Care Medicine (Obstetrics and Gynecology)'));
		$ama[] = array('code'=>'PCP','description'=>__('Cytopathology'));
		$ama[] = array('code'=>'DS','description'=>__('Dermatologic Surgery'));
		$ama[] = array('code'=>'D','description'=>__('Dermatology'));
		$ama[] = array('code'=>'DMP','description'=>__('Dermatopathology (Pathology)'));
		$ama[] = array('code'=>'DIA','description'=>__('Diabetes'));
		$ama[] = array('code'=>'DR','description'=>__('Diagnostic Radiology'));
		$ama[] = array('code'=>'EM','description'=>__('Emergency Medicine'));
		$ama[] = array('code'=>'END','description'=>__('Endocrinology, Diabetes, and Metabolism'));
		$ama[] = array('code'=>'EP','description'=>__('Epidemiology'));
		$ama[] = array('code'=>'FPS','description'=>__('Facial Plastic Surgery'));
		$ama[] = array('code'=>'FP','description'=>__('Family Practice'));
		$ama[] = array('code'=>'OFA','description'=>__('Foot and Ankle Orthopedics'));
		$ama[] = array('code'=>'FOP','description'=>__('Forensic Pathology'));
		$ama[] = array('code'=>'PFP','description'=>__('Forensic Psychiatry'));
		$ama[] = array('code'=>'GE','description'=>__('Gastroenterology'));
		$ama[] = array('code'=>'GP','description'=>__('General Practice'));
		$ama[] = array('code'=>'GPM','description'=>__('General Preventive Medicine'));
		$ama[] = array('code'=>'GS','description'=>__('General Surgery'));
		$ama[] = array('code'=>'FPG','description'=>__('Geriatric Medicine (Family Practice)'));
		$ama[] = array('code'=>'IMG','description'=>__('Geriatric Medicine (Internal Medicine)'));
		$ama[] = array('code'=>'PYG','description'=>__('Geriatric Psychiatry'));
		$ama[] = array('code'=>'GO','description'=>__('Gynecological Oncology'));
		$ama[] = array('code'=>'GYN','description'=>__('Gynecology'));
		$ama[] = array('code'=>'HS','description'=>__('Hand Surgery'));
		$ama[] = array('code'=>'HNS','description'=>__('Head and Neck Surgery'));
		$ama[] = array('code'=>'HEM','description'=>__('Hematology (Internal Medicine)'));
		$ama[] = array('code'=>'HMP','description'=>__('Hematology (Pathology)'));
		$ama[] = array('code'=>'HO','description'=>__('Hematology/Oncology'));
		$ama[] = array('code'=>'HEP','description'=>__('Hepatology'));
		$ama[] = array('code'=>'IG','description'=>__('Immunology'));
		$ama[] = array('code'=>'PIP','description'=>__('Immunopathology'));
		$ama[] = array('code'=>'ID','description'=>__('Infectious Disease'));
		$ama[] = array('code'=>'IM','description'=>__('Internal Medicine'));
		$ama[] = array('code'=>'MPD','description'=>__('Internal Medicine/Pediatrics'));
		$ama[] = array('code'=>'LM','description'=>__('Legal Medicine'));
		$ama[] = array('code'=>'MFM','description'=>__('Maternal and Fetal Medicine'));
		$ama[] = array('code'=>'MXR','description'=>__('Maxillofacial Radiology'));
		$ama[] = array('code'=>'MG','description'=>__('Medical Genetics'));
		$ama[] = array('code'=>'MDM','description'=>__('Medical Management'));
		$ama[] = array('code'=>'MM','description'=>__('Medical Microbiology'));
		$ama[] = array('code'=>'ON','description'=>__('Medical Oncology'));
		$ama[] = array('code'=>'ETX','description'=>__('Medical Toxicology (Emergency Medicine)'));
		$ama[] = array('code'=>'PDT','description'=>__('Medical Toxicology (Pediatrics)'));
		$ama[] = array('code'=>'PTX','description'=>__('Medical Toxicology (Preventive Medicine)'));
		$ama[] = array('code'=>'OMO','description'=>__('Musculoskeletal Oncology'));
		$ama[] = array('code'=>'NPM','description'=>__('Neonatal-Perinatal Medicine'));
		$ama[] = array('code'=>'NEP','description'=>__('Nephrology'));
		$ama[] = array('code'=>'NS','description'=>__('Neurological Surgery'));
		$ama[] = array('code'=>'N','description'=>__('Neurology'));
		$ama[] = array('code'=>'NRN','description'=>__('Neurology/Diagnostic Radiology/Neuroradiology'));
		$ama[] = array('code'=>'NP','description'=>__('Neuropathology'));
		$ama[] = array('code'=>'RNR','description'=>__('Neuroradiology'));
		$ama[] = array('code'=>'NM','description'=>__('Nuclear Medicine'));
		$ama[] = array('code'=>'NR','description'=>__('Nuclear Radiology'));
		$ama[] = array('code'=>'NTR','description'=>__('Nutrition'));
		$ama[] = array('code'=>'OBS','description'=>__('Obstetrics'));
		$ama[] = array('code'=>'OBG','description'=>__('Obstetrics and Gynecology'));
		$ama[] = array('code'=>'OM','description'=>__('Occupational Medicine'));
		$ama[] = array('code'=>'OPH','description'=>__('Ophthalomology'));
		$ama[] = array('code'=>'ORS','description'=>__('Orthopedic Surgery'));
		$ama[] = array('code'=>'OSS','description'=>__('Orthopedic Surgery of the Spine'));
		$ama[] = array('code'=>'OTR','description'=>__('Orthopedic Trauma'));
		$ama[] = array('code'=>'OMM','description'=>__('Osteopathic Manipulative Medicine'));
		$ama[] = array('code'=>'OS','description'=>__('Other'));
		$ama[] = array('code'=>'OTO','description'=>__('Otolaryngology'));
		$ama[] = array('code'=>'OT','description'=>__('Otology/Neurotology'));
		$ama[] = array('code'=>'APM','description'=>__('Pain Management (Anesthesiology)'));
		$ama[] = array('code'=>'PMD','description'=>__('Pain Medicine'));
		$ama[] = array('code'=>'PLM','description'=>__('Palliative Medicine'));
		$ama[] = array('code'=>'PDA','description'=>__('Pediatric Allergy'));
		$ama[] = array('code'=>'PDC','description'=>__('Pediatric Cardiology'));
		$ama[] = array('code'=>'CCP','description'=>__('Pediatric Critical Care Medicine'));
		$ama[] = array('code'=>'PE','description'=>__('Pediatric Emergency Medicine (Emergency Medicine)'));
		$ama[] = array('code'=>'PEM','description'=>__('Pediatric Emergency Medicine (Pediatrics)'));
		$ama[] = array('code'=>'PDE','description'=>__('Pediatric Endocrinology'));
		$ama[] = array('code'=>'PG','description'=>__('Pediatric Gastroenterology'));
		$ama[] = array('code'=>'PHO','description'=>__('Pediatric Hematology/Oncology'));
		$ama[] = array('code'=>'PDI','description'=>__('Pediatric Infectious Diseases'));
		$ama[] = array('code'=>'PN','description'=>__('Pediatric Nephrology'));
		$ama[] = array('code'=>'PO','description'=>__('Pediatric Ophthalmology'));
		$ama[] = array('code'=>'OP','description'=>__('Pediatric Orthopedics'));
		$ama[] = array('code'=>'PDO','description'=>__('Pediatric Otolaryngology'));
		$ama[] = array('code'=>'PP','description'=>__('Pediatric Pathology'));
		$ama[] = array('code'=>'PDP','description'=>__('Pediatric Pulmonology'));
		$ama[] = array('code'=>'PDR','description'=>__('Pediatric Radiology'));
		$ama[] = array('code'=>'PPR','description'=>__('Pediatric Rheumatology'));
		$ama[] = array('code'=>'NSP','description'=>__('Pediatric Surgery (Neurological Surgery)'));
		$ama[] = array('code'=>'PDS','description'=>__('Pediatric Surgery (Surgery)'));
		$ama[] = array('code'=>'UP','description'=>__('Pediatric Urology'));
		$ama[] = array('code'=>'PD','description'=>__('Pediatrics'));
		$ama[] = array('code'=>'PM','description'=>__('Physical Medicine and Rehabilitation'));
		$ama[] = array('code'=>'PS','description'=>__('Plastic Surgery'));
		$ama[] = array('code'=>'PRO','description'=>__('Proctology'));
		$ama[] = array('code'=>'P','description'=>__('Psychiatry'));
		$ama[] = array('code'=>'PYA','description'=>__('Psychoanalysis'));
		$ama[] = array('code'=>'MPH','description'=>__('Public Health and General Preventive Medicine'));
		$ama[] = array('code'=>'PUD','description'=>__('Pulmonary Disease'));
		$ama[] = array('code'=>'PCC','description'=>__('Pulmonary Disease and Critical Care Medicine'));
		$ama[] = array('code'=>'RO','description'=>__('Radiation Oncology'));
		$ama[] = array('code'=>'RIP','description'=>__('Radioisotopic Pathology'));
		$ama[] = array('code'=>'RP','description'=>__('Radiological Physics'));
		$ama[] = array('code'=>'R','description'=>__('Radiology'));
		$ama[] = array('code'=>'REN','description'=>__('Reproductive Endocrinology'));
		$ama[] = array('code'=>'RHU','description'=>__('Rheumatology'));
		$ama[] = array('code'=>'SP','description'=>__('Selective Pathology'));
		$ama[] = array('code'=>'SM','description'=>__('Sleep Medicine'));
		$ama[] = array('code'=>'SCI','description'=>__('Spinal Cord Injury Medicine (Physical Medicine and Rehabilitation)'));
		$ama[] = array('code'=>'ESM','description'=>__('Sports Medicine (Emergency Medicine)'));
		$ama[] = array('code'=>'FSM','description'=>__('Sports Medicine (Family Practice)'));
		$ama[] = array('code'=>'ISM','description'=>__('Sports Medicine (Internal Medicine)'));
		$ama[] = array('code'=>'OSM','description'=>__('Sports Medicine (Orthopedic Surgery)'));
		$ama[] = array('code'=>'PSM','description'=>__('Sports Medicine (Pediatrics)'));
		$ama[] = array('code'=>'CCS','description'=>__('Surgical Critical Care (Surgery)'));
		$ama[] = array('code'=>'SO','description'=>__('Surgical Oncology'));
		$ama[] = array('code'=>'TTS','description'=>__('Transplant Surgery'));
		$ama[] = array('code'=>'TRS','description'=>__('Trauma Surgery'));
		$ama[] = array('code'=>'UM','description'=>__('Undersea Medicine'));
		$ama[] = array('code'=>'US','description'=>__('Unspecified'));
		$ama[] = array('code'=>'U','description'=>__('Urology'));
		$ama[] = array('code'=>'VIR','description'=>__('Vascular and Interventional Radiology'));
		$ama[] = array('code'=>'VS','description'=>__('Vascular Surgery'));
		$specialties['AM'] = $ama; // AM = American Medical Association

		$dea = array(); // DE = Drug Enforcement Agency
		$specialties['DE'] = $dea; // DE = Drug Enforcement Agency

		return $specialties;
	}

	public static function getServiceLevelOptions() {
		$serviceLevels = array();
		$serviceLevels[0] = 'Deactivated';
		$serviceLevels[1] = 'New Prescriptions Only';
		$serviceLevels[3] = 'New Prescriptions & Refills';
		return $serviceLevels;
	}

	public function populateProviderIdWithSPI($SPI = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($SPI === null) {
			$SPI = $this->sureScriptsSPI;
		}
		$sqlSelect = $db->select()
				->from($this->_table,'person_id')
				->where('sureScriptsSPI = ?',$SPI);
		if ($row = $db->fetchRow($sqlSelect)) {
			$this->person_id = $row['person_id'];
		}
	}

	public function getProviderId() {
		return $this->person_id;
	}

	public function getIteratorByPracticeId($practiceId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->joinInner('person','person.person_id = '.$this->_table.'.person_id')
				->where('person.primary_practice_id = ?',(int)$practiceId);
		//trigger_error($sqlSelect->__toString());
		return $this->getIterator($sqlSelect);
	}

	public function getTIN() {
		static $tin = null;
		if ($tin !== null) return $tin;
		$providerTIN = Enumeration::getEnumArray('Provider TIN','key');
		if (isset($providerTIN[$this->person_id])) $tin = $providerTIN[$this->person_id];
		return $tin;
	}

}
