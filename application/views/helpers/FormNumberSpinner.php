<?php
/*****************************************************************************
*       FormNumberSpinner.php
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

class Zend_View_Helper_FormNumberSpinner extends Zend_View_Helper_FormElement {

	public function formNumberSpinner($name, $value = null, $attribs = null) {
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		// build the element
		$disabled = '';
		if ($disable) {
			// disabled
			$disabled = ' disabled="disabled"';
		}

		$maxlength = '';
		if (isset($attribs['maxlength'])) {
			$maxlength = ',maxlength:'.(int)$attribs['maxlength'];
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
		if (!strlen($sanitized['value']) > 0) {
			$sanitized['value'] = 0;
		}

		$xhtml = '<input type="text"' . ' name="' . $sanitized['name'] . '"' . ' id="' . $sanitized['id'] . '"' . ' value="' . $sanitized['value'] . '"' . $disabled . $this->_htmlAttribs($attribs) . $this->getClosingBracket();

		$varName = str_replace(' ','',ucwords(str_replace('-',' ',$sanitized['id'])));

		// min:-10.9,max:155,places:1,round:true,exponent:false
		$constraints = 'round:-1,min:0,places:0';
		if (isset($attribs['places'])) {
			$constraints = 'round:-1,min:0,places:'.$attribs['places'];
		}
		if (isset($attribs['max'])) {
			$constraints .= ',max:'.$attribs['max'];
		}

		$disabled = ((bool)$disabled)?'true':'false';
		$validate = "";
		if (isset($attribs['validate'])) { 
			$validate = "validate:function (input) {" . $attribs['validate'] . "},";
		}
		$xhtml .= <<<EOL
<script>
var spin{$varName} = dijit.byId("{$sanitized['id']}");
if (typeof spin{$varName} != "undefined") {
	spin{$varName}.destroyRecursive();
	spin{$varName} = null;
}
spin{$varName} = new dijit.form.NumberSpinner({name:"{$sanitized['name']}",value:"{$sanitized['value']}",{$validate}constraints:{{$constraints}},disabled:{$disabled},style:"{$style}"{$maxlength},intermediateChanges:true},dojo.byId("{$sanitized['id']}"));
</script>
EOL;
		return $xhtml;
	}
}
