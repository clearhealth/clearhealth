<?php

$loader->requireOnce('/includes/storage/FileStorageFS.class.php');
$loader->requireOnce('/includes/storage/FileStorageDB.class.php');

/**
 * This class is used for store specified revision's file to the storage
 *
 */
class RevisionStorage {

	/**
	 * Revision Id
	 *
	 * @var integer
	 */
	var $revisionId;
	
	/**
	 * Storage Type
	 *
	 * @var integer
	 */
	var $storageType;
	
	/**
	 * FileStorage
	 *
	 * @var string
	 */
	var $storage;
	
	/**
	 * Sets properties and initialize the storage
	 *
	 * @param integer $revisionId
	 * @param integer $storageType
	 * @return RevisionStorage
	 */
	function RevisionStorage($revisionId, $storageType) {
		$this->revisionId = $revisionId;
		$this->storageType = $storageType;
		$this->init();
	}
	
	/**
	 * Initialize storage by storage type
	 *
	 * @return boolean
	 */
	function init() {
		$this->storage = $this->getStorage($this->storageType);
	}
	
	/**
	 * Wrapper for getting file's content
	 *
	 * @return blob
	 */
	function getFile() {
		return $this->storage->getFile();
	}
	
	/**
	 * Wrapper for removing file from the storage 
	 *
	 * @return bool
	 */
	function removeFile() {
		return $this->storage->removeFile();
	}	

	/**
	 * Wrapper for saving file to the storage
	 *
	 * @param string $file
	 * @return bool
	 */
	function saveFile($file) {
		return $this->storage->saveFile($file);
	}		
	
	/**
	 * Copy file to the new storage
	 *
	 * @param string $storageType
	 */
	function copyFile($storageType) {
		$storage = $this->getStorage($storageType);
		return $this->storage->copyFile($storage);
	}
	
	/**
	 * Creat FileStorage object against specified storageType 
	 * and return it
	 *
	 * @param string $storageType
	 * @return FileStorage
	 */
	function getStorage($storageType) {
		switch($storageType) {
			case "FS":
				return new FileStorageFS($this->revisionId);
				break;
			case "DB":
				return new FileStorageDB($this->revisionId);
				break;				
			default:
				return false;
		}
	}
}

?>