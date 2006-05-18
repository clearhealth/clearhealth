<?php
class MediCalEligibilityChecker {

	var $cookies = false;

	var $curl;

	var $username = '';
	var $password = '';	

	var $loginUrl = 'https://www.medi-cal.ca.gov/Eligibility/Menu.asp?GoBack=';
	var $eligibilityUrl = 'https://www.medi-cal.ca.gov/Eligibility/Eligibility.asp';
	var $eligibilityPostUrl = 'https://www.medi-cal.ca.gov/Eligibility/EligResp.asp';
	var $start = '<!-- CONTENT AREA -->';
	var $end = '<!-- END CONTENT AREA -->';

	var $html = false;

	function MediCalEligibilityChecker() {
		// grab username/password from config
		$config =& Celini::configInstance();
		$this->username = $config->get('MediCalUsername');
		$this->password = $config->get('MediCalPassword');
	}

	function login() {
		$this->initCurl();
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_URL, $this->loginUrl);

		$post = 'JScriptTest=ENABLED&CookieTest=ENABLED&HelpField=5&Flag=1&UserID='.
				$this->username.'&UserPw='.$this->password.'&cmdSubmit=Submit';

		$headers = array('Cookie: UserID='.$this->username.'; UserPW='.$this->password.';',
				'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3',
				'Referer: https://www.medi-cal.ca.gov/eligibility/login.asp'
			);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS,$post);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($this->curl, CURLOPT_HEADER,1);

		$results = curl_exec($this->curl);

		preg_match_all('|Set-Cookie: (.*);|U', $results, $matches);
		$this->cookies = $matches[1];

		if (curl_errno($this->curl)) {
			var_dump(curl_error($this->curl));
		} 
	}

	function checkEligibility($subscriberId,$dob,$issueDate,$serviceDate) {
		$this->initCurl();
		curl_setopt($this->curl, CURLOPT_POST,1);
		curl_setopt($this->curl, CURLOPT_URL, $this->eligibilityPostUrl);
		$post = 'HelpField=8&SwipeCard=&UserID=&UserPW=&RecipID='.$subscriberId.
				'&RecipDOB='.urlencode($dob).'&RecipDOI='.urlencode($issueDate).'&RecipDOS='.urlencode($serviceDate);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS,$post);
		curl_setopt($this->curl, CURLOPT_HEADER,0);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR,APP_ROOT.'/tmp/cookiejar');

		$sep = '|';
		$data = $subscriberId.$sep.$dob.$sep.$issueDate.$sep.$serviceDate.$sep.str_repeat($sep,8);

		$cookie = 
			'PlugData='.urlencode($data).'; '.
			'RecipName='.urlencode('ID: ').$subscriberId.
			'; CookieTest=COOKIE'.
			'; UserID='.$this->username.'; UserPW='.$this->password;
		foreach($this->cookies as $c) {
			$cookie .= '; '.$c;
		}
		$headers = array('Cookie: '.$cookie,
			'Referer: https://www.medi-cal.ca.gov/Eligibility/Eligibility.asp',
			'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3',
			'Content-Type: application/x-www-form-urlencoded'
		);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER,$headers);

		$results = curl_exec($this->curl);

		if (curl_errno($this->curl)) {
			var_dump(curl_error($this->curl));
		} 

		//var_dump($results);

		$start = strpos($results,$this->start);
		$end = strpos($results,$this->end);
		$format = TimestampObject::getFormat();
		$html = '<h3>Check ran at: '.date(str_replace('%', '', $format)).'</h3>'.substr($results,$start,$end-$start);

		if(empty($html)) {
			$html = 'Error updating eligibility information';
		}

		$this->html = $html;
	}

	function getLastCheckOutput() {
		return $this->html;
	}

	function initCurl() {
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
	}
}
?>
