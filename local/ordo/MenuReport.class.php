<?php

/**
*	This class is a data model object for representation of report information.
*/

class MenuReport extends ORDataObject {

	var $id = 0;
	var $menu_id;
	var $report_template_id;
	var $title;
	var $_table = 'menu_report';
	var $_internalName='MenuReport';

	function MenuReport($id=0,$db = null) {
		parent::ORDataObject($db);	
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
		parent::populate('menu_report_id');
	}

	/**
	* Get n menu_report_id => title list for report menu items
	*/
	function getMenuList($menu_id = false,$jsformat=false) {
		if ($menu_id) {
			$this->set_menu_id($menu_id);
		}

		$res = $this->_execute("select menu_report_id, title, rt.report_template_id, r.label report_name, rt.name template_name 
			from $this->_prefix$this->_table 
			inner join {$this->_prefix}report_templates rt using(report_template_id)
			inner join {$this->_prefix}reports r on r.id = rt.report_id
			where menu_id=".(int)$this->get_menu_id()." order by title");
						
		$ret = $res->getAll();
		if ($jsformat) {
			$tmp = array();
			foreach($ret as $key => $val) {
				$tmp[] = implode("|",$val);
			}
			$ret = $tmp;
		}
		return $ret;
	}

	/**
	* Delete the current record
	*/
	function drop() {
		$this->_execute("delete from $this->_prefix$this->_table where menu_report_id = ".(int)$this->id);
	}

	/**
	* Utility method to create a new MenuReport entry from javascript
	*/
	function addMenuEntry($menu_id,$report_template_id,$title) {
		$m = new MenuReport();
		$m->set_menu_id($menu_id);
		$m->set_report_template_id($report_template_id);
		$m->set_title($title);
		$m->persist();
	}

	/**
	* Utility method to update an entry from javascript
	*/
	function updateMenuEntry($menu_report_id,$title) {
		$this->set_menu_report_id($menu_report_id);
		$this->set_title($title);
		$this->persist();
	}

	/**
	* Utility method to delete a menu entry
	*/
	function deleteMenuEntry($menu_report_id) {
		$this->set_menu_report_id($menu_report_id);
		$this->drop();
	}

	/**#@+
	*	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
	*/
	function get_menu_report_id()
	{
		return $this->id;
	}
	function set_menu_report_id($id)
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

	function get_report_template_id()
	{
		return $this->report_template_id;
	}
	function set_report_template_id($id)
	{
		$this->report_template_id = $id;
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
