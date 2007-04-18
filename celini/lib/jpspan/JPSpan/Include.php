<?php
/**
* @package JPSpan
* @subpackage Include
* @version $Id: Include.php,v 1.6 2005/04/25 10:13:20 harryf Exp $
*/
//-----------------------------------------------------------------------------

/**
* When to compress the Javascript (remove whitespace formatting)
* Set to TRUE and Javascript will be "compressed"
*/
if ( !defined('JPSPAN_INCLUDE_COMPRESS') ) {
    define ('JPSPAN_INCLUDE_COMPRESS',FALSE);
}

//-----------------------------------------------------------------------------
/**
* Make sure a file_get_contents() implementation exists
* PHP backwards compatability
*/
if ( !function_exists('file_get_contents') ) {
    /**
    * @see http://www.php.net/file_get_contents
    * @param string filename
    * @return string file content
    * @package JPSpan
    * @subpackage Include
    */
    function file_get_contents($filename) {
        $fd = fopen("$filename", 'rb');
        $content = fread($fd, filesize($filename));
        fclose($fd);
        return $content;
    }
}
//-----------------------------------------------------------------------------

/**
* Include a Javascript file. Filename must be relative to the
* JPSpan/js/ directory (without a leading slash if in subdir)
* This is the recommended point to include Javascript files
* Calling this registers a shutdown function which takes care of displaying
* the Javascript
* @package JPSpan
* @subpackage Include
* @param string filename
* @return void
* @access public
*/
function JPSpan_Include($file) {
    $Includer = & JPSpan_Include::instance();
    $Includer->loadFile($file);
    register_shutdown_function('JPSpan_Include_Shutdown');
}

//-----------------------------------------------------------------------------
/**
* PHP shutdown function making sure Javascript is displayed
* @package JPSpan
* @subpackage Include
* @access private
* @return void
*/
function JPSpan_Include_Shutdown() {
    $Includer = & JPSpan_Include::instance();
    echo $Includer->getCode();
}

//-----------------------------------------------------------------------------

/**
* Loads Javascript but does not register shutdown fn
* @see JPSpan_Include
* @package JPSpan
* @subpackage Include
* @param string filename
* @return void
* @access public
*/
function JPSpan_Include_Register($file) {
    $Includer = & JPSpan_Include::instance();
    $Includer->loadFile($file);
}

//-----------------------------------------------------------------------------

/**
* Loads the Javascript error reader
* @see JPSpan_Include
* @package JPSpan
* @subpackage Include
* @param string (optional) 2 letter localization code e.g. 'en'
* @param array (optional) list of Application_Errors to merge in
* @param array (optional) list of Server_Errors to merge in
* @param array (optional) list of Client_Errors to merge in
* @todo Break this function up
* @return void
* @access public
*/
function JPSpan_Include_ErrorReader($lang='en',$app=array(),$ser=array(),$cli=array()) {
    $errorfile = 'errors.'.$lang.'.ini';
    if ( !file_exists(JPSPAN. 'errors/'.$errorfile) ) {
        $errorfile = 'errors.en.ini';
    }
    
    $errors = parse_ini_file(JPSPAN . 'errors/'.$errorfile,TRUE);

    $script = "/**@\n* include 'util/errorreader.js';\n*/\n";
    // Use Object instead of Array as Javascript will fill empty elements
    $script .= "JPSpan_Util_ErrorReader.prototype.errorList = new Object();\n";
    
    foreach ( $errors['Client_Error'] as $key => $value ) {
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }
    
    foreach ( $cli as $key => $value ) {
        if ( array_key_exists($key, $errors['Client_Error']) ) {
            continue;
        }
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }
    
    foreach ( $errors['Server_Error'] as $key => $value ) {
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }
    
    foreach ( $ser as $key => $value ) {
        if ( array_key_exists($key, $errors['Server_Error']) ) {
            continue;
        }
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }
    
    foreach ( $errors['Application_Error'] as $key => $value ) {
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }
    
    foreach ( $app as $key => $value ) {
        if ( array_key_exists($key, $errors['Application_Error']) ) {
            continue;
        }
        $value = addcslashes($value,"\000\042\047\134");
        $script .= "JPSpan_Util_ErrorReader.prototype.errorList[$key] = '$value';\n";
    }

    
    $Includer = & JPSpan_Include::instance();
    $Includer->loadString('errorreaderlist',$script);
}

//-----------------------------------------------------------------------------

/**
* Returns all loaded Javascript
* @see JPSpan_Include
* @package JPSpan
* @subpackage Include
* @return string
* @access public
*/
function JPSpan_Includes_Fetch() {
    $Includer = & JPSpan_Include::instance();
    return $Includer->getCode();
}

//-----------------------------------------------------------------------------

/**
* Displays all loaded Javascript
* @see JPSpan_Include
* @package JPSpan
* @subpackage Include
* @return void
* @access public
*/
function JPSpan_Includes_Display() {
    echo JPSpan_Includes_Fetch();
}

//-----------------------------------------------------------------------------
/**
* Front for dealing with includes
* @package JPSpan
* @subpackage Include
* @access public
*/
class JPSpan_Include {

    /**
    * @var JPSpan_Include_Manager
    * @access private
    */
    var $Manager;
    
    /**
    * Do not construct JPSpan_Include directly! Use instance method
    * @see instance
    * @access private
    */
    function JPSpan_Include() {
        $this->Manager = & new JPSpan_Include_Manager();
    }
    
    /**
    * Load a Javascript file
    * @param string filename
    * @return void
    * @access public
    */
    function loadFile($file) {
        $file = JPSPAN . 'js/' . $file;
        $this->Manager->loadFile($file);
    }
    
    /**
    * Load a Javascript script from a string
    * @param string source code
    * @return void
    * @access public
    */
    function loadString($name, $src) {
        $this->Manager->load($name,$src);
    }
    
    /**
    * Get the code
    * @return string Javascript
    * @access public
    */
    function getCode() {
        if ( JPSPAN_INCLUDE_COMPRESS ) {
            require_once JPSPAN . 'Script.php';
            $code = $this->Manager->getCode();
            return JPSpan_Script::compress($code);
        } else {
            return $this->Manager->getCode();
        }
    }

    /**
    * Obtain singleton instance of JPSpan_Include
    * @return JPSpan_Include
    * @access public
    * @static
    */
    function & instance() {
        static $importer = NULL;
        
        if ( !$importer ) {
            $importer = new JPSpan_Include();
        }
        
        return $importer;
    }
}

//-----------------------------------------------------------------------------
/**
* Manages the includes, making sure dependencies are resolved
* @package JPSpan
* @subpackage Include
* @access protected
*/
class JPSpan_Include_Manager {

    /**
    * List of files to include. Dependencies are added at end of list
    * @var array
    * @access private
    */
    var $includes = array();
    
    /**
    * Map of file name to source code
    * @var array
    * @access private
    */
    var $code = array();
    
    /**
    * Load a Javascript file
    * @param string full path to file
    * @return void
    * @access protected
    */
    function loadFile($file) {
        $src = file_get_contents($file);
        $this->load($file,$src);
    }
    
    /**
    * Load a Javascript contained in a string
    * @param string indentifier for script (e.g. full path + filename)
    * @param string Javascript source
    * @return void
    * @access protected
    */
    function load($name, $src) {
    
        if ( !in_array($name,$this->includes) ) {
            $this->includes[] = $name;
            $File = & new JPSpan_Include_File($this);
            $File->parse($src);
            $this->code[$name] = $File->src;
            $this->resolveDependencies($File->includes);
        }

    }
    
    /**
    * Resolve any dependencies a script has on others
    * @param array list of dependencies (filenames)
    * @return void
    * @access private
    */
    function resolveDependencies($includes) {
    
        foreach ( $includes as $include ) {
            $src = file_get_contents($include);
            $this->load($include,$src);
        }

    }
    
    /**
    * Get the source
    * @return string
    * @access protected
    */
    function getCode() {
        $includes = array_reverse($this->includes);
        $code = '';
        foreach ( $includes as $include ) {
            $code .= $this->code[$include];
        }
        return $code;
    }
    
}

//-----------------------------------------------------------------------------
/**
* Represents a single file - manages parsing the file for dependencies
* Right now this does no error checking / validation of parse files
* @package JPSpan
* @subpackage Include
* @access protected
*/
class JPSpan_Include_File {

    /**
    * List of dependencies, obtained from parsing the source
    * @var array
    * @access protected
    */
    var $includes = array();
    
    /**
    * Source code with dependency statements removed
    * @var string
    * @access protected
    */
    var $src = '';

    /**
    * Parse the file for dependencies
    * @param string Javascript source
    * @return void
    * @access protected
    */
    function parse($src) {
        $Parser = & new JPSpan_Include_Parser($this);
        $Parser->parse($src);
    }
    
    /**
    * Parser handler
    * @param string script token (base state)
    * @param int state (unused)
    * @access protected
    * @return boolean TRUE
    */
    function script($script, $state) {
        $this->src .= $script;
        return TRUE;
    }
    
    /**
    * Parser handler (discards)
    * @param string declaration
    * @param int state (unused)
    * @access protected
    * @return boolean TRUE
    */
    function declaration($decl, $state) {
        return TRUE;
    }
    
    /**
    * Parser handler - handles include statements
    * @param string include
    * @param int state
    * @access protected
    * @return boolean TRUE
    */
    function inc($file, $state) {
        if ( $state == JPSPAN_LEXER_UNMATCHED ) {
            $file = str_replace(array("'",'"'),'',$file);
            $this->includes[] = JPSPAN . 'js/' . trim($file);
        }
        return TRUE;
    }
    
}

//-----------------------------------------------------------------------------
/**
* Parses source for include statements
* @package JPSpan
* @subpackage Include
* @access protected
*/
class JPSpan_Include_Parser {

    /**
    * Callback handler for parser
    * @var JPSpan_Include_File
    * @access private
    */
    var $Handler;
    
    /**
    * @param JPSpan_Include_File
    * @access protected
    */
    function JPSpan_Include_Parser(& $Handler) {
        $this->Handler = & $Handler;
    }
    
    /**
    * Parse some Javascript
    * @param string Javascript
    * @return void
    * @access protected
    */
    function parse($src) {
        $Lexer = & $this->getLexer();
        $Lexer->parse($src);
    }
    
    /**
    * Create the Lexer
    * @see JPSpan_Lexer
    * @return JPSpan_Lexer
    * @access private
    */
    function & getLexer() {
        require_once JPSPAN . 'Lexer.php';
        $Lexer = new JPSpan_Lexer($this->Handler,'script');
        
        $Lexer->addEntryPattern('/\*\*@','script','declaration');
        $Lexer->addExitPattern('\*/','declaration');
        
        $Lexer->addEntryPattern('include','declaration','inc');
        $Lexer->addExitPattern(';','inc');

        return $Lexer;
    }

}