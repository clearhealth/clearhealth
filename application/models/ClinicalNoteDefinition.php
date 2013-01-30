<?php
/*****************************************************************************
*       ClinicalNoteDefinition.php
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


class ClinicalNoteDefinition extends WebVista_Model_ORM {
	protected $clinicalNoteDefinitionId;
	protected $title;
	protected $clinicalNoteTemplateId;
	protected $clinicalNoteTemplate;
	protected $active;
	protected $_primaryKeys = array('clinicalNoteDefinitionId');
	protected $_table = "clinicalNoteDefinitions";
	protected $_cascadePersist = false;
	
	function __construct() {
		parent::__construct();
		$this->clinicalNoteTemplate = new ClinicalNoteTemplate();
		$this->clinicalNoteTemplate->_cascadePersist = false;
	}

	function setClinicalNoteTemplateId($key) {
		if ((int)$key != $this->clinicalNoteTemplateId) {
			$cnTemplate = new ClinicalNoteTemplate();
			unset($this->clinicalNoteTemplate);
			$this->clinicalNoteTemplate = $cnTemplate;
		}
		$this->clinicalNoteTemplateId = (int)$key;
		$this->clinicalNoteTemplate->clinicalNoteTemplateId = (int)$key;
	}

	function __get($key) {
		if (isset($this->$key)) {
			return $this->$key;
		}
		elseif (in_array($key,$this->clinicalNoteTemplate->ORMFields())) {
                        return $this->clinicalNoteTemplate->__get($key);
                }
		return parent::__get($key);
	}

}
