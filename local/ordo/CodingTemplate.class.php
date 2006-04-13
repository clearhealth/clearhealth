<?php
/**
 * Object Relational Persistence Mapping Class for table: coding_template
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class CodingTemplate extends ORDataObject {

	/**#@+
	 * Fields of table: coding_template mapped to class members
	 */
	var $coding_template_id		= '';
	var $practice_id 			= '';
	var $reason_id				= '';
	var $title		= '';
	var $coding_parent_id		= '';
	/**#@-*/

	var $codes = null;
	/**
	 * DB Table
	 */
	var $_table = 'coding_template';

	/**
	 * Primary Key
	 */
	var $_key = 'coding_template_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'CodingTemplate';

	/**
	 * Handle instantiation
	 */
	function CodingTemplate() {
		parent::ORDataObject();
	}

	function drop() {
		$code =& Celini::newORDO('CodingData',$this->get('coding_parent_id'));
		$code->drop();
		parent::drop();
	}
	
	function valueList($id='all',$reason_id=0) {
		if(is_numeric($id)) {
			$out = $this->getForPractice($id,$reason_id);
		}
		elseif($id=='all') {
			$p =& Celini::newORDO('Practice');
			$out = array();
			$plist = $p->valueList('name');
			foreach($plist as $pid=>$pname) {
				$out[$pname] = array('id'=>$pid,'templates'=>$this->getForPractice($pid));
			}
		}
		else {
			$out = parent::valueList($id);
		}
		return $out;
	}
	
	function getForPractice($practice_id,$reason_id = 0) {
		$db =& Celini::dbInstance();
		if($reason_id > 0) {
			$sql = 'SELECT coding_template_id,title FROM '.$this->_table.' WHERE practice_id='.$db->quote($practice_id).' AND reason_id='.$db->quote($reason_id).' ORDER BY title ASC';
		} else {
			$sql = 'SELECT coding_template_id,title FROM '.$this->_table.' WHERE practice_id='.$db->quote($practice_id).' ORDER BY title ASC';
		}
		$res = $db->execute($sql);
		$out = array();
		while($res && !$res->EOF) {
			$out[$res->fields['coding_template_id']] = $res->fields['title'];
			$res->MoveNext();
		}
		return $out;
	}
	
}
?>