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
class ACLWho extends ACLObject
{
	var $_celiniType = 'who';
	var $_gaclType = 'ARO';
	var $_gaclSectionValue = 'users';
	
	function ACLWho() {
		parent::ACLObject();
	}
}

