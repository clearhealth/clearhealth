<?php
/*****************************************************************************
*       FormularyManagerController.php
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
 * Formulary Manager controller
 */
class FormularyManagerController extends WebVista_Controller_Action {

	protected $_form = null;
	protected $_formulary = null;

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		// uncomment next to this line to generate/create default formulary entry to config table
		// FormularyItem::createDefaultConfigIfNotExists();
		$this->render();
	}

	public function listAction() {
		$formularyItem = new FormularyItem();
		$formularyIterator = $formularyItem->populateLike('formulary',1);
		$rows = array();
		foreach ($formularyIterator as $val) {
			$formulary = unserialize($val->value);
			if ($formulary === false || !$formulary instanceof FormularyItem) {
				continue;
			}
			$tmp = array();
			$tmp['id'] = $formulary->getName();
			$tmp['data'][] = $formulary->getPrettyName();
			$tmp['data'][] = (int)$formulary->isActive() . ':' . (int)$formulary->isDefault();
			$rows[] = $tmp;
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("id",""));
		$prettyName = preg_replace('/([A-Z]{1})/',' \1',substr($tableName,9));
		$this->view->tableName = $tableName;
		$this->view->prettyName = $prettyName;
		$this->view->chBaseMed24Url = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24Url;
		$this->view->chBaseMed24DetailUrl = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24DetailUrl;

		$name = Medication::ENUM_ADMIN_SCHED;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$rowset = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$scheduleOptions = array();
		$adminSchedules = array();
		foreach ($rowset as $row) {
			$scheduleOptions[] = $row->key;
			$adminSchedules[$row->key] = $row->name;
		}
		$this->view->scheduleOptions = $scheduleOptions;
		$this->view->quantityQualifiers = Medication::listQuantityQualifiersMapping();
		$this->render();
	}

	public function processAddAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("name",""));
		if (strlen($tableName) == 0) {
			$msg = __("Invalid formulary name");
			$code = 400; // invalid entry
		}
		else {
			$tableName[0] = strtoupper($tableName[0]);
			// prepend formulary to tableName
			$tableName = "formulary{$tableName}";
			$formulary = new FormularyItem($tableName);

			$db = Zend_Registry::get('dbAdapter');
			if ($formulary->populate(false)) {
				$msg = __("Name already exists");
				$code = 401; // name already exists
			}
			else {
				$defaultTableName = FormularyItem::getDefaultFormularyTable();
				if ($defaultTableName === false) {
					$msg = __("Default formulary does not set");
					$code = 403;
				}
				else if (FormularyItem::isTableExists($defaultTableName)) {
					$formulary = new FormularyItem($tableName);
					$formulary->activate(); // set default to active
					$formulary->persist(false);
					$msg = __("Formulary saved successfully");
					$code = 200;
				}
				else {
					$msg = __("Default name does not exists");
					$code = 402; // default name does not exists
				}
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processActiveAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("id",""));
		if (strlen($tableName) == 0) {
			$msg = __("Invalid formulary name");
			$code = 400; // invalid entry
		}
		else {
			$formulary = new FormularyItem($tableName);
			if ($formulary->populate(false)) {
				$formulary->activate();
				$formulary->persist(false);
				$msg = __("Formulary ".$formulary->getPrettyName()." activated successfully");
				$code = 200;
			}
			else {
				$msg = __("Formulary ".$formulary->getPrettyName()." does not exists");
				$code = 401;
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processInactiveAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("id",""));
		if (strlen($tableName) == 0) {
			$msg = __("Invalid formulary name");
			$code = 400; // invalid entry
		}
		else {
			$formulary = new FormularyItem($tableName);
			if ($formulary->populate(false)) {
				if ($formulary->isDefault()) {
					$msg = __("Formulary ".$formulary->getPrettyName()." is default and cannot be deactivated");
					$code = 402;
				} else {
					$formulary->deactivate();
					$formulary->persist(false);
					$msg = __("Formulary ".$formulary->getPrettyName()." deactivated successfully");
					$code = 200;
				}
			}
			else {
				$msg = __("Formulary ".$formulary->getPrettyName()." does not exists");
				$code = 401;
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("id",""));
		if (strlen($tableName) == 0) {
			$msg = __("Invalid formulary name");
			$code = 400; // invalid entry
		}
		else {
			$formulary = new FormularyItem($tableName);
			if ($formulary->populate(false)) {
				if ($formulary->isDefault()) {
					$msg = __("Formulary ".$formulary->getPrettyName()." is default and cannot be deleted");
					$code = 402;
				} else {
					$formulary->setPersistMode(WebVista_Model_ORM::DELETE);
					$formulary->persist(false);
					$msg = __("Formulary ".$formulary->getPrettyName()." deleted successfully");
					$code = 200;
				}
			}
			else {
				$msg = __("Formulary ".$formulary->getPrettyName()." does not exists");
				$code = 401;
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDefaultAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("id",""));
		if (strlen($tableName) == 0) {
			$msg = __("Invalid formulary name");
			$code = 400; // invalid entry
		}
		else {
			$formulary = new FormularyItem($tableName);
			if ($formulary->populate(false)) {
				if (!$formulary->isActive()) {
					$msg = __("Formulary ".$formulary->getPrettyName()." is not active and cannot be set to default");
					$code = 402;
				} else {
					$formulary->setDefault();
					$formulary->persist(false);
					$msg = __("Formulary ".$formulary->getPrettyName()." set to default successfully");
					$code = 200;
				}
			}
			else {
				$msg = __("Formulary ".$formulary->getPrettyName()." does not exists");
				$code = 401;
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function toolbarAction() {
		header("Cache-Control: public");
		header("Pragma: public");

		$cache = Zend_Registry::get('cache');
		$cacheKey = "fm-toolbar-" . Menu::getCurrentlySelectedActivityGroup() . "-" . Menu::getCurrentUserRole();
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
		if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
			header("Content-type: application/xhtml+xml");
		}
		else {
			header("Content-type: text/xml");
		}
		return $items;
	}

	public function listFormulariesAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("name",""));
		$formularyItem = new FormularyItem($tableName);
		$rows = array();
		foreach ($formularyItem->getAllRows() as $row) {
			$tmp = array();
			$drug = $row['fda_drugname'].' - '.$row['dose'].' - '.$row['strength'].' - '.$row['rxnorm'].' - '.$row['tradename'];
			$tmp['id'] = $row['fullNDC'];
			$tmp['data'][] = $drug;
			$tmp['data'][] = $row['directions'];
			$tmp['data'][] = $row['comments'];
			$tmp['data'][] = $row['schedule'];
			$tmp['data'][] = $row['labelId'];
			$tmp['data'][] = $row['externalUrl'];
			$tmp['data'][] = $row['price'];
			$tmp['data'][] = $row['qty'];
			$tmp['data'][] = $row['quantityQualifier'];
			$tmp['data'][] = $row['keywords'];
			$tmp['data'][] = $row['deaSchedule'];
			$tmp['data'][] = $row['print'];
			$tmp['data'][] = $row['description'];
			$tmp['data'][] = $row['dose'];
			$tmp['data'][] = $row['route'];
			$tmp['data'][] = $row['prn'];
			$tmp['data'][] = $row['refills'];
			$tmp['data'][] = $row['daysSupply'];
			$tmp['data'][] = $row['substitution'];
			$rows[] = $tmp;
		}
		/*
		$formularyIterator = $formularyItem->getIterator();
		foreach ($formularyIterator as $formulary) {
			//var_dump($formulary->toString());
			$tmp = array();
			$tmp['id'] = $formulary->fullNDC;
			$tmp['data'][] = '';
			$tmp['data'][] = $formulary->directions;
			$tmp['data'][] = $formulary->comments;
			$tmp['data'][] = $formulary->price;
			$rows[] = $tmp;
		}
		//exit;
		*/
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("name",""));
		$formulary = new FormularyItem($tableName);
		$formularyData = $this->_getParam("formulary");
		$formulary->populateWithArray($formularyData);
		$formulary->persist();
		$data = array();
		$data['msg'] = __("Formulary saved successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteFormularyAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("name",""));
		$fullNDC = preg_replace('/[^0-9_a-z-\.]+/i','',$this->_getParam("formularyId",""));
		$formulary = new FormularyItem($tableName);
		$formulary->fullNDC = $fullNDC;
		$formulary->setPersistMode(WebVista_Model_ORM::DELETE);
		$formulary->persist();
		$data = array();
		$data['msg'] = __("Formulary deleted successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function formularyContextMenuAction() {
		header("Cache-Control: public");
		header("Pragma: public");

		$cache = Zend_Registry::get('cache');
		$cacheKey = "fm-menu-" . Menu::getCurrentlySelectedActivityGroup() . "-" . Menu::getCurrentUserRole();
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
			$items = $this->render('formulary-context-menu');
			$hash = md5($items);
			$lastModified = gmdate("D, d M Y H:i:s")." GMT";
			$objConfig = new ConfigItem();
			$objConfig->configId = 'enableCache';
			$objConfig->populate();
			if ($objConfig->value) {
				$cache->save($hash, $cacheKey."_hash", array('tagMenu'));
				$cache->save($lastModified, $cacheKey."_lastModified", array('tagMenu'));
				$cache->save($items, $cacheKey, array('tagMenu'));
			}
			header("ETag: ". $hash);
			header("Last-Modified: ". $lastModified);
			header("Content-length: "  . mb_strlen($items));
		}
		if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
			header("Content-type: application/xhtml+xml");
		}
		else {
			header("Content-type: text/xml");
		}
		return $items;
	}

	public function getDefaultAction() {
		$data = array();
		$defaultTableName = FormularyItem::getDefaultFormularyTable();
		$prettyName = '';
		if ($defaultTableName !== false && isset($defaultTableName[9])) {
			$prettyName = preg_replace('/([A-Z]{1})/',' \1',substr($defaultTableName,9));
		}
		$data['msg'] = $prettyName;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSingleUpdateAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam("name",""));
		$fullNDC = preg_replace('/[^0-9_a-z-\.]+/i','',$this->_getParam("id",""));
		$formulary = new FormularyItem($tableName);
		$field = $this->_getParam("field");
		$value = preg_replace('/[^a-z_0-9-, :\/\?\&=\.;]/i','',html_entity_decode($this->_getParam("value","")));

		if (in_array($field,$formulary->ormFields())) {
			$formulary->fullNDC = $fullNDC;
			$formulary->populate();
			$formulary->$field = $value;
			if ($field == 'deaSchedule' && $value > 0) {
				$formulary->print = 1;
			}
			$formulary->persist();
		}
		$data = array();
		$data['msg'] = __('Updated successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processChangeNameAction() {
		$tableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam('oldName',''));
		$newTableName = preg_replace('/[^a-zA-Z]+/','',$this->_getParam('newName',''));
		if (strlen($tableName) == 0 || strlen($newTableName) == 0) {
			$msg = __('Invalid formulary name');
			$code = 400; // invalid entry
		}
		else if ($tableName == $newTableName) {
			$msg = __('No changes on formulary name');
			$code = 400; // invalid entry
		}
		else {
			$formulary = new FormularyItem($tableName);
			if (!$formulary->populate(false)) {
				$msg = __('Formulary ' . $formulary->getPrettyName() . ' does not exists');
				$code = 401;
			}
			else {
				$newTableName = 'formulary' . $newTableName;
				$newFormulary = new FormularyItem($newTableName);
				if (!$newFormulary->populate(false)) {
					$newFormulary->_defaultFormularyName = $tableName;
					$newFormulary->activate();
					$newFormulary->persist(false);

					$formulary->setPersistMode(WebVista_Model_ORM::DELETE);
					$formulary->persist(false);
					$msg = __('Formulary ' . $newFormulary->getPrettyName() . ' activated successfully');
					$code = 200;
				}
				else {
					$msg = __('Formulary ' . $formulary->getPrettyName() . ' already exists');
					$code = 401;
				}
			}
		}
		$data['msg'] = $msg;
		$data['code'] = $code;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processUploadCsvAction() {
		$name = preg_replace('/[^a-zA-Z]+/','',$this->_getParam('name',''));
		$data = array('message'=>__('Successfully uploaded'));
		$uploadFileName = $_FILES['uploadFile']['tmp_name'];
		$row = 0;
		if (($handle = fopen($uploadFileName,'r')) !== false) {
			$template = array(''=>'','fullNDC'=>'','directions'=>'','comments'=>'','schedule'=>'','labelId'=>'','externalUrl'=>'','price'=>'','qty'=>'','quantityQualifier'=>'','keywords'=>'','deaSchedule'=>'','print'=>'','description'=>'','dose'=>'','route'=>'','prn'=>'','refills'=>'','daysSupply'=>'','substitution'=>'');
			$arrangements = array('','fullNDC','directions','comments','schedule','labelId','externalUrl','price','qty','quantityQualifier','keywords','deaSchedule','print','description','dose','route','prn','refills','daysSupply','substitution');
			while (($data = fgetcsv($handle,0,',')) !== false) { // 0 = all chars in a line
				$ctr = count($data);
				$row++;
				if ($row == 1) { // headers
					if ($ctr != count($template)) {
						$data = array('error'=>__('Columns not matched'));
						break;
					}
					// remove all existing formularies
					$db = Zend_Registry::get('dbAdapter');
					$formulary = new FormularyItem($name);
					$db->delete($formulary->getName());
					continue;
				}
				$formularyData = $template;
				for ($i=1; $i < $ctr; $i++) {
					$formularyData[$arrangements[$i]] = $data[$i];
				}
				$formulary = new FormularyItem($name);
				$formulary->populateWithArray($formularyData);
				$formulary->persist();
			}
			fclose($handle);
		}
		else {
			$data = array('error'=>__('Failed opening the uploaded file. Please re-upload'));
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$jsonData = $json->direct($data,false);
		$this->getResponse()->setHeader('Content-Type','text/html');
		$this->view->result = $jsonData;
		$this->render();
	}

	public function processDownloadCsvAction() {
		$name = preg_replace('/[^a-zA-Z]+/','',$this->_getParam('name',''));
		$formularyItem = new FormularyItem($name);
		$rows = array();
		$header = array('Drug Name','NDC','Directions','Comments','Schedule','Label ID','External URL','Price','Qty','Qualifier','Keywords','DEA Schedule','Print','Description','Dose','Route','PRN','Refills','Days Supply','Substitution');
		$rows[] = implode(',',$header);
		foreach ($formularyItem->getAllRows() as $row) {
			$tmp = array();
			$drug = $row['fda_drugname'].' - '.$row['dose'].' - '.$row['strength'].' - '.$row['rxnorm'].' - '.$row['tradename'];
			$tmp[] = $drug;
			$tmp[] = $row['fullNDC'];
			$tmp[] = $row['directions'];
			$tmp[] = $row['comments'];
			$tmp[] = $row['schedule'];
			$tmp[] = $row['labelId'];
			$tmp[] = $row['externalUrl'];
			$tmp[] = $row['price'];
			$tmp[] = $row['qty'];
			$tmp[] = $row['quantityQualifier'];
			$tmp[] = $row['keywords'];
			$tmp[] = $row['deaSchedule'];
			$tmp[] = $row['print'];
			$tmp[] = $row['description'];
			$tmp[] = $row['dose'];
			$tmp[] = $row['route'];
			$tmp[] = $row['prn'];
			$tmp[] = $row['refills'];
			$tmp[] = $row['daysSupply'];
			$tmp[] = $row['substitution'];
			$rows[] = implode(',',$this->_sanitizeCSV($tmp));
		}
		$data = implode("\n",$rows);
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="formularies.csv"');
		$this->view->data = $data; 
	}

	protected function _sanitizeCSV(Array $data) {
		$ret = $data;
		foreach ($data as $key=>$value) {
			$enclosed = false;
			// Fields with embedded double-quote characters must be enclosed within double-quote characters, and each of the embedded double-quote characters must be represented by a pair of double-quote characters.
			if (($pos = strpos($value,'"')) !== false) {
				$str = '';
				while (($pos = strpos($value,'"')) !== false) {
					$pos++;
					$str .= substr($value,0,$pos).'"';
					$value = substr($value,$pos);
				}
				$value = $str.$value;
				$enclosed = true;
			}
			// Fields with embedded commas must be enclosed within double-quote characters
			if (strpos($value,',') !== false || strpos($value,"\n") !== false) {
				$enclosed = true;
			}
			if ($enclosed) {
				$ret[$key] = '"'.$value.'"';
			}
		}
		return $ret;
	}

}

