<?php
/*****************************************************************************
*       TeamMember.php
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


class TeamMember extends WebVista_Model_ORM {

	protected $teamMemberId;
	protected $personId;
	protected $cosignWithParent;
	protected $role;

	protected $_primaryKeys = array('teamMemberId');
	protected $_table = "teamMembers";

	const ENUM_PARENT_NAME = 'Team Preferences';

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam("enumerationId");

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
			return $view->action("edit","team-manager",null,$params);
		}
	}

	public static function generateTeamTree($enumerationId,$level=0,$separator="<br />\n") {
		$db = Zend_Registry::get('dbAdapter');
		static $enumerationList = array();
		$data = array();
		$enumeration = new Enumeration();
		$dbSelect = $db->select()->from(array('e'=>$enumeration->_table))
			       ->join(array('ec'=>'enumerationsClosure'),'e.enumerationId = ec.descendant')
			       ->joinLeft(array('t'=>'teamMembers'),'t.teamMemberId = e.ormId')
			       ->joinLeft(array('p'=>'person'),'p.person_id = t.personId')
			       ->where('ec.ancestor = ?',(int)$enumerationId)
			       ->where('ec.ancestor != ec.descendant')
			       ->where('ec.depth = 1')
			       ->where('e.active = 1')
			       ->order('ec.weight ASC');
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		if ($rowset = $db->fetchAll($dbSelect)) {
			$nextLevel = $level + 2;
			foreach ($rowset as $row) {
				if (in_array($row['enumerationId'],$enumerationList)) {
					continue;
				}
				$enumerationList[] = $row['enumerationId'];

				$name = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['initials'] . ', ' . $row['suffix'];
				if ($name == ',   , ') {
					$name = '';
				}
				$contacts = 'Email: ' . $row['email'] . ' Home: ' . ' Pager: ';
				$role = '<label title="' . $contacts . '">' . $row['name'] . ': ' . $name . '</label>';

				$data[] = str_repeat('&nbsp;',$level) . $role;
				if ($enumerationId != $row['enumerationId']) { // prevents infinite loop
					$ret = self::generateTeamTree($row['enumerationId'],$nextLevel);
					if (strlen($ret) > 0) {
						$data[] = $ret;
					}
				}
			}
		}
		return implode($separator,$data);
	}

	public function populateByPersonId($personId = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($personId === null) {
			$personId = $this->personId;
		}
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('personId = ?',(int)$personId);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	public function getTeamByPersonId($personId = null) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = '';
		if ($personId === null) {
			$personId = $this->personId;
		}
		$dbSelect = $db->select()
			       ->from(array('t'=>$this->_table))
			       ->joinLeft(array('e'=>'enumerations'),'t.teamMemberId = e.ormId')
			       ->where('t.personId = ?',(int)$personId);
		$row = $db->fetchRow($dbSelect);
		if (isset($row['enumerationId'])) {
			$enumerationId = $row['enumerationId'];
			$enumerationsClosure = new EnumerationsClosure();
			$parentId = $enumerationsClosure->getParentById($enumerationId);
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $parentId;
			$enumeration->populate();
			$ret = $enumeration->key;
		}
		return $ret;
	}

	public static function getAttending($teamId) {
		$name = TeamMember::ENUM_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName($name);

		$enumerationsClosure = new EnumerationsClosure();
		$rowset = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);

		$ret = 0;
		foreach ($rowset as $row) {
			if ($teamId == $row->key) {
				$attendings = $enumerationsClosure->getAllDescendants($row->enumerationId,1);
				foreach ($attendings as $attending) {
					$teamMember = new self();
					$teamMember->teamMemberId = (int)$attending->ormId;
					$teamMember->populate();
					$ret = $teamMember->personId;
					break 2;
				}
			}
		}
		return $ret;
	}

}
