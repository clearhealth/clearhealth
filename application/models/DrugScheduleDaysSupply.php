<?php
/*****************************************************************************
*       DrugScheduleDaysSupply.php
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


class DrugScheduleDaysSupply {

	protected static function _computeXID($quantity,$divisor) {
		$quantity = (int)$quantity;
		$ret = $quantity / $divisor;
		if ($quantity % $divisor !== 0) {
			$ret++;
		}
		return (int)$ret;
	}

	public static function BID($quantity) {
		return self::_computeXID($quantity,2);
	}

	public static function TID($quantity) {
		return self::_computeXID($quantity,3);
	}

	public static function MOWEFR($quantity) {
		$quantity = (int)$quantity;
		return $quantity;
	}

	public static function NOW($quantity) {
		$quantity = (int)$quantity;
		return $quantity;
	}

	public static function ONCE($quantity) {
		$quantity = (int)$quantity;
		return $quantity;
	}
	public static function OTHER($quantity) {
		$quantity = (int)$quantity;
		return $quantity;
	}

	protected static function _computeQXH($quantity,$multiplier) {
		$quantity = (int)$quantity;
		$quantity *= $multiplier;
		return self::_computeXID($quantity,24);
	}

	public static function Q12H($quantity) {
		return self::_computeQXH($quantity,12);
	}

	public static function Q24H($quantity) {
		return self::_computeQXH($quantity,24);
	}

	public static function Q2H($quantity) {
		return self::_computeQXH($quantity,2);
	}

	public static function Q3H($quantity) {
		return self::_computeQXH($quantity,3);
	}

	public static function Q4H($quantity) {
		return self::_computeQXH($quantity,4);
	}

	public static function Q6H($quantity) {
		return self::_computeQXH($quantity,6);
	}

	public static function Q8H($quantity) {
		return self::_computeQXH($quantity,8);
	}

	public static function Q5MIN($quantity) {
		$quantity = (int)$quantity;
		$quantity *= 5;
		return self::_computeXID($quantity,1440);
	}

	public static function QDAY($quantity) {
		$quantity = (int)$quantity;
		return $quantity;
	}

}

