<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce("includes/ReportFilter.class.php");

class ReportView
{
	var $_template_id = null;
	var $_template = "default";
	var $_template_name = "Default Template";
	
	var $_report = null;
	
	function ReportView($report_id, $template_id = 0) {
		$this->_report =& ORDataObject::factory('Report', $report_id);
		$this->_template_id = $template_id;
		
		$this->_report_action = Celini::link('report')."report_id=" . $this->_report->get('id') . "&template_id=$this->_template_id";
	}

	function _setupTemplates() {
		$templates = $this->_report->get('templates');
		if (isset($templates[$this->_template_id])) {
			$this->_template_name = $templates[$this->_template_id]['name'];
			if ($templates[$this->_template_id]['is_default'] === 'no') {
				$this->_template = APP_ROOT . "/user/report_templates/{$this->_template_id}.tpl.html";
				if (!file_exists($this->_template)) {
					$this->_template = "default";
				}
			}
		}
	}
	
	function render($display) {
		$this->_setupTemplates();

		$display->assign("TOP_ACTION", Celini::link('report')."report_id=" . $this->_report->get('id'));
		
		$display->assign("REPORT_ACTION", $this->_report_action);
		if (!isset($_GET['gridMode'])) {
			$mode = "htmldoc";
			if (isset($GLOBALS['config']['pdfGenerator'])) {
				$mode = $GLOBALS['config']['pdfGenerator'];
			}
			$display->assign('PDF_ACTION',str_replace(array($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/util'),$GLOBALS['config']['entry_file'].'/PDF',$_SERVER['REQUEST_URI'])."&gridMode=$mode");
			$display->assign('PRINT_ACTION',str_replace($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/util',$_SERVER['REQUEST_URI'])."&gridMode=$mode");
			$display->assign('PRINTALL_ACTION',str_replace($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/util',$_SERVER['REQUEST_URI'])."&gridMode=$mode&pageSize=1000");
			if(isset($GLOBALS['config']['printers']) && count($GLOBALS['config']['printers']) > 0) {
				$config =& Celini::configInstance();
				$printer = '';
				$printers = $config->get('default_printers');
				if(is_array($printers)) {
					if(isset($printers['reports'][$r->get('custom_id')])) {
						$printer = "&printer=".$printers['reports'][$r->get('custom_id')];
					} elseif(isset($printers['reports']['default'])) {
						$printer = "&printer=".$printers['reports']['default'];
					} elseif(isset($printers['default'])) {
						$printer = "&printer=".$printers['default'];
					}
				}
				$view->assign('PRINTER_ACTION',str_replace(array($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/util'),$GLOBALS['config']['entry_file'].'/PDF',$_SERVER['REQUEST_URI'])."&gridMode=$mode&print=true$printer");
			}
		}
		$display->assign("report",$this->_report);
		$display->assign("template_name",$this->_template_name);
		
		$reports = $this->getReports();
		$display->assign_by_ref("reports",$reports);

		if ($this->_template === "default") {
			return $display->fetch(Celini::getTemplatePath("/report/" . $display->template_mod . "_view.html"));	
		}
		else {
			foreach(array_keys($reports) as $key) {
				$display->assign_by_ref($key.'_filter',$reports[$key]['filter']);
				$display->assign_by_ref($key.'_extra',$reports[$key]['filter']->extraData);

				$display->assign_by_ref($key.'_title',$reports[$key]['title']);
				$display->assign_by_ref($key.'_ds',$reports[$key]['ds']);
				$display->assign_by_ref($key.'_grid',$reports[$key]['grid']);
				$display->assign_by_ref($key.'_flags',$reports[$key]['flags']);
				//var_dump($key);
			}

			return $display->fetch($this->_template);
		}
	}
	
	
	/**
	 * This returns an array of reports, their grids, and their datasources for
	 * this report
	 *
	 * @return array
	 */
	function getReports() {
		$queries = $this->getQueries();
		foreach($queries as $key => $query) {
			$reports[$key] = $this->generateReportFromQuery($key, $query);
		}
		return $reports;
	}
	
	
	/**
	 * Returns an array of known queries for this report
	 *
	 * @return array
	 */
	function getQueries() {
		$queries = $this->_report->get('exploded_query');
		$reports = array();
		if (count($queries) == 0) {
			$queries['default'] = $this->_report->get('query');
		}
		
		return $queries;
	}
	
	
	/**
	 * This is called for each query to create the datasource/grid combo to
	 * display it.
	 *
	 * @param mixed
	 * @param string
	 * @param string
	 *
	 * @return array
	 * @access private
	 */
	function generateReportFromQuery($key, $query) {
		$returnArray = array();
		if (strstr($key,',')) {
			$flags = explode(',',$key);
			$key = array_shift($flags);
			$returnArray['flags'] = $flags;
		}
		$returnArray['filter'] =& new ReportFilter($query);
		$returnArray['filter']->setAction($this->_report_action);

		$returnArray['ds'] =& $returnArray['filter']->getDatasource();
		foreach($returnArray['filter']->dsFilters as $k => $val) {
			if (strstr($val[0],'&')) {
				$tmp = explode('&',$val[0]);

				if ($tmp[1] == 'ds') {
					$val[0] = array($returnArray['ds'],$tmp[0]);
				}
				else {
					$val[0] = array(${$tmp[1]},$tmp[0]);
				}
			}
			$returnArray['ds']->registerFilter($k,array_shift($val),$val);
		}

		if (isset($flags) && in_array('class',$flags)) {
			$extra = $returnArray['filter']->extraData;
			$c = "Report_".$extra['class'];
			if (!class_exists($c)) {
				if (file_exists( APP_ROOT."/local/includes/$c.class.php")) {
					require_once APP_ROOT."/local/includes/$c.class.php";
				}
			}

			if (!class_exists($c)) {
				trigger_error("Unable to load class $c for dataset $key",E_USER_ERROR); 
			}

			$o = new $c();
			foreach($extra as $k => $v) {
				$o->$k = $v;
			}

			$returnArray['ds'] =& $o->getDs();
		}
		$pageSize = 30;
		if(isset($flags)) {
			foreach($flags as $flag) {
				if(strpos($flag,'pageSize_') === 0) {
					$pageSize = (int)substr($flag,strpos($flag,'_')+1);
					break;
				}
			}
		}
		if($this->controller->GET->get('pageSize') > 0) {
			$pageSize = $this->controller->GET->get('pageSize');
		}
		$returnArray['grid'] =& new cGrid($returnArray['ds']);
		$returnArray['grid']->pageSize = $pageSize;
		$returnArray['grid']->name = $key;
		$returnArray['grid']->setExternalId($this->_report->get('id'));
		$returnArray['grid']->setOutputType('html');
		$returnArray['grid']->_datasource->_internalName = 'ReportView';
		$returnArray['grid']->setExtraURI("&reportName=$key");
		if ($key != 'default') {
			$returnArray['title'] = $key;
		}
		return $returnArray;
	}
}
