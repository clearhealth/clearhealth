<?php
/*****************************************************************************
*       NQF0004.php
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


class NQF0004 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0004: CMS - 29
	 * Title: Initiation and Engagement of Alcohol and Other Drug Dependence Treatment: (a) Initiation, (b) Engagement
	 * Description: The percentage of adolescent and adult patients with a new episode of alcohol and other drug (AOD) dependence who initiate treatment through an inpatient AOD admission, outpatient visit, intensive outpatient encounter or partial hospitalization within 14 days of the diagnosis and who initiated treatment and who had two or more additional services with an AOD diagnosis within 30 days of the initiation visit.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
