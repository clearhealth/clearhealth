<?php
/*****************************************************************************
*       LoginController.php
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


class LoginController extends WebVista_Controller_Action
{
    protected $_session;

    public function init()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

    public function indexAction()
    {
        $this->render();
    }
	public function panelAction() {
		$this->render();
	}

	public function completeAction() {
		$this->render('complete');
	}

	public function processAction() {
		$result = User::processLogin($_POST['username'],$_POST['password']);
		$data = array();
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			//$this->_redirect('login/complete');
			//$this->_forward('index','main');
			$data['msg'] = __("Login successful.");
			$data['code'] = 200;
		} else {
			//$this->_redirect('login');
			//$data['err'] = $result->getMessages();
			$data['code'] = 404;
		}

		header('Content-Type: application/xml;');
		$this->view->data = $data;
		$this->completeAction();
		//$this->render();
	}
}
