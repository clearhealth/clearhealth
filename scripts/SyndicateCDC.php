#!/usr/bin/php
<?php
/*****************************************************************************
*       SyndicateCDC.php
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


function __($str) {
	return $str;
}

define('APPLICATION_ENVIRONMENT','production');
$appFile = realpath(dirname(__FILE__) . '/../application/library/WebVista/App.php');
require_once $appFile;

class SyndicateCDC extends WebVista {

	public static function getInstance() {
        	if (null === self::$_instance) {
        		self::$_instance = new self();
			self::$_instance->_init();
        	}
		return self::$_instance;
	}

	protected function _init() {
		$this->_setupEnvironment()
			->_setupDb()
			->_setupCache()
			->_setupTranslation();
		return $this;
	}

	private function __construct() {
		$this->_computePaths();
	}

	private function __clone() {}

	protected function _setupEnvironment() {
		parent::_setupEnvironment();
		// disable strict reporting
		error_reporting(E_ALL);
		return $this;
	}

	public function NSDRStart() {
		return NSDR::systemStart();
	}

	public function NSDRReload() {
		return NSDR::systemReload();
	}

	public function NSDRUnload() {
		return NSDR::systemUnload();
	}

	public function run() {
		$tables = array(
			'growthChartWeightForAge36' => array(
				'description' => 'Birth to 36 months',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/wtageinf.htm',
			),
			'growthChartLengthForAge36' => array(
				'description' => 'Birth to 36 months',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/lenageinf.htm',
			),
			'growthChartWeightForRecumbentLength36' => array(
				'description' => 'Birth to 36 months',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/wtleninf.htm',
			),
			'growthChartHeadCircumferenceForAge36' => array(
				'description' => 'Birth to 36 months',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/hcageinf.htm',
			),
			'growthChartWeightForStature20' => array(
				'description' => 'Children 2 to 20 years',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/wtstat.htm',
			),
			'growthChartWeightForAge20' => array(
				'description' => 'Children 2 to 20 years',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/wtage.htm',
			),
			'growthChartStatureForAge20' => array(
				'description' => 'Children 2 to 20 years',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/statage.htm',
			),
			'growthChartBMIForAge20' => array(
				'description' => 'Children 2 to 20 years',
				'url' => 'http://www.cdc.gov/growthcharts/html_charts/bmiagerev.htm',
			),
		);
		//ksort($tables);

		$data = array();
		foreach ($tables as $tableName=>$table) {
			$doc = new DOMDocument();
			$doc->loadHTMLFile($table['url']);
			$nodeList = $doc->getElementsByTagName('table');
			foreach ($nodeList as $node) {
				$gender = null;
				foreach ($node->attributes as $attribute) {
					if ($attribute->name != 'id') continue;
					$gender = $attribute->value;
					break;
				}
				if ($gender === null) continue;
				$gender = str_replace('table','',$gender);
				$ctr = 0;
				foreach ($node->childNodes as $trNode) {
					if ($trNode->tagName != 'tr') continue;
					foreach ($trNode->childNodes as $tdNode) {
						if (!($tdNode instanceof DOMElement) || ($tdNode->tagName != 'th' && $tdNode->tagName != 'td')) continue;
						if ($ctr == 0) {
							$header = explode(' ',trim($tdNode->nodeValue));
							$data['headers'][$tableName][$gender][] = trim(strtolower($header[0]));
						}
						else {
							$fullValue = null;
							foreach ($tdNode->attributes as $attribute) {
								if ($attribute->name != 'x:num') continue;
								$fullValue = trim($attribute->value);
								break;
							}
							$data['columns'][$tableName][$gender][$ctr][] = array(
								'fullValue' => $fullValue,
								'value' => trim($tdNode->nodeValue),
							);
						}
					}
					$ctr++;
				}
			}
		}
		foreach ($tables as $tableName=>$table) {
			if (!isset($data['columns'][$tableName])) continue;
			$ormClass = ucfirst($tableName);
			$ormId = $tableName.'Id';
			$i = 1;
			foreach ($data['columns'][$tableName] as $gender=>$values) {
				foreach ($values as $ctr=>$columns) {
					$orm = new $ormClass();
					$orm->$ormId = $i++;
					$orm->gender = $gender;
					foreach ($columns as $key=>$column) {
						$field = $data['headers'][$tableName][$gender][$key];
						$val = 'value';
						if (is_numeric(substr($field,0,1))) {
							$field = 'p'.$field;
							if ($column['fullValue'] !== null) {
								$val = 'fullValue';
							}
						}
						$orm->$field = $column[$val];
					}
					$orm->persist();
				}
			}
		}
		echo "Done.\n";
	}

}

$cdc = SyndicateCDC::getInstance();
$cdc->run();
