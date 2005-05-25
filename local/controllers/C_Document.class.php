<?php

require_once CELLINI_ROOT. "/controllers/Controller.class.php";
require_once APP_ROOT . "/local/ordo/Document.class.php";
require_once APP_ROOT . "/local/includes/CategoryTree.class.php";
require_once CELLINI_ROOT. "/lib/TreeMenu.php";
require_once APP_ROOT . "/local/ordo/Note.class.php";

class C_Document extends Controller {

	var $template_mod;
	var $documents;
	var $document_categories;
	var $tree;
	var $_config;
	var $file_path;

	var $showDocumentsOnTree = true;
	var $showCategoryView = true;
	

	function C_Document($template_mod = "general") {
		parent::Controller();
		$this->documents = array();
		$this->template_mod = $template_mod;
		$this->assign("CURRENT_ACTION", Cellini::link(true));
		
		//get global config options
		$this->_config = $GLOBALS['config']['document_manager'];
		if (isset($this->_config['documents_on_tree'])) {
			$this->showDocumentsOnTree = $this->_config['documents_on_tree'];
		}
		if (isset($this->_config['category_view'])) {
			$this->showCategoryView = $this->_config['category_view'];
		}
		if (!isset($this->_config['repository'])) {
			$this->_config['repository'] = APP_ROOT."/user/documents";
		}

		// read in project id
		if (!isset($_GET['project_id']) && isset($_GET[0])) {
			$_GET['project_id'] = $_GET[0];
		}
		else {
			$_GET['project_id'] = $this->get('patient_id','c_patient');
			//$_GET['project_id'] = 0;
		}
		
		if(!isset($_GET['id'])) {
			$_GET['id'] = $this->get('patient_id','c_patient');
		}
		$this->file_path = $this->_config['repository'] . preg_replace("/[^A-Za-z0-9]/","_",$_GET['id']) . "/";

		$this->_args = array("project_id" => $_GET['project_id']);
		$this->id = $_GET['project_id'];
		$this->assign("FORM_ACTION", Cellini::link(true,true,true,$this->id) . $_SERVER['QUERY_STRING']);
		
		$this->assign("STYLE", $GLOBALS['style']);

		$parent = "";
		if (isset($_GET['parent_id'])) {
			$parent = "&parent_id=".$_GET['parent_id'];
		}
		$this->assign('MULTI_UPLOAD_ACTION',Cellini::link('multi_upload',true,true,$this->id).$parent);

		if (!isset($_SESSION['DM']['group'])) {
			$document =& ORDataObject::factory('Document');
			$glookup = array_flip($document->getGroupList());

			/*$state = $b_address->get_state(true);
			if (isset($glookup[$state])) {
				$_SESSION['DM']['group'] = $glookup[$state];
			}
			else if (isset($glookup['Default'])) {
				$_SESSION['DM']['group'] = $glookup['Default'];
			}
			else {
				$_SESSION['DM']['group'] = 2;
			}*/
		}

		$t = new CategoryTree(1);
		//print_r($t->tree);
		$this->tree = $t;
	}
	
	function upload_action($project_id,$category_id) {
		$activity = "";
		if ($this->showCategoryView) {
			$activity = $this->viewCategory_action($project_id,$category_id);
		}

		if ($this->security->acl_qcheck('uploadFile',false,false,false)) {
			$category_name = $this->tree->get_node_name($category_id);
			$this->assign("category_id", $category_id);
			$this->assign("category_name", $category_name);
			$this->assign("project_id", $project_id);
			$this->assign("foreign_id", $project_id);

			$document =& ORDataObject::factory('Document');
			$group_list = $document->getGroupList();
			$this->assign("group_list",$group_list);

			$activity .= $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_upload.html"));
		}
		$this->assign("activity", $activity);


		return $this->list_action_view($project_id);
	}
	
	function upload_action_process() {
		
		if ($_POST['process'] != "true")
			return;
			
		if (is_numeric($_POST['category_id'])) {	
			$category_id = $_POST['category_id'];
		}
		if (is_numeric($_POST['foreign_id'])) {
			$project_id = $_POST['foreign_id'];
		}
		foreach ($_FILES as $file) {
		  $fname = $file['name'];
		  $err = "";
		  $error = "";
		  if ($file['error'] > 0 || empty($file['name']) || $file['size'] == 0) {
		  	$fname = $file['name'];
		  	if (empty($fname)) {
		  		$fname = htmlentities("<empty>");
		  	}
		  	$error = "Error number: " . $file['error'] . " occured while uploading file named: " . $fname . "\n";
		  	if ($file['size'] == 0) {
		  		$error .= "The system does not permit uploading files of with size 0.\n";
		  	}
		  	
		  }
		  else {
		  	
		  	if (!file_exists($this->file_path)) {
		  		if (!mkdir($this->file_path,0700)) {
		  			$error .= "The system was unable to create the directory for this upload, '" . $this->file_path . "'.\n";
		  		}
		  	}
		  	
		  	$fname = preg_replace("/[^a-zA-Z0-9_.]/","_",$fname);
		  	if (file_exists($this->file_path.$file['name'])) {
		  		$error .= "File with same name already exists at location: " . $this->file_path . "\n";
		  		$fname = basename($this->_rename_file($this->file_path.$file['name']));
		  		$file['name'] = $fname;
		  		$error .= "Current file name was changed to " . $fname ."\n";	
		  	}
		  	if (move_uploaded_file($file['tmp_name'],$this->file_path.$file['name'])) {
		  		$error .= "File " . $file['name'] . " successfully stored.\n";
		  		$d = new Document();
		  		$d->url = "file://" .$this->file_path.$file['name'];
		  		$d->mimetype = $file['type'];
		  		$d->size = $file['size'];
				if (isset($d->type_array['file_url'])){
					$d->type = $d->type_array['file_url'];
				}
		  		$d->set_foreign_id($project_id);
				$d->populate_array($_POST);
		  		$d->persist();
		  		$d->populate();
		  		$this->assign("file",$d);
		  		
		  		if (is_numeric($d->get_id()) && is_numeric($category_id)) {
		  		  $sql = "REPLACE INTO category_to_document set category_id = '" . $category_id . "', document_id = '" . $d->get_id() . "'";
		  		  $d->_db->Execute($sql);
		  		}
		  	}
		  	else {
				$this->messages->addMessage("The file could not be succesfully stored, this error is usually related to permissions problems on the storage system.\n");
		  	}
		  }
		}
		//$this->_state = false;
		$_POST['process'] = "";
		//return $this->fetch($GLOBALS['template_dir'] . "documents/" . $this->template_mod . "_upload.html");
	}
	
	function note_action_process($project_id) {
		
		if ($_POST['process'] != "true")
			return;
			
		$n = new Note();
		parent::populate_object($n);
		$n->persist();
		
		$this->_state = false;
		$_POST['process'] = "";
		return $this->view_action($project_id,$n->get_foreign_id());		
	}

	function default_action() {
		return $this->list_action_view();
	}

	function viewCategory_action($project_id="",$category_id) {
		// lets find the category on the tree, we only care about top level categories
		if (!isset($this->tree->tree[1][$category_id])) {
			return "";
		}
		$tree = $this->tree->tree[1][$category_id];

		$category_names = $this->tree->_get_category_names($project_id);
				
 		$data =& $this->_build_category_view($tree,$category_names);

		$this->assign_by_ref('data',$data);
		$l = Cellini::link('retrieve',true,'util');
		$this->assign('VIEW_LINK',substr($l,0,strlen($l)-1)."/");


		return $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_viewCategory.html"));
	}

	function &_build_category_view($array,$category_names) {
		$out = array();
		if (!is_array($array)) {
			$array = array();	
		}

 		$node = &$this->_last_node;
 		$current_node = &$node;
 		foreach($array as $id => $ar) {
			if (isset($category_names[$id])) {
				$out[$id] = array();
				$out[$id]['name'] = $category_names[$id];
				$out[$id]['documents'] = $this->tree->getDataForParent($id,array(1));
			}
		}
 		return $out;
	}

	function view_action($project_id="",$doc_id) {
		
		$d = new Document($doc_id);	
		$n = new Note();
		
		$notes = $n->notes_factory($doc_id);
		
		$this->assign("file", $d);
		$this->assign("web_path", Cellini::link("retrieve",true,true,$this->id) . "document_id=" . $d->get_id() . "&");
		$this->assign("NOTE_ACTION",Cellini::link("note",true,true,$this->id));
		$this->assign("MOVE_ACTION",Cellini::link("move",true,true,$this->id) . "document_id=" . $d->get_id() . "&process=true");
		
		$this->assign("notes",$notes);
		
		$this->_last_node = null;
		
		$menu  = new HTML_TreeMenu();
		
		//pass an empty array because we don't want the documents for each category showing up in this list box
 		$rnode = $this->_array_recurse($this->tree->tree,array());
		$menu->addItem($rnode);
		$treeMenu_listbox  = &new HTML_TreeMenu_Listbox($menu, array("promoText" => "Move Document to Category:"));
		
		$this->assign("tree_html_listbox",$treeMenu_listbox->toHTML());
		
		$activity = $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_view.html"));
		$this->assign("activity", $activity);
		
		return $this->list_action_view($project_id);
	}
	
	function retrieve_action($project_id="",$document_id,$as_file=true) {
		$d = new Document($document_id);
		$url =  $d->get_url();
		
		//strip url of protocol handler
		$url = preg_replace("|^(.*)://|","",$url);
		
		if (!file_exists($url)) {
			echo "The requested document is not present at the expected location on the filesystem or there are not sufficient permissions to access it. $url";	
		}
		else {
			header("Pragma: public");
        	header("Expires: 0");
        	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        	if ($as_file) {
				header("Content-Disposition: attachment; filename=\"" . basename($d->get_url()) . "\"");
        	}
			header("Content-Type: " . $d->get_mimetype());
			header("Content-Length: " . $d->get_size());
			$f = fopen($url,"r");
			fpassthru($f);
			exit;
		}
	}
	
	function queue_action($project_id="") {
		$queue_files = array();
		
		//see if the repository exists and it is a directory else error
		if (file_exists($this->_config['repository']) && is_dir($this->_config['repository'])) {
			$dir = opendir($this->_config['repository']);
			//read each entry in the directory
			while (($file = readdir($dir)) !== false) {
				//concat the filename and path
				$file = $this->_config['repository'] .$file;
				$file_info = array();
				//if the filename is a file get its info and put into a tmp array
				if (is_file($file) && strpos(basename($file),".") !== 0) {
					$file_info['filename'] = basename($file);
					$file_info['mtime'] = date("m/d/Y H:i:s",filemtime($file));
					$d = Document::document_factory_url("file://" . $file);
					preg_match("/^([0-9]+)_/",basename($file),$patient_match);
					$file_info['project_id'] = $patient_match[1];
					$file_info['document_id'] = $d->get_id();
					$file_info['web_path'] = Cellini::link("retrieve",true,true,$this->id) . "document_id=" . $d->get_id() . "&";
					
					//merge the tmp array into the larger array
					$queue_files[] = $file_info; 
				}
       		}
       		closedir($dir);
		}
		else {
			$this->messages->addMessage('',"The repository directory does not exist, it is not a directory or there are not sufficient permissions to access it. '" . $this->config['repository'] . "'\n");	
		}
		
		
		$this->assign("queue_files",$queue_files);
		$this->_last_node = null;
		
		$menu  = new HTML_TreeMenu();
		
		//pass an empty array because we don't want the documents for each category showing up in this list box
 		$rnode = $this->_array_recurse($this->tree->tree,array());
		$menu->addItem($rnode);
		$treeMenu_listbox  = &new HTML_TreeMenu_Listbox($menu, array());
		
		$this->assign("tree_html_listbox",$treeMenu_listbox->toHTML());
		
		return $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_queue.html"));
	}
	
	function move_action_process($project_id="",$document_id) {
		if ($_POST['process'] != "true")
			return;
		
		$new_category_id = $_POST['new_category_id'];
		if (isset($_POST['new_entity_id'])) {
			$new_entity_id = $_POST['new_entity_id'];
		}
		
		//move to new category
		if (is_numeric($new_category_id) && is_numeric($document_id)) {
			$sql = "UPDATE category_to_document set category_id = '" . $new_category_id . "' where document_id = '" . $document_id ."'";
			$this->messages->AddMessage('',"Document moved to new category '" . $this->tree->_id_name[$new_category_id]['name']  . "' successfully.\n");
			//echo $sql;
			$this->tree->_db->Execute($sql);
		}
		
		//move to new patient
		if (isset($new_entity_id) && is_numeric($new_entity_id) && is_numeric($document_id)) {
			$d = new Document($document_id);
			$sql = "SELECT pid from patient_data where pubpid = '" . $new_project_id . "'";
			$result = $d->_db->Execute($sql);
			
			if (!$result || $result->EOF) {
				//patient id does not exist
				$this->messages->addMessage('',"Document could not be moved to patient id '" . $new_project_id  . "' because that id does not exist.\n");
			}
			else {
				//set the new patient
				$d->set_foreign_id($new_project_id);
				$d->persist();
				$this->_state = false;
				$this->messages->addMessage('',"Document moved to patient id '" . $new_project_id  . "' successfully.\n");
				return $this->list_action_view($project_id);
			}
		}
		/*
		//in this case return the document to the queue instead of moving it
		elseif (strtolower($new_project_id) == "q" && is_numeric($document_id)) {
			$d = new Document($document_id);
			$new_path = $this->_config['repository'];
			$fname = $d->get_url_file();

			//see if there is an existing file with the same name and rename as necessary
		  	if (file_exists($new_path.$d->get_url_file())) {
				$this->messages->addMessage('',"File with same name already exists in the queue.\n");
		  		$fname = basename($this->_rename_file($new_path.$d->get_url_file()));
				$this->messages->addMessage("Current file name was changed to " . $fname ."\n");	
		  	}
		  	 
		  	//now move the file
		  	if (rename($d->get_url_filepath(),$new_path.$fname)) {
		  		$d->url = "file://" .$new_path.$fname;
		  		$d->set_foreign_id("");
				$d->persist();
		  		$d->persist();
		  		$d->populate();
		  		
		  		$sql = "DELETE FROM categories_to_documents where document_id =" . $d->_db->qstr($document_id);
				$d->_db->Execute($sql);
				$this->messages->addMessage('',"Document returned to queue successfully.\n");
				
		  	}
		  	else {
				$this->messages->addMessage('',"The file could not be succesfully stored, this error is usually related to permissions problems on the storage system.\n");
		  	}

			$this->_state = false;
			return $this->list_action_view($project_id);
		}*/
		
		$this->_state = false;
		return $this->view_action($project_id,$document_id);
	}

	function list_action_view($project_id = "") {
		if (empty($project_id) && $this->id > 0) {
			$project_id = $this->id;
		}
		$this->_last_node = null;
		$categories_list = $this->tree->_get_categories_array($project_id);
		//print_r($categories_list);
				
		$menu  = new HTML_TreeMenu();
 		$rnode = $this->_array_recurse($this->tree->tree,$categories_list);
		$menu->addItem($rnode);
		$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $this->base_dir.'images/stock', 'defaultClass' => 'treeMenuDefault'));
		$treeMenu_listbox  = &new HTML_TreeMenu_Listbox($menu, array('linkTarget' => '_self'));
		
		$this->assign("tree_html",$treeMenu->toHTML());

		$document =& ORDataObject::factory('Document');
		$group_list = $document->getGroupList();
		unset($group_list[1]);
		$this->assign("group_list",$group_list);

		//$this->assign('group',$_SESSION['DM']['group']);

		$this->assign("GROUP_ACTION", Cellini::ManagerLink('changeGroup',$this->id).$_SERVER['QUERY_STRING']);

		
		return $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_list.html"));
	}
	
	/*
	*	This is a recursive function to rename a file to something that doesn't already exist. It appends a numeric to the end of the file, if
	*	the file already has a numeric it increments that.
	*	It supports numeric endings upto 1000, after which it uses an md5sum on now() instead. This is because some files end in
	*	dates and we don't want to increment those.
	*/
	function _rename_file($fname) {
		$file = basename($fname);
		$fparts = split("\.",$fname);
		$path = dirname($fname);
		if (is_numeric($fparts[count($fparts) -1]) && $fparts[count($fparts) -1] < 1000) {
			$new_name = "";
			for($i=0;$i<count($fparts);$i++) {
				if ($i == (count($fparts) -1)) {
				  $new_name .= ($fparts[$i] +1);
				}
				else {
				  $new_name .= $fparts[$i] . ".";
				}
			}	
			$fname = $new_name;
		}
		elseif (isset($fparts[count($fparts)]) && is_numeric($fparts[count($fparts)])) {
			$fname = $fname . "." . md5sum(now());
		}
		else {
			$fname = $fname . ".1";
		}
		if (file_exists($fname)) {
			return $this->_rename_file($fname);
		}
		else {
			return($fname);	
		}
	}
	
	function &_array_recurse($array,$categories = array()) {
		if (!is_array($array)) {
			$array = array();	
		}
 		$node = &$this->_last_node;
 		$current_node = &$node;
		$expandedIcon = 'folder-expanded.gif';
 		foreach($array as $id => $ar) {
 			$icon = 'folder.gif';
 			if (is_array($ar)  || !empty($id)) {
 			  if ($node == null) {
 			  	//echo "r:" . $this->tree->get_node_name($id) . "<br>";
				$rnode = new HTML_TreeNode(array("id" => $id, 'text' => $this->tree->get_node_name($id), 
						'link' => Cellini::link("upload",true,true,$this->id)."parent_id=$id&", 
			    			'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => false));
			    $this->_last_node = &$rnode;
 			  	$node = &$rnode;
 			  	$current_node =&$rnode;
			  }
			  else {
			  	//echo "p:" . $this->tree->get_node_name($id) . "<br>";
 			    $this->_last_node = &$node->addItem(new HTML_TreeNode(array("id" => $id, 'text' => $this->tree->get_node_name($id), 
							'link' => Cellini::link("upload",true,true,$this->id)."parent_id=$id&", 
							'icon' => $icon, 'expandedIcon' => $expandedIcon)));
 			    $current_node =&$this->_last_node;
			  }
 			  
 			  $this->_array_recurse($ar,$categories);
 			}
 			else {
 				if ($id === 0 && !empty($ar)) {
 				  $info = $this->tree->get_node_info($id);
 				  //echo "b:" . $this->tree->get_node_name($id) . "<br>";
 				  $current_node = &$node->addItem(new HTML_TreeNode(array("id" => $id, 'text' => $info['value'], 
								'link' => Cellini::link("upload",true,true,$this->id)."parent_id=$id&", 
								'icon' => $icon, 'expandedIcon' => $expandedIcon)));
 				}
 				else {
 					//there is a third case that is implicit here when title === 0 and $ar is empty, in that case we do not want to do anything
 					//this conditional tree could be more efficient but working with recursive trees makes my head hurt, TODO
 					if ($id !== 0 && is_object($node)) {
 					  //echo "n:" . $this->tree->get_node_name($id) . "<br>";
 				  	  $current_node = &$node->addItem(new HTML_TreeNode(array("id" => $id, 'text' => $this->tree->get_node_name($id), 
								'link' => Cellini::link("upload",true,true,$this->id)."parent_id=$id&", 
								'icon' => $icon, 'expandedIcon' => $expandedIcon)));
 				  	  
 					}
 				}
 			}	
 			
			if ($this->showDocumentsOnTree) {
				$icon = "file3.png";
				if (isset($categories[$id]) && is_array($categories[$id])) {
					foreach ($categories[$id] as $doc) {
						$current_node->addItem(new HTML_TreeNode(array('text' => '<span title="'.$doc['note'].'">'
								.basename($doc['url']).'</span>', 
								'link' => Cellini::link("view",true,true,$this->id)."doc_id=". $doc['document_id'] . "&", 
								'icon' => $icon, 'expandedIcon' => $expandedIcon,
								)
							));
					}
				}
			}
 		}
 		return $node;
 	}
	
	
	var $_pullCats = array();
	var $_pullParent = array();
	function multi_upload_action_uploadFile($project_id,$parent_id = 1) {
		$this->_buildPulldown($this->tree->tree);
		$this->assign('list',range(0,4));
		$this->assign('LIST_ACTION',Cellini::link('list',true,true,$this->id));
		$this->assign('categories',$this->_pullCats);
		$this->assign('parent',$parent_id);
		return $this->fetch(Cellini::getTemplatePath("documents/" . $this->template_mod . "_multiUpload.html"));
	}

	function multi_upload_action_process($project_id) {
		if (!file_exists($this->file_path)) {
			if (!@mkdir($this->file_path,0700)) {
				$this->messages->addMessage("The system was unable to create the directory for this upload, '" . $this->file_path . "'.  No file uploads were completed.\n");
				return;
			}
		}
		$report = array();
		foreach($_FILES['file']['name'] as $key => $name) {
			$fname = preg_replace("/[^a-zA-Z0-9_.]/","_",$name);

			if (empty($name)) {
				break;
			}
			if ($_FILES['file']['error'][$key] > 0 || $_FILES['file']['size'][$key] == 0) {
				$extra = " Error Number: ".$_FILES['file']['error'][$key];
				if ($_FILES['file']['size'][$key] == 0) {
					$extra = " , The system does not permit uploading files of with size 0.\n";
				}
				$report[$fname] = "Error Uploading File: $name".$extra;
			}
			else {
				if (file_exists($this->file_path.$fname)) {
					$report[$fname] = "File with same name already exists at location: " . $this->file_path . "\n";
					$fname = basename($this->_rename_file($this->file_path.$fname));
					$report[$fname] = "Current file name was changed to " . $fname ."\n";	
				}
		  	
		  	
				if (move_uploaded_file($_FILES['file']['tmp_name'][$key],$this->file_path.$fname)) {
					if (!isset($report[$fname])) {
						$report[$fname] = "";
					}
					$report[$fname] .= " File successfully stored.\n";
					$d = new Document();
					$d->url = "file://" .$this->file_path.$fname;
					$d->mimetype = $_FILES['file']['type'][$key];
					$d->size = $_FILES['file']['size'][$key];
					if (isset($d->type_array['file_url'])){
						$d->type = $d->type_array['file_url'];
					}
					$d->set_foreign_id($project_id);
					$d->persist();
					
					$category_id = (int)$_POST['category'][$key];
					$sql = "REPLACE INTO category_to_document set category_id = '" . $category_id . "', document_id = '" . $d->get_id() . "'";
					$d->_Execute($sql);

					if (!empty($_POST['note'][$key])) {
						$n = new Note();
						$n->set('foreign_id',$d->get_id());
						$n->set('note',$_POST['note'][$key]);
						$n->set('owner',$this->_me->get_id());
						$n->persist();
					}
				}
				else {
					$report[$fname] = "The file could not be succesfully stored, this error is usually related to permissions problems on the storage system.\n";
				}
			}
		}
		$this->assign('report',$report);
	}

	function _buildPulldown($treeRow) {
		foreach($treeRow as $key => $val) {
			if ($key != 0) {
				if (count($this->_pullParent) > 0) {
					$this->_pullCats[$key] = implode(' -> ',$this->_pullParent).' -> '.$this->tree->_id_name[$key]['name'];
				}
				else {
					$this->_pullCats[$key] = $this->tree->_id_name[$key]['name'];
				}
			}
			if (is_array($val)) {
				array_push($this->_pullParent,$this->tree->_id_name[$key]['name']);
				$this->_buildPulldown($val);
				array_pop($this->_pullParent);
			}
		}
	}
}
//place to hold optional code
//$first_node = array_keys($t->tree);
		//$first_node = $first_node[0];
		//$node1 = new HTML_TreeNode(array('text' => $t->get_node_name($first_node), 'link' => "test.php", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => true), array('onclick' => "alert('foo'); return false", 'onexpand' => "alert('Expanded')"));
		
		//$this->_last_node = &$node1;

?>
