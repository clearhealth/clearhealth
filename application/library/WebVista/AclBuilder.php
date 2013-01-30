<?php
/*****************************************************************************
*       AclBuilder.php
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


class WebVista_AclBuilder {

	protected $_acl;

	public function __construct() {
		$this->_acl = new Zend_Acl();
		$this->_findControllers();
	}
	
	protected function _findControllers() {
		$cDirPaths = Zend_Controller_Front::getInstance()->getControllerDirectory();
		$controllers = array();
		$privileges = array();

		foreach($cDirPaths as $cPath) {
			$d = dir($cPath);
			while(false !== ($entry = $d->read())) {
				preg_match_all('/(.*)\.php$/',$entry,$matches);
				if (! count($matches[0]) > 0) continue;
				$this->_acl->add(new Zend_Acl_Resource($matches[1][0]));
				include_once($cPath . "/" . $matches[1][0]  . ".php");
				$class = new ReflectionClass($matches[1][0]);
				$methods = $class->getMethods();
				foreach ($methods as $method) {
					if (preg_match('/(.*)Action$/',$method->name,$methodMatches)) {
						$privileges[$methodMatches[1]] = "";
					}
				}
				// $matches[1][0] . "<br/>";
			}
		}
		$this->_acl->addRole(new Zend_Acl_Role('administrator'),null,array_keys($privileges));
	}
	
}
