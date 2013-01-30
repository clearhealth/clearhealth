<?php
/*****************************************************************************
*       ClinicalNoteTest.php
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

/**
 * Models_TableModels
 */
require_once 'TableModels.php';
/**
 * Unit test for Clinical Note Model
 */

/**
 * ClinicalNote
 */
require_once 'ClinicalNote.php';

/**
 * ClinicalNoteDefinition
 */
require_once 'ClinicalNoteDefinition.php';

/**
 * ClinicalNoteTemplate
 */
require_once 'ClinicalNoteTemplate.php';

abstract class Models_ClinicalNoteAbstract extends Models_TableModels {

	public function setUp() {
		parent::setUp();

		$user = new User();
		$user->username = TEST_LOGIN_USERNAME;
		$user->populateWithUsername();
		Zend_Auth::getInstance()->getStorage()->write($user);

		$clinicalNoteTemplate = new ClinicalNoteTemplate();
		$clinicalNoteTemplate->name = 'Test Note';
		$clinicalNoteTemplate->template = "<progressNoteTemplate>\r\n	<question label=\"Are you in pain?\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.inPain\" dbValue=\"tinyint\" label=\"y/n\">\r\n		</dataPoint>\r\n	</question>\r\n	<question label=\"Describe the pain:\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.burning\" dbValue=\"tinyint\" label=\"Burning\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.cramping\" dbValue=\"tinyint\" label=\"Cramping\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.penetrating\" dbValue=\"tinyint\" label=\"Penetrating\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.colic\" dbValue=\"tinyint\" label=\"Colic\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.oppressive\" dbValue=\"tinyint\" label=\"Oppressive\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.shooting\" dbValue=\"tinyint\" label=\"Shooting\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.sharp\" dbValue=\"tinyint\" label=\"Sharp\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.other\" dbValue=\"tinyint\" label=\"Other\"/>\r\n	</question>\r\n	<question label=\"Duration of pain:\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.constant\" dbValue=\"tinyint\" label=\"Constant\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.intermittent\" dbValue=\"tinyint\" label=\"Intermittent\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.home\" dbValue=\"tinyint\" label=\"Home\"/>\r\n	</question>\r\n	<question label=\"What relieves the pain?\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.relief.sleep\" dbValue=\"tinyint\" label=\"Sleep\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.relief.heat\" dbValue=\"tinyint\" label=\"Heat\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.relief.cold\" dbValue=\"tinyint\" label=\"Cold\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.description.relief.medicine\" dbValue=\"tinyint\" label=\"Medicine\"/>\r\n	</question>\r\n	<question label=\"Problem Sleeping?\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.sleep.problemSleeping\" dbValue=\"tinyint\" label=\"y/n\"/>\r\n	</question>\r\n	<question label=\"Is pain now controlled?\">\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.nowControlled\" dbValue=\"tinyint\" label=\"y/n\"/>\r\n		<dataPoint type=\"text\" namespace=\"assessment.pain.comment\" dbValue=\"varchar:255\" label=\"Pain Comment:\"/>\r\n	</question>\r\n	<question label=\"Is patient able to communicate?\">\r\n		<heading>Verbal:</heading>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.verbal.positive\" dbValue=\"tinyint\" label=\"Positive\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.verbal.plaintativeWhining\" dbValue=\"tinyint\" label=\"Plaintative/Whining\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.verbal.weeping\" dbValue=\"tinyint\" label=\"Weeping\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.verbal.screaming\" dbValue=\"tinyint\" label=\"Screaming\"/>\r\n		<heading>Body Movements:</heading>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.bodyMovement.easeOfMovement\" dbValue=\"tinyint\" label=\"Ease of Movement\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.bodyMovement.neutral\" dbValue=\"tinyint\" label=\"Neutral\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.bodyMovement.tense\" dbValue=\"tinyint\" label=\"Tense\" />\r\n		<heading>Facial:</heading>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.facial.smiling\" dbValue=\"tinyint\" label=\"Smiling\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.facial.neutral\" dbValue=\"tinyint\" label=\"Neutral\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.facial.grin\" dbValue=\"tinyint\" label=\"Grin\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.communicate.facial.grittedTeeth\" dbValue=\"tinyint\" label=\"Gritted Teeth\"/>\r\n		<heading>Area of pain:</heading>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.areaNonPalpable\" dbValue=\"tinyint\" label=\"Non-palpable\"/>\r\n		<dataPoint type=\"checkbox\" namespace=\"assessment.pain.areaReagent\" dbValue=\"tinyint\" label=\"Reagent\"/>\r\n		<dataPoint type=\"text\" namespace=\"assessment.pain.areaIndicated\" dbValue=\"varchar:255\" label=\"Indicated:\"/>\r\n	</question>\r\n</progressNoteTemplate>";

		$clinicalNoteTemplate->persist();
		$this->_objects['noteTemplate'] = $clinicalNoteTemplate;

		$clinicalNoteDefinition = new ClinicalNoteDefinition();
		$clinicalNoteDefinition->title = 'Test Note Definition';
		$clinicalNoteDefinition->clinicalNoteTemplateId = $clinicalNoteTemplate->clinicalNoteTemplateId;
		$clinicalNoteDefinition->active = 1;
		$clinicalNoteDefinition->persist();
		$this->_objects['noteDefinition'] = $clinicalNoteDefinition;
	}

}

