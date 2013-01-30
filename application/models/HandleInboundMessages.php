<?php
/*****************************************************************************
*       HandleInboundMessages.php
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


/*
 * This class is currently used by daemons/handlerSMTP.php and MUST be invoked in CLI
 */

$error = '';
if (PHP_SAPI != 'cli') {
	$error = 'This MUST be called using PHP CLI';
	echo $error;
	file_put_contents('/tmp/smtp.log',"\n$error",FILE_APPEND);
	trigger_error($error,E_USER_NOTICE);
	die;
}


// error handler function
function HandleErrorHandler($errNo,$errStr,$errFile,$errLine) {
	$error = "[$errNo] $errStr [$errFile : $errLine]\n";
	file_put_contents('/tmp/smtp.log',"\n$error",FILE_APPEND);
	switch ($errNo) {
		case E_USER_ERROR:
			exit(1);
			break;
		case E_USER_WARNING:
		case E_USER_NOTICE:
	}

	/* Don't execute PHP internal error handler */
	return true;
}

$oldHandler = set_error_handler('HandleErrorHandler');
if (strlen($oldHandler) > 0) {
	set_error_handler($oldHandler);
}


define('APPLICATION_ENVIRONMENT','production');

class HandleInboundMessages {

	protected static $_instance = null;
	protected $_paths = array();
	protected $messageFilename = '';

	public static function getInstance() {
        	if (null === self::$_instance) {
        		self::$_instance = new self();
			self::$_instance->init();
        	}
		return self::$_instance;
	}

	protected function getPath($key) {
		if (!isset($this->_paths['application'])) {
			$this->_paths['application'] = realpath(dirname(__FILE__) . '/..');
			$this->_paths['base'] = realpath(dirname(__FILE__) . '/../../');
			$this->_paths['library'] = $this->_paths['application'] . '/library';
			$this->_paths['models'] = $this->_paths['application'] . '/models';
			$this->_paths['controllers'] = $this->_paths['application'] . '/controllers';
		}
		$ret = null;
		if (isset($this->_paths[$key])) {
			$ret = $this->_paths[$key];
		}
		return $ret;
	}

	public function init() {
		file_put_contents('/tmp/smtp.log',"\ninit started",FILE_APPEND);
		error_reporting(E_ALL | E_STRICT);
		set_include_path($this->getPath('library') . PATH_SEPARATOR 
					. $this->getPath('models') . PATH_SEPARATOR
					. $this->getPath('controllers') . PATH_SEPARATOR
					. get_include_path());
		require_once 'Zend/Loader.php';
		Zend_Loader::registerAutoLoad();
		$config = new Zend_Config_Ini($this->getPath('application').'/config/app.ini',APPLICATION_ENVIRONMENT);
		Zend_Registry::set('config',$config);
		date_default_timezone_set($config->date->timezone);

		try {
			$dbConfig = $config->database;
			$dbAdapter = Zend_Db::factory($dbConfig);
			$dbAdapter->query("SET NAMES 'utf8'");
		}
		catch (Zend_Exception $e) {
			$error = $e->getMessage();
			file_put_contents('/tmp/smtp.log',"\n$error",FILE_APPEND);
			die($error);
		}
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter',$dbAdapter);

		file_put_contents('/tmp/smtp.log',"\ninit done",FILE_APPEND);
		return $this;
	}

	public function process($filename) {
		if (!file_exists($filename)) {
			$error = 'Filename '.$filename.' does not exists';
			file_put_contents('/tmp/smtp.log',"\n$error",FILE_APPEND);
			throw new Exception($error);
		}
		file_put_contents('/tmp/smtp.log',"\nprocess started...",FILE_APPEND);
		$this->messageFilename = $filename;
		return $this->_dispatch();
	}

	private function __construct() {}

	private function __clone() {}

	protected function _dispatch() {
		file_put_contents('/tmp/smtp.log',"\ndispatched started for filename: $this->messageFilename",FILE_APPEND);
		$contents = file($this->messageFilename);
		file_put_contents('/tmp/smtp.log',"\ncontents: ".print_r($contents,true),FILE_APPEND);
		$boundary = null;
		$boundaryLen = 0;
		$boundaryStarted = false;
		$data = array();
		$tmpData = array();
		$headers = array('subject'=>'','from'=>'','to'=>'','date'=>'','messageId'=>'');
		$contentsCtr = count($contents);
		$subjectStarted = false;
		for ($i = 0; $i < $contentsCtr; $i++) {
			$line = trim($contents[$i]);
			$nextI = $i + 1;
			if (isset($contents[$nextI]) && preg_match('/^Content-(.*):/i',$line) &&
			   ($contents[$nextI]{0} == "\t" || $contents[$nextI]{0} == ' ')) {
				$contents[$nextI]{0} = ' ';
				$line .= $contents[$nextI];
				$i = $nextI;
			}
			if (!$boundaryStarted) {
				if ($boundary !== null && '--'.$boundary == substr($line,0,($boundaryLen+2))) {
					$boundaryStarted = true;
					continue;
				}
				if (preg_match('/^Subject:(.*)/i',$line,$matches)) {
					$headers['subject'] = ltrim($matches[1]);
					$subjectStarted = true;
				}
				else if (preg_match('/^From:(.*)/i',$line,$matches)) {
					$headers['from'] = ltrim($matches[1]);
				}
				else if (preg_match('/^To:(.*)/i',$line,$matches)) {
					$headers['to'] = ltrim($matches[1]);
				}
				else if (preg_match('/^Content-Type:(.*)/i',$line,$matches)) {
					$contentType = ltrim($matches[1]);
					if (preg_match('/boundary=(.*)/i',$contentType,$matches)) {
						$boundary = $matches[1];
						if (preg_match('/^"(.*)"/',$boundary,$matches)) { // some boundaries are enclosed in ""
							$boundary = $matches[1];
						}
						$boundaryLen = strlen($boundary);
					}
				}
				else if (preg_match('/^Date:(.*)/i',$line,$matches)) {
					$headers['date'] = ltrim($matches[1]);
				}
				else if (preg_match('/^Message-Id:(.*)/i',$line,$matches)) {
					$headers['messageId'] = ltrim($matches[1]);
				}
				else if (preg_match('/^X-Mailer:(.*)/i',$line,$matches)) {
					$headers['mailer'] = ltrim($matches[1]);
				}
				else if ($subjectStarted) {
					$tmpData[] = $line;
				}
			}
			else {
				if (substr($line,0,($boundaryLen+2)) == '--'.$boundary) {
					if (isset($tmpData[0])) {
						$data[] = $tmpData;
						$tmpData = array();
					}
				}
				else {
					$tmpData[] = $line;
				}
			}
		}
		if (isset($tmpData[0])) {
			$data[] = $tmpData;
			$tmpData = array();
		}
		$dataContents = array();
		$i = 0;
		foreach ($data as $lines) {
			$content = '';
			$contentTypes = array();
			foreach ($lines as $line) {
				if (preg_match('/^Content-(.*):(.*)/i',$line,$matches)) {
					$contentTypes['content-'.strtolower($matches[1])] = ltrim($matches[2]);
				}
				else if ($line == '') {
					$lineStart = true;
					$lineEnd = true;
				}
				else {
					$content .= $line;
				}
			}
			if (isset($contentTypes['content-disposition']) && 
			   (isset($contentTypes['content-transfer-encoding']) && 
			    strtolower($contentTypes['content-transfer-encoding']) == 'base64')) { // temporarily accept base64 encoding that serves as an attachment
				$tmpData[] = array('contentTypes'=>$contentTypes,'content'=>$content);
				continue;
			}
			$dataContents[] = array('contentTypes'=>$contentTypes,'content'=>$content);
		}
		if (isset($tmpData[0])) {
			$dataContents = $tmpData;
		}
		$recipients = array();
		$addresses = explode(',',$headers['to']);
		$db = Zend_Registry::get('dbAdapter');
		foreach ($addresses as $address) {
			if (preg_match('/<(.*)>/',$address,$matches)) {
				$address = $matches[1];
			}
			$x = explode('@',$address);
			$username = $x[0];
			$domain = $x[1];
			$sqlSelect = $db->select()
					->from('patient','person_id')
					->where('record_number = ?',$username);
			$personId = 0;
			if ($row = $db->fetchRow($sqlSelect)) {
				$personId = $row['person_id'];
			}
			$recipients[] = array('personId'=>$personId,'address'=>$address,'username'=>$username,'domain'=>$domain);
		}
		file_put_contents('/tmp/smtp.log',"\ndata contents: ".print_r($dataContents,true),FILE_APPEND);
		foreach ($dataContents as $content) {
			$contentTypes = $content['contentTypes'];
			$filename = null;
			// email has an attachments
			if (isset($contentTypes['content-disposition']) && 
			   (isset($contentTypes['content-transfer-encoding']) && 
			    strtolower($contentTypes['content-transfer-encoding']) == 'base64')) { // temporarily accept base64 encoding that serves as an attachment
				$contentType = $contentTypes['content-type']; // image/jpeg; name="sample-signature.jpeg"
				$types = explode(';',$contentType);
				$mimeType = array_shift($types);
				foreach ($types as $type) {
					$type = ltrim($type);
					if (preg_match('/^name="(.*)"/',$type,$matches)) {
						$filename = '/tmp/'.$matches[1];
						break;
					}
				}
				if ($filename === null) { // try to create a random filename with specific extension based on mime type
					$extension = MimeType::extension($mimeType);
					$tmpFile = tempnam('/tmp','ch30_attach_');
					$filename = $tmpFile.'.'.$extension;
					//rename($tmpFile,$filename);
					unlink($tmpFile);
				}
				$content['content'] = base64_decode($content['content']); // decode the base64 encoded file
			}

			foreach ($recipients as $recipient) {
				$personId = $recipient['personId'];

				$messagingId = WebVista_Model_ORM::nextSequenceId();
				$messaging = array();
				$messaging['note'] = 'Email message: '.$content['content'];
				$attachmentId = 0;
				if ($filename !== null) {
					$attachmentId = WebVista_Model_ORM::nextSequenceId();
					$attachment = array();
					$attachment['attachmentId'] = $attachmentId;
					$attachment['attachmentReferenceId'] = md5($headers['messageId']);
					$attachment['name'] = basename($filename);
					$attachment['dateTime'] = date('Y-m-d H:i:s',strtotime($headers['date']));
					$attachment['mimeType'] = $mimeType;
					$attachment['md5sum'] = md5($content['content']);
					$db->insert('attachments',$attachment);
					$audit = array();
					$audit['objectClass'] = 'Attachment';
					$audit['objectId'] = $attachment['attachmentId'];
					$audit['auditValues'] = $attachment;
					Audit::persistManualAuditArray($audit);

					$attachmentBlob = array();
					$attachmentBlob['attachmentId'] = $attachment['attachmentId'];
					$attachmentBlob['data'] = $content['content'];
					$db->insert('attachmentBlobs',$attachmentBlob);
					$messaging['note'] = 'Scanned document for '.$recipient['address'];
				}

				$messaging['messagingId'] = $messagingId;
				$messaging['objectType'] = Messaging::TYPE_INBOUND_FAX;
				$messaging['status'] = 'Fax/Email Received';
				$messaging['dateStatus'] = date('Y-m-d H:i:s');
				$db->insert('messaging',$messaging);

				$messagingInboundFax = array();
				$messagingInboundFax['messagingId'] = $messaging['messagingId'];
				$messagingInboundFax['personId'] = $personId;
				$messagingInboundFax['mrn'] = $recipient['username'];
				$messagingInboundFax['subject'] = $headers['subject'];
				$messagingInboundFax['from'] = $headers['from'];
				$messagingInboundFax['to'] = $recipient['address'];
				$messagingInboundFax['messageId'] = $headers['messageId'];
				$messagingInboundFax['attachmentId'] = $attachmentId;
				$db->insert('messagingInboundFaxes',$messagingInboundFax);
				$audit = array();
				$audit['objectClass'] = 'MessagingInboundFax';
				$audit['objectId'] = $messagingInboundFax['messagingId'];
				$audit['auditValues'] = $messagingInboundFax;
				Audit::persistManualAuditArray($audit);
			}
		}
		unlink($this->messageFilename);
		return $this;
	}

}
