<?php

$loader->requireOnce('/datasources/Revisions_DS.class.php');
$loader->requireOnce('/includes/storage/RevisionStorage.class.php');

define('ERR_COPY', 1);
define('ERR_DELETE', 2);
define('ERR_ROLLBACK', 3);

class StorageManager {
	
	var $storable;
	
	function StorageManager($storable) {
		$this->storable = $storable;
	}
	
	function switchStorage($storageType) {
		if($this->storable->storage_type == $storageType) {
			return false;
		}
		$revisions = new Revisions_DS("revisions.storable_id = '".$this->storable->storable_id."'");
		$revisions = $revisions->toArray();
		print "Copy files ...";
		if($res = $this->copyFiles($revisions, $storageType)) {
			return $res;
		}
		print "ok<br />";
		if($res = $this->removeFiles($revisions)){
			return $res;
		}
		$this->storable->set('storage_type', $storageType);
		$this->storable->persist();
		return false;
	}

	function copyFiles($revisions, $storageType) {
		foreach($revisions as $revision) {
			$storage = new RevisionStorage($revision['revision_id'], $this->storable->storage_type);
			print " [".$revision['revision_id']."] ";
			if(!$storage->copyFile($storageType)) {
				print "!";
				if($res = $this->removeFiles($revisions, $storageType)) {
					return ERR_ROLLBACK;
				}else{
					return ERR_COPY;
				}
			}
			print ".";
		}
		return false;
	}

	function removeFiles($revisions, $storageType = null) {
		$storageType = ($storageType) ? $storageType : $this->storable->storage_type;
		print $storageType;
		foreach($revisions as $revision) {
			$storage = new RevisionStorage($revision['revision_id'], $storageType);
			if(!$storage->removeFile()) {
					return ERR_DELETE;
			}
		}
		return false;
	}	

}


?>