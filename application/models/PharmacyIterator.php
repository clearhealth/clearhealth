<?php
/*****************************************************************************
*       PharmacyIterator.php
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


class PharmacyIterator extends WebVista_Model_ORMIterator implements Iterator {

    public function __construct($dbSelect = null) {
	$db = Zend_Registry::get('dbAdapter');
        $dbSelect = $db->select()
		->from('pharmacies', array('pharmacies.pharmacyId',
				'pharmacies.StoreName',
				'pharmacies.AddressLine1',
				'pharmacies.City',
				'pharmacies.ServiceLevel'))
		->join(array('zipcodeGeo2' => 'zipcodeGeo'),"SUBSTRING(pharmacies.Zip,1,5) = zipcodeGeo2.zip",null)
		->joinLeft('patient','patient.defaultPharmacyId = pharmacies.pharmacyId')
		->where('pharmacies.ActiveStartTime <= NOW()')
		->where('DATE_ADD(pharmacies.ActiveEndTime, INTERVAL 30 DAY) >= NOW()')
		->order('pharmacies.StoreName')
		->group('pharmacies.pharmacyId');
        parent::__construct("Pharmacy",$dbSelect);
    }
	function setFilters(array $filters) {
		foreach ($filters as $filter => $value) {
			switch ($filter) {
				case 'preferred':
					$this->_dbSelect->where('pharmacies.preferred = 1');
					break;
				case 'distance':
					$this->_dbSelect->join(array('zipcodeGeo1' => 'zipcodeGeo'),null);
					$this->_dbSelect->where('ROUND( SQRT( POW( ( 69.1 * ( zipcodeGeo1.geo_lat - zipcodeGeo2.geo_lat ) ) , 2 ) + POW( ( 53 * ( zipcodeGeo1.geo_lon - zipcodeGeo2.geo_lon ) ) , 2 ) ) , 1 ) <=' . (int)$value); 
					break;
				case 'zip':
					$this->_dbSelect->where("zipcodeGeo1.zip = '" . substr((int)$value,0,5) . "'");
					break;
				break;
			}
		}
		//trigger_error($this->_dbSelect->__toString(),E_USER_NOTICE);
	}
}
