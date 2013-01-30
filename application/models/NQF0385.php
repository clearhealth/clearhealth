<?php
/*****************************************************************************
*       NQF0385.php
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


class NQF0385 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0385-pqri72: CMS - 19
	 * Title: Oncology Colon Cancer: Chemotherapy for Stage III Colon Cancer Patients
	 * Description: Percentage of patients aged 18 years and older with Stage IIIA through IIIC colon cancer who are referred for adjuvant chemotherapy, prescribed adjuvant chemotherapy, or have previously received adjuvant chemotherapy within the 12-month reporting period.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
