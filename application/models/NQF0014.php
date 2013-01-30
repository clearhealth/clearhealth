<?php
/*****************************************************************************
*       NQF0014.php
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


class NQF0014 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0014: CMS - 31
	 * Title: Prenatal Care: Anti-D Immune Globulin
	 * Description: Percentage of D (Rh) negative, unsensitized patients, regardless of age, who gave birth during a 12-month period who received anti-D immune globulin at 26-30 weeks gestation.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
