<?php
/**
* Menu datasource
*
* Note: in the source database you need a base entry with an id of 1 and an empty string for its site_section
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
* @package	com.clear-health.celini
*/
class Menu
{
	var $_db;
	var $_menu_array = false;
	var $_dyn = array();
	var $reportAction = 'report';
	var $formAction = 'form';
	var $attachReports = false;
	var $attachForms = false;
	var $currentSection = 'default';
	var $currentAction = false;
	var $attachArrays = false;
	var $_table = "menu";
	/**
	 * The application level database, in case the menu is stored in a different DB
	 *
	 * @var string
	 * @access private
	 */
	var $_appDb = '';

	function Menu() {
		if (isset($_SESSION['Menu']['dyn'])) {
			$this->_dyn = $_SESSION['Menu']['dyn'];
		}
		if (isset($GLOBALS['config']['menu']['attachReports'])) {
			$this->attachReports = $GLOBALS['config']['menu']['attachReports'];
		}
		if (isset($GLOBALS['config']['menu']['attachForms'])) {
			$this->attachForms = $GLOBALS['config']['menu']['attachForms'];
		}
		if (isset($GLOBALS['config']['menu']['attachArrays'])) {
			$this->attachArrays = $GLOBALS['config']['menu']['attachArrays'];
		}
		if (isset($GLOBALS['config']['menu']['database'])) {
			$conf = $GLOBALS['config']['menu']['database'];
			$this->_db = NewADOConnection($conf['db_type']);
			if (!$this->_db->NConnect($conf['db_host'], $conf['db_user'], $conf['db_password'], $conf['db_name'])) {
				trigger_error($this->_db->errorMsg());
			}
			$this->_db->SetFetchMode(ADODB_FETCH_ASSOC);
		}
		else {
			$this->_db =& $GLOBALS['frame']['adodb']['db'];
		}

		if (isset($GLOBALS['config']['db_name'])) {
			$this->_appDb = '`'.$GLOBALS['config']['db_name'] . '`.';
		}
	}

	/**
	* Singleton method for menu
	* @static
	*/
	function &getInstance() {
		if (!(isset($GLOBALS['singleton']['Menu']) && is_a($GLOBALS['singleton']['Menu'],'Menu'))) {
			$GLOBALS['singleton']['Menu'] =& new Menu();
		}
		return $GLOBALS['singleton']['Menu'];
	}

	/**
	* Turn a dynamic section on
	*/
	function activateDynamic($key,$title) {
		$this->_dyn[$key] = $title;
		$_SESSION['Menu']['dyn'][$key] = $title;
		$this->_menu_array = false;
	}

	/**
	* Turn a dynamic section off
	*/
	function disableDynamic($key) {
		unset($this->_dyn[$key]);
		if (isset($_SESSION['Menu']['dyn'][$key])) {
			unset($_SESSION['Menu']['dyn'][$key]);
		}
	}

	/**
	* Create the menu array
	*
	* @access private
	*/
	function _createArray() {
		$currInfo = false;
		$currAction = Celini::link(true,true,false,false,false,false);
		$currAction = strtolower(substr($currAction,1,strlen($currAction)-2));
		$currController = array_shift(explode("/",$currAction));
		$sec =& $GLOBALS['security'];

		$hide = array();
		$this->_db->SetFetchMode(ADODB_FETCH_ASSOC);

		$res = $this->_db->execute("select distinct site_section from menu where site_section not in ('a','all')");
		$all = array();
		while($res && !$res->EOF) {
			$all[] = $res->fields['site_section'];
			$res->MoveNext();
		}

		$res = $this->_db->execute('
			SELECT
				child.*
			FROM
				menu 
				LEFT JOIN menu AS child ON(menu.menu_id = child.parent)
			WHERE
				menu.parent = 1 and child.menu_id is not null
			ORDER BY
				menu.site_section, 
				menu.display_order ASC,
				child.section,
				child.display_order ASC
			');

		$this->_menu_array = array();
		while(is_object($res) && !$res->EOF) {
			$sections = array();
			if ($res->fields['site_section'] == 'all') {
				$sections = $all;
			}
			else {
				$sections[] = $res->fields['site_section'];
			}

			if ($res->fields['parent'] == 1) {
				if ($res->fields['dynamic_key'] !== "") {
					if (isset($this->_dyn[$res->fields['dynamic_key']])) {
						$res->fields['title'] = $this->_dyn[$res->fields['dynamic_key']];
					}
					else {
						$hide[$res->fields['menu_id']] = $res->fields['menu_id'];
					}
				}
				if (!isset($hide[$res->fields['menu_id']])) {
					if ($this->_secCheck($res->fields['action'],$res->fields)) {
						foreach($sections as $site_section) {
							$this->_menu_array[$site_section][$res->fields['menu_id']] = $res->fields;
						}
					}
				}
			}
			else {
				if (!isset($hide[$res->fields['parent']])) {
					if ($this->_secCheck($res->fields['action'],$res->fields)) {
						foreach($sections as $site_section) {
							$this->_menu_array[$site_section][$res->fields['parent']][$res->fields['section']][] = $res->fields;
						}
					}
				}
			}
			if (strtolower($res->fields['action']) === $currAction) {
				$currInfo = $res->fields;
			}

			if (strtolower(array_shift(explode('/',$res->fields['action']))) === $currController) {
				$this->currentSection = $res->fields['site_section'];
			}
			$res->MoveNext();
		}

		if ($this->attachReports) {
			// build attached reports
			$res = $this->_db->execute('
				SELECT
					*,
					mr.title
				FROM
					menu_report AS mr 
					INNER JOIN menu AS m USING(menu_id) 
					INNER JOIN ' . $this->_appDb . 'report_templates AS rt ON(mr.report_template_id = rt.report_template_id)
				ORDER BY mr.title');
			if ($res === false) {
				trigger_error($this->_db->errorMsg());
			}
			while($res && !$res->EOF) {
				$object_id = $sec->get_object_id('resources','report-'.$res->fields['title'],'axo');
				if($object_id === false || ($object_id !== false && Auth::canI('view','report-'.$res->fields['title']))) {
				if (isset($this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']])) {
					if (empty($res->fields['custom_action'])) {
						if(strpos($res->fields['prefix'],'/') === false) {
							$res->fields['action'] = 'Report/'.$this->reportAction . "/".$res->fields['report_id']."?template=".$res->fields['report_template_id'];
						} else {
							$res->fields['action'] = $this->reportAction . "/".$res->fields['report_id']."?template=".$res->fields['report_template_id'];
						}
					}
					else {
						$res->fields['action'] = $res->fields['custom_action'];
					}
					$this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['reports'][] = $res->fields;
					if (!isset($this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['children'])) {
						$this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['children'] = array();
					}
				}
				}
				$res->MoveNext();
			}
		}

		if ($this->attachForms) {
			// build attached forms
			$res = $this->_db->execute('
				SELECT 
					*,
					mf.title
				FROM
					menu_form AS mf
					INNER JOIN menu AS m USING(menu_id)
				ORDER BY mf.title');
			while($res && !$res->EOF) {
				$object_id = $sec->get_object_id('resources','form-'.$res->fields['title'],'axo');
				if($object_id === false || ($object_id !== false && Auth::canI('view','form-'.$res->fields['title']))) {
				if (isset($this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']])) {
					if (empty($res->fields['custom_action'])) {
						$res->fields['action'] = $this->formAction . "/".$res->fields['form_id'];
					}
					else {
						$res->fields['action'] = $res->fields['custom_action'];
					}

					$this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['forms'][] = $res->fields;
					if (!isset($this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['children'])) {
						$this->_menu_array[$res->fields['site_section']][$res->fields['menu_id']]['children'] = array();
					}
				}
				}
				$res->MoveNext();
			}
		}

		if ($this->attachArrays) {
			$this->_handleAttachArrays();
		}

		// remove empty groups
		foreach($this->_menu_array as $site_section => $section) {
			foreach($section as $group_id => $group) {
				if ( (!isset($group['children']) && empty($group['action'])) || (isset($group['children']) && !is_array($group['children'])) ||
		       			empty($group['title'])	) {
					unset($this->_menu_array[$site_section][$group_id]);
				}
			}
		}

		// add an entry with the current pages info
		if ($currInfo !== false) {
			$this->currentAction = $currInfo;
			$this->currentSection = $currInfo['site_section'];
		}
	}

	/**
	 * check if the current user has permission to perform the action
	 *
	 * @todo: fixme, need more permissions hints
	 */
	function _secCheck($action,$fields) {
		if (isset($GLOBALS['config']['autoAcl']) && $GLOBALS['config']['autoAcl'] == false) {
			return true;
		}
		if ($fields['prefix'] === 'RAW') {
			$action = str_replace(array('()',';','javascript:'),'',$action);
			$id = $GLOBALS['security']->get_object_id('resources',$action,'axo');
			if($id !== false) {
				return Auth::canI('view',$action);
			}
			return true;
		}
		if (empty($action)) {
			return true;
		}
		$resource = strtolower(array_shift(explode('/',$action)));
		$test = strtolower(array_pop(explode('/',$action)));

		$check = "view";
		switch($test) {
			case 'edit':
			case 'add':
			case 'override':
			case 'delete':
				$check = $test;
				break;
		}
		return Auth::canI($check,$resource);
	}

	/**
	* Get the menu in an array to render in smarty
	*/
	function toArray() {
		if ($this->_menu_array === false) {
			$this->_createArray();
		}
		//var_dump($this->_menu_array);
		return $this->_menu_array;
	}

	/**
	* Create a Simple Display Array
	*
	* Only top level sections are show -> site section -> section
	*
	* Single dimensional array is returned
	* array(menu_id => title)
	*/
	function toDisplayArray() {
		$this->_db->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $this->_db->execute('
			SELECT 
				*
			FROM
				menu
			WHERE
				menu.parent = 1 AND
				menu.menu_id != 1
			ORDER BY
				site_section,
				display_order');
		$site_section = false;

		$ret = array();
		while(!$res->EOF) {
			if ($res->fields['site_section'] !== $site_section) {
				$site_section = $res->fields['site_section'];
				$ret["ss-".$site_section] = ucfirst($site_section);
			}
			$ret[$res->fields['menu_id']] = $res->fields['title'];
			$site_section = $res->fields['site_section'];
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Get the site section the current page is in
	 */
	function getSection() {
		return $this->currentSection;
	}

	/**
	 * Get information about he current page
	 */
	function getCurrent() {
		return $this->currentAction;
	}

	function getMenuIdFromTitle($section,$title) {
		$sql = "
			SELECT
				menu_id 
			FROM 
				menu
			WHERE 
				title = ".$this->_db->qstr($title) ." AND
				site_section = ".$this->_db->qstr($section);
		$res = $this->_db->execute($sql);
		if ($res && !$res->EOF) {
			return $res->fields['menu_id'];
		}
	}

	/**
	 * Get data for a menu item
	 */
	function getMenuData($section,$menu_id) {
		if ($this->_menu_array === false) {
			$this->_createArray();
		}
		
		if (isset($this->_menu_array[$section][$menu_id])) {
			return $this->_menu_array[$section][$menu_id];
		}
		
		return "";
	}
	
	
	/**
	 * @access private
	 * @todo Document this method
	 */
	function _handleAttachArrays() {
		global $loader;
		// Bail out if we're in an application that doesn't support this...
		if ($loader->requireOnce('includes/MenuArray.php') === false) {
			return;
		}
		global $menuarrays;
		if (!isset($menuarrays) || !is_array($menuarrays)) {
			return;
		}
		foreach($menuarrays as $key=>$menuarray){
			$res = $this->_db->execute($menuarray['sql']);
			$x=0;
			$xmenus=array();
			while($res && !$res->EOF) {
				if($x==0){
					foreach($this->_menu_array as $menus){
						foreach($menus as $menu_id=>$menu){
							if(in_array($menu_id,$menuarray['menus'])){
								$site_section = $menu['site_section'];
								if ($site_section == 'all') {
									$site_section = $this->currentSection;
								}
								$xmenus[$menu_id]=$menu;
								$this->_menu_array[$site_section][$menu_id]['arrays']=array();
								$this->_menu_array[$site_section][$menu_id]['children']=array();
								$this->_menu_array[$site_section][$menu_id]['title']=$menuarray['menutitle'];
							}
						}
					}
				}
				$x++;
				foreach($xmenus as $menu_id=>$menu){
					$site_section = $menu['site_section'];
					if ($site_section == 'all') {
						$site_section = $this->currentSection;
					}
					$this->_menu_array[$site_section][$menu_id]['arrays'][]=array(
						'title'=>$res->fields['title'],
						'action'=>$res->fields['action'],
						'site_section'=>$site_section
					);
				}
				$res->MoveNext();
			}
		}
	}
}
?>
