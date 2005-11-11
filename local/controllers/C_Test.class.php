<?php
$loader->requireOnce('datasources/FeeSchedule_DS.class.php');
$loader->requireOnce('/includes/clni/clniActiveGrid.class.php');
class C_Test extends Controller {

	function actionScroll() {
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('scriptaculous');
		$ajax->jsLibraries[] = array('scrollbar','clniGrid');
		$ajax->stubs[] = 'ActiveFeeSchedule';


		$ds =& new FeeSchedule_DS();
		$this->assign('dsSetup',$ds->setupJs('dsSetup'));
		
		
		return $this->view->render('scroll.html');
	}

	function actionGrid() {
		$ds =& new FeeSchedule_DS();
		$grid =& new clniActiveGrid('testGrid',$ds);
		$grid->gridWidth = "600px";

		$grid->stubName = 'ActiveFeeSchedule';

		return $grid->render();
	}
}
?>
