<?php
$loader->requireOnce("lib/PEAR/Mail.php");
$loader->requireOnce("/lib/PEAR/Mail/mime.php");

/**
* Simple class for sending emails
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/
class Email {

	/**
	 * Email body
	 */
	var $body;

	/**
	 * Email to addresses
	 */
	var $addresses = array();

	/**
	 * Email from
	 */
	var $from = "test";

	/**
	 * Email subject
	 */
	var $subject;

	/**
	 * Send HTML email
	 */
	var $html = false;



	var $prepared_body;
	var $prepared_from;
	var $prepared_addresses;
	var $prepared_subject;
	var $parameters;


	function prepare($params=array()){
		if(!is_array($params)){
			$this->error = "Email::prepare(): Invalid parameters!";
			return FALSE;
		}

		$this->prepared_body = '';
		$this->prepared_subject = '';
		$this->prepared_from = '';

		$this->parameters = $params;

		if($this->_prepare('body') === FALSE) {
			return FALSE;
		}

		if($this->_prepare('subject') === FALSE) {
			return FALSE;
		}

		if($this->_prepare('from') === FALSE) {
			return FALSE;
		}

		if($this->_prepareAddresses() === FALSE) {
			return FALSE;
		}

		return TRUE;
	}

	function _prepare($type) {
		$text = $this->$type;
		foreach($this->parameters as $key => $value){
			if(!is_array($value))
				$text = preg_replace("/\|".$key."\|/", $value, $text);
		}

		$out = "prepared_$type";
		$this->$out = $text;

		return TRUE;
	}

	function _prepareAddresses(){

		/*if(intval($this->id) <= 0){
			$this->error .= "Email::prepareAddresses(): Invalid notification id setup";
			return FALSE;
		}

		$na = new EmailAddress();
		$addresses = $na->getForEmail($this->id);
		if($addresses === FALSE){
			$this->error = "Error getting notification addresses: ".$na->getLastError();
			return FALSE;
		}*/

		$address_string = "";
		foreach($this->addresses as $address) {
			$text = $address;
			if(strstr($text, "|") !== FALSE){
				foreach($this->parameters as $key => $value) {
					if(is_array($value) && strstr($text,"|$key|")) {
						$text = implode(',', $value);
					}
					else {
						$text = preg_replace("/\|".$key."\|/", $value, $text);
					}
				}
			}
			$address_string .= $text.", ";
		}
		$address_string = substr($address_string, 0, strlen($address_string)-2);
		$this->prepared_addresses = $address_string;

		return TRUE;
	}

	/**
	 * Send an email message
	 */
	function send($attachments = NULL, $headers = array()) {
		if(empty($this->prepared_body) || empty($this->prepared_addresses)) {
			$this->error = "Email::send(): Skipping message with empty body or addresses!";
			var_dump($this);
			var_dump($this->parameters);
			return FALSE;
		}

		if($headers != NULL && !is_array($headers)) {
			$this->error = "Email::send(): Invalid headers provided!";
			return FALSE;
		}
		else {
			$found_from = FALSE;
			$found_subject = FALSE;
			foreach($headers as $header => $value){
				if(strtolower($header) == "from")
					$found_from = TRUE;
				elseif(strtolower($header) == "subject")
					$found_subject = TRUE;
			}
			if(!$found_from)
				$headers['From'] = $this->prepared_from;
			if(!$found_subject)
				$headers['Subject'] = $this->prepared_subject;
		}

		$mime = new Mail_mime();
		if ($this->html) {
			$mime->setHTMLBody($this->prepared_body);
		}
		else {
			$mime->setTXTBody($this->prepared_body);
		}

		if(is_array($attachments) && count($attachments) > 0) {
			foreach($attachments as $attach) {
				if(empty($attach['data']) || empty($attach['name'])) {
					$this->error = "Email::send(): Invalid attachment!";
					return FALSE;
				}
				if(!isset($attach['type'])) $attach['type'] = 'application/octet-stream';
				if(!isset($attach['encoding'])) $attach['encoding'] = 'base64';
				$result = $mime->addAttachment($attach['data'], $attach['type'], $attach['name'], FALSE, $attach['encoding']);
				if(PEAR::IsError($result)) {
					$this->error = "Email::send(): Error adding attachment: ".$result->getMessage();
					return FALSE;
				}
			}
		}

		$message = $mime->get();
		$headers = $mime->headers($headers);
		$mailer = Mail::factory('mail');
		$result = $mailer->send($this->prepared_addresses, $headers, $message);
		if(PEAR::IsError($result)) {
			$this->error = "Email::send(): Error sending message: ".$result->getMessage();
			return FALSE;
		}

		return TRUE;
	}

}
?>
