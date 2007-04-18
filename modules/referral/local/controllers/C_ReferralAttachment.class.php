<?php

$loader->requireOnce('controllers/C_DocSmartStorable.class.php');
$loader->requireOnce('includes/StorableNodesGrid.class.php');


class C_ReferralAttachment extends Controller
{
	function actionList($request_id) {
		$request =& Celini::newORDO('refRequest', $request_id);
		
		$ds = $request->loadDatasource('Documents');
		$grid =& new StorableNodesGrid($ds);
		
		$this->view->assign_by_ref('documentGrid', $grid);
		return $this->view->render('list.html');
	}
	
	function processAdd() {
		// handle actual add via DocSmart
		$storable =& new C_DocSmartStorable();
		$storable->processAdd();
		
		$storable =& $GLOBALS['DocSmart']['latestStorable'];
		//echo '<script type="text/javascript">alert("id: " + '. $storable->get('id') . ');</script>';
		$request =& Celini::newORDO('refRequest', $this->GET->getTyped('refRequest_id', 'int'));
		
		// todo: use setChild() when reference issue fixed
		$storable->setRelationship($request, 'child');
		$this->_state = false;
		return true;
	}
	
	function processRemove() {
		foreach($_GET['storables'] as $treeId) {
			$tree =& Celini::newOrdo('TreeNode', $treeId);
			$storable =& Celini::newORDO('Storable', $tree->get('node_id'));
			$storable->removeRelationship();
		}	

		$storable =& new C_DocSmartStorable();
		$storable->actionRemoveStorable();
		$this->_state = false;
		return true;
	}
}

?>
