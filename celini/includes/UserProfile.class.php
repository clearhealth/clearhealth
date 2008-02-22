<?php

$loader->requireOnce('includes/AbstractUserProfile.abstract.php');

/**
 * This serves as a default implementation for Celini's {@link AbstractUserProfile}.
 *
 * This allows an application or module to override Celini's default UserProfile actions by
 * providing their own UserProfile.
 *
 * @package com.clear-health.celini
 */
class UserProfile extends AbstractUserProfile
{
	function UserProfile($userId = 0) {
		parent::AbstractUserProfile($userId);
	}

}

?>
