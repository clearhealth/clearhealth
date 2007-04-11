<?
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('datasources/FolderNodes_DS.class.php');
$loader->requireOnce('datasources/StorableNodes_DS.class.php');
$loader->requireOnce('includes/DocSmartTreeRenderer.class.php');
/**
 * Storable management controller
 *
 */
class C_DocSmart extends Controller {
	
	var $folder = null;
	
	function C_DocSmart() {
		parent::Controller();
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = 'scriptaculous';
		if(isset($_SESSION['flash'])) {
			$this->view->assign('flash', $_SESSION['flash']);
			unset($_SESSION['flash']);
		}

		$this->view->assign('TAG_FORM_ACTION', Celini::link('addTag','DocSmartStorable',false));
		$this->view->assign('REVISION_FORM_ACTION', Celini::link('addRevision','DocSmartStorable',false));
		$this->view->assign('NOTE_FORM_ACTION', Celini::link('addNote','DocSmartStorable',false));
		$this->view->assign('FOLDER_FORM_ACTION', Celini::link('save',"DocSmartFolder",false));
		
		$this->_initStorableFormAction();
		
		$head =& Celini::HTMLHeadInstance();
		$head->addExternalCss('yahoo/folders');
		$head->addExternalCss('grid');
		$head->addExternalCss('screen');
	}
	
	/**
	 * Initial the StorableFormAction value by attempting to load it out of the the session.
	 *
	 * Allows integrating applications to override the controller/action that will handle the 
	 * file upload.  Generally, an integrating application would use this to provide a means of 
	 * setting relationships, etc., while still relying on {@link DocSmartStorable::processAdd()}
	 * to handle the actual add of the file.
	 *
	 * @access private
	 * @todo Switch this over to a clniRegistry once it's in place instead of clniSession
	 */
	function _initStorableFormAction() {
		//var_dump($_SESSION['_clniSession']);
		$session =& Celini::sessionInstance();
		$storableForm = $session->get('DocSmart:storableForm');
		if (!is_array($storableForm)) {
			$storableForm = array(
				'controller' => 'DocSmartStorable',
				'action'     => 'add'
			);
		}
		else {
			if (!isset($storableForm['controller'])) {
				$storableForm['controller'] = 'DocSmartStorable';
			}
			if (!isset($storableForm['action'])) {
				$storableForm['action'] = 'add';
			}
		}
		//$session->set('DocSmart:storableForm', $storableForm);
		
		$storableFormAction = Celini::link($storableForm['action'], $storableForm['controller'], false);
		if (isset($storableForm['extra'])) {
			$storableFormAction .= $storableForm['extra'];
		}
		//var_dump($session->get('DocSmart:storableForm'));
		//var_dump($_SESSION);
		$this->view->assign('STORABLE_FORM_ACTION', $storableFormAction);
	}

	/**
	 * Initialize a folder and add it to the view
	 *
	 * @param  int     $folderId
	 * @access private
	 *
	 * @todo 
	 */
	function _initFolder($folderId) {
		$treeNode =& Celini::newOrdo('TreeNode', $folderId);
		$folder =& Celini::newOrdo('Folder', $treeNode->node_id);
		$this->folder = array_merge($folder->toArray(), $treeNode->toArray());
		$this->view->assign('folder', $this->folder);
	}
	
	
	function actionDefault_view() {
		return $this->actionView();
	}
	
	/**
	 * Default view action
	 */
	function actionView() {
		$this->view->assign('treeView', $this->actionViewTree());
		return $this->view->render('default.html');
	}
	/**
	 * Default action
	 * Shows tree of the storables
	 *
	 * @return rendered template
	 */
	function actionViewTree() {
		$tree = new FolderNodes_DS(1, 0, null);
		$tree->registerTemplate('href', '');
		$renderer =& new DocSmartTreeRenderer();
		$renderer->setDataArray($tree->toArray());
		return $renderer->render();		
	}
	
	/**
	 * Returns add folder form
	 *
	 * @param integer $folderId
	 * @return rendered template
	 */
	function actionAddFolder($folderId = null) {
		$this->_initFolder($folderId);

		$tree = new FolderNodes_DS(1, 0, null);
		$this->view->assign('tree', $tree->toArray());
		
		$this->view->assign('parent', $this->folder);
		$this->view->assign('folder', array());
		return $this->view->render('folder_form.html');
	}
	
	/**
	 * Opens specified folder and shows storable list
	 *
	 * @param integer $folderId
	 * @return rendered template
	 *
	 * @todo Move to C_DocSmartFolder and rename it to actionView
	 */
	function actionOpenFolder($folderId) {
		//$folderId = str_replace("n","",$folderId);
		
		$this->_initFolder($folderId);
		$currentUrl = Celini::link('openFolder', 'DocSmart', false, $folderId);
		$this->view->assign('CURRENT_URL', $currentUrl);
		$this->set('redirectUrl', $currentUrl, 'C_DocSmartStorable');
		
		$this->GET->set('folder_id', $folderId);
		$this->GET->set('tree_id', $this->folder['tree_id']);
		$this->GET->set('embedded', 'true');
		
		$dispatcher =& new Dispatcher();
		$action =& new DispatcherAction();
		$action->controller = 'DocSmartStorable';
		$action->action = 'add';
		$this->view->assign('storableForm', $dispatcher->dispatch($action));
		
		// get parent node of the current selected folder
		$node =& Celini::newOrdo('TreeNode', $this->folder['tree_id']);
		$parentNode = $node->getParentNode(1);
		$this->view->assign('parent', $parentNode->toArray());
		
		// get folders tree
		$tree = new FolderNodes_DS(1, 0, null);
		$this->view->assign('tree', $tree->toArray());
		
		// get storables from the sopecified folder
		$storableList = new StorableNodes_DS($this->folder['tree_id'], $this->folder['level']);
	
		$this->view->assign_by_ref('storableListGrid',C_DocSmart::getStorablesGrid($storableList));
		$this->view->assign('storableList', $storableList->toArray());

		return $this->view->render('folder.html');		
	}
	
	function getStorablesGrid($storableList) {
		$storableListGrid =& new cGrid($storableList);
		$storableListGrid->orderLinks = false;
		$storableListGrid->setLabel('delete','<input type="checkbox" id="bulkChecker" onclick=\'changeSatatus($("bulkDelete").getElementsByTagName("input"), this)\'>');				
		$storableListGrid->registerTemplate('filename', '<a href=\'javascript:void(0)\' onclick="HTML_AJAX.replace(\'content\', \''.Celini::link('default','DocSmartStorable',false).'tree_id={$tree_id}\');">{$filename}</a>');
		$storableListGrid->registerTemplate('delete', '<input type="checkbox" name="storables[]" value="{$tree_id}">');
		return $storableListGrid;
	}

}

?>
