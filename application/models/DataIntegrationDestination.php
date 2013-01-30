<?php
/*****************************************************************************
*       DataIntegrationDestination.php
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


class DataIntegrationDestination extends DataIntegration {

	protected $dataIntegrationDestinationId;
	protected $guid;
	protected $name;
	protected $type;
	protected $connectInfo;
	protected $handlerType;
	protected $_table = 'dataIntegrationDestinations';
	protected $_primaryKeys = array('dataIntegrationDestinationId');

	public function defaultTemplate() {
		return <<<EOL
class [[ClassName]]DataIntegrationDestination extends DataIntegrationDestinationAbstract {
	//abstract requires at least this method
	public static function transmit(Audit \$auditOrm,DataIntegrationTemplate \$template,Array \$dataSource=array()) {
		/* sample code below
		\$query = http_build_query(array('template'=>\$template->template));
		\$ch = curl_init();
		curl_setopt(\$ch,CURLOPT_URL,'[URL]');
		curl_setopt(\$ch,CURLOPT_POST,true);
		curl_setopt(\$ch,CURLOPT_POSTFIELDS,\$query);
		curl_setopt(\$ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt(\$ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt(\$ch,CURLOPT_RETURNTRANSFER,false);
		curl_exec(\$ch);
		curl_close(\$ch);
		*/
	}
}

EOL;
	}

}
