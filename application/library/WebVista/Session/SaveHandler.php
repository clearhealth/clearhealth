<?php
/*****************************************************************************
*       WebVista_Session_SaveHandler.php
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


class WebVista_Session_SaveHandler implements Zend_Session_SaveHandler_Interface {

	protected $_sessionSavePath;
	protected $_sessionName;
	protected $_expiredSessions = array();

	/**
	 * Open Session - retrieve resources
	 *
	 * @param string $save_path
	 * @param string $name
	 * @return boolean
	 */
	public function open($savePath,$name) {
		// file based session storage similar to the PHP sessions default save handler files
		if (!strlen($savePath) > 0) {
			$savePath = '/tmp';
		}
		$this->_sessionSavePath = $savePath;
		$this->_sessionName = $name;
		return true;
	}

	/**
	 * Close Session - free resources
	 *
	 * @return boolean
	 */
	public function close() {
		// file based session storage similar to the PHP sessions default save handler files
		return true;
	}

	/**
	 * Read session data
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id) {
		// file based session storage similar to the PHP sessions default save handler files
		$return = '';
		$sessFile = "{$this->_sessionSavePath}/sess_{$id}";
		if (file_exists($sessFile)) {
			$return = (string) @file_get_contents($sessFile);
		}
		return $return;
	}

	/**
	 * Write Session - commit data to resource
	 *
	 * @param string $id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($id,$data) {
		if (isset($this->_expiredSessions[$id]) && $this->_expiredSessions[$id]) {
			$data = '';
			unset($this->_expiredSessions[$id]);
		}
		// file based session storage similar to the PHP sessions default save handler files
		$sessFile = "{$this->_sessionSavePath}/sess_{$id}";
		if ($fp = @fopen($sessFile,'w')) {
			$return = fwrite($fp,$data);
			fclose($fp);
			return $return;
		} else {
			return false;
		}
	}

	/**
	 * Destroy Session - remove data from resource for given session id
	 *
	 * @param string $id
	 * @return boolean
	 */
	public function destroy($id) {
		// invoke hook to session expired
		$sessFile = "{$this->_sessionSavePath}/sess_{$id}";
		if (file_exists($sessFile)) {
			Logout::hookExpiredSession($this->read($id));
  			return @unlink($sessFile);
		}
  		return false;
	}

	/**
	 * Garbage Collection - remove old session data older
	 * than $maxlifetime (in seconds)
	 *
	 * @param int $maxlifetime
	 * @return true
	 */
	public function gc($maxlifetime) {
		// file based session storage similar to the PHP sessions default save handler files
		foreach (glob("{$this->_sessionSavePath}/sess_*") as $filename) {
			if (filemtime($filename) + $maxlifetime < time()) {
				if (unlink($filename)) {
					$id = str_replace("{$this->_sessionSavePath}/sess_",'',$filename);
					$this->_expiredSessions[$id] = true;
					require_once 'Logout.php';
					require_once 'Zend/Auth.php';
					register_shutdown_function(array('Logout','hookExpiredSession'),$this->read($id));
				}
			}
		}
		return true;
	}

}
