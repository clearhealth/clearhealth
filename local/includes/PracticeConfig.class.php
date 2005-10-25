<?php

/**
 * Configuration tied to a clearhealth practice
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @todo	figure out a decent external format for schema information and write a class to load it
 */
class PracticeConfig extends clniConfig {

	var $defaultPracticeId = 0;
	var $practiceId;

	var $_schema = array('CalendarInterval' => array('label'=>'Calendar Interval','type'=>'second'));

	function getSchema() {
		return $this->_schema;
	}

	function PracticeConfig() {
		$me =& Me::getInstance();
		$user =& $me->get_user();
		$this->defaultPracticeId = $user->get('DefaultPracticeId');
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
	 * Get a value for a key, falling back to the clniConfig if it isn't set for this practice
	 *
	 */
	function get($key, $default = null) {
		if (isset($this->_corral[$key])) {
			return $this->_corral[$key];
		}
		else {
			$conf =& Celini::configInstance();
			return $conf->get($key,$default);
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
