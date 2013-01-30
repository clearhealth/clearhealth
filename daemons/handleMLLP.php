<?php
set_time_limit('90');
file_put_contents("/tmp/connection","smtp connection\n",FILE_APPEND);
//echo "220 clearhealth.local ESMTP\n";
$loop = 0;
$data4 = '';
$loadingData = false;
$email = '';
while (true) {
	$read[] = STDIN;
	stream_select($read, $write = null, $except = null, $tv = 0);
	if (count($read)) {
		$data4 = @fread(STDIN, 32768);
		//$data4 = str_replace("\r\n", "\n", $data4);
		//$data4 = str_replace("\n\r", "\n", $data4);
		//$data4 = str_replace("\r", "\n", $data4);
		//$data4 = str_replace("\n", '', $data4);
	}
	$email .= @$data4;
//file_put_contents("/tmp/connection","stuff:" . @$data4 . "\n",FILE_APPEND);
	if (preg_match('/^\x{0B}.*\x{1C}/',@$data4,$match)) {
		file_put_contents("/tmp/connection","message:" . trim($match[0]) . "\n",FILE_APPEND);
		$str = chr(11) . "MSH|^~\&|HIS|System|Hosp|HL7 Genie|20071016055244||ACK^A01|A234242|P|2.3.1|\nMSA|AA|234242|Message Received Successfully|"  . chr(28) . chr(13);
	
		file_put_contents("/tmp/connection","output:" . $str . "\n",FILE_APPEND);
		echo $str;
	}
	//unset($data4);
	usleep('1000');
	$loop++;
}
