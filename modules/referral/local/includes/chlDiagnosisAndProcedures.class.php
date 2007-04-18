<?php

/**
 * Returns a display of daignosis and procedures forms for CHLCare 
 *
 * This is a copy of pg=12 of visit_info.php.  There's so many things going on 
 * in that block, for timeliness-sake, I've opted to copy it into the code
 * rather than try understand it and write a new, better version.  As soon as
 * time/budget allows, this needs to be refractored.
 *
 * @todo Refractor this into some sort of sensical code
 */ 
class chlDiagnosisAndProcedures
{
	var $_db = null;
	var $GET = null;
	var $visitId = '';
	
	function chlDiagnosisAndProcedures() {
		
		$this->GET =& new clniFilter('_GET');
		$this->_db =& new clniDB();
	}
	
	/**
	 * Imported from {@link Codes} in CHLCare
	 *
	 * @access private
	 */
	function _getICD9CatsList($clinic_id = null) {
		$sql  = sprintf('
			SELECT SQL_CACHE 
				DISTINCT description,
				icd9_cat_id,
				clinic_id 
			FROM
				' . chlUtility::chlCareTable('icd9_categories') . '
			%s
			ORDER BY clinic_id ASC',
			!is_null($clinic_id) ? 'WHERE clinic_id IN(' . (int)$clinic_id .  ',0,1)' : '');
		
		return array(); //$this->_db->execute($sql);
	}
	
	
	/**
	 * Imported from {@link Visits} in CHLCare
	 *
	 * @access private
	 */
	function _getProblemsProcedures($visitId, $patientId) { 
		$sql = sprintf('
			SELECT SQL_CACHE
				diagnoses,
				subjective,
				objective,
				assessment,plan,
				procedures 
			FROM
				' . chlUtility::chlCareTable('visits_diagnosis') . ' 
			 WHERE
			 	%s
				patient_id = %d',
			!is_null($visitId) ? 'visit_id = ' . (int)$visitId . ' AND ' : '',
			$patientId);
		
		$result = $this->_db->execute($sql);
		
		if ($result->recordCount() > 1) {
			while($result && !$result->EOF) {
				$row = $result->fields;
				$result->moveNext();
				if(
					$row['diagnoses'] = "" && 
					$row['procedures'] = "" &&
					$row['subjective'] = "" &&
					$row['objective'] = "" &&
					$row['assessment'] = "" &&
					$row['plan'] = "") 
				{
					continue;
				}
			}
		}
		else {
			$row = $result->fields;
		}
		
/* 
// hiding until proven necessary
 		$GLOBALS["hidden_fields"]	=	 Formicater::Formicate("subjective",$row['subjective'],"hidden","",'','','','',''," ")
			                            .Formicater::Formicate("objective",$row['objective'],"hidden","",'','','','',''," ")
			                            .Formicater::Formicate("assessment",$row['assessment'],"hidden","",'','','','',''," ")
			                            .Formicater::Formicate("plan",$row['plan'],"hidden","",'','','','',''," ");
*/										
		$diagInfo 					= array("icd9_list" => $row['diagnoses'], "cpt_list" => $row['procedures'], "subjective" =>$row['subjective'], "objective"=>$row['objective'],"assessment"=>$row['assessment'],"plan"=>$row['plan'],"row_count"=>$result->recordCount());
		return $diagInfo;
	}

	
	/**
	 * Handles displaying the input form
	 *
	 * {@internal This is almost a direct copy from visit_info.php.  The only
	 *    changes that have been made relate to the database and some various
	 *    dependencies on CHLCare code.}
	 */
	function display() {
		// var initialization
		$out = '';
		$js = '';
		$ill_cat_pdown = '';
		
		// This code only applies when you have a real visit_id - in this case we don't
		// all code that begings with "//*" falls under this assumption
		//$out .= "<input type=hidden name=visit_clinic_id value=".$vst_pvider_res['clinic_id']." >";
		
		//*****************************************************/
		// Illness Category Pdown 							   /
		//*****************************************************/
		$cats 				= $this->_getICD9CatsList((int)$_SESSION['sLoggedInClinicID']);
		$c_loc_string		= array_flip($_SESSION['all_clinic_ids']);
		$c_loc_string[0]	= $c_loc_string[1]	 = "All Clinics";
		while ($cats && !$cats->EOF) { 
			$c = $cats->fields;
			$clinic_icd9s[]	 					= $c['icd9_cat_id'];
			$icd9_cat_desc[$c['icd9_cat_id']]	= $c['description'];
			$ill_cat_pdown 	.= "<option value=\"".$c['icd9_cat_id']."\">".$c['description']." (".$c_loc_string[$c['clinic_id']].")</option>\n";
			$cats->moveNext();
		}
		
		$ill_cat_pdown 	= "	<select style=\"width:100%;\" class=inputBox7 name=category_id onChange=\"populateDropDowns(this.options[this.selectedIndex].value);\">\n<option value=\"0\"></option>\n$ill_cat_pdown\n</select>\n";					
		
		//*****************************************************/
		// Specific Illness Pdown  Prepopulation Javascript    /
		//*****************************************************/
		$sql 			= "SELECT SQL_CACHE ic.*,icc.icd9_cat_id FROM " . chlUtility::chlCareTable('icd9_code_category') . " as icc, " . chlUtility::chlCareTable('icd9_codes') . " as ic WHERE 1 AND icc.icd9_cat_id IN (".implode(",", $clinic_icd9s).") AND ic.icd9_id = icc.icd9_id ORDER BY icd9_cat_id";
		
		// echo $sql;
		
		//Duplicate Query...The 2nd is for the inner loop.  Copying the result set doesn't work...
		$clinic_cat_res  = $this->_db->execute($sql);
		$clinic_cat_res2 = $this->_db->execute($sql);
		$js .= "<script language=javascript>\n";
		$js .= "	icd9s = new Array();\n";
		
		//This Creates the objects for our disease categories
		$created_categories = array();
		while ($clinic_cat_res && !$clinic_cat_res->EOF) {
			$c = $clinic_cat_res->fields;
			if(!in_array($c['icd9_cat_id'],$created_categories)):
			
				$js .= "Category = new icd9_category(".$c['icd9_cat_id'].",'".addslashes($icd9_cat_desc[$c['icd9_cat_id']])."');\n";//$icd9_cat_desc[$c['icd9_cat_id']]
				$outer_cat_id	= $c['icd9_cat_id'];
				
				while ($clinic_cat_res2 && !$clinic_cat_res2->EOF) { 
					$x = $clinic_cat_res2->fields;
					$clinic_cat_res2->moveNext();
					
					if($outer_cat_id == $x['icd9_cat_id']) $js .= "Category.addICD9('".$x['icd9_number']."','".addslashes($x['icd9_description'])."');\n";
				}	
				
				$clinic_cat_res2->moveFirst();
				$js .= "icd9s[".$c['icd9_cat_id']."] 	= Category;\n";
				$created_categories[] = $c['icd9_cat_id'];
				
			endif;
			$clinic_cat_res->moveNext();
		}			
		$js 	.= "</script>\n";
		$out 	.= $js;
		
		//*****************************************************/
		// Illness Category Pdown 							   /
		//*****************************************************/
		$icd9_pdown = "<select name=illness_category class='inputBox7' onChange=\"document.maiden.previous_illness_1.value=this.options[this.selectedIndex].text;document.maiden.new_icd9.value=this.options[this.selectedIndex].value;\" > <option value=\"\"> -- </option> </select>\n";
		
		//More ICD9 Links
		//$more_icds 	= "<a href='#' onclick=\"javascript:window.open('index.php?template=manage/manage_codes_icd9_chooser.php&code_mode=previous_illness2','window_name','width=475,height=400,status=yes,scrollbars=yes')\" class=inner_link>Click here for more ICD9 options</a>\n";
		$more_icds	 = "<a href=\"javascript:popwindow('" . $GLOBALS['config']['chlcare_base'] . "index.php?template=manage/manage_codes_icd9_chooser.php&code_mode=previous_illness2',475,400);\" class=inner_link >Click here for more ICD9 options</a>";
		
		//*****************************************************/
		// Specific Illness Table - Pulldowns 				   /
		//*****************************************************/
		$dia_pt	 = "<table style=\"border:1px solid black;height:60px;\" width='400'>";
		$dia_pt	.= "	<tr><td valign=top  align=right width=50%>$ill_cat_pdown </td></tr>";
		$dia_pt .= "	<tr><td valign=top align=left>$icd9_pdown </td></tr>";
		$dia_pt	.= "</table>";			
		
		
		//*****************************************************/
		// Specific Illness Table - Existing Condition Lists	  /
		//*****************************************************/
		$odiags	 	 = $this->_getProblemsProcedures(null, $this->GET->getTyped('patient_id', 'int'));
		$diags	 	 = @explode(":|:",$odiags['icd9_list']);
		if($diags[0] == "") array_shift($diags);
		$dia_lt	  	 = "<table style=\"border:1px solid black;\" width='400' border=0>\n";
		$dia_lt		.= "	<input type=hidden name=new_icd9 value=\"\">\n";
		$dia_lt		.= "	<input type=hidden name=action 	value=\"\">\n";
		$dia_lt		.= "	<input type=hidden name=icd9_to_delete 	value=\"\">\n";
		$dia_lt		.= "	<tr><td colspan=2><B>Diagnosis/ICD9</B></td></tr>";
		$dia_lt		.= "	<tr><td align=left><input id='previous_illness_1' type=text value=\"\" class='inputBox' name=\"previous_illness_1\"  style=\"width:100%;\"></td><td align=left>&nbsp;</td>\n";
		// Add save button
		$dia_lt     .= '    <td><input type="button" value="Add" onClick="addDiagnosis(\'previous_illness_1\', \'' . $this->visitId . '\')" class="inputBox3" onMouseOver="button_roll(this,\'on\');" onMouseOut="button_roll(this,\'off\');"></td></tr>';
		foreach($diags as $key=>$val)
		{	//These are existing icds
			if(trim($val)=="") continue;
			$icd_code	 = trim(str_replace("(","", str_replace(")","",substr($val, strpos($val,"(")))));
			if($show_save_buttons == 1)	$delte_but	 = "<input type='button' value='delete' onClick=\"if(confirm('Are you sure you want to delete this from the patients record?')) {this.form.action.value='delete_icd9';this.form.icd9_to_delete.value=".($icd_code!=""?"'".$icd_code."'":"''").";this.form.submit();};\" class='inputBox3' onMouseOver=\"button_roll(this,'on');\" onMouseOut=\"button_roll(this,'off');\">";		
			$dia_lt		.= "<tr><td> <input type=\"text\" value=\"$val\" class=\"inputBox\" style=\"width:100%;\"></td><td>$delte_but</td></tr>";
//				print $val;
		}
		
		$dia_lt		.= '<tr><td><div class="innerHTMLExists" id="selectedDiagnosis"></div><div id="diagnosisStatus"></div></td></tr>';
		$dia_lt		.= "</table>";
		$dia_lt     .= '<script type="text/javascript">loadDiagnosisGrid("' . $this->visitId . '");</script>';

		//*****************************************************/
		// Proc Drops										   /
		//*****************************************************/
		$js				= "onChange=\"document.maiden.previous_procedure_1.value=this.options[this.selectedIndex].text;document.maiden.fee_1.value=this.options[this.selectedIndex].value;\" ";
		
		$sql 			= "
			SELECT SQL_CACHE
				*
			FROM
				" . chlUtility::chlCareTable('cpt_codes') . " 
			WHERE 
				category <> '' AND
				marked_as_deleted <> 1
			ORDER BY cpt_id ASC ";
		$cat_res		= $this->_db->execute($sql);
		$sql		 	= "
			SELECT SQL_CACHE
				ccc.*, 
				cc.cpt_description,
				cc.cpt_number 
			FROM
				" . chlUtility::chlCareTable('cpt_code_category') . " AS ccc,
				" . chlUtility::chlCareTable('cpt_codes') . " AS cc 
			WHERE" .
				//ccc.clinic_id = '".$vst_pvider_res['clinic_id']."' AND -- pulled because vst_pvider_res isn't set
			"	ccc.clinic_id = '" . $_SESSION['sLoggedInClinicID'] . "' AND
				ccc.cpt_id = cc.cpt_id";
		$cpt_cat_res 	= $this->_db->execute($sql);
		
		$ev_pdown 		= "<select name=exam 		class='inputBox7' 	$js 	style=\"width:100%;\"><option value=\"\"> Exam Visit </option>\n";
		$rt_pdown 		= "<select name=routine 	class='inputBox7' 	$js 	style=\"width:100%;\"><option value=\"\"> Routine Physical </option>\n";
		$lb_pdown 		= "<select name=lab 		class='inputBox7' 	$js 	style=\"width:100%;\"><option value=\"\"> Labs </option>\n";
		$cl_pdown 		= "<select name=clinic		class='inputBox7' 	$js 	style=\"width:100%;\"><option value=\"\"> Clinic CPTs </option>\n";
		$in_pdown 		= "<select name=injection 	class='inputBox7' 	$js		style=\"width:100%;\"><option value=\"\"> Injections </option>\n";

		while($cat_res && !$cat_res->EOF) {
			$r = $cat_res->fields;
			$cpt_array[$r['cpt_id']] = $r;
			$cat_res->moveNext();
		}
		
		while($cpt_cat_res && !$cpt_cat_res->EOF) {
			$r = $cpt_cat_res->fields;
			$cpt_array[$r['cpt_id']] = $r;
			$cpt_cat_res->moveNext();
		}
		
		while(list($k,$r)=each($cpt_array) )
		{
			// supress undefined index error
			if (!isset($r['fee'])) {
				$r['fee'] = '';
			}

			switch($r['category'])
			{
				case "Exam/Visit":		$ev_pdown .=" <option value=\"$".$r['fee']."\"> $r[cpt_description] ($r[cpt_number]) </option>\n";			break;
				case "RT/Physicals":	$rt_pdown .= "<option value=\"$".$r['fee']."\"> $r[cpt_description] ($r[cpt_number]) </option>\n";			break;
				case "Labs":			$lb_pdown .= "<option value=\"$".$r['fee']."\"> $r[cpt_description] ($r[cpt_number]) </option>\n";			break;
				case "Special/TLC":		$cl_pdown .= "<option value=\"$".$r['fee']."\"> $r[cpt_description] ($r[cpt_number]) </option>\n";			break;
				case "Injections":		$in_pdown .= "<option value=\"$".$r['fee']."\"> $r[cpt_description] ($r[cpt_number]) </option>\n";			break;					
			}	
			
		}	
		
		$ev_pdown 	.= "</select>\n";$rt_pdown 	.= "</select>\n";$lb_pdown 	.= "</select>\n";$cl_pdown 	.= "</select>\n";$in_pdown 	.= "</select>\n";
		$more_cpts	 = "<a href=\"javascript:popwindow('" . $GLOBALS['config']['chlcare_base'] . "index.php?template=manage/manage_codes_cpt_chooser.php&mode=newprocs',475,400);\" class=inner_link > More CPT Codes</a>";
		
		//*****************************************************/
		// Proc Table - Pulldowns  							   /
		//*****************************************************/
		$prc_pt	 = "<table style=\"border:1px solid black;height:60px;\" width='400'   border=0  cellspacing=0 cellpadding=0><tr><td valign=top>";
		$prc_pt	.= "	<table width='400' >";
		$prc_pt	.= "		<tr><td>$ev_pdown</td><td>$rt_pdown</td></tr>\n";
		$prc_pt .= "		<tr><td>$in_pdown</td><td>$cl_pdown</td></tr>\n";
		$prc_pt .= "		<tr><td colspan='2'>$lb_pdown </td></tr>\n";
		$prc_pt	.= "	</table>";	
		$prc_pt	.= "</table>";	
		
		//*****************************************************/
		// Proc Table - List Existing Procs					   /
		//*****************************************************/	
		$procs	 	 = @explode(":|:",$odiags['cpt_list']);
		if($procs[0] == "") array_shift($procs);
		
		
		$prc_lt	 = "<table style=\"border:1px solid black;\" width='400' border=0>";
		$prc_lt	.= "	<input type=hidden name=cpt_to_delete 	value=\"\">\n";
		$prc_lt	.= "	<tr><td><B>Procedures/CPT</B></td>
							<td><B>Fee</B></td>
							<td>&nbsp;</td>
						</tr>";		
		$prc_lt	.= "	<tr><td align=left><input id='procedure_text' type=text value=\"\" class='inputBox' name=\"previous_procedure_1\"  style=\"width:100%;\"></td>
							<td align=left><input id='procedure_fee' type=text value=\"\" class='inputBox' name=\"fee_1\"  style=\"width:100%;\"></td>
							<td>&nbsp;</td>";
		// Add save button
		$prc_lt     .= '    <td><input type="button" value="Add" onClick="addProcedure(\'procedure\', \'' . $this->visitId . '\')" class="inputBox3" onMouseOver="button_roll(this,\'on\');" onMouseOut="button_roll(this,\'off\');"></td></tr>';
		
/* 		foreach($procs as $key=>$val)
		{
			if(trim($val)=="") continue;
			$cpt_code_fee	= split(" ",str_replace("(","", str_replace(")","",substr($val, strpos($val,"(")))));
			$cpt_fee		= trim($cpt_code_fee[0]);
			$cpt_code		= trim($cpt_code_fee[1]);
			$cpt_desc 		= substr($val, 0, strpos($val,"("));
			if($show_save_buttons == 1)	$delte_but	 	= "<input type='button' value='delete' onClick=\"if(confirm('Are you sure you want to delete this from the patients record?')) {this.form.action.value='delete_cpt';this.form.cpt_to_delete.value=".($cpt_code!=""? "'".$cpt_code."'":"''").";this.form.submit();};\" class='inputBox3' onMouseOver=\"button_roll(this,'on');\" onMouseOut=\"button_roll(this,'off');\">";		

			$prc_lt	.= "<tr><td><input type=\"text\" value=\"$cpt_desc ($cpt_code)\" class=\"inputBox\" style=\"width:100%;\"></td>
							<td><input type=\"text\" value=\"$cpt_fee\" class=\"inputBox\" style=\"width:100%;\"></td>
							<td>$delte_but</td>
						</tr>";
		}
 */		
		$prc_lt .= '<tr><td colspan="3"><div class="innerHTMLExists" id="selectedProcedures"></div><div id="procedureStatus"></div></td></tr>';
		$prc_lt	.= "</table>";
		$prc_lt .= '<script type="text/javascript">loadProcedureGrid("' . $this->visitId . '");</script>';
		
		//*****************************************************/
		// Put it all together								   /
		//*****************************************************/
		// Add JS
		include_once dirname(__FILE__) . '/chlDiagnosisAndProceduresJS.php';
		$out    .= chlDiagnosisAndProceduresJS();
		$out 	.= "<table border=0>";
		$out	.= "	<tr><td width='48%'><B>Select Diagnosis</B><br>$more_icds</td></tr>";
		$out    .= "    <tr><td>$dia_pt</td></tr>";
		$out    .= "    <tr><td valign=top>$dia_lt</td></tr>";
		$out    .= "    <tr><td width='48%'><B>Select Procedures</B><br>$more_cpts</td></tr>";
		$out	.= "	<tr><td>$prc_pt</td></tr>";
		$out	.= "	<tr><td valign=top>$prc_lt</td></tr>";//
		$out	.= "</table>";
		
		$out .= '<script type="text/javascript">Behavior.apply();</script>';
		return $out;
	}
}
