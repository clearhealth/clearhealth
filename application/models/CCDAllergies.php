<?php
/*****************************************************************************
*       CCDAllergies.php
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


class CCDAllergies {

	public static function populate(CCD $base,SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.3.88.11.83.102');
		$templateId->addAttribute('assigningAuthorityName','HITSP/C83');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','1.3.6.1.4.1.19376.1.5.3.1.3.13');
		$templateId->addAttribute('assigningAuthorityName','IHE PCC');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.2');
		$templateId->addAttribute('assigningAuthorityName','HL7 CCD');
		// <!--Allergies/Reactions section template-->
		$code = $section->addChild('code');
		$code->addAttribute('code','48765-2');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$code->addAttribute('codeSystemName','LOINC');
		$code->addAttribute('displayName','Allergies');
		$section->addChild('title','Allergies and Adverse Reactions');

		$enumeration = new Enumeration();
		$listSymptoms = array();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SYMPTOM_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enum) {
			$listSymptoms[$enum->key] = $enum->name;
		}

		$filters = array('patientId'=>$base->patient->personId);
		$base->setFiltersDateRange($filters);
		$rows = array();
		$allergies = PatientAllergy::listMedicationAllergies($filters);
		foreach ($allergies as $key=>$allergy) {
			$exp = explode(',',$allergy['symptoms']);
			$symptoms = array();
			foreach ($exp as $symp) {
				$symptoms[] = isset($listSymptoms[$symp])?$listSymptoms[$symp]:'';
			}
			$reactionType = $allergy['reactionType'];
			if (!strlen($reactionType) > 0) $reactionType = 'Unknown';
			$active = ((int)$allergy['active'])?'Active':'Inactive';
			$snomed = '';
			$row = array();
			$row['type'] = $reactionType;//'Drug Allergy';
			if ($reactionType == 'Specific Drug Allergy') $snomed = '416098002';
			$row['snomed'] = $snomed;
			$row['substance'] = html_convert_entities($allergy['causativeAgent']);
			$row['rxnorm'] = html_convert_entities($allergy['rxnorm_cuid']);
			$row['reaction'] = array('id'=>'ReactionID-'.$key,'value'=>html_convert_entities(implode(', ',$symptoms)));
			$row['date'] = date('M d, Y',strtotime($allergy['dateTimeReaction']));
			$row['status'] = html_convert_entities($active);
			$rows[] = $row;
		}
		/*
		-**SNOMED Allergy Type Code** (note from NIST: "The SNOMED Allergy Type Code is required by HITSP/C83, which is a component of the HITSP/C32 implementation guide specified by ONC in the Final Rule")
		-**Medication/Agent Allergy** (including medication/agent allergy and associated RxNorm code)
		*/
		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$tr->addChild('th','Type');
			$tr->addChild('th','Drug allergy SNOMED code');
			$tr->addChild('th','Substance');
			$tr->addChild('th','Substance RxNorm code');
			$tr->addChild('th','Reaction');
			$tr->addChild('th','Date Identified');
			$tr->addChild('th','Status');
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$tr->addChild('td',$row['type']);
				$tr->addChild('td',$row['snomed']);
				$tr->addChild('td',$row['substance']);
				$tr->addChild('td',$row['rxnorm']);
				$td = $tr->addChild('td',$row['reaction']['value']);
				$td->addAttribute('ID',$row['reaction']['id']);
				$tr->addChild('td',$row['date']);
				$tr->addChild('td',$row['status']);
			}
		}

		foreach ($allergies as $allergy) {
			$type = $allergy['reactionType'];
			if (!strlen($type) > 0) $type = 'Unknown';
			$substance = html_convert_entities($allergy['causativeAgent']);
			$exp = explode(',',$allergy['symptoms']);
			$symptoms = array();
			foreach ($exp as $symp) {
				$symptoms[] = isset($listSymptoms[$symp])?$listSymptoms[$symp]:'';
			}
			$reaction = '';
			if ($symptoms) {
				$reaction = html_convert_entities(implode(', ',$symptoms));
			}

			$status = 'Inactive';
			$statusCode = 'completed';
			$effectiveTimeHigh = '<high nullFlavor="UNK"/>';
			if ((int)$allergy['active']) {
				$status = 'Active';
				$statusCode = 'active';
				$effectiveTimeHigh = '';
			}
			$status = ((int)$allergy['active'])?'Active':'Inactive';
			// STATUS CODES: active, suspended, aborted, completed
			$statusCode = ((int)$allergy['active'])?'active':'completed';
			$entry = '<act classCode="ACT" moodCode="EVN">
				<templateId root="2.16.840.1.113883.3.88.11.83.6" assigningAuthorityName="HITSP C83"/>
				<templateId root="2.16.840.1.113883.10.20.1.27" assigningAuthorityName="CCD"/>
				<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5.1" assigningAuthorityName="IHE PCC"/>
				<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5.3" assigningAuthorityName="IHE PCC"/>
				<id root="'.NSDR::create_guid().'"/>
				<code nullFlavor="NA"/>
				<statusCode code="'.$statusCode.'"/>
				<effectiveTime>
					<low nullFlavor="UNK"/>'.$effectiveTimeHigh.'
				</effectiveTime>
				<entryRelationship typeCode="SUBJ" inversionInd="false">
					<observation classCode="OBS" moodCode="EVN">
						<templateId root="2.16.840.1.113883.10.20.1.18" assigningAuthorityName="CCD"/>
						<templateId root="2.16.840.1.113883.10.20.1.28" assigningAuthorityName="CCD"/>
						<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.5" assigningAuthorityName="IHE PCC"/>
						<templateId root="1.3.6.1.4.1.19376.1.5.3.1.4.6" assigningAuthorityName="IHE PCC"/>
						<id root="'.NSDR::create_guid().'"/>
						<code code="416098002" codeSystem="2.16.840.1.113883.6.96" displayName="drug allergy" codeSystemName="SNOMED CT" />
						<text>
							<reference value="PtrToValueInSectionText"/>
						</text>
						<statusCode code="completed"/>
						<effectiveTime>
							<low nullFlavor="UNK"/>
						</effectiveTime>
						<value xsi:type="CD"/>
						<participant typeCode="CSM">
							<participantRole classCode="MANU">
								<addr/>
								<telecom/>
								<playingEntity classCode="MMAT">
									<code code="70618" codeSystem="2.16.840.1.113883.6.88" displayName="'.$substance.'">
										<originalText>
											<reference value="PointrToSectionText"/>
										</originalText>
									</code>
									<name>'.$substance.'</name>
								</playingEntity>
							</participantRole>';
			if ($reaction != '' && false) {
				$entry .= '
							<entryRelationship typeCode="MFST" inversionInd="true">
								<observation classCode="OBS" moodCode="EVN">
									<templateId root="2.16.840.1.113883.10.20.1.54" assigningAuthorityName="CCD"/>
									<!--Reaction observation template -->
									<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>
									<text/>
									<statusCode code="completed"/>
									<value xsi:type="CD" code="247472004" codeSystem="2.16.840.1.113883.6.96" displayName="'.$reaction.'"/>
									<entryRelationship typeCode="SUBJ">
										<observation classCode="OBS" moodCode="EVN">
											<templateId root="2.16.840.1.113883.10.20.1.55" assigningAuthorityName="CCD"/>
											<code code="SEV" displayName="Severity" codeSystemName="HL7 ActCode" codeSystem="2.16.840.1.113883.5.4"/>
											<text>Required by HITSP C-83</text>
											<statusCode code="completed"/>
											<value xsi:type="CE" displayName="moderate" code="6736007" codeSystemName="SNOMED" codeSystem="2.16.840.1.113883.6.96"/>
										</observation>
									</entryRelationship>
								</observation>
							</entryRelationship>';
			}
			$entry .= '
							<!--<entryRelationship typeCode="REFR">
								<observation classCode="OBS" moodCode="EVN">
									<templateId root="2.16.840.1.113883.10.20.1.39"/>
									<code code="33999-4" codeSystem="2.16.840.1.113883.6.1" displayName="Status"/>
									<statusCode code="completed"/>
									<value xsi:type="CE" code="55561003" codeSystem="2.16.840.1.113883.6.96" displayName="'.$status.'"/>
								</observation>
							</entryRelationship>-->
						</participant>
					</observation>
				</entryRelationship>
			</act>';
			$entry = $section->addChild('entry',$entry);
			$entry->addAttribute('typeCode','DRIV');
		}
	}

}
