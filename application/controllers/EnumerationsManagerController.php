<?php
/*****************************************************************************
*       EnumerationsManagerController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * Enumerations Manager controller
 */
class EnumerationsManagerController extends WebVista_Controller_Action {

	protected function _saveEnumeration($data,$parentId=0) {
		$enumerationsClosure = new EnumerationsClosure();
		foreach ($data as $item) {
			$enumerationId = $enumerationsClosure->insertEnumeration($item,$parentId);
			if (isset($item['data'])) {
				$this->_saveEnumeration($item['data'],$enumerationId);
			}
		}
	}

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		$this->view->enumIterator = Enumeration::getIterByDistinctCategories();
		$this->render();
	}

	/**
	 * List distinct enumerations names given a enumerationId of category
	 */
	public function listAction() {
		$rows = array();
		$category = $this->_getParam("category");
		$enumerationsClosure = new EnumerationsClosure();
		$parents = $enumerationsClosure->getAllParentsByCategory($category);
		foreach ($parents as $child) {
			$tmp = array();
			$tmp['id'] = $child['enumerationId'];
			$tmp['data'][] = $child['name'];
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	/**
	 * Render view page
	 */
	public function viewAction() {
		$enumerationId = (int)$this->_getParam("enumerationId");
		$this->view->enumerationId = $enumerationId;
		$this->render();
	}

	/**
	 * Render edit page
	 */
	public function editAction() {
		$enumerationId = (int)$this->_getParam('enumerationId');
		$parentId = (int)$this->_getParam('parentId');
		$category = $this->_getParam('category',null);
		$this->view->ormClass = $this->_getParam('ormClass',null);
		$this->view->enumerationId = $enumerationId;
		$enum = new Enumeration();
		if ($enumerationId > 0) {
			$enum->enumerationId = $enumerationId;
			$enum->populate();
		}
		if ($category !== null) {
			$enum->category = $category;
		}
		if (!strlen($enum->ormEditMethod) > 0) {
			$enum->ormEditMethod = 'ormEditMethod';
		}
		if ($parentId > 0) {
			$parent = new Enumeration();
			$parent->enumerationId = (int)$parentId;
			$parent->populate();
			$this->view->parent = $parent;
			if ($parent->name == 'Facilities') {
				$this->view->ormClass = 'Practice'; // overrides ormClass
			}
		}
		$form = new WebVista_Form(array("name"=>"edit-enumeration"));
		$form->setAction(Zend_Registry::get('baseUrl') . "enumerations-manager.raw/process-edit");
		$form->loadORM($enum,"enumeration");
		$form->setWindow('windowEditORMObjectId');
		$this->view->form = $form;
		$this->view->categoryIterator = Enumeration::getIterByDistinctCategories();
		$this->view->grid = $this->_getParam('grid');
		$this->render('edit');
	}

	public function processEditAction() {
		$parentId = (int)$this->_getParam('parentId');
		$params = $this->_getParam('enumeration');
		$enumClosure = new EnumerationsClosure();

		$isSelf = true;
		$enumerationId = (int)$params['enumerationId'];
		if (($parentId == 0 && $enumerationId == 0) || ($parentId > 0 && !$enumerationId > 0)) {
			$enumerationId = $enumClosure->insertEnumeration($params,$parentId);
			$isSelf = false;
		}

		$enumeration = new Enumeration();
		$enumeration->enumerationId = $enumerationId;
		$enumeration->populate();
		if ($isSelf) {
			$enumeration->populateWithArray($params);
			$enumeration->persist();
		}

		$ormClass = 'Enumeration';
		$icon = "<a onclick=\"enumEditObject({$enumeration->enumerationId})\" title=\"Edit Object\"><img src=\"" . Zend_Registry::get('baseUrl') . "img/sm-editproblem.png\" alt=\"Edit Object\" /></a>";
		if (strlen($enumeration->ormClass) > 0 && class_exists($enumeration->ormClass)) {
			$ormClass = $enumeration->ormClass;
		}

		$data['parentId'] = $parentId;
		$data['id'] = $enumeration->enumerationId;
		$data['data'] = array();
		$data['data'][] = $enumeration->name;
		$data['data'][] = $enumeration->category;
		$data['data'][] = $enumeration->active;
		$data['data'][] = $icon;
		$data['userdata']['ormClass'] = $ormClass;

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$enumerationId = (int)$this->_getParam("enumerationId");
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationsClosure->deleteEnumeration($enumerationId);
		$data = array();
		$data['msg'] = __("Record deleted successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReorderItemsAction() {
		$idFrom = (int)$this->_getParam("idFrom");
		$idTo = (int)$this->_getParam("idTo");
		// utilizing the ORM in the meantime, but can be optimized
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationsClosure->reorder($idFrom,$idTo);
		$data = array();
		$data['msg'] = __('Updated successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditSingleItemAction() {
		$field = $this->_getParam("field");
		$enumerationId = (int)$this->_getParam("enumerationId");
		$value = preg_replace('/[^a-z_0-9- ]/i','',$this->_getParam("value",""));
		$enumeration = new Enumeration();
		$data = '';
		if ($enumerationId > 0 && in_array($field,$enumeration->ormFields())) {
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();
			$enumeration->$field = $value;
			$enumeration->persist();

			$enum = new Enumeration();
			$enum->enumerationId = $enumerationId;
			$enum->populate();
			$data = $enum->$field;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateEnumerationTree(SimpleXMLElement $xml,$enumerationId) {
		static $enumerationList = array();
		$enumerationsClosure = new EnumerationsClosure();
		$descendants = $enumerationsClosure->getEnumerationTreeById($enumerationId);
		$item = null;
		foreach ($descendants as $enum) {
			if (in_array($enum->enumerationId,$enumerationList)) {
				continue;
			}
			$ormClass = 'Enumeration';
			$icon = "<a onclick=\"enumEditObject({$enum->enumerationId})\" title=\"Edit Object\"><img src=\"" . Zend_Registry::get('baseUrl') . "img/sm-editproblem.png\" alt=\"Edit Object\" /></a>";
			if (strlen($enum->ormClass) > 0 && class_exists($enum->ormClass)) {
				$ormClass = $enum->ormClass;
			}
			$category = '';
			if ($item === null) {
				//$item = $xml->addChild("row");
				$item = $xml;
				$category = $enum->category;
			}
			$leaf = $item->addChild("row");
			$leaf->addAttribute('id',$enum->enumerationId);
			$leaf->addChild('cell',htmlspecialchars($enum->name));
			$leaf->addChild('cell',$category);
			$leaf->addChild('cell',$enum->active);
			$leaf->addChild('cell',$icon);
			$userdata = $leaf->addChild('userdata',$ormClass);
			$userdata->addAttribute('name','ormClass');
			$enumerationList[] = $enum->enumerationId;
			if ($enumerationId != $enum->enumerationId) { // prevents infinite loop
				$this->_generateEnumerationTree($leaf,$enum->enumerationId);
			}
		}
	}

	public function listItemsAction() {
		$enumerationId = (int)$this->_getParam("enumerationId");

		$baseStr = "<?xml version='1.0' standalone='yes'?><rows></rows>";
		$xml = new SimpleXMLElement($baseStr);

		$enumeration = new Enumeration();
		$enumeration->enumerationId = $enumerationId;
		$enumeration->populate();
		$ormClass = 'Enumeration';
		$icon = "<a onclick=\"enumEditObject({$enumeration->enumerationId})\" title=\"Edit Object\"><img src=\"" . Zend_Registry::get('baseUrl') . "img/sm-editproblem.png\" alt=\"Edit Object\" /></a>";
		if (strlen($enumeration->ormClass) > 0 && class_exists($enumeration->ormClass)) {
			$ormClass = $enumeration->ormClass;
		}
		$item = $xml->addChild("row");
		$item->addAttribute('id',$enumeration->enumerationId);
		$item->addChild('cell',$enumeration->name);
		$item->addChild('cell',$enumeration->category);
		$item->addChild('cell',$enumeration->active);
		$item->addChild('cell',$icon);
		$userdata = $item->addChild('userdata',$ormClass);
		$userdata->addAttribute('name','ormClass');
		$this->_generateEnumerationTree($item,$enumerationId);

                header('content-type: text/xml');
		$this->view->content = $xml->asXml();
                $this->render();
	}

	/**
	 * Toolbar xml structure
	 */
	public function toolbarAction() {
		header("Cache-Control: public");
		header("Pragma: public");

		$cache = Zend_Registry::get('cache');
		$cacheKey = "enum-toolbar-" . Menu::getCurrentlySelectedActivityGroup() . "-" . Menu::getCurrentUserRole();
		$cacheKey = str_replace('-', '_', $cacheKey);
		$cacheKey = str_replace('/', '_', $cacheKey);
		if ($cache->test($cacheKey."_hash")) {
			$hash = $cache->load($cacheKey."_hash");
			$lastModified = $cache->load($cacheKey."_lastModified");
			$headers = getallheaders();
			if (isset($headers['If-None-Match']) && preg_match('/'.$hash.'/', $headers['If-None-Match'])) {
				header("Last-Modified: " . $lastModified);
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}

		if ($cache->test($cacheKey)) {
			$items = $cache->load($cacheKey);
		}
		else {
			$items = $this->render('toolbar');
			$hash = md5($items);
			$lastModified = gmdate("D, d M Y H:i:s")." GMT";
			$objConfig = new ConfigItem();
			$objConfig->configId = 'enableCache';
			$objConfig->populate();
			if ($objConfig->value) {
				$cache->save($hash, $cacheKey."_hash", array('tagToolbar'));
				$cache->save($lastModified, $cacheKey."_lastModified", array('tagToolbar'));
				$cache->save($items, $cacheKey, array('tagToolbar'));
			}
			header("ETag: ". $hash);
			header("Last-Modified: ". $lastModified);
			header("Content-length: "  . mb_strlen($items));
		}
		header("Content-type: text/xml");
		return $items;
	}

	public function contextMenuXmlAction() {
                header('Content-Type: application/xml;');
                $this->view->xmlHeader = '<?xml version="1.0" ?>';
		$this->render();
	}

	public function autoCompleteEnumAction() {
		$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9 >]/','',$match);
		$strMatch = $match;
		$matches = array();
		if (strlen($match) < 3) {
			$this->_helper->autoCompleteDojo($matches);
		}
		$enumerations = EnumerationClosure::searchByLevels($match);
		foreach ($enumerations as $enumeration) {
			$matches[$enumeration->enumerationId] = $enumeration->name;
		}
		$this->_helper->autoCompleteDojo($matches);
	}

}

