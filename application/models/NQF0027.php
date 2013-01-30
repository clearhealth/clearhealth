<?php
/*****************************************************************************
*       NQF0027.php
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


class NQF0027 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0027-pqri115: CMS - 21
	 * Title: Smoking and Tobacco Use Cessation, Medical assistance: a. Advising Smokers and Tobacco Users to Quit, b. Discussing Smoking and Tobacco Use Cessation Medications, c. Discussing Smoking and Tobacco Use Cessation Strategies
	 * Description: Percentage of patients 18 years of age and older who were current smokers or tobacco users, who were seen by a practitioner during the measurement year and who received advice to quit smoking or tobacco use or whose practitioner recommended or discussed smoking or tobacco use cessation medications, methods or strategies.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
