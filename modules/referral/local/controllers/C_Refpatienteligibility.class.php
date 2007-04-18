<?php

$loader->requireOnce('includes/refPatientProgramEligibilityList.class.php');
$loader->requireOnce('includes/refEligibilityForRequestChanger.class.php');

class C_Refpatienteligibility extends Controller
{
	var $_templatePath = '/refpatienteligibility/';
	
	function actionEdit() {
		$patient_id = $this->GET->getTyped('patient_id', 'int');
		$programEligibilityList =& new refPatientProgramEligibilityList($patient_id);
		$programEligibilityArray = array();
		$i = 1;
		while ($eligibility = $programEligibilityList->nextEligibility()) {
			$eligibility['schema'] = str_replace(
				array('name="refPatientEligibility[eligibility]"', 'refPatientEligibility__eligibility'),
				array('name="refPatientEligibility[' . $i . '][eligibility]"', 'refPatientEligibility__' . $i . '__eligibility'),
				$eligibility['schema']);
			$programEligibilityArray[] = $eligibility;
			$i++;
		}
		
		// handle setting up all of the various FPL enums
		$session =& Celini::sessionInstance();
		$em =& Celini::enumManagerInstance();
		$fplValues = array();
		$refProgram =& Celini::newORDO('refProgram');
		foreach ($refProgram->valueList('memberPrograms') as $program_id => $program_name) {
			$tmpProgram =& Celini::newORDO('refProgram', $program_id);
			if ($tmpProgram->get('schema') == 0) {
				$fplValues[] = false;
				continue;
			}
			//echo "Setting {$program_id}<br />";
			$session->set('referral:currentProgramId', $program_id);
			$fplValues[] = $em->enumArray('federal_poverty_level');
			
			unset($em->_elCache['federal_poverty_level']);
			//var_dump(array_keys($em->_elCache));
		}
		$this->view->assign('fplValues', $fplValues);
		
		$this->view->assign('programEligibilityArray', $programEligibilityArray);
		$this->view->assign('patient_id', $this->GET->get('patient_id'));
		
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		$this->view->assign_by_ref('person', $person);
		/*
		 * See if this user is a referral manager on any program.  Disabling this per program would
		 * require a change to the way this is handled as they're handled in bulk.
		 */
		if ($person->isType('referral manager')) {
			$this->view->assign('FORM_ACTION', Celini::link('edit', 'refpatienteligibility', 'chlpopup') . 'patient_id=' . $this->GET->get('patient_id'));
		}
		else {
			$this->view->assign('FORM_ACTION', false);
		}
		
		return $this->_fetchDisplay('edit');
	}
	
	function processEdit($id = 0) {
		foreach ($this->POST->get('refPatientEligibility') AS $valuesToPersist) {
			$eligibility =& Celini::newORDO('refPatientEligibility',$valuesToPersist['refpatient_eligibility_id']);
			$eligibility->populate_array($valuesToPersist);
			$eligibility->persist();
			
			$changer =& new refEligibilityForRequestChanger($eligibility);
			$changer->doChange();
		}
		
		echo '<script type="text/javascript">window.close();</script>"';
	}
	
	
	/**
	 * Returns the default display for a given type.
	 *
	 * @todo This, or something like it should be moved up to the superclass so
	 *    it is accessible by all controllers.
	 *
	 * @param  string
	 * @return string
	 */
	function _fetchDisplay($type) {
		return $this->view->fetch(Celini::getTemplatePath($this->_templatePath . $this->template_mod . '_' . $type . '.html'));
	}
}

