<?php

require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";

/**
*	This class is a data model object for representation of form information.
*/

class MenuForm extends ORDataObject {

	var $id = 0;
	var $menu_id;
	var $form_id;
	var $title;

	function MenuForm($id=0,$db = null) {
		parent::ORDataObject($db);	
		$this->_table = "menu_form";
		$this->_sequence_name = "sequences";	
    	
		$this->id = $id;
		if (is_numeric($this->id) && $this->id != 0)
		{
			$this->populate();
		}
	}
    
	/**
	* Pull data for this record from the database
	*/
	function populate() {
		parent::populate('menu_form_id');
	}

	/**
	* Get n menu_form_id => title list for form menu items
	*/
	function getFormList($menu_id = false,$jsFormat = true) {
		if ($menu_id) {
			$this->set_menu_id($menu_id);
		}

		$res = $this->_execute("select menu_form_id, title, f.name, f.form_id
			from $this->_prefix$this->_table mf
			inner join {$this->_prefix}form f using(form_id)
			where menu_id=".(int)$this->get_menu_id()." order by title");
		$tmp = $res->getAll();
		$ret = array();
		if ($jsFormat) {
			foreach($tmp as $row) {
				$r = new stdClass();
				foreach($row as $key => $val) {
					$r->$key = $val;
				}
				$ret[] = $r;
			}
		}
		else {
			$ret = $tmp;
		}
		return $ret;
	}

	/**
	* Delete the current record
	*/
	function drop() {
		$this->_execute("delete from $this->_prefix$this->_table where menu_form_id = ".(int)$this->id);
	}

	/**
	* Utility method to create a new MenuForm entry from javascript
	*/
	function addMenuEntry($menu_id,$form_id,$title) {
		$m = new MenuForm();
		$m->set_menu_id($menu_id);
		$m->set_form_id($form_id);
		$m->set_title($title);
		$m->persist();
	}

	/**
	* Utility method to update an entry from javascript
	*/
	function updateMenuEntry($menu_form_id,$title) {
		$this->set_menu_form_id($menu_form_id);
		$this->set_title($title);
		$this->persist();
	}

	/**
	* Utility method to delete a menu entry
	*/
	function deleteMenuEntry($menu_form_id) {
		$this->set_menu_form_id($menu_form_id);
		$this->drop();
	}

	/**#@+
	*	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
	*/
	function get_menu_form_id()
	{
		return $this->id;
	}
	function set_menu_form_id($id)
	{
		return $this->id = $id;
	}

	function get_menu_id()
	{
		return $this->menu_id;
	}
	function set_menu_id($id)
	{
		$this->menu_id = $id;
	}

	function get_form_id()
	{
		return $this->form_id;
	}
	function set_form_id($id)
	{
		$this->form_id = $id;
	}

	function get_title()
	{
		return $this->title;
	}
	function set_title($title)
	{
		$this->title = $title;
	}
} 
?>
