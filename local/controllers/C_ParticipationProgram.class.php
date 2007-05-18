<?php
$loader->requireOnce('/controllers/C_CRUD.class.php');
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');

class C_ParticipationProgram extends C_CRUD {
	
	var $_ordoName = "ParticipationProgram";
	var $form_data_id = 0;
	var $_person_program_id = 0;

	function actionAdd() {
		$this->view->assign('FORM_ACTION',Celini::link('edit',true,true,0));
		return parent::actionAdd();
        }
	function actionEdit($id) {
		$id = (int)$id;
		$parProg = ORDataObject::factory('ParticipationProgram',$id);
		$optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                $GLOBALS['loader']->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                $options = ORDataObject::factory($optionsClassName);
		if (strlen($options->administrationLink($id)) > 0) {
		$this->view->assign("administerLink",'<a href="' . $options->administrationLink($id) .'">administer</a>');
		}
		$form = ORDataObject::factory("Form");
                $formList = $form->simpleFormList();
                $this->assign("formList",$formList);
		$this->view->assign('FORM_ACTION',Celini::link('edit',true,true,$id));
		return parent::actionEdit($id);
	}
	function processEdit() {
		global $loader;
		$this->process();
		$optionsClassName = 'ParticipationProgram'. ucwords($this->_ordo->get('class'));
		$loader->requireOnce('includes/ParticipationPrograms/'.$optionsClassName. ".class.php");
		$options = ORDataObject::factory($optionsClassName);
		$options->_createTables();
	}

	function actionAddConnect_edit($person_id) {
		global $loader;
		$parProg = ORDataObject::factory('ParticipationProgram');
		$ppp = ORDataObject::factory('PersonParticipationProgram');	
		$ppp->set("person_id", $person_id);
		$this->view->assign('progNamesList',$parProg->valueList("name"));
		$this->view->assign('FORM_ACTION', Celini::link('editConnect',true,true,$ppp->get("person_program_id")));
		$this->view->assign('ACTION_ADD',true);
		$this->view->assign('ordo', $ppp);
		
		return $this->view->render("connect.html");
	}
	
	function actionEditConnect($person_program_id="") {
		global $loader;
		if (!is_numeric($person_program_id)) $person_program_id = $this->_person_program_id;
		$ppp = ORDataObject::factory('PersonParticipationProgram',$person_program_id);	
		$parProg = ORDataObject::factory('ParticipationProgram',$ppp->get('participation_program_id'));
		$optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                $loader->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                $options = ORDataObject::factory($optionsClassName, $person_program_id);
                $this->view->assign('options', $options);

		$this->view->assign('progNamesList',$parProg->valueList("name"));
		$this->view->assign('FORM_ACTION', Celini::link('editConnect',true,true,$ppp->get("person_program_id")));
		
                $this->view->assign('ordo', $ppp);
                return $this->view->render("connect.html");
		
	}	
	
	function processEditConnect($person_program_id = "") {
		$ppdata = $this->POST->get('ParticipationProgram');
		$ppdata=$ppdata['personparticipationprogram'];
		$ppp = ORDataObject::factory('PersonParticipationProgram',$person_program_id);
		$ppp->populateArray($ppdata);
		$ppp->persist();
		if(isset($ppdata['options']) && count($ppdata['options'] > 1)) {
		  $parProg = ORDataObject::factory('ParticipationProgram',$ppp->get('participation_program_id')); 
		  $optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                  $GLOBALS['loader']->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                  $options = ORDataObject::factory($optionsClassName, $person_program_id);
		  $options->populateArray($ppdata['options']);
		  $options->set('person_program_id',$ppp->get('person_program_id'));
		  $options->persist();	
		}
		$this->_person_program_id = $ppp->get("person_program_id");
		$this->messages->addMessage('Participation Program Connection Updated');
	}
}
?>
