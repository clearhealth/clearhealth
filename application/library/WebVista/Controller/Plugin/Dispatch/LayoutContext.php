<?php
/*****************************************************************************
*       LayoutContext.php
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


class WebVista_Controller_Plugin_Dispatch_LayoutContext extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if (preg_match('/(.*)\.popup$/',$request->getControllerName(),$matches)) {
			Zend_Layout::getMvcInstance()->setInflectorTarget('../../views/scripts/:script.popup.:suffix');
			$request->setControllerName($matches[1]);
		}
		else if (preg_match('/(.*)\.raw$/',$request->getControllerName(),$matches)) {
			Zend_Layout::getMvcInstance()->setInflectorTarget('../../views/scripts/:script.raw.:suffix');
			$request->setControllerName($matches[1]);
		}
		else if (preg_match('/(.*)\.xml$/',$request->getControllerName(),$matches)) {
			header ("content-type: text/xml");
			Zend_Layout::getMvcInstance()->setInflectorTarget('../../views/scripts/:script.xml.:suffix');
			$request->setControllerName($matches[1]);
		}
	}
}
