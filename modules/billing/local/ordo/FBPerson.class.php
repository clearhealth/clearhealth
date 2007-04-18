<?php
/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freeb2
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('ordo/MergeDecorator.class.php');
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freeb2
 */
class FBPerson extends MergeDecorator {

	/**#@+
	 * Fields of table: Person mapped to class members
	 */
	var $id				= '';
	var $claim_id		= '';
	var $index			= '';
	var $type			= '';
	var $salutation		= '';
	var $last_name		= '';
	var $first_name		= '';
	var $middle_name	= '';
	var $gender			= 'U';
	var $date_of_birth	= '';
	var $summary		= '';
	var $identifier 	= '';
	var $identifier_type = "34"; //24 is EIN, 34 is SSN, XX is National Provider Pin
	var $record_number = '';
	var $phone_number  = '';
	var $comment = '';
	/**#@-*/

	/**#@+
	 * Lookup cache
	 */
	var $_lookup	= false;
	var $_lookupi	= false;
	/**#@-*/

	var $_table = 'fbperson';
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FBPerson($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	

		// merge an fbaddress ordo
		$fbaddress =& ORDataObject::factory('FBAddress');
		$this->merge('fbaddress',$fbaddress);
		$this->addMetaHints("hide",array("claim_id","type","salutation","index"));
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Person with this
	 */
	function setup($id = 0, $claim_id = 0,$index = 0) {
		$this->set('id',$id);
		if ($claim_id != false) {
			$this->set('claim_id',$claim_id);
		}
		
		if ($this->get('id') > 0) {
			$this->populate();
		}
		elseif ($claim_id > 0 && is_numeric($index)) {
			$sql = '
				SELECT
					* 
				FROM 
					fbperson
				WHERE
					type = ' . $this->dbHelper->quote($this->get('type')) . ' AND
					claim_id = ' . $this->dbHelper->quote($this->get('claim_id')) . ' AND
					`index` = ' . (int)$index;
			$res = $this->dbHelper->execute($sql); 
			$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
			$this->helper->populateStorageValues($this);
			$this->_populateAddress();
		}
		
		if (is_numeric($index)) {
			$this->set('index',$index);
		}
	}

	/**
	 * Pull data for this record from the database
	 */
	function populate() {
		parent::populate('person_id');
		$this->_populateAddress();
	}
	
	
	/**
	 * Handle loading the attached address object
	 *
	 * @access private
	 */
	function _populateAddress() {
		// find the attached address object and populate it
		$address_id = 0;
		$res = $this->dbHelper->execute('SELECT address_id FROM fbaddress WHERE external_id = ' . (int)$this->get('id'));
		if ($res && !$res->EOF) {
			$address_id = $res->fields['address_id'];
		}
		$this->fbaddress->setup($address_id, $this->get('id'));
		$this->mergePopulate();
	}

	/**
	* Store data to the database
	*/
	function persist() {
		if (!is_numeric($this->get('index'))) {
			$in = 0;
			$sql = "select MAX(p.`index`)+1 as `index` from $this->_table p where p.claim_id = " . $this->get("claim_id") . " and p.type = "  . $this->_quote($this->get("type")) . " group by p.claim_id";
			$res = $this->_execute($sql);
			if ($res && !$res->EOF) {
				$in = $res->fields['index'];
			}
			$this->set('index',$in);
		}
		parent::persist();
		$this->external_id = $this->get('id');
		$this->mergePersist('external_id');
	}
	
	/**
	 * Get all the claimlines associated with a claim_id
	 *
	 * note: this is a high performance method bypassing normal setup method
	 *
	 * @param	int	$claim_id
	 */
	function &arrayFromClaimId($claim_id) {
		settype($claim_id,'int');
		
		$case_sql = $this->_case_sql();
		
		$sql = "select p.*,a.*, p.type as type $case_sql from $this->_prefix$this->_table p left join fbaddress a on a.external_id = p.person_id "
				." left join storage_int on storage_int.foreign_key=p.person_id"
				." left join storage_string on storage_string.foreign_key=p.person_id"
				." left join storage_date on storage_date.foreign_key=p.person_id"
				." where p.type = " . $this->_quote($this->get("type")) . " and p.claim_id = " . $this->_quote($claim_id) . " group by p.person_id";


		$res = $this->_execute($sql);
		$array = array();
		while($res && !$res->EOF) {
			$o =& ORDataObject::factory($this->get("type"));
			$o->populate_array($res->fields);
			$array[] = $o;
			$res->MoveNext();
		}
		
		if (count($array) > 0) {
			return $array;
		}
		$ret = false;	
		return $ret;
	}
	

	/**
	* Generic lookup function, match an id to a first_name last_name
	*/
	function lookup($key,$initials = false) {
		if ($initials) {
			return $this->_lookupInitials($key);
		}
		if ($this->_lookup === false) {
			$res = $this->_execute("select person_id, concat_ws(' ',first_name,last_name) name from $this->_prefix$this->_table");
			$this->_lookup = $res->getAssoc();
		}
		if (isseT($this->_lookup[$key])) {
			return $this->_lookup[$key];
		}
		return "";
	}

	/**#@+
	 * Getters and Setters for Table: Person
	 */
	
	/**
	 * Getter for Primary Key: person_id
	 */
	function get_person_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: person_id
	 */
	function set_person_id($id)  {
		$this->id = $id;
	}
	
	/**
	 * Getter for printable name
	 */
	function get_print_name() {
		$name = '';
		if (strlen($this->get('first_name')) > 0) {
			$name .= trim($this->get('first_name'));
		}
		if (strlen($this->get('middle_name')) > 0) {
			$name .= ' ' . trim($this->get('middle_name'));	
		}
		if (strlen($this->get('last_name')) > 0) {
			$name .= ' ' . trim($this->get('last_name'));
		}
		return trim($name);
	}

		function get_print_name_last_first() {
		$name = '';
		if (strlen($this->get('last_name')) > 0) {
			$name .= trim($this->get('last_name'));
		}
		if (strlen($this->get('first_name')) > 0) {
			$name .= ', '.trim($this->get('first_name'));
		}
		if (strlen($this->get('middle_name')) > 0) {
			$name .= ' '.trim($this->get('middle_name'));
		}
		return trim($name);
	}
	
	/**
	 * Sets the date of birth, insuring it's ISO formatted
	 */
	function set_date_of_birth($date) {
		$this->_setDate('date_of_birth', $date);
	}
	
	/**
	 * Returns the date of birth, using the configured formatting
	 *
	 * @return string
	 */
	function get_date_of_birth() {
		return $this->_getDate('date_of_birth');
	}
	
	
	/**
	 * Return the numeric value for the month of this {@link FBPerson}'s date of birth.
	 *
	 * @return string
	 */
	function value_date_of_birth_month() {
		$date =& $this->date_of_birth->getDate();
		return $date->month;
	}
	
	/**
	 * Return the numeric value for the date of this {@link FBPerson}'s date of birth.
	 *
	 * @return string
	 */
	function value_date_of_birth_date() {
		$date =& $this->date_of_birth->getDate();
		return $date->month;
	}
	
	/**
	 * Return the numeric value for the year of this {@link FBPerson}'s date of birth.
	 *
	 * @return string
	 */
	function value_date_of_birth_year() {
		$date =& $this->date_of_birth->getDate();
		return $date->year;
	}

	/**#@-*/
	
	function set_fbaddress($array) {
		$this->fbaddress->populate_array($array);
	}

	/**
	 * Set identifier type, translating from names like SSN to 
		billing values like 24 or 34.
	 */
	function set_identifier_type($type) {
		if(strcmp($type, "SSN") == 0 || strcmp($type, "ssn") == 0){ //Add more socsec strings
			$this->identifier_type = "34";
			return;
		}
		
		if(strcmp($type, "EIN") == 0 || strcmp($type, "ein") == 0) { //Add more socsec strings
			$this->identifier_type = "24";
			return;
		}
		
		$this->identifier_type = $type;
	}

	
	/**
	 * Set the gender of this FBPerson
	 *
	 * This accepts M for male, F for female, or U for unknown.  Any value not
	 * recognized will be treated as U.
	 *
	 * @param string|null M|F|U
	 */
	function set_gender($type) {
		switch (strtoupper($type)) {
		case 'M' :
			$this->gender = 'M';
			break;
		case 'F' :
			$this->gender = 'F';
			break;
		case 'U' :
		default :
			$this->gender = 'U';
			break;
		}
	}
}
?>
