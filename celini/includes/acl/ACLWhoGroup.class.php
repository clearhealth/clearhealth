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
 * Provides an interface for introspection of a Who object in the CeliniACL system
 *
 * @see CeliniACL
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ACLWhoGroup extends ACLObjectGroup
{
	var $_gaclType = 'ARO';
	var $_gaclSectionValue = 'users';
	
	function ACLWhoGroup() {
		parent::ACLObjectGroup();
	}
}

