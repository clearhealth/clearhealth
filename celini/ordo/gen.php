<?php
require_once dirname(__FILE__) . "/../bootstrap.php";
require_once CELINI_ROOT."/controllers/Controller.class.php";
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";

$package = "com.uversainc.celini";
$author = "Uversa Inc.";
$table = $argv[1];


$or =& new ORDataObject();
$class = ucfirst($table);

if (strstr($class,'_')) {
	$tmp = explode('_',$class);
	$class = '';
	foreach($tmp as $part) {
		$class .= ucfirst($part);
	}
}
$or->_table = $table;

$fieldList = $or->metadata->listFields();

$pkey = $or->metadata->getPrimaryKey();

$file = "<?php
/**
 * Object Relational Persistence Mapping Class for table: $table
 *
 * @package	$package
 * @author	$author
 */
class $class extends ORDataObject {

	/**#@+
	 * Fields of table: $table mapped to class members
	 */
";
$out = "";
foreach($fieldList as $field) {
	switch($or->metadata->getType($field)) {
		case 'date':
		case 'datetime':
			$out .= "
	/**#@+
	 * Field: $field, time formatting
	 */
	function get_$field() {
		return \$this->_getDate('$field');
	}
	function set_$field(\$date) {
		\$this->_setDate('$field',\$date);
	}
	/**#@-*/\n";
			break;
		default:
	}
	$file .= "\tvar \$$field		= '';\n";
}

$file .= "\t/**#@-*/


	/**
	 * DB Table
	 */
	var \$_table = '$table';

	/**
	 * Primary Key
	 */
	var \$_key = '$pkey';
	
	/**
	 * Internal Name
	 */
	var \$_internalName = '$class';

	/**
	 * Handle instantiation
	 */
	function $class() {
		parent::ORDataObject();
	}

	$out
}
?>
";

$filePath = APP_ROOT."/local/ordo/$class.class.php";
if (file_exists($filePath)) {
	die("File $filePath already exists");
}
$fp = fopen($filePath,'w');
fwrite($fp,$file);
fclose($fp);

?>
