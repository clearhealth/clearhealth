<?php

/**
*	This class is a data model object for representation of user information. Not application specific user information but only 
*	framework and web interaction information. Currently this is used in role lookup, login authentication and as one of the elements
*	in {@link Me} objects array.
*/
class Enumeration extends ORDataObject {
	var $name;
	var $title;
	var $description;
	var $enumeration = array();
	
	function Enumeration($db = null) {
		parent::ORDataObject($db);	
		$this->_table = "enumeration";
		$this->_sequence_name = "sequences";	
	}

	function setup($name = null) {

		if (!is_null($name)) {
			$this->name = $name;
			$this->populate();
		}
	}

	/**
	* Get all enumerations
	*/
	function enumeration_factory($limit="")
	{
		// this is an unoptomized factory because you need to do special loading to grab the enumeration value
		// this shouldn't ever matter since you'll never have hundreds of enumeration rows
		$res = $this->_db->execute("select name from $this->_table order by name".$limit);
			
		$enums = array();
		$i = 0;
		while ($res && !$res->EOF) {
			$enums[$i] =& ORDataObject::factory('Enumeration',$res->fields['name']);
			$res->MoveNext(); 
			$i++;
		}	
		return $enums;
	}

	/**
	* Pull data for this record from the database
	*/
	function populate() {
		$sql = "SELECT * from $this->_prefix$this->_table where name = ".$this->_quote($this->name);
		$res = $this->_execute($sql);
		if ($res) {
			$this->populate_array($res->fields);
		}

		$this->enumeration = $this->_load_enum($this->name,false);
	}

	/**
	* Store data to the database
	*/
	function persist()
	{
		//limit name to a-Z_
		$_POST['name'] = preg_replace("/[^A-Za-z0-9_]/","",$_POST['name']);
		parent::persist();
		// build table update


		$enums = implode("','",$this->enumeration);
		$this->_db->execute("ALTER TABLE `$this->_table` DROP `$this->name`");
		$this->_execute("ALTER TABLE `$this->_table` ADD `$this->name` ENUM( '$enums' ) NOT NULL");
		if (isset($GLOBALS['static']['enums'][$this->_table][$this->name])) {
			unset($GLOBALS['static']['enums'][$this->_table][$this->name]);
		}
	}

	/**
	* Delete this record
	*/
	function drop()
	{
		$this->_execute("delete from $this->_table where name = ". $this->_db->qstr($this->name));
		$this->_execute("ALTER TABLE `$this->_table` DROP `$this->name`");
	}
    
 	/**#@+
	*	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
	*/

	function get_name()
	{
		return $this->name;
	}
	function set_name($name)
	{
		$this->name = $name;
	}

	function get_title()
	{
		return $this->title;
	}
	function set_title($title)
	{
		$this->title = $title;
	}

	function get_description()
	{
		return $this->description;
	}
	function set_description($desc)
	{
		$this->description = $desc;
	}

	function get_enumeration()
	{
		return $this->enumeration;
	}
	function set_enumeration($enum)
	{
		$this->enumeration = $enumeration;
	}

	function get_enum_list($name) {
		$list = $this->_load_enum($name,false);
		return array_flip($list);
	}

	var $enumLookup = array();
	function enumLookup($name,$value) {
		if (!isset($this->enumLookup[$name])) {
			$this->enumLookup[$name] = $this->get_enum_list($name);
		}
		if (isset($this->enumLookup[$name][$value])) {
			return $this->enumLookup[$name][$value];
		}
		return "";
	}
	
	
	/**
	 * Helper function that loads enumerations of a specific $enumName as an
	 * array.
	 *
	 * This is also efficient because it uses psuedo-class variables so that it
	 * doesn't have to do database work for each instance
	 *
	 * If $blank is set to true, a blank element will be unshifted onto the
	 * returned array
	 *
	 * @param	string
	 * @param	boolean
	 * @return	array
	 * @static
	 */
	function loadEnum($enumName,$blank = true) {
		$table = 'enumeration';
		if (    isset($GLOBALS['static']['enums'][$table][$enumName])
			&& is_array($GLOBALS['static']['enums'][$table][$enumName])
		) {
			$enum = $GLOBALS['static']['enums'][$table][$enumName];
		}
		else  {
			$cols = $this->_db->MetaColumns($table);
			if ($cols === false) {
				 $cols = $this->_db->MetaColumns("enumerations");
			}
			$enum = array();
			if ($cols && !$cols->EOF) {
				 //why is there a foreach here? at some point later there will be a scheme to autoload all enums
				 //for an object rather than 1x1 manually as it is now
				 foreach ($cols as $col) {
					  if ($col->name == $enumName && substr($col->type,0,4) == "enum") {
						   preg_match_all("|[\'](.*)[\']|U",$col->type,$enum_types);
						   //position 1 is where preg_match puts the matches sans the delimiters
						   $enum = $enum_types[1];
						   //for future use
						   //$enum[$col->name] = $enum_types[1];
					  }
				 }
			}
			array_unshift($enum," ");

			$enum = array_flip($enum);
			$GLOBALS['static']['enums'][$table][$enumName] = $enum;
		}
		//keep indexing consistent whether or not a blank is present
		if (!$blank) {
			unset($enum[" "]);
		}
		return $enum;
	}
}
