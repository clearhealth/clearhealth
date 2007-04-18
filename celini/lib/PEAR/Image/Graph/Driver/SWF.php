<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in SWF format.
 *
 * Outputs the graph in SWF format (ShockWave Flash). Requires MING.
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
 * @version    CVS: $Id: SWF.php,v 1.4 2005/02/21 20:49:52 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 * @since      File available since Release 0.3.0dev2
 */

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
 * SWF Driver class.
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
class Image_Graph_Driver_SWF extends Image_Graph_Driver
{
    /**
     * The SWF Movie
     * @var SWFMovie
     * @access private
     */
    var $_movie;

    /**
     * The SWF Font
     * @var SWFFont
     * @access private
     */
    var $_swfFont = null;

    /**
     * Create a driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function &Image_Graph_Driver_SWF($param)
    {
        parent::Image_Graph_Driver($param);

        include_once 'Image/Graph/Color.php';

        $this->_movie =& new SWFMovie();
        $this->_movie->setDimension($this->_width, $this->_height);
        $this->_movie->setBackground(255, 255, 255);

        $this->setFont(array());
    }

    /**
     * Get the GD applicable linestyle
     *
     * @param SWFShape $shape The shape to set the line style for
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     * @return bool Whether the line style makes the element visible or not
     * @access private
     */
    function _getLineStyle(&$shape, $lineStyle = false)
    {
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        if (($lineStyle == 'transparent') || ($lineStyle === false)) {
            return false;
        } else {
            if (is_array($lineStyle)) {
            } else {
                $color = Image_Graph_Color::color2rgb($lineStyle);
                $shape->setLine($this->_thickness, $color[0], $color[1], $color[2], $color[3]);
            }
            return true;
        }
    }

    /**
     * Get the GD applicable fillstyle
     *
     * @param SWFShape $shape The shape to set the line style for
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     * @return mixed A GD compatible fillstyle
     * @access private
     */
    function _getFillStyle(&$shape, $fillStyle = false, $x0 = 0, $y0 = 0, $x1 = 0, $y1 = 0)
    {
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }

        if (($fillStyle == 'transparent') || ($fillStyle === false)) {
            return false;
        } else {
            if ((is_string($fillStyle)) && (file_exists($fillStyle))) {
                // TODO Image fill SWF doesn't seem to work even with JPEG's
                //$fill =& new SWFBitmap($fillStyle);
                //$fill =& $shape->addFill($fill);
                //$shape->setRightFill($fill);
                // TODO Scale the image fill in SWF
            } elseif (is_array($fillStyle)) {
                // TODO I don't get SWF gradient fill: moveTo/scaleTo/rotateTo

                $startColor = Image_Graph_Color::color2rgb($fillStyle['start']);
                $endColor = Image_Graph_Color::color2rgb($fillStyle['end']);

                switch ($this->_fillStyle['direction']) {
                case IMAGE_GRAPH_GRAD_HORIZONTAL:
                case IMAGE_GRAPH_GRAD_VERTICAL:
                case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR:
                case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR:
                    $fill =& new SWFGradient();
                    $fill->addEntry(0.0, $startColor[0], $startColor[1], $startColor[2], $startColor[3]);
                    $fill->addEntry(1.0, $endColor[0], $endColor[1], $endColor[2], $endColor[3]);
                    $fill =& $shape->addFill($fill, SWFFILL_LINEAR_GRADIENT);
                    switch ($this->_fillStyle['direction']) {
                        case IMAGE_GRAPH_GRAD_HORIZONTAL:
                            $fill->scaleTo(abs($x1 - $x0) / $this->_width);
                            $fill->moveTo(min($x0, $x1), min($y0, $y1));
                            break;

                        case IMAGE_GRAPH_GRAD_VERTICAL:
                            $fill->rotateTo(90);
                            break;

                        case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR:
                            $fill->rotateTo(315);
                            break;

                        case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR:
                            $fill->rotateTo(135);
                            break;
                    }
                    break;

                case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED:
                case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED:
                    $fill =& new SWFGradient();
                    $fill->addEntry(0.0, $startColor[0], $startColor[1], $startColor[2], $startColor[3]);
                    $fill->addEntry(0.5, $endColor[0], $endColor[1], $endColor[2], $endColor[3]);
                    $fill->addEntry(1.0, $startColor[0], $startColor[1], $startColor[2], $startColor[3]);
                    $fill =& $shape->addFill($fill, SWFFILL_LINEAR_GRADIENT);
                    break;

                case IMAGE_GRAPH_GRAD_RADIAL:
                    $fill =& new SWFGradient();
                    $fill->addEntry(0.0, $startColor[0], $startColor[1], $startColor[2], $startColor[3]);
                    $fill->addEntry(1.0, $endColor[0], $endColor[1], $endColor[2], $endColor[3]);
                    $fill =& $shape->addFill($fill, SWFFILL_RADIAL_GRADIENT);
                    //$fill->moveTo($w / 2, $h / 2);
                    break;
                }
                $shape->setRightFill($fill);
            } else {
                $color = Image_Graph_Color::color2rgb($fillStyle);
                $shape->setRightFill($color[0], $color[1], $color[2], $color[3]);
            }
            return true;
        }
    }

    /**
     * Sets the font options.
     *
     * The $font array may have the following entries:
     * 'type' = 'ttf' (TrueType) or omitted for default<br>
     * If 'type' = 'ttf' then the following can be specified<br>
     * 'size' = size in pixels<br>
     * 'angle' = the angle with which to write the text
     * 'file' = the .ttf file (either the basename, filename or full path)
     *
     * @param array $font The font options.
     */
    function setFont($fontOptions)
    {
     //   if ($fontOptions != $this->_font) {
            parent::setFont($fontOptions);
/*
            if (isset($this->_font['file'])) {
                $file = $this->_font['file'];
            } else {
                $file = 'arial.fdb';
            }

            if (strtolower(substr($file, -4)) != '.fdb') {
                $file .= '.fdb';
            }

            if (file_exists(dirname(__FILE__) . '/../Fonts/' . $file)) {
                $file = dirname(__FILE__) . '/../Fonts/' . $file;
            }

            $this->_swfFont =& new SWFFont($file);
        }*/
    }

    /**
     * Sets an image that should be used for filling
     *
     * @param string $filename The filename of the image to fill with
     */
    function setFillImage($filename)
    {
        $this->_fillStyle = $filename;
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
        $line =& new SWFShape();
        if ($this->_getLineStyle($line, $color)) {
            $line->drawLine($x1 - $x0, $y1 - $y0);
            $line_ =& $this->_movie->add($line);
            $line_->moveTo($x0, $y0);
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
        $polygon =& new SWFShape();

        $this->_getLineStyle($polygon, $lineColor);

        foreach ($this->_polygon as $point) {
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

        if ($connectEnds) {
            $this->_getFillStyle($polygon, $fillColor, $low['X'], $low['Y'], $high['X'], $high['Y']);
        }

        foreach ($this->_polygon as $point) {
            if (isset($last)) {
                $polygon->drawLine($point['X'] - $last['X'], $point['Y'] - $last['Y']);
            }
            if (!isset($p0)) {
                $p0 = $point;
            }
            $last = $point;
        }


        if ($connectEnds) {
            $polygon->drawLine($p0['X'] - $last['X'], $p0['Y'] - $last['Y']);
        }

        $polygon_ =& $this->_movie->add($polygon);
        $polygon_->moveTo($p0['X'], $p0['Y']);

        parent::polygonEnd($connectEnds, $fillColor, $lineColor);
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
        $polygon =& new SWFShape();

        foreach ($this->_polygon as $point) {
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

        $this->_getLineStyle($polygon, $lineColor);

        if ($connectEnds) {
            $this->_getFillStyle($polygon, $fillColor, $low['X'], $low['Y'], $high['X'], $high['Y']);
        }

        $first = true;
        foreach ($this->_polygon as $point) {
            if ($first === true) {
                $first = $point;
            } else {
                if (isset($last['P1X'])) {
                    // TODO SWF curve smoothing requires only one control point - this method is not very good!
                    $polygon->drawCurveTo(
                        ($last['P1X'] + $last['P2X']) / 2 - $first['X'],
                        ($last['P1Y'] + $last['P2Y']) / 2 - $first['Y'],
                        $point['X'] - $first['X'],
                        $point['Y'] - $first['Y']
                    );
                } else {
                    $polygon->drawLineTo(
                        $point['X'] - $first['X'],
                        $point['Y'] - $first['Y']
                    );
                }
            }
            $last = $point;
        }

        if ($connectEnds) {
            if (isset($last['P1X'])) {
                $polygon->drawCurveTo(
                    ($last['P1X'] + $last['P2X']) / 2 - $first['X'],
                    ($last['P1Y'] + $last['P2Y']) / 2 - $first['Y'],
                    0,
                    0
                );
            } else {
                $polygon->drawLineTo(
                    0,
                    0
                );
            }
        }

        $polygon_ =& $this->_movie->add($polygon);
        $polygon_->moveTo($first['X'], $first['Y']);

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
        $rectangle =& new SWFShape();
        $line = $this->_getLineStyle($rectangle, $lineColor);
        $fill = $this->_getFillStyle($rectangle, $fillColor, $x0, $y0, $x1, $y1);
        if (($fill) || ($line)) {
            $rectangle->drawLine($x1 - $x0, 0);
            $rectangle->drawLine(0, $y1 - $y0);
            $rectangle->drawLine($x0 - $x1, 0);
            $rectangle->drawLine(0, $y0 - $y1);
            $rectangle_ =& $this->_movie->add($rectangle);
            $rectangle_->moveTo($x0, $y0);
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
        $ellipse =& new SWFShape();
        $line = $this->_getLineStyle($ellipse, $lineColor);
        $fill = $this->_getFillStyle($ellipse, $fillColor, $x - $rx, $y - $ry, $x + $rx, $y + $ry);
        if (($fill) || ($line)) {
            $ax = $rx * 0.414213562; // = tan(22.5 deg)
            $bx = $rx * 0.707106781; // = sqrt(2)/2 = sin(45 deg)

            $ay = $ry * 0.414213562; // = tan(22.5 deg)
            $by = $ry * 0.707106781; // = sqrt(2)/2 = sin(45 deg)

            $ellipse->movePenTo($x + $rx, $y);

            $ellipse->drawCurveTo($x + $rx, $y - $ay, $x + $bx, $y - $by);
            $ellipse->drawCurveTo($x + $ax, $y - $ry, $x, $y - $ry);
            $ellipse->drawCurveTo($x - $ax, $y - $ry, $x - $bx, $y - $by);
            $ellipse->drawCurveTo($x - $rx, $y - $ay, $x - $rx, $y);
            $ellipse->drawCurveTo($x - $rx, $y + $ay, $x - $bx, $y + $by);
            $ellipse->drawCurveTo($x - $ax, $y + $ry, $x, $y + $ry);
            $ellipse->drawCurveTo($x + $ax, $y + $ry, $x + $bx, $y + $by);
            $ellipse->drawCurveTo($x + $rx, $y + $ay, $x + $rx, $y);

            $ellipse_ =& $this->_movie->add($ellipse);
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
        // TODO Implement SWF::pieSlice
        parent::pieSlice($x, $y, $rx, $ry, $v1, $v2, $srx, $sry, $fillColor, $lineColor);
    }

    /**
     * Get the width of a text,
     *
     * @param string $text The text to get the width of
     * @return int The width of the text
     */
    function textWidth($text)
    {
        // TODO Implement SWF::textWidth
/*        return 10;
        $atext =& new SWFText();
        $atext->setFont($this->_swfFont);
        $atext->setColor(0, 0, 0);
        $atext->setHeight(12);
        return $atext->getWidth($text);*/
    }

    /**
     * Get the height of a text,
     *
     * @param string $text The text to get the height of
     * @return int The height of the text
     */
    function textHeight($text)
    {
        // TODO Implement SWF::textHeight
        //return 12;
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
        // TODO Implement SWF::write
        /*$font = new SWFFont('BUMP!');
        $atext =& new SWFText();
        $atext->setFont($font);
        $atext->setColor(0, 0, 0);
        $atext->setHeight(12);
        $atext->addString($text);
        $atext->moveTo($x, $y);
        $this->_movie->add($atext);*/
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
        // TODO Implement SWF::overlayImage
        parent::overlayImage($x, $y, $filename, $width, $height);
    }

    /**
     * Output the result of the driver
     *
     * @param array $param Parameter array
     * @abstract
     */
    function done($param = false)
    {
        if (($param !== false) && (isset($param['filename']))) {
            $this->_movie->save($param['filename']);
        } else {
            header("Content-type: application/x-shockwave-flash");
            $this->_movie->output();
        }
    }
}

?>