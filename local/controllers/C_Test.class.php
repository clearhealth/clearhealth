<?php
$loader->requireOnce('datasources/FeeSchedule_DS.class.php');
class C_Test extends Controller {

	function actionScroll() {
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = 'scriptaculous';
		$ajax->jsLibraries[] = 'scrollbar';
		//$ajax->jsLibraries[] = 'clniGrid';
		$ajax->stubs[] = 'ActiveFeeSchedule';


		$ds =& new FeeSchedule_DS();
		$this->assign('dsSetup',$ds->setupJs('dsSetup'));
		
		
		return $this->view->render('scroll.html');
	}
}
?>
