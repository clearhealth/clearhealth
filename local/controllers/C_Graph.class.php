<?php

$loader->requireOnce('/includes/ReportAction.class.php');
$loader->requireOnce('/ordo/GraphDefinition.class.php');
$loader->requireOnce('/includes/clniWidgetGraph.class.php');

class C_Graph extends Controller {

        function __contructor() {

        }

	function actionTestGraph() {
		$r = ORDataObject::factory('Report',36073312);
		$ra = new ReportAction();
		$ra->controller = new Controller();	
		$ra->action($r->get('report_id'),$r->get('default_template_id'));
		$reports = $ra->reports;
		$gDefs = GraphDefinition::getAllGraphDefsForReport($r->get('report_id'));
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
		$this->view->assign('graphImgs',$graphImgs);
		return $this->view->render('view.html');	
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
		return $this->view->render('graphDefinitions.html');
	}
}

?>
