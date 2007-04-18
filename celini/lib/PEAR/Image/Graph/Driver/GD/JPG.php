<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in JPEG format.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Driver
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: JPG.php,v 1.5 2005/02/21 20:49:59 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 * @since      File available since Release 0.3.0dev2
 */

/**
 * Include file Image/Graph/Driver/GD.php
 */
require_once 'Image/Graph/Driver/GD.php';

/**
 * GD JPEG Driver class.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Driver
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.3.0
 * @link       http://pear.php.net/package/Image_Graph
 * @since      Class available since Release 0.3.0dev2
 */
class Image_Graph_Driver_GD_JPG extends Image_Graph_Driver_GD
{
    
    /**
     * The JPEG quality
     * @var int
     * @access private
     */
    var $_quality = 75;
    
    /**
     * Create the JPEG driver
     * 
     * Additional parameters other than those available for common {@link
     * Image_Graph_Driver_GD} class are:
     * 
     * 'quality' The JPEG quality in as a percentage value from 0 (lowest
     * quality, smallest file) to 100 (highest quality, biggest file)
     *
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_GD_JPG($param)
    {
        parent::Image_Graph_Driver_GD($param);
        
        if (isset($param['quality'])) {
            $this->_quality = max(0, min(100, $param['quality']));
        } 

        $this->rectangle(
            $this->_left,
            $this->_top,
            $this->_left + $this->_width - 1,
            $this->_top + $this->_height - 1,
            'white',
            'transparent'
        );
    }

    /**
     * Output the result of the driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        parent::done($param);
        if (($param === false) || (!isset($param['filename']))) {
            header('Content-type: image/jpg');
            header('Content-Disposition: inline; filename = \"'. basename($_SERVER['PHP_SELF'], '.php') . '.jpg\"');
            ImageJPEG($this->_canvas, '', $this->_quality);
        } elseif (isset($param['filename'])) {
            ImageJPEG($this->_canvas, $param['filename'], $this->_quality);
        }
    }

}

?>