<?php
/*****************************************************************************
*       TemplateXSLTTest.php
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

/**
 * Unit test for NSDR Definition Model
 */

require_once dirname(dirname(__FILE__)).'/TestHelper.php';

/**
 * TestCase
 */
require_once 'TestCase.php';

/**
 * TemplateXSLT
 */
require_once 'TemplateXSLT.php';

class Models_TemplateXSLTTest extends TestCase {

	public function testValidXMLFormat() {
		$data = array();
		$person = array();
		$person['person_id'] = 1234;
		$person['first_name'] = 'Test';
		$person['last_name'] = 'ClearHealth';
		$data['person'] = $person;
		$templateXSLT = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template match="person">
<xsl:value-of select="person_id"/>-<xsl:value-of select="first_name"/>-<xsl:value-of select="last_name"/>
</xsl:template>
</xsl:stylesheet>
EOL;
		try {
			$template = TemplateXSLT::render($data,$templateXSLT);
			$this->assertContains('1234-Test-ClearHealth',$template);
		}
		catch (Exception $e) {
			$this->assertTrue(false,$e->getMessage());
		}
	}

	public function testInvalidXMLFormat() {
		$data = array();
		$person = array();
		$person['person_id'] = 1234;
		$person['first_name'] = 'Test';
		$person['last_name'] = 'ClearHealth';
		$data['person'] = $person;
		// invalid XML format: close tag of xsl:template is xsl:templates
		$templateXSLT = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template match="person">
<xsl:value-of select="person_id"/>-<xsl:value-of select="first_name"/>-<xsl:value-of select="last_name"/>
</xsl:templates>
</xsl:stylesheet>
EOL;
		$assert = false;
		$msg = '';
		try {
			$template = TemplateXSLT::render($data,$templateXSLT);
		}
		catch (Exception $e) {
			$assert = true;
			$msg = $e->getMessage();
		}
		$this->assertTrue($assert,$msg);
	}

}
