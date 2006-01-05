<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";
require_once CELINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for generic form processing
 */
class C_Form extends Controller {

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
		$form->populate_array($_POST['form']);
		$form->persist();
		$this->form_id = $form->get('id');
		$this->messages->addMessage('Form Added Successfully');

		if (isset($_FILES['form']['tmp_name']['upload_form'])) {
			$filename = $form->get('file_path');
			if (!move_uploaded_file($_FILES['form']['tmp_name']['upload_form'],$filename)) {
				$this->messages->addMessage('Problem Uploading Form');
			}
			else {
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
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_view.html"));
	}

	function actionFillout_edit($form_id = 0,$form_data_id = 0) {
		$form_data_id = EnforceType::int($form_data_id);

		$form =& ORDataObject::factory('Form',$form_id);
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
			$this->assign('FORM_ACTION',Celini::link('fillout',true,true,$form_id)."form_data_id=$form_data_id");
			$data =& ORDataObject::factory('FormData',$form_data_id);
			$this->assign_by_ref('form',$form);
			$this->assign_by_ref('data',$data);
			$this->secure_dir[] = APP_ROOT."/user/form/";
		}

		if ($this->GET->exists('returnTo')) {
			$this->assign('returnTo',$this->GET->get('returnTo'));
		}

		return $this->view->render("fillout.html");
	}

	function processFillout_edit($form_id = 0, $form_data_id = 0) {
			$data =& ORDataObject::factory('FormData',$form_data_id);
			$data->populate_array($_POST);
			$data->set('form_id',$form_id);

			$data->set('external_id',$this->get('external_id','c_patient'));

			$data->set('last_edit',date('Y-m-d H:i:s'));
			$data->persist();
			$this->messages->addMessage('Form Updated');
			$this->form_data_id = $data->get('id');
	}

	function actionData_view($form_data_id) {
		$data =& ORDataObject::factory('FormData',$form_data_id);
		$form =& ORDataObject::Factory('Form',$data->get('form_id'));

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
		$this->assign("REMOTE_ACTION", $this->base_dir."jpspan_server.php?");

		$menu = Menu::getInstance();
		$this->assign_by_ref('menu',$menu);

		$form =& ORDataObject::factory('Form');
		$this->assign_by_ref('form',$form);

		return $this->fetch(Celini::getTemplatePath("/form/" . $this->template_mod . "_connect.html"));
	}

}
?>
