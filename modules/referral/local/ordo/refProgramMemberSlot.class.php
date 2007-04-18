<?php

require_once CELINI_ROOT . '/ordo/ORDataObject.class.php';

class refProgramMemberSlot extends ORDataObject
{
	var $refprogram_member_slot_id = '';
	var $month = '';
	var $year  = '';
	var $total = '';
	var $slots = '';
	
	var $used = '';
	
	var $refprogram_member_id = '';
	var $external_type        = '';
	var $external_id          = '';
	
	var $_table = 'refprogram_member_slot';

	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', (int)$id);
			$this->populate();
		}
	}
	
	/**
	 * @todo consider adding an ORDOHelper::populateFromFields() to handle 
	 *   populating from multiple criteria
	 */
	function setupExternalIDYearMonth($external_id, $year, $month, $refprogram_member_id) {
		$this->set('external_id', (int)$external_id);
		$this->set('year', (int)$year);
		$this->set('month', (int)$month);
		$this->set('refprogram_member_id', (int)$refprogram_member_id);
		
		$sql = '
			SELECT 
				*
			FROM 
				' . $this->_table . '
			WHERE 
				external_id = ' . $this->get('external_id') . ' AND
				year = ' . $this->get('year') . ' AND
				month = ' . $this->get('month') . ' AND
				refprogram_member_id = ' . $this->get('refprogram_member_id');
		$results = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this, $results);
	}

	function updateByExternalIDYearMonth($external_id, $year, $month, $refprogram_member_id, $value) {
		$this->setupExternalIDYearMonth($external_id, $year, $month, $refprogram_member_id);

		$this->set('slots',(int)$value);
		$this->persist();

		return $this->get('id');
	}
	
	function populate() {
		parent::populate('refprogram_member_slot_id');
	}
	
	function get_id() {
		return $this->get('refprogram_member_slot_id');
	}
	
	function set_id($value) {
		$this->set('refprogram_member_slot_id', $value);
	}
	
	function get_total() {
		if (empty($this->slots)) {
			//echo "grab defaults";
			switch ($this->external_type) {
				case 'Practice' :
					$sql = 'SELECT default_num_of_slots FROM refpractice WHERE refpractice_id = ' . (int)$this->get('external_id');
					break;
				case 'Provider' :
					$sql = '
						SELECT 
							prac.default_num_of_slots 
						FROM
							refpractice AS prac
							INNER JOIN refprovider as prov USING(refpractice_id)
						WHERE
							prov.refprovider_id = ' . (int)$this->get('external_id');
					break;
			}
			
			$slot = $this->_db->GetOne($sql);
			return $slot;
		}
		else {
			return $this->slots;
		}
	}
	
	function get_available() {
		return $this->get('total') - $this->get('used');
	}
	
	function get_used() {
		$sql = '
			SELECT
				COUNT(*) AS used_slots 
			FROM
				refRequest AS req
				JOIN refappointment AS appt USING(refappointment_id) 
			WHERE
				req.refStatus BETWEEN 3 AND 5 AND
				DATE_FORMAT(appt.date, "%Y-%c") = "' . (int)$this->get('year') . '-' . (int)$this->get('month') . '" AND 
				';
		switch ($this->external_type) {
			case 'Practice' :
				$sql .= 'appt.refpractice_id = ' . (int)$this->get('external_id');
				break;
			case 'Provider':
				$sql .= 'appt.refprovider_id = ' . (int)$this->get('external_id');
				break;
			default: 
				//var_dump($this->get('external_type'));
		}
		//var_dump(str_replace("\t", " ", $sql));
		$used = $this->_db->getOne($sql);
		return $used;
	}
	
	function get_external_type() {
		// check to see if this needs to be loaded from the refProgramMember
		if (empty($this->external_type)) {
			$programMember =& Celini::newORDO('refProgramMember', $this->get('refprogram_member_id'));
			$this->external_type = $programMember->get('external_type');
		}
		return $this->external_type;
	}
	
}

