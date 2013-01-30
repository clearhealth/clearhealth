<?php
/*****************************************************************************
*       AppointmentTemplate.php
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


class AppointmentTemplate extends WebVista_Model_ORM {

	protected $appointmentTemplateId;
	protected $name;
	protected $breakdown; // serialize array
	protected $_table = 'appointmentTemplates';
	protected $_primaryKeys = array('appointmentTemplateId');

	const ENUM_PARENT_NAME = 'Appointment Reason';

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
			return $view->action('edit','enumerations-manager',null,$params);
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['ormId'] = $ormId;
			return $view->action('edit','appointment-templates',null,$params);
		}
	}

	public function getAppointmentReasons() {
		$name = self::ENUM_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationId = $enumeration->enumerationId;

		$db = Zend_Registry::get('dbAdapter');
		$enumerationsClosure = new EnumerationsClosure();

		$dbSelect = $db->select()->from(array('e'=>$enumeration->_table))
			       ->join(array('ec'=>'enumerationsClosure'),'e.enumerationId = ec.descendant')
			       ->join(array('at'=>$this->_table),'at.appointmentTemplateId = e.ormId',array('appointmentTemplateName'=>'name','breakdown'))
			       ->where('ec.ancestor = ?',(int)$enumerationId)
			       ->where('ec.ancestor != ec.descendant')
			       ->where('ec.depth = 1')
			       ->where('e.active = 1')
			       ->order('ec.weight ASC')
			       ->order('at.name ASC');

		$rows = array();
		if ($rowset = $db->fetchAll($dbSelect)) {
			foreach ($rowset as $row) {
				// unserialize breakdown
				if (strlen($row['breakdown']) > 0) {
					$row['breakdown'] = unserialize($row['breakdown']);
				}
				else {
					$row['breakdown'] = array();
				}
				$rows[$row['enumerationId']] = $row;
			}
		}
		return $rows;
	}

}
