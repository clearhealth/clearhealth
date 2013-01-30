<?php
/*****************************************************************************
*       Login.php
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


require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Login extends PHPUnit_Extensions_SeleniumTestCase
{
	function setUp()  {
		$this->setBrowser('{"username": "clearhealth", "access-key": "42ca404e-5a02-4375-9dde-a6a3982f1ee0", "os": "Windows 2003", "browser": "firefox", "browser-version": "3.5"}') ;
		$this->setBrowserUrl ("https://sdemo.clear-health.com/");
		$this->setHost("saucelabs.com");
	}
	function testLogin() {
		$this->_login();
		sleep(5);
		$this->_selectProblemTab();
		sleep(5);
		$this->_selectPatient();
		sleep(5);
		$this->_problemListAddActive();
		sleep(5);
		$this->_problemListAnnotate();
		sleep(5);
		$this->_problemListEditActive();
		sleep(5);
		$this->_problemListEditInactive();
		sleep(5);
		$this->_problemListDeleteActive();
		sleep(5);
		$this->_problemListDeleteAdd();
		sleep(5);
		$this->_problemListDeleteInactive();
		sleep(5);
		$this->_logout();
	}
	function _login()  {
		$this->open("/30/main/index");
		$this->type("username", "admin");
		$this->type("password", "admin");
		$this->click("//input[@value='Login']");
		$this->waitForPageToLoad("30000");
		$this->assertEquals("Main / Index / Connected as admin", $this->getTitle());
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isVisible("//td[@id='menuItem_All_file']")) break;
			} catch (Exception $e) {}
			sleep(1);
		}
	}

	function _logout() {
		$this->clickAt("//td[@id='menuItem_All_file']", "");
		$this->clickAt("//td[@id='menuItem_All_quit']", "");
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ("Login / Index / Connected as Anonymous" == $this->getTitle()) break;
			} catch (Exception $e) {}
			sleep(1);
		}

		$this->assertEquals("Login / Index / Connected as Anonymous", $this->getTitle());
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isVisible("username")) break;
			} catch (Exception $e) {}
		sleep(1);
		}
	}

	function _selectProblemTab() {
		$this->clickAt("//div[@class='dhx_tabbar_row']/div[contains(.,'Problem')]", "");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isVisible("//div[@id='problemListToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td/img")) break;
			} catch (Exception $e) {}
			sleep(1);
		}
	}

	function _selectPatient() {
		$this->clickAt("//div[@id='mainToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) $this->fail("timeout");
			try {
				if ($this->isVisible("//input[@id='patientSelectAutoCompleteDiv']")) break;
			} catch (Exception $e) {}
			sleep(1);
		}
		$this->click("//td[2]/div/div[3]/div/div[1]");
		$this->click("patientSelectAutoCompleteDiv");
		$this->type("patientSelectAutoCompleteDiv","Test");
		// waitForSelectedIndex workaround?
		/*
		<tr>
			<td>waitForSelectedIndex</td>
			<td>patientSelectMultiSelect</td>
			<td>0</td>
		</tr>
		*/
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("patientSelectMultiSelect")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->click("//option[@value='65650']");
		$this->click("//input[@value='Select Patient']");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='mainToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td/div[1]/strong")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->assertElementContainsText("//div[@id='mainToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td/div[1]/strong","Test, One");
	}

	function _problemListAddActive() {
		$this->clickAt("//div[@id='problemListToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isVisible("//input[@id='q']")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->type("q","DMII");
		$this->click("//input[@id='searchLabel']");

		// wait for text here... workaround?
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->assertElementContainsText("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td","DMII KETOACD UNCONTROLD");

		$this->clickAt("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td");
		$this->click("//input[@value='Select']");
		$this->assertEquals("250.12",$this->getValue("//input[@id='problemList-code']"));
		$this->click("dateOfOnsetInput");
		$this->click("problemList-provider-id");
		$this->select("problemList-provider-id","label=Gooddoc, Horace F");
		$this->click("//option[@value='48502']");
		$this->click("//input[@id='problemList-status' and @name='problemList[status]' and @value='Active']");
		$this->click("problemList-immediacy");
		$this->answerOnNextPrompt("first comment");
		$this->click("//input[@value='Add']");
		$this->assertPrompt("Please enter your comment");
		$this->answerOnNextPrompt("yet another comment");
		$this->click("//input[@value='Add']");
		$this->assertPrompt("Please enter your comment");
		$this->click("//input[@value='Save']");
		sleep(1);
		$this->clickAt("//div[@id='problemsFilterContainer']/div[2]/table/tbody/tr[2]/td");

		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->assertElementContainsText("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td","Active");
	}

	function _problemListAnnotate() {
	}

	function _problemListEditActive() {
	}

	function _problemListEditInactive() {
	}

	function _problemListDeleteActive() {
		$this->clickAt("//div[@id='problemsFilterContainer']/div[2]/table/tbody/tr[2]/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->clickAt("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemListToolbar']/table/tbody/tr/td[4]/table[@class='itemDefault']/tbody/tr/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->clickAt("//div[@id='problemListToolbar']/table/tbody/tr/td[4]/table/tbody/tr/td");
	}

	function _problemListAddInactive() {
		$this->clickAt("//div[@id='problemListToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isVisible("//input[@id='q']")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->type("q","DMII");
		$this->click("//input[@id='searchLabel']");

		// wait for text here... workaround?
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->assertElementContainsText("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td","DMII KETOACD UNCONTROLD");

		$this->clickAt("//div[@id='problemLookupContainer']/div[2]/table/tbody/tr[2]/td");
		$this->click("//input[@value='Select']");
		$this->assertEquals("250.12",$this->getValue("//input[@id='problemList-code']"));
		$this->click("dateOfOnsetInput");
		$this->click("problemList-provider-id");
		$this->select("problemList-provider-id","label=Gooddoc, Horace F");
		$this->click("//option[@value='48502']");
		$this->click("//input[@id='problemList-status' and @name='problemList[status]' and @value='Inactive']");
		$this->click("problemList-immediacy");
		$this->answerOnNextPrompt("first comment");
		$this->click("//input[@value='Add']");
		$this->assertPrompt("Please enter your comment");
		$this->answerOnNextPrompt("yet another comment");
		$this->click("//input[@value='Add']");
		$this->assertPrompt("Please enter your comment");
		$this->click("//input[@value='Save']");
		sleep(1);
		$this->clickAt("//div[@id='problemsFilterContainer']/div[2]/table/tbody/tr[3]/td");

		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->assertElementContainsText("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td","Inactive");
	}

	function _problemListDeleteInactive() {
		$this->clickAt("//div[@id='problemsFilterContainer']/div[2]/table/tbody/tr[3]/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->clickAt("//div[@id='problemsListContainer']/div[2]/table/tbody/tr[2]/td");
		sleep(3);
		for ($second = 0; ; $second++) {
			if ($second >= 60) {
				$this->fail("timeout");
			}
			try {
				if ($this->isElementPresent("//div[@id='problemListToolbar']/table/tbody/tr/td[4]/table[@class='itemDefault']/tbody/tr/td")) {
					break;
				}
			} catch (Exception $e) {
			}
			sleep(1);
		}
		$this->clickAt("//div[@id='problemListToolbar']/table/tbody/tr/td[4]/table/tbody/tr/td");
	}

}
