<?php
$loader->requireOnce('datasources/AppointmentTemplate_DS.class.php');
class C_AppointmentTemplate extends Controller {

	function actionAdd() {
		return $this->actionEdit(0);
	}

	function actionEdit($id = -1) {
		$at =& Celini::newOrdo('AppointmentTemplate',$id);

		$this->assign('FORM_ACTION',celini::link(true,true,true,$id));
		$this->assign_by_ref('at',$at);

		if ($id > 0) {
			$action = new DispatcherAction();
			$action->wrapper = false;
			$action->controller = 'OccurenceBreakdown';
			$action->action = 'edit';
			$action->defaultValue = $id;

			$d = new Dispatcher();
			$content = $d->dispatch($action);
			$this->assign('breakdown',$content);
		}
		return $this->view->render('edit.html');
	}

	function actionList() {

		$ds =& new AppointmentTemplate_DS();
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}

	function processAdd() {
		$id = $this->processEdit(0,true);
		header('Location: '.Celini::link('edit',true,true,$id));
	}

	function processEdit($id,$returnId = false) {
		$at =& Celini::newOrdo('AppointmentTemplate',$id);
		$at->populateArray($this->POST->get('AppointmentTemplate'));
		$at->persist();
		if ($returnId) {
			return $at->get('id');
		}
	}
}
?>
