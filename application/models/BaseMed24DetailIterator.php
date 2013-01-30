<?php
/*****************************************************************************
*       BaseMed24DetailIterator.php
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


class BaseMed24DetailIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'BaseMed24';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbName = Zend_Registry::get('config')->database->params->dbname;
		$dbSelect = $db->select(null)
			->from('chmed.basemed24')
			->joinLeft('chmed.basemed24labels', 'basemed24labels.pkey = basemed24.pkey',null)
			->columns(array("basemed24.pkey as id","if(chmed.basemed24labels.pkey IS NOT NULL OR formulary.labelId > 0 OR LENGTH(formulary.externalUrl) > 0, 1, 0) as hasLabel","if(formulary.qty > 0, CONCAT(CAST(formulary.qty AS CHAR),' ',chmed.basemed24.dose), CONCAT('1 ',chmed.basemed24.dose)) AS dose"));
		foreach ($filters as $filter => $value) {
			switch ($filter) {
				case 'pkey':
					$dbSelect->where("basemed24.pkey = ?", $value);
					break;
				case 'formulary':
					$dbSelect->joinLeft($dbName.'.formulary' . preg_replace('/[^a-zA-Z0-9]+/','',ucfirst($value)) . ' as formulary', 'formulary.fullNDC = basemed24.full_ndc');
					break;
				default:
					$dbSelect->where("false");
			}
		}

		//echo $dbSelect->__toString();exit;
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

	public function currentArray() {
                $row = $this->_dbStmt->fetch(PDO::FETCH_NUM,null,$this->_offset);
		if ($this->_offset == 0) {
			for ($i=0,$ctr=count($row);$i<$ctr;$i++) {
				$this->_columnMeta[$i] = $this->_dbStmt->getColumnMeta($i);
			}
		}
		$col = 0;
		$data = array();
		$extra = array();
		$ctr = count($this->_columnMeta);
		while ($col < $ctr) {
			if ($this->_columnMeta[$col]['table'] == 'basemed24') {
				$data[$this->_columnMeta[$col]['name']] = $row[$col];
			}
			else {
				$extra[$this->_columnMeta[$col]['table']][$this->_columnMeta[$col]['name']] = $row[$col];
			}
			$col++;
		}

		$data['formularySchedule'] = '';
		$data['formularyDose'] = '';
		$data['formularyRoute'] = '';
		$data['keywords'] = '';
		$data['print'] = '';
		$data['directions'] = '';
		$data['comments'] = '';

		$data['description'] = '';
		$data['prn'] = '';
		$data['quantityQualifier'] = '';
		$data['refills'] = '';
		$data['daysSupply'] = '';
		$data['substitution'] = '';
		if (isset($extra['formulary']) && strlen($extra['formulary']['fullNDC']) > 0) { // override basemed24 with formulary
			$formulary = $extra['formulary'];
			// override basemed values
			$data['directions'] = $formulary['directions'];
			$data['comments'] = $formulary['comments'];
			$data['formularySchedule'] = $formulary['schedule'];
			$data['price'] = $formulary['price'];
			$data['labelId'] = $formulary['labelId'];
			$data['externalUrl'] = $formulary['externalUrl'];
			$data['packsize'] = $formulary['qty'];
			$data['keywords'] = $formulary['keywords'];
			$data['vaclass'] = $formulary['vaclass'];
			$data['schedule'] = $formulary['deaSchedule'];
			$data['print'] = '';
			if ($formulary['print'] == 1) {
				$data['print'] = $formulary['print'];
			}
			$data['formularyDose'] = $formulary['dose'];
			$data['formularyRoute'] = $formulary['route'];

			$data['description'] = $formulary['description'];
			$data['prn'] = $formulary['prn'];
			$data['quantityQualifier'] = $formulary['quantityQualifier'];
			$data['refills'] = $formulary['refills'];
			$data['daysSupply'] = $formulary['daysSupply'];
			$data['substitution'] = $formulary['substitution'];
		}
		/*$ormObj = new $this->_ormClass();
		$ormObj->populateWithArray($data);
		return $ormObj;*/
		return $data;
        }

}
