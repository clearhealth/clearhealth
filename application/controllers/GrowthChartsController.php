<?php
/*****************************************************************************
*       GrowthChartsController.php
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


class GrowthChartsController extends WebVista_Controller_Action {

	public function indexAction() {
		$personId = (int)$this->_getParam('personId');
		$person = new Person();
		$person->personId = $personId;
		$person->populate();
		$this->view->person = $person;

		$this->view->chartList = GrowthChartBase::listCharts();

		switch (strtolower($person->displayGender)) {
			case 'female':
				$gender = GrowthChartBase::GENDER_FEMALE;
				break;
			case 'male':
			default:
				$gender = GrowthChartBase::GENDER_MALE;
				break;
		}
		$listGrowthCharts = array();
		foreach ($this->view->chartList as $key=>$value) {
			$ormClass = ucfirst($key);
			$orm = new $ormClass();
			$ormIterator = $orm->getIteratorByGender($gender);
			$xMax = 0;
			$yMax = 0;
			$xMin = 0;
			$yMin = 0;
			$percentiles = array();
			$mappings = $orm->_dataTableMappings;
			list($base,$fields) = each($mappings);
			$columns = array();
			foreach ($ormIterator as $row) {
				if ($row->$base > $xMax) {
					$xMax = $row->$base;
				}
				if ($xMin == 0 || $row->$base < $xMin) {
					$xMin = $row->$base;
				}
				foreach ($fields as $field) {
					if ($row->$field > $yMax) {
						$yMax = $row->$field;
					}
					if ($yMin == 0 || $row->$field < $yMin) {
						$yMin = $row->$field;
					}
					$columns[$field]['name'] = substr($field,1);
					$columns[$field]['percentiles'][$row->$base] = $row->$field;
				}
			}
			$listGrowthCharts[$key]['data'] = $orm->listVitals($person);
			// this MUST be called right after $orm->listVitals($person) is called
			$vitalSigns = array();
			foreach (GrowthChartBase::$_vitalSigns as $vitalSign) {
				$dateVitalsTime = strtotime($vitalSign['dateTime']);
				if (!isset($vitalSigns[$vitalSign['vitalSignGroupId']])) {
					list($bYear,$bMonth,$bDay) = explode('-',date('Y-m-d',strtotime($person->dateOfBirth)));
					list($vYear,$vMonth,$vDay) = explode('-',date('Y-m-d',$dateVitalsTime));
					$age = (($vMonth >= $bMonth && $vDay >= $bDay) || ($vMonth > $bMonth))?($vYear - $bYear):($vYear - $bYear - 1);
					$vitalSigns[$vitalSign['vitalSignGroupId']]['label'] = date('m/d/Y h:iA',$dateVitalsTime).': Age='.($age*12).' months';
					$vitalSigns[$vitalSign['vitalSignGroupId']]['data'] = array();
				}
				$value = $vitalSign['value'];
				$ussValue = $value;
				$metricValue = '';
				if (strlen($vitalSign['units']) > 0) {
					if (strlen($ussValue) > 0) {
						$ussValue .= ' '.$vitalSign['units'];
					}
					$ret = VitalSignValue::convertValues($vitalSign['vital'],$value,$vitalSign['units']);
					if ($ret !== false) {
						$ussValue = $ret['uss'];
						$metricValue = $ret['metric'];
					}
				}
				$tmp['data'][] = $ussValue;
				$tmp['data'][] = $metricValue;
				$vitalSigns[$vitalSign['vitalSignGroupId']]['data'][] = $vitalSign['vital'].'='.$metricValue.' ('.$ussValue.')';
			}
			$vitalTxt = array();
			foreach ($vitalSigns as $groupId=>$value) {
				$arr = array();
				foreach ($value['data'] as $val) {
					$arr[] = $val;
				}
				$vitalTxt[] = $value['label'].' '.implode(' ',$arr);
			}
			$listGrowthCharts[$key]['vitalSigns'] = implode("\n",$vitalTxt);
			$listGrowthCharts[$key]['columns'] = $columns;
			$listGrowthCharts[$key]['name'] = GrowthChartBase::prettyName($base);
			$listGrowthCharts[$key]['unit'] = constant("{$ormClass}::BASE_UNIT");
			$listGrowthCharts[$key]['percentileName'] = constant("{$ormClass}::PERCENTILE_NAME");
			$listGrowthCharts[$key]['percentileUnit'] = constant("{$ormClass}::PERCENTILE_UNIT");
			$listGrowthCharts[$key]['xMax'] = ceil($xMax);
			$listGrowthCharts[$key]['yMax'] = ceil($yMax);
			$listGrowthCharts[$key]['xMin'] = floor($xMin);
			$listGrowthCharts[$key]['yMin'] = floor($yMin);
		}
		//file_put_contents('/tmp/growth.charts',print_r($listGrowthCharts,true));
		$this->view->listGrowthCharts = $listGrowthCharts;
		$this->render();
	}

}
