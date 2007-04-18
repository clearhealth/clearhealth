<?php

/**
 * Provides a series of shortcuts to the {@link altNotice} ORDO to send alerts,
 * thus acting as the alerts module's post office.
 *
 * Any of these methods can be used as static or traditional object methods.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.alerts
 */
class altPostOffice
{
	/**
	 * Sends a message/alert via {@link altNotice}
	 *
	 * This provides a raw interface for taking an array and turning it into
	 * a notice.
	 *
	 * @param array
	 *
	 * @todo Determine if this is a duplication of populate_array() that is
	 *   necessary.  This does provide a flex-point if anything additional were
	 *   ever necessary for an alert to happen, so that's what its here for.
	 */
	function sendNotice($attributes) {
		assert('is_array($attributes)');
		
		$notice =& Celini::newORDO('altNotice');
		foreach ($attributes as $noticeName => $noticeValue) {
			$notice->set($noticeName, $noticeValue);
		}
		$notice->persist();
	}
	
	
	/**
	 * Sends a message/alert from an ORDO to a particular ACL group
	 *
	 * This provides a shortcut to {@link sendORDONotice()}
	 *
	 * @param ORDataObject
	 * @param string
	 * @param array
	 */
	function sendORDONoticeToGroup(&$ordo, $groupName, $attributes) {
		$attributes['owner_type'] = 'ACL Group';
		$attributes['owner_id']   = $groupName;
		
		altPostOffice::sendORDONotice($ordo, $attributes);
	}
	
	
	/**
	 * Sends a message/alert from an ORDO
	 *
	 * This is a utility method that utilizes {@link altNotice} to send a notice
	 * from an ORDO to some other part of the system.  It is effectively a 
	 * shortcut to {@link saveNotice()}.
	 *
	 * @param ORDataObject
	 * @param array
	 */
	function sendORDONotice(&$ordo, $attributes) {
		assert('is_a($ordo, "ORDataObject")');
		assert('is_array($attributes)');
		
		$attributes['external_type'] = strtolower(get_class($ordo));
		$attributes['external_id']   = $ordo->get('id');
		
		altPostOffice::sendNotice($attributes);
	}
	
	
	/**
	 * Sends a message/alert from an ORDO to a particular user
	 *
	 * This provides a shortcut to {@link sendORDONotice()}.
	 *
	 * @param ORDataObject
	 * @param int
	 * @param array
	 */
	function sendORDONoticeToUser(&$ordo, $user_id, $attributes = array()) {
		$attributes['owner_type'] = 'User';
		$attributes['owner_id']   = (int)$user_id;
		
		altPostOffice::sendORDONotice($ordo, $attributes);
	}
}

