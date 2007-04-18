<?php
/**
* Controller for Enumerations
*
* @package	com.uversainc.celini
*/

$loader->requireOnce('includes/EnumManager.class.php');

/**
*
*/
class C_Enumeration extends Controller {
	 
	/**
	* Sets up TOP_ACTION
	*/
	function C_Enumeration ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Celini::link('default'));
	}

	/**
	* If no action is specified use the list action
	*/
	function actionDefault_view() {
		return $this->actionList();
	}

	/**
	* List all Enumerations
	*/
	function actionList() {
		$this->assign("FORM_ACTION", Celini::link('edit'));
		
		$ed =& Celini::newORDO('EnumerationDefinition');

		$ds =& $ed->loadDataSource('List');

		$grid =& new cGrid($ds);
		$grid->prepare();

		$this->assign_by_ref('grid',$grid);

		$this->assign('edit_action',Celini::link('edit'));

		return $this->view->render('list.html');
	}

	/**
	 * Edit an enum by an <i>$id</i>.
	 *
	 * Will redirect to /list if no <i>$id</i> is specified
	 *
	 * @param  int
	 * @return string
	 * @see    _handleEditDisplay()
	 */
	function actionEdit($id = 0) {
		if (isset($this->enumerationId)) {
			$id = $this->enumerationId;
		}else{
			$rawGet = Celini::filteredGet();
			if($rawGet->exists('id')){
				$id = $rawGet->getTyped('id', 'int');
			}
		}
		
		$ed =& Celini::newORDO('EnumerationDefinition',$id);
		
		$fa = Celini::link('edit', true, true, $ed->get('id'));
		$gets = array();
		foreach($_GET as $key=>$val) {
			if(!is_numeric($key) && !empty($key) && $this->GET->exists($key)) {
				$gets[]="$key=".$this->GET->get($key);
			}
			$fa.=implode('&',$gets);
		}
		$this->view->assign("FORM_ACTION",$fa);
		
		return $this->_handleEditDisplay($ed);
	}
	
	/**
	 * Edit an enum by its <i>$name</i>
	 *
	 * @param  string
	 * @return string
	 * @see    _handleEditDisplay()
	 */
	function actionEditByName_edit($name) {
		$ed =& Celini::newORDO('EnumerationDefinition', $name, 'ByName');
		$this->view->assign('FORM_ACTION', Celini::link('editbyname') . 'name=' . $ed->get('name'));
		
		return $this->_handleEditDisplay($ed);
	}

	/**
	* Create a new enumeration
	*/
	function actionAdd()
	{
		return $this->actionEdit($e = null);
	}

	/**
	 * Process edit submissions using an ID lookup.
	 *
	 * @param int
	 * @see   _handleProcessEdit()
	 */
	function processEdit($id = 0) {
		$ed = Celini::newORDO('EnumerationDefinition',$id);
		$this->_handleProcessEdit($ed);
	}
	
	/**
	 * Process edit submissions using a name lookup.
	 *
	 * @param string
	 * @see   _handleProcessEdit()
	 */
	function processEditByName_edit($name) {
		$ed =& Celini::newORDO('EnumerationDefinition', $name, 'ByName');
		$this->_handleProcessEdit($ed);
	}

	function actionExport($id) {
		$ed =& Celini::newORDO('EnumerationDefinition', $id);
		$manager =& EnumManager::getInstance();
		$manager->editing = true;
		$enum =& $manager->enumList($ed->get('name'));
		$this->assign_by_ref('enum',$enum);
		$enumArray = array();
		for($enum->rewind();$enum->valid();$enum->next()) {
			$row = $enum->current();
			unset($row['enumeration_id']);
			unset($row['enumeration_value_id']);
			$enumArray[] = $row;
		}
		$this->view->assign('data',$enumArray);
		$this->view->assign_by_ref('def',$ed);

		header('Content-Type: text/xml');
		return $this->view->render('export.xml');
	}

	function actionExportAll() {
		$ed =& Celini::newORDO('EnumerationDefinition');

		$ds =& $ed->loadDataSource('List');
		
		$ret = "<enumerations>\n";
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			$row = $ds->get();
			$ret .= $this->actionExport($row['enumeration_id']);
		}
		$ret .= "</enumerations>";
		return $ret;
	}
	
	/**
	 * Handles the actual display of an edit form for an enum.
	 *
	 * This relies on one of the valid actionEdit* methods to setup the 
	 * {@link EnumerationDefinition} and pass it in.
	 *
	 * @param  EnumerationDefinition
	 * @return string
	 * @access private
	 * @see    actionEdit(), actionEditByName()
	 */
	function _handleEditDisplay(&$ed) {
		assert('is_a($ed, "EnumerationDefinition")');
		
		$this->assign_by_ref("ed",$ed);

		if ($ed->get('id') > 0) {
			$manager =& EnumManager::getInstance();
			$manager->editing = true;
			$enum =& $manager->enumList($ed->get('name'));
			$this->assign_by_ref('enum',$enum);

			$ajax =& Celini::ajaxInstance();

			$enumArray = array();
			$orderCheck = array();
			for($enum->rewind();$enum->valid();$enum->next()) {
				$row = $enum->current();
				$enumArray[] = $row;
				$orderCheck[$row->sort] = $row->sort;
			}
			if (count($orderCheck) < count($enumArray)) {
				$keys = array_keys($enumArray);
				$i = 0;
				foreach($keys as $key) {
					$enumArray[$key]->sort = $i++;
				}
			}
			$enumList = $ajax->jsonEncode($enumArray);
			$this->assign('enumList',$enumList);

			$def = $enum->type->definition;
			$builtin = array('boolean','order','hidden');
			foreach($def as $key => $val) {
				if (isset($val['type']) && !in_array($val['type'],$builtin)) {
					$def[$key]['template'] = $enum->type->jsWidget($key,$val);
				}
			}
			$this->assign('enumDef',$def);
		}
		else {
			$this->assign('enumList','{}');
		}
		
		return $this->view->render('edit.html');
	}
	
	/**
	 * Handle the actual processing of an edit form once the 
	 * {@link EnumerationDefinition} has been defined.
	 *
	 * @param EnumerationDefinition
	 * @see   processEdit(), processEditByName_edit()
	 */
	function _handleProcessEdit(&$ed) {
		assert('is_a($ed, "EnumerationDefinition")');
		
		$ed->populate_array($_POST['EnumerationDefinition']);
		$ed->persist();
		$this->enumerationId= $ed->get('id');

		if (isset($_POST['enumList'])) {
			$manager =& EnumManager::getInstance();
			$enum =& $manager->enumList($ed->get('name'));
			$enum->type->editing = true;
			$enum->updateValues($_POST['enumList']);
		}

		$this->messages->addMessage("Update Successful","");
	}
}
?>
