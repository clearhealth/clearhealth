<?php
/*****************************************************************************
*       GrowthChartBMIForAge20.php
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


class GrowthChartBase extends WebVista_Model_ORM {

	protected $gender;
	protected $p3rd;
	protected $p5th;
	protected $p10th;
	protected $p25th;
	protected $p50th;
	protected $p75th;
	protected $p90th;
	protected $p95th;
	protected $p97th;

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;

	protected $_dataTableMappings = array(
		'age' => array(
			'p3rd',
			'p5th',
			'p10th',
			'p25th',
			'p50th',
			'p75th',
			'p90th',
			'p95th',
			'p97th',
		)
	);

	public static $_vitalSigns = array(); // holds ORM vital sign values

	public function getIteratorByGender($gender=null) {
		if ($gender === null) {
			$gender = $this->gender;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('gender = ?',(int)$gender);
		return $this->getIterator($sqlSelect);
	}

	public function listMales() {
		return $this->getIteratorByGender(self::GENDER_MALE);
	}

	public function listFemales() {
		return $this->getIteratorByGender(self::GENDER_FEMALE);
	}

	public static function listCharts() {
		$chartList = array(
			'growthChartBMIForAge20' => __('BMI for Age (2 to 20 years)'),
			'growthChartHeadCircumferenceForAge36' => __('Head Circumference for Age (Birth to 36 months)'),
			'growthChartLengthForAge36' => __('Length for Age (Birth to 36 months)'),
			'growthChartStatureForAge20' => __('Stature for Age (2 to 20 years)'),
			'growthChartWeightForAge20' => __('Weight for Age (2 to 20 years)'),
			'growthChartWeightForAge36' => __('Weight for Age (Birth to 36 months)'),
			'growthChartWeightForRecumbentLength36' => __('Weight for Recumbent Length (Birth to 36 months)'),
			'growthChartWeightForStature20' => __('Weight for Stature (2 to 20 years)'),
		);
		return $chartList;
	}

	public static function prettyName($name) {
		$name = trim($name);
		$name = preg_replace('/([a-z])Id$/','$1',$name);
		$name = preg_replace('/([A-Z])(?![A-Z])/',' $1',$name);
		$name = ucwords($name);
		return trim($name);
	}

	public function listVitals(Person $person) {
		return array();
	}

	public static function calculateMonthsDiff($dateFrom,$dateTo,$isTimestamp=false) {
		if (!$isTimestamp) {
			$dateFrom = strtotime($dateFrom);
			$dateTo = strtotime($dateTo);
		}
		$months = 0;
		$minDiff = 0;
		do {
			$yrFrom = date('Y',$dateFrom);
			$moFrom = date('m',$dateFrom);
			$dayFrom = date('d',$dateFrom);
			$hrFrom = date('H',$dateFrom);
			$minFrom = date('i',$dateFrom);

			$yrTo = date('Y',$dateTo);
			$moTo = date('m',$dateTo);
			$dayTo = date('d',$dateTo);
			$hrTo = date('H',$dateTo);
			$minTo = date('i',$dateTo);

			if ($yrFrom == $yrTo && $moFrom == $moTo) {
				$diff = $dateTo - $dateFrom;
				$numOfDays = date('t',$dateFrom);
				$min = $numOfDays * 86400;
				$minDiff = $diff / $min;

				$months += $minDiff;
				break;
			}
			$dateFrom = strtotime('+1 month',$dateFrom);
			$months++;
		} while (true);
		return $months;
	}

	public static function getAllVitals(Array $filters,$dateOfBirth=null) {
		$data = array();
		$dates = array();
		$results = VitalSignGroup::getVitalsByFilters($filters);
		self::$_vitalSigns = array();
		foreach ($results as $result) {
			self::$_vitalSigns[] = $result;
			// get the age the date taken
			if (!isset($dates[$result['vitalSignGroupId']])) {
				$v = $result['vitalSignGroupId'];
				if ($dateOfBirth !== null) {
					$v = GrowthChartBase::calculateMonthsDiff($dateOfBirth,strtotime($result['dateTime']),true);
				}
				$dates[$result['vitalSignGroupId']] = $v;
				$data["{$v}"] = array();
			}

			$convertedValues = VitalSignValue::convertValues($result['vital'],$result['value'],$result['units']);
			if ($convertedValues !== false) {
				$x = explode(' ',$convertedValues['metric']);
				$unit = array_pop($x);
				$y = implode(' ',$x);
			}
			else {
				$y = $result['value'];
			}
			$data["{$dates[$result['vitalSignGroupId']]}"][$result['vital']] = $y;
		}
		return $data;
	}

}
