<?php
/*****************************************************************************
*       ProcessAbstract.php
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
 * Abstract process
 * HL7 messages, user alerts and health status alerts implement this abstract class
 */
abstract class ProcessAbstract {

	public function preProcess(Audit $audit) {
		// nothing to do here. this can be overridden
	}

	abstract public function process(Audit $audit);

	public function postProcess(Audit $audit) {
		// nothing to do here. this can be overridden
	}

	public function extraProcess() {
		// nothing to do here. this can be overridden
	}

	public static function isImplementOf($className,$abstractName) {
		$ok = false;
		if (class_exists($className)) {
			$rc = new ReflectionClass($className);
			$interfaces = $rc->getInterfaceNames();
			if (in_array($abstractName,$interfaces)) {
				$ok = true;
			}
		}
		return $ok;
	}

	public static function isParentOf($className,$abstractName) {
		$ok = false;
		if (class_exists($className)) {
			$rc = new ReflectionClass($className);
			$parent = $rc->getParentClass();
			$ok = ($parent->getName() == $abstractName);
		}
		return $ok;
	}

	protected function _evaluateCodes($handler) {
		if (!($handler instanceof HealthStatusHandler) && !($handler instanceof GeneralAlertHandler)) {
			return;
		}
		try {
			$cacheCodeObjects = Zend_Registry::get('cacheCodeObjects');
		} catch (Exception $e) {
			$cacheCodeObjects = array();
		}
		$handlerObject = $handler->handlerObject;
		if (!strlen($handlerObject) > 0) {
			$handlerObject = $handler->generateDefaultHandlerObject();
		}
		$md5 = md5($handlerObject);
		if (!in_array($md5,$cacheCodeObjects)) {
			$cacheCodeObjects[] = $md5;
			eval($handlerObject); // TODO: needs to be validated
		}

		$datasource = $handler->datasource;
		if (!strlen($datasource) > 0) {
			$datasource = $handler->generateDefaultDatasource();
		}
		$md5 = md5($datasource);
		if (!in_array($md5,$cacheCodeObjects)) {
			$cacheCodeObjects[] = $md5;
			eval($datasource); // TODO: needs to be validated
		}
		Zend_Registry::set('cacheCodeObjects',$cacheCodeObjects);
	}

}
