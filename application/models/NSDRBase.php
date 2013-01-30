<?php
/*****************************************************************************
*       NSDRBase.php
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

/**
 * Independent class specific for NSDR as its Base
 */

class NSDRBase {

	public function aggregateDisplay($tthis,$context,$data) {
		$ret = '';
		$values = $this->_display($data);
		$ret = implode(' ',$values);
		return $ret;
	}

	public function aggregateDisplayByLine($tthis,$context,$data) {
		$ret = '';
		$values = $this->_display($data,1);
		$ret = implode("\n",$values);
		return $ret;
	}

	private function _display($data,$level=0) {
		$values = array();
		foreach ($data as $key=>$val) {
			if (preg_match('/([a-z])Id$/',$key) || preg_match('/([a-z])_id$/',$key)) continue;
			$value = str_repeat(' ',$level);
			if ($val instanceof ORM) $val = $val->toArray();
			if (is_array($val)) {
				$nextLevel = $level;
				if (!is_numeric($key)) {
					$pads = str_repeat('*',$level);
					$value .= $pads.self::prettyName($key).$pads;
					$nextLevel = $level + 1;
				}
				$value .= "\n".implode("\n",$this->_display($val,$nextLevel));
				if (strlen(trim($value)) > 0) $values[] = $value;
			}
			else {
				if (!is_numeric($key)) $value .= self::prettyName($key).': ';
				$value .= ucwords($val);
				if (strlen(trim($value)) > 0) $values[] = $value;
			}
		}
		return $values;
	}

	public static function prettyName($name) {
		$name = trim($name);
		$name = preg_replace('/([a-z])Id$/','$1',$name);
		$name = preg_replace('/([a-z])_id$/','$1',$name);
		$name = preg_replace('/([A-Z])(?![A-Z])/',' $1',$name);
		$name = preg_replace('/_/',' ',$name);
		$name = ucwords($name);
		return trim($name);
	}

}
