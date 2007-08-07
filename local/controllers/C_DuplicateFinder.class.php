<?php
$GLOBALS['loader']->requireOnce('includes/Grid.class.php');
$GLOBALS['loader']->requireOnce('datasources/DuplicateFinder_DS.class.php');

class C_DuplicateFinder extends Controller {

	function __contruct() {
		parent::Controller();
	}

	function actionFind_view() {
		$dfds = new DuplicateFinder_DS();
		if (isset($_SESSION['duplicateFinder'])) {
			$dfds->_threshold = (int)$_SESSION['duplicateFinder']['threshold'];	
			$dfds->_searchtype = $_SESSION['duplicateFinder']['searchtype'];
		}
		$this->view->assign('searchtype',$dfds->_searchtype);	
		$this->view->assign('threshold',$dfds->_threshold);	
		$dfgrid = new cGrid($dfds);
		$dfgrid->pageSize = 5;
		$this->view->assign('dfgrid',$dfgrid);
		return $this->view->render("find.html");
	}
	function processFind() {
		$_SESSION['duplicateFinder'] = array();
		$_SESSION['duplicateFinder']['searchtype'] = $_POST['searchtype'];
		$_SESSION['duplicateFinder']['threshold'] = $_POST['threshold'];
	}
}

?>
