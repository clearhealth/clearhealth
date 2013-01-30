<?php
/*****************************************************************************
*       NQF0002.php
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


class NQF0002 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0002-pqri66: CMS - 17
	 * Title: Appropriate Testing for Children with Pharyngitis
	 * Description: Percentage of children 2-18 years of age who were diagnosed with pharyngitis, dispensed an antibiotic and received a group A streptococcus (strep) test for the episode.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
