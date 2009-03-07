<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('includes/ReportFilter.class.php');
$loader->requireOnce('includes/Datasource_array.class.php');
$loader->requireOnce('controllers/C_Graph.class.php');

/**
 * Class that handles building and displaying a report for the base controller
 */
class ReportAction {
	var $controller;
	var $reports;
	var $fetch = true;

	function action($report_id,$template_id) {
		$filteredGet =& Celini::filteredGet();
		if ($filteredGet->exists('report_id') && $filteredGet->get('report_id') !== $report_id) {
			$report_id = $filteredGet->getTyped('report_id', 'int');
		}
		$r =& Celini::newORDO('Report',$report_id);

		$sr = $r->get('system_report');
		$sr_template = false;
		if (preg_match('/\/*(.+)\/(.+)/',$sr,$match)) {
			$class = 'C_'.$match[1];
			if (!class_exists($class)) {
				$GLOBALS['loader']->requireOnce("/controllers/$class.class.php");
			}
			$controller = new $class();
			$controller->noRender = true;
			$sr_template = $controller->dispatch($match[2],array(),'action');
			$view =& $controller->view;
		}
		else {
			$view =& $this->controller->view;
		}

		$templates = $r->get_templates(true);
		//echo("<pre>".print_r($templates,true)."</pre>");
		if (isset($templates[$template_id])) {
			$template_name = $templates[$template_id]['name'];
			if (isset($templates[$template_id]['system_template_id']) && $templates[$template_id]['system_template_id'] > 0) {
				$template = APP_ROOT."/user/system_templates/{$templates[$template_id]['system_template_id']}.tpl.html";
			}
			else {
				if (file_exists(APP_ROOT."/user/report_templates/$template_id.tpl.html")) {
				$template = APP_ROOT."/user/report_templates/$template_id.tpl.html";
				}
				elseif(file_exists(APP_ROOT."/user/report_templates/$template_id.tpl.pdf")) {
				$template = APP_ROOT."/user/report_templates/$template_id.tpl.pdf";

				}
			}
			//one last sanity check..
			if (!isset($template) || !file_exists($template)) {
				$template = "default";
			}
		}
		else {
			$template_name = "Default Template";
			$template = "default";
		}

		$view->assign("TOP_ACTION", Celini::link('report')."report_id=$report_id");
		$view->assign("REPORT_ACTION", Celini::link('report')."report_id=$report_id&template_id=$template_id");
		if (!isset($_GET['gridMode'])) {
			$mode = "htmldoc";
			if (isset($GLOBALS['config']['pdfGenerator'])) {
				$mode = $GLOBALS['config']['pdfGenerator'];
			}
			$view->assign('PDF_ACTION',str_replace(array($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/minimal'),$GLOBALS['config']['entry_file'].'/PDF',$_SERVER['REQUEST_URI'])."&gridMode=$mode");
			$view->assign('PRINT_ACTION',str_replace($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/minimal',$_SERVER['REQUEST_URI'])."&gridMode=$mode");
			$view->assign('PRINTALL_ACTION',str_replace($GLOBALS['config']['entry_file'].'/main',$GLOBALS['config']['entry_file'].'/minimal',$_SERVER['REQUEST_URI'])."&gridMode=$mode&pageSize=1000");
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
		$view->assign('pageType',Celini::getCurrentPageType());

		$report_sequence = $r->nextSequence($template_id);
		$view->assign('report_sequence',$report_sequence);

		$view->assign("report",$r);
		$view->assign("template_name",$template_name);

		$queries = $r->get('exploded_query');
		$reports = array();

		$snapshotId = false;
		$data = array();
		if ($this->controller->GET->exists('snapshotId')) {
			$rs =& Celini::newOrdo('ReportSnapshot',$this->controller->GET->get('snapshotId'));
			$data = unserialize($rs->get('data'));
			$snapshotId = true;
			$view->assign('snapshotDate',$rs->get('snapshot_date'));
		}

		if (count($queries) == 0) {
			if($report_id == "0" && isset($_SESSION['_xml_report_temp'])){
				$queries['default'] = $_SESSION['_xml_report_temp']['query'];
			}else{
				$queries['default'] = $r->get('query');
			}

		}
		foreach($queries as $key => $query) {
			$query = $this->_parseSqlGenerators($query);
			$flags = array();	
			if (strstr($key,',')) {
				$flags = explode(',',$key);
				$key = array_shift($flags);
				$reports[$key]['flags'] = $flags;
			}
			$reports[$key]['hcserver'] = $r->get('hcserver');
			$reports[$key]['filter'] =& new ReportFilter($query);
			$reports[$key]['filter']->setAction($view->_tpl_vars['REPORT_ACTION']);

			if ($snapshotId && isset($data['labels'][$key])) {
				$reports[$key]['ds'] =& new Datasource_array();
				$reports[$key]['ds']->setup($data['labels'][$key],$data['data'][$key]);
			}
			else {
				//var_dump('live data');
				if (in_array('skipDefaultRun',$flags)) {
					$reports[$key]['ds'] =& new Datasource();
				}
				else {
					$reports[$key]['ds'] =& $reports[$key]['filter']->getDatasource();
				}
			}
			foreach($reports[$key]['filter']->dsFilters as $k => $val) {
				if (strstr($val[0],'&')) {
					$tmp = explode('&',$val[0]);

					if ($tmp[1] == 'ds') {
						$val[0] = array($reports[$key]['ds'],$tmp[0]);
					}
					else {
						$val[0] = array(${$tmp[1]},$tmp[0]);
					}
				}
				$reports[$key]['ds']->registerFilter($k,array_shift($val),$val);
			}

			if (isset($flags) && in_array('class',$flags)) {
				$extra = $reports[$key]['filter']->extraData;
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

				$reports[$key]['ds'] =& $o->getDs();
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
			//echo $reports[$key]['ds']->preview(). "<br>";
			if (strlen($reports[$key]['hcserver']) > 0) {
				$reports[$key]['ds']->setHCServer($reports[$key]['hcserver']);
			}
			$reports[$key]['grid'] =& new cGrid($reports[$key]['ds']);
			$reports[$key]['grid']->pageSize = $pageSize;
			$reports[$key]['grid']->name = $key;
			if ($key != 'default') {
				$reports[$key]['title'] = $key;
			}

			// Setup export
			$reports[$key]['grid']->setExternalId($report_id);
			$reports[$key]['grid']->setExportAction('export_report');
			$reports[$key]['grid']->setExtraURI('&name=' . $reports[$key]['grid']->name);
			$reports[$key]['ds']->_type = 'html';
		}
		$view->assign_by_ref("reports",array_values($reports));
		$this->reports = $reports;
		$cg = new C_Graph();
		$visualizations = $cg->reportGraphs($this,$r);
		$view->assign("visualizations",$visualizations);

		if ($this->controller->GET->get('snapshot') == 'true' || $r->get('snapshot_style') == 1) {
			if (isset($view->rs)) {
				$rs =& $view->rs;
				$data = unserialize($rs->get('data'));
			}
			else {
				$rs =& Celini::newOrdo('ReportSnapshot');
				$view->rs =& $rs;
			}
			if (!isset($data['labels'])) {
				$data['labels'] = array();
			}
			if (!isset($data['data'])) {
				$data['data'] = array();
			}

			foreach(array_keys($reports) as $key) {
				$data['data'][$key] = $reports[$key]['ds']->toArray();
				$data['labels'][$key] = $reports[$key]['ds']->getColumnLabels();
			}

			$rs->set('report_id',$r->get('id'));
			$rs->set('template_id',$template_id);

			$rs->set('data',serialize($data));
			$rs->persist();
		}


		if ($template === "default") {
			if ($sr_template) {
				$return = '';
				if ($this->fetch) {
				$return = $view->render($sr_template);
				}
				if ($queries !== array('default' => "select 'No Query Found' error")) {
					$view->path = 'report';
					if ($this->fetch) {
					$return .= $view->render('embeddedView.html');
					}
				}
				return $return;
			}

			else {
				$view->path = 'report';
				if ($this->fetch) {
				return $view->render("view.html");
				}
				else {
				return true;
				}
			}
		} else {
			foreach(array_keys($reports) as $key) {
				$view->assign_by_ref($key.'_filter',$reports[$key]['filter']);
				$view->assign_by_ref($key.'_extra',$reports[$key]['filter']->extraData);

				$view->assign_by_ref($key.'_title',$reports[$key]['title']);
				$view->assign_by_ref($key.'_ds',$reports[$key]['ds']);
				$view->assign_by_ref($key.'_grid',$reports[$key]['grid']);
				$view->assign_by_ref($key.'_flags',$reports[$key]['flags']);
				//var_dump($key);
			}
			$em = EnumManager::getInstance();
			$view->assign_by_ref('em',$em);

			if ($this->fetch) {
			if (stripos($template,"pdf") === false) {
				return $view->fetch($template);
			}
			//template is PDF
			else {
				$header = 
'<?xml version="1.0" encoding="UTF-8"?><?xfa generator="XFA2_4" APIVersion="2.6.7116.0"?>
<xdp:xdp xmlns:xdp="http://ns.adobe.com/xdp/" timeStamp="2008-01-16T02:06:28Z" uuid="6aee0086-4ab9-40a0-8119-5a0f3d39220a">
<xfa:datasets xmlns:xfa="http://www.xfa.org/schema/xfa-data/1.0/">
<xfa:data>
<form1>';
$str = '';
	foreach($this->reports as $report) {
		$data = $report['ds']->toArray();
		$str .= ORDataObject::toXML($data,$report['grid']->name);
	}
$str .= 
'</form1>
</xfa:data>
</xfa:datasets>
<pdf href="'. $this->controller->view->_tpl_vars['base_uri'] ."index.php/Images/".basename($template).'" xmlns="http://ns.adobe.com/xdp/pdf/" />
</xdp:xdp>';
		if (isset($_GET['binaryHeader']) && $_GET['binaryHeader'] == true) {
			header("Content-type: application/binary");
                	header('Content-Disposition: attachment; filename="sample.xml"');
		}
		else {
                header("Content-type: application/vnd.adobe.xfdf");
		}
                echo $header.$str;exit;

			}
			}
		}
			return true;

	}
	
	/**
	 * Parse a stored query for {@link SqlGenerator} tags and run them
	 *
	 * @param  string
	 */
	function _parseSqlGenerators($query) {
		preg_match_all('/\{([a-z]+)(:([^\}]*))?\}/i', $query, $matches);
		
		foreach ($matches[1] as $key => $tagName) {
			$sqlGeneratorClass = 'ReportSqlGenerator_' . $tagName;
			$GLOBALS['loader']->requireOnce('includes/SqlGenerators/' . $sqlGeneratorClass . '.class.php');
			$obj =& new $sqlGeneratorClass();
			parse_str($matches[3][$key], $parameters);
			$query = str_replace($matches[0][$key], $obj->sql($parameters), $query);

		}
		
		return $query;
 	}
}
?>
