<?php

class DocSmartTreeRenderer
{
	var $_dataArray = array();
	var $_view = null;
	
	function DocSmartTreeRenderer() {
		$this->_view = new clniView();
		$this->_view->path = 'treerenderer';
	}
	
	function setDataArray($array) {
		assert('is_array($array)');
		$this->_dataArray = $array;
	}
	
	function render() {
		$this->_view->assign('tree', $this->_dataArray);		
		return $this->_view->render('tree.html');
	}
}
