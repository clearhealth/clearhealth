<?php
$loader->requireOnce('datasources/FeeSchedule_DS.class.php');
$loader->requireOnce('/includes/clni/clniActiveGrid.class.php');

$loader->requireOnce('/includes/transaction/TransactionManager.class.php');


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

	function actionRaw() {

		$person =& Celini::newOrdo('Patient',1110);
		var_dump($person->get('gender'));
		$person->set('gender',new ClniValueRaw('gender + 1'));
		$person->persist();
		var_dump($person->get('gender'));
	}

	function actionTrans() {
		$tm = new TransactionManager();

		$trans = $tm->createTransaction('Claim');

		$trans->setClaim('206530-4290-201442');
		$trans->setPayer('CHDP','CHDP');

		$trans->type = 'credit';
		$trans->amount = 13.00;
		$trans->paymentDate = date('Y-m-d');

		$tm->processTransaction($trans);
	}

	function actionRadio() {
		return $this->view->render('radio.html');
	}
}
?>
