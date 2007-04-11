<?

$loader->requireOnce('/includes/ORDOList.class.php');

/**
 * Collection of the TreeNode elements type folder
 *
 */
class FolderNodeList extends ORDOList {
	
	function FolderNodeList() {
		parent::ORDOList('treenode');
	}

	/**
	 * Return collection as array with each element converted to the array too
	 *
	 * @return array
	 */
	function toArray() {
		$list = parent::toArray();
		foreach($list as $key => $node) {
			if($node['node_type'] != "folder") {
				unset($list[$key]);
				continue;
			}
			$folder =& Celini::newOrdo('Folder', $node['node_id']);
			$node['folder'] = $folder->toArray();
			$list[$key] = $node;
		}
		return $list;
	}	
	
}

?>