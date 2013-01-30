<?php
/*****************************************************************************
*       OrdersController.php
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
 * Orders controller
 */
class OrdersController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render('index');
	}

	public function listJsonAction() {
		$filter = $this->_getParam('filter');
		$personId = (int)$this->_getParam('personId');
		$rows = array();
		$filters = array();
		$filters['patientId'] = $personId;
		$filters[$filter] = $filter;
		$orderIterator = new OrderIterator(null,false);
		$orderIterator->setFilter($filters);
		foreach ($orderIterator as $order) {
			$tmp = array();
			$tmp['id'] = $order->orderId;
			$tmp['data'][] = $order->service;
			$tmp['data'][] = $order->displayStatus;
			$tmp['data'][] = $order->orderText;
			$start = __('(Not Specified)');
			if ($order instanceof OrderImaging) {
				$start = $order->dateRequested;
			}
			else if ($order instanceof OrderLabTest) {
				$start = $order->dateCollection;
			}
			else if ($order->dateStart != '0000-00-00 00:00:00') {
				//$start = date('m/d/Y',strtotime($order->dateStart));
				$start = $order->dateStart;
			}
			else {
				$start = $order->dateTime;
			}
			if ($start == '0000-00-00 00:00:00') {
				$start = $order->dateTime;
			}
			$stop = __('(Not Specified)');
			if ($order->dateStop != '0000-00-00 00:00:00') {
				//$stop = date('m/d/Y',strtotime($order->dateStop));
				$stop = $order->dateStop;
			}
			$discontinued = 0;
			$label = __('Ordered');
			if ($order->dateDiscontinued != '0000-00-00 00:00:00') {
				$discontinued = 1;
				$label = __('Completed');
				$start = $order->dateDiscontinued;
			}
			if ($order->release) {
				$label = __('Completed');
				$start = $order->dateStop;
			}
			$tmp['data'][] = $label . ': ' . $start;// . '<br />' . __('Stop') . ': ' . $stop;
			$tmp['data'][] = $order->provider->displayName;
			$tmp['data'][] = $order->type;
			$tmp['data'][] = $order->eSignatureId;
			$tmp['data'][] = (int)$order->release;
			$tmp['data'][] = $discontinued;
			$rows[] = $tmp;
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDiscontinueAction() {
		$orderId = (int)$this->_getParam('orderId');
		$data = false;
		if ($orderId > 0) {
			$order = new Order();
			$order->orderId = $orderId;
			$order->populate();
			$order->dateDiscontinued = date('Y-m-d H:i:s');
			$order->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function viewDetailsAction() {
		$orderId = (int)$this->_getParam('orderId');
		$order = new Order();
		$ormObj = $order;
		if ($orderId > 0) {
			$ormObj = Order::factory($orderId);
		}
		$this->view->order = $ormObj;
		$this->render();
	}

	public function contextMenuAction() {
		$this->view->type = $this->_getParam('type');
		header('Content-Type: text/xml;');
		$this->render();
	}

	public function toolbarXmlAction() {
		$id = $this->_getParam('id');
		if (strlen($id) <= 0) {
			throw new Exception('Id is empty');
		}
		$this->view->toolbars = $this->_getToolbars($id);
		header("Content-type: text/xml");
		$this->render();
	}

	public function textOnlyAction() {
		$personId = (int)$this->_getParam('personId');
		$orderId = (int)$this->_getParam('orderId');
		$copy = (int)$this->_getParam('copy');
		$form = new WebVista_Form(array('name'=>'textOnlyOrder'));
		$form->setAction(Zend_Registry::get('baseUrl').'orders.raw/process-text-order');
		$order = new Order();
		$order->orderId = $orderId;
		if (!$orderId > 0 || !$order->populate()) {
			$order->patientId = $personId;
		}
		if ($copy) $order->orderId = 0;
		$form->loadORM($order,'Order');
		$form->setWindow('windowTextOnlyId');
		$this->view->form = $form;
		$this->render();
	}

	public function processTextOrderAction() {
		$textOrders = $this->_getParam('order');
		$status = $this->_getParam('status');
		$order = new Order();
		$order->populateWithArray($textOrders);
		$order->service = 'Text Only';
		$order->status = 'Active';
		$order->dateTime = date('Y-m-d H:i:s');
		if (!$order->providerId > 0) $order->providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$order->persist();
		$msg = __('Record successfully saved');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('msg'=>$msg));
	}

	public function labTestAction() {
		$personId = (int)$this->_getParam('personId');
		$orderId = (int)$this->_getParam('orderId');
		$copy = (int)$this->_getParam('copy');
		$form = new WebVista_Form(array('name'=>'labTest'));
		$form->setAction(Zend_Registry::get('baseUrl').'orders.raw/process-lab-test');
		$orderLabTest = new OrderLabTest();
		$orderLabTest->orderId = $orderId;
		if (!$orderId > 0 || !$orderLabTest->populate()) {
			$orderLabTest->order->patientId = $personId;
		}
		if ($copy) $orderLabTest->orderId = 0;
		$form->loadORM($orderLabTest,'labTest');
		$form->setWindow('windowLabTestId');
		$this->view->form = $form;

		$labTestsList = array();
		$collectionSamples = array();
		$specimens = array();
		$urgencies = array();
		$collectionTypes = array();
		$schedules = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(OrderLabTest::LAB_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		$labEnums = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1,true);
		foreach ($labEnums as $labEnum) {
			$rowset = $enumerationClosure->getAllDescendants($labEnum->enumerationId,1,true);
			if ($labEnum->key == OrderLabTest::LAB_SPECIMENS_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$specimens[$enum->key] = $enum->name;
				}
			}
			else if ($labEnum->key == OrderLabTest::LAB_URGENCIES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$urgencies[$enum->key] = $enum->name;
				}
			}
			else if ($labEnum->key == OrderLabTest::LAB_COLLECTION_TYPES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$collectionTypes[$enum->key] = $enum->name;
				}
			}
			else if ($labEnum->key == OrderLabTest::LAB_SCHEDULES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$schedules[$enum->key] = $enum->name;
				}
			}
		}

		$this->view->labTestsList = $labTestsList;
		$this->view->collectionSamples = ProcedureCodesLOINC::sampleTypes();
		$this->view->specimens = $specimens;
		$this->view->urgencies = $urgencies;
		$this->view->collectionTypes = $collectionTypes;
		// Lab Collect date options are "Next scheduled lab collection" and "Future"
		$this->view->oftens = $schedules;
		$this->render('lab-test');
	}

	public function processLabTestAction() {
		$params = $this->_getParam('labTest');
		$orderLabTest = new OrderLabTest();
		if (isset($params['orderId'])) {
			$orderLabTest->orderId = (int)$params['orderId'];
			$orderLabTest->populate();
		}
		$orderLabTest->populateWithArray($params);
		if (isset($params['order'])) $orderLabTest->order->populateWithArray($params['order']);
		$orderLabTest->order->status = 'Active';
		$orderLabTest->order->dateTime = date('Y-m-d H:i:s');
		$providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		if (!$providerId > 0) {
			$providerId = (int)Zend_Auth::getInstance()->getIdentity()->userId;
		}
		$orderLabTest->order->providerId = $providerId;
		if (!$orderLabTest->order->providerId > 0) $orderLabTest->order->providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$orderLabTest->persist();
		$msg = __('Record saved');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('msg'=>$msg));
	}

	public function imagingAction() {
		$personId = (int)$this->_getParam('personId');
		$orderId = (int)$this->_getParam('orderId');
		$copy = (int)$this->_getParam('copy');
		$orderImaging = new OrderImaging();
		$orderImaging->orderId = $orderId;
		if (!$orderId > 0 || !$orderImaging->populate()) {
			$orderImaging->order->patientId = $personId;
		}
		if ($copy) $orderImaging->orderId = 0;
		$form = new WebVista_Form(array('name'=>'imagingId'));
		$form->setAction(Zend_Registry::get('baseUrl').'orders.raw/process-imaging');
		$form->loadORM($orderImaging,'imaging');
		$form->setWindow('windowImagingId');
		$this->view->form = $form;
		$namespace = $personId.'::com.clearhealth.person.examsOver7days';
		$this->view->examsOver7days = NSDR2::populate($namespace);

		$imagingList = array();
		$categories = array();
		$urgencies = array();
		$transports = array();
		$pregnants = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(OrderImaging::IMAGING_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		$imagingEnums = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1,true);
		foreach ($imagingEnums as $imagingEnum) {
			$rowset = $enumerationClosure->getAllDescendants($imagingEnum->enumerationId,1,true);
			if ($imagingEnum->key == OrderImaging::IMAGING_TYPES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$enumKey = $enum->enumerationId;
					$imagingList[$enumKey] = array();
					$imagingList[$enumKey]['name'] = $enum->name;
					$imagingList[$enumKey]['procedures'] = array();
					$imagingList[$enumKey]['modifiers'] = array();
					$rows = $enumerationClosure->getAllDescendants($enum->enumerationId,1,true);
					foreach ($rows as $row) {
						if ($row->name == 'Procedures') {
							$rowsProcedures = $enumerationClosure->getAllDescendants($row->enumerationId,1,true);
							foreach ($rowsProcedures as $rowProcedure) {
								$rowProcedureKey = $rowProcedure->enumerationId;
								$imagingList[$enumKey]['procedures'][$rowProcedureKey]['procedure'] = $rowProcedure->name;
								$rowsUnitComment = $enumerationClosure->getAllDescendants($rowProcedure->enumerationId,1,true);
								$comment = '';
								foreach ($rowsUnitComment as $unitComment) {
									$comment = $unitComment->name;
									break; // only one comment is expected
								}
								$imagingList[$enumKey]['procedures'][$rowProcedureKey]['comment'] = $comment;
							}
						}
						else if ($row->name == 'Modifiers') {
							$rowsModifiers = $enumerationClosure->getAllDescendants($row->enumerationId,1,true);
							foreach ($rowsModifiers as $rowModifier) {
								$rowModifierKey = $rowModifier->enumerationId;
								$imagingList[$enumKey]['modifiers'][$rowModifierKey] = $rowModifier->name;
							}
						}
					}
				}
			}
			else if ($imagingEnum->key == OrderImaging::IMAGING_CATEGORIES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$categories[$enum->key] = $enum->name;
				}
			}
			else if ($imagingEnum->key == OrderImaging::IMAGING_URGENCIES_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$urgencies[$enum->key] = $enum->name;
				}
			}
			else if ($imagingEnum->key == OrderImaging::IMAGING_TRANSPORTS_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$transports[$enum->key] = $enum->name;
				}
			}
			else if ($imagingEnum->key == OrderImaging::IMAGING_PREGNANTS_ENUM_KEY) {
				foreach ($rowset as $enum) {
					$pregnants[$enum->key] = $enum->name;
				}
			}
		}

		$this->view->imagingList = $imagingList;
		$this->view->categories = $categories;
		$this->view->urgencies = $urgencies;
		$this->view->transports = $transports;
		$this->view->pregnants = $pregnants;
		$this->view->imagingSubmitTo = array('CLEARHEALTH HOSPITAL'); // temporarily hard-coded
		$this->render();
	}

	public function processImagingAction() {
		$params = $this->_getParam('imaging');
		$orderImaging = new OrderImaging();
		if (isset($params['orderId'])) {
			$orderImaging->orderId = (int)$params['orderId'];
			$orderImaging->populate();
		}
		$orderImaging->populateWithArray($params);
		if (isset($params['order'])) $orderImaging->order->populateWithArray($params['order']);
		$orderImaging->order->status = 'Active';
		$orderImaging->order->dateTime = date('Y-m-d H:i:s');
		if (!$orderImaging->order->providerId > 0) $orderImaging->order->providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$orderImaging->persist();
		$msg = __('Record saved');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('msg'=>$msg));
	}

	public function labResultsAction() {
		$orderId = (int)$this->_getParam('orderId');
		$order = new OrderLabTest();
		$order->orderId = $orderId;
		if (!$order->populate()) {
			$error = 'Order Id '.$orderId.' not found';
			trigger_error($error);
			throw new Exception($error);
		}
		$loinc = new ProcedureCodesLOINC();
		$loinc->loincNum = $order->labTest;
		$loinc->populate();

		$shortname = $loinc->shortname;
		if (!strlen($shortname) > 0) {
			$shortname = $loinc->longCommonName;
		}

		$labTest = new LabTest();
		$labTest->labOrderId = $order->orderId;
		$labTest->populateByLabOrderId();

		$labOrder = new LabOrder();
		$labOrder->labOrderId = $order->orderId;
		if (!$labOrder->populate()) {
			$labOrder->labOrderId = $order->orderId;
			$labOrder->patientId = $order->order->patientId;
			$labOrder->personId = $order->order->patientId;
			$labOrder->orderingProvider = $order->order->provider->displayName;
			$labOrder->manualOrderDate = $order->order->dateTime;
			$labOrder->orderDescription = $shortname;
			$labOrder->persist();

			$labTest = new LabTest();
			$labTest->labOrderId = $labOrder->labOrderId;
			$labTest->componentCode = $loinc->class;
			$labTest->service = $shortname;
			$labTest->orderNum = $order->orderId;
			$labTest->persist();

			$orderObs = strtolower($loinc->orderObs);
			if ($orderObs == 'both') { // create default lab_results
				$labResult = new LabResult();
				$labResult->description = $order->displayLabTest;
				$labResult->labTestId = $labTest->labTestId;
				$labResult->observationTime = date('Y-m-d H:i');
				$labResult->units = $loinc->exampleUcumUnits;
				$labResult->identifier = $loinc->loincNum;
				$labResult->persist();
			}
			else if ($orderObs == 'order') { // temporarily do nothing
			}
		}
		$this->view->order = $order;

		$form = new WebVista_Form(array('name'=>'labTestId'));
		$form->setAction(Zend_Registry::get('baseUrl').'orders.raw/set-lab-test');
		$form->loadORM($labTest,'LabTest');
		$this->view->form = $form;

		$this->render();
	}

	public function setLabTestAction() {
		$params = $this->_getParam('labTest');
		$labTest = new LabTest();
		if (isset($params['labTestId'])) {
			$labTest->labTestId = (int)$params['labTestId'];
			$labTest->populate();
		}
		$labTest->populateWithArray($params);
		$labTest->persist();
		$data = __('Lab test successfully set.');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateLabResultRowData(LabResult $lab) {
		$row = array();
		$row['id'] = $lab->labResultId;
		$row['data'] = array();
		$row['data'][] = date('Y-m-d H:i',strtotime($lab->observationTime));
		$row['data'][] = $lab->description;
		$row['data'][] = $lab->value;
		$row['data'][] = $lab->units;
		$row['data'][] = $lab->referenceRange;
		$row['data'][] = $lab->abnormalFlag;
		$row['data'][] = $lab->resultStatus;
		$arr = array('','','','','','','');

		// newValue = arr[0]+"^"+arr[1]+"^^^^CLIA&2.16.840.1.113883.19.4.6&ISO^XX^^^1236|"+arr[2]+"^^"+arr[3]+"^"+arr[4]+"^"+arr[5]+"^^B";
		$performingOrg = explode('|',$lab->cliaPerformingOrg);
		if (count($performingOrg) >= 2) {
			$x = explode('^',$performingOrg[0]);
			$arr[0] = $x[0];
			$arr[1] = isset($x[1])?$x[1]:'';
			$y = explode('^',$performingOrg[1]);
			$arr[2] = $y[0];
			$arr[3] = isset($y[2])?$y[2]:'';
			$arr[4] = isset($y[3])?$y[3]:'';
			$arr[5] = isset($y[4])?$y[4]:'';
		}
		foreach ($arr as $val) $row['data'][] = $val;
		return $row;
	}

	public function listLabResultsJsonAction() {
		$orderId = (int)$this->_getParam('orderId');
		$rows = array();
		$labsIterator = new LabsIterator();
		$labsIterator->setFilters(array('orderId'=>$orderId));
		foreach ($labsIterator as $lab) {
			$rows[] = $this->_generateLabResultRowData($lab);
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddLabResultAction() {
		$this->_processEditLabResult();
	}

	public function processEditLabResultAction() {
		$this->_processEditLabResult();
	}

	protected function _processEditLabResult() {
		$orderId = (int)$this->_getParam('orderId');
		$params = $this->_getParam('result');
		$resultId = isset($params['labResultId'])?(int)$params['labResultId']:0;
		$labResult = new LabResult();
		$labResult->labResultId = $resultId;
		if (!$resultId > 0 || !$labResult->populate()) { // new
			$labResult->labResultId = 0;
		}
		$labResult->populateWithArray($params);
		$labResult->persist();
		$data = $this->_generateLabResultRowData($labResult);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteLabResultAction() {
		$resultId = (int)$this->_getParam('resultId');
		$labResult = new LabResult();
		$labResult->labResultId = $resultId;
		$data = false;
		if ($resultId > 0 && $labResult->populate()) {
			$labResult->setPersistMode(WebVista_Model_ORM::DELETE);
			$labResult->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processReleaseAction() {
		$this->_processRelease(1,date('Y-m-d H:i:s'));
	}

	public function processUnreleaseAction() {
		$this->_processRelease(0,'0000-00-00 00:00:00');
	}

	protected function _processRelease($release,$dateStop) {
		$orderId = (int)$this->_getParam('orderId');
		$data = false;
		$order = new Order();
		$order->orderId = $orderId;
		if ($orderId > 0 && $order->populate()) {
			$order->release = $release;
			$order->dateStop = $dateStop;
			$order->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function labTestAutoCompleteAction() {
		$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9\. ]/','',$match);
		$matches = array();
		if (strlen($match) < 3) {
			$this->_helper->autoCompleteDojo($matches);
			return;
		}
		$iterator = new ProcedureCodesLOINCIterator(null,false); // false = disable auto query all
		$iterator->setFilters(array('autocomplete'=>$match));
		foreach ($iterator as $loinc) {
			$matches[$loinc->loincNum] = ($loinc->shortname != '')?$loinc->shortname:$loinc->longCommonName;
		}
		$this->_helper->autoCompleteDojo($matches);
	}

	public function getLabTestInfoAction() {
		$labTestId = $this->_getParam('labTestId');
		$loinc = new ProcedureCodesLOINC();
		$loinc->loincNum = $labTestId;
		$loinc->populate();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($loinc->toArray());
	}

}
