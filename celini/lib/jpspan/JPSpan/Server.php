<?php
/**
* @package JPSpan
* @subpackage Server
* @version $Id: Server.php,v 1.7 2005/04/28 09:58:58 harryf Exp $
*/
//--------------------------------------------------------------------------------
/**
* Define
*/
if ( !defined('JPSPAN') ) {
    define ('JPSPAN',dirname(__FILE__).'/');
}
/**
* Include
*/
require_once JPSPAN . 'Handle.php';
//--------------------------------------------------------------------------------

/**
* Base Server class.
* @package JPSpan
* @subpackage Server
* @public
* @abstract
*/
class JPSpan_Server {

    /**
    * Hash of user defined handlers (keys are class name)
    * @var array
    * @access private
    */
    var $handlers = array();
    
    /**
    * Descriptions of handlers stored here as hash
    * @var array
    * @access private
    */
    var $descriptions = array();
    
    /**
    * URL where server is published
    * @var string
    * @access private
    */
    var $serverUrl;

    /**
    * Sets up the default server url
    * @access public
    */
    function JPSpan_Server() {
        if ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) {
            $prot = 'https://';
        } else {
            $prot = 'http://';
        }
        $this->serverUrl = $prot.$_SERVER['HTTP_HOST'].$this->resolveScriptName();
    }
    
    /**
    * Set the URL where the server is published
    * @param string server url (where the server is public)
    * @return void
    * @access public
    */
    function setServerUrl($serverUrl) {
        $this->serverUrl = $serverUrl;
    }
    
    /**
    * Return the server url
    * @return string server url (where the server is public)
    * @access public
    */
    function getServerUrl() {
        return $this->serverUrl;
    }
    
    /**
    * Return reference to a handler given it's name.
    * Note this will also resolve the handle
    * @param string handler name (class name)
    * @return mixed object handler or FALSE if not found
    * @access public
    */
    function & getHandler($name) {
        $name = strtolower($name);
        if ( isset($this->handlers[$name]) ) {
            JPSpan_Handle::resolve($this->handlers[$name]);
            return $this->handlers[$name];
        }
        return FALSE;
    }
    
    /**
    * Return handler description given it's name
    * @param string handler name (class name)
    * @return mixed object handler description or FALSE if not found
    * @access public
    */
    function getDescription($name) {
        $name = strtolower($name);
        if ( isset($this->descriptions[$name]) ) {
            return $this->descriptions[$name];
        }
        return FALSE;
    }

    /**
    * Registers a user handler class with the server
    * @see http://wact.sourceforge.net/index.php/Handle
    * @param mixed handle to user class
    * @return void
    * @access public
    */
    function addHandler(& $Handle, $Description = NULL) {
        if ( is_null($Description) ) {
            if ( FALSE !== ($Description = JPSpan_Handle::examine($Handle)) ) {
                $this->handlers[$Description->Class] = & $Handle;
                $this->descriptions[$Description->Class] = $Description;
            } else {
                trigger_error('Invalid handle',E_USER_ERROR);
            }
        } else {
            if ( isset($Description->Class) && is_string($Description->Class) && is_array($Description->methods) ) {
                $Description->Class = strtolower($Description->Class);
                $Description->methods = array_map('strtolower',$Description->methods);
                $this->handlers[strtolower($Description->Class)] = & $Handle;
                $this->descriptions[strtolower($Description->Class)] = $Description;
            } else {
                trigger_error('Invalid description',E_USER_ERROR);
            }
        }
    }

    /**
    * Returns object for generating the client
    * @return object
    * @access public
    * @abstract
    */
    function getGenerator() {}

    /**
    * Start serving (override in subclasses)
    * @return boolean FALSE if serve failed
    * @access public
    * @abstact
    */
    function serve() {}
    
    /**
    * Returns the portion of the URL to the right of the executed
    * PHP script e.g. http://localhost/index.php/foo/bar/ returns
    * 'foo/bar'. Returns the string up to the end or to the first ?
    * character
    * @return string
    * @access public
    * @static
    */
    function getUriPath() {
        
        $basePath = explode('/',$this->resolveScriptName());
        $script = array_pop($basePath);
        $basePath = implode('/',$basePath);
        
        // Determine URI path - path variables to the right of the PHP script
        if ( $script && ( false !== strpos ( $_SERVER['REQUEST_URI'], $script ) ) ) {
            $uriPath = explode( $script,$_SERVER['REQUEST_URI'] );
            $uriPath = $uriPath[1];
        } else {
            $pattern = '/^'.str_replace('/','\/',$basePath).'/';
            $uriPath = preg_replace($pattern,'',$_SERVER['REQUEST_URI']);
        }
        if ( FALSE !== ( $pos = strpos($uriPath,'?') )  ) {
            $uriPath = substr($uriPath,0,$pos);
        }
        $uriPath = preg_replace(array('/^\//','/\/$/'),'',$uriPath);
        return $uriPath;
        
    }
    
    /**
    * Introspects the name of the script. Depending on the PHP SAPI
    * determining the name of the current script varies. This will probably
    * need updating later and testing under a number of environments
    * @return string script name
    * @access public
    */
    function resolveScriptName() {
        if ( isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] == $_SERVER['PHP_SELF'] ) {
            $script_name = $_SERVER['PATH_INFO'];
        } else {
            $script_name = $_SERVER['SCRIPT_NAME'];
        }
        return $script_name;
    }
    
    /**
    * Load the error reader
    * @param string (optional) 2 letter localization code e.g. 'en'
    * @param array (optional) list of Application_Errors to merge in
    * @param array (optional) list of Server_Errors to merge in
    * @param array (optional) list of Client_Errors to merge in
    * @todo Break this function up
    * @return void
    * @access public
    */
    function loadErrorReader($lang='en',$app=array(),$ser=array(),$cli=array()) {
        require_once JPSPAN . 'Include.php';
        JPSpan_Include_ErrorReader($lang,$app,$ser,$cli);
    }
    
    /**
    * Display the Javascript client and exit
    * @return void
    * @access public
    */
    function displayClient() {
        $G = & $this->getGenerator();
        require_once JPSPAN . 'Include.php';
        $I = & JPSpan_Include::instance();
        
        // HACK - this needs to change
        $I->loadString(__FILE__,$G->getClient());
        $client = $I->getCode();
        header('Content-Type: application/x-javascript');
        header('Content-Length: '.strlen($client));
        echo $client;
        exit();
    }
    
}


