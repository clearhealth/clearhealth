<?php

$loader->requireOnce("ordo/ORDataObject.class.php");
$loader->requireOnce("includes/Pager.class.php");

/**
*	Controller class to handle user management
*	Extends {@link Controller} see that for additional implementation and guideline details
*/
class C_User extends Controller {
	 
	function C_User ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Celini::link('list'));
	}

	function list_action_view() {
		$this->assign("TOP_ACTION", Celini::link('list'));
		$u = ORdataObject::factory('User');

		$pager = new Pager();

		$res = $u->_db->query('select count(*) c from ' . $u->tableName());
		$pager->setMaxRows($res->fields['c']);

		$users = $u->users_factory($pager->getLimit());

		$this->assign('EDIT_ACTION', Celini::link('edit'));

		$this->assign("users",$users);
		$this->assign("pager",$pager);

		return $this->view->render('list.html'); 
	}
	
	function edit_action_edit($id=-1) {
		if (!is_numeric($id)) {
			echo "No suitable user id was provided, please check your query string.";	
		}
		if ($id == -1)
		{
			return $this->list_action_view();
		}
		
		ORDataObject::factory_include('User');
		$u = User::fromId($id);

		$this->assign('user',$u);

		
		$list = $this->security->getGroups();
		$groups = array();
		foreach($list as $key => $name)
		{
			$sel = "";
			if (isset($u->groups[$key])) {
				$sel = "SELECTED";
			}
			$groups[$key] = array('id'=>$key,'value'=>$name,'selected'=>$sel);
		}
		$this->assign("groups",$groups);
		
		$this->assign("TOP_ACTION", Celini::link("edit")."user_id=$id");
	
		return $this->view->render('edit.html');	
	}
	
	function add_action_edit() {
		return $this->edit_action_edit(0);
	}
	
	function edit_action_process($id) {
		if ($_POST['process'] != "true")
			return;

		ORDataObject::factory_include('User');
		$u = User::fromId($id);
		$u->populate_array($_POST);
		if (isset($_POST['groups'])) {
			$u->groups = $_POST['groups'];
		}
		$u->persist();

		$this->messages->addMessage("User updated successfully","");
	}
}

?>
