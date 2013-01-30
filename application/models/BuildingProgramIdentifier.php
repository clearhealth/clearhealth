<?php
/*****************************************************************************
*       BuildingProgramIdentifier.php
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


class BuildingProgramIdentifier extends WebVista_Model_ORM {

	protected $building_id;
	protected $building;
	protected $program_id;
	protected $identifier;
	protected $x12_sender_id;

	protected $_table = 'building_program_identifier';
	protected $_primaryKeys = array('building_id','program_id');
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->building = new Building();
		$this->building->_cascadePersist = $this->_cascadePersist;
	}

	public function getIteratorByProgramId($programId = null) {
		if ($programId === null) {
			$programId = $this->programId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('program_id = ?',(int)$programId);
		return $this->getIterator($dbSelect);
	}

	public function getBuildingProgramIdentifierId() {
		return $this->building_id;
	}

}
