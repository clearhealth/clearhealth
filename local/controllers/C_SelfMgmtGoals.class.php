<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');
$loader->requireOnce('datasources/Person_SelfMgmtGoalsList_DS.class.php');
$loader->requireOnce('datasources/Person_GenericNotes_DS.class.php');

class C_SelfMgmtGoals extends Controller {

	function __construct() {
		$head =& Celini::HTMLHeadInstance();
                $head->addJs('clniPopup');
		parent::Controller();
	}
	function actionEdit_edit($personId) {
		return $this->actionList_view($personId);
	}
	function actionList_view($personId) {
		$personId = (int)$personId;
		
		$em =& Celini::enumManagerInstance();
		$this->view->assign('em',$em);
		$this->view->assign('personId',$personId);
		$smgBlock = $this->_generateSMGList($personId);
		$this->view->assign('smgBlock',$smgBlock);
		if ($this->GET->exists('returnTo')) {
                        $this->assign('returnTo',$this->GET->get('returnTo'));
                }


		return $this->view->render('list.html');
	}
	function test(){
		return "test";
	}
	function _generateSMGList($personId) {
		$smgDS =& new Person_SelfMgmtGoalsList_DS($personId);
        	$smgDS->prepare();
		$this->assign('smgList',$smgDS->toArray());
		$this->view->assign_by_ref('controller',$this);
		return $this->view->render('smgBlock.html');

	}
	function ajaxList($personId) {
		return $this->_generateSMGList($personId);
	}
	function ajaxAddUpdateSMG($array) {
		$self_mgmt_id = 0;
		if (isset($array['self_mgmt_id'])) {
		  $self_mgmt_id = (int)$array['self_mgmt_id'];
		}
		$smg = ORDataObject::factory('SelfMgmtGoals',$self_mgmt_id);
		$smg->set('initiated',date('Y-m-d'));
		$smg->populateArray($array);
		
		$smg->persist();
		return true;
	}
	function ajaxAddNote($smgId) {
		$smgn = ORDataObject::factory('GenericNote');
		$smgn->set('parent_obj_id',(int)$smgId);
		$this->view->assign("ordo",$smgn);
		return $this->view->render("editNote.html");
	}
	function ajaxAddUpdateNote($array) {
		$smgn = ORDataObject::factory('GenericNote',(int)$array['generic_note_id']);
		if (!isset($array['parent_obj_id'])) {
		  $this->messages->addMessage("No Self Management Goal Id Supplied");
		}
		else {
		  $smgn->set('parent_obj_id',(int)$array['parent_obj_id']);
		  $smgn->set('note',$array['note']);
		  $profile =& Celini::getCurrentUserProfile();
		  $smgn->set('person_id',$profile->getPersonId());
		  $smgn->set('created',date('Y-m-d'));
		  $smgn->set('self_mgmt_goal',date('Y-m-d'));
		  $smgn->set('parent_obj_id',$array['parent_obj_id']);
		  $smgn->set('type','self_mgmt_goals');
		  $smgn->populateArray($array);
		  $smgn->persist();
		  $this->assign('incoming', $array['generic_note_id']);
		  $this->assign("ordo",$smgn);
		  $this->messages->addMessage("Note update successfully");
		  $this->assign('success',true);
		}
		return $this->view->render('alertMessages.html');
	}
	function _getNotesGrid($smgId) {
		$notesDS = new Person_GenericNotes_DS($smgId);
		$notesDS->prepare();
		$grid = new cGrid($notesDS);
		return $grid;
	}
	function _getNotesGridRender($smgId) {
		$grid = $this->_getNotesGrid($smgId);
		return $grid->render();
	}
	

}
?>
