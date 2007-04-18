<?php
/**
* @package JPSpan
* @subpackage Types
* @version $Id: Types.php,v 1.3 2004/11/18 13:33:26 harryf Exp $
*/
//--------------------------------------------------------------------------------

/**
* Javascript Objects are unserialized into instances of
* of this class
* @package JPSpan
* @subpackage Types
* @access public
*/
class JPSpan_Object {}
//--------------------------------------------------------------------------------

/**
* Used to generate Javascript errors
* @package JPSpan
* @subpackage Types
* @access public
*/
class JPSpan_Error {
    /**
    * Error code
    * @var string
    * @access public
    */
    var $code;
    
    /**
    * Name of Javascript error class
    * @var string
    * @access public
    */
    var $name;
    
    /**
    * Error message
    * @var string
    * @access public
    */
    var $message;
    
    /**
    * Values can be passed optionally to the constructor
    * @param int (optional) error code
    * @param string (optional) name to be given to Javascript error class
    * @param string (optional) error message
    * @return void
    * @access public
    */
    function JPSpan_Error($code=NULL,$name=NULL,$message=NULL) {
        if ( $code && $name && $message ) {
            $this->setError($code,$name,$message);
        }
    }
    
    /**
    * Set the error name and message (also reports to the monitor
    * @see JPSpan_Monitor
    * @param int error code
    * @param string name to be given to Javascript error class
    * @param string error message
    * @return void
    * @access public
    */
    function setError($code,$name,$message) {
        $this->code = $code;
        $this->name = $name;
        $this->message = $message;
        
        require_once JPSPAN . 'Monitor.php';
        $M = & JPSpan_Monitor::instance();
        $M->announceError($name, $code, $message, __FILE__, __LINE__);
    }
}

//--------------------------------------------------------------------------------
/**
* Registers the native types for unserialization. Called when Unserializer.php is
* included (and expects it to already be included)
* @access private
* @return void
* @package JPSpan
* @subpackage Types
*/
function JPSpan_Register_Unserialization_Types() {
    JPSpan_Unserializer::addType('JPSpan_Object');
    JPSpan_Unserializer::addType('JPSpan_Error');
}


