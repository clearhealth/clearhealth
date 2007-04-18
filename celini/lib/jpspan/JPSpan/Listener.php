<?php
/**
* @package JPSpan
* @subpackage Listener
* @version $Id: Listener.php,v 1.2 2004/11/15 10:47:19 harryf Exp $
*/
//-----------------------------------------------------------------------------

/**
* Include handlers for incoming request data
*/
require_once JPSPAN . 'RequestData.php';

/**
* Check always_populate_raw_post_data is switched off
*/
if ( ini_get('always_populate_raw_post_data') ) {
    trigger_error (
        "Configuration error: PHP ini setting 'always_populate_raw_post_data' must be off",
        E_USER_ERROR
    );
}
//-----------------------------------------------------------------------------

/**
* Listener for incoming requests
* @package JPSpan
* @subpackage Listener
* @access public
*/
class JPSpan_Listener {

    /**
    * Encoding used by request (e.g. 'xml' or 'php')
    * @var string
    * @access public
    */
    var $encoding = 'xml';
    
    /**
    * Object which responds to request
    * @var object implementing Responder interface
    * @access private
    */
    var $Responder;
    
    /**
    * Constructs the listener, setting the default NullResponder
    * @access public
    */
    function JPSpan_Listener() {
        $this->Response = & new JPSpan_NullResponder();
    }
    
    /**
    * Set the Responder
    * @param object implementing Responder interface
    * @return void
    * @access public
    */
    function setResponder(& $Responder) {
        $this->Responder= & $Responder;
    }
    
    /**
    * Serve incoming requests
    * @return void
    * @access public
    */
    function serve() {  
        $this->Responder->execute($this->getRequestData());
    }
    
    /**
    * Detects the type of incoming request and calls the corresponding
    * RequestData handler to deal with it.
    * @return mixed request data as native PHP variables.
    * @access private
    */
    function getRequestData () {
        switch ( $_SERVER['REQUEST_METHOD'] ) {
            case 'POST':
                global $HTTP_RAW_POST_DATA;
                if ( $HTTP_RAW_POST_DATA ) {
                    return JPSpan_RequestData_RawPost::fetch($this->encoding);
                } else {
                    return JPSpan_RequestData_Post::fetch($this->encoding);
                }
            break;
            case 'GET':
            default:
                return JPSpan_RequestData_Get::fetch($this->encoding);
            break;
        }
    }
    
}
//-----------------------------------------------------------------------------

/**
* A NullResponder loaded as the default responder
* @package JPSpan
* @subpackage Listener
* @access public
*/
class JPSpan_NullResponder {
    /**
    * Does nothing
    * @param mixed incoming request data
    * @return void
    * @access public
    */
    function execute(& $payload) {}
}


