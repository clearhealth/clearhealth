<?php

$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('includes/ReportAction.class.php');

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
			$file_command = Celini::config_get("document_manager:file_command_path");
                        $cmd_args = "-i ".escapeshellarg($_FILES['form']['tmp_name']['upload_form']);
                        $command = $file_command." ".$cmd_args;
                        $mimetype = exec($command);
                        $mime_array = split(":", $mimetype);
                        $ext = "html";
                        if (is_array($mime_array) && count($mime_array) > 1) {
                        	$mimetype = $mime_array[1];
                                if (stripos($mimetype,"pdf") !== false) $ext = "pdf";
                        }

			if (!move_uploaded_file($_FILES['form']['tmp_name']['upload_form'], $form->get_file_path($ext))) {
				$this->messages->addMessage('Problem Uploading Form');
			}
			else {
						$form_structure_id = 0;
						
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
		$data = '';
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
			$data =& ORDataObject::factory('FormData',$form_data_id);
			$data->set('form_id',$form_id);
			$formAction = Celini::link('fillout',true,true,$form_id)."form_data_id=$form_data_id$retTo";
			if ($externalId > 0) {
				$formAction .= "&externalId=$externalId";
				$data->set('external_id',$externalId); 
			}
			elseif ($this->get('external_id','c_patient')>0) {
				$this->externalId = $this->get('external_id','c_patient');
				$data->set('external_id',$this->externalId); 
			}
			if ($encounterId > 0) {
				$formAction .= "&encounterId=$encounterId"; 
				$data->set('encounter_id',$encounterId); 
			}
			$this->assign('FORM_ACTION',$formAction);
			
			$this->assign_by_ref('form',$form);
			$this->assign_by_ref('data',$data);
		}
		$filename = $form->get('file_path');
                if (substr($filename,-3) === "pdf") {
		$data->strings = $data->_string_storage->_values;
		$data->ints = $data->_int_storage->_values;
		$data->dates = $data->_date_storage->_values;
		$data->texts = $data->_text_storage->_values;
                header("Content-type: application/vnd.adobe.xfdf");
                $this->_makeFormXml($data,$form->get('system_name'),$form->get('file_path'));
                }
		return $this->view->render("fillout.html");
	}
	function actionFilloutCombined_edit($form_id = 0,$form_data_id = 0, $reportId=0) {
		$form_data_id = EnforceType::int($form_data_id);
		$externalId = $this->GET->get("externalId","int");
		$encounterId = $this->GET->get("encounterId","int");
		$reportId = $this->GET->get('reportId',"int");

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
		}
		$reportData = array();
		if ($reportId > 0) {
			$ra = new ReportAction();
                	$ra->controller = new Controller();
                	$ra->fetch = false;
                	//0 is to use default template id
                	$ra->action($reportId, 0);
			foreach ($ra->reports as $report_name => $report) {
				$reportData[$report_name] = $report['ds']->toArray();
			}
		}

		$filename = $form->get('file_path');
                if (substr($filename,-3) === "pdf") {
                header("Content-type: application/vnd.adobe.xfdf");
                $this->_makeFormXml($data,$form->get('system_name'),$form->get('file_path'),$reportData);
                }
		return $this->view->render("fillout.html");
}

	function actionPreview_view($formId = 0) {
		$formId = (int)$formId;

		$form =& ORDataObject::factory('Form',$formId);
		$this->assign('formId',$form->get('form_id'));
		if ($formId == false) {
			$ds =& $form->formList();
			$ds->template['name'] = '<a href="'.Celini::link('fillout').'id={$formId}">{$name}</a>';
			$grid =& new cGrid($ds);
			$this->assign_by_ref('grid',$grid);
		}
		else {
			$this->assign_by_ref('form',$form);
		}
		$filename = $form->get('file_path');
		if (substr($filename,-3) === "pdf") {
                header("Content-type: application/vnd.adobe.xfdf");
		$this->_makeFormXml(array(),$form->get('system_name'),$form->get('file_path'));
		}
		return $this->view->render("fillout.html");
	}

	function _makeFormXml($data=array(),$name="",$filename="",$reportData=NULL) {
		$header =
'<?xml version="1.0" encoding="UTF-8"?><?xfa generator="XFA2_4" APIVersion="2.6.7116.0"?>
<xdp:xdp xmlns:xdp="http://ns.adobe.com/xdp/" timeStamp="2008-01-16T02:06:28Z" uuid="6aee0086-4ab9-40a0-8119-5a0f3d39220a">
<xfa:datasets xmlns:xfa="http://www.xfa.org/schema/xfa-data/1.0/">
<xfa:data>
<form1>';
$str = '';
                $str = ORDataObject::toXML($data,$name);
		if ($reportData !== NULL) {
			foreach ($reportData as $reportName => $report) {
                		$str .= ORDataObject::toXML($report,$reportName);
			}
		}
$str .=
'</form1>
</xfa:data>
</xfa:datasets>
<pdf href="'. $this->view->_tpl_vars['base_uri'] ."index.php/Images/".basename($filename).'" xmlns="http://ns.adobe.com/xdp/pdf/" />
</xdp:xdp>';
                echo $header.$str;exit;
	}

	function processFillout_edit($form_id = 0, $form_data_id = 0) {
			$data =& ORDataObject::factory('FormData',$form_data_id);
			if (isset($_POST) && isset($_POST['form_type']) && strtolower($_POST['form_type']) === 'pdf') {
                        $int = array();
                        $string = array();
                        $date = array();
                        $text = array();
                        foreach ($_POST as $key => $var) {
                                if (preg_match('/^(string|int|date|text)_(.*)/',$key,$match)) {
                                        if ($match[1] === "int") $int[$key] = $var;
                                        if ($match[1] === "string") $string[$key] = $var;
                                        if ($match[1] === "date") $date[$key] = $var;
                                        if ($match[1] === "text") $text[$key] = $var;
                                }
                        }
                        $pdfData = array("string" => $string, "int" =>$int, "date" => $date, "text" => $text);
                        $data->populate_array($pdfData);
                        }
                        else {
                        $data->populate_array($_POST);
                        }

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
			
			//$formRule = & ORDataObject::factory('FormRule');
			
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
