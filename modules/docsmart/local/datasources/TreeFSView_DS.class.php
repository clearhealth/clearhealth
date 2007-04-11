<?php
/*
 * Created on Apr 14, 2006
 *
 */
 
 $loader->requireOnce('/datasources/FolderNodes_DS.class.php');
 $loader->requireOnce('/datasources/StorableNodes_DS.class.php');
  
 class TreeFSView_DS {

	var $folders;
	var $files;
	 
 	function TreeFSView_DS($parentId = 1, $parentLevel = 0) {
 		 $this->folders = new FolderNodes_DS($parentId, $parentLevel, $parentLevel + 1, 'folders.label');
 		 $this->files = new StorableNodes_DS($parentId, $parentLevel, $parentLevel + 1, 'storables.filename');
 	} 

	function toArray() {
		return array_merge($this->folders->toArray(), $this->files->toArray());
	}
	
	function preview() {
		return $this->folders->preview()."\n".$this->files->preview();	
	}
 }
 
?>
