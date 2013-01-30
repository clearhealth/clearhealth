<?php
/*****************************************************************************
*       Logout.php
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

class Logout extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
	$this->setBrowser('{"username": "clearhealth", "access-key": "42ca404e-5a02-4375-9dde-a6a3982f1ee0", "os": "Windows 2003", "browser": "firefox", "browser-version": "3.5"}') ;
    $this->setBrowserUrl ("https://sdemo.clear-health.com/");
        $this->setHost("saucelabs.com");
  }

  function testMyTestCase()
  {
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
}
?>
