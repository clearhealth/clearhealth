<?php
/**
* Library for serializing PHP variables into Javascript for use with
* Javascript eval()
* @package JPSpan
* @subpackage Serializer
* @version $Id: Serializer.php,v 1.5 2005/05/26 23:09:37 harryf Exp $
*/
//-----------------------------------------------------------------------------

/**
* Includes
*/
require_once JPSPAN . 'CodeWriter.php';
//-----------------------------------------------------------------------------

/**
* Define global for mapping PHP types to element generation
* classes
* @package JPSpan
* @subpackage Serializer
*/
$GLOBALS['_JPSPAN_SERIALIZER_MAP'] = array(
    'string'=>array(
        'class'=>'JPSpan_SerializedString',
        'file'=>NULL
        ),
    'integer'=>array(
        'class'=>'JPSpan_SerializedInteger',
        'file'=>NULL
        ),
    'boolean'=>array(
        'class'=>'JPSpan_SerializedBoolean',
        'file'=>NULL
        ),
    'double'=>array(
        'class'=>'JPSpan_SerializedFloat',
        'file'=>NULL
        ),
    'null'=>array(
        'class'=>'JPSpan_SerializedNull',
        'file'=>NULL
        ),
    'array'=>array(
        'class'=>'JPSpan_SerializedArray',
        'file'=>NULL
        ),
    'object'=>array(
        'class'=>'JPSpan_SerializedObject',
        'file'=>NULL
        ),
    'jpspan_error'=>array(
        'class'=>'JPSpan_SerializedError',
        'file'=>NULL
        ),
    );
//-----------------------------------------------------------------------------

/**
* Serializes PHP data types into a JavaScript string containing an Function
* object for use with eval()<br>
* Based on Frederic Saunier's JSserializerCLASS<br/>
* Example:
* <pre>
* $myVar = 'Hello World!';
* echo JPSpan_Serializer::serialize($myVar);
* // Displays: new Function("var t1 = \'Hello World!\';return t1;");
* </pre>
* Use in Javascript would be;
* <pre>
* var data_serialized = 'new Function("var t1 = \'Hello World!\';return t1;");';
* var data_func = eval(data_serialized);
* var data = data_func(); // data now contains string: Hello World!
* </pre>
* @see http://www.tekool.net/php/js_serializer/
* @package JPSpan
* @subpackage Serializer
* @access public
*/
class JPSpan_Serializer {
    /**
    * Serializes a PHP data structure into Javascript
    * @param mixed PHP data structure
    * @return string data as Javascript
    * @access public
    * @static
    */
    function serialize($data) {
        JPSpan_getTmpVar(TRUE);
        $code = & new JPSpan_CodeWriter();
        $root = & new JPSpan_RootElement($data);
        $root->generate($code);
        return $code->toString();
    }
    /**
    * Adds an entry to the type map
    * @param string name of type
    * @param string name of PHP class to map type to
    * @param string (optional) filename where class can be found
    * @return void
    * @access public
    * @static
    */
    function addType($type,$class,$file=NULL) {
        $GLOBALS['_JPSPAN_SERIALIZER_MAP'][strtolower($type)] =
            array (
                'class'=>$class,
                'file'=>$file,
            );
    }
    /**
    * Determine the type of a PHP value, returning an object
    * used to generated a serialized Javascript representation
    * @param mixed PHP variable
    * @return object subclass of JPSpan_SerializedElement
    * @access protected
    */
    function & reflect($data) {
        $type = strtolower(gettype($data));
        if ( $type == 'object' ) {
            $objtype = strtolower(get_class($data));
            if (array_key_exists($objtype,$GLOBALS['_JPSPAN_SERIALIZER_MAP']) ) {
                $type = $objtype;
            }
        }
        if ( array_key_exists($type,$GLOBALS['_JPSPAN_SERIALIZER_MAP']) ) {
            $class = $GLOBALS['_JPSPAN_SERIALIZER_MAP'][$type]['class'];
            $file = $GLOBALS['_JPSPAN_SERIALIZER_MAP'][$type]['file'];
            if ( !is_null($file) ) {
                require_once $file;
            }
            $element = & new $class();
        } else {
            $element = & new JPSpan_SerializedNull();
        }
        $element->setTmpVar();
        $element->setValue($data);
        return $element;
    }
}
//-----------------------------------------------------------------------------

/**
* Function for generating temporary variable names for use in
* serialized Javascript. Uses a static counter to keep names
* unique
* @return string e.g. t2
* @access protected
* @package JPSpan
* @subpackage Serializer
*/
function JPSpan_getTmpVar($refresh = FALSE) {
    static $count = 1;
    if ( !$refresh ) {
        $name = 't'.$count;
        $count++;
        return $name;
    }
    $count = 1;
}
//-----------------------------------------------------------------------------
/**
* Wraps the generated JavaScript in an anonymous function
* @access protected
* @package JPSpan
* @subpackage Serializer
*/
class JPSpan_RootElement {

    /**
    * Data to be serialized
    * @var mixed
    * @access private
    */
    var $data;

    /**
    * @param mixed data to be serialized
    * @access protected
    */
    function JPSpan_RootElement($data) {
        $this->data = $data;
    }
    
    /**
    * Triggers code generation for child data structure then wraps
    * in anonymous function
    * @param CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {

        $child = & JPSpan_Serializer::reflect($this->data);
        $child->generate($code);

        $code->write('new Function("'.addcslashes($code->toString(),"\000\042\047\134").$child->getReturn().'");');
    }
}

/**
* Base of class hierarchy for generating Javascript
* @access protected
* @package JPSpan
* @subpackage Serializer
* @abstract
*/
class JPSpan_SerializedElement {
    /**
    * Value of the element - used only for scalar types
    * @var mixed
    * @access private
    */
    var $value;

    /**
    * Temporary variable name to use in serialized Javascript
    * @var string
    * @access private
    */    
    var $tmpName;

    /**
    * Sets the value of the element
    * @param mixed
    * @return void
    * @access protected
    */
    function setValue($value) {
        $this->value = $value;
    }

    /**
    * Sets the temporary variable name
    * @return void
    * @access protected
    */
    function setTmpVar() {
        $this->tmpName = JPSpan_getTmpVar();
    }

    /**
    * JavaScript string to return if this is the root data element
    * Called from JPSpan_RootElement::generate
    * @return string
    * @access protected
    */
    function getReturn() {
        return 'return '.$this->tmpName.';';
    }

    /**
    * Template method for generating code
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {}
    
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of a string in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedString extends JPSpan_SerializedElement {
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        $value = addcslashes($this->value,"\000\042\047\134");
        $value = str_replace("\r\n",'\n',$value);
        $value = str_replace("\n",'\n',$value);
        $value = str_replace("\r",'\n',$value);
        $value = str_replace("\t",'\t',$value);
        $code->append("var {$this->tmpName} = '$value';");
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of a boolean value in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedBoolean extends JPSpan_SerializedElement {
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        if ( $this->value ) {
            $code->append("var {$this->tmpName} = true;");
        } else {
            $code->append("var {$this->tmpName} = false;");
        }
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of an integer value in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedInteger extends JPSpan_SerializedElement {
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        $code->append("var {$this->tmpName} = parseInt('{$this->value}');");
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of a float value in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedFloat extends JPSpan_SerializedElement {
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        $code->append("var {$this->tmpName} = parseFloat('{$this->value}');");
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of a null value in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedNull extends JPSpan_SerializedElement {
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        $code->append("var {$this->tmpName} = null;");
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of an array in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedArray extends JPSpan_SerializedElement {
    /**
    * Representations of the elements of the array
    * @var array
    * @access private
    */
    var $children = array();
    /**
    * @param mixed
    * @return void
    * @access protected
    */
    function setValue($value) {
        foreach ( $value as $key => $value ) {
            $this->children[$key] = & JPSpan_Serializer::reflect($value);
        }
    }
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {
        $code->append("var {$this->tmpName} = new Array();");
        foreach ( array_keys($this->children) as $key ) {
            $this->children[$key]->generate($code);
            $tmpName = $this->children[$key]->tmpName;
            // Spot the difference between index and hash keys..
            if ( preg_match('/^[0-9]+$/',$key) ) {
                $code->append("{$this->tmpName}[$key] = $tmpName;");
            } else {
                $code->append("{$this->tmpName}['$key'] = $tmpName;");
            }
        }
        
        // Override Javascript toString to display hash values
        $toString = "function() { ";
        $toString.= "var str = '[';";
        $toString.= "var sep = '';";
        $toString.= "for (var prop in this) { ";
        $toString.= "if (prop == 'toString') { continue; }";
        $toString.= "str+=sep+prop+': '+this[prop];";
        $toString.= "sep = ', ';";
        $toString.= "} return str+']';";
        $toString.= "}";

        $code->append("{$this->tmpName}.toString = $toString;");
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of an object in Javascript
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedObject extends JPSpan_SerializedElement {
    /**
    * Name for Javascript object
    * @var string (= Object)
    * @access private
    */
    var $classname = 'Object';
    /**
    * Representations of the properties of the object
    * @var array
    * @access private
    */
    var $children = array();
    /**
    * @param mixed
    * @return void
    * @access protected
    */
    function setValue($value) {
        $this->setChildValues($value);
    }
    /**
    * Called from setValue. Sets the value of all children of
    * an object
    * @param mixed value
    * @return void
    * @access protected
    */
    function setChildValues($value) {
        $properties = get_object_vars($value);
        foreach ( array_keys($properties) as $property ) {
            $this->children[$property] = & JPSpan_Serializer::reflect($value->$property);
        }
    }
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */    
    function generate(&$code) {
        $code->append('var '.$this->tmpName.' = new '.$this->classname.'();');
        $this->generateChildren($code);
    }
    /**
    * Called from generate. Invokes generate on each child
    * of the object
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */    
    function generateChildren(&$code) {
        foreach ( array_keys($this->children) as $key ) {
            $this->children[$key]->generate($code);
            $tmpName = $this->children[$key]->tmpName;
            if ( preg_match('/^[0-9]+$/',$key) ) {
                $code->append("{$this->tmpName}[$key] = $tmpName;");
            } else {
                $code->append("{$this->tmpName}.$key = $tmpName;");
            }
        }
    }
}
//-----------------------------------------------------------------------------

/**
* Generates the representation of a JPSpan_Error object.
* Note that you can only generate a single error and that it will
* erase all other generated code (the first error in a data structure
* will be that which be generated)
* @package JPSpan
* @subpackage Serializer
* @access protected
*/
class JPSpan_SerializedError {

    /**
    * Name of Javascript Error class
    * @var string
    * @access private
    */
    var $name;

    /**
    * Error message
    * @var string
    * @access private
    */
    var $message;

    /**
    * Obey interface
    * @var string
    * @access private
    */
    var $tmpName = '';

    /**
    * @param mixed
    * @return void
    * @access protected
    */
    function setValue($error) {
        $this->code = $error->code;
        $this->name = $error->name;
        $this->message = strip_tags($error->message);
        $this->message = str_replace("'",'',$this->message);
        $this->message = str_replace('"','',$this->message);
    }
    
    /**
    * Conform to interface
    * @return void
    * @access protected
    */
    function setTmpVar() {}
    
    /**
    * Errors do no return - exception thrown
    * @ return string empty
    * @access protected
    */
    function getReturn() {
        return '';
    }
    
    /**
    * @param JPSpan_CodeWriter
    * @return void
    * @access protected
    */
    function generate(&$code) {

        $error = "var e = new Error('{$this->message}');";
        $error .= "e.name = '{$this->name}';";
        $error .= "e.code = '{$this->code}';";
        $error .= "throw e;";
        // Wrap in anon function - violates RootElement
        $code->write('new Function("'.addcslashes($error,"\000\042\047\134").'");');

        // Disable further code writing so only single Error returned
        $code->enabled = FALSE;
    }
}


