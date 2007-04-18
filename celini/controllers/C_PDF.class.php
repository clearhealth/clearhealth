<?php

$loader->requireOnce('controllers/C_PageType.abstract.php');

/**
* The main controller is used to perform  wrapping dance around the controller that does the actual work
*/
class C_PDF extends C_PageType {

	var $template_mod;
	var $menu = false;
	var $_methodMap = array('Report_action_view'=> false);

	function C_PDF ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", $GLOBALS['config']['entry_file'] . $_SERVER['QUERY_STRING']);
		$this->assign("TOP_ACTION", $_SERVER['SCRIPT_NAME']."/main/");
		$this->assign("NAV_ACTION", $_SERVER['SCRIPT_NAME']."/");

		$GLOBALS['C_MAIN'] = array();

		$this->assign('translate',$GLOBALS['config']['translate']);
		$this->view->path = 'PDF';
	}

	function buildHtmldocCmd($fancy,$tmp_filename) {
		$options = " --webpage -t pdf14 ";
		if (!$fancy) {
			$options .= " --header ... --footer ... ";
		}
		if(isset($_GET['landscape'])){
			$options .= " --browserwidth 800 --landscape ";
		}

		$cmd = $GLOBALS['config']['htmldoc']." $options -f $tmp_filename --webpage ";
		return $cmd;

	}

	function buildPagePrintCmd($html_file,$tmp_file) {
		putenv('LD_LIBRARY_PATH=/usr/lib/mozilla');
		putenv('DISPLAY=:10');
		putenv('HOME=/tmp');
		$print = '';
		if(isset($_GET['printer'])) {
			$config =& Celini::configInstance();
			$printers = $config->get('printers');
			if(isset($printers[$_GET['printer']])) {
				$print = '-p '.$printers[$_GET['printer']]['systemName'];
				if(isset($printers[$_GET['printer']]['lprOptions'])) {
					foreach($printers[$_GET['printer']]['lprOptions'] as $option) {
						$print .= " -o $option";
					}
				}
			}
		} elseif(isset($_GET['print']) && $_GET['print']) {
			$config = Celini::configInstance();
			$printers = $config->get('default_printers');
			if(is_array($printers)) {
				if(isset($printers['default'])) {
					$printerdata = $config->get('printers');
					$print = ' -p '.$printerdata[$printers['default']]['systemName'];
					if(isset($printerdata[$printers['default']]['lprOptions'])) {
						foreach($printerdata[$printers['default']]['lprOptions'] as $option) {
							$print .= " -o $option";
						}
					}
				}
			}
		}
		$cmd = $GLOBALS['config']['pageprint'] ." file://$html_file $tmp_file $print";// -hn set-cookie -hv session_id".' > /tmp/ppdebug';
		return $cmd;
	}

	function display($display = "",$fancy= true) {
		$tmp_filename = tempnam("/tmp/","pdf-");


		$this->assign("display",$display);

		// Use HTMLDoc to convert to PDF
		$html = $this->view->render('list.html');
		$pdf = '';
		$error = false;
		$error_text = '';


		if (!isset($GLOBALS['config']['pdfGenerator'])) {
			$GLOBALS['config']['pdfGenerator'] = 'htmldoc';
		}

		switch($GLOBALS['config']['pdfGenerator']) {
			case "pageprint":
				$fname = tempnam("/tmp","html-"); 
				$fp = fopen($fname,'w');
				fwrite($fp,$html);
				fclose($fp);

				$cmd = $this->buildPagePrintCmd($fname,$tmp_filename);
				$process = popen($cmd,"r");
				if (is_resource($process)) {
				} else
				{
					$error = true;
					$error_text = "Could not exec pageprint: $cmd";	
				}


				$data = "";
				while(!feof($process)) {
					$data .= fgets($process,1024);
				}

				/*$output = exec($cmd,$retvar);
				var_dump($retvar);
				var_dump($output);
				*/
				break;
			default:
				$cmd = $this->buildHtmldocCmd($fancy,$tmp_filename);
				$fp = fopen($tmp_filename . '-html', 'w');
				if (is_resource($fp)) {
					fwrite($fp, $html);
					fclose($fp);
					
					exec($cmd . ' ' . $tmp_filename . '-html');
				} else
				{
					$error = true;
					$error_text = "Could not exec htmldoc";	
				}
				break;
		}


		if($error){
			return nl2br("Error Generating PDF:\n$error_text\n\n$html");
		} elseif(isset($_GET['print']) && $_GET['print']) {
			return 'Document printed.';
		} else
		{
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Content-type: application/pdf");
			header("Content-Disposition: inline; filename=report.pdf");
			$pdf = readfile($tmp_filename);
			unlink($tmp_filename);				
			return 	$pdf;			
		}
	}

	function empty_action() {
		return $this->display();
	}

	function magic_action($controller,$action,$managerArg = "") {
		$fga = func_get_args();
		array_splice($fga,0,3);
		return $this->run_child_controller($controller,$action,$managerArg,$fga);
	}
}
?>
