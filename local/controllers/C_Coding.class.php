<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/CodingDatasource.class.php";

class C_Coding extends Controller {

	function list_action() {
		return $this->update_action(1);
	}
	function update_action($superbill_id) {
		$icd =& new IcdCodingDatasource();
		$icd->reset();
		$renderer_icd = new Grid_Renderer_JS();
		$renderer_icd->id = "gicd";
		$gicd =& new cGrid($icd,$renderer_icd);
		$this->assign_by_ref('icd',$gicd);

		$cpt =& new CptCodingDatasource();
		$cpt->reset();
		$renderer_cpt = new Grid_Renderer_JS();
		$renderer_cpt->id = "gcpt";
		$gcpt =& new cGrid($cpt,$renderer_cpt);
		$this->assign_by_ref('cpt',$gcpt);

		return $this->fetch(Cellini::getTemplatePath("/coding/" . $this->template_mod . "_update.html"));	
	}
}
?>
