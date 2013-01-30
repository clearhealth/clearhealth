<?php
/********************************************************

xinetd file smtp:

service unlisted
              {
                     type                = UNLISTED
                     socket_type         = stream
                     protocol            = tcp
                     wait                = no
                     server              = /usr/bin/php
                     server_args         = daemons/handleSMTP.php
                     port                = 25
                     user                = root
              }

***********************************************************/
set_time_limit('90');


$appFile = realpath(dirname(__FILE__) . '/../application/models/HandleInboundMessages.php');
require_once $appFile;

function handleSMTPLog($data) {
	file_put_contents('/tmp/handle_smtp.log',"\n$data",FILE_APPEND);
}

function handleSMTPResponse($message) {
	handleSMTPLog("RESPONSE: $message");
	fwrite(STDOUT,$message."\n");
}

handleSMTPLog('START: '.date('c'));
handleSMTPResponse('220 clearhealth.local ESMTP');

$email = '';
$loadingData = false;

while ($data = fgets(STDIN)) {
	$data = str_replace("\r\n","\n",$data);
	$data = str_replace("\n\r","\n",$data);
	$data = str_replace("\r","\n",$data);

	handleSMTPLog($data);
	if (preg_match('/^QUIT.*/',$data)) {
		handleSMTPResponse('221 clearhealth.local');
		file_put_contents('/tmp/email.log',$email,FILE_APPEND);
		$tmpFile = tempnam('/tmp','ch30_email_');
		file_put_contents($tmpFile,$email."\n\n\n\n");

		$handler = HandleInboundMessages::getInstance();
		$handler->process($tmpFile);
		exit;
	}
	$fullStop = preg_match('/^\.$/',$data);
	if ($loadingData && $fullStop) {
		handleSMTPResponse('250 ok 1251934559 qp 9841');
		handleSMTPLog('250 Ok: queued');
		$loadingData = false;
		$data = '';
	}
	else if (!($loadingData && !$fullStop)) {
		if (preg_match('/^DATA.*/',$data)) {
			handleSMTPResponse('354 End data with <CR><LF>.<CR><LF>');
			handleSMTPLog('354 end data');
			$loadingData = true;
			$data = '';
		}
		else {
			handleSMTPResponse('250 Ok');
			if ($loadingData || preg_match('/^HELO.*/',$data) || preg_match('/^EHLO.*/',$data)) {
				handleSMTPLog('HELO/EHLO');
				$data = '';
			}
			else if (preg_match('/^RCPT TO:.*/',$data)) {
				handleSMTPLog('RCPT TO');
				$data = '';
			}
			else if (preg_match('/^MAIL FROM.*/',$data)) {
				handleSMTPLog('MAIL FROM');
				$data = '';
			}
			else if (preg_match('/^RSET.*/',$data) || preg_match('/^NOOP.*/',$data)) {
				handleSMTPLog('RSET/NOOP');
				$data = '';
			}
		}
	}
	$email .= $data;
}
