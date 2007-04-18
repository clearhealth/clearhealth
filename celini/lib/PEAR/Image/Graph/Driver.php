<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling different output formats
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
 * @version    CVS: $Id: Driver.php,v 1.8 2005/02/21 20:49:47 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 * @since      File available since Release 0.3.0dev2
 */

/**
 * Driver class.
 *
 * Handles different output formats.
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
 * @abstract
 */
class Image_Graph_Driver
{
    
    // TODO Implement image maps

    /**
     * The leftmost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_left = 0;

    /**
     * The topmost pixel of the element on the canvas
     * @var int
     * @access private
     */
    var $_top = 0;

    /**
     * The width of the graph
     * @var int
     * @access private
     */
    var $_width = 0;

    /**
     * The height of the graph
     * @var int
     * @access private
     */
    var $_height = 0;

    /**
     * Polygon vertex placeholder
     * @var array
     * @access private
     */
    var $_polygon = array();

    /**
     * The thickness of the line(s)
     * @var int
     * @access private
     */
    var $_thickness = 1;

    /**
     * The line style
     * @var mixed
     * @access private
     */
    var $_lineStyle = 'transparent';

    /**
     * The fill style
     * @var mixed
     * @access private
     */
    var $_fillStyle = 'transparent';

    /**
     * The font options
     * @var array
     * @access private
     */
    var $_font = array();

    /**
     * The default font
     * @var array
     * @access private
     */
    var $_defaultFont = array('file' => 'Courier', 'color' => 'black', 'size' => 9);

    /**
     * Create the driver.
     *
     * Parameters available:
     *
     * 'width' The width of the graph on the canvas
     *
     * 'height' The height of the graph on the canvas
     *
     * 'left' The left offset of the graph on the canvas
     *
     * 'top' The top offset of the graph on the canvas
     *
     * @param array $param Parameter array
     * @abstract
     */
    function &Image_Graph_Driver($param)
    {
        if (isset($param['left'])) {
            $this->_left = $param['left'];
        }

        if (isset($param['top'])) {
            $this->_top = $param['top'];
        }

        if (isset($param['width'])) {
            $this->_width = $param['width'];
        }

        if (isset($param['height'])) {
            $this->_height = $param['height'];
        }        
    }

    /**
     * Get the x-point from the relative to absolute coordinates
     *
     * @param float $x The relative x-coordinate (in percentage of total width)
     * @return float The x-coordinate as applied to the driver
     * @access private
     */
    function _getX($x)
    {
        return floor($this->_left + $x);
    }

    /**
     * Get the y-point from the relative to absolute coordinates
     *
     * @param float $y The relative y-coordinate (in percentage of total width)
     * @return float The y-coordinate as applied to the driver
     * @access private
     */
    function _getY($y)
    {
        return floor($this->_top + $y);
    }

    /**
     * Get the width of the canvas
     *
     * @return int The width
     */
    function getWidth()
    {
        return $this->_width;
    }

    /**
     * Get the height of the canvas
     *
     * @return int The height
     */
    function getHeight()
    {
        return $this->_height;
    }

    /**
     * Sets the thickness of the line(s) to be drawn
     *
     * @param int $thickness The actual thickness (in pixels)
     */
    function setLineThickness($thickness)
    {
        $this->_thickness = $thickness;
    }

    /**
     * Sets the color of the line(s) to be drawn
     *
     * @param mixed $color The color of the line
     */
    function setLineColor($color)
    {
        $this->_lineStyle = $color;
    }

    /**
     * Sets the style of the filling of drawn objects.
     *
     * This method gives simple access to setFillColor(), setFillImage() and
     * setGradientFill()
     *
     * @param mixed $fill The fill style
     */
    function setFill($fill)
    {
        if (is_array($fill)) {
            $this->setGradientFill($fill);
        } elseif (file_exists($fill)) {
            $this->setFillImage($fill);
        } else {
            $this->setFillColor($fill);
        }
    }

    /**
     * Sets the color of the filling of drawn objects
     *
     * @param mixed $color The fill color
     */
    function setFillColor($color)
    {
        $this->_fillStyle = $color;
    }

    /**
     * Sets an image that should be used for filling
     *
     * @param string $filename The filename of the image to fill with
     */
    function setFillImage($filename)
    {
    }

    /**
     * Sets a gradient fill
     *
     * @param array $gradient Gradient fill options
     */
    function setGradientFill($gradient)
    {
        $this->_fillStyle = $gradient;
    }

    /**
     * Sets the font options.
     *
     * The $font array may have the following entries:
     *
     * 'ttf' = the .ttf file (either the basename, filename or full path)
     * If 'ttf' is specified, then the following can be specified
     *
     * 'size' = size in pixels
     *
     * 'angle' = the angle with which to write the text
     *
     * @param array $fontOptions The font options.
     */
    function setFont($fontOptions)
    {
        $this->_font = $fontOptions;
    }

    /**
     * Sets the default font options.
     *
     * The $font array may have the following entries:
     *
     * 'ttf' = the .ttf file (either the basename, filename or full path)
     * If 'ttf' is specified, then the following can be specified
     *
     * 'size' = size in pixels
     *
     * 'angle' = the angle with which to write the text
     *
     * @param array $fontOptions The font options.
     */
    function setDefaultFont($fontOptions)
    {
        $this->setFont($fontOptions);
        $this->_defaultFont = $this->_font;
    }

    /**
     * Resets the driver.
     *
     * Includes fillstyle, linestyle, thickness and polygon
     *
     * @access private
     */
    function _reset()
    {
        $this->_lineStyle = false;
        $this->_fillStyle = false;
        $this->_thickness = 1;
        $this->_polygon = array();
        $this->_font = $this->_defaultFont;
    }

    /**
     * Draw a line
     *
     * @param int $x0 X start point
     * @param int $y0 X start point
     * @param int $x1 X end point
     * @param int $y1 Y end point
     * @param mixed $color The line color, can be omitted
     */
    function line($x0, $y0, $x1, $y1, $color = false)
    {
        $this->_reset();
    }

    /**
     * Adds vertex to a polygon
     *
     * @param int $x X point
     * @param int $y Y point
     */
    function polygonAdd($x, $y)
    {
        $this->_polygon[] =
            array(
                'X' => $this->_getX($x),
                'Y' => $this->_getY($y)
            );
    }

    /**
     * Adds vertex to a polygon
     *
     * @param int $x X point
     * @param int $y Y point
     */
    function splineAdd($x, $y, $p1x, $p1y, $p2x, $p2y)
    {
        $this->_polygon[] =
            array(
                'X' => $this->_getX($x),
                'Y' => $this->_getY($y),
                'P1X' => $this->_getX($p1x),
                'P1Y' => $this->_getY($p1y),
                'P2X' => $this->_getX($p2x),
                'P2Y' => $this->_getY($p2y)
            );
    }

    /**
     * Draws a polygon
     *
     * @param bool $connectEnds Specifies whether the start point should be
     *   connected to the endpoint (closed polygon) or not (connected line)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function polygonEnd($connectEnds = true, $fillColor = false, $lineColor = false)
    {
        $this->_reset();
    }

    /**
     * Draws a polygon
     *
     * @param bool $connectEnds Specifies whether the start point should be
     *   connected to the endpoint (closed polygon) or not (connected line)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function splineEnd($connectEnds = true, $fillColor = false, $lineColor = false)
    {
        $this->_reset();
    }

    /**
     * Draw a rectangle
     *
     * @param int $x0 X start point
     * @param int $y0 X start point
     * @param int $x1 X end point
     * @param int $y1 Y end point
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function rectangle($x0, $y0, $x1, $y1, $fillColor = false, $lineColor = false)
    {
        $this->_reset();
    }

    /**
     * Draw an ellipse
     *
     * @param int $x Center point x-value
     * @param int $y Center point y-value
     * @param int $rx X-radius of ellipse
     * @param int $ry Y-radius of ellipse
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function ellipse($x, $y, $rx, $ry, $fillColor = false, $lineColor = false)
    {
        $this->_reset();
    }

    /**
     * Draw a pie slice
     *
     * @param int $x Center point x-value
     * @param int $y Center point y-value
     * @param int $rx X-radius of pie slice
     * @param int $ry Y-radius of pie slice
     * @param int $v1 The starting angle
     * @param int $v2 The end angle
     * @param int $srx Starting X-radius of the pie slice  i.e. for a doughnut)
     * @param int $sry Starting Y-radius of the pie slice (i.e. for a doughnut)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function pieSlice($x, $y, $rx, $ry, $v1, $v2, $srx = false, $sry = false, $fillColor = false, $lineColor = false)
    {
        $this->_reset();
    }

    /**
     * Get the width of a text,
     *
     * @param string $text The text to get the width of
     * @return int The width of the text
     */
    function textWidth($text)
    {
    }

    /**
     * Get the height of a text,
     *
     * @param string $text The text to get the height of
     * @return int The height of the text
     */
    function textHeight($text)
    {
    }

    /**
     * Writes text
     *
     * @param int $x X-point of text
     * @param int $y Y-point of text
     * @param string $text The text to write
     * @param int $alignment The alignment of the text
     * @param mixed $color The color of the text
     */
    function write($x, $y, $text, $alignment, $color = false)
    {
        $this->_reset();
    }

    /**
     * Overlay image
     *
     * @param int $x X-point of overlayed image
     * @param int $y Y-point of overlayed image
     * @param string $filename The filename of the image to overlay
     * @param int $width The width of the overlayed image (resizing if possible)
     * @param int $height The height of the overlayed image (resizing if
     *   possible)
     */
    function overlayImage($x, $y, $filename, $width = false, $height = false)
    {
    }

    /**
     * Start a group.
     * 
     * What this does, depends on the driver/format.
     *
     * @param string $name The name of the group
     */
    function startGroup($name = false)
    {
    }

    /**
     * End the "current" group.
     * 
     * What this does, depends on the driver/format.
     */
    function endGroup()
    {
    }

    /**
     * Output the result of the driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        if ($param === false) {
            header('Expires: Tue, 2 Jul 1974 17:41:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
            header('Pragma: no-cache');
        }
    }

    /**
     * Driver factory method.
     *
     * Supported drivers are:
     *
     * 'png': output in PNG format (using GD)
     *
     * 'jpg': output in JPEG format (using GD)
     *
     * 'svg': Scalable Vector Graphics
     *
     * 'xmlsvg': Scalable Vector Graphics, requires PEAR::XML_SVG (not
     * implemented)
     *
     * 'pdf': PDF Output, requires PEAR::File_PDF (not implemented)
     *
     * 'pdflib': PDF Output, requires PDFlib (not implemented)
     *
     * 'swf': SWF Output, requires MING (not implemented)
     *
     * 'libswf': SWF Output, requires libswf (not implemented)
     *
     * @param string $driver The driver
     * @param array $param The parameters for the driver constructor
     * @return Image_Graph_Driver The newly created driver
     */
    function &factory($driver, $param)
    {
        $driver = strtoupper($driver);
        if (($driver == 'PNG') || ($driver == 'GD')) {
            $driver = 'GD_PNG';
        }
        if ($driver == 'GIF') {
            $driver = 'GD_GIF';
        }
        if ($driver == 'WBMP') {
            $driver = 'GD_WBMP';
        }
        if (($driver == 'JPG') || ($driver == 'JPEG')) {
            $driver = 'GD_JPG';
        }
        if ($driver == 'PDFLIB') {
            $driver = 'PDFlib';
        }
        if ($driver == 'LIBSWF') {
            $driver = 'LibSWF';
        }
        
        $class = 'Image_Graph_Driver_'. $driver;
      	include_once 'Image/Graph/Driver/'. str_replace('_', '/', $driver) . '.php';
        return new $class($param);
    }

}

?>