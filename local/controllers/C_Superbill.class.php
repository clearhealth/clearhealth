<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/SuperbillDatasource.class.php";

class C_Superbill extends Controller {

	function list_action() {
		return $this->update_action(1);
	}
	function update_action($superbill_id) {
		$ds =& new SuperbillDatasource();
		$ds->reset();
		$renderer = new Grid_Renderer_JS();
		$grid =& new cGrid($ds,$renderer);
		//$grid->pageSize = 30;

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/superbill/" . $this->template_mod . "_update.html"));	
	}
}
?>
