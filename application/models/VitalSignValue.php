<?php
/*****************************************************************************
*       VitalSignValue.php
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


class VitalSignValue extends WebVista_Model_ORM {

	protected $vitalSignValueId;
	protected $vitalSignGroupId;
	protected $vitalSignGroup;
	protected $unavailable;
	protected $refused;
	protected $vital;
	protected $value;
	protected $units;
	protected $_primaryKeys = array('vitalSignValueId');
	protected $_table = "vitalSignValues";
	protected $_cascadePersist = false;

	public function __construct() {
		$this->vitalSignGroup = new VitalSignGroup();
		$this->vitalSignGroup->_cascadePersist = false;
	}

	public function setVitalSignGroupId($id) {
		$this->vitalSignGroupId = (int)$id;
		$this->vitalSignGroup->vitalSignGroupId = $this->vitalSignGroupId;
	}

	public static function convertValuesHeadCircumference($value,$unit) {
		return self::convertValuesHeight($value,$unit);
	}

	public static function convertValuesHeight($value,$unit) {
		if (!is_numeric($value) || !$value > 0) {
			return false;
		}
		$uss = $value;
		$metric = $value;
		switch ($unit) {
			case 'IN': // Inch - USS
				// 1 in = 2.5 cm; 1 in multiplied by 2.54 = 2.5 cm.
				$metric = sprintf("%.2f",($value * 2.54));
				break;
			case 'CM': // Centimeter - Metric
				// 1 cm = 0.4 in; 1 cm multiplied by 0.3937008 = 0.4 in.
				$uss = sprintf("%.2f",($value * 0.3937008));
				break;
			default:
				return false;
		}
		return array('uss'=>$uss.' IN','metric'=>$metric.' CM');
	}

	public static function convertValuesWeight($value,$unit) {
		if (!is_numeric($value) || !$value > 0) {
			return false;
		}
		$uss = $value;
		$metric = $value;
		switch ($unit) {
			case 'LB': // Pound - USS
				// 1 lb = 0.5 kg; 1 lb divided by 2.2 = 0.5 kg.
				$metric = sprintf("%.2f",($value / 2.2));
				break;
			case 'KG': // Kilogram - Metric
				// 1 kg = 2.2 lbs; 1 kg multiplied by 2.2 = 2.2 lbs.
				$uss = sprintf("%.2f",($value * 2.2));
				break;
			default:
				return false;
		}
		return array('uss'=>$uss.' LB','metric'=>$metric.' KG');
	}

	public static function convertValuesTemperature($value,$unit) {
		if (!is_numeric($value) || !$value > 0) {
			return false;
		}
		$uss = $value;
		$metric = $value;
		switch ($unit) {
			case 'F': // Fahrenheit - USS
				// 1 F = -17.2 C; (1 F - 32) multiplied by 5/9 = -17.2 C
				$metric = sprintf("%.2f",(($value - 32) * (5 / 9)));
				break;
			case 'C': // Celsius - Metric
				// 1 C = 33.8 F; 1 C multiplied by 9/5 +32 = 33.8 F
				$uss = sprintf("%.2f",($value * (9 / 5) + 32));
				break;
			default:
				return false;
		}
		return array('uss'=>$uss.' F','metric'=>$metric.' C');
	}

	public static function convertValues($vital,$value,$unit) {
		$methodName = 'convertValues'.ucfirst($vital);
		$vitalSignValue = new self();
		if (method_exists($vitalSignValue,$methodName)) {
			return $vitalSignValue->$methodName($value,$unit);
		}
		return false;
	}

	public static function unitTypeHeadCircumference($unit,$converted=false) {
		return self::unitTypeHeight($unit,$converted);
	}

	public static function unitTypeHeight($unit,$converted=false) {
		$ret = null;
		switch ($unit) {
			case 'IN': // Inch - USS
				$ret = ($converted)?'metric':'uss';
				break;
			case 'CM': // Centimeter - Metric
				$ret = ($converted)?'uss':'metric';
				break;
		}
		return $ret;
	}

	public static function unitTypeWeight($unit,$converted=false) {
		$ret = null;
		switch ($unit) {
			case 'LB': // Pound - USS
				$ret = ($converted)?'metric':'uss';
				break;
			case 'KG': // Kilogram - Metric
				$ret = ($converted)?'uss':'metric';
				break;
		}
		return $ret;
	}

	public static function unitTypeTemperature($unit,$converted=false) {
		$ret = null;
		switch ($unit) {
			case 'F': // Fahrenheit - USS
				$ret = ($converted)?'metric':'uss';
				break;
			case 'C': // Celsius - Metric
				$ret = ($converted)?'uss':'metric';
				break;
		}
		return $ret;
	}

	public static function unitType($vital,$unit,$converted=false) {
		$methodName = 'unitType'.ucfirst($vital);
		$vitalSignValue = new self();
		if (method_exists($vitalSignValue,$methodName)) {
			return $vitalSignValue->$methodName($unit,$converted);
		}
		return false;
	}

	public static function recalculate($vitalSignGroupId) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('vitalSignGroupId = ?',(int)$vitalSignGroupId);
		$factor = 1;
		$bmi = null;
		$bsa = null;
		$height = null;
		$weight = null;
		$iterator = $orm->getIterator($sqlSelect);
		foreach ($iterator as $vital) {
			if ($vital->vital == 'BMI') {
				$bmi = $vital;
			}
			else if ($vital->vital == 'BSA') {
				$bsa = $vital;
			}
			else if ($vital->vital == 'height') {
				$height = $vital;
			}
			else if ($vital->vital == 'weight') {
				$weight = $vital;
			}
		}
		// recalculate BMI
		if ($bmi !== null && ($height !== null && $height->value > 0) && ($weight !== null && $weight->value > 0)) {
			$h = $height->value;
			$w = $weight->value;
			if ($height->units == 'IN' && $weight->units == 'LB') {
				$factor = 703;
			}
			$bmi->value = round((($w / ($h * $h)) * $factor ) * 100) / 100;
			$bmi->persist();
		}
		// recalculate BSA
		if ($bsa !== null && ($height !== null && $height->value > 0) && ($weight !== null && $weight->value > 0)) {
			$h = $height->value;
			$w = $weight->value;
			if ($height->units == 'IN' && $weight->units == 'LB') {
				$factor = 3131;
			}
			else if ($height->units == 'CM' && $weight->units == 'KG') { // TODO: it SHALL be meter and not CM
				$factor = 3600;
			}
			$bsa->value = round(sqrt(($h * $w) / $factor) * 100) / 100;
			$bsa->persist();
		}
	}

}
