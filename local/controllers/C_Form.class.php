<?php

$loader->requireOnce('includes/Grid.class.php');

/**
 * Controller for generic form processing
 */
class C_Form extends Controller {
	var $_external_id = 0;
	var $_encounter_id = 0;
	
	function setExternalId($id) {
		$this->_external_id = (int)$id;
	}
	function setEncounterId($id) {
		$this->_encounter_id = (int)$id;
	}
	/**
	 * List all the forms in the system
	 */
	function actionList() {
		$form =& ORDataObject::factory('Form');

		$ds =& $form->formList();
		$ds->template['name'] = '<a href="'.Celini::link('edit').'id={$form_id}">{$name}</a>';
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);


		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_list.html"));
	}

	
	/**
	 * Add a new form
	 *
	 * @see actionEdit()
	 */
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	
	/**
	 * Edit/Add a new form
	 */

	function actionEdit($form_id = 0) {
		if (isset($this->form_id)) {
			$form_id = $this->form_id;
		}
		$form =& ORDataObject::factory('Form',$form_id);

		$this->assign_by_ref('form',$form);
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$form_id));
		$this->assign('DOWNLOAD_ACTION',Celini::link('download',true,true,$form_id));
		$this->assign('VIEW_ACTION',Celini::link('view',true,true,$form_id));

		$this->secure_dir[] = APP_ROOT."/user/form/";
		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_edit.html"));
		
	}

	function processEdit($form_id) {
		if (!isset($_POST['form'])) {
			return "";
		}
		$form =& ORDataObject::factory('Form',$form_id);
		
		//print_r ($_POST['form']);
		$form->populate_array($_POST['form']);
		$form->persist();
		$this->form_id = $form->get('id');

		$this->messages->addMessage('Form Added Successfully');

		if (isset($_FILES['form']['tmp_name']['upload_form']) && strlen($_FILES['form']['tmp_name']['upload_form']) > 0) {
			$filename = $form->get('file_path');

			if (!move_uploaded_file($_FILES['form']['tmp_name']['upload_form'],$filename)) {
				$this->messages->addMessage('Problem Uploading Form');
			}
			else {
						$form_structure_id = 0;
						
//						$structure=& Celini::newOrdo('FormStructure',$form_structure_id);
//						$structure->set('form_id', $this->form_id);
//						$structure->set ('form_structure_id', 0);
//						$structure->getFieldsList ($filename);
			
				$this->messages->addMessage('Form Uploaded Successfully');
			}
		}
	}

	function actionDownload_view($form_id) {
		// force download headers
		header('Content-type: text/html');
		$form =& ORDataObject::factory('Form',$form_id);
		$file = basename($form->get('file_path'));

		header('Content-Disposition: attachment; filename="'.$file.'"');
		readfile($form->get('file_path'));
		die();
	}

	function actionView($form_id = false) {
		$form =& ORDataObject::factory('Form',$form_id);
		if ($form_id == false) {
			$ds =& $form->formList();
			$ds->template['name'] = '<a href="'.Celini::link('view').'id={$form_id}">{$name}</a>';
		}
		else {
			$data =& ORDataObject::factory('FormData');
			$ds =& $data->dataList($form_id);
			$ds->template['last_edit'] = '<a href="'.Celini::link('data').'id={$form_data_id}">{$last_edit}</a>';
			$this->assign_by_ref('form',$form);
		}
		if ($this->GET->exists('returnTo')) {
			$this->assign('returnTo',$this->GET->get('returnTo'));
			$retTo = "&returnTo=".$this->GET->get('returnTo');
		}
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_view.html"));
	}

	function actionFillout_edit($form_id = 0,$form_data_id = 0) {
		$form_data_id = EnforceType::int($form_data_id);
		$externalId = $this->GET->get("externalId","int");
		$encounterId = $this->GET->get("encounterId","int");

		$retTo = "";
		if ($this->GET->exists('returnTo')) {
			$this->assign('returnTo',$this->GET->get('returnTo'));
			$retTo = "&returnTo=".$this->GET->get('returnTo');
		}

		$form =& ORDataObject::factory('Form',$form_id);
		$this->assign('formId',$form->get('form_id'));
		if ($form_id == false) {
			$ds =& $form->formList();
			$ds->template['name'] = '<a href="'.Celini::link('fillout').'id={$form_id}">{$name}</a>';
			$grid =& new cGrid($ds);
			$this->assign_by_ref('grid',$grid);
		}
		else {
			if (isset($this->form_data_id)) {
				$form_data_id = $this->form_data_id;
			}
			$formAction = Celini::link('fillout',true,true,$form_id)."form_data_id=$form_data_id$retTo";
			if ($externalId > 0) {
				$formAction .= "&externalId=$externalId"; 
			}
			if ($encounterId > 0) {
				$formAction .= "&encounterId=$encounterId"; 
			}
			$this->assign('FORM_ACTION',$formAction);
			$data =& ORDataObject::factory('FormData',$form_data_id);
			
			$this->assign_by_ref('form',$form);
			$this->assign_by_ref('data',$data);
			$this->secure_dir[] = APP_ROOT."/user/form/";
		}


		return $this->view->render("fillout.html");
	}

	function processFillout_edit($form_id = 0, $form_data_id = 0) {
			$data =& ORDataObject::factory('FormData',$form_data_id);
			$data->populate_array($_POST);
			$data->set('form_id',$form_id);
			
			if (!$data->get('external_id') >0 && $this->_external_id >0) {
				$data->set('external_id',$this->_external_id);	
			}
			elseif(!$data->get('external_id') >0) {
				$data->set('external_id',$this->get('external_id','c_patient'));	
			}
			$data->set('last_edit',date('Y-m-d H:i:s'));
			if ($this->GET->get("encounterId","int") > 0) {
				$data->set("encounter_id",$this->GET->get("encounterId","int"));
				
			}
			elseif ($this->_encounter_id > 0) {
				$data->set("encounter_id",$this->_encounter_id);
			
			}
			$data->persist();
			
//			$structure=& Celini::newOrdo('FormStructure');
//			$structure->set('form_id', $form_id);
//			$form_structure = $structure->build_form_structure_array ($form_data_id);
			//print_r ($form_structure);
			
			$formRule = & ORDataObject::factory('FormRule');
			
			/*foreach ($form_structure as $field_name => $field_value) {
				$messages = $formRule->checkFieldRule ($field_name, $field_value);
				if (is_array($messages)){
					foreach ($messages as $message) {
						$this->messages->addMessage($message);
					}
				}
			}*/
			
			$this->messages->addMessage('Form Updated');
			$this->form_data_id = $data->get('id');
	}

	function actionData_view($form_data_id) {
		$data =& ORDataObject::factory('FormData',$form_data_id);
		$form =& ORDataObject::Factory('Form',$data->get('form_id'));
		
		if ($this->GET->exists('returnTo')) {
			$this->assign('returnTo',$this->GET->get('returnTo'));
			$retTo = "&returnTo=".$this->GET->get('returnTo');
		}

		$this->Assign_by_ref('data',$data);
		$this->Assign_by_ref('form',$form);
		$this->assign('EDIT_ACTION',celini::link('fillout',true,true,$data->get('form_id')).'form_data_id='.$form_data_id);

		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_data.html"));
	}

	/**
	* Connect a report to the menu all reports
	*/
	function actionConnect_edit() {
		$this->assign("FORM_ACTION", Celini::link('connect'));

		$menu = Menu::getInstance();
		$this->assign_by_ref('menu',$menu);

		$ajax =& Celini::AJAXInstance();
		$ajax->stubs[] = 'Form';
		$ajax->stubs[] = 'MenuForm';

		$form =& ORDataObject::factory('Form');
		$this->assign_by_ref('form',$form);

		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_connect.html"));
	}

}

?>
