<?php
define ("NEWLINE", "\n");
// {{{ class HTML_AJAX_Debug
/**
 * AJAX Debugging implementation
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    AJAX
 * @author     David Coallier <davidc@php.net>
 * @copyright  2005 David Coallier 
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.4.9.2
 */
class HTML_AJAX_Debug {
    // {{{ properties
    /**
     * This is the error message.
     *
     * @access private
     */
    var $errorMsg;

    /**
     * The line where the error occured.
     *
     * @access private
     */
    var $errorLine;

    /**
     * The error code.
     *
     * @access private
     */
    var $errorCode;
    
    /**
     * The file where the error occured.
     *
     * @access private
     */
    var $errorFile;

    /**
     * Time the error occured
     *
     * @access private
     */
    var $_timeOccured;

    /**
     * The whole error itself
     *
     * @access private
     * @see errorMsg
     * @see errorLine
     * @see errorFile
     * @see errorCode
     */
    var $error;

    /**
     * The file to save the error to.
     *
     * @access private
     * @default ajaxErrLog.xml
     */
    var $file = 'ajaxErrLog.xml';
    // }}}
    // {{{ constructor
    /**
     * The constructor.
     *
     * @param string $errorMsg   The error message.
     * @param string $errLine    The line where error occured.
     * @param string $errCode    The error Code.
     * @param string $errFile    The file where error occured.
     */
    function HTML_AJAX_Debug($errMsg, $errLine, $errCode, $errFile)
    {
        $this->errorMsg    = $errMsg;
        $this->errorLine   = $errLine;
        $this->errorCode   = $errCode;
        $this->errorFile   = $errFile;
        $this->_timeOccured = date("Y-m-d H:i:s", time());
        $this->xmlError();
    }
    // }}}
    // {{{ xmlError
    /**
     * This functions formats the error to xml format then we can save it.
     *
     * @access protected
     * @return $this->error   the main error.
     */
    function xmlError()
    {
        $error  = " <when>{$this->_timeOccured}</when>" . NEWLINE;
        $error .= " <msg>{$this->errorMsg}</msg>"       . NEWLINE;
        $error .= " <code>{$this->errorCode}</code>"    . NEWLINE;
        $error .= " <line>{$this->errorLine}</line>"    . NEWLINE;
        $error .= " <file>{$this->errorFile}</file>"    . NEWLINE . NEWLINE;
        return $this->error = $error; 
    }
    // }}}
    // {{{ sessionError
    /**
     * This function pushes the array $_SESSION['html_ajax_debug']['time'][]
     * with the values inside of $this->error
     *
     * @access public
     */
    function sessionError() 
    {
        $_SESSION['html_ajax_debug']['time'][] = $this->error;
    }
    // }}}
    // {{{ _saveError
    /**
     * This function saves the error to a file
     * appending to this file.
     *
     * @access private.
     */
    function _saveError()
    {
        if ($handle = fopen($this->file, 'a')) {
            fwrite($handle, $this->error);
        }
    }
    // }}}
}
// }}}
?>
