<?php
/*****************************************************************************
*       ProcedureCodesLOINC.php
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


class ProcedureCodesLOINC extends WebVista_Model_ORM {

	protected $loinc_num;
	protected $component;
	protected $property;
	protected $time_aspct;
	protected $system;
	protected $scale_typ;
	protected $method_typ;
	protected $relat_nms;
	protected $class;
	protected $source;
	protected $dt_last_ch;
	protected $chng_type;
	protected $comments;
	protected $answerlist;
	protected $status;
	protected $map_to;
	protected $scope;
	protected $consumer_name;
	protected $ipcc_units;
	protected $reference;
	protected $exact_cmp_sy;
	protected $molar_mass;
	protected $classtype;
	protected $formula;
	protected $species;
	protected $exmpl_answers;
	protected $acssym;
	protected $base_name;
	protected $final;
	protected $naaccr_id;
	protected $code_table;
	protected $setroot;
	protected $panelelements;
	protected $survey_quest_text;
	protected $survey_quest_src;
	protected $unitsrequired;
	protected $submitted_units;
	protected $relatednames2;
	protected $shortname;
	protected $order_obs;
	protected $cdisc_common_tests;
	protected $hl7_field_subfield_id;
	protected $external_copyright_notice;
	protected $example_units;
	protected $inpc_percentage;
	protected $long_common_name;
	protected $hl7_v2_datatype;
	protected $hl7_v3_datatype;
	protected $curated_range_and_units;
	protected $document_section;
	protected $definition_description_help;
	protected $example_ucum_units;
	protected $example_si_ucum_units;
	protected $status_reason;
	protected $status_text;

	protected $_table = 'procedureCodesLOINC';
	protected $_primaryKeys = array('loinc_num');
	protected $_legacyORMNaming = true;

	public static function sampleTypes() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('procedureCodesLOINC','system')
				->distinct()
				->where("status = 'ACTIVE'")
				->where("order_obs = 'Order' OR order_obs = 'Both'")
				->order('system');
		$sampleTypesTable = self::sampleTypesTable();
		$iterator = new ProcedureCodesLOINCIterator($sqlSelect);
		$samples = array();
		foreach ($iterator as $loinc) {
			$value = $loinc->system;
			if (isset($sampleTypesTable[$loinc->system])) {
				$value = $sampleTypesTable[$loinc->system];
			}
			$samples[$loinc->system] = $value;
		}
		//trigger_error($sqlSelect->__toString());
		return $samples;
	}

	public static function loadLOINC($filename) {
		if (!file_exists($filename)) {
			trigger_error('Filename '.$filename.' does not exists.');
			return;
		}
		$lines = file($filename);
		if ($lines === false || !isset($lines[0])) return;
		// first line MUST be a header
		$headers = array();
		$headerLine = array_shift($lines);
		foreach (explode("\t",$headerLine) as $val) {
			$headers[] = trim(strtolower(str_replace('"','',$val)));
		}
		$lengths = array();
		foreach ($lines as $line) {
			$orm = new self();
			foreach (explode("\t",$line) as $key=>$value) {
				$value = trim($value);
				$len = strlen($value);
				if ($len > 0) $value = substr($value,1,$len-2); // removed double quotes
				if (!isset($headers[$key])) continue;
				$field = $headers[$key];
				if (!isset($lengths[$field])) $lengths[$field] = 0;
				$len = strlen($value);
				if ($len > $lengths[$field]) $lengths[$field] = $len;
				$orm->$field = $value;
			}
			$orm->persist();
		}
		//file_put_contents('/tmp/lengths.txt',print_r($lengths,true));
	}

	public static function sampleTypesTable() {
		// sample types refer to system field
		$tables = array();
		$tables['Abs'] = 'Abscess';
		$tables['Amnio fld'] = 'Amniotic fluid';
		$tables['Anal'] = 'Anus';
		$tables['Asp'] = 'Aspirate';
		$tables['Bil fld'] = 'Bile fluid';
		$tables['BldA'] = 'Blood arterial';
		$tables['BldL'] = 'Blood bag';
		$tables['BldC'] = 'Blood capillary';
		$tables['BldCo'] = 'Blood - cord';
		$tables['BldMV'] = 'Blood- Mixed Venous';
		$tables['BldP'] = 'Blood - peripheral';
		$tables['BldV'] = 'Blood venous';
		$tables['Bld.dot'] = 'Blood filter paper';
		$tables['Bone'] = 'Bone';
		$tables['Brain'] = 'Brain';
		$tables['Bronchial'] = 'Bronchial';
		$tables['Burn'] = 'Burn';
		$tables['Calculus'] = 'Calculus (=Stone)';
		$tables['Cnl'] = 'Cannula';
		$tables['CTp'] = 'Catheter tip';
		$tables['CSF'] = 'Cerebral spinal fluid';
		$tables['Cvm'] = 'Cervical mucus';
		$tables['Cvx'] = 'Cervix';
		$tables['Col'] = 'Colostrum';
		$tables['Cnjt'] = 'Conjunctiva';
		$tables['Crn'] = 'Cornea';
		$tables['Dentin'] = 'Dentin';
		$tables['Dial fld'] = 'Dialysis fluid';
		$tables['Dose'] = 'Dose med or substance';
		$tables['Drain'] = 'Drain';
		$tables['Duod fld'] = 'Duodenal fluid';
		$tables['Ear'] = 'Ear';
		$tables['Endomet'] = 'Endometrium';
		$tables['RBC'] = 'Erythrocytes';
		$tables['Eye'] = 'Eye';
		$tables['Exhl gas'] = 'Exhaled gas (=breath)';
		$tables['Fibroblasts'] = 'Fibroblasts';
		$tables['Fistula'] = 'Fistula';
		$tables['Body fld'] = 'Body fluid, unsp';
		$tables['Food'] = 'Food sample';
		$tables['Gas'] = 'Gas';
		$tables['Gast fld'] = 'Gastric fluid/contents';
		$tables['Genital'] = 'Genital';
		$tables['Genital fld'] = 'Genital fluid';
		$tables['Genital loc'] = 'Genital lochia';
		$tables['Genital muc'] = 'Genital mucus';
		$tables['Hair'] = 'Hair';
		$tables['Inhl gas'] = 'Inhaled gas';
		$tables['Isolate'] = 'Isolate';
		$tables['WBC'] = 'Leukocytes';
		$tables['Line'] = 'Line';
		$tables['Liver'] = 'Liver';
		$tables['Lung tiss'] = 'Lung tissue';
		$tables['Bone mar'] = 'Marrow (bone)';
		$tables['Meconium'] = 'Meconium';
		$tables['Milk'] = 'Milk';
		$tables['Nail'] = 'Nail';
		$tables['Nose'] = 'Nose (nasal passage)';
		$tables['Nph'] = 'Naspopharynx';
		$tables['Penile vessels'] = 'Penile vessels';
		$tables['Penis'] = 'Penis';
		$tables['Pericard fld'] = 'Pericardial fluid';
		$tables['Periton fld'] = 'Peritoneal fluid /ascites';
		$tables['Dial fld prt'] = 'Peritoneal dialysis fluid';
		$tables['Placent'] = 'Placenta';
		$tables['Plas'] = 'Plasma';
		$tables['Plr fld'] = 'Pleural fluid (thoracentesis fld)';
		$tables['PPP'] = 'Platelet poor plasma';
		$tables['PRP'] = 'Platelet rich plasma';
		$tables['Pus'] = 'Pus';
		$tables['RBCCo'] = 'Red Blood Cells Cord';
		$tables['Saliva'] = 'Saliva';
		$tables['Semen'] = 'Seminal fluid';
		$tables['Ser'] = 'Serum';
		$tables['Skin'] = 'Skin';
		$tables['Sputum'] = 'Sputum';
		$tables['Sptt'] = 'Sputum - tracheal aspirate';
		$tables['Stool'] = 'Stool = Fecal';
		$tables['Sweat'] = 'Sweat';
		$tables['Synv fld'] = 'Synovial fluid (Joint fluid)';
		$tables['Tear'] = 'Tears';
		$tables['Thrt'] = 'Throat';
		$tables['Platelets'] = 'Thrombocyte (platelet)';
		$tables['Tiss'] = 'Tissue, unspecified';
		$tables['Tlgi'] = 'Tissue large intestine';
		$tables['Tsmi'] = 'Tissue small intestine';
		$tables['Trachea'] = 'Trachea';
		$tables['Tube'] = 'Tube, unspecified';
		$tables['Ulc'] = 'Ulcer';
		$tables['Urethra'] = 'Urethra';
		$tables['Urine'] = 'Urine';
		$tables['Urine sed'] = 'Urine sediment';
		$tables['Unk sub'] = 'Unknown substance';
		$tables['Vag'] = 'Vagina';
		$tables['Vitr fld'] = 'Vitreous Fluid';
		$tables['Vomitus'] = 'Vomitus';
		$tables['Bld'] = 'Whole blood';
		$tables['Water'] = 'Water';
		$tables['Wound'] = 'Wound';
		$tables['XXX'] = 'To be specified in another part of the message';
		return $tables;
	}

}
