<?php
/*****************************************************************************
*       FormDateText.php
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

class Zend_View_Helper_FormDateText extends Zend_View_Helper_FormElement {

	public function formDateText($name, $value = null, $attribs = null) {
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		// build the element
		$disabled = '';
		if ($disable) {
			// disabled
			$disabled = ' disabled="disabled"';
		}
        
		if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
			$endTag= '>';
		}

		$sanitized = array();
		$sanitized['name'] = $this->view->escape($name);
		$sanitized['id'] = $this->view->escape($id);
		$sanitized['value'] = $this->view->escape($value);

		$xhtml = '<input type="text"' . ' name="' . $sanitized['name'] . '"' . ' id="' . $sanitized['id'] . '"' . ' value="' . $sanitized['value'] . '"' . $disabled . $this->_htmlAttribs($attribs) . $this->getClosingBracket();

		$varName = str_replace(' ','',ucwords(str_replace('-',' ',$sanitized['id'])));

		$dateValue = date('Y-m-d',strtotime($sanitized['value']));
		if ($dateValue == '1969-12-31') {
			$dateValue = date('Y-m-d');
		}
		$x = explode('-',$dateValue);
		if (count($x) >= 3) {
			$x[0] = (int)$x[0];
			$x[1] = (int)$x[1] - 1;
			$x[2] = (int)$x[2];
		}
		else {
			$x = array(date('Y'),date('m')-1,date('d'));
		}
		$disabled = ((bool)$disabled)?'true':'false';
		$style = 'width:90px;';
		if (isset($attribs['style'])) {
			$style = $attribs['style'];
		}
		$xhtml .= <<<EOL
<script>
var box{$varName} = dijit.byId("{$sanitized['id']}");
if (typeof box{$varName} != "undefined") {
	box{$varName}.destroyRecursive();
	box{$varName} = null;
}
box{$varName} = new dijit.form.DateTextBox({name:"{$sanitized['name']}",constraints:{datePattern:"yyyy-MM-dd"},disabled:{$disabled},style:"{$style}"},dojo.byId("{$sanitized['id']}"));
var date{$varName} = new Date();
date{$varName}.setFullYear({$x[0]},{$x[1]},{$x[2]});
box{$varName}.setValue(date{$varName});
</script>
EOL;
		return $xhtml;
	}
}
