<?php
require_once CELINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/SuperbillDatasource.class.php";

class C_Superbill extends Controller {

	function list_action_edit() {
		$sbd =& ORDataObject::factory('SuperbillData');
		$ds =& $sbd->superbillList();
		$ds->template['superbill_id'] = "<a href='".Celini::link('update')."id={\$superbill_id}'>{\$superbill_id}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->render("list.html");
	}
	
	function update_action_edit($superbill_id) {
		$ds =& new SuperbillDatasource();
		$ds->reset();
		$renderer = new Grid_Renderer_JS();
		$grid =& new cGrid($ds,$renderer);
		//$grid->pageSize = 30;

		$this->assign_by_ref('grid',$grid);

		return $this->render("update.html"));
	}
}
?>
