<?php
/*****************************************************************************
*       SelectProblemTab.php
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

class SelectProblemTab extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
/*    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://change-this-to-the-site-you-are-testing/");*/
  }

  function testMyTestCase()
  {
    $this->clickAt("//div[@class='dhx_tabbar_row']/div[contains(.,'Problem')]", "");
    for ($second = 0; ; $second++) {
        if ($second >= 60) $this->fail("timeout");
        try {
            if ($this->isVisible("//div[@id='problemListToolbar']/table/tbody/tr/td[2]/table/tbody/tr/td/img")) break;
        } catch (Exception $e) {}
        sleep(1);
    }

  }
}
?>
