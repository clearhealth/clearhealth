<?php

$GLOBALS['loader']->requireOnce('/ordo/ORDataObject.class.php');

/**
 * ORDO for recurrence table
 */
 
class CalendarRecurrence extends ORDataObject{
	
	/**
	 *	
	 *	@var int
	 */
	 var $recurrence_id = '';

	/**
	 * ISO start date
	 * (example: 2005-03-30)
	 * @var string
	 */
	var $start_date = '';
	
	/**
	 * ISO start date
	 * (example: 2005-03-30)
	 * @var string
	 */
	var $end_date = '';
	
	/**
	 * 24hr Time of start
	 * (example - 23:05)
	 * @var string
	 */
	var $start_time = '';
	
	/**
	 * 24hr Time of end
	 * (example - 23:05)
	 * @var string
	 */
	var $end_time = '';
	
	var $recurrence_pattern_id = '';
	
	var $_table = 'recurrence';
	var $_key = 'recurrence_id';
	var $_internalName = 'CalendarRecurrence';

	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function CalendarRecurrence($id = 0){
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}


	function get_delete_message() {
		$message = "Deleting recurrence #".$this->_db->qstr($this->id);
		return $message;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
		$this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		if (empty($result)) {
			return true;
		}
		return false;
	}
	
	function getForSchedule($id){
		$schedule =& Celini::newORDO('Schedule',$id);
		$recs = $schedule->getChildren('Recurrence');
		$ocs=array();
		while($rec =& $recs->current() && $recs->valid()){
			$ocs[$rec->get('id')]=$rec;
			$recs->next();
		}
		return $ocs;
	}
	
	function getMonthDays(){
		return array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,
					14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,
					25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31);
	}
	
	function &createPattern($data){
		if(isset($data['days'])) {
			$data['number'] = 0;
			foreach($data['days'] as $day) {
				$data['number'] += pow(2,$day);
			}
		}
		$rp=&Celini::newORDO('CalendarRecurrencePattern');
		$rp->populate_array($data);
		$rp->persist();
		$this->set('recurrence_pattern_id',$rp->get('id'));
		$this->persist();
		return $rp;
	}
	
	function createEvents($rp=null,$ordoName = 'CalendarEvent'){
		if(is_null($rp)){
			$rp =& Celini::newORDO('CalendarRecurrencePattern',$this->get('recurrence_pattern_id'));
		}
		if(!$rp){
			return array();
		}
		$ocs=$rp->createEvents($this->start_date,$this->end_date,$this->start_time,$this->end_time,$ordoName);
		foreach($ocs as $ocid){
			$sql = "INSERT INTO relationship (`relationship_id`,`parent_id`,`parent_type`,`child_id`,`child_type`) VALUES (" . $this->dbHelper->nextId('sequences') . ",".$this->dbHelper->quote($this->get('id')).",".$this->dbHelper->quote($this->name()).",".$this->dbHelper->quote($ocid).",".$this->dbHelper->quote($ordoName).")";
			$this->dbHelper->execute($sql);
		}
		return $ocs;
	}
	
	function getPatternString(){
		$rp =& Celini::newORDO('CalendarRecurrencePattern',$this->get('recurrence_pattern_id'));
		return $rp->getString();
	}
	
	function drop(){
		$ocs=$this->getChildren('CalendarEvent');
		while($oc=&$ocs->current() && $ocs->valid()){
			$oc->drop();
			$oc=&$ocs->next();
		}
		$rp =& Celini::newORDO('CalendarRecurrencePattern',$this->get('recurrence_pattern_id'));
		$rp->drop();
		parent::drop();
	}

} // end of Class

?>
