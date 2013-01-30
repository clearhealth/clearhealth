<?php
/*****************************************************************************
*       NQF0105.php
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


class NQF0105 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0105-pqri9: CMS - 11
	 * Title: Anti-depressant medication management: (a) Effective Acute Phase Treatment,(b)Effective Continuation Phase Treatment
	 * Description: The percentage of patients 18 years of age and older who were diagnosed with a new episode of major depression, treated with antidepressant medication, and who remained on an antidepressant medication treatment.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
