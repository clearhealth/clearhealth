<?php
/*****************************************************************************
*       FormTimeSpinner.php
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

/** Zend_View_Helper_FormElement */
require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_FormTimeSpinner extends Zend_View_Helper_FormElement {

	public function formTimeSpinner($name, $value = null, $attribs = null) {
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		// build the element
		$disabled = '';
		if ($disable) {
			// disabled
			$disabled = ' disabled="disabled"';
		}

		$smallDelta = 5;
		if (isset($attribs['smallDelta'])) {
			$smallDelta = (int)$attribs['smallDelta'];
		}
		$style = 'width:90px;';
		if (isset($attribs['style'])) {
			$style = $attribs['style'];
		}
        
		if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
			$endTag= '>';
		}

		$sanitized = array();
		$sanitized['name'] = $this->view->escape($name);
		$sanitized['id'] = $this->view->escape($id);
		$sanitized['value'] = $this->view->escape($value);
		if ($sanitized['value'] == '') {
			$sanitized['value'] = '12:00 AM';
		}
		if ($sanitized['id'] == '') {
			$sanitized['id'] = $sanitized['name'];
		}

		$xhtml = '<input type="text"' . ' name="' . $sanitized['name'] . '"' . ' id="' . $sanitized['id'] . '"' . ' value="' . $sanitized['value'] . '"' . $disabled . $this->_htmlAttribs($attribs) . $this->getClosingBracket();

		$varName = str_replace(' ','',str_replace('-',' ',$sanitized['id']));

		$disabled = ((bool)$disabled)?'true':'false';
		$xhtml .= <<<EOL
<script>
var {$varName}TimeSpin = dijit.byId("{$sanitized['id']}");
if (typeof {$varName}TimeSpin != "undefined") {
	{$varName}TimeSpin.destroyRecursive();
	{$varName}TimeSpin = null;
}
{$varName}TimeSpin = new dojox.widget.TimeSpinner({name:"{$sanitized['name']}",value:"{$sanitized['value']}",disabled:{$disabled},smallDelta:{$smallDelta},style:"{$style}",intermediateChanges:true},dojo.byId("{$sanitized['id']}"));
</script>
EOL;
		return $xhtml;
	}
}
