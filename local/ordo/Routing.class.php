<?php
/**
 * Object Relational Persistence Mapping Class for table: routing
 *
 * @package	com.clearhealth
 * @author	ClearHealth Inc.
 */
class Routing extends ORDataObject {

	/**#@+
	 * Fields of table: routing mapped to class members
	 */
	var $routing_id		= '';
	var $person_id		= '';
	var $station_id		= '';
	var $appointment_id	= '';
	var $provider_id	= '';
	var $room_id		= '';
	var $from_station_id	= '';
	var $timestamp		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'routing';

	/**
	 * Primary Key
	 */
	var $_key = 'routing_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Routing';

	/**
	 * Handle instantiation
	 */
	function Routing() {
		parent::ORDataObject();
	}

	
}
?>
