<?php
/*****************************************************************************
*       CCDProblems.php
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


class CCDProblems {

	public static function populate(CCD $base,SimpleXMLELement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.3.88.11.83.103');
		$templateId->addAttribute('assigningAuthorityName','HITSP/C83');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','1.3.6.1.4.1.19376.1.5.3.1.3.6');
		$templateId->addAttribute('assigningAuthorityName','IHE PCC');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.11');
		$templateId->addAttribute('assigningAuthorityName','HL7 CCD');

		// <!-- Problem section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','11450-4');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$code->addAttribute('codeSystemName','LOINC');
		$code->addAttribute('displayName','Problem list');
		$section->addChild('title','Problems');

		$icd9Rows = array();
		$snomedRows = array();
		$rows = array(
			'ICD-9'=>array(),
			'SNOMED'=>array(),
		);
		foreach ($base->problemLists as $problem) {
			$code = html_convert_entities($problem->code);
			$row = array(
				'code'=>$code,
				'problem'=>html_convert_entities($problem->codeTextShort),
				'date'=>date('M d, Y',strtotime($problem->dateOfOnset)),
				'status'=>html_convert_entities($problem->status),
			);
			if (strpos($code,'.') !== false) {
				$rows['ICD-9'][] = $row;
			}
			else {
				$rows['SNOMED'][] = $row;
			}
		}

		$text = $section->addChild('text');
		if ($rows) {
			foreach ($rows as $key=>$values) {
				$table = $text->addChild('table');
				$thead = $table->addChild('thead');
				$tr = $thead->addChild('tr');
				$tr->addChild('th',$key.' Code');
				$tr->addChild('th','Problem');
				$tr->addChild('th','Date Diagnosed');
				$tr->addChild('th','Problem Status');
				$tbody = $table->addChild('tbody');
				foreach ($values as $row) {
					$tr = $tbody->addChild('tr');
					$tr->addChild('td',$row['code']);
					$tr->addChild('td',$row['problem']);
					$tr->addChild('td',$row['date']);
					$tr->addChild('td',$row['status']);
				}
			}
		}

		foreach ($base->problemLists as $problem) {
			$entry = '<act classCode="ACT" moodCode="EVN">
				<templateId root="2.16.840.1.113883.3.88.11.83.7" assigningAuthorityName="HITSP C83"/>
				<templateId root="2.16.840.1.113883.10.20.1.27" assigningAuthorityName="CCD"/>
				<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5.1" assigningAuthorityName="IHE PCC"/>
				<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5.2" assigningAuthorityName="IHE PCC"/>
				<!-- Problem act template -->
				<id root="'.NSDR::create_guid().'"/>
				<code nullFlavor="NA"/>
				<statusCode code="active"/>
				<effectiveTime>
					<low nullFlavor="UNK"/>
				</effectiveTime>
				<performer typeCode="PRF">
					<time>
						<low nullFlavor="UNK"/>
					</time>
					<assignedEntity>
						<id extension="PseudoMD-'.$problem->providerId.'" root="2.16.840.1.113883.3.72.5.2"/>
						<addr/>
						<telecom/>
					</assignedEntity>
				</performer>
				<entryRelationship typeCode="SUBJ" inversionInd="false">
					<observation classCode="OBS" moodCode="EVN">
						<templateId root="2.16.840.1.113883.10.20.1.28" assigningAuthorityName="CCD"/>
						<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5" assigningAuthorityName="IHE PCC"/>
						<!--Problem observation template -->
						<id root="'.NSDR::create_guid().'"/>
						<code displayName="Condition" code="64572001" codeSystemName="SNOMED-CT" codeSystem="2.16.840.1.113883.6.96"/>
						<text>
							<reference value="#CondID-'.$problem->providerId.'"/>
						</text>
						<statusCode code="completed"/>
						<effectiveTime>
							<low nullFlavor="UNK"/>
							<high nullFlavor="UNK"/>
						</effectiveTime>
						<value xsi:type="CD" displayName="'.html_convert_entities($problem->codeTextShort).'" code="233604007" codeSystemName="SNOMED" codeSystem="2.16.840.1.113883.6.96"/>
						<entryRelationship typeCode="REFR">
							<observation classCode="OBS" moodCode="EVN">
								<templateId root="2.16.840.1.113883.10.20.1.50"/>
								<!-- Problem status observation template -->
								<code code="33999-4" codeSystem="2.16.840.1.113883.6.1" displayName="Status"/>
								<statusCode code="completed"/>
								<value xsi:type="CE" code="413322009" codeSystem="2.16.840.1.113883.6.96" displayName="'.html_convert_entities($problem->status).'"/>
							</observation>
						</entryRelationship>
					</observation>
				</entryRelationship>
			</act>';
			$entry = $section->addChild('entry',$entry);
			$entry->addAttribute('typeCode','DRIV');
		}
	}

}
