<?php

$loader->requireOnce('/includes/ReportAction.class.php');
$loader->requireOnce('/ordo/GraphDefinition.class.php');
$loader->requireOnce('/includes/clniWidgetGraph.class.php');

class C_Graph extends Controller {

        function __contructor() {

        }
	function &reportGraphs($ra,$report) {
		$ra->controller = new Controller();	
		$reports = $ra->reports;
		$gDefs = GraphDefinition::getAllGraphDefsForReport($report->get('report_id'));
		$cachePath = APP_ROOT . "/tmp/pic_cache/";
		$graphImgs = array();
		foreach($gDefs as $gDef) {
			$rnames = array_keys($reports);
			$data = array();
			foreach($rnames as $rname) {
			if (in_array($rname,$gDef->get('querylinks'))) {
			$data[] = $reports[$rname]['ds']->toArray();
			}
			}
                	$gDef->data = $data;
                	$gDef->setup();
			$filename = $cachePath.$gDef->get('graph_type').'Graph-'.uniqid() .".jpg";
                	$gDef->writeGraph($filename);
			$graphImgs[] = basename($filename);
		}
		return $graphImgs;
	}
	function actionImage($imgname) {
		$cachePath = APP_ROOT . "/tmp/pic_cache/";
		$img = $cachePath . preg_replace('/[^a-zA-Z-0-9\.]/','',$imgname);
                $mtime = filemtime($img);
                $etag = md5($img.$mtime);
                header("Last-Modified: ".gmdate("D, d M Y H:i:s") . ' GMT');
                $info = getImageSize($img);
                header("Content-Type: {$info['mime']}");
                header('Content-Length: '.filesize($img));
                readfile($img);
                exit;
	}
	function actionShowGraphDefinitions($externalId) { 
		$graphGrid = GraphDefinition::getGraphsByReportId((int)$externalId);
		$this->view->assign('graphGrid',$graphGrid);
		$this->view->assign('externalId',(int)$externalId);
		return $this->view->render('graphDefinitions.html');
	}
	function actionAdd($externalId) {
		return $this->actionShowGraphDefinitions((int)$externalId);
	}
	function processAdd($externalId) {
		$gd = ORDataObject::factory('GraphDefinition');
		$externalId = (int)$externalId;
		$gdData = $_POST['graphDefinition'];
		$gd->populateArray($gdData);
		$gd->set('externalId',$externalId);
		$gd->persist();
	}
}

?>
