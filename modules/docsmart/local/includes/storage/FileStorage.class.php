<?php

/**
 * Abstract class which implements interface 
 * for different storages
 *
 */
class FileStorage {

	/**
	 * Revision Id
	 *
	 * @var integer
	 */
	var $revisionId;
	
	/**
	 * Initialize local variables
	 *
	 * @param integer $revisionId
	 * @return FileStorage
	 */
	function FileStorage($revisionId) {
		$this->revisionId = $revisionId;
	}	
	
	/**
	 * Abstract method for getting file's content
	 *
	 * @return blob
	 */
	function getFile() { return false; }

	/**
	 * Abstract method for removing file from the storage
	 *
	 * @return bool
	 */
	function removeFile() { return false; }

	/**
	 * Abstract method for saving file to the storage
	 *
	 * @return bool
	 */
	function saveFile() { return false; }
	
	/**
	 * Copy file to new storage
	 *
	 * @param FileStorage $storage
	 * @return bool
	 */
	function copyFile($storage) { return false; }
	
}

?>