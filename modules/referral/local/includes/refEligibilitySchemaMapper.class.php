<?php
$GLOBALS['loader']->requireOnce('/includes/EnumManager.class.php');

/**
 * UNSTABLE
 *
 * This code may very well change and have the bulk of it moved into Celini for
 * re-use purposes.
 *
 * The idea with this is to allow program managers to select the eligibility 
 * schema for a given program from the refEligibilitySchema enum.  That stored
 * value will be accessed again when a referral request is being made via
 * $program->get('schema_input') - a virtual accessor on refProgram.  That will
 * utilize this class to determine what type of input(s) to display.
 *
 * The two types of schemas are another enum (in which the extra1 field of the
 * enum refers to the name of another enum) and classes (in which case the 
 * extra1 field refers to a the name of another class).  In the case of another
 * enum, this will create a series of radio boxes and return them when toInput()
 * is called.
 *
 * In the case of a class, this should load the other class, and call toInput()
 * on it and return its results.  <b>NOTE</b>: This behavior has not been
 * implemented.  This code has been put here as a flex point to allow for this
 * at a future time when it is required.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class refEligibilitySchemaMapper
{
	var $_schemaTitle = null;
	var $_schemaObjectName = '';
	
	var $_inputString = null;
	var $_type = '';
	
	/**
	 * The default input string if <i>$schema == 0</i> on construct
	 *
	 * @var string
	 */
	var $defaultInputString = 'Not Applicable';
	
	
	/**
	 * The name attribute of the inputs to be generated
	 *
	 * @var string
	 */
	var $inputName = 'refPatientEligibility';
	
	
	/**
	 * @todo Break this apart.  Simple solution: multiple methods for each
	 *   different type.  More robust: Strategy Pattern
	 */
	function refEligibilitySchemaMapper($schema) {
		if ($schema == 0) {
			$this->_inputString = $this->defaultInputString;
		}
		else {
			$this->_init($schema);
		}
	}
	
	
	/**
	 * @access private
	 */
	function _init($schema) {
		$em =& EnumManager::getInstance();
		$this->_schemaTitle = $em->lookup('refEligibilitySchema', $schema);
		
		$enumList =& $em->enumList('refEligibilitySchema');
		$enumList->rewind();
		while ($enumList->valid()) {
			$enum =& $enumList->current();
			if ($enum->value == $this->_schemaTitle) {
				$this->_schemaObjectName = $enum->extra1;
				break;
			}
			$enumList->next();
		}
		unset($enum);
		
		// todo: move check to EnumManager at some point?  Maybe EnumManager::enumExists()
		$enum =& Celini::newORDO('EnumerationDefinition', $this->_schemaObjectName, 'ByName');
		if ($enum->isPopulated()) {
			$this->_type = 'enum';
		}
		// Check for schema class.
		else {
			trigger_error('Unable to process non-Enumeration schemas currently.');
		}
	}
	
	function toInput($selected = null, $disabled = false) {
		if (is_null($this->_inputString)) {
			$this->_initInput($selected, $disabled);
		}
		return $this->_inputString;
	}
	
	/**
	 * @access private
	 */
	function _initInput($selected, $disabled) {
		if ($this->_type == 'enum') {
			if ($disabled) {
				$this->_buildEnumInput($selected, 'disabled="disabled"');
			}
			else {
				$this->_buildEnumInput($selected);
			}
		}
	}
	
	/**
	 * @access private
	 */
	function _buildEnumInput($selected, $extraHtml = '') {
		$em =& EnumManager::getInstance();
		$array = $em->enumArray($this->_schemaObjectName);
		$i = 0;
		foreach ($array as $value => $text) {
			$id = $this->inputName . '__eligibility__' . $i;
			$this->_inputString .= '<label style="white-space:nowrap;" for="' . $id . '"><input id="' . $id . '" type="radio" name="' . $this->inputName . '[eligibility]" value="' . $value . '"';
			if (!is_null($selected) && $selected == $value) {
				$this->_inputString .= ' checked="checked"';
			}
			$this->_inputString .= " {$extraHtml} />{$text}</label>";
			$i++;
		}
	}
	
	function toList($selected = '') {
		if ($this->_type == 'enum') {
			$this->_buildEnumList($selected);
			return $this->_inputString;
		}
	}
	
	/**
	 * Use _buildEnumInput() instead
	 * @deprecated
	 * @access private
	 */
	function _buildEnumList($selected, $style = ' style="display:inline;"') {
		$this->_inputString = '';
		$this->_buildEnumInput($selected, 'disabled="disabled"');
	}
	
}
