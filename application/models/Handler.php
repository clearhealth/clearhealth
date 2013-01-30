<?php
/*****************************************************************************
*       Handler.php
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


class Handler extends DataIntegration {

	protected $handlerId;
	protected $guid;
	protected $name;
	protected $direction;
	protected $condition;
	protected $conditionObject;
	protected $action;
	protected $active;
	protected $review;
	protected $resolve;
	protected $timeframe;
	protected $handlerType;
	protected $dataIntegrationDatasourceId;
	protected $dataIntegrationDatasource;
	protected $dataIntegrationTemplateId;
	protected $dataIntegrationTemplate;
	protected $dataIntegrationDestinationId;
	protected $dataIntegrationDestination;
	protected $dataIntegrationActionId;
	protected $dataIntegrationAction;

	protected $_table = 'handlers';
	protected $_primaryKeys = array('handlerId');

	protected $_cascadePersist = false;

	const HANDLER_TYPE_HL7 = 0; // HL7
	const HANDLER_TYPE_GA = 1; // General Alerts
	const HANDLER_TYPE_HSA = 2; // Health Status Alerts

	public function __construct($handlerType = 0) {
		parent::__construct($handlerType);
		$this->dataIntegrationDatasource = new DataIntegrationDatasource($handlerType);
		$this->dataIntegrationDatasource->_cascadePersist = false;
		$this->dataIntegrationTemplate = new DataIntegrationTemplate($handlerType);
		$this->dataIntegrationTemplate->_cascadePersist = false;
		$this->dataIntegrationDestination = new DataIntegrationDestination($handlerType);
		$this->dataIntegrationDestination->_cascadePersist = false;
		$this->dataIntegrationAction = new DataIntegrationAction($handlerType);
		$this->dataIntegrationAction->_cascadePersist = false;
	}

	/**
	 * Returns list of conditions
	 * @return array
	 */
	public static function listConditions() {
		$db = Zend_Registry::get('dbAdapter');
		$audit = new Audit();
		$sqlSelect = $db->select()
				->from($audit->_table)
				->where('auditId != 0')
				->group('objectClass')
				->group('type');
		$conditions = $audit->getIterator($sqlSelect);
		$listConditions = array();
		foreach ($conditions as $condition) {
			$prettyName = $condition->objectClass . '->';
			switch ($condition->type) {
				case WebVista_Model_ORM::REPLACE:
					$prettyName .= __('Replace');
					break;
				case WebVista_Model_ORM::INSERT:
					$prettyName .= __('Insert');
					break;
				case WebVista_Model_ORM::UPDATE:
					$prettyName .= __('Update');
					break;
				case WebVista_Model_ORM::DELETE:
					$prettyName .= __('Delete');
					break;
				default:
					$prettyName .= __('Unknown');
					break;
			}
			$listConditions[$condition->auditId] = $prettyName;
		}
		return $listConditions;
	}

	/**
	 * Returns direction list
	 * @return array
	 */
	public static function listDirections() {
		$directions = array();
		$directions['INCOMING'] = 'INCOMING';
		$directions['OUTGOING'] = 'OUTGOING';
		return $directions;
	}

	/**
	 * Generate default object for condition based on handler type
	 * @param string Handler Type
	 * @return string PHP code for conditionObject
	 */
	public function generateDefaultConditionObject($handlerType = null) {
		if ($handlerType === null) {
			$handlerType = $this->handlerType;
		}
		switch ($handlerType) {
			case self::HANDLER_TYPE_GA: // General Alert
				return $this->generateAlertsDefaultConditionObject();
				break;
			case self::HANDLER_TYPE_HSA: // Health Status Alert
				return $this->generateHSADefaultConditionObject();
				break;
			default: // HL7
				return $this->generateHL7DefaultConditionObject();
				break;
		}
	}

	/**
	 * Generate HL7 default object for condition
	 * @return string PHP code for conditionObject
	 */
	public function generateHL7DefaultConditionObject() {
		$audit = new Audit();
		$audit->auditId = $this->condition;
		$audit->populate();

		$handlerName = self::normalizeHandlerName($this->name);

		$conditionHandler = '';
		$objectClass = $audit->objectClass;

		if (strlen($objectClass) > 0 && class_exists($objectClass)) {
			$conditionHandler .= <<<EOL
		if (\$auditOrm->objectClass == '{$objectClass}' && \$auditOrm->type == '{$audit->type}') {
			\$ret = true;
		}
EOL;

		}

		$conditionObject = <<<EOL

class {$handlerName}ConditionHandler extends DataIntegrationConditionHandlerAbstract {
	//abstract requires at least this method
	public static function matchAudit(Audit \$auditOrm) {
		\$ret = false;
{$conditionHandler}
		return \$ret;
	}
}

EOL;
		return $conditionObject;
	}

	/**
	 * Generate Alerts default object for condition
	 * @return string PHP code for conditionObject
	 */
	public function generateAlertsDefaultConditionObject() {
		$audit = new Audit();
		$audit->auditId = $this->condition;
		$audit->populate();

		$handlerName = self::normalizeHandlerName($this->name);

		$conditionHandler = '';
		$dataIntegrationDatasource = '';
		$objectClass = $audit->objectClass;
		if (strlen($objectClass) > 0 && class_exists($objectClass)) {
			$tmp = new $objectClass();

			$conditionHandler .= <<<EOL
		if (\$auditOrm->objectClass == '{$objectClass}' && \$auditOrm->type == '{$audit->type}') {
			return true;
		}
EOL;

			$dataIntegrationDatasource .= <<<EOL
		if (class_exists('{$objectClass}')) {
			\$orm = new {$objectClass}();
EOL;
			foreach ($tmp->_primaryKeys as $key) {
				$dataIntegrationDatasource .= <<<EOL

			\$orm->{$key} = {$audit->objectId};
EOL;
			}
			$dataIntegrationDatasource .= <<<EOL

			\$orm->populate();
			\$a2 = \$orm->toArray();
		}
EOL;
		}

		$conditionObject = <<<EOL

class {$handlerName}ConditionHandler extends DataIntegrationConditionHandlerAbstract {
	//abstract requires at least this method
	public static function matchAudit(Audit \$auditOrm) {
{$conditionHandler}
		return false;
	}
}

class {$handlerName}DataIntegrationDatasource extends DataIntegrationDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(Audit \$audit) {
		\$a1 = \$audit->toArray();
		\$a2 = array();
{$dataIntegrationDatasource}
		return array_merge(\$a1,\$a2);
	}
}

EOL;
		return $conditionObject;
	}

	/**
	 * Generate Health Status Alerts default object for condition
	 * @return string PHP code for conditionObject
	 */
	public function generateHSADefaultConditionObject() {
		$audit = new Audit();
		$audit->auditId = $this->condition;
		$audit->populate();

		$handlerName = self::normalizeHandlerName($this->name);

		$conditionHandler = '';
		$dataIntegrationDatasource = '';
		$objectClass = $audit->objectClass;
		if (strlen($objectClass) > 0 && class_exists($objectClass)) {
			$tmp = new $objectClass();

			$conditionHandler .= <<<EOL
		if (\$auditOrm->objectClass == '{$objectClass}' && \$auditOrm->type == '{$audit->type}') {
			return true;
		}
EOL;

			$dataIntegrationDatasource .= <<<EOL
		if (class_exists('{$objectClass}')) {
			\$orm = new {$objectClass}();
EOL;
			foreach ($tmp->_primaryKeys as $key) {
				$dataIntegrationDatasource .= <<<EOL

			\$orm->{$key} = {$audit->objectId};
EOL;
			}
			$dataIntegrationDatasource .= <<<EOL

			\$orm->populate();
			\$ret = \$orm->toArray();
		}
EOL;
		}

		$conditionObject = <<<EOL

class {$handlerName}ConditionHandler extends DataIntegrationConditionHandlerAbstract {
	//abstract requires at least this method
	public static function matchAudit(Audit \$auditOrm) {
{$conditionHandler}
		return false;
	}

	public static function fulfill(HealthStatusAlert \$alert) {
		\$alert->status = 'fulfilled';
		\$alert->persist();
	}

	public static function patientMatch(Handler \$handler,Patient \$patient) {
		// get/retrieve patient alert using personId of \$patient and handlerId of \$handler
		\$alert = new HealthStatusAlert();
		\$alert->populateByHandlerPatientId(\$handler->handlerId,\$patient->personId);
		if (strlen(\$alert->status) > 0) {
			//return true;
		}
		if (\$alert->status == 'active' || \$alert->status == 'fulfilled' || \$alert->status == 'ignored') {
			return false;
		}
		return true;
	}
}

class {$handlerName}DataIntegrationDatasource extends DataIntegrationDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(Audit \$auditOrm) {
		\$ret = array();
{$dataIntegrationDatasource}
		return \$ret;
	}
}

EOL;
		return $conditionObject;
	}

	/**
	 * Normalized the given handler name
	 * @param string $name Handler name
	 * @return string Normalized handler name
	 */
	public static function normalizeHandlerName($name) {
		$name = ucwords($name);
		$name = preg_replace('/\ /','',$name);
		$name = preg_replace('/[^a-z0-9]/i','_',$name);
		if (is_numeric(substr($name,0,1))) {
			$name = '_'.$name;
		}
		return $name;
	}

}
