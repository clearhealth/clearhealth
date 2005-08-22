<?php
/**
 * Class were extending
 */
require_once CELLINI_ROOT .'/includes/Datasource_sql.class.php';
/**
 * Datasource for handling appointments
 *
 * @package	com.uversainc.cellini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class Person_Appointment_DS extends Datasource_sql {
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Person_Appointment_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $user_id;
	var $_labels = array(
			'start' 	=> 'Start',
			'length'	=> 'Time',
			'notes'		=> 'Title',
			'reason_code'	=> 'Reason',
			'provider'	=> 'Provider',
			'location'	=> 'Location',
		);
	
	var $reasonLookup = array();

	var $showPast = true;

	function Person_Appointment_DS($user_id) {
		$this->user_id = $user_id;
		$this->setup(Cellini::dbInstance(),
		array(
			'cols' => "date_format(start,'%m/%d/%y %H:%i') start,
					if (
						((time_to_sec(end)-time_to_sec(start))/60 > 60), 
						( concat( floor((time_to_sec(end)-time_to_sec(start))/60/60) ,'h ', 
							(mod(round(time_to_sec(end)-time_to_sec(start))/60,60)) ,'m') ),
						concat(round((time_to_sec(end)-time_to_sec(start))/60),'m')
					) length, concat(b.name,' -> ',r.name) location,
					o.notes, reason_code, concat_ws(', ',p.last_name,p.first_name) provider ",
			'from' => 'occurences o inner join rooms r on r.id = o.location_id inner join buildings b on b.id = r.building_id
			left join user u on o.user_id = u.user_id left join person p on u.person_id = p.person_id',
			'orderby' => 'start DESC',
			'where'   => ''
			),
		$this->_labels);
		$this->registerFilter('reason_code',array(&$this,'reasonFilter'));

		$enum =& ORDataObject::factory('Enumeration');
		$this->reasonLookup = $enum->get_enum_list('appointment_reasons');

	}

	function prepare() {
		settype($this->user_id,'int');
		$this->_query['where'] .= " external_id = $this->user_id";

		if (!$this->showPast) {
			$this->_query['where'] = " and start > date_format(now(),'%Y-%m-%d 00:00:00') ";
		}

		parent::prepare();
	}

	function reasonFilter($code) {
		if ($code == 0) { $code = 1;  }
		if (isset($this->reasonLookup[$code])) {
			return $this->reasonLookup[$code];
		}
		return $code;
	}
}
?>
