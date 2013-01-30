<?php
/*****************************************************************************
*       ScriptTag.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

require_once 'Zend/Form/Decorator/HtmlTag.php';

/**
 * Zend_Form_Decorator_Element_HtmlTag
 *
 * Wraps content in an HTML block tag.
 *
 * Options accepted are:
 * - tag: tag to use in decorator
 * - noAttribs: do not render attributes in the opening tag
 * - placement: 'append' or 'prepend'. If 'append', renders opening and 
 *   closing tag after content; if prepend, renders opening and closing tag
 *   before content.
 * - openOnly: render opening tag only
 * - closeOnly: render closing tag only
 *
 * Any other options passed are processed as HTML attributes of the tag.
 * 
 */
class WebVista_Form_Decorator_ScriptTag extends Zend_Form_Decorator_HtmlTag {
    protected $_content;

    public function render($content)    {
        $tag       = $this->getTag();
        $innerHTML = $this->getOption('innerHTML');
        $placement = $this->getPlacement();
        $noAttribs = $this->getOption('noAttribs');
        $openOnly  = $this->getOption('openOnly');
        $closeOnly = $this->getOption('closeOnly');
        $this->removeOption('noAttribs');
        $this->removeOption('openOnly');
        $this->removeOption('closeOnly');

        $attribs = null;
        if (!$noAttribs) {
            $attribs = $this->getOptions();
        }

        switch ($placement) {
            case self::APPEND:
                if ($closeOnly) {
                    return $content . $this->_getCloseTag($tag);
                }
                if ($openOnly) {
                    return $content . $this->_getOpenTag($tag, $attribs);
                }
                return $content 
                     . $this->_getOpenTag($tag, $attribs) 
                     . $innerHTML 
                     . $this->_getCloseTag($tag);
            case self::PREPEND:
                if ($closeOnly) {
                    return $this->_getCloseTag($tag) . $content;
                }
                if ($openOnly) {
                    return $this->_getOpenTag($tag, $attribs) . $content;
                }
                return $this->_getOpenTag($tag, $attribs)
                     . $this->_getCloseTag($tag)
                     . $content;
            default:
                return (($openOnly || !$closeOnly) ? $this->_getOpenTag($tag, $attribs) : '')
                     . $content
                     . (($closeOnly || !$openOnly) ? $this->_getCloseTag($tag) : '');
        }
    }
}
