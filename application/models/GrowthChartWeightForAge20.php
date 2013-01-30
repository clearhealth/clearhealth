<?php
/*****************************************************************************
*       GrowthChartWeightForAge20.php
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


class GrowthChartWeightForAge20 extends GrowthChartBase {

	protected $growthChartWeightForAge20Id;
	protected $age;

	const BASE_UNIT = 'Months';
	const PERCENTILE_NAME = 'Weight';
	const PERCENTILE_UNIT = 'kg';

	protected $_table = 'growthChartWeightForAge20';
	protected $_primaryKeys = array('growthChartWeightForAge20Id');

	public function listVitals(Person $person) {
		$dateOfBirth = strtotime($person->dateOfBirth);
		$dateBegin = date('Y-m-d',strtotime('+24 months',$dateOfBirth));
		$dateEnd = date('Y-m-d 23:59:59',strtotime('+240 months',$dateOfBirth));
		$vitals = array();

		$filters = array();
		$filters['personId'] = (int)$person->personId;
		$filters['dateBegin'] = $dateBegin;
		$filters['dateEnd'] = $dateEnd;
		$filters['vitals'] = array('weight');
		$data = GrowthChartBase::getAllVitals($filters,$dateOfBirth);
		foreach ($data as $age=>$row) {
			$vital = array();
			$vital['x'] = $age;
			$vital['y'] = $row['weight'];
			$vitals[] = $vital;
		}
		return $vitals;
	}

}
