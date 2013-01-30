<?php
/*****************************************************************************
*       ProcessAlert.php
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


class ProcessAlert extends ProcessAbstract {

	protected $_handlers = array();

	protected function _populateHandlers() {
		$this->_handlers = array();
		try {
			$cacheCodeObjects = Zend_Registry::get('cacheCodeObjects');
		} catch (Exception $e) {
			$cacheCodeObjects = array();
		}
		$handler = new GeneralAlertHandler();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($handler->_table)
				->where('active = 1');
		$handlerIterator = $handler->getIterator($sqlSelect);
		foreach ($handlerIterator as $item) {
			$this->_evaluateCodes($item);
			$this->_handlers[] = $item;
		}
		Zend_Registry::set('cacheCodeObjects',$cacheCodeObjects);
	}

	/**
	 * Process condition and do action
	 * @param Audit $audit Audit ORM
	 * @param Handler $handler Handler ORM
	 * @return boolean Return TRUE if successful, FALSE otherwise
	 */
	public function process(Audit $audit) {
		$this->_populateHandlers();

		$ret = true;
		foreach ($this->_handlers as $handler) {
			$result = $this->_doProcess($handler,$audit);
			if ($result === false) {
				// log processing errors
				$processingError = new ProcessingError();
				$processingError->_shouldAudit = false;
				$processingError->auditId = $audit->auditId;
				$processingError->handlerId = $handler->handlerId;
				$processingError->persist();
			}
			$ret &= $result;
		}

		return $ret;
	}

	protected function _doProcess(GeneralAlertHandler $handler,Audit $audit) {
		$handlerName = Handler::normalizeHandlerName($handler->name);
		$classHandlerObject = $handlerName.'GeneralAlertHandlerObject';
		if (!parent::isParentOf($classHandlerObject,'GeneralAlertHandlerObjectAbstract')) {
			trigger_error($classHandlerObject.' is not an instance of GeneralAlertHandlerObjectAbstract',E_USER_NOTICE);
			return false;
		}
		$ret = false;

		if (call_user_func_array(array($classHandlerObject,'matchAudit'),array($audit))) {
			do {
				$classDatasource = $handlerName.'GeneralAlertDatasource';
				if (!parent::isParentOf($classDatasource,'GeneralAlertDatasourceAbstract')) {
					trigger_error($classDatasource.' is not an instance of GeneralAlertDatasourceAbstract',E_USER_NOTICE);
					break;
				}
				try {
					$data = call_user_func_array(array($classDatasource,'sourceData'),array($audit));
				}
				catch (Exception $e) {
					trigger_error('Exception error ('.$e->getCode().'): '.$e->getMessage(),E_USER_NOTICE);
					break;
				}

				$ret = true;

				if (!strlen($handler->template) > 0) {
					$handler->template = $handler->generateDefaultTemplate();
				}
				foreach ($data as $row) {
					$message = TemplateXSLT::render($row,$handler->template);
					$generalAlert = new GeneralAlert();
					$generalAlert->message = $message;
					$generalAlert->urgency = 'Med';
					$generalAlert->status = 'new';
					$generalAlert->dateTime = date('Y-m-d H:i:s');
					if (isset($row['teamId'])) {
						$generalAlert->teamId = $row['teamId'];
					}
					if (isset($row['signingUserId'])) {
						$generalAlert->userId = $row['signingUserId'];
					}
					if (isset($row['objectId'])) {
						$generalAlert->objectId = $row['objectId'];
					}
					if (isset($row['objectClass'])) {
						$generalAlert->objectClass = $row['objectClass'];
					}
					$generalAlert->persist();
				}
			} while(false);
		}
		return $ret;
	}

}
