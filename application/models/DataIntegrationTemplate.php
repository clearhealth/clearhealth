<?php
/*****************************************************************************
*       DataIntegrationTemplate.php
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


class DataIntegrationTemplate extends DataIntegration {

	protected $dataIntegrationTemplateId;
	protected $guid;
	protected $name;
	protected $template;
	protected $handlerType;
	protected $_table = 'dataIntegrationTemplates';
	protected $_primaryKeys = array('dataIntegrationTemplateId');

	public function persist() {
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) return parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		$dataIntegrationTemplateId = (int)$this->dataIntegrationTemplateId;
		$data = $this->toArray();
		if ($dataIntegrationTemplateId > 0) {
			$ret = $db->update($this->_table,$data,'dataIntegrationTemplateId = '.$dataIntegrationTemplateId);
		}
		else {
			$this->dataIntegrationTemplateId = WebVista_Model_ORM::nextSequenceId();
			$data['dataIntegrationTemplateId'] = $this->dataIntegrationTemplateId;
			$ret = $db->insert($this->_table,$data);
		}
		return $this;
	}

	public function getCustomIterator($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$handlerSelect = $db->select()
					    ->from('handlers','dataIntegrationTemplateId')
					    ->where('handlerType = ?',$this->handlerType);
			$dbSelect = $db->select()
					->from($this->_table)
					->where('handlerType = ?',$this->handlerType)
					->where('dataIntegrationTemplateId NOT IN ( '.$handlerSelect->__toString().' )',$this->handlerType)
					->order('name');
		}
		return parent::getIterator($dbSelect);
	}

}
