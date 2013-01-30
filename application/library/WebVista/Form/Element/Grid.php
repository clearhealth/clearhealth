<?php
/*****************************************************************************
*       Grid.php
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

class Zend_Form_Element_Grid extends Zend_Form_Element_Xhtml {

	public $helper = null;

	public function render(Zend_View_Interface $view = null) {
		// USAGE: <dataPoint type="grid" src="controller.raw/action" withPatientId="true" params="param1=value1&amp;param2=value2" namespace="namespaceId" headers="Col1,Col2" widths="*,*" colTypes="ed,ed" width="600px" height="200px" />
		$view = $this->getView();
		$name = $this->getName();
		$id = $this->getId();
		$value = $this->getValue();
		$src = $this->getAttrib('src');
		$withPatientId = strtolower($this->getAttrib('withPatientId')).'';
		$params = $this->getAttrib('params');
		$headers = $this->getAttrib('headers');
		$widths = $this->getAttrib('widths');
		$types = $this->getAttrib('colTypes');
		$skin = $this->getAttrib('skin');
		if (!strlen($skin) > 0) $skin = 'xp';
		$onLoaded = $this->getAttrib('onLoaded');

		$style = '';
		$height = strtolower($this->getAttrib('height'));
		if (strlen($height) > 0) $style .= 'height:'.$height.';';
		$width = strtolower($this->getAttrib('width'));
		if (strlen($width) > 0) $style .= 'width:'.$width.';';
		$isReadonly = false;
		if (isset($view->signatureInfo) && strlen($view->signatureInfo) > 0) {
			$isReadonly = true;
		}
		$attachEvents = array();
		$events = $this->listEvents();
		foreach ($this->getAttribs() as $k=>$v) {
			if (in_array($k,$events)) $attachEvents[] = "{$name}Grid.attachEvent(\"{$k}\",{$v});";
		}
		$inHeaderSpecial = '';
		$headerSpecial = $this->getAttrib('headerSpecial');
		if (strlen($headerSpecial) > 0) {
			$inHeaderSpecial = "{$name}Grid._in_header_special=function(tag,index,data){ 
	{$headerSpecial}
}";
		}
		$attachEvents = implode("\n",$attachEvents);

		$docType = $view->doctype();

		$completeName = $this->getFullyQualifiedName();
		if (!$isReadonly) {
			$ret = <<<EOL
<style>div.gridbox_xp table.obj td {border-bottom: none;border-right:none;}</style>
<div style="width:100%;height:100%;">
	<div id="{$name}GridId" style="{$style}"></div>
	<input type="hidden" id="{$id}" name="{$completeName}" value="{$value}" />
</div>
<script>
function {$name}GridReload() {
	{$name}Grid.clearAll();
	var params = [];
	if ("{$params}" != "") params.push("{$params}");
	if ("{$withPatientId}" == "true") params.push("personId="+mainController.getActivePatient());
	{$name}Grid.load("{$view->baseUrl}/{$src}?"+params.join("&"),function(){
		var value = {{$value}};
		{$name}Grid.forEachRow(function(id){
			if (!value[id]) return;
			var val = value[id];
			for (var i = 0; i < this.getColumnsNum(); i++) {
				if (this.getColType(i) == "ro") continue;
				var tmp = (val[i])?val[i]:"";
				this.cells(id,i).setValue(tmp);
			}
		});
		{$onLoaded}
	},"json");
}

var {$name}Grid = new dhtmlXGridObject("{$name}GridId");
{$name}Grid.setImagePath("{$view->baseUrl}/img/");
{$name}Grid.setHeader("{$headers}");
{$name}Grid.setInitWidths("{$widths}");
{$name}Grid.setColTypes("{$types}");
{$name}Grid.setSkin("{$skin}");
{$inHeaderSpecial}
{$attachEvents}
{$name}Grid.init();
{$name}GridReload();


globalNoteTemplateCallbacks.push(function(){
	var obj = dojo.byId("{$id}");
	if (!obj) return false;
	var value = [];
	{$name}Grid.forEachRow(function(id){
		var tmp = [];
		for (var i = 0; i < this.getColumnsNum(); i++) {
			if (this.getColType(i) != "ro") {
				tmp.push("\""+this.cells(id,i).getValue()+"\"");
			}
			else {
				tmp.push("\"\"");
			}
		}
		var val = "\""+id+"\":["+tmp.join(",")+"]";
		value.push(val);
	});
	obj.value = value.join(",");
	return true;
});

function {$name}Print() {
	var printHtml = (<r><![CDATA[
<?xml version="1.0" encoding="UTF-8" ?>
{$docType}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
]]></r>).toString();

	var docHead = document.getElementsByTagName("head")[0];
	printHtml += docHead.innerHTML;
	printHtml += '</head><body class="tundra">';
	printHtml += dojo.byId('mainToolbar').innerHTML;
	printHtml += dojo.byId('cntemplateform').innerHTML;

	printHtml += "</body></html>";

	var iframe = dojo.byId("iframeprint");
	var doc = null;
	if (iframe.contentDocument) {
		// Firefox/Opera
		doc = iframe.contentDocument;
	}
	else if (iframe.contentWindow) {
		// Internet Explorer
		doc = iframe.contentWindow.document;
	}
	else if (iframe.document) {
		// Others
		doc = iframe.document;
	}
	if (doc == null) {
		throw "Document not initialized";
	}

	doc.open();
	doc.write(printHtml);
	doc.close();

	dojo.byId('iframeprint').contentWindow.focus();
	dojo.byId('iframeprint').contentWindow.print();
}


</script>
EOL;
		}
		else { // table instead of grid
			$thead = array('<tr>');
			foreach (explode(',',$headers) as $header) {
				$thead[] = '<th>'.$header.'</th>';
			}
			$thead[] = '</tr>';
			$tbody = array();
			$val = array();
			foreach (explode('":["',$value) as $k=>$v) {
				if (!isset($key)) {
					$key = substr($v,1);
					continue;
				}
				$arr = explode('","',$v);
				$len = count($arr) - 1;
				if (isset($arr[$len])) {
					$list = explode('"]',$arr[$len]);
					$arr[$len] = $list[0];
					$tmpKey = substr($list[1],2);
				}
				$val[$key] = $arr;
				$key = $tmpKey;
			}
			foreach ($val as $k=>$v) {
				$tr = '<tr>';
				foreach ($v as $w) $tr .= '<td>'.$w.'</td>';
				$tr .= '</tr>';
				$tbody[] = $tr;
			}
			$ret = '<table width="'.$width.'" height="'.$height.'" border="1">
					<thead>'.implode("\n",$thead).'</thead>
					<tbody>'.implode("\n",$tbody).'</tbody>
				</table>';
		}

		return $ret;
	}

	protected function listEvents() {
		return array(
			// Drag-and-Drop Events
			'onBeforeDrag','onDragIn','onDragOut','onDrag','onDrop',
			// Editing Events
			'onEditCell','onRowDblClicked','onCellChanged','onCheckbox','onCheck','onRowPaste',
			// Grid Reconstruction Events
			'onGridReconstructed','onRowAdded','onRowCreated','onBeforeRowDeleted','onAfterRowDeleted','onSubRowOpen',
			// Loading Events
			'onXLS','onXLE','onDynXLS','onDistributedEnd','onSubGridCreated','onSubGridLoaded','onSubAjaxLoad',
			// Moving Events
			'onBeforeCMove','onAfterCMove',
			// Paging Events
			'onBeforePageChanged','onPageChanged','onPaging',
			// Resizing Events
			'onResize','onResizeEnd',
			// Selection Events
			'onBeforeSelect','onRowSelect','onSelectStateChanged','onBlockSelected','onCellMarked','onCellUnMarked',
			// Sorting Events
			'onBeforeSorting','onAfterSorting',
			// Context Menu Events
			'onBeforeContextMenu','onRightClick',
			// Grouping
			'onGroup','onGroupStateChanged','onUnGroup',
			// Filtering
			'onFilterStart','onFilterEnd','onCollectValues',
			// Validation
			'onValidationError','onValidationCorrect','onLiveValidationError','onLiveValidationCorrect',
			// Other Events
			'onDhxCalendarCreated','onEnter','onStatReady','onHeaderClick','onKeyPress','onTab','onMouseOver','onScroll',
			// Inner events
			'onRowIdChange','onClearAll','onColumnHidden',
		);
	}

}
