<?php
/*****************************************************************************
*       RichEdit.php
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
class Zend_Form_Element_RichEdit extends Zend_Form_Element_Xhtml
{
	var $helper = null;
	public function render(Zend_View_Interface $view = null) {
		// overtake $view arguments
		$currentView = $this->getView();
		$belongsTo = $this->getBelongsTo();
		$name = $this->getName();
		$value = $this->getValue();
		$id = $belongsTo .'-' . $name;
		$completeName = $belongsTo .'[' . $name . ']';
		$str =  "
<div id=\"{$id}-container\" style=\"width:100%;height:250px;\"></div>
<input type=\"hidden\" id=\"{$id}\" name=\"{$completeName}\" value=\"{$value}\" />
<script type=\"text/javascript\">
richEdit{$name} = new dhtmlXEditor('{$id}-container');
richEdit{$name}.setIconsPath('{$currentView->baseUrl}/img/');
richEdit{$name}.init();
richEdit{$name}.setContent('{$value}');

function {$name}FormSubmit() {
	dojo.byId('{$id}').value = richEdit{$name}.getContent();
}

</script>
";
		return $str;
	}

}
