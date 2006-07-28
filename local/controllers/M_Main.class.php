<?php

class M_Main extends Manager {
	function preProcess() {
		$me =& Me::getInstance();
		$user =& $me->get_user();
		if(isset($_GET['changepractice'])){
			$_SESSION['defaultpractice']=$_GET['changepractice'];
		}
		if(!isset($_SESSION['defaultpractice']) && $user->get('id') > 0){
			$_SESSION['defaultpractice']=$user->get('DefaultPracticeId');
		}
		$this->controller->view->assign('celini', new Celini());
	}

	function postProcess() {
		if($this->controller->session->get('patient_action') == null) {
			$this->controller->session->set('patient_action', $this->controller->base_dir .'index.php/main/PatientFinder');
		}
		$this->controller->view->assign('patient_action', $this->controller->session->get('patient_action'));
	
		$patient_id = $this->controller->get('patient_id','c_patient');
		if ($patient_id > 0) {
			$patient =& ORDataObject::factory('Patient',$patient_id);
			$this->controller->assign_by_ref('selectedPatient',$patient);

			// confidentiality overlay code
			$this->controller->view->assign('showConfidentialityBanner',false);
			$c = $patient->get('confidentiality');
			if ($c > 2) {
				$this->controller->view->assign('showConfidentialityBanner',true);
				$showScreen = false;
				if (!isset($_SESSION['confidentiality'][$patient_id])) {
					$showScreen = true;
					if ($c == 5) {
						$config =& Celini::ConfigInstance();
						$actionsList = $config->get('confidentialActions',array());
						$controller = strtolower(Celini::getCurrentController());
						$action = strtolower(Celini::getCurrentAction());

						if (isset($actionsList[$controller]['*']) || isset($actionsList[$controller][$action])) {
							$this->controller->view->assign('confidentiality',$c);
						}
						$this->controller->view->assign('showConfidentialityBanner',true);
					}
				}
				if ($c == 3 || $c == 4 || $c == 6) {
					$em =& Celini::enumManagerInstance();
					if ($c == 3) {
						$codes = $em->enumArray('confidential_family_planning_codes');
					}
					elseif ($c == 4) {
						$codes = $em->enumArray('confidential_disease_codes');
					} elseif ($c == 6) {
						$codes = $em->enumArray('confidential_family_planning_and_disease_codes');
					}
					$conf = false;
					if (isset($GLOBALS['currentCodeList'])) {
						foreach ($GLOBALS['currentCodeList'] as $code) {
							if (in_array($code['code'],$codes)) {
								$conf = true;
								$this->controller->view->assign('confidentiality',$c);
								$this->controller->view->assign('showConfidentialityBanner',true);
								break;
							}
						}
					}

					if ($conf && $showScreen) {
						$this->controller->view->assign('confidentiality',$c);
					}
				}
				$_SESSION['confidentiality'][$patient_id] = true;
			}
		}
	}
}
?>
