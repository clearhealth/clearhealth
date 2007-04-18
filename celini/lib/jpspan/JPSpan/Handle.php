<?php
/**
* Swiped from WACT: http://wact.sourceforge.net (handle.inc.php)
* @package JPSpan
* @subpackage Handle
* @see http://wact.sourceforge.net/index.php/ResolveHandle
* @version $Id: Handle.php,v 1.1 2004/11/09 13:30:39 harryf Exp $
*/
//-----------------------------------------------------------------------------

/**
* Contains static methods for resolving and reflecting on handles
* @see http://wact.sourceforge.net/index.php/Handle
* @package JPSpan
* @subpackage Handle
*/
class JPSpan_Handle {

    /**
    * Takes a "handle" to an object and modifies it to convert it to an instance
    * of the class. Allows for "lazy loading" of objects on demand.
    * @see http://wact.sourceforge.net/index.php/ResolveHandle
    * @todo Cases where Handle not array, string or object?
    * @param mixed
    * @return boolean FALSE if handle not resolved
    * @access public
    * @static
    */
    function resolve(&$Handle) {
    
        switch ( gettype($Handle) ) {
            case 'array':
                $Class = array_shift($Handle);
                $ConstructionArgs = $Handle;
            break;
            case 'string':
                $ConstructionArgs = array();
                $Class = $Handle;
            break;
            case 'object':
                return TRUE;
            break;
            default:
                return FALSE;
            break;
        }
        
        if (is_integer($Pos = strpos($Class, '|'))) {
            $File = substr($Class, 0, $Pos);
            $Class = substr($Class, $Pos + 1);
            require_once $File;
        }
        
        switch (count($ConstructionArgs)) {
            case 0:
                $Handle = new $Class();
                break;
            case 1:
                $Handle = new $Class(array_shift($ConstructionArgs));
                break;
            case 2:
                $Handle = new $Class(
                    array_shift($ConstructionArgs), 
                    array_shift($ConstructionArgs));
                break;
            case 3:
                $Handle = new $Class(
                    array_shift($ConstructionArgs), 
                    array_shift($ConstructionArgs), 
                    array_shift($ConstructionArgs));
                break;
            default:
                trigger_error(
                    'Maximum constructor arg count exceeded',
                    E_USER_ERROR
                );
                return FALSE;
                break;
        }
        return TRUE;
    }
    
    /**
    * Determines the "public" class methods exposed by a handle
    * Class constructors and methods beginning with an underscore
    * are ignored.
    * @see http://wact.sourceforge.net/index.php/ResolveHandle
    * @todo Cases where Handle not array, string or object?
    * @param mixed
    * @return mixed JPSpan_HandleDescription or FALSE if invalid handle
    * @access public
    * @static
    */
    function examine($Handle) {

        switch ( gettype($Handle) ) {
            case 'array':
                $Class = array_shift($Handle);
            break;
            case 'string':
                $Class = $Handle;
            break;
            case 'object':
                $Class = get_class($Handle);
            break;
            default:
                return FALSE;
            break;
        }
        
        if (is_integer($Pos = strpos($Class, '|'))) {
                $File = substr($Class, 0, $Pos);
                $Class = substr($Class, $Pos + 1);
                require_once $File;
        }
        
        $Class = strtolower($Class);
        
        $Description = new JPSpan_HandleDescription();
        $Description->Class = $Class;
        
        $methods = get_class_methods($Class);
        if ( is_null($methods) ) {
            return FALSE;
        }
        $methods = array_map('strtolower',$methods);
        
        if ( FALSE !== ( $constructor = array_search($Class,$methods) ) ) {
            unset($methods[$constructor]);
        }
        
        foreach ( $methods as $method ) {
            if ( preg_match('/^[a-z]+[0-9a-z_]*$/',$method) == 1 ) {
                $Description->methods[] = $method;
            }
        }
        
        return $Description;
    }

}
//-----------------------------------------------------------------------------

/**
* Describes a handle: used to help generate Javascript clients
* and validate incoming calls
* @package JPSpan
* @subpackage Handle
*/
class JPSpan_HandleDescription {

    /**
    * @var string class name for handle
    * @access public
    */
    var $Class = '';

    /**
    * @var array methods exposed by handle
    * @access public
    */
    var $methods = array();
    
}


