<?php

$loader->requireOnce('includes/MimeLookup.class.php');

/**
 * class Document
 * This class is the logical representation of a physical file on some system somewhere that can be referenced with a URL
 * of some type. This URL is not necessarily a web url, it could be a file URL or reference to a BLOB in a db.
 * It is implicit that a document can have other related tables to it at least a one document to many notes which join on a documents 
 * id and categories which do the same. 
 */
 
class Document extends ORDataObject{
	
	/*
	*	Database unique identifier
	*	@var id
	*/
	var $id;
	
	/*
	*	DB unique identifier reference to some other table, this is not unique in the document table
	*	@var int
	*/
	var $foreign_id;
	
	/*
	*	Enumerated DB field which is met information about how to use the URL
	*	@var int can also be a the properly enumerated string
	*/
	var $type;
	
	/*
	*	Array mapping of possible for values for the type variable
	*	mapping is array text name to index
	*	@var array
	*/
	var $type_array = array();
	
	/*
	*	Size of the document in bytes if that is available
	*	@var int
	*/
	var $size;
	
	/*
	*	Date the document was first persisted
	*	@var string
	*/
	var $date;
	
	/*
	*	URL which point to the document, may be a file URL, a web URL, a db BLOB URL, or others
	*	@var string
	*/
	var $url;
	
	/*
	*	Mimetype of the document if available
	*	@var string
	*/
	var $mimetype;
	
	/*
	*	If the document is a multi-page format like tiff and has at least 1 page this will be 1 or greater, if a non-multi-page format this should be null or empty
	*	@var int
	*/
	var $pages;
	
	/*
	*	Foreign key identifier of who initially persisited the document,
	*	potentially ownership could be changed but that would be up to an external non-document object process
	*	@var int
	*/
	var $owner;
	
	/*
	*	Timestamp of the last time the document was changed and persisted, auto maintained by DB, manually change at your own peril
	*	@var int
	*/
	var $revision;

	/**
	 * Group of the document, 1, is always the special group all
	 */
	var $group_id = 1;

	/**
	 * Display name, if not will be set to basename fileurl on get
	 */
	var $name = "";

	/**
	 * Load enums from this table
	 */
	var $enumTable = "dm_document";

	var $_table = "document";
	var $_internalName='Document';
	/**
	 * Constructor sets all Document attributes to their default value
	 * @param int $id optional existing id of a specific document, if omitted a "blank" document is created 
	 */
	function Document($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		//shore up the most basic ORDataObject bits
		$this->id = $id;
		
		//load the enum type from the db using the parent helper function, this uses psuedo-class variables so it is really cheap
		$this->type_array = $this->_load_enum("type");
		$tmp = array_flip($this->type_array);
		
		$this->type = $tmp[0];
		$this->size = 0;
		$this->date = date("Y-m-d H:i:s");
		$this->url = "";
		$this->mimetype = "";
		
		if ($id != "") {
			$this->populate();
		}
	}
	
	/**
	 * Convenience function to get an array of many document objects
	 * For really large numbers of documents there is a way more efficient way to do this by overwriting the populate method
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function documents_factory($foreign_id = "") {
		$documents = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$d = new Document();
		$sql = "SELECT id FROM  " . $d->_table . " WHERE foreign_id " .$foreign_id ;
		$result = $d->_db->Execute($sql);
		
		while ($result && !$result->EOF) {
			$documents[] = new Document($result->fields['id']);
			$result->MoveNext();
		}

		return $documents;
	}
	
	/**
	 * Convenience function to get a document object from a url
	 * Checks to see if there is an existing document with that URL and if so returns that object, otherwise
	 * creates a new one, persists it and returns it
	 * @param string $url  
	 * @return object new or existing document object with the specified URL
	 */
	function document_factory_url($url) {
		$d = new Document();
		//strip url handler, for now we always assume file://
		$filename = preg_replace("|^(.*)://|","",$url);
		
		if (!file_exists($filename)) {
			die("An invalid URL was specified to crete a new document, this would only be caused if files are being deleted as you are working through the queue. '$filename'\n");	
		}
		
		$sql = "SELECT id FROM  " . $d->_table . " WHERE url= '" . mysql_real_escape_string($url) ."'" ;
		$result = $d->_db->Execute($sql);
		
		if ($result && !$result->EOF) {
			if (file_exists($filename)) {
				$d = new Document($result->fields['id']);
			}
			else {
				$sql = "DELETE FROM  " . $d->_table . " WHERE id= '" . $result->fields['id'] ."'";
				$result = $d->_db->Execute($sql);
				echo("There is a database for the file but it no longer exists on the file system. Its document entry has been deleted. '$filename'\n");
			}
		}
		else {
			$file_command = $GLOBALS['config']['document']['file_command_path'] ;
			$cmd_args = "-i ".escapeshellarg($new_path.$fname);
		  		
		  	$command = $file_command." ".$cmd_args;
		  	$mimetype = exec($command);
		  	$mime_array = split(":", $mimetype);
		  	$mimetype = $mime_array[1];
		  		
		  	$d->set_mimetype($mimetype);
			$d->url = $url;
		  	$d->size = filesize($filename);
		  	$d->type = $d->type_array['file_url'];
		  	$d->persist();
		  	$d->populate();	
		}

		return $d;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		. "FID: " . $this->foreign_id."\n"
		. "type: " . $this->type . "\n"
		. "type_array: " . print_r($this->type_array,true) . "\n"
		. "size: " . $this->size . "\n"
		. "date: " . $this->date . "\n"
		. "url: " . $this->url . "\n"
		. "mimetype: " . $this->mimetype . "\n"
		. "pages: " . $this->pages . "\n"
		. "owner: " . $this->owner . "\n"
		. "revision: " . $this->revision. "\n"
		. "group: " . $this->group. "\n";

		if ($html) {
			return nl2br($string);
		}
		else {
			return $string;
		}
	}

	/**#@+
	*	Getter/Setter methods used by reflection to affect object in persist/poulate operations
	*	@param mixed new value for given attribute
	*/
	function set_id($id) {
		$this->id = $id;
	}
	function get_id() {
		return $this->id;
	}
	function set_foreign_id($fid) {
		$this->foreign_id = $fid;
	}
	function get_foreign_id() {
		return $this->foreign_id;
	}
	function set_type($type) {
		$this->type = $type;
	}
	function get_type() {
		return $this->type;
	}
	function set_size($size) {
		$this->size = $size;
	}
	function get_size() {
		return $this->size;
	}	
	function set_date($date) {
		$this->date = $date;
	}
	function get_date() {
		return $this->date;
	}
	function set_url($url) {
		$this->url = $url;
	}
	function get_url() {
		return $this->url;
	}
	/**
	* this returns the url stripped down to basename
	*/
	function get_url_web() {
		return basename($this->url);
	}
	/**
	* get the url without the protocol handler
	*/
	function get_url_filepath() {
		return preg_replace("|^(.*)://|","",$this->url);
	}
	/**
	* get the url filename only
	*/
	function get_url_file() {
		return basename(preg_replace("|^(.*)://|","",$this->url));
	}

	function get_name() {
		if (empty($this->name)) {
			return $this->get_url_file();
		}
		return $this->name;
	}
	function set_name($name) {
		$this->name = $name;
	}

	/**
	* get the url path only
	*/
	function get_url_path() {
		return dirname(preg_replace("|^(.*)://|","",$this->url)) ."/";
	}
	function set_mimetype($mimetype) {
		$ml = new MimeLookup();
		$mimetype = $ml->cleanup($mimetype);
		$this->mimetype = $mimetype;
	}
	function get_mimetype() {
		return $this->mimetype;
	}
	function set_pages($pages) {
		$this->pages = $pages;
	}
	function get_pages() {
		return $this->pages;
	}
	function set_owner($owner) {
		$this->owner = $owner;
	}
	function get_owner() {
		return $this->owner;
	}

	function get_displaytype() {
		$ml = new MimeLookup();
		return $ml->lookup($this->get('mimetype'));
	}

	function isDisplayable() {
		$ml = new MimeLookup();
		return $ml->displayable($this->get('mimetype'));
	}

	function get_displaySize() {
		$size = $this->get('size');
		$a = array("Bytes", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}

		return round($size,2)." ".$a[$pos];
	}

	/**
	 * Pulls a chunk out of the notes to get a description of the item
	 */
	function get_description() {
		ORdataObject::factory_include('Note');
		$notes = Note::notes_fatory($this->get('id'));
		if (isset($notes[0])) {
			return $notes[0]->get('note');
		}
	}

	/*
	*	No getter for revision because it is updated automatically by the DB.
	*/
	function set_revision($revision) {
		$this->revision = $revision;
	}
	
	/*
	*	Overridden function to stor current object state in the db.
	*	current overide is to allow for a just in time foreign id, often this is needed 
	*	when the object is never directly exposed and is handled as part of a larger
	*	object hierarchy.
	*	@param int $fid foreign id that should be used so that this document can be related (joined) on it later
	*/
	
	function persist($fid ="") {
		if (!empty($fid)) {
			$this->foreign_id = $fid;
		}
		parent::persist();
	}

	/**
	 * Get an associtive array of possible groups
	 */
	function getGroupList() {
		$enumTable = $this->enumTable;
		$this->enumTable = false;

		$list = array_flip($this->_load_enum('dm_group_list',false));

		$this->enumTable = $enumTable;
		return $list;
	}

} // end of Document

/*
$d = new Document(3);
$d->type = $d->type_array[1];
$d->url = "file:///tmp/test.gif";
$d->pages = 0;
$d->owner = 60;
$d->size = 8000;
$d->foreign_id = 25;
$d->persist();
$d->populate();

echo $d->toString(true);*/
?>
