<?php
/*****************************************************************************
*       NQF.php
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


abstract class NQF {

	protected $tthis;
	protected $context;
	protected $data;
	protected $dateStart;
	protected $dateEnd;
	protected $providerId;
	protected static $info = array();

	abstract public function populate();
	abstract public static function getResults();

	public static function getInfo() {
		return self::$info;
	}

	public function __construct($tthis,$context,$data) {
		$this->tthis = $tthis;
		$this->context = $context;
		$this->data = $data;
		$year = 0;
		if (isset($tthis->_attributes['year'])) $year = (int)$tthis->_attributes['year'];
		if (!$year > 0) $year = date('Y') - 1; // default year to previous
		$this->dateStart = date('Y-m-d',strtotime($year.'-01-01'));
		$this->dateEnd = date('Y-m-d',strtotime($year.'-12-31'));
		$this->providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$providerId = (int)$this->context;
		if ($providerId > 0) $this->providerId = $providerId;
		$info = array();
		$info['dateStart'] = $this->dateStart;
		$info['dateEnd'] = $this->dateEnd;
		$provider = new Provider();
		$provider->personId = $this->providerId;
		$provider->populate();
		$info['provider'] = $provider;
		self::$info = $info;
	}

	protected function _formatCodeList(Array $codes) {
		$genericCodeList = array();
		$codeList = array();
		foreach ($codes as $code) {
			$genericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$codeList[] = "'$code'";
		}
		return array('generic'=>$genericCodeList,'code'=>$codeList);
	}

	public static function calculatePerformanceMeasure($denominator,$numerator,$exclusions = 0) {
		if ($denominator === 0 && $numerator === 0) return '0%'; // Division by zero
		$percentage = ($numerator / ($denominator - $exclusions)) * 100;
		if ($percentage < 0) $percentage = 0;
		return str_replace('.00','',sprintf("%.2f",$percentage)).'%';
	}

}
