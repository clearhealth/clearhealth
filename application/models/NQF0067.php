<?php
/*****************************************************************************
*       NQF0067.php
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


class NQF0067 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0067-pqri6: CMS - 9
	 * Title: Coronary Artery Disease (CAD): Oral Antiplatelet Therapy Prescribed for Patients with CAD
	 * Description: Percentage of patients aged 18 years and older with a diagnosis of CAD who were prescribed oral antiplatelet therapy.
	 */
	public function populate() {
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		return 0;
	}

}
