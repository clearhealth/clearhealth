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
class AppointmentDataSource extends Datasource_sql {
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

	function AppointmentDataSource($user_id) {
		$this->user_id = $user_id;
		$this->_db = Cellini::dbInstance();
		$this->_query = array(
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
			'where'=> '',
			);
		$this->addOrderRule('start');
		$this->registerFilter('reason_code',array(&$this,'reasonFilter'));

		$enum =& ORDataObject::factory('Enumeration');
		$this->reasonLookup = $enum->get_enum_list('appointment_reasons');

	}

	function prepare() {
		settype($this->user_id,'int');
		$this->_query['where'] .= " external_id = $this->user_id";

		if (!$this->showPast) {
			$this->_query['where'] .= " and start > date_format(now(),'%Y-%m-%d 00:00:00') ";
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
