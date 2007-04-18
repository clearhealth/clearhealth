<?php
$loader->requireOnce('includes/chlTestNames.class.php');
$loader->requireOnce('includes/chlLabProviders.class.php');

class C_Chllabtests extends Controller
{
	/**
	 * Is this controller being used by another controller?
	 *
	 * @var boolean
	 */
	var $embedded = false;
	
	function actionAdd() {
		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'chlTestNames';
		
		$testNames = chlTestNames::toArray();
		$this->view->assign('testNames', $testNames);
		
		$labProviders = chlLabProviders::toArray();
		$this->view->assign('labProviders', $labProviders);
		
		$formAction = Celini::link('add', 'chllabtests');
		if ($this->embedded) {
			$formAction .= 'embedded=true';
		}
		$this->view->assign('FORM_ACTION', $formAction);
		
		$appt =& Celini::newORDO('refAppointment', $this->GET->getTyped('visit_id', 'int'));
		$this->view->assign('visit_id', $appt->get('id'));
		
		$request =& Celini::newORDO('refRequest', $appt->get('refrequest_id'));
		$this->view->assign('patient_id', $request->get('patient_id'));
		return $this->view->render('add.html');
	}
	
	function processAdd() {
		$labTests =& Celini::newORDO('chlLabTests');
		$labTests->populate_array($_POST['chlLabTests']);
		$labTests->persist();
		
		if ($this->GET->exists('embedded')) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
	}
	
	function processDelete() {
		$labTests =& Celini::newORDO('chlLabTests', $this->GET->getTyped('id', 'int'));
		$labTests->remove();
		
		if ($this->GET->exists('embedded')) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit;
		}
	}
}
