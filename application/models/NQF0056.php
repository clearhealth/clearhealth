<?php
/*****************************************************************************
*       NQF0056.php
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


class NQF0056 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0056-pqri163: CMS - 24
	 * Title: Diabetes: Foot Exam
	 * Description: The percentage of patients aged 18 - 75 years with diabetes (type 1 or type 2) who had a foot exam (visual inspection, sensory exam with monofilament, or pulse exam).
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
