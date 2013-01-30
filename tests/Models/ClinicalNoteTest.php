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

class Models_ClinicalNoteTest extends Models_ClinicalNoteAbstract {

	protected $_keyValues = array('personId'=>123,
				      'visitId'=>456,
				      'clinicalNoteDefinitionId'=>789,
				      'dateTime'=>'2009-09-23',
				      'authoringPersonId'=>1011,
				      'consultationId'=>1213,
				      'locationId'=>1415,
				      'eSignatureId'=>1617,);
	protected $_assertMatches = array('personId'=>123);
	protected $_assertTableName = 'clinicalNotes'; // value MUST be the same as $_table

	public function testAddNote() {
		$personId = 1000;
		$authoringPersonId = 65650;
		$dateTime = date('Y-m-d h:i:s');
		$note = new ClinicalNote();
		$note->personId = $personId;
		$note->dateTime = $dateTime;
		$note->authoringPersonId = $authoringPersonId;
		$note->clinicalNoteDefinitionId = $this->_objects['noteDefinition']->clinicalNoteDefinitionId;
		$note->persist();
		$noteId = $note->clinicalNoteId;

		$note = new ClinicalNote();
		$note->clinicalNoteId = $noteId;
		$note->populate();

		$this->assertEquals($personId,$note->personId);
		$this->assertEquals($authoringPersonId,$note->authoringPersonId);
		$this->assertEquals($dateTime,$note->dateTime);
	}

}

