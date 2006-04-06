<?php
/**
* Controller for reports
*
* @package	com.uversainc.clearhealth
*/

/**
*/
$loader->requireOnce("ordo/MenuReport.class.php");
$loader->requireOnce("includes/Pager.class.php");
$loader->requireOnce("includes/ReportFilter.class.php");
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce("includes/Datasource_sql.class.php");


/**
*
* @todo switch jpspan usage to html_ajax
*/
class C_Report extends Controller {

	/**
	* Sets up TOP_ACTION
	*/
	function C_Report ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Celini::link('default'));
	}

	/**
	* If no action is specified use the list action
	*/
	function default_action_view() {
		return $this->actionView();
	}

	/**
	* List all reports
	*/
	function actionList() {
		$this->assign("TOP_ACTION", Celini::link('list'));
		$this->assign("EDIT_ACTION", Celini::link('edit'));
		$this->assign("VIEW_ACTION", Celini::link('view'));

		$r =& Celini::newOrdo('Report');

		$reports = new cGrid($r->getReportDs());
		$this->assign("reports",$reports);

		return $this->view->render("list.html");
	}

	/**
	* Connect a report to the menu all reports
	*/
	function actionConnect_edit() {
		$this->assign("FORM_ACTION", Celini::link('connect'));
		$this->assign("REMOTE_ACTION", $this->base_dir."jpspan_server.php?");

		$r = Celini::newOrdo('Report');
		$r->set_id(663);

		$this->assign("report",$r);

		$menu = Menu::getInstance();
		$this->assign_by_ref('menu',$menu);

		$sections = $menu->toDisplayArray();

		$em =& Celini::enumManagerInstance();
		if ($em->enumExists('chronic_care_codes')) {
			$chronicCodes = $em->enumArray('chronic_care_codes','enumeration_value_id','value');
			$sections['ss-chronic'] = 'Chronic Care Codes';
			foreach($chronicCodes as $key => $val) {
				$sections[$key] = $val;
			}
		}
		$this->assign('sections',$sections);
		//$mr = new MenuReport();
		//var_dump($mr->getMenuList(7,true));

		return $this->view->render("connect.html");
	}

	/**
	* remotly access a report object
	*/
	function actionRemote_edit() {
		$S = & new JPSpan_Server_PostOffice();
		$S->addHandler(Celini::newOrdo('Report'));
		$S->addHandler(new MenuReport());
		$l = Celini::link('remote',false,'util');
		$S->setServerUrl(substr($l,0,strlen($l)-1));



		// This allows the JavaScript to be seen by
		// just adding ?client to the end of the
		// server's URL

		if (isset($_SERVER['QUERY_STRING']) &&
			strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

		    // Compress the output Javascript (e.g. strip whitespace)
		    define('JPSPAN_INCLUDE_COMPRESS',true);

		    // Display the Javascript client
		    $S->displayClient();

		} else {
		    // hack up url
		    $_SERVER['REQUEST_URI'] = str_replace('/util/Report/remote','',$_SERVER['REQUEST_URI']);


		    // This is where the real serving happens...
		    // Include error handler
		    // PHP errors, warnings and notices serialized to JS
		    require_once JPSPAN . 'ErrorHandler.php';

		    // Start serving requests...
		    $S->serve();

		}
				
	}

	/**
	* Edit a report
	*/
	function actionEdit($id = 0) {
		$id = $this->_enforcer->int($id);

		if ($id == 0 && isset($this->report_id)) {
			$id = $this->report_id;
		}
		
		$r =& Celini::newOrdo('Report',$id);

		$manager =& Celini::enumManagerInstance();
		if ($manager->enumExists('system_reports')) {
			$sysReports = $manager->enumArray('system_reports','extra1','value');
			$this->assign('systemReports',$sysReports);
		}
		
		$this->assign("TOP_ACTION", Celini::link('edit',true,true,$id));
		$this->assign("template_top", Celini::link('edit'));
		$this->assign_by_ref("report",$r);
		$this->assign("template_edit",Celini::link('edit_template'));
		$this->assign("template_add",Celini::link("add_template")."report_id=$id");
		$this->assign("templates",$r->get_templates());
		$this->assign("DOWNLOAD_ACTION",Celini::link('download_template',true,true,$id));
	
	
		return $this->view->render('edit_report.html');	
	}

	/**
	* Create a new template
	*/
	function actionAdd_template_add($id) {
		$this->add_template_edit_edit(0,$id);
	}

	/**
	* Create a new report
	*/
	function actionAdd() {
		return $this->edit_action_edit(0);
	}

	/**
	* process edit form submissions
	*/
	function processEdit($id) {
		if ($_POST['process'] != "true") {
			return;
		}
		$report =& Celini::newOrdo('Report',$id);
		$report->populate_array($_POST);
		if (isset($_POST['new_templates'])) {
			$report->newTemplates = $_POST['new_templates']; 
		}
		if (isset($_POST['templates'])) {
			$report->templates = $_POST['templates']; 
		}
		if (isseT($_POST['deleted_templates'])) {
			$report->deletedTemplates = $_POST['deleted_templates'];
		}

		if (isset($_FILES['template']))
		{
			if (is_array($report->templates))
			{
				foreach($report->templates as $key => $val)
				{
					if (isset($_FILES['template']['tmp_name'][$key]['file']))
					{
						move_uploaded_file($_FILES['template']['tmp_name'][$key]['file'],
							realpath(APP_ROOT."/user/report_templates/")."/".(int)$key.".tpl.html");
					}
				}
			}
		}

		$report->persist();

		if ($id == 0) {
			$this->messages->addMessage("Report Added");
		}
		else {
			$this->messages->addMessage("Report Updated");
		}
		$this->report_id = $report->get('id');
	}

	/**
	* Download a report template
	*/
	function actionDownload_template_edit($report_id=0,$template_id=0) {
		if (is_numeric($template_id)) {
			$file = realpath(APP_ROOT."/user/report_templates/")."/".(int)$template_id.".tpl.html";
			if (file_exists($file)) {
				header("Content-type: text/html");
				header('Content-Disposition: attachment; filename="'.$template_id.'.tpl.html"');
				readfile($file);
				exit;
			}
			else {
				// generate a default template
				$r = Celini::newOrdo('Report',$report_id);
				$template_id = $r->generateDefaultTemplate();
				$this->actionDownload_template_edit($template_id,$report_id);
			}
		}
	}

	function actionView($id,$template_id = 0) {
		return $this->actionReport_view($id,$template_id);
	}

	function actionBatch_view($reportIds,$templateIds) {
		$ret = '';
		foreach($reportIds as $key => $id) {
			$ret .= "<div style='page-break-before: always'></div>".$this->actionReport_view($id,$templateIds[$key]);
		}
		return $ret;
	}
}

?>
