<?php
/*****************************************************************************
*       ProblemListTest.php
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
 * ProblemList
 */
require_once 'ProblemList.php';

class Models_ProblemListTest extends Models_TableModels {

	protected $_keyValues = array('code'=>'CODE',
				      'codeTextShort'=>'Test codeTextShort',
				      'dateOfOnset'=>'2009-09-23',
				      'personId'=>1234,);
	protected $_assertMatches = array('personId'=>1234);
	protected $_assertTableName = 'problemLists'; // value MUST be the same as $_table

	public function testProblemList() {

		$date = date('Y-m-d H:i:s');
		$comments = array();
		$comments[] = 'first comment';
		$comments[] = 'yet another comment';
		$problemInput = 'DMII KETOACD UNCONTROLD  (250.12)';
		$problemList = array();
		$problemList['code'] = '250.12';
		$problemList['dateOfOnset'] = $date;
		$problemList['immediacy'] = 'Chronic';
		$problemList['personId'] = '65650';
		$problemList['providerId'] = '1000172';
		$problemList['status'] = 'Active';
		$problemList['lastUpdated'] = $date;

		$problem = new ProblemList();
		$problemListComments = array();
		$tmpComment = array();
		$tmpComment['authorId'] = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$tmpComment['date'] = $date;
		foreach ($comments as $comment) {
			$tmpComment['comment'] = $comment;
			$problemListComments[] = $tmpComment;
		}
		$problem->setProblemListComments($problemListComments);
		$problem->populateWithArray($problemList);
		$problem->persist();
		// retrieve problem list id
		$problemListId = $problem->problemListId;

		// check if problem list successfully saved
		$problem = new ProblemList();
		$problem->problemListId = $problemListId;
		$problem->populate();

		// assert individual data
		foreach ($problemList as $fieldName=>$value) {
			$this->assertEquals($problem->$fieldName,$value,"$fieldName not equal.");
		}
		// assert if comments contains the same number of comments
		$this->assertEquals(count($problem->problemListComments),count($comments),"Problem List Comments does not match.");

		// remove problem list by changing the status to Removed
		$status = 'Removed';
		$problem->status = $status;
		$problem->persist();

		$problem = new ProblemList();
		$problem->problemListId = $problemListId;
		$problem->populate();

		$this->assertEquals($problem->status,$status,"Status not equal.");
	}

}

