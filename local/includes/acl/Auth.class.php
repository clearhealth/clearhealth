<?php
/**
 * @package com.uversainc.clearhealth
 */
 
/**
 * Require abstract
 */
$loader->requireOnce('includes/acl/AbstractAuth.abstract.php');

/**
 * Provides Clearhealth specific authorization
 *
 * @package com.uversainc.clearhealth
 */
class Auth extends AbstractAuth
{
	/**
	 * {@inheritdoc}
	 */
	function canI($what, $where = null) {
		return parent::canI($what, $where);
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function can($who, $what, $where = null) {
		return parent::can($who, $what, $where);
	}
}

?>
