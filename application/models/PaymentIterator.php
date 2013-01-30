<?php
/*****************************************************************************
*       PaymentIterator.php
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


class PaymentIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = false) {
		$this->_ormClass = 'Payment';
		if ($dbSelect !== null) $this->_dbSelect = $dbSelect;
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$ormClass = $this->_ormClass;
		$orm = new $ormClass();
		$sqlSelect = $db->select(null)
				->from($orm->_table)
				->order('timestamp DESC','payment_date DESC');
		foreach($filters as $key=>$value) {
			switch ($key) {
				case 'visitId':
					$sqlSelect->where('encounter_id = ?',(int)$value);
					break;
				case 'claimLineId':
				case 'claimFileId':
					$sqlSelect->where($key.' = ?',(int)$value);
					break;
				case 'personId':
					$sqlSelect->where('personId = ?',(int)$value);
					break;
				case 'unallocated':
				case '100%unallocated':
					if ($key == 'unallocated') $sqlSelect->where('(amount - allocated) > 0');
					else $sqlSelect->where('allocated = 0');
					$sqlSelect->where('encounter_id = 0')
						->where('appointmentId = 0');
					break;
				case 'unposted':
					$sqlSelect->where('(amount - allocated) > 0');
					break;
				case 'company':
					$sqlSelect->join('insurance_program','insurance_program.insurance_program_id = '.$orm->_table.'.payer_id',null)
						->join('company','company.company_id = insurance_program.company_id',null)
						->where('company.name = ?',$value);
					break;
				case 'paymentDate':
					$sqlSelect->where('payment_date = ?',$value);
					break;
			}
		}
		$this->_dbSelect = $sqlSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
