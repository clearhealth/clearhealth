<?php
/**
* Controller for reports
*
* @package	com.uversainc.hnr-erp
*/

/**
*/
require_once CELLINI_ROOT . "/controllers/Controller.class.php";
require_once APP_ROOT . "/local/ordo/Report.class.php";
require_once APP_ROOT . "/local/ordo/MenuReport.class.php";
require_once CELLINI_ROOT . "/includes/Pager.class.php";
require_once CELLINI_ROOT . "/includes/ReportFilter.class.php";
require_once CELLINI_ROOT . "/includes/Grid.class.php";
require_once CELLINI_ROOT . "/includes/Datasource_sql.class.php";


/**
*
*/
class C_Report extends Controller {

	/**
	* Sets up TOP_ACTION
	*/
	function C_Report ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Cellini::link('default'));
	}

	/**
	* If no action is specified use the list action
	*/
	function default_action_view() {
		return $this->list_action_view();
	}

	/**
	* List all reports
	*/
	function list_action_view() {
		$this->assign("TOP_ACTION", Cellini::link('list'));
		$this->assign("EDIT_ACTION", Cellini::link('edit'));
		$this->assign("VIEW_ACTION", Cellini::link('view'));

		$r = new Report(null,null);

		$pager = new Pager();

		$res = $r->_db->query("select count(*) c from reports");
		$pager->setMaxRows($res->fields['c']);

		$reports = $r->report_factory($pager->getLimit());

		$this->assign("reports",$reports);
		$this->assign("pager",$pager);

		return $this->fetch(Cellini::getTemplatePath("/report/" . $this->template_mod . "_list.html"));
	}

	/**
	* Connect a report to the menu all reports
	*/
	function connect_action_edit() {
		$this->assign("FORM_ACTION", Cellini::link('connect'));
		$this->assign("REMOTE_ACTION", $this->base_dir."jpspan_server.php?");

		$r = new Report();
		$r->set_id(663);

		$this->assign("report",$r);

		$menu = Menu::getInstance();
		$this->assign_by_ref('menu',$menu);
		//$mr = new MenuReport();
		//var_dump($mr->getMenuList(7,true));

		return $this->fetch(Cellini::getTemplatePath("/report/" . $this->template_mod . "_connect.html"));
	}

	/**
	* remotly access a report object
	*/
	function remote_action_edit() {
		$S = & new JPSpan_Server_PostOffice();
		$S->addHandler(new Report());
		$S->addHandler(new MenuReport());
		$l = Cellini::link('remote',false,'util');
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
	function edit_action_edit($id=-1) {
		if (!is_numeric($id)) {
			//echo "No suitable report id was provided, please check your query string.";	
		}
		if ($id === -1)
		{
			return $this->list_action_view();
		}
		
		$r = new Report($id);
		
		$this->assign("TOP_ACTION", Cellini::link('edit')."report_id=$id");
		$this->assign("template_top", Cellini::link('edit'));
		$this->assign("report",$r);
		$this->assign("template_edit",Cellini::link('edit_template'));
		$this->assign("template_add",Cellini::link("add_template")."report_id=$id");
		$this->assign("templates",$r->get_templates());
		$this->assign("DOWNLOAD_ACTION",Cellini::link('download_template',true,true,$id));
	
	
		return $this->fetch(Cellini::getTemplatePath("/report/" . $this->template_mod . "_edit_report.html"));	
	}

	/**
	* Create a new template
	*/
	function add_template_action_add($id) {
		$this->add_template_edit_edit(0,$id);
	}

	/**
	* Create a new report
	*/
	function add_action_add()
	{
		return $this->edit_action_edit(0);
	}

	/**
	* process edit form submissions
	*/
	function edit_action_process($id) {
		if ($_POST['process'] != "true") {
			return;
		}
		$report = new Report($id);
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

		$this->messages->addMessage("Update Successful","");
	}

	/**
	* Download a report template
	*/
	function download_template_action_edit($template_id=0,$report_id=0) {
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
				$r = new Report($report_id);
				$template_id = $r->generateDefaultTemplate();
				$this->download_template_action_edit($template_id,$report_id);
			}
		}
	}

	/**
	* View a report
	*/
	function view_action_view($id,$template_id=0)
	{
		if (!is_numeric($id)) {
			echo "No suitable report id was provided, please check your query string.";	
		}
		$r = new Report($id);
		$templates = $r->get_templates();
		if (isset($templates[$template_id])) {
			$template_name = $templates[$template_id]['name'];
			if ($templates[$template_id]['is_default'] === 'no') {
				$template = APP_ROOT."/user/report_templates/$template_id.tpl.html";
				if (!file_exists($template)) {
					$template = "default";
				}
			}
			else {
				$template = "default";
			}
		}
		else {
			$template_name = "Default Template";
			$template = "default";
		}



		$this->assign("TOP_ACTION", Cellini::link('view')."report_id=$id");
		$this->assign("report",$r);
		$this->assign("template_name",$template_name);

		$filter = new ReportFilter($r->get_query());

		$ds =& $filter->getDatasource();

		$grid =& new cGrid($ds);
		$grid->pageSize = 20;

		$this->assign_by_ref("grid",$grid);
		$this->assign_by_ref("filter",$filter);

		return $this->fetch(Cellini::getTemplatePath("/report/" . $this->template_mod . "_view.html"));	
	}
}
?>
