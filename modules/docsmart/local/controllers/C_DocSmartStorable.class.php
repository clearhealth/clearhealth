<?
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('datasources/Tree_DS.class.php');
$loader->requireOnce('datasources/FoldersSearch_DS.class.php');
$loader->requireOnce('datasources/TagsSearch_DS.class.php');
$loader->requireOnce('datasources/StorablesSearch_DS.class.php');
$loader->requireOnce('datasources/Notes_DS.class.php');
$loader->requireOnce('datasources/Tags_DS.class.php');
$loader->requireOnce('datasources/Revisions_DS.class.php');
$loader->requireOnce('datasources/CloudTags_DS.class.php');
$loader->requireOnce('controllers/C_DocSmart.class.php');
$loader->requireOnce('includes/viewer/FileViewer.class.php');
$loader->requireOnce('includes/storage/StorageManager.class.php');
/**
 * Storable management controller
 *
 * @todo Move all of the extra stuff out of this controller.  This should only have methods related
 *     directly to creating, retrieving, updating, or deleting {@link Storable}s.  Any other methods
 *     should be on their own controller.
 */
class C_DocSmartStorable extends C_DocSmart {
	
	/**
	 * Default action
	 *
	 * @param integer $storableId
	 * @return rendered template
	 *
	 * @todo rename this to something descriptive and make actionDefault() an alias to it
	 */
	function actionDefault_view() {
		
		$this->view->assign('storableId', $this->GET->getTyped('tree_id', 'int'));
		return $this->view->render('storable.html');
	}		
	
	/**
	 * Shows details tab
	 *
	 * @param integer $storableId
	 * @return rendered template
	 */
	function actionDetails($storableId, $revisionId = null) {
		
		$node =& Celini::newOrdo('TreeNode', $storableId);
		$parent = $node->getParentNode();
		$storable =& Celini::newOrdo('Storable', $node->node_id);
		$revisionCurrent = $storable->getCurrentRevision();
		
		if(isset($revisionId)) {
			$revision =& Celini::newOrdo('Revision', $revisionId);
		}else{
			$revision = $revisionCurrent;
		}
		
		$notes = new Notes_DS($node->node_id);
		$tags = new CloudTags_DS($node->node_id);
		//print $tags->preview();

		$this->view->assign('tags', $tags->toArray());
		$this->view->assign('notes', $notes->toArray());
		$this->view->assign('revisionCurrent', $revisionCurrent->toArray());
		$this->view->assign('revision', $revision->toArray());
		$this->view->assign('node', $node->toArray());
		$this->view->assign('storable', $storable->toArray());
		if (is_object($parent)) {
			$this->view->assign('parent', $parent->toArray());
		}
		
		return $this->view->render('details.html');
	}	

	/**
	 * Show Information tab
	 *
	 * @param integer $storableId
	 * @return rendered template
	 */
	function actionInformation($storableId = null) {
		$node =& Celini::newOrdo('TreeNode', $storableId);
		$storable =& Celini::newOrdo('Storable', $node->node_id);
		$revisions = new Revisions_DS("tree.node_id = '".$node->node_id."'");
		$revisionListGrid =& new cGrid($revisions);
		$revisionListGrid->orderLinks = false;
		$revisionListGrid->registerTemplate('view', '<a href="javascript:void(0);" onclick="HTML_AJAX.replace(\'tabContent\', \''.$_SERVER['SCRIPT_NAME'].'/DocSmartStorable/details/{$tree_id}/{$revision_id}\')">view</a>');
		$this->view->assign('storable', $storable);		
		$this->view->assign_by_ref('revisionListGrid', $revisionListGrid);
		$this->view->assign('revisions', $revisions->toArray());		
		$this->view->assign('node', $node->toArray());
		return $this->view->render('information.html');
	}		

	/**
	 * Download the file
	 *
	 * @param integer $storableId
	 * @param integer $revisionId
	 */
	function actionDownload($storableId, $revisionId = null) {
		$storable =& Celini::newOrdo('Storable', $storableId);		
		if(isset($revisionId)) {
			$revision =& Celini::newOrdo('Revision', $revisionId);
		}else{
			$revision = $storable->getCurrentRevision();
		}

		$storage = new RevisionStorage($revision->revision_id, $storable->storage_type);
		$viewer = new FileViewer($storage->getFile(), $storable->filename);
		$viewer->download();
		exit;		
	}	
	
	/**
	 * Previw storable's revision if specified or current revision
	 *
	 * @param integer $storableId
	 * @param integer $revisionId
	 */
	function actionPreview($storableId, $revisionId = null) {
		$storable =& Celini::newOrdo('Storable', $storableId);		
		if(isset($revisionId)) {
			$revision =& Celini::newOrdo('Revision', $revisionId);
		}else{
			$revision = $storable->getCurrentRevision();
		}

		$storage = new RevisionStorage($revision->revision_id, $storable->storage_type);
		$viewer = new FileViewer($storage->getFile(), $storable->filename);
		$viewer->preview();
		exit;
	}			
	
	/**
	 * Creating new storable
	 * Adding it to the tree
	 * Creating new revision for this storable 
	 * Saving file data to the specified storage
	 *
	 * @return boolean
	 */
	function processAdd() {
		// save storable to the storables table	
		$storable=& Celini::newOrdo('Storable');
		$storable->populate_array( array(
				'filename' => $_FILES['storable']['name']['filename'],
				'storage_type' => $_POST['storable']['storage_type'],
				'webdavname' => $_POST['storable']['webdavname'],
				'mimetype' => Viewer::mimeContentType($_FILES['storable']['name']['filename']) ));	
		$storable->persist();
	
		// save revision to the revisions table
		if(!($revision = $this->_saveRevision($storable, $_FILES['storable']['tmp_name']['filename']))) {
			return false;
		}
		
		// add storable node to the tree
		$node =& Celini::newOrdo('TreeNode');
		$node->populate_array( array(
				'node_type' => 'storable',
				'node_id' => $storable->storable_id ));
		$node->insert($_POST['storable']['folder_id']);
		$this->view->assign('storable', $storable->toArray());

		if ($this->POST->get('embedded') == true) {
			$this->_state = false;
		}
		
		// todo: create a clniRegistry and move this to it
		$GLOBALS['DocSmart']['latestStorable'] =& $storable;
		
		return true;
	}
	
	
	function actionAdd() {
		$this->view->assign('mode', 'Add');
		return $this->actionEdit();
	}
	
	function actionEdit() {
		if (!$this->view->exists('mode')) {
			$this->view->assign('mode', 'Edit');
		}
		$redirectUrl = $this->get('redirectUrl');
		if (!is_null($redirectUrl)) {
			$this->view->assign('redirectUrl', $redirectUrl);
		}
		
		$this->view->assign('embedded', $this->GET->getTyped('embedded', 'htmlsafe'));
		$this->view->assign('tree_id', $this->GET->getTyped('tree_id', 'int'));
		return $this->view->render('edit.html');
	}

	/**
	 * Making new revision & storing file data to the speciied storage
	 *
	 * @return boolean
	 */
	function actionAddRevision() {
		
		$storable =& Celini::newOrdo('Storable', $_POST['revision']['storable_id']);
		if(!isset($storable->storable_id) || $storable->storable_id != $_POST['revision']['storable_id']) {
			return false;
		}
		
		// save revision to the revisions table
		if(!($revision = $this->_saveRevision($storable, $_FILES['revision']['tmp_name']['filename']))) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Remove storable from the tree
	 *
	 * @param integer $storableId
	 *
	 * @todo Remove "Storable" from name
	 */
	function actionRemoveStorable() {
		if(is_array($_GET['storables'])) {
			foreach($_GET['storables'] as $storableId) {
				$node =& Celini::newOrdo('TreeNode', $storableId);
				$node->delete(true);
			}	
		}	
		return true;
	}		

	/**
	 * Add note for storable and assigned it to specifiy revision
	 *
	 * @return rendered template
	 *
	 * @todo move into another DocSmartStorableNote controller
	 */
	function actionAddNote() {
		$storable =& Celini::newOrdo('Storable', $_POST['note']['storable_id']);
		if($_POST['note']['revision_id']) {
			$revision =& Celini::newOrdo('Revision', $_POST['note']['revision_id']);
		}else{
			$revision = $storable->getCurrentRevision();
		}	
		
		$me =& me::getInstance();		
		$note =& Celini::newOrdo('Note');
		$data = $_POST['note'];
		$data['revision_id'] = $revision->revision_id;
		$data['user_id'] = $me->get_id();
		$data['create_date'] = date('Y-m-d H:i:s');
		$note->populate_array($data);
		$note->persist();
		
		$note = $note->toArray();
		$revision = $revision->toArray();
		$note['revision'] = $revision['revision'];
		$this->assign('note', $note);
		return $this->view->render('note.html');		
	}
	
	/**
	 * Remove note 
	 *
	 * @return boolean
	 *
	 * @todo move into another DocSmartStorableNote controller
	 */
	function actionRemoveNote($noteId) {
		$note =& Celini::newOrdo('Note', $noteId);
		$note->drop();
		return true;
	}	

	/**
	 * Add tag for a storable
	 *
	 * @return rendered template
	 *
	 * @todo move into another DocSmartStorableTag controller
	 * @todo use $storable->loadDatasource() to load the tags ds
	 */
	function actionAddTag() {
		$storable =& Celini::newOrdo('Storable');
		$storable->setTag($_POST['tag']);
		$tags = new CloudTags_DS($_POST['tag']['storable_id']);
		$this->view->assign('tags', $tags->toArray());		
		return $this->view->render('tags_list.html');
	}

	/**
	 * Get could tags
	 *
	 * @return rendered template
	 */
	function actionCloudTags() {
		$tags = new CloudTags_DS();
		$this->view->assign('tags', $tags->toArray());		
		return $this->view->render('cloud_tags.html');
	}	
	
	/**
	 * Remove note 
	 *
	 * @return boolean
	 *
	 * @todo move into another DocSmartStorableTag controller
	 * @todo use $storable->loadDatasource() to load the tags ds
	 */
	function actionRemoveTags() {
		$tag =& Celini::newOrdo('TagStorable');
		$tag->bulkDrop(@$_POST['tagList'], $_POST['storableId']);
		$tags = new CloudTags_DS($_POST['storableId']);
		$this->view->assign('tags', $tags->toArray());				
		return $this->view->render('tags_list.html');
	}		

	function actionChangeStorageType($storbleId, $storageType) {
		$storable =& Celini::newOrdo('Storable', $storbleId);
		$storageManager = new StorageManager($storable);
		if($res = $storageManager->switchStorage($storageType)) {
			print $res;
		}
	}
	
	function actionSearch() {
		$folders = new FoldersSearch_DS($_POST['query']);
		$storables = new StorablesSearch_DS($_POST['query']);
		$tags = new TagsSearch_DS($_POST['query']);
		$this->view->assign('nodes', array_merge($folders->toArray(), $storables->toArray()));
		$this->view->assign('tags', $tags->toArray());
		return $this->view->render('search_results.html');
	}
	
	/**
	 * Saving revision for specified storable
	 *
	 * @param integer $storableId
	 * @return Revision
	 *
	 * @todo remove direct access to ORDO properties - should always go through get()
	 */
	function _saveRevision($storable, $filename) {
		$me =& me::getInstance();
		$revision =& Celini::newOrdo('Revision');
		$revision->populate_array( array(
				'storable_id' => $storable->storable_id,
				'user_id' => $me->get_id(),
				'create_date' => date('Y-m-d H:i:s') ));
		$revision->persist();
		
		// Save revision data to the storage	
		$storage = new RevisionStorage($revision->revision_id, $storable->storage_type);
		$filesize = $storage->saveFile($filename);
		if ($filesize == 0) {
			$revision->drop();
			return false;			
		}
		
		// update revision record wih actual filesize
		$revision->set('filesize', $filesize);
		$revision->persist();				

		// update last_revision_id for storable
		$storable->set('last_revision_id', $revision->revision_id);
		$storable->persist();		
		
		return $revision;
	}

}

?>