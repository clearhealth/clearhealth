<?php

class C_Altnotice extends Controller
{
	function actionAdd() {
		//$this->view->assign('user_id', $this->GET->getTyped('user_id', 'int'));
		
		if ($this->GET->exists('embedded')) {
			$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http'; 
			$this->view->assign('redirectURL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
		
		if ($this->GET->exists('owner_type')) {
			$this->view->assign('owner_type', $this->GET->getTyped('owner_type', 'htmlsafe'));
		}
		else {
			$this->view->assign('owner_type', 'User');
		}
		if ($this->GET->exists('owner_id')) {
			$this->view->assign('owner_id', $this->GET->getTyped('owner_id', 'htmlsafe'));
		}
		else {
			$this->view->assign('owner_id', $this->GET->getTyped('user_id', 'htmlsafe'));
		}
		
		$this->view->assign('FORM_ACTION', Celini::link('add', 'altnotice'));
		return $this->view->render('add.html');
	}
	
	function processAdd() {
		$notice =& Celini::newORDO('altNotice');
		$notice->populate_array($_POST['altNotice']);
		$notice->persist();
		
		if (isset($_POST['redirectURL'])) {
			header('Location: ' . $_POST['redirectURL']);
			exit;
		}
	}
	
	function processDelete() {
		$alert =& Celini::newORDO('altNotice', $this->GET->getTyped('alert_id', 'int'));
		$alert->set('deleted', 1);
		$alert->persist();
		
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}
	
	function actionAddEdit($altnotice_id = 0) {
		global $loader;
		$loader->requireOnce('inclues/DatasourceFileLoader.class.php');
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('altNoticeByGroupListForEditing_DS');
		
		$ds =& new altNoticeByGroupListForEditing_DS(
			$this->GET->getTyped('owner_type', 'htmlsafe'),
			$this->GET->getTyped('owner_id', 'htmlsafe')
		);
		if ($this->GET->exists('embedded')) {
			$ds->target = 'altNoticeAdmin';
		}
		$grid =& new cGrid($ds);
		$grid->name = 'AlertList';
		$this->view->assign_by_ref('grid', $grid);
		
		// setup edit
		$altnotice =& Celini::newORDO('altNotice', $altnotice_id);
		$this->view->assign('editMode', $altnotice->isPopulated());
		$this->view->assign_by_ref('altnotice', $altnotice);
		
		$formAction = Celini::link('addEdit', 'altnotice');
		if ($this->GET->exists('embedded')) {
			$formAction = str_replace('/main/', '/minimal/', $formAction);
		}
		foreach ($this->GET->keys() as $key) {
			$formAction .= '&' . $key . '=' . $this->GET->getTyped($key, 'htmlsafe');
		}
		$this->view->assign('FORM_ACTION', $formAction);
		return $this->view->render('addEdit.html');
	}
	
	function processAddEdit($altnotice_id) {
		$altnotice =& Celini::newORDO('altNotice', $altnotice_id);
		$altnotice->populate_array($_POST['altNotice']);
		$altnotice->persist();
	}
	
	function actionList() {
		global $loader;
		$loader->requireOnce('inclues/DatasourceFileLoader.class.php');
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('altNoticeByGroupListForEditing_DS');
		
		$ds =& new altNoticeByGroupListForEditing_DS(
			$this->GET->getTyped('owner_type', 'htmlsafe'),
			$this->GET->getTyped('owner_id', 'htmlsafe')
		);
		if ($this->GET->exists('embedded')) {
			$ds->target = 'altNoticeAdmin';
		}
		$grid =& new cGrid($ds);
		$grid->name = 'AlertList';
		$this->view->assign_by_ref('grid', $grid);
		
		$addURL = Celini::link('edit', 'altnotice');
		if ($this->GET->exists('embedded')) {
			foreach ($this->GET->keys() as $key) {
				$addURL .= '&' . $key . '=' . $this->GET->getTyped($key, 'htmlsafe');
			}
			$addURL = str_replace('/main/', '/minimal/', $addURL);
		}
		$this->view->assign('addURL', $addURL);
		return $this->view->render('list.html');		
	}
	
	function actionEdit($altnotice_id = 0) {
		$altnotice =& Celini::newORDO('altNotice', $altnotice_id);
		$this->view->assign('editMode', $altnotice->isPopulated());
		$this->view->assign_by_ref('altnotice', $altnotice);
		
		$formAction = Celini::link('edit', 'altnotice');
		$listURL = Celini::link('list', 'altnotice');
		if ($this->GET->exists('embedded')) {
			$formAction = str_replace('/main/', '/minimal/', $formAction);
			$listURL = str_replace('/main/', '/minimal/', $listURL);
                        $this->view->assign('redirect_action', $_SERVER['HTTP_REFERER']);
		}
		foreach ($this->GET->keys() as $key) {
			$formAction .= '&' . $key . '=' . $this->GET->getTyped($key, 'htmlsafe');
			$listURL .= '&' . $key . '=' . $this->GET->getTyped($key, 'htmlsafe');
		}
		$this->view->assign('FORM_ACTION', $formAction);
		$this->view->assign('listURL', $listURL);
		$this->view->assign('editMode', $altnotice->isPopulated());
		return $this->view->render('edit.html');
	}
	
	function processEdit($altnotice_id = 0) {
		$altnotice =& Celini::newORDO('altNotice', $altnotice_id);
		$altnotice->populate_array($_POST['altNotice']);
		$altnotice->persist();
		
		if ($_POST['redirect_action']) {
			header('Location: ' . $_POST['redirect_action']); 
			exit;
		}
	}
}

