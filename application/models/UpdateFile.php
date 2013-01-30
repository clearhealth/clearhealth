<?php
/*****************************************************************************
*       UpdateFile.php
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


class UpdateFile extends WebVista_Model_ORM {

	protected $updateFileId;
	protected $name;
	protected $dateTime;
	protected $md5sum;
	protected $version;
	protected $active;
	protected $description;
	protected $channelId;
	protected $channel;
	protected $license;
	protected $status = 'New';
	protected $queue;
	protected $notes;

	protected $_table = 'updateFiles';
	protected $_primaryKeys = array('updateFileId');

	protected $_fd = null; // file descriptor
	protected $_tables = array(); // list of all existing tables
	protected $_changes = array(); // diff results container

	const USER_CHANNEL_ID = 0;
	const USER_CHANNEL = 'User Channel';

	public function persist() {
		$filename = $this->getUploadFilename();
		$version = (int)$this->version;
		if ($version <= 0) {
			$this->version = $this->getLatestVersion() + 1;
		}
		$ret = parent::persist();
		if ($ret && $this->_persistMode == self::DELETE && file_exists($filename)) {
			unlink($filename);
		}
		return $ret;
	}

	public function setUpdateFileId($val) {
		$updateFileId = (int)$val;
		$this->updateFileId = $updateFileId;
	}

	public function getIterator($sqlSelect = null) {
		if ($sqlSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from($this->_table)
					->order('dateTime DESC');
		}
		return parent::getIterator($sqlSelect);
	}

	public function getIteratorActive($sqlSelect = null) {
		if ($sqlSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from($this->_table)
					->where('active = 1')
					->order('channel ASC')
					->order('version DESC');
		}
		return $this->getIterator($sqlSelect);
	}

	public function getLatestVersion() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('channel = ?',$this->channel)
				->order('version DESC')
				->limit(1);
		$version = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$version = $row['version'];
		}
		return $version;
	}

	public function getUploadDir() {
		return Zend_Registry::get('basePath').'data'.DIRECTORY_SEPARATOR.'updates'.DIRECTORY_SEPARATOR;
	}

	public function getUploadFilename() {
		return $this->getUploadDir().$this->updateFileId.'.xml';
	}

	public function getAllVersions() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,array('name','MAX(version) AS version','channelId'))
				->where('channelId != ?',self::USER_CHANNEL_ID)
				->group('channelId')
				->group('name');
		$versions = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$versions[] = $row;
			}
		}
		return $versions;
	}

	public function verify($filename) {
		set_time_limit(0);
		if (!file_exists($filename)) {
			throw new Exception('File '.$filename.' does not exists');
		}
		$zd = gzopen($filename,'r');
		if (!$zd) {
			throw new Exception('Could not open gzip file '.$filename);
		}
		$file = $this->getUploadFilename();
		$fp = fopen($file,'w');
		if (!$fp) {
			throw new Exception('Could not write file '.$file);
		}
		$tfile = $file.'.tmp';
		$tfp = fopen($tfile,'w');
		if (!$tfp) {
			throw new Exception('Could not write temporary file '.$tfile);
		}
		$signatureTag = '';
		$licenseTag = '';
		$notes = array();
		$dumpStarted = false;
		while (!gzeof($zd)) {
			$buffer = gzgets($zd,4096);
			$include = true;
			if (!$dumpStarted && substr($buffer,0,10) == '<mysqldump') $dumpStarted = true;
			if (!$dumpStarted) {
				if ($signatureTag == '' && substr($buffer,0,11) == '<signature>') {
					$signatureTag = $buffer;
					continue;
				}
				else if (isset($licenseTagComplete) && !$licenseTagComplete) {
					$licenseTag .= $buffer;
					if (substr($buffer,-10) == '</license>') $licenseTagComplete = true;
					$include = false;
				}
				else if ($licenseTag == '' && substr($buffer,0,9) == '<license>') {
					$licenseTag = $buffer;
					$licenseTagComplete = false;
					if (substr($buffer,-10) == '</license>') $licenseTagComplete = true;
					$include = false;
				}
				else {
					$tagName = null;
					$pos = strpos($buffer,'>');
					if ($pos !== false && $pos > 0) {
						$tagName = substr($buffer,1,($pos-1));
						$x = explode(' ',$tagName);
						$tagName = $x[0];
					}
					if ($tagName !== null) {
						if (substr($tagName,0,4) != '?xml') {
							$include = false;
							$notes[$tagName] = $buffer;
						}
					}
					else {
						$notes[] = $buffer;
					}
				}
			}
			if ($include) fwrite($tfp,$buffer);
			fwrite($fp,$buffer);
		}
		gzclose($zd);
		fclose($fp);
		fclose($tfp);
		$signature = strip_tags($signatureTag);
		if ($signature == '') {
			throw new Exception('Invalid signature');
		}

		$hash = hash_file('sha256',$file);
		$keyFile = Zend_Registry::get('basePath');
		$keyFile .= Zend_Registry::get('config')->healthcloud->updateServerPubKeyPath;
		$serverPublicKey = file_get_contents($keyFile);
		$publicKey = openssl_get_publickey($serverPublicKey);
		openssl_public_decrypt(base64_decode($signature),$verifyHash,$publicKey);
		openssl_free_key($publicKey);
		$verifyHash = trim($verifyHash);
		if ($hash !== $verifyHash) {
			$error = __('Data verification with signature failed.');
			trigger_error($error);
			throw new Exception($error);
		}
		if (!rename($tfile,$file)) {
			$error = __('Failed to rename update file.');
			trigger_error($error);
			throw new Exception($error);
		}
		$this->notes = serialize($notes);
		$this->license = strip_tags($licenseTag);
		$this->persist();
		return true;
	}

	public function getIteratorByQueue() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('queue = 1')
				->order('dateTime DESC');
		return parent::getIterator($sqlSelect);
	}

	public function install() {
		$filename = $this->getUploadFilename();
		if (file_exists($filename)) {
			$size = sprintf("%u",filesize($filename));
			$units = array('B','KB','MB','GB','TB');
			$pow = floor(($size?log($size):0)/log(1024));
			$pow = min($pow,count($units)-1);
			$size /= pow(1024,$pow);
			if (($pow == 2 && round($size,1) > 10) ||$pow > 2) { // queue if > 10 MB
				$this->queue = 1;
				$this->status = 'Pending';
				$this->persist();
			}
		}
		$audit = new Audit();
		$audit->objectClass = 'UpdateManager';
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->message = 'License of update file '.$this->name.' from '.$this->channel.' channel was accepted';
		$audit->dateTime = date('Y-m-d H:i:s');

		if ($this->queue) {
			$audit->message .= ' and updates pending to apply.';
			$ret = true;
		}
		else {
			$this->queue = 0;
			$alterTable = new AlterTable();
			$ret = $alterTable->generateSqlChanges($filename);
			if ($ret === true) {
				$alterTable->executeSqlChanges();
				//$this->active = 0;
				$this->status = 'Completed';
				$this->persist();
				$audit->message .= ' and updates applied successfully.';
			}
			else {
				$audit->message .= ' and updates failed to apply.';
				$this->status = 'Error: '.$ret;
				$this->persist();
			}
		}
		$audit->persist();
	}

}
