<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('controllers/C_FreeBGateway.class.php');

/**
 * Controller for editing/adding claims
 *
 * @package	com.clear-health.freeb2
 */
class C_Claim extends Controller {

	var $claimline_id = false;
	var $filters = "";
	var $displayFilters = array();
	
	function C_Claim() {
		parent::Controller();
//unset($_SESSION['freeb2']['filters'][strtolower(get_class($this))]);
		if (!isset($_SESSION['freeb2']['filters'][strtolower(get_class($this))])) {

			$defaults = array();
			// defaults
			// status - new
			$defaults['status'] = 1;

			// service date - this month
			$defaults['dos_start'] = date('Y-m-01');
			$defaults['dos_end'] = date('Y-m-d');


			$_SESSION['freeb2']['filters'][strtolower(get_class($this))] = $defaults;

		}
		$this->filters = $_SESSION['freeb2']['filters'][strtolower(get_class($this))];

		$this->displayFilters = $this->filters;
		if (isset($this->displayFilters['dos_start'])) {
			$this->displayFilters['dos_start'] = date('m/d/Y',strtotime($this->displayFilters['dos_start']));
		}
		if (isset($this->displayFilters['dos_end'])) {
			$this->displayFilters['dos_end'] = date('m/d/Y',strtotime($this->displayFilters['dos_end']));
		}
		$this->assign("filters",$this->filters);
	}


	/**
	 * Add/edit a claim
	 */
	function edit_action($claim_id = 0, $open_claim = true) {
		$c =& Celini::newORDO("FBClaim",$claim_id);
		
		if ($claim_id == 0) {
			$c->set("status",'new');
			$this->assign_by_ref("claim", $c);
			$this->assign("NEW_ACTION", Celini::link("new"));
			return $this->fetch(Celini::getTemplatePath("/claim/general_edit.html"));	
		}
		$claim_identifier = $c->get("claim_identifier");
		
		$fbg = new C_FreeBGateway();
		
		$storedRevisionNumber = $fbg->openClaim($claim_identifier,$c->get("revision"));
		
		/*
		 * If we open a claim that hasn't been closed, it will have a revision
		 * number of 0.  If that's the case, or the stored revision number is
		 * less than what the ORDO has stored, we need to display an error 
		 * message alert the user to the possible problem.
		 */
		if ($open_claim && $storedRevisionNumber == 0) {
			$this->assign("message", "Claim " . $c->get("claim_identifier") . ", revision " . $c->get("revision") . " is already open. This may mean that someone else is editing the claim or that a previous session was not closed properly.<br> ");
		}
		
		$revision = ($storedRevisionNumber > 0) ? $storedRevisionNumber : $c->get("revision");
		unset($c);
		
		// Now that know for sure what the revision number is, reload the 
		// FBClaim object at the right revision.
		$c =& FBClaim::fromClaimId($claim_identifier,$revision);
		
		//hide fields that can't be edited on the claim now that it exists from autoform
		$c->addMetaHints("hide",array("claim_id","claim_identifier","revision","status", "format"));
		
		$this->assign_by_ref("patient", $c->childEntity("FBPatient"));
		$this->assign_by_ref("provider", $c->childEntity("FBProvider"));
		$this->assign_by_ref("referring_provider", $c->childEntity("FBReferringProvider"));
		$this->assign_by_ref("supervising_provider", $c->childEntity("FBSupervisingProvider"));
		$this->assign_by_ref("subscriber", $c->childEntity("FBSubscriber"));
		$this->assign_by_ref("subscribers", $c->childEntities("FBSubscriber"));
		$this->assign_by_ref("billing_contact", $c->childEntity("FBBillingContact"));
		$this->assign_by_ref("responsible_party", $c->childEntity("FBResponsibleParty"));
		$this->assign_by_ref("claim_lines", $c->childEntities("FBClaimline"));
		$this->assign_by_ref("practice", $c->childEntity("FBPractice"));
		$this->assign_by_ref("billing_facility", $c->childEntity("FBBillingFacility"));
		$this->assign_by_ref("treating_facility", $c->childEntity("FBTreatingFacility"));
		$this->assign_by_ref("payer", $c->childEntity("FBPayer"));
		$this->assign_by_ref("payers", $c->childEntities("FBPayer"));
		$this->assign_by_ref("clearing_house", $c->childEntity("FBClearingHouse"));
		$this->assign_by_ref("claim", $c);
		
		$this->assign("FORM_ACTION", Celini::link(true) . "claim_id=" . $c->get("claim_id"));
		$this->assign("ADD_ENTITY_ACTION", Celini::link('add_entity') . "claim_id=" . $c->get("claim_id") . "&");
		$this->assign("DROP_ENTITY_ACTION", Celini::link('drop_entity') . "claim_id=" . $c->get("claim_id") . "&");
		$this->assign("CLOSE_ACTION", Celini::link("close") . "claim_id=" . $c->get("claim_id"));
		$this->assign('AJAX_ACTION',Celini::link("ajax",'Claim',false));

		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'Controller';
		$ajax->jsLibraries[] = 'scriptaculous';

		return $this->view->render('edit.html');
	}
	
	/**
	 * Add/edit a claim
	 */
	function edit_action_process($claim_id = 0) {
		foreach ($_POST as $field => $value) {
			switch ($field) {
				case 'process':
					break;
				default:
					if (is_array($_POST[$field])) {
						$o =& ORDataObject::factory($field);
						if (is_object($o)) {
							$o->populate_array($_POST[$field]);
							$o->persist();
							if (get_class($o) == "claim") {
								$claim_id = $o->get("claim_id");	
							}
							$this->assign("message","The $field was updated.");
						}
						else {
							$this->assign("message","Could not perform the update, object: $field could not be found.");	
						}
					}
					break;	
				
			}
		}
		$this->_state = false;
		//return the user to the edit screen, pass false as there is no need to reopen the claim again
		return $this->edit_action($claim_id,false);
		
	}
	
	/**
	 * set claim status to archive
	 */
	function archive_action_edit($claim_id) {
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$c->set("status",'archive');
		$c->persist();
		header("Location: " . Celini::link("list","claim"));
		exit;
	}

	/**
	 * set status to deleted
	 */
	function delete_action_delete($claim_id) {
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$c->set("status",'deleted');
		$c->persist();
		header("Location: " . Celini::link("list","claim"));
		exit;
	}
	
	/**
	 * close a claim
	 */
	function close_action_process($claim_id) {
		if ($_POST['process'] != "true") return;
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$c->set("status",'pending');
		$c->persist();
		header("Location: " . Celini::link("list","claim"));
		exit;
	}
	
	/**
	 * generate a new claim and redirect to edit screen
	 *
	 * @todo determine if there is a use for $claim_id.  If it were to actually
	 *   be used then it's acting as a processEdit().
	 */
	function new_action_process($claim_id = 0) {
		if ($_POST['process'] != "true") return;
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$c->populate_array($_POST['FBClaim']);
		$c->set("status",'new');
		$c->persist();
		header("Location: " . Celini::link("edit","claim") . "claim_id=" . $c->get("claim_id") . "&reopen=0");
		exit;
	}
	
	/**
	 * add an additional entity to a claim
	 * generally this is used for payer and subscriber only
	 */
	//function add_entity_action_process($claim_id,$entity) {
	function add_entity_action($claim_id, $entity) {
		if ($_REQUEST['process'] != true) return;
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$o =& ORDataObject::factory($entity);
		$o->set("claim_id",$claim_id);
		$o->persist();
		header("Location: " . Celini::link("edit") . "claim_id=" . $claim_id . "&reopen=0&");
		exit;
	}
	function actionAdd_entity_ajax($claim_id, $entity) {
		$c =& ORDataObject::factory("FBClaim",$claim_id);
		$o =& ORDataObject::factory($entity);
		$o->set("claim_id",$claim_id);
		$o->persist();

		$this->GET->set('claim_id',$claim_id);
		$this->object = $o;
		$ret = array('action'=>'add','html'=>$this->_actionAJAX(true,true));
		return $ret;
	}

	/**
	 * Drop a claimline
	 */
	function drop_entity_action($claim_id,$entity_id,$entity) {
		if ($_REQUEST['process'] != true) return;
		$o =& ORDataObject::factory($entity,$entity_id);
		$o->drop();
		header("Location: " . Celini::link("edit") . "claim_id=" . $claim_id . "&reopen=0&");
		exit;
	}
	function actionDrop_entity_ajax($claim_id,$entity_id,$entity) {
		$o =& ORDataObject::factory($entity,$entity_id);
		$o->drop();

		$ret = array('action'=>'remove','id'=>EnforceType::int($entity_id));
		return $ret;
	}

	function _claimLineInfo($claimId) {
		$claimId = strip_tags($claimId);
		$db =& new clniDb();
		$sql = "select
			concat_ws('|', concat(c.claimline_id,'-',`procedure`), concat(group_concat(diagnosis),' $',amount)) claimline
			from fbclaimline c
			left join fbdiagnoses d on c.claimline_id = d.claimline_id
			where claim_id = $claimId
			group by c.claimline_id";
		$res = $db->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields['claimline'];
			$res->moveNext();
		}
		return implode('/',$ret);
	}
	
	/**
	* List Claims
	*/
	function list_action_view() {
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('billingList');

		$c =& ORDataObject::factory('FBClaim');
		$fbg = new C_FreeBGateway();

		if ($this->GET->exists('queue')) {
			$q =& Celini::newOrdo('FBQueue',$this->GET->get('queue'));
			$this->filters = array();
			$this->filters['id'] = $q->get('ids');
		}

		if ($this->GET->exists('history_id')) {
			$GLOBALS['loader']->requireOnce('includes/clni/clniAudit.class.php');
			$audit = new clniAudit();
			$this->filters = array();
			$this->filters['id'] = unserialize($audit->oldFieldFromLogEntry($this->GET->get('history_id'),'ids'));;
		}

  	  	$cds =& $c->claimList($this->filters);
		$cds->registerTemplate('claim_id',"<a href='".Celini::link('edit')."id={\$claim_id}'>{\$claim_id}</a>");
		$cds->registerTemplate('batch','<div><input type="checkbox" name=batch[{$claim_id}][on] value="{$claim_id}"></div>');
		$cds->registerTemplate('revision',"<a href='".Celini::link('list_revisions')."id={\$claim_identifier}'>{\$revision}</a>");
		$cds->registerTemplate('date_of_treatment',"<span class='extraInfo'>{\$date_of_treatment}</span>");
		$cds->registerFilter('status',array(&$this,'_archiveLink'));
			  
		$grid =& new cGrid($cds);
		$grid->pageSize = 30;
		$grid->indexCol = false;

		$grid->prepare();
		$data = $cds->toArray();

		foreach($data as $key => $row) {
			$data[$key]['claimlines'] = $this->_claimLineInfo($row['claim_id']);
		}

		//var_dump($data[0]);

		$ajax =& Celini::ajaxInstance();
		$this->view->assign('data',$ajax->jsonEncode($data));
			
		$this->assign_by_ref("claim",$c);
		$this->assign_by_ref("fbg",$fbg);			
		$this->assign_by_ref('grid',$grid);
		$this->assign_by_ref('filters',$this->displayFilters);
		
		$this->assign("FILTER_ACTION", Celini::managerLink("setfilter"));
		$this->assign("QUEUE_ACTION", Celini::link('queue',true,false));
		$this->assign("DELETE_ACTION", Celini::link('delete',true,true));

		$action = new DispatcherAction();
		$action->wrapper = false;
		$action->controller = 'Queue';
		$action->action = 'view';
		$d = new Dispatcher();

		$this->assign('queue',$d->dispatch($action));

		if ($this->noRender) {
			return 'list.html';
		}
		else {
			return $this->view->render('list.html');
		}
	}
    
	function _archiveLink($status,$row) {
		if  ($status == "sent") {
			return $status .  ' <a href="' . Celini::link('archive',true,true,$row['claim_id']) . '">arc</a>';
		}
		return $status;
	}
    
    
    /**
    * process batch Claims
    */
    function list_action_process() {
    	$results = "";
    	if (isset($_POST['batch'])) {

 	   		//foreach ($_POST['batch'] as $claim_id => $claim_array) { //old foreach
			$claim_id = key($_POST['batch']);    
			$c =& ORDataObject::factory('FBClaim',$claim_id);
			//the first claim id is the batch id...

    			$fbg = new C_FreeBGateway();
    			$results = $fbg->claimResult_action_view($_POST['batch'],$_POST['variation'],$_POST['target'],$_POST['destination']);
   
 			if ($results === false) {
					$message = $fbg->claimLastError($c->get("claim_identifier"));
					$this->messages->addMessage("Error on claim trns id $claim_id: " .implode(" ", $message));
					header('Content-type: text/html');
					header('Content-Disposition:');
				}    



    		if (count($this->messages->getMessages()) == 0) {
    			$this->_continue_processing = false;
    			$this->_state = false;
   
			//if the target is a pdf we need to create that now... 		
    			if ($_POST['target'] === "pdf") {
    				$GLOBALS['loader']->requireOnce("controllers/C_PDF.class.php");
    				$cpdf = new C_PDF();
    				$cpdf->display($results,false);
    				exit;
    			}
    		}
    	}
    	
		return $results;    	
    }
    
    /**
    * Get a claim result (package)
    */
    function result_action_view($claim_id,$format,$package = "txt",$destination ="browser") {
    	$c =& ORDataObject::factory('FBClaim',$claim_id);
        
    	$fbg = new C_FreeBGateway();
		$result = $fbg->claimResult_action_view($c->get("claim_identifier"),$c->get("revision"),$format,$package,$destination,true,true,1);
		
		if ($result === false) {
			$message = $fbg->claimLastError($c->get("claim_identifier"));
			$this->assign("message", $message[1]);
			return $this->list_action_view();	
		}       
		$this->_continue_processing = false;   
        return $result;
    }
    
    /**
    * List Claims
    */
    function list_revisions_action_view($claim_identifier) {
           $c =& ORDataObject::factory('FBClaim');
                                                                                
           $cds =& $c->claimRevisionList($claim_identifier);
           $cds->template['claim_id'] = "<a href='".Celini::link('edit')."id={\$claim_id}'>{\$claim_id}</a>";
           
           $grid =& new cGrid($cds);
                                                                           
           $this->assign_by_ref('grid',$grid);
                                                                             
           return $this->fetch(Celini::getTemplatePath("/claim/" . $this->template_mod . "_list_revisions.html"));
    }

	var $_includeFrame = false;
	var $_includeForm = false;
    	function _actionAJAX($includeFrame = false,$includeForm = false) {
		$this->_includeFrame = $includeFrame;
		$this->_includeForm = $includeForm;
		return $this->actionAJAX();
	}

	function actionAJAX() {
		$claimId = $this->GET->getTyped('claim_id','int');

		$id = $this->GET->get('id');

		if (strtolower(get_class($this->object)) == "fbclaim") {
			$claimId = $this->object->get("claim_id");	
			$this->object->addMetaHints("hide",array("claim_id","claim_identifier","revision","status", "format"));
		}
		$this->assign("DROP_ENTITY_ACTION", Celini::link('drop_entity','Claim','main') . "claim_id=" . $this->object->get("claim_id") . "&");

		$this->view->assign("FORM_ACTION", Celini::link('edit','Claim','main') . "claim_id=" . $claimId);

		$this->view->assign_by_ref('object',$this->object);
		$this->view->assign('id','FB'.$id);
		
		if (isset($_POST['entity'])) {
			$this->view->assign('id',$_POST['entity']);
		}

		$this->assign('includeFrame',$this->_includeFrame);
		$this->assign('includeForm',$this->_includeForm);
		if ($this->POST->exists('legend')) {
			$this->view->assign('legend',$this->POST->get('legend'));
		}

		$ret = trim($this->view->render('single.html'));
		return $ret;
	}

	function processAJAX() {
		$claimId = $this->GET->getTyped('claim_id','int');

		$keys = array_keys($_POST);
		$field = $keys[1];
		$class = preg_replace('/[^A-Za-z0-9]/','',$field);

		if (is_array($_POST[$field])) {
			$o =& ORDataObject::factory($class);
			if (is_object($o)) {
				$o->populate_array($_POST[$field]);
				$o->persist();
				$o->populate();
				$this->assign("message","The $field was updated.");
			}
			else {
				$this->assign("message","Could not perform the update, object: $field could not be found.");	
			}
			$this->object =& $o;
		}
	}
}
?>
