<?php

$loader->requireOnce('includes/Grid.class.php');

/**
 * Controller for generic form processing
 */
class C_Form extends Controller {
	var $_external_id = 0;
	
	function setExternalId($id) {
		$this->_external_id = $id;
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

		if (isset($_FILES['form']['tmp_name']['upload_form'])) {
			$filename = $form->get('file_path');

			if (!move_uploaded_file($_FILES['form']['tmp_name']['upload_form'],$filename)) {
				$this->messages->addMessage('Problem Uploading Form');
			}
			else {
						$form_structure_id = 0;
						
						$structure=& Celini::newOrdo('FormStructure',$form_structure_id);
						$structure->set('form_id', $this->form_id);
						$structure->set ('form_structure_id', 0);
						$structure->getFieldsList ($filename);
			
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
			//print_r ($_POST);
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
			$data->persist();
			
			$structure=& Celini::newOrdo('FormStructure');
			$structure->set('form_id', $form_id);
			$form_structure = $structure->build_form_structure_array ($form_data_id);
			//print_r ($form_structure);
			
			$formRule = & ORDataObject::factory('FormRule');
			
			foreach ($form_structure as $field_name => $field_value) {
				$messages = $formRule->checkFieldRule ($field_name, $field_value);
				if (is_array($messages)){
					foreach ($messages as $message) {
						$this->messages->addMessage($message);
					}
				}
			}
			
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

	/**
	 * What to graph is 100% session driven
	 *
	 * @todo: create a graphical error message
	 */
	function actionGraph_view($graphId) {
		$this->_setupVitalsGraph($graphId);
		if (!isset($_SESSION['GRAPH'][$graphId])) {
			Celini::raiseError('No data to graph');
		}
		else {
			$graph =& new clniGraph();
			$graph->definition =& new clniGraphDefinition();
			$graph->data = $_SESSION['GRAPH'][$graphId]['data'];
			$graph->titles = $_SESSION['GRAPH'][$graphId]['titles'];
			$graph->setup();
			$graph->graph();
			exit();
		}

		
	}

	// this is a hack
	function _setupVitalsGraph($id) {

		$patientId = $this->get('patient_id', 'c_patient');

		$db =& new clniDb();

		/*
		code to get field keys
		$sql = "select value_key from storage_string ss inner join form_data fd on fd.form_data_id = ss.foreign_key where external_id = $patientId";
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			$fields[] = $res->fields['value_key'];
			
			$res->MoveNext();
		}
		*/
		$fields = array(
			'sitting_bp_1',
			'sitting_bp_2',
			'standing_bp_1',
			'standing_bp_2',
			'supine_bp_1',
			'supine_bp_2',
		);
		/*
			'systolic_bp_1',
			'systolic_bp_2',
			'diastolic_bp_1',
			'diastolic_bp_2',
		);
	*/	

		$join = '';
		$select = '';
		foreach($fields as $field) {
			$join .= " left join storage_string as s$field on( fd.form_data_id = s$field.foreign_key and s$field.value_key = '$field')\n";
			$select .= ", max(case s$field.value_key when '$field' then s$field.value else null end) as $field";
		}

		$format = DateObject::getFormat();

		$sql = "select date_format(last_edit,'$format') last_edit $select 
			from form_data fd $join where external_id = $patientId or external_id in
			 (select encounter_id from encounter where patient_id = $patientId)
			 group by fd.form_data_id";

		$res = $db->execute($sql,ADODB_FETCH_NUM);

		$data = array();
		while($res && !$res->EOF) {
			$data[] = $res->fields;
			$res->MoveNext();
		}
		$_SESSION['GRAPH'][$id]['data'] = $data;
		$titles = array();
		foreach($fields as $field) {
			$titles[] = ucfirst(str_replace('_',' ',$field));
			
		}
		$_SESSION['GRAPH'][$id]['titles'] = $titles;
	}
}

class clniGraph {
	var $definition;
	var $data;
	var $titles;

	var $Graph;
	var $Datasets = array();
	var $Support = array();

	function setup() {
		//$GLOBALS['loader']->requireOnce('lib/PEAR/Image/Graph.php');
		require_once 'Image/Graph.php';

		$this->Graph =& Image_Graph::factory('graph', array(
				array(
					'width'=>$this->definition->width, 
					'height'=>$this->definition->height, 
					'canvas'=>'png',
					'antialias' => 'native',
				))
			);

		$this->Support['Font'] =& $this->Graph->addNew($this->definition->fontType, $this->definition->fontFile);
		$this->Support['Font']->setSize($this->definition->fontSize);
		$this->Graph->setFont($this->Support['Font']);

		$this->definition->layout($this);
	}

	function prepareDataset() {
		for($i = 1; $i < count($this->data[0]); $i++) {
			$this->Datasets[$i] =& Image_Graph::factory('dataset');
			foreach($this->data as $row) {
				$this->Datasets[$i]->addPoint($row[0],$row[$i]);
			}
		}

	}

	var $colors = array('red','green','blue','orange','red','gray','lightblue','maroon','peach','marine','brown');

	function graph() {
		$this->prepareDataset();

		foreach(array_keys($this->Datasets) as $key) {
			$this->Support['plots'][$key] =& $this->Support['Plotarea']->addNew('line',$this->Datasets[$key]);
			$this->Support['plots'][$key]->setTitle($this->titles[($key-1)]);
			$this->Support['plots'][$key]->setLineColor($this->colors[$key]);
			
		}

		$this->Support['AxisX'] =& $this->Support['Plotarea']->getAxis(IMAGE_GRAPH_AXIS_X);
		$this->Support['AxisX']->setTitle('Date');
		

		$this->Graph->done();
	}
}

class clniGraphDefinition {
	var $width = 350;
	var $height = 350;

	var $fontSize = 8;
	var $fontType = 'ttf_font';
	var $fontFile = false;

	var $title = 'Patient Vitals';
	var $titleSize = 12;

	function clniGraphDefinition() {
		$this->fontFile = CELINI_ROOT.'/fonts/vera.ttf';
	}

	function layout(&$graph) {
		$graph->Support['Plotarea'] = Image_Graph::factory('plotarea');
		$graph->Support['Legend'] = Image_Graph::factory('legend');
		$graph->Support['Legend']->setBorderColor('black');
		$graph->Graph->add(
			Image_Graph::vertical(
				Image_Graph::factory('title', array($this->title, $this->titleSize)),
					Image_Graph::vertical(
						$graph->Support['Plotarea'],
						$graph->Support['Legend'],
						80
					),
					5
			)
		);
		$graph->Support['Legend']->setPlotarea($graph->Support['Plotarea']);
	}
}
?>
