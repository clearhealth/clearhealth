<?php
$loader->requireOnce('datasources/Tree_DS.class.php');

class FolderNodes_DS extends Tree_DS {
	
	function FolderNodes_DS($parentId = 1, $startLevel = 1, $endLevel = null, $orderBy = null) {
		parent::Tree_DS($parentId, $startLevel, $endLevel, $orderBy);
	}
	
	function getCols() {
		return "folders.modify_date, UNIX_TIMESTAMP(folders.create_date) as mtime, UNIX_TIMESTAMP(folders.create_date) as ctime, folders.label, folders.webdavname as displayname, folders.label as filename, ".parent::getCols();
	}
	
	function getFrom() {
		return parent::getFrom()
			.' LEFT JOIN folders ON folders.folder_id = '.$this->_table.'.node_id AND '.$this->_table.'.node_type = '."'folder'";
	}	
	
	function getWhere($parentId, $level) {
		return parent::getWhere($parentId, $level)." AND ".$this->_table.".node_type LIKE 'folder'";
	}

}

?>
