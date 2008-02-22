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
class ACLWhat extends ACLObject
{
	var $_celiniType = 'what';
	var $_gaclType = 'ACO';
	var $_gaclSectionValue = 'actions';
	
	function ACLWhat() {
		parent::ACLObject();
	}
}

