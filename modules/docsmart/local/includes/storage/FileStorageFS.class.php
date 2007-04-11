<?php

$loader->requireOnce('includes/storage/FileStorage.class.php');

/**
 * Special storage which is used for managing files on the filesystem
 *
 */
class FileStorageFS extends FileStorage {
	
	/**
	 * Getting file's content
	 *
	 * @return blob
	 */
	function getFile() {
		$filename = FileStorageFS::getFileName($this->revisionId);
		return file_get_contents($filename);		
	}
	
	/**
	 * Removing file from the file system
	 *
	 * @return bool
	 */
	function removeFile() {
		$filename = FileStorageFS::getFileName($this->revisionId);
		return unlink($filename);
	}

	/**
	 * Saving file to the file system
	 *
	 * @return bool
	 */
	function saveFile($file) {
		$filename = FileStorageFS::getFileName($this->revisionId);
		if(!copy($file, $filename)) {
			return 0;
		}
		return filesize($filename);
	}

	/**
	 * Copy file from filesystem to new storage
	 *
	 * @param FileStorage $storage
	 * @return bool
	 */
	function copyFile($storage) {
		$file = FileStorageFS::getFileName($this->revisionId);
		return $storage->saveFile($file);
	}	
	
	function getFileName($revisionId) {
		$config =& Celini::configInstance();
		return $config->get('storage_path_fs', APP_ROOT . '/user/documents') . '/' . $revisionId;
	}
}

?>