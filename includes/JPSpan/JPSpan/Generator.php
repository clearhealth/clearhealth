<?php
/**
* @package JPSpan
* @subpackage Generator
* @version $Id: Generator.php,v 1.3 2004/11/15 20:27:36 harryf Exp $
*/
//--------------------------------------------------------------------------------

/**
* Generaters client-side Javascript primed to access a server
* Works with JPSpan_HandleDescription to generate
* client primed for a server
* @see JPSpan_Server::getGenerator()
* @package JPSpan
* @subpackage Generator
* @access public
*/
class JPSpan_Generator {

    /**
    * Object responsible for generating client
    * @var object
    * @access private
    */
    var $ClientGenerator;
    
    /**
    * Initialize the generator
    * @param Object responsible for generating client
    * @param array of JPSpan_HandleDescription objects
    * @param string URL of the server
    * @param string encoding to use when making requests (e.g. xml or php)
    * @access public
    */
    function init(& $ClientGenerator, & $descriptions, $serverUrl, $encoding) {
        $this->ClientGenerator = & $ClientGenerator;
        $this->ClientGenerator->descriptions = & $descriptions;
        $this->ClientGenerator->serverUrl = $serverUrl;
        $this->ClientGenerator->RequestEncoding = $encoding;
    }
    
    /**
    * Return the Javascript client for the server
    * @return string Javascript
    * @access public
    */
    function getClient() {
        require_once JPSPAN . 'CodeWriter.php';
        $Code = & new JPSpan_CodeWriter();
        $this->ClientGenerator->generate($Code);
        return $Code->toString();
    }

}

