<?php
/*****************************************************************************
*       ReportsManagerController.php
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


class ReportsManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function addBaseAction() {
		$this->_editBase();
	}

	public function editBaseAction() {
		$baseId = (int)$this->_getParam('baseId');
		$this->_editBase($baseId,'edit');
	}

	protected function _editBase($baseId=0,$mode='add') {
		$parentId = (int)$this->_getParam('parentId');
		$reportBaseClosure = new ReportBaseClosure();
		$reportBase = new ReportBase();
		if ($baseId > 0) {
			$reportBase->reportBaseId = $baseId;
			$reportBase->populate();
			$parentId = (int)$reportBaseClosure->getParentById($reportBase->reportBaseId);
		}
		$this->view->parentId = $parentId;

		$form = new WebVista_Form(array('name'=>$mode.'-base'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'reports-manager.raw/process-'.$mode.'-base');
		$form->loadORM($reportBase,'ReportBase');
		$form->setWindow('winReportManagerBaseId');
		$this->view->form = $form;

		$filters = array();
		if (strlen($reportBase->filters) > 0) {
			$filters = unserialize($reportBase->filters);
		}
		foreach ($filters as $key=>$value) {
			$filters[$key]->options = $this->_formatOptions($filters[$key]->options);
		}
		$this->view->filters = $filters;

		$parent = '';
		if ($parentId > 0) {
			$reportBase = new ReportBase();
			$reportBase->reportBaseId = $parentId;
			$reportBase->populate();
			$parent = $reportBase->displayName;
		}

		$this->view->parent = $parent;

		$this->render('edit-base');
	}

	public function processAddBaseAction() {
		$params = $this->_getParam('reportBase');
		$params['reportBaseId'] = 0;
		$this->_processEditBase($params);
	}

	public function processEditBaseAction() {
		$params = $this->_getParam('reportBase');
		$this->_processEditBase($params);
	}

	protected function _processEditBase($params) {
		$isAdd = ($params['reportBaseId'] > 0)?false:true;
		$reportBase = new ReportBase();
		if (!$isAdd) {
			$reportBase->reportBaseId = $params['reportBaseId'];
			$reportBase->populate();
		}
		$reportBase->populateWithArray($params);
		$reportBase->persist();

		if ($params['reportBaseId'] <= 0) { // add default view
			$reportView = new ReportView();
			$reportView->reportBaseId = $reportBase->reportBaseId;
			$reportView->displayName = 'Default';
			$reportView->systemName = 'default';
			$reportView->customizeColumnNames = 0;
			$reportView->runQueriesImmediately = 0;
			$reportView->active = 1;
			$reportView->persist();
		}

		$parentId = 0;
		$useParentId = $this->_getParam('useParentId',null);
		if ($useParentId !== null && $useParentId == 'on') {
			$parentId = (int)$this->_getParam('parentId');
		}

		if ($isAdd) {
			$ancestor = $parentId;
			$descendant = $reportBase->reportBaseId;
			$closure = new ReportBaseClosure();
			$closure->insertClosures($ancestor,$descendant);
		}

		if (!$parentId > 0) {
			$parentId = 'rootId';
		}
		$data = array();
		$data['isAdd'] = $isAdd;
		$data['parentId'] = $parentId;
		$data['id'] = $reportBase->reportBaseId;
		$data['data'] = array();
		$data['data'][] = $reportBase->displayName;
		$data['data'][] = $reportBase->systemName;

		//trigger_error(print_r($params,true),E_USER_NOTICE);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteBaseAction() {
		$params = $this->_getParam('baseId');
		$baseIds = explode(',',$params);
		$reportBase = new ReportBase();
		foreach ($baseIds as $baseId) {
			$reportBase->reportBaseId = $baseId;
			$reportBase->setPersistMode(WebVista_Model_ORM::DELETE);
			$reportBase->persist();
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function addFilterAction() {
		$this->_editFilter();
	}

	public function editFilterAction() {
		$filterId = $this->_getParam('filterId');
		$this->_editFilter($filterId,'edit');
	}

	protected function _editFilter($filterId=0,$mode='add') {
		$this->view->filterId = $filterId;
		$baseId = (int)$this->_getParam('baseId');
		$this->view->baseId = $baseId;
		$reportBase = new ReportBase();
		if ($baseId > 0) {
			$reportBase->reportBaseId = $baseId;
			$reportBase->populate();
		}
		else {
			$this->view->error = __('Report does not exists.');
		}
		$filterTypes = ReportBase::getFilterTypes();
		$types = array();
		foreach ($filterTypes as $key=>$value) {
			$types[$key] = $key;
		}
		$this->view->types = $types;
		$this->view->options = ReportBase::$_filterOptions;

		$filters = array();
		if (strlen($reportBase->filters) > 0) {
			$filters = unserialize($reportBase->filters);
		}
		$filterData = array(
			'id'=>'',
			'name'=>'',
			'defaultValue'=>'',
			'type'=>'',
			'options'=>array(),
			'query'=>'',
			'includeBlank'=>'',
			'enumName'=>array('id'=>'','value'=>''),
			'special'=>'',
		);
		if (isset($filters[$filterId])) {
			$filter = $filters[$filterId];
			$filterData = array(
				'id'=>$filter->id,
				'name'=>$filter->name,
				'defaultValue'=>$filter->defaultValue,
				'type'=>$filter->type,
				'options'=>$filter->options,
				'query'=>$filter->query,
				'includeBlank'=>$filter->includeBlank,
				'enumName'=>$filter->enumName,
				'special'=>$filter->special,
			);
		}
		$this->view->filterData = $filterData;

		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = $baseId;
		$this->view->reportQueries = $reportQuery->getIteratorByBaseId()->toArray('reportQueryId','displayName');
		$this->view->reportSpecial = ReportBase::$_specialOptions;

		$this->render('edit-filter');
	}

	protected function _formatOptions($options,$sep='<br />') {
		$ret = array();
		$filterOptions = ReportBase::$_filterOptions;
		foreach ($options as $key=>$value) {
			if (!isset($filterOptions[$key])) continue;
			$ret[] = $filterOptions[$key];
		}
		return implode($sep,$ret);
	}

	public function processAddFilterAction() {
		$params = $this->_getParam('filter');
		//trigger_error(print_r($params,true),E_USER_NOTICE);
		$this->_processEditFilter($params);
	}

	public function processEditFilterAction() {
		$params = $this->_getParam('filter');
		$this->_processEditFilter($params);
	}

	protected function _processEditFilter($params) {
		$baseId = (int)$this->_getParam('baseId');
		$reportBase = new ReportBase();
		if ($baseId > 0) {
			$reportBase->reportBaseId = $baseId;
			$reportBase->populate();
		}
		else {
			$this->view->error = __('Report does not exists.');
		}

		$filters = array();
		if (strlen($reportBase->filters) > 0) {
			$filters = unserialize($reportBase->filters);
		}

		if (!strlen($params['id']) > 0) {
			$params['id'] = uniqid('',true);
		}
		if (!isset($params['options']) || !is_array($params['options'])) {
			$params['options'] = array();
		}
		$oFilter = new StdClass();
		$oFilter->id = $params['id'];
		$oFilter->name = $params['name'];
		$oFilter->defaultValue = $params['defaultValue'];
		$oFilter->type = $params['type'];
		$oFilter->options = $params['options'];
		if ($oFilter->type == 'QUERY') $oFilter->query = $params['query'];
		$oFilter->includeBlank = isset($params['includeBlank'])?1:0;
		$oFilter->special = isset($params['special'])?$params['special']:'';
		$oFilter->enumName = $params['enumName'];
		$filters[$params['id']] = $oFilter;

		$reportBase->filters = serialize($filters);
		$reportBase->persist();

		$data = array();
		$data['id'] = $oFilter->id;
		$data['data'] = array();
		$data['data'][] = $oFilter->name;
		$data['data'][] = $oFilter->defaultValue;
		$data['data'][] = $oFilter->type;
		$data['data'][] = $this->_formatOptions($oFilter->options);

		//trigger_error(print_r($params,true),E_USER_NOTICE);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteFilterAction() {
		$baseId = (int)$this->_getParam('baseId');
		$filterId = $this->_getParam('filterId');
		$reportBase = new ReportBase();
		$reportBase->reportBaseId = $baseId;
		$reportBase->populate();

		$filters = array();
		if (strlen($reportBase->filters) > 0) {
			$filters = unserialize($reportBase->filters);
		}
		$data = false;
		if (isset($filters[$filterId])) {
			unset($filters[$filterId]);
			$reportBase->filters = serialize($filters);
			$reportBase->persist();
			$data = true;
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listAction() {
		$xml = new SimpleXMLElement('<rows/>');

		// Root handler
		$root = $xml->addChild('row');
		$root->addAttribute('id','rootId');
		$root->addChild('cell',__('Report List'));
		$root->addChild('cell','');
		ReportBaseClosure::generateXMLTree($root);
                header('content-type: text/xml');
		$this->view->content = $xml->asXml();
		$this->render('list');
	}

	public function addQueryAction() {
		$this->_editQuery();
	}

	public function editQueryAction() {
		$queryId = (int)$this->_getParam('queryId');
		$this->_editQuery($queryId,'edit');
	}

	protected function _editQuery($queryId=0,$mode='add') {
		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = (int)$this->_getParam('baseId');
		if ($queryId > 0) {
			$reportQuery->reportQueryId = $queryId;
			$reportQuery->populate();
		}
		$form = new WebVista_Form(array('name'=>$mode.'-query'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'reports-manager.raw/process-'.$mode.'-query');
		$form->loadORM($reportQuery,'ReportQuery');
		$form->setWindow('winReportBaseQueryId');
		$this->view->form = $form;
		$this->view->types = array('SQL'=>'SQL','NSDR'=>'NSDR');
		$this->render('edit-query');
	}

	public function processAddQueryAction() {
		$params = $this->_getParam('reportQuery');
		$params['reportQueryId'] = 0;
		$this->_processEditQuery($params);
	}

	public function processEditQueryAction() {
		$params = $this->_getParam('reportQuery');
		$this->_processEditQuery($params,'edit');
	}

	public function processDeleteQueryAction() {
		$queryId = (int)$this->_getParam('queryId');
		$reportQuery = new ReportQuery();
		$reportQuery->reportQueryId = $queryId;
		$reportQuery->setPersistMode(WebVista_Model_ORM::DELETE);
		$reportQuery->persist();
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _processEditQuery($params) {
		$reportQuery = new ReportQuery();
		$reportQuery->populateWithArray($params);
		$reportQuery->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($this->_generateQueryGridRowData($reportQuery));
	}

	protected function _generateQueryGridRowData(ReportQuery $query) {
		$row = array();
		$row['id'] = $query->reportQueryId;
		$row['data'] = array();
		$row['data'][] = $query->displayName;
		return $row;
	}

	public function listQueriesAction() {
		$baseId = (int)$this->_getParam('baseId');
		$rows = array();
		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = $baseId;
		$reportQueryIterator = $reportQuery->getIteratorByBaseId();
		foreach ($reportQueryIterator as $report) {
			$rows[] = $this->_generateQueryGridRowData($report);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function addViewAction() {
		$this->_editView();
	}

	public function editViewAction() {
		$viewId = (int)$this->_getParam('viewId');
		$this->_editView($viewId,'edit');
	}

	protected function _editView($viewId=0,$mode='add') {
		$reportView = new ReportView();
		$reportView->reportBaseId = (int)$this->_getParam('baseId');
		if ($viewId > 0) {
			$reportView->reportViewId = $viewId;
			$reportView->populate();
		}
		$form = new WebVista_Form(array('name'=>$mode.'-view'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'reports-manager.raw/process-'.$mode.'-view');
		$form->loadORM($reportView,'ReportView');
		$form->setWindow('winReportBaseViewId');
		$this->view->form = $form;

		$queries = array();
		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = $reportView->reportBaseId;
		$reportQueryIterator = $reportQuery->getIteratorByBaseId();
		$this->view->queries = $reportQueryIterator->toArray('reportQueryId','displayName');
		$mappings = array();
		if ($reportView->unserializedColumnDefinitions === null) {
			$reportView->unserializedColumnDefinitions = array();
		}
		foreach ($reportView->unserializedColumnDefinitions as $col) {
			$mappings[] = $this->_generateMappingGridRowData($col);
		}
		$this->view->mappings = $mappings;
		$this->view->transformTypes = ReportView::getTransformTypes();
		$this->view->showResults = ReportView::getShowResultOptions();

		$showResultsOptions = $reportView->unserializedShowResultsOptions;
		if (isset($showResultsOptions['pdfTemplateFile'])) {
			$attachment = new Attachment();
			$attachment->attachmentId = (int)$showResultsOptions['pdfTemplateFile'];
			$attachment->populate();
                        $showResultsOptions['pdfTemplateFileContent'] = '<a href="'.$this->view->baseUrl.'/attachments.raw/view-attachment?attachmentId=' . $attachment->attachmentId . '">' . $attachment->name . '</a>';
		}
		$this->view->showResultsOptions = $showResultsOptions;

		$this->view->lineEndings = ReportView::getLineEndingOptions();

		$this->render('edit-view');
	}

	public function processAddViewAction() {
		$params = $this->_getParam('reportView');
		$params['reportViewId'] = 0;
		$params['customizeColumnNames'] = 0;
		$params['runQueriesImmediately'] = 0;
		$params['active'] = 1;
		$params['columnDefinitions'] = '';
		$params['viewOrder'] = 0;
		$this->_processEditView($params);
	}

	public function processEditViewAction() {
		$params = $this->_getParam('reportView');
		$this->_processEditView($params,'edit');
	}

	protected function _processEditView($params) {
		$reportView = new ReportView();
		$reportView->reportViewId = $params['reportViewId'];
		$reportView->populate();
		$reportView->populateWithArray($params);
		$params['showResultsOptions'] = $this->_getParam('showResultsOptions');
		$showResultsOptions = array();
		if (isset($params['showResultsOptions'])) {
			$showResultsOptions = $params['showResultsOptions'];
		}
		$reportView->serializedShowResultsOptions = $showResultsOptions;
		$reportView->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($this->_generateViewGridRowData($reportView));
	}

	public function processDeleteViewAction() {
		$viewId = (int)$this->_getParam('viewId');
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->setPersistMode(WebVista_Model_ORM::DELETE);
		$reportView->persist();
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReorderViewAction() {
		$baseId = (int)$this->_getParam('baseId');
		$from = $this->_getParam('from');
		$to = $this->_getParam('to');
		ReportView::reorderViews($baseId,$from,$to);
		$this->listViewsAction();
	}

	protected function _generateViewGridRowData(ReportView $view,$default=false) {
		$row = array();
		$row['id'] = $view->reportViewId;
		$row['data'] = array();
		$displayName = $view->displayName;
		if ($default) {
			$displayName .= ' (default)';
		}
		$row['data'][] = $displayName;
		return $row;
	}

	public function listViewsAction() {
		$baseId = (int)$this->_getParam('baseId');
		$rows = array();
		$reportView = new ReportView();
		$reportView->reportBaseId = $baseId;
		$reportViewIterator = $reportView->getIteratorByBaseId();
		$default = false;
		$hasDefault = false;
		foreach ($reportViewIterator as $report) {
			if ($report->active && !$hasDefault) {
				$default = true;
				$hasDefault = true;
			}
			$rows[] = $this->_generateViewGridRowData($report,$default);
			$default = false;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function getContextMenuAction() {
		$this->view->type = $this->_getParam('type');
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

	public function processReorderBaseAction() {
		$from = (int)$this->_getParam('from');
		$to = (int)$this->_getParam('to');
		// utilizing the ORM in the meantime, but can be optimized
		$reportBaseClosure = new ReportBaseClosure();
		$reportBaseClosure->reorder($from,$to);
		$data = array();
		$data['msg'] = __('Updated successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReorderFilterAction() {
		$baseId = (int)$this->_getParam('baseId');
		$from = $this->_getParam('from');
		$to = $this->_getParam('to');
		// utilizing the ORM in the meantime, but can be optimized
		$reportBase = new ReportBase();
		$reportBase->reportBaseId = $baseId;
		$reportBase->populate();
		$rows = array();
		if (strlen($reportBase->filters) > 0) {
			$filters = unserialize($reportBase->filters);
			foreach ($filters as $id=>$filter) {
				if ($from == $id) {
					$fromFilter = $filters[$id];
					unset($filters[$id]);
					break;
				}
			}
			if (isset($fromFilter)) {
				$tmpFilters = array();
				foreach ($filters as $id=>$filter) {
					$tmpFilters[$id] = $filter;
					if ($to == $id) {
						$tmpFilters[$from] = $fromFilter;
					}
				}
				$filters = $tmpFilters;
			}
			foreach ($filters as $id=>$filter) {
				$row = array();
				$row['id'] = $filter->id;
				$row['data'] = array();
				$row['data'][] = $filter->name;
				$row['data'][] = $filter->defaultValue;
				$row['data'][] = $filter->type;
				$row['data'][] = $this->_formatOptions($filter->options);
				$rows[] = $row;
			}
			$reportBase->filters = serialize($filters);
			$reportBase->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function autoCompleteEnumFilterAction() {
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

	public function loadColumnMetaDataFromQueriesAction() {
		$viewId = (int)$this->_getParam('viewId');
		$this->view->viewId = $viewId;
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = $reportView->reportBaseId;
		$reportQueryIterator = $reportQuery->getIteratorByBaseId();
		$sql = array();
		foreach ($reportQueryIterator as $query) {
			if ($query->type != ReportQuery::TYPE_SQL) continue;
			$sql[$query->reportQueryId] = preg_replace('/WHERE (.*)/i','WHERE 1',$query->query).' LIMIT 1';
		}
		$this->view->queries = $sql;
		$this->render('load-column-meta-data-from-queries');
	}

	protected function _generateMappingGridRowData(StdClass $mapping) {
		$baseUrl = Zend_Registry::get('baseUrl');
		$row = array();
		$row['id'] = $mapping->id;
		$row['data'] = array();
		$row['data'][] = $mapping->queryName;
		$row['data'][] = $mapping->resultSetName;
		$row['data'][] = $mapping->displayName;
		//$row['data'][] = $mapping->transform;
		$row['data'][] = (count($mapping->transforms) > 0)?$baseUrl.'img/transform.png^Transform Set':$baseUrl.'img/stop.png^No Transform';
		return $row;
	}

	public function processLoadColumnMetaDataFromQueriesAction() {
		$viewId = (int)$this->_getParam('viewId');
		$queries = $this->_getParam('queries');

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$data = array();
		try {
			$mappings = $reportView->generateMetaDataFromQueries($queries);
			$rows = array();
			foreach ($mappings as $mapping) {
				$rows[] = $this->_generateMappingGridRowData($mapping);
			}
			$reportView->serializedColumnDefinitions = $mappings;
			$reportView->persist();
			$data['rows'] = $rows;
		}
		catch (Exception $e) {
			$data['error'] = $e->getMessage();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteMappingAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingIds = $this->_getParam('mappingIds');
		if (!is_array($mappingIds)) {
			$mappingIds = explode(',',$mappingIds);
		}
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$data = array();
		if ($columnDefinitions !== null) {
			foreach ($mappingIds as $id) {
				unset($columnDefinitions[$id]);
			}
			$reportView->serializedColumnDefinitions = $columnDefinitions;
			$reportView->persist();
			$rows = array();
			foreach ($columnDefinitions as $mapping) {
				$rows[] = $this->_generateMappingGridRowData($mapping);
			}
			$data['rows'] = $rows;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddMappingAction() {
		$config = Zend_Registry::get('config');
		$dbName = $config->database->params->dbname;

		$viewId = (int)$this->_getParam('viewId');
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$queryName = '';
		$row = ReportView::generateMappingObject($queryName);
		$row->resultSetName = $dbName;
		$row->displayName = ReportView::metaDataPrettyName($row->resultSetName);
		$columnDefinitions[$row->id] = $row;
		$reportView->serializedColumnDefinitions = $columnDefinitions;
		$reportView->persist();
		$data = $this->_generateMappingGridRowData($row);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditMappingAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$key = $this->_getParam('key');
		$value = $this->_getParam('value');
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		/*if (!isset($columnDefinitions[$mappingId])) {
			$queryName = '';
			$row = ReportView::generateMappingObject($queryName);
			$row->resultSetName = $dbName;
			$columnDefinitions[$row->id] = $row;
		}*/
		$row = $columnDefinitions[$mappingId];
		if (isset($row->$key)) {
			$row->$key = $value;
		}
		$reportView->serializedColumnDefinitions = $columnDefinitions;
		$reportView->persist();
		$data = $this->_generateMappingGridRowData($row);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReorderMappingAction() {
		$viewId = (int)$this->_getParam('viewId');
		$from = $this->_getParam('from');
		$to = $this->_getParam('to');

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$rows = array();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		foreach ($columnDefinitions as $id=>$mapping) {
			if ($from == $id) {
				$fromMapping = $columnDefinitions[$id];
				unset($columnDefinitions[$id]);
				break;
			}
		}
		if (isset($fromMapping)) {
			$tmpMappings = array();
			foreach ($columnDefinitions as $id=>$mapping) {
				$tmpMappings[$id] = $mapping;
				if ($to == $id) {
					$tmpMappings[$from] = $fromMapping;
				}
			}
			$columnDefinitions = $tmpMappings;
		}
		foreach ($columnDefinitions as $id=>$mapping) {
			$rows[] = $this->_generateMappingGridRowData($mapping);
		}
		$reportView->serializedColumnDefinitions = $columnDefinitions;
		$reportView->persist();

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listMappingTransformsAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$rows = array();
		if (isset($columnDefinitions[$mappingId])) {
			$row = $columnDefinitions[$mappingId];
			if (isset($row->transforms)) {
				foreach ($row->transforms as $id=>$transform) {
					$rows[] = $this->_generateTransformGridRowData($transform);
				}
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	protected function _generateTransformGridRowData(StdClass $transform) {
		$row = array();
		$row['id'] = $transform->id;
		$row['data'] = array();
		$row['data'][] = $transform->displayName;
		$row['data'][] = $transform->systemName;
		return $row;
	}

	public function processDeleteMappingTransformAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$transformIds = $this->_getParam('transformIds');
		if (!is_array($transformIds)) {
			$transformIds = explode(',',$transformIds);
		}

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$data = array();
		if ($columnDefinitions !== null && isset($columnDefinitions[$mappingId])) {
			$row = $columnDefinitions[$mappingId];
			foreach ($transformIds as $transformId) {
				if (isset($row->transforms[$transformId])) {
					unset($columnDefinitions[$mappingId]->transforms[$transformId]);
				}
			}
			$reportView->serializedColumnDefinitions = $columnDefinitions;
			$reportView->persist();
			$rows = array();
			foreach ($row->transforms as $transform) {
				$rows[] = $this->_generateTransformGridRowData($transform);
			}
			$data['rows'] = $rows;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddMappingTransformAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$value = $this->_getParam('value');

		$tranformTypes = ReportView::getTransformTypes();
		$displayName = '';
		$systemName = '';
		if (isset($tranformTypes[$value])) {
			$systemName = $value;
			$displayName = $tranformTypes[$value];
		}
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$data = array();
		if ($columnDefinitions !== null && isset($columnDefinitions[$mappingId])) {
			$transform = new StdClass();
			$transform->id = uniqid('',true);
			$transform->displayName = $displayName;
			$transform->systemName = $systemName;
			$transform->options = array();
			$columnDefinitions[$mappingId]->transforms[$transform->id] = $transform;
			$reportView->serializedColumnDefinitions = $columnDefinitions;
			$reportView->persist();
			$data = $this->_generateTransformGridRowData($transform);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditMappingTransformAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$transformId = $this->_getParam('transformId');
		$options = $this->_getParam('options');

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$data = array();
		if ($columnDefinitions !== null && isset($columnDefinitions[$mappingId])) {
			$row = $columnDefinitions[$mappingId];
			if (isset($row->transforms[$transformId])) {
				$transform = $row->transforms[$transformId];
				$transform->options = $options;
				$columnDefinitions[$mappingId]->transforms[$transformId] = $transform;
				$reportView->serializedColumnDefinitions = $columnDefinitions;
				$reportView->persist();
				$data = $this->_generateTransformGridRowData($transform);
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getMappingTransformOptionsAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$transformId = $this->_getParam('transformId');

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		$data = array();
		if ($columnDefinitions !== null && isset($columnDefinitions[$mappingId])) {
			$row = $columnDefinitions[$mappingId];
			if (isset($row->transforms[$transformId])) {
				$data = $row->transforms[$transformId]->options;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReorderMappingTransformAction() {
		$viewId = (int)$this->_getParam('viewId');
		$mappingId = $this->_getParam('mappingId');
		$from = $this->_getParam('from');
		$to = $this->_getParam('to');

		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$columnDefinitions = $reportView->unserializedColumnDefinitions;
		if ($columnDefinitions !== null && isset($columnDefinitions[$mappingId])) {
			$row = $columnDefinitions[$mappingId];
			if (isset($row->transforms[$from])) {
				$fromTransform = $row->transforms[$from];
				unset($columnDefinitions[$mappingId]->transforms[$from]);
				$tmpTransforms = array();
				foreach ($row->transforms as $id=>$transform) {
					$tmpTransforms[$id] = $transform;
					if ($to == $id) {
						$tmpTransforms[$from] = $fromTransform;
					}
				}
				$columnDefinitions[$mappingId]->transforms = $tmpTransforms;
				$reportView->serializedColumnDefinitions = $columnDefinitions;
				$reportView->persist();
			}
		}
		$rows = array();
		foreach ($columnDefinitions[$mappingId]->transforms as $id=>$transform) {
			$rows[] = $this->_generateTransformGridRowData($transform);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

}

