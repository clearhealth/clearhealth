<?php
$loader->requireOnce('controllers/C_Coding.class.php');
$loader->requireOnce('controllers/C_FreeBGateway.class.php');
$loader->requireOnce('includes/freebGateway/CHToFBArrayAdapter.class.php');
$loader->requireOnce('includes/LockManager.class.php');
$loader->requireOnce('datasources/MiscCharge_Encounter_DS.class.php');
$loader->requireOnce('datasources/Encounter_PayerGroup_DS.class.php');

/**
 * A patient Encounter
 */
class C_CriticalView extends Controller {

	function C_CriticalView() {
		$this->controller();
	}


	/**
	 * Edit/Add an encounter
	 */
	function actionViewCriticals() {
                // Criticals data block
                $GLOBALS['loader']->requireOnce("controllers/C_WidgetForm.class.php");
                $cwf = new C_WidgetForm();
                $widget_form_content = $cwf->actionShowCritical_view($this->get('patient_id','c_patient'));
                $this->assign("widget_form_content",$widget_form_content);
                $this->assign("cwf", $cwf);
	

                // Retrieve PatientStatistics view
                $GLOBALS['loader']->requireOnce('controllers/C_PatientStatistics.class.php');
                $patientStatsController =& new C_PatientStatistics();
		$p =& $this->_loadPatient($this->get('patient_id','c_patient'));
                $this->assign_by_ref("person_data",$p);		

                $this->view->assign('patientStatisticsView', $patientStatsController->actionView($this->get('patient_id','c_patient')));

		return $this->view->render("criticalview.html");
	}
			
        function &_loadPatient($patient_id) {

                $p =& Celini::newORDO('Patient', $patient_id);

                // used to interact with stuff that just wants a generic id instead of patient one (C_Form, maybe others)
                $this->set("patient_id", $patient_id, 'c_patient');
                $this->set('external_id',$patient_id,'c_patient');

                return $p;
        }

}
?>
