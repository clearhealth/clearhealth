<?php
/*****************************************************************************
*       BaseMed24.php
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


class BaseMed24 extends WebVista_Model_ORM {
    protected $pkey;
    protected $fda_id;
    protected $fda_app_id;
    protected $fda_app_prodnum;
    protected $fda_drugname;
    protected $ndc;
    protected $full_ndc;
    protected $hipaa_ndc;
    protected $tradename;
    protected $generic;
    protected $market_status;
    protected $schedule;
    protected $rxnorm_cuid;
    protected $rxnorm;
    protected $strength;
    protected $unit;
    protected $packsize;
    protected $packtype;
    protected $formulation;
    protected $equivalents;
    protected $dose;
    protected $route;
    protected $vaclass;
    protected $firm_name;
    protected $directions;
    protected $comments;
    protected $price;
    protected $hasLabel;
    protected $labelId;
    protected $externalUrl;
    protected $md5;
    protected $notice;

    protected $_table = "basemed24";
    protected $_primaryKeys = array("pkey");
    protected $_legacyORMNaming = true;

	function getBaseMed24Id() {
		return $this->pkey;
	}
	function setBaseMed24Id($id) {
		$this->pkey = $id;
	}
	function getNotice() {
		switch ($this->notice) {
			case 'S': return "SIGNIFICANT";
			case 'C': return "CRITICAL";
			default:
				return $this->notice;
		}
	}

	public function populateByHipaaNDC($hipaaNDC = null) {
		if ($hipaaNDC === null) {
			$hipaaNDC = $this->hipaa_ndc;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			->from('chmed.basemed24')
			->where('hipaa_ndc = ?',$hipaaNDC);
		$this->populateWithSql($dbSelect->__toString());
	}

	public function populateByDrugDescription($drugDescription) {
		$drugDescription .= '';
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			->from('chmed.basemed24')
			->where('concat(tradename,\' \',unit) = ?',$hipaaNDC);
		$this->populateWithSql($dbSelect->__toString());
	}

}
