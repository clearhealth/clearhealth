<?php
/*****************************************************************************
*       IndexController.php
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


class IndexController extends WebVista_Controller_Action
{
    public function indexAction()
    {
	$this->_redirect(Zend_Registry::get('config')->user->preferences->default_action);
        //$this->render();
    }

    public function deniedAction()
    {
        $this->render();
    }

	public function importUsersAction() {
		exit;
		$f = fopen('/tmp/newusers.csv','r');
		$counter = 0;
		while (($data = fgetcsv($f)) !== FALSE) {
			if ($counter == 0) { $counter++; continue; }
			echo $data[4] . "<br />";
			$user = new User();
			$user->username = $data[0];    
			$user->password = $data[1];    
			//$user->passphrase = $data[2];    
			$user->person->firstName = $data[3];    
			$user->person->lastName = $data[4];    
			$user->person->middleName = $data[5];    
			$user->person->defaultIdentifier = $data[6];    
			$user->person->defaultIdentifierType = $data[7];  
			$user->persist();
			//$user->person->persist(); 
			if ($data[8] == "PROVIDER") {
				$provider = new Provider();
				$provider->personId = $user->person->personId;
				$provider->type = "MD";
				$provider->providerIdentifier = $data[6];    
				$provider->providerIdentifierType = $data[7];  
				$provider->persist();
			}
			elseif ($data[8] == "STAFF") {
				//
			} 
			elseif ($data[8] == "ADMIN") {
				//
			} 
			//echo $user->toString();
			echo $data[11] . "\n";
			$counter++;
		}
		fclose($f);
		exit;
	}
	public function importPatientsAction() {
		$f = fopen('/tmp/Pacientes.csv','r');
		$counter = 0;
		//"recordNumber","lastName","firstName","middleName","gender (M, F, O)","initials","dateOfBirth (YYY-MM-DD)","email","defaultIdentifier","defaultIdentifierType (SHORT ALL CAPS IDENTIFIED FOR ID)","maritalStatus (S, M, D)"
		while (($data = fgetcsv($f)) !== FALSE) {
			if ($counter == 0) { $counter++; continue; }
			$patient = new Patient();
			$patient->_shouldAudit = false;
			$patient->confidentiality = "DEFAULT";    
			$patient->recordNumber = $data[0];    
			$patient->person->lastName = $data[1];    
			$patient->person->firstName = $data[2];    
			$patient->person->middleName = $data[3];    
			$patient->person->gender = $data[4];    
			$patient->person->initials = $data[5];    
			$patient->person->dateOfBirth = $data[6];    
			$patient->person->email = $data[7];    
			$patient->person->defaultIdentifier = $data[8];    
			$patient->person->defaultIdentifierType = $data[9];    
			$patient->person->maritalStatus = $data[10];   
			//echo "<pre>" . $patient->toString() . "<br /></pre>"; 
			echo $patient->person->firstName . " " .  $patient->person->lastName . "<br />";
			$patient->persist();
			
			//echo $user->toString();
			$counter++;
		}
		fclose($f);
		exit;
	}
	
	public function generateUsersKeysAction() {
		exit;
		//$f = fopen('/tmp/newusers.csv','r');
		//$counter = 0;
		//while (($data = fgetcsv($f)) !== FALSE) {
			//if ($counter == 0) { $counter++; continue; }
			echo $data[4] . "<br />"; 
			flush();
			$user = new User();
			$user->username = 'admin';    
			$user->populateWithUsername();
			$userKey = new UserKey();
			$userKey->userId = $user->userId;
			//field 2 is passphrase 
			$userKey->generateKeys('test passphrase');
			echo $user->toString();
			echo $data[0] . "\n";
			flush();
			$userKey->persist();
			$counter++;
		//}
		//fclose($f);
		exit;
	}
}
