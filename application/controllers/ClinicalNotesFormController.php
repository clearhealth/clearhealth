<?php
/*****************************************************************************
*       ClinicalNotesFormController.php
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


class ClinicalNotesFormController extends WebVista_Controller_Action {

        protected $_form;
        protected $_cn;

        public function init() {
                $this->_session = new Zend_Session_Namespace(__CLASS__);
                $cprss = new Zend_Session_Namespace('CprsController');
        }

	public function indexAction() {
		$this->render();
	}

	function templateAction() {
		$clinicalNoteId = $this->_getParam('clinicalNoteId',0);
		$revisionId = (int)$this->_getParam('revisionId');
		$cn = new ClinicalNote();
		$cn->clinicalNoteId = (int)$clinicalNoteId;
		$cn->populate();
		if ($revisionId > 0) {
			$cn->revisionId = $revisionId;
		}
		$this->_cn = $cn;
		$templateId = $cn->clinicalNoteTemplateId;
		assert('$templateId > 0');
		$cnTemplate = $cn->clinicalNoteDefinition->clinicalNoteTemplate;
		$this->_form = new WebVista_Form(array('name' => 'cn-template-form'));
		$this->_form->setWindow('dummyWindowId');
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "clinical-notes-form.raw/process");
		$cnXML = simplexml_load_string($cnTemplate->template);
		$this->_buildForm($cnXML);
		$this->_form->addElement($this->_form->createElement('hidden','clinicalNoteId', array('value' => (int)$cn->clinicalNoteId)));

		$formData = array();
		$this->_form->removeElement('ok');
		$element = $this->_form->createElement('hidden','clinicalNoteOKId',array('value'=>'OK'));
		$this->_form->addElement($element);

		if ($revisionId > 0) {
			//$this->_form->removeElement('ok');
			$this->_form->removeElement('clinicalNoteOKId');
		}

		$this->_form->addElement($this->_form->createElement('hidden','revisionId', array('value' => (int)$revisionId)));

		$db = Zend_Registry::get('dbAdapter');
		$cndSelect = $db->select()
				->from('genericData')
				->where("objectClass = 'ClinicalNote'")
				->where('objectId = ?',(int)$cn->clinicalNoteId);
		if ($revisionId > 0) {
			$cndSelect->where('revisionId = ?',(int)$revisionId);
		}
		trigger_error($cndSelect->__toString(),E_USER_NOTICE);
		foreach($db->query($cndSelect)->fetchAll() as $row) {
			$formData[ClinicalNote::encodeNamespace($row['name'])] = $row['value'];
		}

		if ((int)$cn->eSignatureId > 0) { // On signed notes generic data is shown
			//$this->_form->removeElement('ok');
			$this->_form->removeElement('clinicalNoteOKId');
			$esig = new ESignature();
			$esig->eSignatureId = $cn->eSignatureId;
			$esig->populate();
			$signPerson = new Person();
			$signPerson->personId = $esig->signingUserId;
			$signPerson->populate();
			$person = new Person();
			$person->personId = $esig->signingUserId;
			$person->populate();
			$this->view->signatureInfo = "Signed on: " . $esig->signedDateTime . " by: " . $person->firstName.' '.$person->lastName.' '.$person->suffix;
			$element = $this->_form->createElement('hidden','clinicalNoteSignatureId',array('value'=>$this->view->signatureInfo));
			$this->_form->addElement($element);
		}
		else { // on unsigned notes NSDR is shown but a warning also needs to appear that says data has changed since last save if generic data != NSDR data
			if ((string)$cnXML->attributes()->useNSDR && (string)$cnXML->attributes()->useNSDR == 'true') {
				$nsdrData = ClinicalNote::getNSDRData($cnXML,$cn,$revisionId);
				if ($formData != $nsdrData) {
					$msg = __('Data has been changed since last save');
					$this->_form->addElement($this->_form->createElement('hidden','dataChangedId',array('value'=>$msg)));
				}
				$formData = $nsdrData;
			}
		}
		$this->_form->populate($formData);

		$this->view->form = $this->_form;
        }

	function processAction() {
		$clinicalNoteId = (int)$this->_getParam('clinicalNoteId');
		$revisionId = (int)$this->_getParam('revisionId');
		$data = $this->_getParam('namespaceData');
		$saveDate = date('Y-m-d H:i:s');

		$cn = new ClinicalNote();
		$cn->clinicalNoteId = $clinicalNoteId;
		$cn->populate();

		if ((int)$cn->eSignatureId > 0) {
			$msg = __('Failed to save. Note is already signed');
		}
		else {
			$cn->dateTime = date('Y-m-d H:i:s');
			$cn->persist();

                	$msg = __('Data saved.');
			$template = $cn->clinicalNoteDefinition->clinicalNoteTemplate->template;
			$xml = simplexml_load_string($template);

			$objectClass = 'ClinicalNote';
			list($name,$value) = each($data);
			$gd = new GenericData();
			$gd->objectClass = $objectClass;
			$gd->objectId = $clinicalNoteId;
			$gd->name = $name;
			$rowExists = $gd->doesRowExist(true);
			$preQueries = null;
			if ($rowExists) {
				$revisionId = (int)$gd->revisionId;
				$preQueries = 'DELETE FROM `'.$gd->_table.'` WHERE `revisionId`='.$revisionId;
			}
			else {
				$revisionId = WebVista_Model_ORM::nextSequenceId();
			}

			$otm = new WebVista_Model_ORMTransactionManager();
			foreach($data as $name => $value) {
				$gd = new GenericData();
				$gd->objectClass = $objectClass;
				$gd->objectId = $clinicalNoteId;
				$gd->dateTime = $saveDate;
				$gd->name = ClinicalNote::decodeNamespace($name);
				$gd->value = $value;
				$gd->revisionId = $revisionId;
				$otm->addORM($gd);
			}
			if (!$otm->persist($preQueries)) {
				$msg = __('Failed to save.');
			}

			if ((string)$xml->attributes()->useNSDR && (string)$xml->attributes()->useNSDR == 'true') {
				if (!ClinicalNote::processNSDRPersist($xml,$cn,$data,$revisionId)) {
					$msg = __('Failed to save.');
				}
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($msg);
	}

	protected function _buildForm($xml) {
		static $_namespaceElements = array();
		$headingCounter = 1;
		$counter = 1;
		foreach ($xml as $question) {
			$elements = array();
			foreach($question as $key => $item) {
				if ($key == "dataPoint") $dataPoint = $item;
				elseif ($key == "heading") {
					$headingName = 'heading'.$headingCounter++;//preg_replace('/[^a-zA-Z0-9\ ]/','',(string)$item);
					$element = $this->_form->createElement('hidden',$headingName, array('label' => (string)$item, 'disabled' => "disabled"));
					$element->addDecorator('Label', array('tag' => 'h3'));
					$this->_form->addElement($element);
					$elements[] = $headingName;
					continue;
				}
				elseif ($key == 'break') {
					$breakName = 'break'.$counter++;
					$element = $this->_form->createElement('hidden',$breakName,array('disabled'=>'disabled'));
					$element->addDecorator('HtmlTag',array('placement'=>'APPEND','tag'=>'<br />'));
					$this->_form->addElement($element);
					$elements[] = $breakName;
					continue;
				}
				else { continue; } 
				$scripts = array();
				$type = (string)$dataPoint->attributes()->type;
				if ($this->_cn->eSignatureId > 0 && $type == 'div') {
					$type = 'pre';
				}
				else if ($this->_cn->eSignatureId > 0 && $type == 'richEdit') {
					$type = 'readOnly';
				}
                                if ($type == "img" && (string)$dataPoint->attributes()->draw == "true") {
					$type = 'drawing';
                                }

				$elementName = ClinicalNote::encodeNamespace((NSDR2::extractNamespace((string)$dataPoint->attributes()->namespace)));
				if ($this->_form->getElement($elementName) instanceof Zend_Form_Element) {
                                        $element = $this->_form->getElement($elementName);
                                }
				else {
					$element = $this->_form->createElement($type,$elementName, array('label' => (string)$dataPoint->attributes()->label));
				}

				$reservedAttributes = array(
					'templateText'=>'templateText',
					'label'=>'label',
					'value'=>'value',
					'containerStyle'=>'containerStyle',
					'type'=>'type',
					'draw'=>'draw',
					'namespace'=>'namespace',
					'default'=>'default',
					'radioGroup'=>'radioGroup',
					'radioGroupDefault'=>'radioGroupDefault',
				);

                                if ((string)$dataPoint->attributes()->type == "radio" || $type == "select") {
					$attributes = array();
					foreach ($dataPoint->attributes() as $k=>$v) {
						if (isset($reservedAttributes[$k])) continue;
						$attributes[$k] = $v;
					}
                                        $element->setLabel("");
                                        $element->setSeparator("&nbsp;&nbsp;");
					if ((string)$dataPoint->attributes()->type == 'radio') {
						$optionLabels = array('label'=>(string)$dataPoint->attributes()->label,'attributes'=>$attributes); // composed of label and attributes
                                        	$element->addMultiOption((string)$dataPoint->attributes()->value,$optionLabels);
					}
					else {
						$element->setAttribs($attributes);
                                        	$element->addMultiOption((string)$dataPoint->attributes()->value,(string)$dataPoint->attributes()->label);
					}
                                        if ((string)$dataPoint->attributes()->default == true) {
                                                $element->setValue((string)$dataPoint->attributes()->value);
                                        }
                                }
				else {
					foreach ($dataPoint->attributes() as $k=>$v) {
						if (isset($reservedAttributes[$k])) continue;
						$element->setAttrib($k,$v);
					}
				}
                                if ((string)$dataPoint->attributes()->type == "checkbox") {
					$element->addDecorator('Label', array('placement' => 'APPEND'));
					$element->addDecorator('HtmlTag', array('placement' => 'PREPEND', 'tag' => '<br />'));
					if ((string)$dataPoint->attributes()->radioGroup) {
						$radioGroup = '_'.preg_replace('/\./','_',(string)$dataPoint->attributes()->radioGroup);
						$innerHTML = '';
						$elName = 'namespaceData['.$element->getName().']';
						if (!isset($_namespaceElements[$radioGroup])) {
							$_namespaceElements[$radioGroup] = true;
							$innerHTML = '
var '.$radioGroup.'Members = [];
function '.$radioGroup.'(name) {
	var elName = null;
	var el = null;
	var elem = null;
	for (var i = 0; i < '.$radioGroup.'Members.length; i++) {
		elName = '.$radioGroup.'Members[i];
		el = document.getElementsByName(elName);
		if (!el) {
			continue;
		}
		elem = null;
		for (var j = 0; j < el.length; j++) {
			if (el[j].type == "checkbox") {
				elem = el[j];
				break;
			}
		}
		if (elem == null) continue;
		if (elem.checked && elName != name) {
			elem.checked = false;
			//break; // there is only one checked element in a group
		} 
	}
}
';
						}
						$innerHTML .= $radioGroup.'Members.push("'.$elName.'");';
                                       		$scripts[] = $innerHTML;
						$element->setAttrib('onchange',$radioGroup.'("'.$elName.'")');
					}
					if ((string)$dataPoint->attributes()->radioGroupDefault && (string)$dataPoint->attributes()->radioGroupDefault == '1') {
						$element->setAttrib('checked','checked');
					}
				}
                                if (strlen((string)$dataPoint->attributes()->templateText) > 0) {
					$templateName = (string)$dataPoint->attributes()->templateText;
					//$element->setValue($this->view->action('index','template-text',null,array('personId' => $this->_cn->personId)));
					$element->setValue($this->view->action('templated-text','template-text',null,array('personId' => $this->_cn->personId,'templateName'=>$templateName)));
                                }

				if ((string)$dataPoint->script) $scripts[] = (string)$dataPoint->script;

				if (isset($scripts[0])) $element->addDecorator("ScriptTag",array('placement' => 'APPEND','tag' => 'script','innerHTML' => implode("\n",$scripts),'noAttribs' => true));

				$element->setBelongsTo('namespaceData');
				//var_dump($element);
				$this->_form->addElement($element);
				$elements[] = $elementName;
			}
			//var_dump($elements);
			if (count($elements) > 0) {
				$this->_form->addDisplayGroup($elements,(string)$question->attributes()->label,array("legend" => (string)$question->attributes()->label));
				if (strlen(trim((string)$question->attributes()->containerStyle)) > 0) { 
				    $displayGroup = $this->_form->getDisplayGroup((string)$question->attributes()->label);
				    $style = preg_replace('/\xEF\xBB\xBF/','',trim((string)$question->attributes()->containerStyle));
				    $displayGroup->setDecorators(array(
						'FormElements',
						array('HtmlTag',
						  array('tag'=>'dl', 
						    'style'=> $style
						  )
						),
						'Fieldset'
					));
				}
			}
		}
	}

}
