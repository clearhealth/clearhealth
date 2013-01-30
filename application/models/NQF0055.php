<?php
/*****************************************************************************
*       NQF0055.php
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


class NQF0055 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0055-pqri117: CMS - 22
	 * Title: Diabetes: Eye Exam
	 * Description: Percentage of patients 18 -75 years of age with diabetes (type 1 or type 2) who had a retinal or dilated eye exam or a negative retinal exam (no evidence of retinopathy) by an eye care professional.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
