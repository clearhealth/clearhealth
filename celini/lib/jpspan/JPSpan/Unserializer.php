<?php
/**
* @package JPSpan
* @subpackage Unserialzier
* @version $Id: Unserializer.php,v 1.3 2004/11/17 20:40:05 harryf Exp $
*/
//--------------------------------------------------------------------------------

/**
* Global array of known classes which may be unserialized.
* Use JPSpan_Unserializer::register() to register new types
* @see JPSpan_Unserializer::register
* @package JPSpan
* @subpackage Unserialzier
*/
$GLOBALS['_JPSPAN_UNSERIALIZER_MAP'] = array();

/**
* Include the Script Server types for mapping JS <> PHP
*/
require_once JPSPAN . 'Types.php';

/**
* Register the allowed unserialization objects
* Function defined in Types script
* @todo Change the name of this function
*/
JPSpan_Register_Unserialization_Types();
//--------------------------------------------------------------------------------

/**
* Handles unserialization on incoming request data
* @package JPSpan
* @subpackage Unserialzier
* @access public
*/
class JPSpan_Unserializer {
    /**
    * Unserialize a string into PHP data types. 
    * @param string data serialized with PHP's serialization protocol
    * @param string encoding (default = 'xml') - how the data is serialized
    * @return mixed PHP data
    * @access private
    * @static
    */
    function unserialize($data, $encoding = 'xml') {
        switch ( $encoding ) {
            case 'php':
                require_once JPSPAN . 'Unserializer/PHP.php';
                $U = & new JPSpan_Unserializer_PHP();
            break;
            case 'xml':
            default:
                require_once JPSPAN . 'Unserializer/XML.php';
                $U = & new JPSpan_Unserializer_XML();
            break;
        }
        return $U->unserialize($data);
    }
    
    /**
    * Register a known class for unserialization
    * Places a value in the global _JPSpan_UNSERIALIZER_MAP variable
    * @param string class name
    * @param string (optional) script include - your job to get path right!
    * @return void
    * @access public
    * @static
    */
    function addType($class, $file = NULL) {
        $GLOBALS['_JPSPAN_UNSERIALIZER_MAP'][strtolower($class)] = $file;
    }
    

}

