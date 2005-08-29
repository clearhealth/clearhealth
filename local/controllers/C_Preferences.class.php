<?php

require_once CELLINI_ROOT . "/controllers/Controller.class.php";
require_once CELLINI_ROOT . "/includes/PreferenceTree.class.php";
require_once CELLINI_ROOT . "/lib/TreeMenu.php";

/**
*	Controller class the handles the default case of access that leads to login
*	Extends {@link Controller} see that for additional implementation and guideline details
*/

class C_Preferences extends Controller {
	 
	var $_last_node;
	 
	function C_Preferences ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Cellini::link('list','preferences'));
		
		$this->_load_prefs();
	}

	function default_action() {
		header("Location: ".Cellini::link('default','default','main'));
	}
	
	function list_action() {
		$dprefs = $_SESSION['prefs']['default'];
		$uprefs = $_SESSION['prefs']['user'];
		
		$prefs = $dprefs->tree;
		if (count($uprefs->tree) > 0) {
			$prefs = array_merge_recursive($dprefs->tree,$uprefs->tree); 	
		}
		
		//print_r($prefs);
		
		$icon         = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
		$menu  = new HTML_TreeMenu();
 		$this->_last_node = null;
 		$rnode = $this->_array_recurse($dprefs->tree, $dprefs);
		$menu->addItem($rnode);
		if (is_null($menu->items[0])) {
			$messages =& $this->get_template_vars('messages');
			$messages->addMessage('Database Error', 
				'The preference database has not properly been initiated.');
		}
		else {
			$treeOptions = array(
				'images' => $this->base_dir.'images/stock',
				'defaultClass' => 'treeMenuDefault'
			);
			$treeMenu = &new HTML_TreeMenu_DHTML($menu, $treeOptions);
			$this->assign("tree_html",$treeMenu->toHTML());
		}
		return $this->fetch($GLOBALS['template_dir'] . "preferences/" . $this->template_mod . "_list.html");
	}
	
	function delete_node_action_process($id) {
		if ($_POST['process'] != "true")
			return;
		
		$category_name = $this->tree->get_node_name($id);
		$category_info = $this->tree->get_node_info($id);
		$parent_name = $this->tree->get_node_name($category_info['parent']);
		
		if($parent_name != false && $parent_name != '')
		{
			$this->tree->delete_node($id);	
			$this->assign("message", "Category '$category_name' had been successfully deleted. Any sub-categories if present were moved below '$parent_name'.<br>");
			
			if (is_numeric($id)) {
				$sql = "UPDATE categories_to_documents set category_id = '" . $category_info['parent'] . "' where category_id = '" . $id ."'";
				$this->tree->_db->Execute($sql);
			}
		}
		else
		{
			$this->assign("message", "Category '$category_name' is a root node and can not be deleted.<br>");
		}
		$this->_state = false;
		
		return $this->list_action();
	}
	
	function &_array_recurse($array,$treeObj) {
		if (!is_array($array)) {
			$array = array();	
		}
 		$node = &$this->_last_node;
 		$icon = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
 		foreach($array as $id => $ar) {
 			if (is_array($ar) || !empty($id)) {
 			  if ($node == null) {
 			  	
 			  	//echo "r:" . $this->tree->get_node_name($id) . "<br>";
			    $rnode = new HTML_TreeNode(array('text' => $treeObj->get_node_name($id), 'link' => $this->_link("edit_node",true) . "id=" . ($id) . "&", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => false));
			    $this->_last_node = &$rnode;
 			  	$node = &$rnode;
			  }
			  else {
			  	//echo "p:" . $this->tree->get_node_name($id) . "<br>";
 			    $this->_last_node = &$node->addItem(new HTML_TreeNode(array('text' => $treeObj->get_node_name($id), 'link' => $this->_link("edit_node",true) . "id=" . ($id) . "&", 'icon' => $icon, 'expandedIcon' => $expandedIcon, "contents" => $ar)));
			  }
 			  if (is_array($ar)) {
 			    $this->_array_recurse($ar,$treeObj);
 			  }
 			}
 			else {
 				if ($id === 0 && !empty($ar)) {
 				  $info = $treeObj->get_node_info($id);
 				  //echo "b:" . $this->tree->get_node_name($id) . "<br>";
 				  $node->addItem(new HTML_TreeNode(array('text' => $info['value'], 'link' => $this->_link("edit_node",true) . "id=" . ($id) . "&", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
 				}
 				else {
 					//there is a third case that is implicit here when title === 0 and $ar is empty, in that case we do not want to do anything
 					//this conditional tree could be more efficient but working with trees makes my head hurt, TODO
 					if ($id !== 0 && is_object($node)) {
 					  //echo "n:" . $this->tree->get_node_name($id) . "<br>";
 				  	  $node->addItem(new HTML_TreeNode(array('text' => $treeObj->get_node_name($id), 'link' => $this->_link("edit_node",true) . "id=" . ($id) . "&", 'icon' => $icon, 'expandedIcon' => $expandedIcon)));
 					}
 				}
 			}	
 		}
 		return $node;
 	}
 	
 	function edit_node_action($id = "") {
 		if (is_numeric($id)) {
 		
 			$db = $GLOBALS['config']['adodb']['db'];
 			$sql = "SELECT * FROM preferences where id=" . $db->qstr($id);
 			
 			$result = $db->Execute($sql);
 			if ($result && !$result->EOF) {
 				$this->assign("NODE_ACTION",$this->_link("edit_node",true));
 				$this->assign("edit_node_id",$id);
 				$this->assign("edit_node_name", $result->fields['name']);
 				$this->assign("edit_node_value", $result->fields['value']);	
 			}
 		}
 		
 		return $this->list_action();	
 	}
 	
 	function edit_node_action_process() {
 		if ($_POST['process'] != "true")
			return;
 		$db = $GLOBALS['config']['adodb']['db'];
 		
 		if (is_numeric($_POST['edit_node_id'])) {
 			$sql = "UPDATE preferences set value=" . $db->qstr($_POST['edit_node_value']) . " where id=" . $db->qstr($_POST['edit_node_id']);
 			$result = $db->Execute($sql);
 			if ($result) {
 				$this->_load_prefs();
				$this->messages->addMessage('',"The node was successfully updated.<br>");	
 				$this->_state = false;
 				return $this->list_action();
 			}
 			else {
				$this->messages->addMessage("", "There was a problem with the database while updating the node.<br>");		
 			}		
 		}
 		else {
			$this->message->addMessage("", "An invalid node id was specified.<br>");	
 		}
 	}
 	
 	function _load_prefs() {
 		
 		$me =& Me::getInstance();
		
		$ct = new PreferenceTree("9000");
		$_SESSION['prefs']['default'] = $ct;
		
		$ct = new PreferenceTree($me->get_user_id());
		$_SESSION['prefs']['user'] = $ct;	
 	}
}

?>
