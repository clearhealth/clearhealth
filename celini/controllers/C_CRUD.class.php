<?php
/**
* Generic CRUD controller
*
* @package	com.uversainc.celini
* @author	Uversa Inc.
*
* Extends {@link Controller} see that for additional implementation and guideline details
* 
*/

$loader->requireOnce('/controllers/Controller.class.php');
$loader->requireOnce('/ordo/ORDataObject.class.php');

class C_CRUD extends Controller{

	var $_ordoName;
	var $_ordo;

 	function actionAdd() {
		$this->view->assign('addMode', true);
		return $this->actionEdit(0);
	}

	function actionEdit($id = 0) {
		$ordo = '';
		if (!is_object($this->_ordo)) {
		  if (!$id > 0) {
		    $id = $this->getDefault($this->_ordoName . "_id", '0');
		  }
		  $ordo =& Celini::newORDO($this->_ordoName, $id);
		}
		else { 
		  $id = $this->_ordo->get("id");
		  $ordo = $this->_ordo;
		}
		$this->assign("EDIT_ACTION", Celini::managerLink($id));
		$this->view->assign_by_ref('ordo', $ordo);
		return $this->view->render('edit.html');
	}

	function actionView($id) {
		$id = $this->getDefault($this->_ordoName . "_id", '0');
		$ordo =& Celini::newORDO($this->_ordoName, $id);
		$this->view->assign_by_ref('ordo', $ordo);
		return $this->view->render('view.html');
	}

	function actionList() {
		$dsname = $this->_ordoName.'_DS';
		global $loader;		
		if (!$loader->includeOnce('datasources/'.$dsname.'.class.php')) {
			return 'Datasource '.$dsname.' not found.';
		}
		$ds =& new $dsname();
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}

	function process(){
		$id = $this->getDefault($this->_ordoName . "_id", '0');
		$properties = $_POST[$this->_ordoName];
		$ordo =& Celini::newORDO($this->_ordoName, $id);
		$ordo->populate_array($properties);
		$ordo->persist();
		$this->_ordo = $ordo;
		$this->messages->addMessage("Updated " . $this->_ordoName);
	}
}
?>
