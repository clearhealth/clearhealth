<?php

$loader->requireOnce('controllers/C_PageType.abstract.php');
$loader->requireOnce('controllers/C_CriticalView.class.php');
$loader->requireOnce('includes/Menu.class.php');
$loader->requireOnce('includes/Grid.class.php');

/**
* The main controller is used to perform  wrapping dance around the controller that does the actual work
*/
class C_Main extends C_PageType {

	/**
	 * @var private
	 * @todo remove this in favor of setting {@link clniView::$templateType}
	 */
	var $template_mod;
	var $menu = false;
	
	function C_Main ($template_mod = "general") {
		parent::C_PageType();
		$this->template_mod = $template_mod;
		$this->view->templateType = $template_mod;
		if (isset($_SERVER['QUERY_STRING'])) {
			$this->assign("FORM_ACTION", $GLOBALS['config']['entry_file'] . $_SERVER['QUERY_STRING']);
		}
		else {
			$this->assign("FORM_ACTION", $GLOBALS['config']['entry_file']."?");
		}
		$this->assign("TOP_ACTION", $_SERVER['SCRIPT_NAME']."/main/");
		$this->assign("NAV_ACTION", $_SERVER['SCRIPT_NAME']."/");
		if (!isset($GLOBALS['style'])) {
			$GLOBALS['style'] = "";
		}
		$this->assign("STYLE", $GLOBALS['style']);
		if (isset($_GET['set_print_view'])) {
			$this->_print_view = true;
		}
		
		$GLOBALS['C_MAIN'] = array();

		if (isset($GLOBALS['config']['use_menu']) && $GLOBALS['config']['use_menu']) {
			$this->menu =& Menu::getInstance();
			$this->assign('menu_group',$this->menu->getSection());
		}
		if ($this->get('patient_id','c_patient') >  0) {
		  $p =& new C_CriticalView();
		  $criticalview = $p->actionViewCriticals();
		  $this->assign("criticalview",$criticalview);
		}

		if (isset($GLOBALS['config']['extra_css']) && is_array($GLOBALS['config']['extra_css'])) {
			$this->assign('extra_css', $GLOBALS['config']['extra_css']);
		}

		$this->assign('translate',$GLOBALS['config']['translate']);
		
		// If it's set, assign the base_dir
		if (isset($GLOBALS['config']['base_dir'])) {
			$this->assign('base_dir', $GLOBALS['config']['base_dir']);
		}
	}

	function display($display = '') {
		$this->_setupDisplay($display);
		return $this->view->render($this->_determinePage() . '.html');	
	}
	
	
	/**
	 * Handles the setup of values for {@link display()}
	 *
	 * @param  mixed
	 * @access protected
	 */
	function _setupDisplay($display) {
		$this->view->assign_by_ref('HTMLHead',clniHTMLHead::getInstance());
		
		// add the prepend/append files if they exists
		$mainPath = $this->view->path;
		$this->view->path = strtolower(Celini::getCurrentController());
		if ($this->view->templateExists('prepend.html') !== false) {
			$display = $this->view->render('prepend.html') . $display; 
		}
		if ($this->view->templateExists('append.html') !== false) {
			$display .= $this->view->render('append.html');
		}
		$this->view->path = $mainPath;
				
		$this->assign('display',$display);

		foreach($GLOBALS['C_MAIN'] as $key => $val) {
			$this->assign($key,$val);
		}

		if (isset($GLOBALS['util']) && $GLOBALS['util'] == true) {
			return $display;
		}
		
		if ($this->menu && is_object($this->menu)) {
			$this->assign('menu',$this->menu->toArray());
			$this->assign('menu_group',$this->menu->getSection());
			$this->assign('menu_current',$this->menu->getCurrent());
		}
	}
	
	
	/**
	 * Determines and return the page name to load
	 *
	 * @return string
	 * @access protected
	 */
	function _determinePage() {
		if ($this->_print_view) {
			return 'print';
		}
		return parent::_determinePage();
	}
	
	
	function actionDefault() {
		if (!$GLOBALS['config']['require_login'] || $this->_me->get_id() > 0) {
			Celini::redirectDefaultLocation();
		}
		else {
			Celini::redirect('access', 'login');
		}
	}
	
	/**
	 * This handles all requests to export grids.
	 *
	 * This will create a file of the type $to for the DS of the name $grid 
	 * and return it as a downloadable file.
	 *
	 * Note: All execution stops after this has been executed.
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 */
	function export_grid_action($to, $dsName, $external_id) {
		$mimeType = $this->_checkMimeType($to);
		
		include_once CELINI_ROOT . '/includes/DatasourceFileLoader.class.php';
		$loader =& new DatasourceFileLoader();
		$loader->load($dsName);
		$ds =& new $dsName($external_id);
		
		include_once CELINI_ROOT . '/includes/Grid.class.php';
		$grid =& new cGrid($ds);
		$grid->pageSize = 65000;
		$grid->name = $this->GET->get('gridName');

		$this->_sendGridToBrowser($grid, $dsName . '-' . date("dmYHis"), $to);
	}
	
	
	/**
	 * This handles requests to export part of a report
	 *
	 * @todo This is going to be a big ol' ugly method until we get the
	 *    datasource and grid portion of report generation out of 
	 *    {@link Controller::report_action_view()} and into it's own object.
	 */
	function export_report_action($to, $external_id, $name) {
		$mimeType = $this->_checkMimeType($to);
		
		$GLOBALS['loader']->requireOnce("includes/ReportFilter.class.php");
		$GLOBALS['loader']->requireOnce("includes/ReportAction.class.php");

		$reportAction = new ReportAction();

		$r =& ORDataObject::factory('Report',$external_id);

		$queries = $r->get('exploded_query');
		if (count($queries) == 0) {
			$queries['default'] = $r->get('query');
		}
		foreach($queries as $key => $query) {
			$query = $reportAction->_parseSqlGenerators($query);
			// don't worry about the other queries in the report
			if (!preg_match('/^' . $name . '(,(.+))?$/', $key)) {
				continue;
			}
			if (strstr($key,',')) {
				$flags = explode(',',$key);
				$key = array_shift($flags);
				$report['flags'] = $flags;
			}
			$report['filter'] =& new ReportFilter($query);

			$report['ds'] =& $report['filter']->getDatasource();
			foreach($report['filter']->dsFilters as $k => $val) {
				if (strstr($val[0],'&')) {
					$tmp = explode('&',$val[0]);

					if ($tmp[1] == 'ds') {
						$val[0] = array($report['ds'],$tmp[0]);
					}
					else {
						$val[0] = array(${$tmp[1]},$tmp[0]);
					}
				}
				$report['ds']->registerFilter($k,array_shift($val),$val);
			}

			if (isset($flags) && in_array('class',$flags)) {
				$extra = $report['filter']->extraData;
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

				$report['ds'] =& $o->getDs();
			}

			$report['grid'] =& new cGrid($report['ds']);
			$report['grid']->pageSize = 65000;
			$report['grid']->name = $key;
			if ($key != 'default') {
				$report['title'] = $key;
			}
			
			$report['ds']->_type = $to;
		}

		$toReplace = array(':', '/', ' ');
		$filename = str_replace($toReplace, '_', $r->get('label'));
		if ($name != 'default') {
			$filename .= '-' . str_replace($toReplace, '_', $name);
		}
		$this->_sendGridToBrowser($report['grid'], $filename, $to);
	}
	
	
	/**
	 * Sends a Grid to the browser as a file
	 *
	 * @param object
	 * @param string
	 * @param string
	 */
	function _sendGridToBrowser(&$grid, $filename, $filetype) {
		$mimeType = $this->_checkMimeType($filetype);
		
		$rendererName = 'Grid_Renderer_' . $filetype;

		// big hack
		$f = $filetype.'Renderer';
		if (isset($grid->_datasource->$f)) {
			$c = $grid->_datasource->$f;
			$GLOBALS['loader']->requireOnce("includes/$c.class.php");
			$grid->set_renderer(new $c());
		}
		else {
			$grid->set_renderer(new $rendererName());
		}
		$grid->setOutputType($filetype);
		
		$this->_sendFileDownloadHeaders($mimeType, $filename. '.' . $filetype);
		echo $grid->render(false);
		exit;
	}
	
	/**
	 * Determine whether or not a given type is allowed and returns the 
	 * mime-type string.
	 *
	 * This should eventually be its own object and would throw an exception in
	 * PHP 5 on an error so a higher level could attempt to recover and present
	 * something meaningful.
	 *
	 * @param string
	 * @return string
	 */
	function _checkMimeType($to) {
		static $mimeTypes = array('csv' => 'text/csv');
		if (!isset($mimeTypes[$to])) {
			die('Unrecognized export type: ' . htmlspecialchars($to));
		}
		
		return $mimeTypes[$to];
	}

	/**
	 * Displays the CSV icon
	 */
	function displayexporttocsvicon_action() {
		header('Content-Type: image/png');
		$img = imagecreatefrompng(CELINI_ROOT . '/images/export-to-csv.png');
		imagepng($img);
	}
	
	function empty_action() {
		return $this->view->render('list.html');
	}
}
?>
