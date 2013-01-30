<?php
/*****************************************************************************
*       Menu.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * Menu Model
 */
class Menu {

    protected static $_instance = null;
    protected static $_arrMenus = array();
    protected static $_arrCollectionMenus = array();


	public static function generateMenus($menu = 'default',$attributes = array('name' => '','absolutePosition' => 'auto','mixedImages' => 'yes')) {
		static $_menus = array();

		if (!strlen($menu) > 0) {
			throw new Exception("Menu argument must be a string referencing a valid activity tab");
			return;
		}

		$rootTag = '<menu';
		foreach ($attributes as $k => $v) {
			$rootTag .= " {$k}=\"{$v}\"";
		}
		$rootTag .= ' />';
		$xml = new SimpleXMLElement($rootTag);

		$name = 'Menu';
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		self::_generateEnumerationTree($xml,$enumeration->enumerationId);

		return $xml->asXML();
	}

	protected static function _generateEnumerationTree(SimpleXMLElement $xml,$enumerationId) {
		$db = Zend_Registry::get('dbAdapter');
		static $enumerationList = array();
		$enumerationsClosure = new EnumerationsClosure();

		$enumeration = new Enumeration();
		$dbSelect = $db->select()->from(array('e'=>$enumeration->_table))
			       ->join(array('ec'=>'enumerationsClosure'),'e.enumerationId = ec.descendant')
			       ->join(array('m'=>'mainmenu'),'m.menuId = e.ormId',array('menuId','title','siteSection'))
			       ->where('ec.ancestor = ?',(int)$enumerationId)
			       ->where('ec.ancestor != ec.descendant')
			       ->where('ec.depth = 1')
			       ->where('e.active = 1')
			       ->order('ec.weight ASC')
			       ->order('m.displayOrder ASC');

		if ($rowset = $db->fetchAll($dbSelect)) {
			$item = null;
			foreach ($rowset as $row) {
				if (in_array($row['enumerationId'],$enumerationList)) {
					continue;
				}
				$enumerationList[] = $row['enumerationId'];
				if ($item === null) {
					$item = $xml;
				}
				$leaf = $item->addChild('MenuItem');
				$idName = preg_replace('/\ |[^A-Za-z]/','_',strtolower($row['title']));
				$menuId = "{$row['siteSection']}_{$idName}";
				$leaf->addAttribute('id',$menuId);
				$leaf->addAttribute('height',25);
				$leaf->addAttribute('name',$row['title']);
				if (!self::userHasPermissionForMenuItem($row['menuId'])) {
					$leaf->addAttribute('disabled','disabled');
				}
				if ($enumerationId != $row['enumerationId']) { // prevents infinite loop
					self::_generateEnumerationTree($leaf,$row['enumerationId']);
				}
			}
		}
	}


	public static function generateCurrentMenu() {
		return Menu::generateMenus(Menu::getCurrentlySelectedActivityGroup());
	}


    public static function generateMenu(
			$menu = 'default', 
			$attributes = array(
				'name' => '', 
				'absolutePosition' => 'auto', 
				'mixedImages' => 'yes')) {

        static $_menus = array();

        if (!strlen($menu) > 0) {
		throw new Exception("Menu argument must be a string referencing a valid activity tab");
            return;
        }
        if (!isset($_menus[$menu])) {
            $_menus[$menu]['MenuItem'] = array();
            $db = Zend_Registry::get('dbAdapter');
            $dbSelect = $db->select()->from('mainmenu')
			   ->joinLeft(array('child'=>'mainmenu'), 'mainmenu.menuId = child.parentId')
			   ->where('mainmenu.parentId = 0 AND child.menuId IS NOT NULL')
			   ->where("mainmenu.siteSection = ? OR mainmenu.siteSection = 'default'",$menu)
			   ->where("mainmenu.active = 1")
			   ->where("child.active = 1")
			   ->order('mainmenu.siteSection')
			   ->order('mainmenu.displayOrder ASC')
			   ->order('child.section')
			   ->order('child.displayOrder ASC');
            if ($rowset = $db->fetchAll($dbSelect)) {
                $parentIds = array();
                $currentParentId = null;
                foreach ($rowset as $row) {
                    if ($currentParentId !== null && $row['parentId'] != $currentParentId) {
                        $_menus[$menu]['MenuItem'][] = $parentIds[$currentParentId];
                    }
                    if (!isset($parentIds[$row['parentId']])) {
                        $currentParentId = $row['parentId'];
                        $parentSql = $db->quoteInto('SELECT mainmenu.menuId, mainmenu.title, mainmenu.siteSection, mainmenu.prefix FROM mainmenu WHERE menuId = ?', $row['parentId']);
                        if ($parentRow = $db->fetchRow($parentSql)) {
                            $parentTmpMenu = array();
                            $parentTmpMenu['name'] = $parentRow['title'];
			    $idName = preg_replace('/\ |[^A-Za-z]/','_',strtolower($parentRow['title']));
                            $parentTmpMenu['id'] = "{$parentRow['siteSection']}_{$idName}";
                            $parentTmpMenu['height'] = '25';
                            if (!self::userHasPermissionForMenuItem($parentRow['menuId'])) {
                                $parentTmpMenu['disabled'] = 'disabled';
                            }
                            $parentIds[$row['parentId']] = $parentTmpMenu;
                        }
                    }

                    $tmpMenu = array();
                    $tmpMenu['name'] = $row['title'];
		    $idName = preg_replace('/\ |[^A-Za-z]/','_',strtolower($row['title']));
                    $tmpMenu['id'] = "{$parentRow['siteSection']}_{$idName}";
                    $tmpMenu['height'] = '25';
                    if (!self::userHasPermissionForMenuItem($row['menuId'])) {
                        $tmpMenu['disabled'] = 'disabled';
                    }
                    $parentIds[$row['parentId']]['MenuItem'][] = $tmpMenu;
                    $lastParentRow = $row['parentId'];
                }
                if (isset($lastParentRow)) {
                    $_menus[$menu]['MenuItem'][] = $parentIds[$lastParentRow];
                }
            }
        }
        /*
        Array
        (
        [0] => Array
            (
                [menu_id] => 57
                [site_section] => patient
                [parent] => 1
                [dynamic_key] => 
                [section] => children
                [display_order] => -1
                [title] => Encounter Forms
                [action] => 
                [prefix] => main/Encounter
            )
        */

        $rootTag = '<menu';
        foreach ($attributes as $k => $v) {
            $rootTag .= " {$k}=\"{$v}\"";
        }
        $rootTag .= ' />';
        $xml = new SimpleXMLElement($rootTag);
        Menu::addChild($_menus[$menu], $xml);
        return $xml->asXML();
    }

    public static function addChild($data, SimpleXMLElement $xml)
    {
        foreach ($data as $tagName => $tagValue) {
            if (is_numeric($tagName)) {
                Menu::addChild($tagValue, $xml);
                continue;
            }
            if (!is_array($tagValue)) {
                $child = $xml->addChild($tagName, (string)$tagValue);
                continue;
            }

            foreach ($tagValue as $key => $value) {
                $child = $xml->addChild($tagName);
                // Do we need this?
                if (!is_array($value)) {
                    $child->addAttribute($key, (string)$value);
                    continue;
                }
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        Menu::addChild(array($k => $v), $child);
                    } else {
                        $child->addAttribute($k, (string)$v);
                    }
                }
            }
        }

        /* this is the recently working code
        if (isset($data[$this->_menuTag])) {
            foreach ($data[$this->_menuTag] as $key => $value) {
                $child = $xml->addChild($this->_menuTag);
                foreach ($value as $k => $v) {
                    if ($k == $this->_menuTag) {
                        Menu::addChild(array($k => $v), $child);
                    } else {
                        $child->addAttribute($k, (string)$v);
                    }
                }
            }
        }
        */
    }

    /**
     * Get the current user's role
     */
    public static function getCurrentUserRole()
    {
        return 'superAdmin';
    }

    /**
     * Checks whether user has permission for menu item
     *
     * @param   int         Menu Item ID
     * @return  boolean     TRUE if user has permission, FALSE otherwise
     */
    public static function userHasPermissionForMenuItem($menuId)
    {
        $ret = false;
        if (self::getCurrentUserRole() == 'superAdmin') {
            $ret = true;
        }
        return $ret;
    }

	/**
	 * Set the currently selected activity group.
	 */
	public static function setCurrentlySelectedActivityGroup($activityGroup) {
		$_SESSION['currentlySelectedActivityGroup'] = $activityGroup;
	}

	/**
	 * Get the currently selected activity group.
	 */
	public static function getCurrentlySelectedActivityGroup() {
		return isset($_SESSION['currentlySelectedActivityGroup'])?$_SESSION['currentlySelectedActivityGroup']:'Provider';
	}

    /**
     * Retrieves menu from database and format in XML
     *
     * @return  String      XML menu format
     */
    public static function getXmlMenu() {

        $strXml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
        $strXml .= "\n<clearhealth>\n<menu>";

        $db = Zend_Registry::get('dbAdapter');
	$dbSelect = $db->select()
		       ->from('mainmenu')
		       ->order('siteSection')
		       ->order('displayOrder ASC');
        if ($rowset = $db->fetchAll($dbSelect)) {
            // retrieve all records and store it on arrCollectionMenus
            foreach ($rowset as $row) {
                // check for the root menu and exclude it in the list
                if ($row['menuId'] == $row['parentId']) {
                    continue;
                }
                $strXml .= "\n\t<menuItem>";
                foreach ($row as $k=>$v) {
                    $strXml .= "\n\t\t<$k>$v</$k>";
                }
                $strXml .= "\n\t</menuItem>";
            }
        }
        $strXml .= "\n</menu>\n</clearhealth>";
        return $strXml;
    }

    /**
     * Retrieves menu from database and format in JSON
     *
     * @return  String      JSON representation of menu
     */
    public static function generateJsonMenu() {

        $sql = "SELECT * FROM mainmenu ORDER BY siteSection, displayOrder ASC";
        $db = Zend_Registry::get('dbAdapter');
        if ($rowset = $db->fetchAll($sql)) {
            // retrieve all records and store it on arrCollectionMenus
            foreach ($rowset as $row) {
                // check for the root menu and exclude it in the list
                if ($row['menuId'] == 0) {
                    $rootMenuId = 0;
                    $row['menuId'] = 'menu';
                    $rootData = array();
                    $rootData['id'] = $row['menuId'];
                    $rootData['text'] = $row['title'];
                    continue;
                }
                $tmpMenu = array();
                $tmpMenu['id'] = $row['menuId'];
                $tmpMenu['text'] = $row['title'];
                self::$_arrCollectionMenus[$row['parentId']][] = $tmpMenu;
            }
            // it must have a rootMenuId
            if (isset($rootMenuId)) {
                // retrieve rootMenus from arrCollectionMenus.
                self::$_arrMenus = self::$_arrCollectionMenus[$rootMenuId];
                // remove rootMenus from arrCollectionMenus.
                unset(self::$_arrCollectionMenus[$rootMenuId]);
                // traverse the root menus and align it based on levels.
                foreach (self::$_arrMenus as $key=>$menu) {
                    self::alignMenus(self::$_arrMenus[$key]);
                }
            }
        }

        $strJson = "{id:'0', item:[\n";
        if (isset($rootData)) {
            $strJson .= "{id:'{$rootData['id']}', text:'{$rootData['text']}', item:[\n";
        }
        $strJson .= self::traverseArray(self::$_arrMenus);
        if (isset($rootData)) {
            $strJson .= "]}\n";
        }
        $strJson .= "]}\n";
        return $strJson;
    }

    /**
     * Traverse menu to setup/create JSON representation
     *
     * @param   Array   $menus      Source array of menus
     * @return  String              JSON representation of menus
     */
    protected static function traverseArray($menus) {
        $strJson = '';
        $i = 0;
        foreach ($menus as $menu) {
            if ($i != 0) {
                $strJson .= ",";
            }
            $i++;
            $strJson .= "{id:'{$menu['id']}', text:'" . addslashes($menu['text']) . "', child:'1' ";
            //$strJson .= "{id:'{$menu['id']}', text:'" . addslashes($menu['text']) . "' ";
            if (isset($menu['item'])) {
                $strJson .= ", item:[" . self::traverseArray($menu['item']) . "]\n";
            }
            $strJson .= "}";
        }
        return $strJson;
    }

    /**
     * Align menus for multi-level
     *
     * @param   Array   $arrMenu    Reference to the array menu
     * @return  Boolean             True on success, false otherwise
     */
    protected static function alignMenus(&$arrMenu) {
        $ret = false;
        if (isset(self::$_arrCollectionMenus[$arrMenu['id']])) {
            $arrMenu['item'] = self::$_arrCollectionMenus[$arrMenu['id']];
            foreach ($arrMenu['item'] as $k=>$v) {
                if (!isset(self::$_arrCollectionMenus[$v['id']])) {
                    continue;
                }
                self::alignMenus($arrMenu['item'][$k]);
            }

            $ret = true;
        }
        return $ret;
    }

	public static function userHasPermissionForTab($tabName) {
		return true;
	}

	public static function getMainTabs($baseUrl='') {
		$mainTabs = array();
		$mainTabs['Calendar']['url'] = $baseUrl.'/calendar.raw';
		$mainTabs['Calendar']['hrefMode'] =  'ajax-html';
		$mainTabs['Provider']['url'] = $baseUrl.'/provider-dashboard.raw';
		$mainTabs['Provider']['hrefMode'] =  'ajax-html';
		$mainTabs['Station']['url'] = $baseUrl.'/station-dashboard.raw';
		$mainTabs['Station']['hrefMode'] =  'ajax-html';
		//$mainTabs['Patient']['url'] = "/index.php/minimal/PatientDashboard/View?patient_id=' + mainController.getActivePatient() + '";
		//$mainTabs['Patient']['hrefMode'] =  'iframe';
		$auth = Zend_Auth::getInstance();
		$userId = $auth->getIdentity()->userId;
		$user = new User();
		$user->userId = $userId;
		$user->populate();
		$mainTabs['Medications']['url']   = $baseUrl.'/medications.raw';
		$mainTabs['Medications']['hrefMode'] =  'ajax-html';
		$mainTabs['Problems']['url']   = $baseUrl.'/problem-list.raw';
		$mainTabs['Problems']['hrefMode'] =  'ajax-html';
		$mainTabs['Notes']['url']   = $baseUrl.'/clinical-notes.raw';
		$mainTabs['Notes']['hrefMode'] =  'ajax-html';
		$mainTabs['Vitals']['url']   = $baseUrl.'/vital-signs.raw';
		$mainTabs['Vitals']['hrefMode'] =  'ajax-html';
		$mainTabs['Labs']['url']   = $baseUrl.'/lab-results.raw';
		$mainTabs['Labs']['hrefMode'] =  'ajax-html';
		$mainTabs['Orders']['url']   = $baseUrl.'/orders.raw';
		$mainTabs['Orders']['hrefMode'] =  'ajax-html';
		$mainTabs['Claims']['url']   = $baseUrl.'/claims.raw';
		$mainTabs['Claims']['hrefMode'] =  'ajax-html';
		$mainTabs['A/R']['url']   = $baseUrl.'/accounts.raw';
		$mainTabs['A/R']['hrefMode'] =  'ajax-html';
		$mainTabs['Reports']['url']   = $baseUrl.'/reports.raw';
		$mainTabs['Reports']['hrefMode'] =  'ajax-html';
		//$mainTabs['Billing']['url']   = '/index.php/minimal/claim/list';
		//$mainTabs['Billing']['hrefMode'] =  'iframe';
		$mainTabs['Messaging']['url']   = $baseUrl.'/messaging.raw';
		$mainTabs['Messaging']['hrefMode'] =  'ajax-html';
		$mainTabs['Admin']['url']   = $baseUrl.'/admin.raw';
		$mainTabs['Admin']['hrefMode'] =  'ajax-html';

		foreach ($mainTabs as $tabName => $url) {
			if (self::userHasPermissionForTab($tabName) === false) {
				unset($mainTabs[$tabName]);
			}
		}
		return $mainTabs;
	}

}
