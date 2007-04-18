<?php
/**
* @package JPSpan
* @subpackage Generator
* @version $Id: Generator.php,v 1.4 2005/06/02 23:47:15 harryf Exp $
*/
//--------------------------------------------------------------------------------
/**
* Define
*/
if ( !defined('JPSPAN') ) {
    define ('JPSPAN',dirname(__FILE__).'/');
}

/**
* Generaters client-side Javascript primed to access a server
* Works with JPSpan_HandleDescription to generate
* client primed for a server
* @todo Review this - may be worth eliminating - not serving much useful purpose
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
    * @todo This method needs to die - just setup the ClientGenerator object
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

//--------------------------------------------------------------------------------
/**
* @package JPSpan
* @subpackage Generator
* @access public
*/
class JPSpan_Generator_AdHoc {

    var $descriptions = array();
    
    var $RequestEncoding = 'xml';
    
    var $RequestMethod = 'rawpost';
    
    var $jsRequestClass = 'JPSpan_Request_RawPost';
    
    var $jsEncodingClass = 'JPSpan_Encode_Xml';
    
    function addDescription($description) {
        $this->descriptions[$description->jsClass] = $description;
    }

    /**
    * Invokes code generator
    * @param JPSpan_CodeWriter
    * @return void
    * @access public
    */
    function generate(& $Code) {

        switch ( $this->RequestMethod ) {
            case 'rawpost':
                $this->jsRequestClass = 'JPSpan_Request_RawPost';
            break;
            case 'post':
                $this->jsRequestClass = 'JPSpan_Request_Post';
            break;
            case 'get':
                // The JPSpan JS GetRequest object has bugs plus
                // changing state via GET is bad idea
                // http://www.intertwingly.net/blog/2005/03/16/AJAX-Considered-Harmful
                trigger_error('Sending data via GET vars not supported',E_USER_ERROR);
            break;
            default:
                trigger_error('Request method unknown: '.$this->RequestMethod,E_USER_ERROR);
            break;
        }
        
        if ( $this->RequestEncoding == 'xml' ) {
            $this->jsEncodingClass = 'JPSpan_Encode_Xml';
        } else {
            $this->jsEncodingClass = 'JPSpan_Encode_PHP';
        }
        
        $this->generateScriptHeader($Code);
        
        foreach ( array_keys($this->descriptions) as $key ) {
            $this->generateJsClass($Code, $this->descriptions[$key]);
        }
    }
    
    /**
    * Generate the starting includes section of the script
    * @param JPSpan_CodeWriter
    * @return void
    * @access private
    */
    function generateScriptHeader(& $Code) {
        ob_start();
?>
/**@
* include 'remoteobject.js';
<?php
switch ( $this->RequestMethod ) {
    case 'rawpost':
?>
* include 'request/rawpost.js';
<?php
    break;
    case 'post':
?>
* include 'request/rawpost.js';
<?php
    break;
}

if ( $this->RequestEncoding == 'xml' ) {
?>

* include 'encode/xml.js';
<?php
} else {
?>
* include 'encode/php.js';
<?php
}
?>
*/
<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }
    
    /**
    * Generate code for a single description (a single PHP class)
    * @param JPSpan_CodeWriter
    * @param JPSpan_HandleDescription
    * @return void
    * @access private
    */
    function generateJsClass(& $Code, & $Description) {
        ob_start();
?>

function <?php echo $Description->Class; ?>() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__remoteClass = '<?php echo $Description->Class; ?>';
    
    oParent.__request = new <?php echo $this->jsRequestClass;
        ?>(new <?php echo $this->jsEncodingClass; ?>());
<?php
foreach ( $Description->methods as $method => $url ) {
?>
    
    // @access public
    oParent.<?php echo $method; ?> = function() {
        return this.__call('<?php echo $url; ?>',arguments,'<?php echo $method; ?>');
    };
<?php
}
?>
    
    return oParent;
}

<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }

    function getClient() {
        require_once JPSPAN . 'CodeWriter.php';
        $Code = & new JPSpan_CodeWriter();
        $this->generate($Code);
        $client = $Code->toString();
        
        require_once JPSPAN . 'Include.php';
        $I = & JPSpan_Include::instance();
        
        // HACK - this needs to change
        $I->loadString(__FILE__,$client);
        return $I->getCode();
    }
}

//--------------------------------------------------------------------------------
/**
* @package JPSpan
* @subpackage Generator
* @access public
*/
class JPSpan_Generator_AdHoc_Description {
    
    var $Class;
    
    /**
    * Map of method name to URL endpoint for method
    */
    var $methods = array();
    
}
