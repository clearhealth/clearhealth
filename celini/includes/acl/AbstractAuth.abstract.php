<?php

/**
 * Handles authorization requests from within the application
 *
 * @package com.uversainc.celini
 * @abstract
 */
class AbstractAuth
{
	/**
	 * Can the current user execute a  given action
	 *
	 * If $where is left NULL, this will assume generic permissions are being requested
	 *
	 * @param  string        What the requested action is
	 * @param  string|null   Where the requested action will be performed
	 * @return boolean
	 * @see    can()
	 */
	function canI($what, $where = null) {
		// get user
		$me =& Me::getInstance();
		return Auth::can($me->get_username(), $what, $where);
	}
	
	
	/**
	 * Can a particular user execute a given action
	 *
	 * If $where is left NULL, this will assume generic permissions are being requested
	 *
	 * @param  string        Who is the user that we are checking for
	 * @param  string        What the requested action is
	 * @param  string|null   Where the requested action will be performed
	 * @return boolean
	 */
	function can($who, $what, $where = null) {
		global $security;
		if (is_null($where)) {
			return $security->acl_check('actions', $what, 'users', $who);
		}
		else {
			return $security->acl_check('actions', $what, 'users', $who, 'resources', $where);
		}
	}
}

