<?php
/*****************************************************************************
*       GrowthChartWeightForStature20.php
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


class GrowthChartWeightForStature20 extends GrowthChartBase {

	protected $growthChartWeightForStature20Id;
	protected $stature;
	protected $p85th;

	const BASE_UNIT = 'cm';
	const PERCENTILE_NAME = 'Weight';
	const PERCENTILE_UNIT = 'kg';

	protected $_table = 'growthChartWeightForStature20';
	protected $_primaryKeys = array('growthChartWeightForStature20Id');

	protected $_dataTableMappings = array(
		'stature' => array(
			'p3rd',
			'p5th',
			'p10th',
			'p25th',
			'p50th',
			'p75th',
			'p85th',
			'p90th',
			'p95th',
			'p97th',
		)
	);

	public function listVitals(Person $person) {
		$dateOfBirth = strtotime($person->dateOfBirth);
		$dateBegin = date('Y-m-d',strtotime('+24 months',$dateOfBirth));
		$dateEnd = date('Y-m-d 23:59:59',strtotime('+240 months',$dateOfBirth));
		$vitals = array();

		$filters = array();
		$filters['personId'] = (int)$person->personId;
		$filters['dateBegin'] = $dateBegin;
		$filters['dateEnd'] = $dateEnd;
		$filters['vitals'] = array('height','weight');
		$data = GrowthChartBase::getAllVitals($filters);
		foreach ($data as $row) {
			$vital = array();
			$vital['x'] = $row['height'];
			$vital['y'] = $row['weight'];
			$vitals[] = $vital;
		}
		return $vitals;
	}

}
