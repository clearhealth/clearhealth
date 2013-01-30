<?php
/*****************************************************************************
*       FormCurrencyText.php
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

class Zend_View_Helper_FormCurrencyText extends Zend_View_Helper_FormElement {

	public function formCurrencyText($name, $value = null, $attribs = null) {
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		// build the element
		$style = 'width:90px;';
		if (isset($attribs['style'])) {
			$style = $attribs['style'];
		}

		$sanitized = array();
		$sanitized['name'] = $this->view->escape($name);
		$sanitized['id'] = $this->view->escape($id);
		$sanitized['value'] = $this->view->escape($value);
		if (!strlen($sanitized['value']) > 0) {
			$sanitized['value'] = 0;
		}

		$disabled = '';
		if (isset($disable) && $disable) {
			// disabled
			$disabled = ' disabled="disabled"';
		}
		$xhtml = '<input type="text"' . ' name="' . $sanitized['name'] . '"' . ' id="' . $sanitized['id'] . '"' . ' value="' . $sanitized['value'] . '"' . $disabled . $this->_htmlAttribs($attribs) . $this->getClosingBracket();

		$varName = str_replace(' ','',ucwords(str_replace('-',' ',$sanitized['id'])));

		$currency = 'USD';
		if (isset($attribs['currency'])) {
			$currency = $attribs['currency'];
		}
                $invalidMessage = __('Invalid amount.');
		if (isset($attribs['invalidMessage'])) {
			$invalidMessage = $attribs['invalidMessage'];
		}
		$disabled = ((bool)$disabled)?'true':'false';
		$xhtml .= <<<EOL
<script>
var currency{$varName} = dijit.byId("{$sanitized['id']}");
if (typeof currency{$varName} != "undefined") {
	currency{$varName}.destroyRecursive();
	currency{$varName} = null;
}
var example = dojo.currency.format(54775.53, {
	currency: "{$currency}"
});
currency{$varName} = new dijit.form.CurrencyTextBox({name:"{$sanitized['name']}",value:{$sanitized['value']},disabled:{$disabled},style:"{$style}",intermediateChanges:true,currency:"{$currency}",invalidMessage:"{$invalidMessage} Exampe: "+example},dojo.byId("{$sanitized['id']}"));

</script>
EOL;
		return $xhtml;
	}
}
