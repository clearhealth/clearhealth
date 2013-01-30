<?php
/*****************************************************************************
*       CodeLookup.php
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


/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * RichEdit form element
 */
class Zend_Form_Element_CodeLookup extends Zend_Form_Element_Xhtml {

	var $helper = null;

	public function render(Zend_View_Interface $view = null) {
		$src = strtolower($this->getAttrib('src'));
		switch ($src) {
			case 'cpt':
				break;
			case 'icd9':
				break;
			default:
				return __('Invalid source.');
				break;
		}

		$view = $this->getView();
		$belongsTo = $this->getBelongsTo();
		$name = $this->getName();
		$value = $this->getValue();
		$id = $belongsTo .'-' . $name;
		$completeName = $belongsTo .'[' . $name . ']';

		$isReadonly = false;
		if (isset($view->signatureInfo) && strlen($view->signatureInfo) > 0) {
			$isReadonly = true;
		}

		$codesValues = '';
		if (strlen($value) > 0) {
			$arrValues = explode("^|^",$value);
			foreach ($arrValues as $v) {
				$checked = substr($v,0,2);
				$strVal = substr($v,2);
				$codesValues .= '<div><input type="checkbox" id="'.$name.'Codes" name="'.$name.'Codes" value="'.$strVal.'"';
				if ($checked == '1-') {
					$codesValues .= ' checked="checked"';
				}
				if ($isReadonly) {
					$codesValues .= ' disabled="disabled"';
				}
				$codesValues .= ' /> '.$strVal.'<br /></div>';
			}
		}

		$ret = '';
		if (!$isReadonly) {
			$ret .= <<<EOL

<div style="width:100%;height:100%;">
	<input type="text" id="{$src}q" name="{$src}q" style="width:80%" onkeypress="return {$src}CodeLookupKeyPressInput(event);" /><button id="{$src}SearchLabel" onClick="return {$src}CodeLookup();">Search</button>
	<br />
	<style>div.gridbox_xp table.obj td {border-bottom: none;border-right:none;}</style>
	<div id="{$src}CodeLookupGridContainer" style="height:150px;"></div>
	<input type="button" id="{$src}CodeLookupAddId" value="Add" onClick="{$src}CodeLookupAdd()" disabled="true" />
</div>

<script>

function {$src}CodeLookup() {
	{$src}CodeLookupGrid.clearAll();
	{$src}CodeLookupGrid.load("{$view->baseUrl}/code-lookup.raw?src={$src}&q="+dojo.byId("{$src}q").value,function() {
		dojo.byId("{$src}CodeLookupAddId").disabled = true;},"json");
	return false;
}

function {$src}CodeLookupAdd() {
	var rowId = {$src}CodeLookupGrid.getSelectedRowId();
	if (rowId == null) {
		alert('No code selected');
		return;
	}

	var {$src}CodesContainer = dojo.byId("{$src}CodesContainer");
	var strTxt = {$src}CodeLookupGrid.cells(rowId,0).getValue();
	var val = rowId + ' - ' + strTxt;

	var cbInput = document.createElement("input");
	cbInput.type = "checkbox";
	cbInput.id = "{$name}Codes";
	cbInput.name = "{$name}Codes";
	cbInput.value = val;
	cbInput.checked = cbInput.defaultChecked = true;

	var oDiv = document.createElement("div");
	oDiv.appendChild(cbInput);
	oDiv.innerHTML += ' ' + val + '<br />';
	{$src}CodesContainer.appendChild(oDiv);
}

function {$src}CodeLookupKeyPressInput(e) {
	var key = window.event ? e.keyCode : e.which;
	if (key == 13) {
		{$src}CodeLookup();
		return false;
	}
}

var {$src}CodeLookupGrid = new dhtmlXGridObject('{$src}CodeLookupGridContainer');
{$src}CodeLookupGrid.setImagePath("{$view->baseUrl}/img/");
{$src}CodeLookupGrid.setHeader('Description,Code');
{$src}CodeLookupGrid.setInitWidths("*,120");
{$src}CodeLookupGrid.setColAlign("left,right");
{$src}CodeLookupGrid.setColTypes("ro");
{$src}CodeLookupGrid.setSkin("xp");
{$src}CodeLookupGrid.attachEvent("onRowSelect",{$src}CodeLookupRowSelectHandler);
{$src}CodeLookupGrid.attachEvent("onRowDblClicked",{$src}CodeLookupRowDoubleClickedHandler);
{$src}CodeLookupGrid.init();

function {$src}CodeLookupRowSelectHandler(rowId,cellIndex) {
	dojo.byId("{$src}CodeLookupAddId").disabled = false;
}

function {$src}CodeLookupRowDoubleClickedHandler(rowId,colIndex) {
	{$src}CodeLookupAdd();
}

</script>
<br />

EOL;
		}

		$ret .= <<<EOL

<div id="{$src}CodesContainer">{$codesValues}</div>
<input type="hidden" id="{$id}" name="{$completeName}" value="{$value}" />

<script>
globalNoteTemplateCallbacks.push(function(){
	var codesEl = document.getElementsByName("{$name}Codes");
	if (codesEl == null) {
		return true;
	}
	var val = new Array();
	for (i = 0; i < codesEl.length; i++) {
		var chk = 0;
		if (codesEl[i].checked) {
			chk = 1;
		}
		val.push(chk + "-" + codesEl[i].value);
	}
	dojo.byId('{$id}').value = val.join("^|^");
	return true;
});
</script>

EOL;

		return $ret;
	}

}
