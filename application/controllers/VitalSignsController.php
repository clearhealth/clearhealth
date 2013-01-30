<?php
/*****************************************************************************
*       VitalSignsController.php
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


class VitalSignsController extends WebVista_Controller_Action {

	protected $_form;
	protected $_filterDates = array('today'=>'TODAY','d-1'=>'T-1','d-2'=>'T-2','d-3'=>'T-3','d-4'=>'T-4','d-5'=>'T-5','d-6'=>'T-6','d-7'=>'T-7','d-15'=>'T-15','d-30'=>'T-30','m-6'=>'Six Months','y-1'=>'One Year','y-2'=>'Two Years','all'=>'All Results');
	protected $_filterGraphs = array('BMI'=>'BMI','bloodPressure'=>'B/P','bloodPressure-weight'=>'BP/Weight','cg'=>'C/G','cvp'=>'CVP','height'=>'Height','height-weight'=>'Height/Weight','pain'=>'Pain','pulse'=>'Pulse','pulseOxygenation'=>'Pulse Ox.','respiration'=>'Respiration','temperature'=>'Temperature','temperature-pulse-respiration'=>'TPR','weight'=>'Weight');

	public function indexAction() {
		$this->view->filterDates = $this->_filterDates;
		$this->view->filterGraphs = $this->_filterGraphs;
		$this->view->labelKeyValues = $this->getVitalSignsTemplateKeyValue();
		$this->render('index');
		return;
		exit;
		$pat = new Patient();
		$pat->personId = 1983;
		$pat->populate();
		echo $pat->bmi;
		//var_dump(VitalSignGroup::getBMIVitalsForPatientId(1983));
		exit;
		$vitals = new VitalSignGroup();
		$vitalsIter = $vitals->getIterator();
		foreach ($vitalsIter as $vitals) {
			print_r($vitals->toString());
		}
		$this->render();
	}

	public function listVitalSignsAction() {
		$personId = (int)$this->_getParam('personId');
		$vitalSignIter = new VitalSignGroupsIterator();
                $vitalSignIter->setFilter(array("personId" => $personId));
		//normally can just use ORMIterator->toJsonArray but vital sign groups are a special case with a nested array
		$vitalsJsonArray = array();
		foreach($vitalSignIter as $vitalSignGroup) {
			foreach($vitalSignGroup->getVitalSignValues() as $vitalValue) {
				$tmpArray = array();
				$tmpArray['id'] = $vitalValue->vitalSignValueId;
				$tmpArray['data'] = array();
				$tmpArray['data'][] = $vitalSignGroup->dateTime;
				$tmpArray['data'][] = $vitalValue->vital;
				$tmpArray['data'][] = $vitalValue->value;
				$tmpArray['data'][] = $vitalValue->units;
				$vitalsJsonArray[] = $tmpArray;
			}
		}
		//$vitals = $vitalSignIter->toJsonArray('vitalSignGroupId',array('dateTime','vital','value','units'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('rows' => $vitalsJsonArray));	

	}
	public function addAction() {
		$personId = (int)$this->_getParam('personId');
		$this->_form = new WebVista_Form(array('name' => 'vs-add-form'));
		$this->_form->setWindow('windowAddVitalSignsId');
		$this->_form->setWindowAction(WebVista_Form::WINDOW_CLOSE);
		$vitalSignTemplate = new VitalSignTemplate();
		$vitalSignTemplate->vitalSignTemplateId = 1;
		$vitalSignTemplate->populate();
		$template = simplexml_load_string($vitalSignTemplate->template);
                $this->_form->setAction(Zend_Registry::get('baseUrl') . "vital-signs.raw/process-add");
		$this->_buildForm($template);
		$element = $this->_form->createElement("hidden",'vitalSignTemplateId',array('value' => $vitalSignTemplate->vitalSignTemplateId));
                $element->setBelongsTo('vitalSignGroup');
                $this->_form->addElement($element);
		$element = $this->_form->createElement("hidden",'personId',array('value' => $personId));
                $element->setBelongsTo('vitalSignGroup');
                $this->_form->addElement($element);
		$this->view->form = $this->_form;
		$this->view->jsCallback = $this->_getParam('jsCallback','');
		$this->render();
	}
	
	function processAddAction() {
		$vitalSignGroup = new VitalSignGroup();
		$params = $this->_getParam('vitalSignGroup');
		trigger_error(print_r($params,true),E_USER_NOTICE);
		$vitalSignGroup->populateWithArray($params);
		$vitalSignGroup->dateTime = date('Y-m-d H:i:s');
		//$vitalSignGroup->personId = (int)$this->_getParam('personId');
		$vitalSignGroup->enteringUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$vitalSignGroup->persist();
	}

	function _buildForm($template) {
		foreach ($template as $vital) {
                        $elements = array();

                        $element = $this->_form->createElement("checkbox","unavailable");
			$element->setBelongsTo('vitalSignGroup[vitalSignValues]['. (string)$vital->attributes()->label.']');
                        $this->_form->addElement($element);
                        $elements[] = "unavailable";

                        $element = $this->_form->createElement("checkbox","refused");
			$element->setBelongsTo('vitalSignGroup[vitalSignValues]['. (string)$vital->attributes()->label.']');
                        $this->_form->addElement($element);
                        $elements[] = "refused";

                        $elementName = preg_replace('/\./','_',(string)$vital->attributes()->title);
                        $element = $this->_form->createElement('hidden','vital', array('value' => $elementName));
			$element->setBelongsTo('vitalSignGroup[vitalSignValues]['. (string)$vital->attributes()->label.']');
                        $this->_form->addElement($element);
                        $elements[] = 'vital';

                        $element = $this->_form->createElement((string)$vital->attributes()->type,'value', array('label' => (string)$vital->attributes()->label));
			$element->setBelongsTo('vitalSignGroup[vitalSignValues]['. (string)$vital->attributes()->label.']');
			$element->clearDecorators();
			$element->addDecorator('ViewHelper');
			$element->addDecorator('Label', array('tag' => 'dt'));
			if ((string)$vital->script) {
				$element->addDecorator('ScriptTag',array('placement' => 'APPEND','tag' => 'script','innerHTML' => (string)$vital->script,'noAttribs' => true));
			}
                        $this->_form->addElement($element);
                        $elements[] = 'value';

			if ((string)$vital->attributes()->units) {
                        	$element = $this->_form->createElement("select","units");
				$element->addMultiOptions(Enumeration::getEnumArray((string)$vital->attributes()->units,"key","name"));
				$element->setBelongsTo('vitalSignGroup[vitalSignValues]['. (string)$vital->attributes()->label.']');
                        	$this->_form->addElement($element);
                        	$elements[] = "units";
			}

                        $this->_form->addDisplayGroup($elements,(string)$vital->attributes()->label);
                } 
	}
	function listMostRecentAction() {
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $personId = (int)$this->_getParam('personId');
                if (!$personId > 0) $json->direct(array());

		//echo $vsSelect->__toString();exit;
                $vitalSigns = array();
		//var_dump($db->query($vsSelect)->fetchAll());exit;
                foreach(VitalSignGroup::getMostRecentVitalsForPatientId($personId) as $row) {
                        $vitalSigns[] = array("id" => $row['vitalSignValueId'],"data" => array($row['vital'], '', $row['value'], $row['dateTime']));
                }

                $json->direct(array("rows" => $vitalSigns));
        }

	public function listPatientVitalsAction() {
		$personId = (int)$this->_getParam('personId');
		$rows = array();
		if ($personId > 0) {
                	foreach(VitalSignGroup::getMostRecentVitalsForPatientId($personId) as $row) {
				$value = $row['value'];
				$ussValue = $value;
				$metricValue = '';
				$convertible = 0;
				if (strlen($row['units']) > 0) {
					if (strlen($ussValue) > 0) {
						$ussValue .= ' '.$row['units'];
					}
					$ret = VitalSignValue::convertValues($row['vital'],$value,$row['units']);
					if ($ret !== false) {
						$ussValue = $ret['uss'];
						$metricValue = $ret['metric'];
						$convertible = 1;
					}
				}
				$tmp = array();
				$tmp['id'] = $row['vitalSignValueId'];
				$tmp['data'][] = date('n/j/Y h:i:s A',strtotime($row['dateTime']));
				$tmp['data'][] = $row['vital'];
				$tmp['data'][] = $ussValue;
				$tmp['data'][] = $metricValue;
				$tmp['data'][] = ''; // to be implemented
				$tmp['data'][] = $row['last_name'].','.$row['first_name'].' '.$row['middle_name'];
				$tmp['data'][] = $convertible;
				$rows[] = $tmp;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function toolbarXmlAction() {
		header('Content-Type: text/xml');
		$this->render();
	}

	public function listXmlAction() {
		$personId = (int)$this->_getParam('personId');
		$filter = $this->_getParam('filter');

		$dateToday = date('Y-m-d');
		$today = strtotime($dateToday);
		$filterRange = array();
		$filterRange['begin'] = $dateToday;
		$filterRange['end'] = date('Y-m-d 23:59:59',$today);
		switch ($filter) {
			case 'd-1':
			case 'd-2':
			case 'd-3':
			case 'd-4':
			case 'd-5':
			case 'd-6':
			case 'd-7':
			case 'd-15':
			case 'd-30':
				$filterRange['begin'] = date('Y-m-d',strtotime(substr($filter,1).' days',$today));
				break;
			case 'm-6':
				$filterRange['begin'] = date('Y-m-d',strtotime(substr($filter,1).' months',$today));
				break;
			case 'y-1':
			case 'y-2':
				$filterRange['begin'] = date('Y-m-d',strtotime(substr($filter,1).' years',$today));
				break;
			case 'all':
				$filterRange['begin'] = date('Y-m-d',strtotime(''));
				break;
			default:
				$x = explode('|',$filter);
				if (isset($x[1])) {
					$filterRange['begin'] = date('Y-m-d',strtotime($x[0]));
					$filterRange['end'] = date('Y-m-d',strtotime($x[1]));
				}
				break;
		}
		//trigger_error(print_r($filterRange,true),E_USER_NOTICE);

		$filters = array();
		$filters['personId'] = $personId;
		$filters['dateBegin'] = $filterRange['begin'];
		$filters['dateEnd'] = $filterRange['end'];
		$filters['vitalSignTemplateId'] = 1;
		$results = VitalSignGroup::getVitalsByFilters($filters);
		$vitals = array();
		$dates = array();
		$data = array();
		foreach ($results as $result) {
			if (!isset($vitals[$result['vital']])) {
				$vitals[$result['vital']] = array();
			}
			if (!isset($dates[$result['vitalSignGroupId']])) {
				$dates[$result['vitalSignGroupId']] = date('m/d/Y h:i A',strtotime($result['dateTime']));
			}
			$convertedValues = VitalSignValue::convertValues($result['vital'],$result['value'],$result['units']);
			if ($convertedValues !== false) {
				trigger_error(print_r($convertedValues,true),E_USER_NOTICE);
				$x = explode(' ',$convertedValues['uss']);
				$unit = array_pop($x);
				$value = implode(' ',$x).' ('.$convertedValues['metric'].')';
				$vitals[$result['vital']][$result['vitalSignGroupId']] = $value;
			}
			else {
				$vitals[$result['vital']][$result['vitalSignGroupId']] = $result['value'];
			}

			if (!isset($data[$result['vitalSignGroupId']])) {
				$data[$result['vitalSignGroupId']] = array();
			}
			$data[$result['vitalSignGroupId']][$result['vital']] = $result;
		}

		$xml = new SimpleXMLElement('<rows />');
		$head = $xml->addChild('head');

		$column = $head->addChild('column','');
		$column->addAttribute('type','ro');
		$column->addAttribute('width','150');
		$column->addAttribute('color','#ddd');

		if (!empty($dates)) {
			$row = $xml->addChild('row');
			$row->addAttribute('id','dates');
			$row->addChild('cell','');
			$ctr = 1;
			foreach ($dates as $vitalSignGroupId=>$date) {
				$row->addChild('cell',date('m/d/Y h:iA',strtotime($date)));
				$column = $head->addChild('column','');
				$column->addAttribute('type','ro');
				$column->addAttribute('width','130');

				$userdata = $xml->addChild('userdata',$vitalSignGroupId);
				$userdata->addAttribute('name','groupId'.$ctr++);
			}
		}
		$labelKeyValues = $this->getVitalSignsTemplateKeyValue();
		foreach ($labelKeyValues as $key=>$value) {
			$row = $xml->addChild('row');
			$row->addAttribute('id',$key);
			$row->addChild('cell',$value);
			if (isset($vitals[$key])) {
				foreach ($vitals[$key] as $vital) {
					$row->addChild('cell',$vital);
				}
			}
		}
		header('Content-Type: text/xml');
		$this->view->xmlContents = $xml->asXML();
		trigger_error($this->view->xmlContents,E_USER_NOTICE);
		$this->render('list-xml');
	}

	protected function getVitalSignsTemplateKeyValue($vitalSignTemplateId = 1) {
		return VitalSignTemplate::generateVitalSignsTemplateKeyValue($vitalSignTemplateId);
	}

	public function processEditVitalSignValueFieldAction() {
		$vitalSignValueId = (int)$this->_getParam('id');
		$field = $this->_getParam('field');
		$value = $this->_getParam('value'); // expected format "[value] [unit]" e.g. "120 LB"
		$conversion = (int)$this->_getParam('conversion'); // flag to check if value needs conversion?
		$ret = false;
		$vitalSignValue = new VitalSignValue();
		$vitalSignValue->vitalSignValueId = $vitalSignValueId;
		$validFields = array('date'=>'date','value'=>'value');
		if (isset($validFields[$field]) && $vitalSignValue->populate()) {
			if ($field == 'date') {
				$vitalSignValue->vitalSignGroup->dateTime = date('Y-m-d H:i:s',strtotime($value));
				$vitalSignValue->vitalSignGroup->persist();
				$ret = true;
			}
			else if ($field == 'value') {
				// units is part of value and needs to be extracted, if no units is specified it will used the same unit in db
				$arrValue = explode(' ',$value);
				if (count($arrValue) > 1) {
					$units = array_pop($arrValue);
				}
				else {
					$units = $vitalSignValue->units;
				}
				$value = implode(' ',$arrValue); // is value contains spaces?
				// check if value needs conversion
				if ($conversion) { // needs conversion
					$convertedValues = VitalSignValue::convertValues($vitalSignValue->vital,$value,$units);
					if ($convertedValues !== false) {
						$type = VitalSignValue::unitType($vitalSignValue->vital,$vitalSignValue->units);
						$x = explode(' ',$convertedValues[$type]);
						array_pop($x);
						$value = implode(' ',$x);
					}
				}
				$vitalSignValue->$field  = $value;
				$vitalSignValue->persist();
				if ($vitalSignValue->vital == 'height' || $vitalSignValue->vital == 'weight') {
					VitalSignValue::recalculate($vitalSignValue->vitalSignGroupId);
				}
				$ret = true;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processEnteredInErrorAction() {
		$groupId = (int)$this->_getParam('groupId');
		$ret = false;
		$vitalSignGroup = new VitalSignGroup();
		$vitalSignGroup->vitalSignGroupId = $groupId;
		if ($vitalSignGroup->populate()) {
			$vitalSignGroup->enteredInError = 1;
			$vitalSignGroup->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function getMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-menu');
	}

}
