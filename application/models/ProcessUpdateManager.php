<?php
/*****************************************************************************
*       ProcessUpdateManager.php
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


class ProcessUpdateManager extends ProcessAbstract {

	/**
	 * Process condition and do action
	 * @param Handler $handler Handler ORM
	 * @param Audit $audit Audit ORM
	 * @return boolean Return TRUE if successful, FALSE otherwise
	 */
	public function process(Audit $audit) {
		$ret = true;
		$update = new UpdateFile();
		$updateIterator = $update->getIteratorByQueue();
		foreach ($updateIterator as $updateFile) {
			$updateFile->queue = 0;
			$alterTable = new AlterTable();
			$ret = $alterTable->generateSqlChanges($updateFile->getUploadFilename());
			if ($ret === true) {
				$alterTable->executeSqlChanges();
				//$updateFile->active = 0;
				$updateFile->status = 'Completed';
				$updateFile->persist();
			}
			else {
				$updateFile->status = 'Error: '.$ret;
				$updateFile->persist();
			}
		}
		return $ret;
	}

}
