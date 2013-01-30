<?php
/*****************************************************************************
*       Div.php
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
 * Hidden form element
 */
class Zend_Form_Element_Div extends Zend_Form_Element_Xhtml
{
	var $helper = null;
	public function render(Zend_View_Interface $view = null) {
		// overtake $view arguments
		$currentView = $this->getView();
		$id = $this->getBelongsTo() .'-' . $this->getName();
	$str = <<<EOL
<pre id='tncontent' style='display:none;'>
ONE, TEST - 10026
DOB: 1954-02-01
ENCOUNTER: Thu Feb 01 10:23:00 CST 2009
PHYSICIAN: Doctor, Test
LOCATION: Main Office - Exam 2
TRANSCRIBED: jdoe
Medical Star Health Clinic
1 Main St
Phoenix, AZ 85224
877-571-7679
www.clear-health.com

ORIENTATION: Ms. Yudell returns today in followup for stage II dysgerminoma, resected and then treated with BEP times two. She is being followed today with last cycle being 02/01/09. She is doing quite well. She has excellent performance status. She is eating well. She has had no further intercurrent issues and she looks great overall.

PHYSICAL EXAMINATION:
Vital signs: Weight 127 pounds. Temperature 97.8. Blood pressure 110/88. Pulse 67.
Constitutional:
Eyes: no conjunctivitis.
ENT: Good dentition. No stomatitis, no mucositis, no thrush.
Neck: Nontender, no masses, no thyromegaly, no nodules.
Resp: Chest clear to auscultation and percussion, normal respiratory effort.
CV: Normal S1, S2. No murmurs, gallops or rub. Normal PMI.
Abdomen: No hepatomegaly, splenomegaly, ascites or tenderness.
Lymphatic: No cervical, supraclavicular, axillary, inguinal adenopathy.
Muscular: Normal gait, no nail changes. Strength symmetric 5/5 in all four extremities.
Skin: No rashes, ecchymoses, petechiae. No palpable masses
Psych: Oriented times three. Normal affect.
Neuro: Cranial nerves II/XI grossly intact. No focal motor defects.

LABORATORY: Hemoglobin 13.6, hematocrit 41.0, white blood cells 4,200, with platelets of 169. She had an alpha fetoprotein and beta HCG which are pending. Last was 1.8 on the alpha fetoprotein and 3 on the beta HCG titer, well within normal limits. The others will be reviewed when they come in.

Assessment and Plan:
1. Stage II Dysgermomia s/p BEP times two now in long term follow up. She will followup again with Dr. Test and have CT scan in May with CT of the chest, abdomen and pelvis and repeat labs.
</pre>
EOL;
		$str = nl2br($str);
		$str .= "<div id='{$id}' style='width:100%;height:250px;'></div>";
		$str .=  "<script>
var richEdit = new dhtmlXEditor('{$id}');
richEdit.setIconsPath('{$currentView->baseUrl}/img/');
richEdit.init();
richEdit.setContent(dojo.byId('tncontent').innerHTML);
</script>
";


		return $str;
	}

}
