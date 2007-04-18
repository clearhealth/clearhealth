<?php
$loader->requireOnce('/includes/altPostOffice.class.php');

class C_Altnoticelist extends Controller
{
	function C_Altnoticelist() {
		$this->Controller();
		$this->view->templateExtType = 'html';
	}
	
	
	function actionListByGroup_list() {
		$name = $this->GET->get('name');
		$type = $this->GET->exists('type') ? $this->GET->get('type') : 'ACL Group';
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('altNoticeByGroupList_DS');
		$noticeList =& new altNoticeByGroupList_DS($name, $type);
		
		$noticeListGrid =& new cGrid($noticeList);
		$noticeListGrid->indexCol = false;
		$noticeListGrid->pageSize = 6;
		
		$this->view->assign_by_ref('noticeListGrid', $noticeListGrid);
		$this->view->assign('groupName', ucwords($name));
		
		$returnString = '';
		if ($this->GET->get('embedded')) {
			// todo: update so its portable
			$returnString .= '<link rel="stylesheet" href="/chlreferral/index.php/css/view/celini.css" type="text/css" />';
		}
		return $returnString . $this->view->render('list');
	}
	
	function actionSystemAlerts_list() {
		global $loader;
		$loader->requireOnce('includes/altAlertListByTypeAndName.class.php');
		
		$alertList =& new altAlertListByTypeAndName('System', 'Login Alerts');
		$alertListText = '';
		while(($alert =& $alertList->nextAlert()) !== false) {
			$this->view->assign_by_ref('alert', $alert);
			$alertListText .= $this->view->render('systemalerts_individual');
		}
		
		$this->view->assign('alertbody', $alertListText);
		$this->view->assign('popup', $this->GET->exists('popup'));
		if ($this->GET->exists('popup')) {
			$ajax =& Celini::ajaxInstance();
			$ajax->jsLibraries[] = 'clniPopup';
		}
		return $this->view->render('systemalerts');
	}
	
	function actionPersonalAlerts_list() {
		global $loader;
		if ($loader->requireOnce('includes/chlPersonalAlertList.class.php')) {
			$alertList =& new chlPersonalAlertList($this->GET->get('user_id'));
		}
		else {
			$alertList =& new altPersonalAlertList($this->GET->get('user_id'));
		}
		
		$alertListText = '';
		$this->view->assign('DELETE_URL', Celini::link('delete', 'altnotice') . 'process=true');
		while(($alert =& $alertList->nextAlert()) !== false) {
			$this->view->assign_by_ref('alert', $alert);
			$alertListText .= $this->view->render('personalalerts_individual');
		}
		
		if (empty($alertListText)) {
			$alertListText = false;
		}
		
		$this->view->assign('alertbody', $alertListText);
		return $this->view->render('personalalerts');
	}
}

