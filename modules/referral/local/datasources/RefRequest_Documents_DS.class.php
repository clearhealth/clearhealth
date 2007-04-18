<?php

$loader->requireOnce('datasources/Tree_DS.class.php');

class RefRequest_Documents_DS extends Tree_DS
{
	var $_requestId = null;
	
	function RefRequest_Documents_DS($request_id) {
		$this->_requestId = $request_id;
		parent::Tree_DS(1);
		
		$this->setLabel('delete','<input type="checkbox" id="bulkChecker" onclick="changeSatatus($(\'bulkDelete\').getElementsByTagName(\'input\'), this)">');
		$this->registerTemplate('filename', '<a href="javascript:void(0)" onclick="window.open(\'' . Celini::link('default','DocSmartStorable','minimal').'tree_id={$tree_id}\', \'chlreferral\', \'menubar=no,location=no,scrollbars=yes,resizable=yes,status=no,height=300,width=630\');">{$filename}</a>');
		$this->registerTemplate('delete', '<input type="checkbox" name="storables[]" value="{$tree_id}">');
	}
	
	function getCols() {
		return '
			t.tree_id,
			UNIX_TIMESTAMP(s.create_date) as ctime,
			s.create_date as create_date,
			s.mimetype,
			s.type,
			s.filename as name,
			s.filename,
			s.webdavname as displayname,
			s.storable_id';
	}
	
	function getFrom() {
		return '
			storables AS s
			INNER JOIN tree AS t ON(t.node_type = "storable" AND t.node_id = s.storable_id)
			INNER JOIN relationship AS r ON (r.parent_type = "Storable" AND s.storable_id = r.parent_id)'; 
	}	
	
	function getWhere($parentId, $level) {
		$db = new clniDB();
		$qRequestId = $db->quote($this->_requestId);
		
		return "
			r.child_type = 'refRequest' AND 
			r.child_id = {$qRequestId}";
	}
	
	function getLabels() {
		return array(
			'delete' => '',					
			'filename' => "Filename");
	}
	
	function getGroupBy() {
		return false;
	}
	
	function getOrderBy() {
		return false;
	}
}

?>
