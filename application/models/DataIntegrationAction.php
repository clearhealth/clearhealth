<?php
/*****************************************************************************
*       DataIntegrationAction.php
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


class DataIntegrationAction extends DataIntegration {

	protected $dataIntegrationActionId;
	protected $guid;
	protected $name;
	protected $action;
	protected $handlerType;
	protected $_table = 'dataIntegrationActions';
	protected $_primaryKeys = array('dataIntegrationActionId');

	public function defaultTemplate() {
		return <<<EOL
class [[ClassName]]DataIntegrationAction extends DataIntegrationActionAbstract {
	//abstract requires at least this method
	public static function act(Audit \$auditOrm,array \$dataSourceData) {
		\$orm = new HL7Message();
		\$orm->populateWithArray(\$dataSourceData);
		if (!strlen(\$orm->message)) {
			trigger_error('Empty message');
		}
		else {
			\$orm->persist();
		}
		return true;
	}
}

EOL;
	}

}
