<?php
/**
* @package JPSpan
* @subpackage Monitor
* @version $Id: Monitor.php,v 1.1 2004/11/18 13:33:26 harryf Exp $
*/
//--------------------------------------------------------------------------------
/**
* Define as TRUE to switch on monitor
*/
if ( !defined('JPSPAN_MONITOR') ) {
    define('JPSPAN_MONITOR',FALSE);
}

/**
* Observable for logging - notifies registered logger of events
* You should create instances of this using the instance method
* @package JPSpan
* @subpackage Monitor
* @access public
*/
class JPSpan_Monitor {

    /**
    * Array of request info containing keys 'class', 'method', 'args'
    * @var array
    * @access private
    */
    var $requestInfo = array('class'=>NULL,'method'=>NULL,'args'=>NULL);
    
    /**
    * Array of response info containing keys 'payload'
    * @var array
    * @access private
    */
    var $responseInfo = array('payload'=>NULL);
    
    /**
    * Objects observing the monitor
    * @var array
    * @access private
    */
    var $observers = array();
    
    /**
    * Register and observer for notifications
    * @see JPSpan_Monitor_Observer
    * @param object
    * @return void
    * @access public
    */
    function addObserver(& $Observer) {
        $this->observers[] = & $Observer;
    }
    
    /**
    * Add a value to the request info.
    * @param string key ('class', 'method'  or 'args')
    * @return void
    * @access protected
    */
    function setRequestInfo($key,$value) {
        $this->requestInfo[$key] = $value;
    }
    
    /**
    * Add a value to the response info.
    * @param string key ('payload')
    * @return void
    * @access protected
    */
    function setResponseInfo($key,$value) {
        $this->responseInfo[$key] = $value;
    }
    
    /**
    * Captures data about the current environment, before a notification
    * @return array
    * @access private
    */
    function prepareData() {
        global $HTTP_RAW_POST_DATA;
        
        $Data = array (
            'timestamp' => time(),
            'gmt' => gmdate("D, d M Y H:i:s", time())." GMT",
            'requestInfo' => $this->requestInfo,
            'responseInfo' => $this->responseInfo,
            'SERVER'=>$_SERVER,
            'GET'=>$_GET,
            'POST'=>$_POST,
            'RAWPOST'=>$HTTP_RAW_POST_DATA,
        );
        
        if ( function_exists('apache_request_headers') ) {
            $Data['requestHeaders']= apache_request_headers();
            $Data['responseHeaders'] = apache_response_headers();
        }
        return $Data;
    }
    
    /**
    * Report and error to observers
    * @param string name of error
    * @param int error code
    * @param string error message
    * @param string file where error was triggered
    * @param int line number in file where error was triggered
    * @return void
    * @access protected
    */
    function announceError($name, $code, $message, $file, $line) {
        $Data = $this->prepareData();
        $Data['errorName'] = $name;
        $Data['errorCode'] = $code;
        $Data['errorMsg'] = $message;
        $Data['errorFile'] = $file;
        $Data['errorLine'] = $line;
        foreach (array_keys($this->observers) as $key) {
          $this->observers[$key]->error($Data);
        }
    }
    
    /**
    * Report successful request / response to observers
    * @return void
    * @access protected
    */
    function announceSuccess() {
        $Data = $this->prepareData();
        foreach (array_keys($this->observers) as $key) {
          $this->observers[$key]->success($Data);
        }
    }

    /**
    * Create an instance of the Monitor
    * @param boolean used for unit tests to override constant
    * @return JPSpan_Monitor or JPSpan_Monitor_Null is monitoring disabled
    * @access public
    */
    function & instance($getMonitor = FALSE) {
        static $Monitor = NULL;
        if ( !$Monitor ) {
            // Allow constant or argument to specify use of the real instance
            if ( JPSPAN_MONITOR || $getMonitor ) {
                $Monitor = new JPSpan_Monitor();
            } else {
                $Monitor = new JPSpan_Monitor_Null();
            }
        }
        return $Monitor;
    }

}

/**
* Null monitor for when monitoring is disabled
* @package JPSpan
* @subpackage Monitor
* @access public
*/
class JPSpan_Monitor_Null {

    function addObserver(& $Observer) {}
    
    function setRequestInfo($key,$value) {}
    
    function setResponseInfo($key,$value) {}
    
    function announceError($name, $code, $message, $file, $line) {}
    
    function announceSuccess() {}

}

/**
* Interface observers should provide. Just for info - you don't need to directly extend it
* @package JPSpan
* @subpackage Monitor
* @access public
*/
class JPSpan_Monitor_Observer {

    /**
    * Called when an error occurs
    * @param array request / response / error / environment data snapshot
    * @return void
    * @access public
    */
    function error($Data) {}

    /**
    * Called on a successful request / response
    * @param array request / response / environment data snapshot
    * @return void
    * @access public
    */
    function success($Data) {}
    
}
