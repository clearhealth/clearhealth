<?php
/*****************************************************************************
*       ClinicalNote.php
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


class ClinicalNote extends WebVista_Model_ORM implements Document, NSDRMethods {
	protected $clinicalNoteId;
	protected $personId;
	protected $visitId;
	protected $clinicalNoteDefinitionId;
	protected $clinicalNoteDefinition;
	protected $dateTime;
	protected $authoringPersonId;
	protected $consultationId;
	protected $locationId;
	protected $eSignatureId;

	protected $_primaryKeys = array('clinicalNoteId');
	protected $_table = "clinicalNotes";
	protected $_cascadePersist = false;
	
	function __construct() {
		parent::__construct();
		$this->clinicalNoteDefinition = new ClinicalNoteDefinition();
		$this->clinicalNoteDefinition->_cascadePersist = false;
	}

	function setClinicalNoteDefinitionId($key) {
		if ((int)$key != $this->clinicalNoteTemplateId) {
			$cnDefinition = new ClinicalNoteDefinition();
			$cnDefinition->_cascadePersist = false;
			unset($this->clinicalNoteDefinition);
			$this->clinicalNoteDefinition = $cnDefinition;
		}
		$this->clinicalNoteDefinitionId = (int)$key;
		$this->clinicalNoteDefinition->clinicalNoteDefinitionId = (int)$key;
	}

	function __get($key) {
		if (in_array($key,$this->clinicalNoteDefinition->ORMFields())) {
			return $this->clinicalNoteDefinition->__get($key);
		}
		return parent::__get($key);
	}

	function getSummary() {
		$this->clinicalNoteDefinition->populate();
                return $this->clinicalNoteDefinition->title;
	}

	public function getDocumentId() {
		return $this->clinicalNoteId;
	}

	function setDocumentId($id) {
		$this->clinicalNoteId = (int)$id;
	}

	function getContent() {
		return "";
	}

	static function getPrettyName() {
		return "Clinical Notes";
	}

	public static function getControllerName() {
		return "ClinicalNotesController";
	}
	
	function setSigned($eSignatureId) {
		$this->eSignatureId = (int)$eSignatureId;
		$this->persist();
	}

	public static function getNSDRData(SimpleXMLElement $xml,self $clinicalNote,$revisionId=0) {
		$ret = array();
		if ((string)$xml->attributes()->useNSDR != 'true') {
			return $ret;
		}
		foreach ($xml as $questions) {
			foreach($questions as $key=>$item) {
				$namespace = (string)$item->attributes()->namespace;
				if ($key != 'dataPoint' || ($namespace && !strlen($namespace) > 0)) {
					continue;
				}
				$default = 0;
				$selectedPatientId = '[selectedPatientId]';
				$key = str_replace($selectedPatientId,$clinicalNote->personId,$namespace);
				if (preg_match('/^[^.]*/',$key,$matches) && strpos($matches[0],'::') === false) { // context is not defined, default to *?
					$key = '*::'.$key;
					$default = 1;
				}
				if (preg_match('/(.*)\[(.*)\]$/',$key,$matches)) {
					$args = $matches[2];
					$x = explode(',',$args);
					$x[] = '@revisionId='.$revisionId;
					$x[] = '@clinicalNoteId='.$clinicalNote->clinicalNoteId;
					$x[] = '@isDefaultContext='.$default;
					$key = str_replace($args,implode(',',$x),$key);
				}
				else {
					$key .= '[@revisionId='.$revisionId.',@clinicalNoteId='.$clinicalNote->clinicalNoteId.',@isDefaultContext='.$default.']';
				}
				$namespace = NSDR2::extractNamespace($namespace);
				$result = NSDR2::populate($key);
				$value = $result;
				if (is_array($result) && isset($result[$key])) {
					$value = $result[$key];
				}
				// this MUST coincide with the $elementName of ClinicalNotesFormController::_buildForm()
				$elementName = ClinicalNote::encodeNamespace($namespace);
				$ret[$elementName] = $value;
			}
		}
		return $ret;
	}

	public static function processNSDRPersist(SimpleXMLElement $xml,self $clinicalNote,Array $data,$revisionId=0) {
		$ret = false;
		if ((string)$xml->attributes()->useNSDR != 'true') {
			$ret = false;
		}
		$requests = array();
		foreach ($xml as $questions) {
			foreach($questions as $key => $item) {
				$namespace = (string)$item->attributes()->namespace;
				if ($key != 'dataPoint' || ($namespace && !strlen($namespace) > 0)) {
					continue;
				}
				$default = 0;
				$selectedPatientId = '[selectedPatientId]';
				$key = str_replace($selectedPatientId,$clinicalNote->personId,$namespace);
				if (preg_match('/^[^.]*/',$key,$matches) && strpos($matches[0],'::') === false) { // context is not defined, default to *?
					$key = '*::'.$key;
					$default = 1;
				}
				if (preg_match('/(.*)\[(.*)\]$/',$key,$matches)) {
					$args = $matches[2];
					$x = explode(',',$args);
					$x[] = '@revisionId='.$revisionId;
					$x[] = '@clinicalNoteId='.$clinicalNote->clinicalNoteId;
					$x[] = '@isDefaultContext='.$default;
					$key = str_replace($args,implode(',',$x),$key);
				}
				else {
					$key .= '[@revisionId='.$revisionId.',@clinicalNoteId='.$clinicalNote->clinicalNoteId.',@isDefaultContext='.$default.']';
				}
				$val = array();
				$namespace = NSDR2::extractNamespace($namespace);
				// this MUST coincide with the $elementName of ClinicalNotesFormController::_buildForm()
				$elementName = ClinicalNote::encodeNamespace($namespace);
				if (isset($data[$elementName])) {
					$val = $data[$elementName];
				}

				//$requests[$key] = $val;
				if (!is_array($val)) {
					$val = array($val);
				}
				$ret = NSDR2::persist($key,$val);
			}
		}
		return $ret;
	}

	function nsdrPersist($tthis,$context,$data) {
	}

	public function nsdrPopulate($tthis,$context,$data) {
	}

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	public function buildDefaultGenericData(SimpleXMLElement $xml = null) {
		if ($xml === null) {
			if (!strlen($this->clinicalNoteDefinition->clinicalNoteTemplate->template) > 0) {
				$this->clinicalNoteDefinition->populate();
			}
			$xml = new SimpleXMLElement($this->clinicalNoteDefinition->clinicalNoteTemplate->template);
		}
		$revisionId = WebVista_Model_ORM::nextSequenceId();
		$nsdrData = array();
		if ((string)$xml->attributes()->useNSDR && (string)$xml->attributes()->useNSDR == 'true') {
			$nsdrData = ClinicalNote::getNSDRData($xml,$this,$revisionId);
		}
		$dateTime = date('Y-m-d H:i:s');
		foreach ($xml as $question) {
			foreach($question as $key => $item) {
				if ($key != 'dataPoint') continue;
				$namespace = NSDR2::extractNamespace((string)$item->attributes()->namespace);
				$name = $namespace;
				$value = '';

				if (strlen((string)$item->attributes()->templateText) > 0) {
					$templateName = (string)$item->attributes()->templateText;
					$view = Zend_Layout::getMvcInstance()->getView();
					$value = $view->action('templated-text','template-text',null,array('personId' => $this->personId,'templateName'=>$templateName));
				}
				if ((string)$item->attributes()->default == true) {
					$value = (string)$item->attributes()->value;
				}
				if (isset($nsdrData[$name])) {
					$value = $nsdrData[$name];
				}

				$gd = new GenericData();
				$gd->objectClass = get_class($this);
				$gd->objectId = $this->clinicalNoteId;
				$gd->dateTime = $dateTime;
				$gd->name = $name;
				$gd->value = $value;
				$gd->revisionId = $revisionId;
				$gd->persist();
				//trigger_error('PERSISTED:'.print_r($gd->toArray(),true),E_USER_NOTICE);
			}
		}
		return $revisionId;
	}

	public function signatureNeeded() {
		$ret = true;
		if (!strlen($this->clinicalNoteDefinition->clinicalNoteTemplate->template) > 0) {
			$this->clinicalNoteDefinition->populate();
		}
		if (strlen($this->clinicalNoteDefinition->clinicalNoteTemplate->template) > 0) {
			$xml = new SimpleXMLElement($this->clinicalNoteDefinition->clinicalNoteTemplate->template);
			if (isset($xml->attributes()->signable) && (string)$xml->attributes()->signable == 'false') {
				$ret = false;
			}
		}
		return $ret;
	}

	public static function encodeNamespace($value) {
		return strtr(base64_encode($value),'+/=','___');
	}

	public static function decodeNamespace($value) {
		return base64_decode(strtr($value,'___','+/='));
	}

	protected function _addChild(SimpleXMLElement $xml,$key,$value,$checked=true) {
		if (is_object($value)) trigger_error($key.'='.get_class($value));
		if (!$checked || (strlen($key) > 0 && strlen($value) > 0)) $xml->addChild($key,htmlentities($value));
	}

	public function populateXML(SimpleXMLElement $xml=null,$revisionId=null,$checked=true) {
		if ($xml === null) $xml = new SimpleXMLElement('<data/>');
		$db = Zend_Registry::get('dbAdapter');
		$clinicalNoteId = (int)$this->clinicalNoteId;
		if ($revisionId === null) $revisionId = GenericData::getMostRecentRevisionId('ClinicalNote',$clinicalNoteId);
		$sqlSelect = $db->select()
				->from('genericData',array('name','value'))
				->where("objectClass = 'ClinicalNote'")
				->where('objectId = ?',$clinicalNoteId)
				->where('revisionId = ?',(int)$revisionId)
				->order('name');
		//trigger_error($sqlSelect->__toString());
		$stmt = $db->query($sqlSelect);
		$namespaces = array();
		while ($row = $stmt->fetch()) {
			$value = $row['value'];
			if (!strlen($value) > 0) continue;
			$namespace = explode('.',$row['name']);
			$tags = array();
			$prevKey = null;
			for ($i=0,$ctr=count($namespace);$i<$ctr;$i++) {
				$tagName = $namespace[$i];
				$tags[] = $tagName;
				$key = implode('.',$tags);
				if (!isset($namespaces[$key])) {
					if ($prevKey === null) {
						$namespaces[$key] = $xml->addChild($tagName);
					}
					else if ($i + 1 >= $ctr) {
						$this->_addChild($namespaces[$prevKey],$tagName,$value,$checked);
					}
					else {
						$namespaces[$key] = $namespaces[$prevKey]->addChild($tagName);
					}
				}
				$prevKey = $key;
			}
		}

		$signatureId = (int)$this->eSignatureId;
		if ($signatureId > 0) {
			$esig = new ESignature();
			$esig->eSignatureId = $signatureId;
			$esig->populate();
			$signingUserId = (int)$esig->signingUserId;
			if ($signingUserId > 0) {
				$person = new Person();
				$person->personId = $signingUserId;
				$person->populate();
				$xmlSigned = $xml->addChild('signingInfo');
				$this->_addChild($xmlSigned,'signedBy',$person->displayName,$checked);
				$this->_addChild($xmlSigned,'dateSigned',$esig->signedDateTime,$checked);
			}
		}
		return $xml;
	}

	public function toASCII($revisionId) {
		$template = $this->clinicalNoteDefinition->clinicalNoteTemplate->template;
		$xml = new SimpleXMLElement($template);

		$options = array();
		$types = array();
		$elements = array();
		foreach ($xml as $question) {
			$elements[] = "\n---".(string)$question->attributes()->label."---\n";
			foreach($question as $key=>$item) {
				if ($key == 'dataPoint') $dataPoint = $item;
				else if ($key == 'heading') {
					$elements[] = '  '.(string)$item;
					continue;
				}
				else continue;
				$namespace = (string)$dataPoint->attributes()->namespace;

				$value = '';
				$val = (string)$dataPoint->attributes()->value;
				$label = '    '.(string)$dataPoint->attributes()->label;
				$type = (string)$dataPoint->attributes()->type;
				switch ($type) {
					case 'img':
					case 'drawing':
						$value = $namespace.': Source note includes annotated diagram';
						break;
					case 'radio':
					case 'select':
						if ((string)$dataPoint->attributes()->default == true) $value = $val;
						if (!isset($options[$namespace])) $options[$namespace] = array();
						$options[$namespace][$val] = $label;
						break;
					case 'codeLookup':
					case 'currencyText':
					case 'dateText':
					case 'checkbox':
					case 'div':
					case 'grid':
					case 'numberSpinner':
					case 'pre':
					case 'readOnly':
					case 'richEdit':
					default:
						break;
				}
				$templateText = (string)$dataPoint->attributes()->templateText;
				if (strlen($templateText) > 0) {
					$view = Zend_Layout::getMvcInstance()->getView();
					$value = $view->action('templated-text','template-text',null,array('personId'=>$this->personId,'templateName'=>$templateText));
				}
				$elements[$namespace] = array('label'=>$label,'value'=>$value);
				$types[$namespace] = $type;
			}
		}
		if ((int)$this->eSignatureId > 0) {
			$esig = new ESignature();
			$esig->eSignatureId = $this->eSignatureId;
			$esig->populate();
			$signPerson = new Person();
			$signPerson->personId = $esig->signingUserId;
			$signPerson->populate();
			$person = new Person();
			$person->personId = $esig->signingUserId;
			$person->populate();
			$elements[] = "\n\n\n=== Signed on: ".$esig->signedDateTime.' by: '.$person->firstName.' '.$person->lastName.' '.$person->suffix.' === ';
		}

		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('genericData',array('name','value'))
				->where("objectClass = 'ClinicalNote'")
				->where('objectId = ?',(int)$this->clinicalNoteId);
		if ($revisionId > 0) $sqlSelect->where('revisionId = ?',(int)$revisionId);

		$stmt = $db->query($sqlSelect);
		while ($row = $stmt->fetch()) {
			$namespace = $row['name'];
			if (!isset($elements[$namespace])) continue;

			$value = $row['value'];
			switch ($types[$namespace]) {
				case 'img':
				case 'drawing':
					$elements[$namespace] = $namespace.': Source note includes annotated diagram';
					continue 2;
				case 'radio':
				case 'select':
					if (isset($options[$namespace]) && isset($options[$namespace][$value])) {
						$elements[$namespace] = $options[$namespace][$value];
						continue 2;
					}
					break;
				case 'codeLookup':
					$tmp = '';
					foreach (explode('^|^',$value) as $val) {
						if ($tmp != '') $tmp .= "\n";
						$tmp .= '    '.substr($val,2).': '.substr($val,0,1);
					}
					$elements[$namespace] = $tmp;
					continue 2;
				case 'currencyText':
				case 'dateText':
				case 'checkbox':
				case 'div':
				case 'grid':
				case 'numberSpinner':
				case 'pre':
				case 'readOnly':
				case 'richEdit':
				default:
					break;
			}
			$elements[$namespace]['value'] = $value;
		}
		$ret = '';
		foreach ($elements as $element) {
			if ($ret != '') $ret .= "\n";
			if (is_array($element)) {
				$label = $element['label'];
				if (substr($label,-1) != ':') $label .= ':';
				$value = htmlentities(str_replace("\n","\n ".str_repeat(' ',strlen($label)),$element['value']));
				$ret .= $label.' '.$value;
			}
			else $ret .= $element;
		}
		return $ret;
	}

	public static function mostRecent($personId,$clinicalNoteDefinitionId=0) {
		$db = Zend_Registry::get('dbAdapter');
		$clinicalNote = new self();
		$sqlSelect = $db->select()
				->from($clinicalNote->_table)
				->where('personId = ?',(int)$personId)
				->order('dateTime DESC')
				->limit(1);
		$clinicalNoteDefinitionId = (int)$clinicalNoteDefinitionId;
		if ($clinicalNoteDefinitionId > 0) {
			$sqlSelect->where('clinicalNoteDefinitionId = ?',$clinicalNoteDefinitionId);
		}
		$clinicalNote->populateWithSql($sqlSelect->__toString());
		return $clinicalNote;
	}

}
