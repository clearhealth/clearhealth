<?php
/**
 * @package com.uversainc.celini
 */

/**#@+
 * Required library
 */
$loader->requireOnce('includes/acl/ACLObjectGroup.abstract.php');
/**#@-*/

/**
 * Provides an interface for introspection of a Where object in the CeliniACL system
 *
 * @see CeliniACL
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ACLWhereGroup extends ACLObjectGroup
{
	var $_gaclType = 'AXO';
	var $_gaclSectionValue = 'resources';
	
	function ACLWhereGroup() {
		parent::ACLObjectGroup();
	}
}

