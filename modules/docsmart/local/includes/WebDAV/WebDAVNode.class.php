<?php

class WebDAVNode {

	var $treeNode;
	var $node_id;
	var $ctime;
	var $mtime;
	var $filename;
	var $displayname;	
	var $filesize;
	var $mimetype;
	var $node_type;
	var $revision_id;
	var $path;
	var $storage_type;

	function WebDAVNode($name = null, $level = 0) {
		$this->treeNode =& Celini::newOrdo('TreeNode');
		if(isset($name) && $level > 0) {
			$this->load($name, $level);
		} else {
			$row['ctime'] = mktime();
			$row['mtime'] = mktime();
			$row['tree_id'] = 1;
			$row['level'] = 0;
			$row['node_type'] = 'folder';
			$this->populate_array($row);
		}	
	}

	function load($name = '', $level = 0) {
		$db =& Celini::dbInstance();
		$sql = "SELECT tree.*, storables.storage_type, revisions.revision_id, IF(folders.folder_id IS NULL, storables.filename, folders.label) as filename, storables.mimetype, revisions.filesize, IF(folders.folder_id IS NULL, storables.create_date, folders.create_date) as ctime, IF(folders.folder_id IS NULL, revisions.create_date, folders.modify_date) as mtime  FROM tree LEFT JOIN tree as _tree ON _tree.tree_id=tree.tree_id LEFT JOIN folders ON folders.folder_id=tree.node_id AND tree.node_type='folder' LEFT JOIN storables ON storables.storable_id=_tree.node_id AND _tree.node_type='storable' LEFT JOIN revisions ON revisions.revision_id=storables.last_revision_id WHERE (folders.label='".$name."' AND tree.level = '".$level."') OR (storables.filename='".$name."' AND tree.level = '".$level."');";
		$rs = $db->Execute($sql);
		$row = $rs->FetchRow();
		if(is_array($row)) {
			$this->populate_array($row);
		}else{
			$this->node_type = null;	
		}
	}
	
	function loadByPath($path = "") {
		$db =& Celini::dbInstance();

		$_path = pathinfo($path);

//		// load node from the database if it not a ROOT
		if($_path['dirname'] == "/" && empty($_path['basename'])) {
			return;
		}		

		$name = $_path['basename'];		
		$path = preg_split('%/%', HTTP_WebDAV_Server::_slashify($_path['dirname']));

		$from = array();
		$where = array();		
		foreach($path as $id => $p) {
			if($p == "") {
				continue;
			}
			$from[] = "tree as t".$id." left join folders as f".$id." on f".$id.".folder_id=t".$id.".node_id and t".$id.".node_type='folder'";
			$where[] = "f".$id.".label='".$p."' and t".$id.".level=".($id)." and tree.lft between t".$id.".lft AND t".$id.".rght";
		}

		
		
		$sql = "SELECT tree.*, storables.storage_type, revisions.revision_id, IF(folders.folder_id IS NULL, storables.filename, folders.label) as filename, IF(folders.folder_id IS NULL, storables.webdavname, folders.webdavname) as displayname, storables.mimetype, revisions.filesize, IF(folders.folder_id IS NULL, storables.create_date, folders.create_date) as ctime, IF(folders.folder_id IS NULL, revisions.create_date, folders.modify_date) as mtime FROM ".((sizeof($from) > 0) ? implode(",",$from)."," : "")." tree LEFT JOIN folders ON folders.folder_id=tree.node_id LEFT JOIN storables ON storables.storable_id=tree.node_id AND tree.node_type='storable' LEFT JOIN revisions ON revisions.revision_id=storables.last_revision_id WHERE (folders.label='".$name."' OR storables.filename='".$name."') AND tree.level=".(sizeof($from)+1)." ".((sizeof($where) > 0) ? " AND ".implode(" AND ",$where) : "");
		$rs = $db->Execute($sql);
		$row = $rs->FetchRow();
		if(is_array($row)) {
			$this->populate_array($row);
		}else{
			$this->node_type = null;	
		}		
	}
	
	function populate_array($row = array()) {
		$this->treeNode->populate_array($row);
		foreach(get_class_vars(get_class($this)) as $key => $var) {
			if(!method_exists($this->$key, 'populate_array')) {
				$this->$key = @$row[$key];
			}		
		}
	}
	
	function isDir() {
		return $this->node_type == 'folder';	
	}
	
	function toArray() {
		$result = array();
		foreach(get_class_vars(get_class($this)) as $key => $var) {
			if(method_exists($this->$key, 'toarray')) {
				$result[$key] = $this->$key->toArray();
			}else{
				$result[$key] = $this->$key;	
			}
		}
		return $result;
	}
	
	function set($key, $val) {
		if(in_array($key, array_keys(get_class_vars(get_class($this))))) {
			$this->$key = $val;	
		}
	}
	
	function getContent() {
		$storage = new RevisionStorage($this->revision_id, $this->storage_type);
		return $storage->getFile();
	}
	
}

?>
