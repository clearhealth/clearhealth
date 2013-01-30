<?php
/*****************************************************************************
*       ClaimRule.php
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


class ClaimRule extends WebVista_Model_ORM {

	protected $claimRuleId;
	protected $title;
	protected $message;
	protected $event;
	protected $type;
	protected $operator;
	protected $code;
	protected $value;
	protected $operand; // AND, OR
	protected $groupId;
	protected $rowOrder;
	protected $dateTime;

	protected $_table = 'claimRules';
	protected $_primaryKeys = array('claimRuleId');
	protected $_cascadePersist = false;

	public static $_operators = array(
		'='=>'=',
		'<'=>'<',
		'<='=>'<=',
		'>'=>'>',
		'>='=>'>=',
		'like'=>'like',
	);

	public static $_types = array(
		1=>'Procedure',
		2=>'Diagnosis',
		3=>'Insurance Program',
		4=>'Modifier',
		5=>'Line Amount',
		6=>'Claim Total',
	);

	public static $_events = array(
		1=>'Warning',
		2=>'Block',
	);

	public static $_operands = array(
		'AND'=>'AND',
		'AND ('=>'AND (',
		'OR'=>'OR',
		'OR ('=>'OR (',
		')'=>')',
	);

	const OPERATOR_EQUALS = '=';
	const OPERATOR_LESS_THAN = '<';
	const OPERATOR_LESS_THAN_EQUALS = '<=';
	const OPERATOR_GREATER_THAN = '>';
	const OPERATOR_GREATER_THAN_EQUALS = '>=';
	const OPERATOR_LIKE = 'like';

	const TYPE_PROCEDURE = 1;
	const TYPE_DIAGNOSIS = 2;
	const TYPE_INSURANCE_PROGRAM = 3;
	const TYPE_MODIFIER = 4;
	const TYPE_LINE_AMOUNT = 5;
	const TYPE_CLAIM_TOTAL = 6;

	const EVENT_WARNING = 1;
	const EVENT_BLOCK = 2;

	const OPERAND_AND = 'AND';
	const OPERAND_AND_OPEN = 'AND (';
	const OPERAND_OR = 'OR';
	const OPERAND_OR_OPEN = 'OR (';
	const OPERAND_CLOSE = ')';

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		return parent::persist();
	}

	public static function listGroups() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$ret = array();
		$sqlSelect = $db->select()
				->from($orm->_table,array('title','groupId','event','message'))
				->order('title')
				->group('groupId');
		$stmt = $db->query($sqlSelect);
		while ($row = $stmt->fetch()) {
			$row['displayEvent'] = isset(self::$_events[$row['event']])?self::$_events[$row['event']]:'';
			$ret[$row['groupId']] = $row;
		}
		return $ret;
	}

	public function getDisplayValue() {
		return $this->operator.' '.(strlen($this->code) > 0)?$this->code.': '.$this->value:$this->value;
	}

	public function populateWithGroupId($groupId=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($groupId === null) $groupId = $this->groupId;
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('groupId = ?',(int)$groupId)
				->limit(1);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public static function checkRules(Visit $visit,Array $fees = array()) {
		if (!isset($fees['details'])) {
			$fees = $visit->calculateFees(true);
		}
		$visitId = (int)$visit->visitId;
		$payerId =(int) $visit->activePayerId;

		$procedures = null;

		$iterator = new ClaimRuleIterator();
		foreach (self::listGroups() as $groupId=>$values) {
			// TODO: use operators
			$matched = false;
			$iterator->setFilters(array('groupId'=>$groupId));
			foreach ($iterator as $claimRule) {
				$type = $claimRule->type;
				switch ($type) {
					case self::TYPE_PROCEDURE:
					case self::TYPE_DIAGNOSIS:
						if ($procedures === null) {
							$procedures = array();
							$patientProcedureIterator = new PatientProcedureIterator();
							$patientProcedureIterator->setFilters(array('visitId'=>$visitId));
							foreach ($patientProcedureIterator as $patientProcedure) {
								$procedures[] = $patientProcedure;
							}
						}
						$code = $claimRule->code;
						foreach ($procedures as $procedure) {
							if ($type == self::TYPE_PROCEDURE) {
								if ($procedure->code == $code) {
									$matched = true;
									break;
								}
							}
							else {
								for ($i=1;$i<=8;$i++) {
									$key = 'diagnosisCode'.$i;
									if ($procedure->$key == $code) {
										$matched = true;
										break 2;
									}
								}
							}
						}
						break;
					case self::TYPE_INSURANCE_PROGRAM:
						if ($claimRule->value == $payerId) {
							$matched = true;
						}
						break;
					case self::TYPE_MODIFIER:
						break;
					case self::TYPE_LINE_AMOUNT:
						break;
					case self::TYPE_CLAIM_TOTAL:
						break;
				}
			}
			if ($matched) $visit->_claimRule = $values;
		}
		return $visit;
	}

}
