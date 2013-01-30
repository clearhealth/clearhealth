<?php
/*****************************************************************************
*       NQF0012.php
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


class NQF0012 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0012: CMS - 30
	 * Title: Prenatal Care: Screening for Human Immunodeficiency Virus (HIV)
	 * Description: Percentage of patients, regardless of age, who gave birth during a 12-month period who were screened for HIV infection during the first or second prenatal care visit.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
