<?php
/*****************************************************************************
*       PatientStatistics.php
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


class PatientStatistics extends WebVista_Model_ORM {

	protected $person_id;
        protected $ethnicity;
	protected $race;
	protected $income;
        protected $language;
        protected $migrant_status;
        protected $registration_location;
	protected $sign_in_date;
	protected $monthly_income;
	protected $family_size;
	protected $education_level;
	protected $employment_status;

        protected $_primaryKeys = array('person_id');
        protected $_table = "patient_statistics";
        protected $_legacyORMNaming = true;

	function getDisplayEthnicity() {

	}

	public function getPatientStatisticsId() {
		return $this->person_id;
	}

}
