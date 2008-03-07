<?php
class C_RSS extends Controller {

	function actionReport_view($report_id,$template_id=0) {
		$controller = new Controller(); 
		$controller->actionReport_view($report_id,$template_id);
		$reports = array();
		foreach($controller->view->_tpl_vars['reports'] as $report) {
		$reports[] = $report['ds']->toArray();
		}
		$this->view->assign('reports',$reports);
		header('Content-Type: application/rss+xml;');
		return $this->view->render("rss.tpl.xml");
	}
}
?>
