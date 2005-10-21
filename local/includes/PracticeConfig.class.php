<?php

/**
 * Configuration tied to a clearhealth practice
 *
 * @package	com.uversainc.clearhealth
 * @uathor	Joshua Eichorn <jeichorn@mail.com>
 */
class PracticeConfig extends clniConfig {

	var $defaultPracticeId = 0;
	var $practiceId;

	function PracticeConfig() {
		$this->loadPractice($this->defaultPracticeId);
	}

	/**
	 * Load config for a given practice
	 */
	function loadPractice($practiceId) {
		$db = new clniDb();

		$typeEnforcer = new EnforceType();
		$this->practiceId = $typeEnforcer->int($practiceId);
		$res = $db->execute("select name, value, serialized from practice_setting where practice_id = $this->practiceId");

		while($res && !$res->EOF) {
			if ($res->fields['serialized']) {
				$this->_corral[$res->fields['name']] = unserialize($res->fields['value']);
			}
			else {
				$this->_corral[$res->fields['name']] = $res->fields['value'];
			}
			$res->MoveNext();
		}

	}

	/**
	 * Set a property
	 *
	 * @param string	$key
	 * @param mixed		$value
	 */
	function set($key,$value) {
		$ps =& Celini::newORDO('PracticeSetting',array($key,$this->practiceId),'name');

		if (is_object($value) || is_array($value)) {
			$value = serialize($value);
			$ps->set('serialized',1);
		}

		$ps->set('value',$value);
		$ps->persist();
	}
	
}
?>
