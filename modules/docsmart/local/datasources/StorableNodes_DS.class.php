<?php
$loader->requireOnce('datasources/Tree_DS.class.php');

class StorableNodes_DS extends Tree_DS {
	
	function StorableNodes_DS($parentId, $level = 1, $revisionId = null, $orderBy = null) {
		parent::Tree_DS($parentId, $level, $level + 1, $orderBy);
	}
	
	function getCols() {
		return "
			COUNT(revisions.revision) as revisions,
			r.filesize,
			UNIX_TIMESTAMP(r.create_date) as mtime,
			revisions.user_id,
			UNIX_TIMESTAMP(storables.create_date) as ctime,
			storables.create_date as create_date,
			storables.mimetype, 
			storables.type,
			storables.filename as name,
			storables.filename, 
			storables.webdavname as displayname,
			storables.storable_id, "
			.parent::getCols();
	}
	
	function getFrom() {
		return parent::getFrom()
			.' LEFT JOIN storables ON storables.storable_id = '.$this->_table.'.node_id'
			.' LEFT JOIN revisions ON revisions.storable_id=storables.storable_id'
			.' LEFT JOIN revisions as r ON r.revision_id=revisions.revision_id';
	}	
	
	function getWhere($parentId, $level) {
		return parent::getWhere($parentId, $level) . " AND " .
		$this->_table . ".node_type LIKE 'storable' AND
		r.revision_id=storables.last_revision_id";
	}

	function getLabels() {
		return array(
			'delete' => '',					
			'filename' => "Filename",
			'create_date' => "Last rev. date",
			'revisions' => "Total revs.",
			'user_id' => "User");
	}
	
	function getGroupBy() {
		return "revisions.storable_id";
	}
}

?>
