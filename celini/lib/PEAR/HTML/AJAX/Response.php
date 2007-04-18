<?php
/**
 * OO AJAX Implementation for PHP
 *
 * @category   HTML
 * @package    AJAX
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005 Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.4.9.2
 */

/**
 * Require the main AJAX library
 */
require_once 'HTML/AJAX.php';

/**
 * Josh says we need this, I don't know why yet
 */
class HTML_AJAX_Response
{

	/**
	 * about
	 * @var $contentType type
	 */
	var $contentType = 'text/plain';

	/**
	 * about
	 * @var $payload type
	 */
	var $payload = '';

	/**
	 * public function getContentType()
	 *
	 * gets the right content type?
	 */
	function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * public function getPayload()
	 *
	 * something
	 */
	function getPayload()
	{
		return $this->payload;
	}
}
?>