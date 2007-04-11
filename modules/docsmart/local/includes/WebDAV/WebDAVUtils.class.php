<?php

/**
 * Helper methods for WebDAVServer logic
 *
 */
class WebDAVUtils {
	
	/**
	 * Returns true is the given node is dir and false if it's not
	 *
	 * @param array $node
	 * @return boolean
	 */
	function isDir($node) {
		return $node['node_type'] == 'folder';
	}

	/**
	 * Returns temp filename
	 *
	 * @return string
	 */
	function getTmpFileName() {
		return tempnam(APP_ROOT."/tmp", "WEBDAV_");
	}
	
	/**
	 * Returns WebDAVNode by given path
	 *
	 * @param string $path
	 * @return WebDAVNode
	 */
	function getNode($path) {
		$_path = pathinfo($path);
		// define node as a ROOT 
		$node = new WebDAVNode();
		$node->loadByPath($path);
//		print_r($node->toArray());

//		// load node from the database if it not a ROOT
//		if(!($_path['dirname'] == "/" && empty($_path['basename']))) {
//			$name = $_path['basename'];		
//			$level = count(preg_split('%/%', HTTP_WebDAV_Server::_slashify($_path['dirname']))) - 1;
//			$node->load($name, $level);
//		}
		
		// return false if node didn't find in the database by the specified name
		if(!$node->node_type) {
			return false;
		}        	

		// set path for the node
		$node->set('path', $path);
		return $node;
	}	

	/**
	 * Returns parent WebDAVNode for the node by given path
	 *
	 * @param string $path
	 * @return WebDAVNode
	 */
	function getParentNode($path) {
    	$path = pathinfo($path);
    	// return false if the given node is a ROOT
        if($path['dirname'] == "/" && empty($path['basename'])) {
        	return false;
        }
		$name = $path['basename'];
		return WebDAVUtils::getNode($path['dirname']);
	}		

	/**
	 * Moves the $source with all it's child to the $dest path
	 *
	 * @param WebDAVNode $source
	 * @param string $dest
	 * @return boolean
	 */
	function moveNode($source, $dest) {
    	$parent = WebDAVUtils::getParentNode($dest);
		$node =& Celini::newOrdo('TreeNode', $source->treeNode->tree_id);
//		$_parent = $node->getParentNode();
//		if($_parent->tree_id != $parent->treeNode->tree_id) {
		if(!$node->moveNode($parent->treeNode->tree_id)) {
			return "412 precondition failed";
		}
//		}	
		if(!$source->isDir()) {
			// move the source node as a file
			$object =& Celini::newOrdo('Storable', $source->node_id);
			$object->set('filename', basename($dest));
		}else{
			// move the source node as a folder
			$object =& Celini::newOrdo('Folder', $source->node_id);
			$object->set('label', basename($dest));
		}
		// save the storable/folder to the database
		$object->persist();
		return false;
	}
	
	/**
	 * Copy $source to the specified path
	 *
	 * @param WebDAVNode $source
	 * @param string $dest
	 * @return boolean
	 */
	function copyNode($source, $dest) {
		$parent = WebDAVUtils::getParentNode($dest);
		if(!$parent->isDir()) {
			return "412 precondition failed";
		}

		if($source->isDir()) {
			// copy folder
			print_r($source->toArray());
			$ndoe =& Celini('TreeNode');
			exit;
		}else{
			// copy file
			// save storable
			$storable =& Celini::newOrdo('Storable');
			$storable->populate_array(
				array(
					'mimetype' => $source->mimetype,
					'filename' => $source->filename,
					'storage_type' => 'FS'
				));
			$storable->persist();
			if(!$storable->storable_id) {
				return "409 Conflict";
			}
			// save revision
			$revision =& Celini::newOrdo('Revision');
			$revision->populate_array(
				array(
					'storable_id' => $storable->storable_id,
					'revision' => 1,
					'filesize' => $source->filesize
				));
			$revision->persist();	
			if(!$revision->revision_id) {
				return "409 Conflict";
			}
			// update storable with revision_id
			$storable->set('last_revision_id', $revision->revision_id);
			$storable->persist();
			
			// save file's content
			$tempnam = WebDAVUtils::getTmpFileName();
			$fp = fopen($tempnam, "w");
			fwrite($fp, $source->getContent());
			fclose($fp);
			$storage = new RevisionStorage($revision->revision_id, $storable->storage_type);
			$storage->saveFile($tempnam);
			unlink($tempnam);
			
			//save storable to the tree
			$node =& Celini::newOrdo('TreeNode');
			$node->populate_array(
				array(
					'node_id' => $storable->storable_id,
					'node_type' => 'storable'
				));
			$node->insert($parent->treeNode->tree_id);	
		}
		return false;
	}
	
}

?>