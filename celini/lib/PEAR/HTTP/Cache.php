<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Stephan Schmidt <schst@php.net>                             |
// +----------------------------------------------------------------------+
//
//    $Id: Cache.php,v 1.2 2004/12/07 21:43:59 schst Exp $

/**
 * HTTP_Cache
 *
 * Auxiliary class to implement conditional GET requests.
 * This class sends and checks all necessary HTTP headers.
 *
 * @author      Stephan Schmidt <schst@php.net>
 * @category    HTTP
 * @package     HTTP_Cache
 * @version     0.1
 */

/**
 * no data available to calculate an ETag
 */
define('HTTP_CACHE_ERROR_NO_BODY', 1);

/**
 * Headers could not be sent
 */
define('HTTP_CACHE_ERROR_HEADERS_SENT', 2);

/**
 * PEAR error management
 */
require_once 'PEAR.php';

/**
 * HTTP_Cache
 *
 * Auxiliary class to implement conditional GET requests.
 * This class sends and checks all necessary HTTP headers.
 *
 * @author      Stephan Schmidt <schst@php.net>
 * @category    HTTP
 * @package     HTTP_Cache
 * @version     0.1
 */
class HTTP_Cache
{
   /**
    * ETag that has been sent by the client
    *
    * @access   private
    * @var      string
    */
    var $_clientETag = null;

   /**
    * ETag that has been calculated on the server
    *
    * @access   private
    * @var      string
    */
    var $_serverETag = null;

   /**
    * Body of the HTTP response
    *
    * @access   private
    * @var      string
    */
    var $_body = null;

   /**
    * create a new HTTP_Cache
    *
    * @access   public
    * @param    array   options
    */
    function HTTP_Cache($options = array())
    {
        // client has an etag
    	if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    		$this->_clientETag = $_SERVER['HTTP_IF_NONE_MATCH'];
    	}
    	
    	if (isset($options['auto']) && $options['auto'] == true) {
    		ob_start(array(&$this, '_captureOutput'));
    	}
    }
    
   /**
    * set the reponse body
    *
    * @access   public
    * @param    string      response body (your HTML code)
    */
    function setBody($body)
    {
        $this->_body = $body;
    }

   /**
    * set the etag
    *
    * @access   public
    * @param    string      etag
    */
    function setEtag($eTag)
    {
        $this->_serverETag = $eTag;
    }

   /**
    * send the data or the not modified header
    *
    * @access   public
    * @return   boolean
    */
    function send()
    {
        if ($this->isValid()) {
        	if (!$this->_sendNotModified()) {
        		return PEAR::raiseError('Could not send headers.', HTTP_CACHE_ERROR_HEADERS_SENT);
        	}
        } else {
        	if (!$this->_sendHeaders()) {
        		return PEAR::raiseError('Could not send headers.', HTTP_CACHE_ERROR_HEADERS_SENT);
        	}
            // send the body
            echo $this->_body;
        }
        return true;
    }

   /**
    * check, whether the current cache is valid
    *
    * @access   public
    * @return   boolean
    */
    function isValid()
    {
        if (empty($this->_serverETag)) {
        	$this->_serverETag = $this->calculateETag();
        }
        if (PEAR::isError($this->_serverETag)) {
            $this->_serverETag = null;
        	return $this->_serverETag;
        }
        if ($this->_clientETag == $this->_serverETag) {
            return true;
        }
        return false;
    }
    
   /**
    * send needed headers for the HTTP cache
    *
    * @access   private
    * @return   boolean
    */
    function _sendHeaders()
    {
        if (headers_sent($file, $line)) {
        	return false;
        }
        header('Cache-Control: must-revalidate');
        header('ETag: '.$this->_serverETag);
    	return true;
    }

   /**
    * send the header for data that has not been modified
    *
    * @access   private
    * @return   boolean
    */
    function _sendNotModified()
    {
        if (headers_sent()) {
        	return false;
        }
        header('HTTP/1.0 304 Not Modified');
        return true;
    }
    
   /**
    * calculate the etag based on the request body
    * 
    * @access   public
    * @return   string
    */
    function calculateETag()
    {
    	if ($this->_body === null) {
    		return PEAR::raiseError('No request body has been set to generate the ETag.', HTTP_CACHE_ERROR_NO_BODY);
    	}
    	return md5($this->_body);
    }

   /**
    * function used to capture output using ob_start()
    *
    * @access   private
    * @param    string      content of output buffer
    * @return   string      content to send (or null, if cache was used)
    */
    function _captureOutput($output)
    {
        $this->setBody($output);
        if ($this->isValid()) {
        	$this->_sendNotModified();
        	return null;
        }
        $this->_sendHeaders();
        return $output;
    }
}
?>