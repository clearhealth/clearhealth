<?php
/**
 * Class were extending
 */
$loader->requireOnce('/includes/Datasource_sql.class.php');
/**
 * Datasource for handling appointments
 *
 * @package	com.clear-health.Celini
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
			'title'		=> 'Title',
			'reason'	=> 'Reason',
			'appointment_code'	=> 'Code',
			'provider'	=> 'Provider',
			'location'	=> 'Location',
		);
	
	var $reasonLookup = array();

	var $showPast = true;

	function Person_Appointment_DS($user_id) {
		$this->user_id = $user_id;
		$this->setup(Celini::dbInstance(),
		array(
			'cols' => "concat('<!--',UNIX_TIMESTAMP(start),'-->',date_format(start,'%m/%d/%y %H:%i')) start,
					date_format(start,'%Y-%m-%d') isoday,
					if (
						((time_to_sec(end)-time_to_sec(start))/60 > 60), 
						( concat( floor((time_to_sec(end)-time_to_sec(start))/60/60) ,'h ', 
							(mod(round(time_to_sec(end)-time_to_sec(start))/60,60)) ,'m') ),
						concat(round((time_to_sec(end)-time_to_sec(start))/60),'m')
					) length, concat(b.name,' -> ',r.name) location,
					a.title, reason, appointment_code, concat_ws(', ',p.last_name,p.first_name) provider,b.id as building_id",
			'from' => 'appointment a inner join event e on a.event_id = e.event_id inner join rooms r on r.id = a.room_id inner join buildings b on b.id = r.building_id
			left join person p on a.provider_id = p.person_id ',
			'orderby' => 'start DESC',
			'where'   => ''
			),
		$this->_labels);
		$this->registerFilter('reason',array(&$this,'reasonFilter'));
		$this->registerFilter('start',array(&$this,'startFilter'),false, "html");
		$this->registerTemplate('start','<a href="'.Celini::link('day','CalendarDisplay').'date={$isoday}&Filter[building][]={$building_id}">{$start}</a>');

		$enum =& ORDataObject::factory('Enumeration');
		$this->reasonLookup = $enum->get_enum_list('appointment_reasons');

	}

	function startFilter($value,$rowValues) {
               return "<a href=" . Celini::link('day','CalendarDisplay') . "date="  . $rowValues['isoday']  . "&Filter[building][]=" . $rowValues['building_id'] . ">" . $value . "</a>";
       }

	function prepare() {
		settype($this->user_id,'int');
		$this->_query['where'] .= " a.patient_id = $this->user_id";

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
