<?php
/*****************************************************************************
*       LogoutController.php
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


class LogoutController extends WebVista_Controller_Action {

	public function indexAction() {
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			$audit = new Audit();
			$audit->objectClass = 'Logout';
			$audit->userId = (int)$identity->personId;
			$audit->message = __('user') . ': ' . $identity->username . ' ' . __('logged out');
			$audit->dateTime = date('Y-m-d H:i:s');
			$audit->_ormPersist = true;
			$audit->persist();
			// audit only if logged in to avoid multiple logouts
		}
		$noRedirect = (int)$this->_getParam('noRedirection',0);
		Zend_Auth::getInstance()->clearIdentity();
		$_SESSION = array(); // clear all sessions
		// comment-out session destroy to give way the session expiration in WebVista_Session_SaveHandler hook
		//Zend_Session::destroy(true);
		if ($noRedirect === 0) {
			$this->_redirect('');
		}
		exit;
	}
}
