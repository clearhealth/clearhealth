<?php

class C_DocSmartFolder extends Controller
{
	/**
	 * Returns edit folder form
	 *
	 * @param integer $folderId
	 * @return rendered template
	 *
	 * @todo determine if this is actually used - if not, remove.  This is broken after it was
	 *     moved from C_DocSmart - it apparently isn't used as I've noticed no errors. 
	 */
	function actionEdit($folderId) {

		$this->_addFolder($folderId);
		$node =& Celini::newOrdo('TreeNode', $this->folder['tree_id']);
		$parentNode = $node->getParentNode(1);
		$this->view->assign('parent', $parentNode->toArray());
		
		$tree =& new FolderNodes_DS(1, 0, null);
		$this->view->assign('tree', $tree->toArray());
		
		return $this->view->render('folder_form.html');
	}
	
	
	/**
	 * Save folder & folder as tree node
	 *
	 * @return redirect
	 */
	function processSave() {
		// save folder
		$folder =& Celini::newOrdo('Folder');
		$folder->populate_array($_POST['folder']);
		$folder->persist();
	
		// save tree node
		if($_POST['folder']['tree_id']) {
			// move folder to the specified parent tree node
			$node =& Celini::newOrdo('TreeNode', $_POST['folder']['tree_id']);
			$node->node_id = $folder->folder_id;
			$node->node_type = 'folder';
			$parent = $node->getParentNode();
			if($parent->tree_id != $_POST['folder']['parentId']) {
				if(!$node->moveNode($_POST['folder']['parentId'])) {
					$_SESSION['flash']['error'] = "Can't move folder to the specified parent";
				}
			}	
		}
		else {
			// insert folder into the specified parent tree node
			$node =& Celini::newOrdo('TreeNode');
			$data = array(
				'tree_id' => $_POST['folder']['tree_id'],
				'node_id' => $folder->folder_id,
				'node_type' => 'folder');
			$node->populate_array($data);
			$node->insert($_POST['folder']['parentId']);
		}
		if(!isset($_SESSION['flash']['error'])) {
			$_SESSION['flash']['notice'] = "Folder has been saved successfully.";
		}
		
		Celini::redirectURL(Celini::link('default','DocSmart', 'main'));
	}
	
	
	/**
	 * Remove folder with all children from the tree
	 *
	 * @param integer $folderId
	 */
	function processRemove($folderId) {
		$node =& Celini::newOrdo('TreeNode', $folderId);
		if($node->delete(true)) {
			$_SESSION['flash']['notice'] = 'Folder has been deleted successfully';
		}	
		Celini::redirect('default', 'DocSmart');
	}
}

?>
