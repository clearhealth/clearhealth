<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in GD compatible format.
 * 
 * Supported formats are PNG, JPEG, GIF and WBMP. 
 * 
 * Requires PHP extension GD (version 1 or 2 - 2 preferred for optimal
 * performance)
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
 * @version    CVS: $Id: GD.php,v 1.11 2005/02/24 19:05:43 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 * @since      File available since Release 0.3.0dev2
 */

/**
 * Include file Image/Graph/Config.php
 */
require_once 'Image/Graph/Config.php';

/**
 * Include file Image/Graph/Driver.php
 */
require_once 'Image/Graph/Driver.php';

/**
 * Include file Image/Graph/Constants.php
 */
require_once 'Image/Graph/Constants.php';

/**
 * Include file Image/Graph/Color.php
 */
require_once 'Image/Graph/Color.php';

/**
 * GD Driver class.
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
class Image_Graph_Driver_GD extends Image_Graph_Driver
{

    /**
     * The canvas of the graph
     * @var resource
     * @access private
     */
    var $_canvas;

    /**
     * The canvas to use for tiled filling
     * @var resource
     * @access private
     */
    var $_tileImage = null;

    /**
     * Is version GD2 installed?
     * @var bool
     * @access private
     */
    var $_gd2 = true;

    /**
     * Font map
     * @var array
     * @access private
     */
    var $_fontMap = array(
    );
    
    /**
     * Create the GD driver.
     *
     * Parameters available:
     * 
     * 'antialias' Set to true if line antialiasing should be enabled, this is
     * the built in GD antialiasing, which causes lines with a linestyle to
     * disappear and lines with a thickness > 1 to display as = 1. This does
     * also not look good when displaying "short" lines as fx. done with a
     * smooth line/area chart. Use it for best results with a line chart having
     * few datapoints.
     *
     * 'width' The width of the graph on the canvas
     *
     * 'height' The height of the graph on the canvas
     *
     * 'left' The left offset of the graph on the canvas
     *
     * 'top' The top offset of the graph on the canvas
     *
     * 'filename' An image to open, on which the graph is created on
     *
     * 'gd' A GD resource to add the image to, use this option to continue
     * working on an already existing GD resource. Make sure this is passed 'by-
     * reference' (using &amp;)
     *
     * 'gd' and 'filename' are mutually exclusive with 'gd' as preference
     *
     * 'width' and 'height' are required unless 'filename' or 'gd' are
     * specified, in which case the width and height are taken as the actual
     * image width/height. If the latter is the case and 'left' and/or 'top' was
     * also specified, the actual 'width'/'height' are altered so that the graph
     * fits inside the canvas (i.e 'height' = actual height - top, etc.)
     *
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_GD($param)
    {
        include_once 'Image/Graph/Color.php';

        parent::Image_Graph_Driver($param);
        $this->_gd2 = ($this->_version() == 2);
        $this->_font = array('font' => 1, 'color' => 'black');

        if ((isset($param['gd'])) && (is_resource($param['gd']))) {
            $this->_canvas =& $param['gd'];
        } elseif (isset($param['filename'])) {
            $this->_canvas =& $this->_getGD($param['filename']);
        } else {
            if ($this->_gd2) {
                $this->_canvas = ImageCreateTrueColor(
                    $this->_width,
                    $this->_height
                );
                ImageAlphaBlending($this->_canvas, true);               
            } else {
                $this->_canvas = ImageCreate($this->_width, $this->_height);
            }            
        }
        
        if (($this->_gd2) && (isset($param['antialias'])) && ($param['antialias'])) {
            ImageAntialias($this->_canvas, true);
        }

        if (file_exists($fontmap = (dirname(__FILE__) . '/../Fonts/fontmap.txt'))) {
            $file = file($fontmap);
            foreach($file as $fontmapping) {
                list($filename, $fontname) = explode("\t", $fontmapping);
                $filename = trim($filename);
                $fontname = trim($fontname);
                $this->_fontMap[$fontname] = $filename;
            }
        }

        if (!$this->_width) {
            $this->_width = ImageSX($this->_canvas) - $this->_left;
        }
        if (!$this->_height) {
            $this->_height = ImageSY($this->_canvas) - $this->_top;
        }
    }

    /**
     * Maps a font name to an actual font file (i.e. a .ttf file)
     *
     * Used to translate names (i.e. 'Courier New' to 'cour.ttf' or
     * '/Windows/Fonts/Cour.ttf')
     *
     * Font names are translated using the tab-separated file
     * Image/Graph/Fonts/fontmap.txt.
     *
     * The translated font-name (or the original if no translation) exists is
     * then returned if it is an existing file, otherwise the file is searched
     * first in the path specified by IMAGE_GRAPH_SYSTEM_FONT_PATH defined in
     * Image/Graph/Config.php, then in the Image/Graph/Fonts folder. If a font
     * is still not found and the name is not beginning with a '/' the search is
     * left to the library, otherwise the font is deemed non-existing.
     *
     * @param string $name The name of the font
     * @return string The filename of the font
     * @access private
	 * @since 0.3.0dev2
     */
    function _mapFont($name)
    {
        if (isset($this->_fontMap[$name])) {
            $filename = $this->_fontMap[$name];
        } else {
            $filename = $name;
        }

        if (strtolower(substr($filename, -4)) !== '.ttf') {
            $filename .= '.ttf';
        }

        if (file_exists($filename)) {
            return $filename;
        } elseif (file_exists($file = (IMAGE_GRAPH_SYSTEM_FONT_PATH . $filename))) {
            return $file;
        } elseif (file_exists($file = (dirname(__FILE__) . '/../Fonts/' . $filename))) {
            return $file;
        } elseif (substr($name, 0, 1) !== '/') {
            // leave it to the library to find the font
            return $name;
        } else {
            return false;
        }
    }

    /**
     * Get an GD image resource from a file
     *
     * @param string $filename
     * @return mixed The GD image resource
     * @access private
     */
    function &_getGD($filename)
    {
        if (strtolower(substr($filename, -4)) == '.png') {
            return ImageCreateFromPNG($filename);
        } else {
            return ImageCreateFromJPEG($filename);
        }
    }

    /**
     * Get the color index for the RGB color
     *
     * @param int $color The color
     * @return int The GD image index of the color
     * @access private
     */
    function _color($color = false)
    {
        if (($color === false) || ($color === 'opague')) {
            return ImageColorTransparent($this->_canvas);
        } else {
            return Image_Graph_Color::allocateColor($this->_canvas, $color);
        }
    }

    /**
     * Get the GD applicable linestyle
     *
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     * @return mixed A GD compatible linestyle
     * @access private
     */
    function _getLineStyle($lineStyle = false)
    {
        if ($this->_gd2) {
            ImageSetThickness($this->_canvas, $this->_thickness);
        }

        if ($lineStyle == 'transparent') {
            return false;
        } elseif ($lineStyle === false) {
            if (is_array($this->_lineStyle)) {
                $colors = array();
                foreach ($this->_lineStyle as $color) {
                    if ($color === 'transparent') {
                        $color = false;
                    }
                    $colors[] = $this->_color($color);
                }
                ImageSetStyle($this->_canvas, $colors);
                return IMG_COLOR_STYLED;
            } else {
                return $this->_color($this->_lineStyle);
            }
        } else {
            return $this->_color($lineStyle);
        }
    }

    /**
     * Get the GD applicable fillstyle
     *
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     * @return mixed A GD compatible fillstyle
     * @access private
     */
    function _getFillStyle($fillStyle = false, $x0 = 0, $y0 = 0, $x1 = 0, $y1 = 0)
    {
        if ($this->_tileImage != null) {
            ImageDestroy($this->_tileImage);
            $this->_tileImage = null;
        }
        if ($fillStyle == 'transparent') {
            return false;
        } elseif ($fillStyle === false) {
            if (is_resource($this->_fillStyle)) {
                $x = min($x0, $x1);
                $y = min($y0, $y1);
                $w = abs($x1 - $x0) + 1;
                $h = abs($y1 - $y0) + 1;
                if ($this->_gd2) {
                    $this->_tileImage = ImageCreateTrueColor(
                        $this->getWidth(),
                        $this->getHeight()
                    );

                    ImageCopyResampled(
                        $this->_tileImage,
                        $this->_fillStyle,
                        $x,
                        $y,
                        0,
                        0,
                        $w,
                        $h,
                        ImageSX($this->_fillStyle),
                        ImageSY($this->_fillStyle)
                    );
                } else {
                    $this->_tileImage = ImageCreate(
                        $this->getWidth(),
                        $this->getHeight()
                    );

                    ImageCopyResized(
                        $this->_tileImage,
                        $this->_fillStyle,
                        $x,
                        $y,
                        0,
                        0,
                        $w,
                        $h,
                        ImageSX($this->_fillStyle),
                        ImageSY($this->_fillStyle)
                    );
                }
                ImageSetTile($this->_canvas, $this->_tileImage);
                return IMG_COLOR_TILED;
            } elseif (is_array($this->_fillStyle)) {
                $width = abs($x1 - $x0) + 1;
                $height = abs($y1 - $y0) + 1;

                switch ($this->_fillStyle['direction']) {
                case IMAGE_GRAPH_GRAD_HORIZONTAL:
                    $count = $width;
                    break;

                case IMAGE_GRAPH_GRAD_VERTICAL:
                    $count = $height;
                    break;

                case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED:
                    $count = $width / 2;
                    break;

                case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED:
                    $count = $height / 2;
                    break;

                case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR:
                case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR:
                    $count = sqrt($width * $width + $height * $height);
                    break;

                case IMAGE_GRAPH_GRAD_RADIAL:
                    $count = max($width, $height, sqrt($width * $width + $height * $height)) + 1;
                    break;

                }

                $count = round($count);

                if ($this->_gd2) {
                    $this->_tileImage = ImageCreateTrueColor(
                        $this->getWidth(),
                        $this->getHeight()
                    );
                } else {
                    $this->_tileImage = ImageCreate(
                        $this->getWidth(),
                        $this->getHeight()
                    );
                }


                $startColor = Image_Graph_Color::color2RGB(
                    ($this->_fillStyle['direction'] == IMAGE_GRAPH_GRAD_RADIAL ?
                        $this->_fillStyle['end'] :
                        $this->_fillStyle['start']
                    )
                );
                $endColor = Image_Graph_Color::color2RGB(
                    ($this->_fillStyle['direction'] == IMAGE_GRAPH_GRAD_RADIAL ?
                        $this->_fillStyle['start'] :
                        $this->_fillStyle['end']
                    )
                );

                $redIncrement = ($endColor[0] - $startColor[0]) / $count;
                $greenIncrement = ($endColor[1] - $startColor[1]) / $count;
                $blueIncrement = ($endColor[2] - $startColor[2]) / $count;

                for ($i = 0; $i < $count; $i ++) {
                    unset($color);
                    if ($i == 0) {
                        $color = $startColor;
                        unset($color[3]);
                    } else {
                        $color[0] = round(($redIncrement * $i) +
                            $redIncrement + $startColor[0]);
                        $color[1] = round(($greenIncrement * $i) +
                            $greenIncrement + $startColor[1]);
                        $color[2] = round(($blueIncrement * $i) +
                            $blueIncrement + $startColor[2]);
                    }
                    $color = Image_Graph_Color::allocateColor(
                        $this->_tileImage,
                        $color
                    );

                    switch ($this->_fillStyle['direction']) {
                    case IMAGE_GRAPH_GRAD_HORIZONTAL:
                        ImageLine($this->_tileImage,
                            $x0 + $i,
                            $y0,
                            $x0 + $i,
                            $y1, $color);
                        break;

                    case IMAGE_GRAPH_GRAD_VERTICAL:
                        ImageLine($this->_tileImage,
                            $x0,
                            $y1 - $i,
                            $x1,
                            $y1 - $i, $color);
                        break;

                    case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED:
                        if (($x0 + $i) <= ($x1 - $i)) {
                            ImageLine($this->_tileImage,
                                $x0 + $i,
                                $y0,
                                $x0 + $i,
                                $y1, $color);

                            ImageLine($this->_tileImage,
                                $x1 - $i,
                                $y0,
                                $x1 - $i,
                                $y1, $color);
                        }
                        break;

                    case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED:
                        if (($y0 + $i) <= ($y1 - $i)) {
                            ImageLine($this->_tileImage,
                                $x0,
                                $y0 + $i,
                                $x1,
                                $y0 + $i, $color);
                            ImageLine($this->_tileImage,
                                $x0,
                                $y1 - $i,
                                $x1,
                                $y1 - $i, $color);
                        }
                        break;

                    case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR:
                        if (($i > $width) && ($i > $height)) {
                            $polygon = array (
                                $x1, $y0 + $i - $width - 1,
                                $x1, $y1,
                                $x0 + $i - $height - 1, $y1);
                        } elseif ($i > $width) {
                            $polygon = array (
                                $x0, $y0 + $i,
                                $x0, $y1,
                                $x1, $y1,
                                $x1, $y0 + $i - $width - 1);
                        } elseif ($i > $height) {
                            $polygon = array (
                                $x0 + $i - $height - 1, $y1,
                                $x1, $y1,
                                $x1, $y0,
                                $x0 + $i, $y0);
                        } else {
                            $polygon = array (
                                $x0, $y0 + $i,
                                $x0, $y1,
                                $x1, $y1,
                                $x1, $y0,
                                $x0 + $i, $y0);
                        }
                        ImageFilledPolygon(
                            $this->_tileImage,
                            $polygon,
                            count($polygon) / 2,
                            $color
                        );
                        break;

                    case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR:
                        if (($i > $width) && ($i > $height)) {
                            $polygon = array (
                                $x1, $y1 - $i + $width - 1,
                                $x1, $y0,
                                $x0 + $i - $height - 1, $y0);
                        } elseif ($i > $width) {
                            $polygon = array (
                                $x0, $y1 - $i,
                                $x0, $y0,
                                $x1, $y0,
                                $x1, $y1 - $i + $width - 1);
                        } elseif ($i > $height) {
                            $polygon = array (
                                $x0 + $i - $height - 1, $y0,
                                $x1, $y0,
                                $x1, $y1,
                                $x0 + $i, $y1);
                        } else {
                            $polygon = array (
                                $x0, $y1 - $i,
                                $x0, $y0,
                                $x1, $y0,
                                $x1, $y1,
                                $x0 + $i, $y1);
                        }
                        ImageFilledPolygon(
                            $this->_tileImage,
                            $polygon,
                            count($polygon) / 2,
                            $color
                        );
                        break;

                    case IMAGE_GRAPH_GRAD_RADIAL:
                        if (($this->_gd2) && ($i < $count)) {
                            ImageFilledEllipse(
                                $this->_tileImage,
                                $x0 + $width / 2,
                                $y0 + $height / 2,
                                $count - $i,
                                $count - $i,
                                $color
                            );
                        }
                        break;
                    }
                }
                ImageSetTile($this->_canvas, $this->_tileImage);
                return IMG_COLOR_TILED;
            } else {
                return $this->_color($this->_fillStyle);
            }
        } else {
            return $this->_color($fillStyle);
        }
    }

    /**
     * Sets an image that should be used for filling
     *
     * @param string $filename The filename of the image to fill with
     */
    function setFillImage($filename)
    {
        $this->_fillStyle =& $this->_getGD($filename);
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
     * @param array $font The font options.
     */
    function setFont($fontOptions)
    {
        parent::setFont($fontOptions);

        if (isset($this->_font['ttf'])) {
            $this->_font['ttf_file'] = str_replace('\\', '/', $this->_mapFont($this->_font['ttf']));
        } elseif (!isset($this->_font['font'])) {
            $this->_font['font'] = 1;
        }

        if (!isset($this->_font['color'])) {
            $this->_font['color'] = 'black';
        }

        if ((isset($this->_font['angle'])) && ($this->_font['angle'] === false)) {
            $this->_font['angle'] = 0;
        }
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
        $x0 = $this->_getX($x0);
        $y0 = $this->_getY($y0);
        $x1 = $this->_getX($x1);
        $y1 = $this->_getY($y1);
        if (($line = $this->_getLineStyle($color)) !== false) {
            ImageLine($this->_canvas, $x0, $y0, $x1, $y1, $line);
        }
        parent::line($x0, $y0, $x1, $y1, $color);
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
        if ($connectEnds) {
            reset($this->_polygon);
            foreach ($this->_polygon as $point) {
                $polygon[] = $point['X'];
                $polygon[] = $point['Y'];
                if (!isset($low['X'])) {
                    $low['X'] = $point['X'];
                } else {
                    $low['X'] = min($point['X'], $low['X']);
                }
                if (!isset($high['X'])) {
                    $high['X'] = $point['X'];
                } else {
                    $high['X'] = max($point['X'], $high['X']);
                }
                if (!isset($low['Y'])) {
                    $low['Y'] = $point['Y'];
                } else {
                    $low['Y'] = min($point['Y'], $low['Y']);
                }
                if (!isset($high['Y'])) {
                    $high['Y'] = $point['Y'];
                } else {
                    $high['Y'] = max($point['Y'], $high['Y']);
                }
            }
            if ((isset($polygon)) && (is_array($polygon))) {
                if (($fill = $this->_getFillStyle($fillColor, $low['X'], $low['Y'], $high['X'], $high['Y'])) !== false) {
                    ImageFilledPolygon($this->_canvas, $polygon, count($this->_polygon), $fill);
                }
                if (($line = $this->_getLineStyle($lineColor)) !== false) {
                    ImagePolygon($this->_canvas, $polygon, count($this->_polygon), $line);
                }
            }
        } else {
            $prev_point = false;
            if (($line = $this->_getLineStyle($lineColor)) !== false) {
                foreach ($this->_polygon as $point) {
                    if ($prev_point) {
                        ImageLine(
                            $this->_canvas,
                            $prev_point['X'],
                            $prev_point['Y'],
                            $point['X'],
                            $point['Y'],
                            $line
                        );
                    }
                    $prev_point = $point;
                }
            }
        }
        $this->_polygon = array();
        parent::polygonEnd($connectEnds, $fillColor, $lineColor);
    }

    /**
     * Ends a spline
     *
     * @param bool $connectEnds Specifies whether the start point should be
     *   connected to the endpoint (closed polygon) or not (connected line)
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function splineEnd($connectEnds = true, $fillColor = false, $lineColor = false)
    {
        
        include_once 'Image/Graph/Tool.php';
        
        if (!$connectEnds) {
            $fillColor = 'transparent';
        }
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);

        $lastPoint = false;
        foreach ($this->_polygon as $point) {
            if (($lastPoint) && (isset($lastPoint['P1X'])) &&
                (isset($lastPoint['P1Y'])) && (isset($lastPoint['P2X'])) &&
                (isset($lastPoint['P2Y'])))
            {
                $dx = abs($point['X'] - $lastPoint['X']);
                $dy = abs($point['Y'] - $lastPoint['Y']);
                $d = sqrt($dx * $dx + $dy * $dy);
                if ($d > 0) {
                    $interval = 1 / $d;
                    for ($t = 0; $t <= 1; $t = $t + $interval) {
                        $x = Image_Graph_Tool::bezier(
                            $t,
                            $lastPoint['X'],
                            $lastPoint['P1X'],
                            $lastPoint['P2X'],
                            $point['X']
                        );
    
                        $y = Image_Graph_Tool::bezier(
                            $t,
                            $lastPoint['Y'],
                            $lastPoint['P1Y'],
                            $lastPoint['P2Y'],
                            $point['Y']
                        );
    
                        if (!isset($low['X'])) {
                            $low['X'] = $x;
                        } else {
                            $low['X'] = min($x, $low['X']);
                        }
                        if (!isset($high['X'])) {
                            $high['X'] = $x;
                        } else {
                            $high['X'] = max($x, $high['X']);
                        }
                        if (!isset($low['Y'])) {
                            $low['Y'] = $y;
                        } else {
                            $low['Y'] = min($y, $low['Y']);
                        }
                        if (!isset($high['Y'])) {
                            $high['Y'] = $y;
                        } else {
                            $high['Y'] = max($y, $high['Y']);
                        }
                        $polygon[] = $x;
                        $polygon[] = $y;
                    }
                    if (($t - $interval) < 1) {
                        $x = Image_Graph_Tool::bezier(
                            1,
                            $lastPoint['X'],
                            $lastPoint['P1X'],
                            $lastPoint['P2X'],
                            $point['X']
                        );
    
                        $y = Image_Graph_Tool::bezier(
                            1,
                            $lastPoint['Y'],
                            $lastPoint['P1Y'],
                            $lastPoint['P2Y'],
                            $point['Y']
                        );
    
                        $polygon[] = $x;
                        $polygon[] = $y;
                    }
                }
            } else {
                if (!isset($low['X'])) {
                    $low['X'] = $point['X'];
                } else {
                    $low['X'] = min($point['X'], $low['X']);
                }
                if (!isset($high['X'])) {
                    $high['X'] = $point['X'];
                } else {
                    $high['X'] = max($point['X'], $high['X']);
                }
                if (!isset($low['Y'])) {
                    $low['Y'] = $point['Y'];
                } else {
                    $low['Y'] = min($point['Y'], $low['Y']);
                }
                if (!isset($high['Y'])) {
                    $high['Y'] = $point['Y'];
                } else {
                    $high['Y'] = max($point['Y'], $high['Y']);
                }

                $polygon[] = $point['X'];
                $polygon[] = $point['Y'];
            }
            $lastPoint = $point;
        }

        if ((isset($polygon)) && (is_array($polygon))) {
            if ($connectEnds) {
                if (($fill = $this->_getFillStyle($fillColor, $low['X'], $low['Y'], $high['X'], $high['Y'])) !== false) {
                    ImageFilledPolygon($this->_canvas, $polygon, count($polygon)/2, $fill);
                }
                if (($line = $this->_getLineStyle($lineColor)) !== false) {
                    ImagePolygon($this->_canvas, $polygon, count($polygon)/2, $line);
                }
            } else {
                $prev_point = false;
                if (($line = $this->_getLineStyle($lineColor)) !== false) {
                    reset($polygon);
                    while (list(, $x) = each($polygon)) {
                        list(, $y) = each($polygon);
                        if ($prev_point) {
                            ImageLine(
                                $this->_canvas,
                                $prev_point['X'],
                                $prev_point['Y'],
                                $x,
                                $y,
                                $line
                            );
                        }
                        $prev_point = array('X' => $x, 'Y' => $y);;
                    }
                }
            }
        }

        parent::splineEnd($connectEnds);
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
        $x0 = $this->_getX($x0);
        $y0 = $this->_getY($y0);
        $x1 = $this->_getX($x1);
        $y1 = $this->_getY($y1);
        if (($fill = $this->_getFillStyle($fillColor, $x0, $y0, $x1, $y1)) !== false) {
            ImageFilledRectangle($this->_canvas, $x0, $y0, $x1, $y1, $fill);
        }

        if (($line = $this->_getLineStyle($lineColor)) !== false) {
            ImageRectangle($this->_canvas, $x0, $y0, $x1, $y1, $line);
        }

        parent::rectangle($x0, $y0, $x1, $y1, $fillColor, $lineColor);
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
        $x = $this->_getX($x);
        $y = $this->_getY($y);
        $rx = $this->_getX($rx);
        $ry = $this->_getY($ry);

        if (($fill = $this->_getFillStyle($fillColor, $x - $rx, $y - $ry, $x + $rx, $y + $ry)) !== false) {
            ImageFilledEllipse($this->_canvas, $x, $y, $rx * 2, $ry * 2, $fill);
        }

        if (($line = $this->_getLineStyle($lineColor)) !== false) {
            ImageEllipse($this->_canvas, $x, $y, $rx * 2, $ry * 2, $line);
        }
        parent::ellipse($x, $y, $rx, $ry, $fillColor, $lineColor);
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
        $dA = 0.1;

        if (($srx !== false) && ($sry !== false)) {
            $angle = max($v1, $v2);
            while ($angle >= min($v1, $v2)) {
                $polygon[] = ($x + $srx * cos(deg2rad($angle % 360)));
                $polygon[] = ($y + $sry * sin(deg2rad($angle % 360)));
                $angle -= $dA;
            }
            if (($angle + $dA) > min($v1, $v2)) {
                $polygon[] = ($x + $srx * cos(deg2rad(min($v1, $v2) % 360)));
                $polygon[] = ($y + $sry * sin(deg2rad(min($v1, $v2) % 360)));
            }
        } else {
            $polygon[] = $x;
            $polygon[] = $y;
        }

        $angle = min($v1, $v2);
        while ($angle <= max($v1, $v2)) {
            $polygon[] = ($x + $rx * cos(deg2rad($angle % 360)));
            $polygon[] = ($y + $ry * sin(deg2rad($angle % 360)));
            $angle += $dA;
        }

        if (($angle - $dA) < max($v1, $v2)) {
            $polygon[] = ($x + $rx * cos(deg2rad(max($v1, $v2) % 360)));
            $polygon[] = ($y + $ry * sin(deg2rad(max($v1, $v2) % 360)));
        }

        if (($fill = $this->_getFillStyle($fillColor, $x - $rx - 1, $y - $ry - 1, $x + $rx + 1, $y + $ry + 1)) !== false) {
            ImageFilledPolygon($this->_canvas, $polygon, count($polygon) / 2, $fill);
        }

        if (($line = $this->_getLineStyle($lineColor)) !== false) {
            ImagePolygon($this->_canvas, $polygon, count($polygon) / 2, $line);
        }

        parent::pieSlice($x, $y, $rx, $ry, $v1, $v2, $fillColor, $lineColor);
    }

    /**
     * Get the width of a text,
     *
     * @param string $text The text to get the width of
     * @return int The width of the text
     */
    function textWidth($text)
    {
        if (isset($this->_font['ttf_file'])) {
            $angle = 0;
            if (isset($this->_font['angle'])) {
                $angle = $this->_font['angle'];
            }
            $bounds = ImageTTFBBox(
                $this->_font['size'],
                $angle,
                $this->_font['ttf_file'],
                $text
            );

            $x0 = min($bounds[0], $bounds[2], $bounds[4], $bounds[6]);
            $x1 = max($bounds[0], $bounds[2], $bounds[4], $bounds[6]);
            return abs($x0 - $x1);
        } else {
            if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
                return ImageFontHeight($this->_font['font']);
            } else {
                return ImageFontWidth($this->_font['font']) * strlen($text);
            }
        }
    }

    /**
     * Get the height of a text,
     *
     * @param string $text The text to get the height of
     * @return int The height of the text
     */
    function textHeight($text)
    {       
        if (isset($this->_font['ttf_file'])) {
            $angle = 0;
            if (isset($this->_font['angle'])) {
                $angle = $this->_font['angle'];
            }

            $linebreaks = substr_count($text, "\n");            
            if (($angle == 0) && ($linebreaks == 0)) {
                /*
                 * if the angle is 0 simply return the size, due to different
                 * heights for example for x-axis labels, making the labels
                 * _not_ appear as written on the same baseline
                 */
                return $this->_font['size'] + 2;               
            }
            
            $bounds = ImageTTFBBox(
                $this->_font['size'],
                $angle,
                $this->_font['ttf_file'],
                $text
            );

            $y0 = min($bounds[1], $bounds[3], $bounds[5], $bounds[7]);
            $y1 = max($bounds[1], $bounds[3], $bounds[5], $bounds[7]);;
            return abs($y0 - $y1);
        } else {
            if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
                return ImageFontWidth($this->_font['font']) * strlen($text);
            } else {
                return ImageFontHeight($this->_font['font']);
            }
        }
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
        // TODO Make a better method for "baseline" adjustment
        
        $text = str_replace("\r", '', $text);
        $lines = explode("\n", $text);

        $x0 = $this->_getX($x);
        $y0 = $this->_getY($y);
        
        foreach ($lines as $line) {                    
    
            $textWidth = $this->textWidth($line);
            $textHeight = $this->textHeight($line);
            
            $x = $x0; 
            $y = $y0;
            
            $y0 += $textHeight + 2;
   
            if ($alignment & IMAGE_GRAPH_ALIGN_RIGHT) {
                $x = $x - $textWidth;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_X) {
                $x = $x - ($textWidth / 2);
            }
    
            if ($alignment & IMAGE_GRAPH_ALIGN_BOTTOM) {
                $y = $y - $textHeight;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_Y) {
                $y = $y - ($textHeight / 2);
            }
            
            if (($color === false) && (isset($this->_font['color']))) {
                $color = $this->_font['color'];
            }
    
            if ($color != 'transparent') {
                if (isset($this->_font['ttf_file'])) {
                    if (($this->_font['angle'] < 180) && ($this->_font['angle'] >= 0)) {
                        $y += $textHeight;
                    }
                    if (($this->_font['angle'] >= 90) && ($this->_font['angle'] < 270)) {
                        $x += $textWidth;
                    }
    
                    ImageTTFText(
                        $this->_canvas,
                        $this->_font['size'],
                        $this->_font['angle'],
                        $x,
                        $y,
                        $this->_color($color),
                        $this->_font['ttf_file'],
                        $line
                    );
    
                } else {
                    if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
                        ImageStringUp(
                            $this->_canvas,
                            $this->_font['font'],
                            $x,
                            $y + $this->textHeight($line),
                            $line,
                            $this->_color($color)
                        );
                    } else {
                        ImageString(
                            $this->_canvas,
                            $this->_font['font'],
                            $x,
                            $y,
                            $line,
                            $this->_color($color)
                        );
                    }
                }
            }
        }
        parent::write($x, $y, $text, $alignment);
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
    function overlayImage($x, $y, $filename, $width = false, $height = false, $alignment = false)
    {
        $x = $this->_getX($x);
        $y = $this->_getY($y);

        if (file_exists($filename)) {
            if (strtolower(substr($filename, -4)) == '.png') {
                $image = ImageCreateFromPNG($filename);
            } elseif (strtolower(substr($filename, -4)) == '.gif') {
                $image = ImageCreateFromGIF($filename);
            } else {
                $image = ImageCreateFromJPEG($filename);
            }

            $imgWidth = ImageSX($image);
            $imgHeight = ImageSY($image);

            $outputWidth = ($width !== false ? $width : $imgWidth);
            $outputHeight = ($height !== false ? $height : $imgHeight);

            if ($alignment & IMAGE_GRAPH_ALIGN_RIGHT) {
                $x -= $outputWidth;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_X) {
                $x -= $outputWidth / 2;
            }

            if ($alignment & IMAGE_GRAPH_ALIGN_BOTTOM) {
                $y -= $outputHeight;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_Y) {
                $y -= $outputHeight / 2;
            }

            if ((($width !== false) && ($width != $imgWidth)) ||
                (($height !== false) && ($height != $imgHeight)))
            {
                if ($this->_gd2) {
                    ImageCopyResampled(
                        $this->_canvas,
                        $image,
                        $x,
                        $y,
                        0,
                        0,
                        $width,
                        $height,
                        $imgWidth,
                        $imgHeight
                    );
                } else {
                    ImageCopyResized(
                        $this->_canvas,
                        $image,
                        $x,
                        $y,
                        0,
                        0,
                        $width,
                        $height,
                        $imgWidth,
                        $imgHeight
                    );
                }
            } else {
                ImageCopy(
                    $this->_canvas,
                    $image,
                    $x,
                    $y,
                    0,
                    0,
                    $imgWidth,
                    $imgHeight
                );
            }
            ImageDestroy($image);
        }
        parent::overlayImage($x, $y, $filename, $width, $height);
    }

    /**
     * Resets the driver.
     *
     * Include fillstyle, linestyle, thickness and polygon
     * @access private
     */
    function _reset()
    {
        if ($this->_gd2) {
            ImageSetThickness($this->_canvas, 1);
        }
        parent::_reset();
        $this->_font = array('font' => 1, 'color' => 'black');
    }

    /**
     * Check which version of GD is installed
     *
     * @return int 0 if GD isn't installed, 1 if GD 1.x is installed and 2 if
     *   GD 2.x is installed
     * @access private
     */
    function _version()
    {
        if (function_exists('gd_info')) {
            $info = gd_info();
            $version = $info['GD Version'];
        } else {
            ob_start();
            phpinfo(8);
            $php_info = ob_get_contents();
            ob_end_clean();

            if (ereg("<td[^>]*>GD Version *<\/td><td[^>]*>([^<]*)<\/td>",
                $php_info, $result))
            {
                $version = $result[1];
            }
        }

        if (ereg('1\.[0-9]{1,2}', $version)) {
            return 1;
        } elseif (ereg('2\.[0-9]{1,2}', $version)) {
            return 2;
        } else {
            return 0;
        }
    }

}

?>