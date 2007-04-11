<?php

$loader->requireOnce('/includes/storage/FileStorage.class.php');

/**
 * Special storage which is used for managing files in the database
 *
 */
class FileStorageDB extends FileStorage {

	/**
	 * Getting file's content from the databases
	 *
	 * @return bool
	 */
	function getFile() {
		$revisionDb =& Celini::newOrdo('RevisionDb', $this->revisionId);
		return $revisionDb->filedata;
	}

	/**
	 * Removing file from the database
	 *
	 * @return bool
	 */
	function removeFile() {
		$revisionDb =& Celini::newOrdo('RevisionDb', $this->revisionId);
		return $revisionDb->drop(true);
	}

	/**
	 * Saving file to the database
	 *
	 * @return bool
	 */
	function saveFile($file) {
		if(filesize($file) == 0) {
			return false;
		}
		$fh = fopen ($file, "rb"); 
		$filedata = fread ($fh, filesize($file)); 
		fclose ($fh); 
		$revisionDb =& Celini::newOrdo('RevisionDb');
		$revisionDb->populate_array( array(
			'filedata' => $filedata,
			'revision_id' => $this->revisionId ));
		if(!$revisionDb->persist()) {
			return 0;
		}
		return filesize($file);
	}

	/**
	 * Copy file from database to new storage
	 *
	 * @param FileStorage $storage
	 * @return bool
	 */
	function copyFile($storage) {
		$revisionDb =& Celini::newOrdo('RevisionDb', $this->revisionId);
		$file = tempnam(session_save_path(),"");
		$fh = fopen($file, "wb");
		fwrite($fh, $revisionDb->filedata);
		fclose($fh);
		if($storage->saveFile($file)) {
			unlink($file);
		}
		return true;
	}	
	
}

?>