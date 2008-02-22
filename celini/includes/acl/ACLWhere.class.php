<?php
/**
 * @package com.clear-health.celini
 */

/**#@+
 * Required library
 */
$loader->requireOnce('includes/acl/ACLObject.abstract.php');
/**#@-*/

/**
 * Provides an interface for introspection of a Who object in the CeliniACL system
 *
 * @see CeliniACL
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ACLWhere extends ACLObject
{
	var $_celiniType = 'where';
	var $_gaclType = 'AXO';
	var $_gaclSectionValue = 'resources';
	
	function ACLWhere() {
		parent::ACLObject();
	}
}

