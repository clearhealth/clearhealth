<?php

require_once CELINI_ROOT . '/includes/EnumManager.class.php';
$loader->requireOnce('/includes/Grid.class.php');

class C_refPractice extends Controller
{
	var $_practice = null;
	
	function list_action() {
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refPracticeList_DS');
		$practiceList =& new refPracticeList_DS();
		
		$practiceListGrid =& new cGrid($practiceList);
		$practiceListGrid->indexCol = false;
		
		$this->view->assign_by_ref('practiceListGrid', $practiceListGrid);
		
		$this->view->assign('addURL', Celini::link('edit'));
		return  $this->view->render('list.html');
	}
	
	function actionEdit($refPractice_id = 0) {
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		// check to see if user is a referral manager on any program
		//TODO fix permission
	
		if (!is_null($this->_practice)) {
			$practice =& $this->_practice;
			$refPractice_id = $practice->get('id');
		}
		else {
			$practice =& Celini::newORDO('refPractice', $refPractice_id);
		}
		if ($this->GET->exists('program_id')) {
			$practice->set('refprogram_id', $this->GET->getTyped('program_id', 'int'));
		}
		
		$this->view->assign_by_ref('practice', $practice);
		
		$location =& ORDataObject::factory('refPracticeLocation', $this->GET->getTyped('pl_id', 'int'));
		$this->view->assign_by_ref('location', $location);
		
		$provider =& ORDataObject::factory('refProvider', $this->GET->getTyped('provider_id', 'int'));
		$this->view->assign_by_ref('provider', $provider);
		
		$specialty =& Celini::newORDO('refPracticeSpecialty', $this->GET->getTyped('ps_id', 'int'));
		$this->view->assign_by_ref('specialty', $specialty);
		
		// setup specialty section
		$em =& new EnumManager();
		$this->view->assign('refSpecialty', $em->enumArray('refSpecialty'));
		
		
		// setup practice locations
		$practiceLocationList =& $practice->loadDatasource('refLocationList');
		$practiceLocationListGrid =& new cGrid($practiceLocationList);
		$practiceLocationListGrid->name = "practiceLocationListGrid";
		$practiceLocationListGrid->indexCol = false;
		
		$providerList =& $practice->loadDatasource('refProviderList');
		$providerListGrid =& new cGrid($providerList);
		$providerListGrid->prepare();
		$this->view->assign_by_ref('providerListGrid', $providerListGrid);
		
		$this->view->assign_by_ref('practiceLocationListGrid', $practiceLocationListGrid);
		$this->view->assign('possibleAssignBy', array('Practice', 'Provider'));
		
		$this->view->assign('statusOptions', array('Active', 'Inactive'));
		
		$this->view->assign('programLink', Celini::link('edit', 'refprogram', 'main') . 'program_id=' . $practice->get('refprogram_id')); 
		$this->view->assign('refprogram_id', $practice->get('refprogram_id'));
		
		$this->view->assign('FORM_ACTION', Celini::link("edit/{$refPractice_id}", 'refpractice'));
		return $this->view->render('edit.html');
	}
	
	function processEdit($refPractice_id) {
		$practice =& Celini::newORDO('refPractice', $refPractice_id);
		$oldAssignBy = $practice->get('id') > 0 ? $practice->value('assign_by') : false;
		
		$practice->populate_array($_POST['refPractice']);
		$practice->persist();
		$this->_practice =& $practice;
		
		if (isset($_POST['refPracticeLocation'])) {
			$process = false;
			
			// Check to see if we really need to process this
			foreach ($_POST['refPracticeLocation'] as $key => $practiceLocationValue) {
				if (!empty($practiceLocationValue) && $key != 'refPractice_id') {
					$process = true;
					break;
				}
			}
			if ($process) {
				$location =& Celini::newORDO('refPracticeLocation', (int)$_POST['refPracticeLocation']['id']);
				$location->populate_array($_POST['refPracticeLocation']);
				$location->persist();
			}
		}
		
		if (isset($_POST['refProvider'])) {
			$process = false;
			
			foreach ($_POST['refProvider'] as $key => $providerValue) {
				if (!empty($providerValue)) {
					$process = true;
					break;
				}
			}
			if ($process) {
				//printf('<pre>%s</pre>', var_export($_POST['refProvider'] , true));
				//exit;
				$providerId = isset($_POST['refProvider']['id']) ? (int)$_POST['refProvider']['id'] : 0;
				$provider =& Celini::newORDO('refProvider', $providerId);
				$provider->populate_array($_POST['refProvider']);
				$provider->set('refpractice_id', $practice->get('id'));
				$provider->persist();
			}
		}
		
		if ($oldAssignBy !== false && $oldAssignBy != $practice->value('assign_by')) {
			switch ($oldAssignBy) {
				case 'Practice' :
					$member =& Celini::newORDO(
						'refProgramMember',
						array($practice->get('refprogram_id'), 'Practice', $practice->get('id')),
						'ActiveByProgramAndExternalTypeId');
					$member->set('inactive', 1);
					$member->persist();
					break;
				
				case 'Provider' :
					$providerCollection =& $practice->getChildren('providers');
					while ($provider =& $providerCollection->nextORDO()) {
						$member =& Celini::newORDO(
							'refProgramMember',
							array($practice->get('refprogram_id'), 'Provider', $provider->get('id')),
							'ActiveByProgramAndExternalTypeId');
						$member->set('inactive', 1);
						$member->persist();
					}
					break;
			}
		}
		
		switch ($practice->value('assign_by')) {
			case 'Practice' :
				$member =& Celini::newORDO(
					'refProgramMember',
					array($practice->get('refprogram_id'), 'Practice', $practice->get('id')),
					'ActiveByProgramAndExternalTypeId');
				if (!$member->isPopulated()) {
					$member->persist();
				}
				break;
			
			case 'Provider' :
				$providerCollection =& $practice->getChildren('providers');
				while ($provider =& $providerCollection->nextORDO()) {
					$member =& Celini::newORDO(
						'refProgramMember',
						array($practice->get('refprogram_id'), 'Provider', $provider->get('id')),
						'ActiveByProgramAndExternalTypeId');
					if (!$member->isPopulated()) {
						$member->persist();
					}
				}
				break;
		}
		
	}
	
		
	function actionEmbeddedLocationList_list($practice_id) {
		global $loader;
		$loader->requireOnce('includes/ORDO/ORDOFinder.class.php');
		
		$finder =& new ORDOFinder('refPracticeLocation', 'refpractice_id = ' . (int)$practice_id);
		$results =& $finder->find();
		$locarray = $results->toArray();
		$this->view->assign('locationsArray', $locarray);
		if(count($locarray) == 1) {
			$this->view->assign('onelocation',true);
		}
		return $this->view->render('embeddedLocationList.html');
	}
	
	function actionEmbeddedProviderList_list($practice_id,$provider_id=0) {
		global $loader;
		$loader->requireOnce('includes/ORDO/ORDOFinder.class.php');
		
		$finder =& new ORDOFinder('refProvider', 'refpractice_id = ' . (int)$practice_id);
		$results =& $finder->find();
		$provarray = $results->toArray();
		$this->view->assign('providersArray', $provarray);
		$this->view->assign('selectedprovider',$provider_id);
		if(count($provarray) == 1) {
			$this->view->assign('oneprovider',true);
		}
		return $this->view->render('embeddedProviderList.html');
	}
	
	function actionEmbeddedTextFields_list($practice_id, $provider_id = null) {
		$practice =& Celini::newORDO('refPractice', $practice_id);
		if ($practice->get('assign_by') != 'Practice') {
			$practice =& Celini::newORDO('refProvider', $provider_id);
		}
		$this->view->assign_by_ref('practice', $practice);
		return $this->view->render('embeddedTextFields.html');
	}
	
	function ajaxMethods() {
		return array(
			'actionEmbeddedLocationList_list', 
			'actionEmbeddedProviderList_list',
			'actionEmbeddedTextFields_list');
	}
}
 
