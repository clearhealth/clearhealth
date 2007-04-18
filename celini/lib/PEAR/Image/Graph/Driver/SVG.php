<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in SVG format.
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
 * @version    CVS: $Id: SVG.php,v 1.6 2005/02/21 20:49:52 nosey Exp $
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
 * SVG Driver class.
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
class Image_Graph_Driver_SVG extends Image_Graph_Driver
{

    /**
     * The SVG elements
     * @var string
     * @access private
     */
    var $_elements = '';

    /**
     * The SVG defines
     * @var string
     * @access private
     */
    var $_defs = '';

    /**
     * The current indention level
     * @var string
     * @access private
     */
    var $_indent = '    ';

    /**
     * A unieuq id counter
     * @var int
     * @access private
     */
    var $_id = 1;

    /**
     * The current group ids
     * @var array
     * @access private
     */
    var $_groupIDs = array();

    /**
     * Create the SVG driver.
     *
     * Parameters available:
     *
     * 'width' The width of the graph
     *
     * 'height' The height of the graph
     *
     * @param array $param Parameter array
     */
    function &Image_Graph_Driver_SVG($param)
    {
        parent::Image_Graph_Driver($param);
        $this->_reset();        
    }

    /**
     * Add a SVG "element" to the output
     *
     * @param string $element The element
     * @access private
     */
    function _addElement($element) {
        $this->_elements .= $this->_indent . $element . "\n";
    }

    /**
     * Add a SVG "define" to the output
     *
     * @param string $def The define
     * @access private
     */
    function _addDefine($def) {
        $this->_defs .= '        ' . $def . "\n";
    }

    /**
     * Get the color index for the RGB color
     *
     * @param int $color The color
     * @return int A SVG compatible color
     * @access private
     */
    function _color($color = false)
    {
        if ($color === false) {
            return 'transparent';
        } else {
            $color = Image_Graph_Color::color2RGB($color);
            return 'rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')';
        }
    }

    /**
     * Get the opacity for the RGB color
     *
     * @param int $color The color
     * @return int A SVG compatible opacity value
     * @access private
     */
    function _opacity($color = false)
    {
        if ($color === false) {
            return false;
        } else {
            $color = Image_Graph_Color::color2RGB($color);
            if ($color[3] != 255) {
                return sprintf('%0.1f', $color[3]/255);
            } else {
                return false;
            }
        }
    }

    /**
     * Get the SVG applicable linestyle
     *
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     * @return mixed A SVG compatible linestyle
     * @access private
     */
    function _getLineStyle($lineStyle = false)
    {
        $result = '';
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        // TODO Linestyles (i.e. fx. dotted) does not work

        if (($lineStyle != 'transparent') && ($lineStyle !== false)) {
            $result = 'stroke-width:' . $this->_thickness . ';';
            $result .= 'stroke:' .$this->_color($lineStyle) . ';';
            if ($opacity = $this->_opacity($lineStyle)) {
                $result .= 'stroke-opacity:' . $opacity . ';';
            }
        }
        return $result;
    }

    /**
     * Get the SVG applicable fillstyle
     *
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     * @return mixed A SVG compatible fillstyle
     * @access private
     */
    function _getFillStyle($fillStyle = false)
    {
        $result = '';
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }

        if (is_array($fillStyle)) {
            if ($fillStyle['type'] == 'gradient') {
                $id = 'gradient_' . ($this->_id++);
                $startColor = $this->_color($fillStyle['start']);
                $endColor = $this->_color($fillStyle['end']);
                $startOpacity = $this->_opacity($fillStyle['start']);
                $endOpacity = $this->_opacity($fillStyle['end']);

                switch ($fillStyle['direction']) {
                case IMAGE_GRAPH_GRAD_HORIZONTAL:
                case IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED:
                    $x1 = '0%';
                    $y1 = '0%';
                    $x2 = '100%';
                    $y2 = '0%';
                    break;

                case IMAGE_GRAPH_GRAD_VERTICAL:
                case IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED:
                    $x1 = '0%';
                    $y1 = '100%';
                    $x2 = '0%';
                    $y2 = '0%';
                    break;

                case IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR:
                    $x1 = '0%';
                    $y1 = '0%';
                    $x2 = '100%';
                    $y2 = '100%';
                    break;

                case IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR:
                    $x1 = '0%';
                    $y1 = '100%';
                    $x2 = '100%';
                    $y2 = '0%';
                    break;

                case IMAGE_GRAPH_GRAD_RADIAL:
                    $cx = '50%';
                    $cy = '50%';
                    $r = '100%';
                    $fx = '50%';
                    $fy = '50%';
                    break;

                }

                if ($fillStyle['direction'] == IMAGE_GRAPH_GRAD_RADIAL) {
                    $this->_addDefine(
                        '<radialGradient id="' . $id . '" cx="' .
                            $cx .'" cy="' . $cy .'" r="' . $r .'" fx="' .
                            $fx .'" fy="' . $fy .'">'
                    );
                    $this->_addDefine(
                        '    <stop offset="0%" style="stop-color:' .
                            $startColor. ';' . ($startOpacity ? 'stop-opacity:' .
                            $startOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '    <stop offset="100%" style="stop-color:' .
                            $endColor. ';' . ($endOpacity ? 'stop-opacity:' .
                            $endOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '</radialGradient>'
                    );
                } elseif (($fillStyle['direction'] == IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED) ||
                    ($fillStyle['direction'] == IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED))
                {
                    $this->_addDefine(
                        '<linearGradient id="' . $id . '" x1="' .
                            $x1 .'" y1="' . $y1 .'" x2="' . $x2 .'" y2="' .
                            $y2 .'">'
                    );
                    $this->_addDefine(
                        '    <stop offset="0%" style="stop-color:' .
                            $startColor. ';' . ($startOpacity ? 'stop-opacity:' .
                            $startOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '    <stop offset="50%" style="stop-color:' .
                            $endColor. ';' . ($endOpacity ? 'stop-opacity:' .
                            $endOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '    <stop offset="100%" style="stop-color:' .
                            $startColor. ';' . ($startOpacity ? 'stop-opacity:' .
                            $startOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '</linearGradient>'
                    );
                } else {
                    $this->_addDefine(
                        '<linearGradient id="' . $id . '" x1="' .
                            $x1 .'" y1="' . $y1 .'" x2="' . $x2 .'" y2="' .
                            $y2 .'">'
                    );
                    $this->_addDefine(
                        '    <stop offset="0%" style="stop-color:' .
                            $startColor. ';' . ($startOpacity ? 'stop-opacity:' .
                            $startOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '    <stop offset="100%" style="stop-color:' .
                            $endColor. ';' . ($endOpacity ? 'stop-opacity:' .
                            $endOpacity . ';' : ''). '"/>'
                    );
                    $this->_addDefine(
                        '</linearGradient>'
                    );
                }

                return 'fill:url(#' . $id . ');';
            }
        } elseif (($fillStyle != 'transparent') && ($fillStyle !== false)) {
            $result = 'fill:' . $this->_color($fillStyle) . ';';
            if ($opacity = $this->_opacity($fillStyle)) {
                $result .= 'fill-opacity:' . $opacity . ';';
            }
            return $result;
        } else {
            return 'fill:none;';
        }
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
        $this->_fillStyle['type'] = 'gradient';
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
        parent::setFont($fontOptions);
        if (!isset($this->_font['size'])) {
            $this->_font['size'] = 10;
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
        $style = $this->_getLineStyle($color) . $this->_getFillStyle('transparent');
        if ($style != '') {
            $this->_addElement(
                '<line ' .
                    'x1="' . round($x0) . '" ' .
                    'y1="' . round($y0) . '" ' .
                    'x2="' . round($x1) . '" ' .
                    'y2="' . round($y1) . '" ' .
                    'style="' . $style . '"' .
                '/>'
            );
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
		if (!$connectEnds) {
            $fillColor = 'transparent';
        }
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);
        foreach ($this->_polygon as $point) {
            if (isset($points)) {
                $points .= ' L';
            } else {
                $points = 'M';
            }
            $points .= round($point['X']) . ',' . round($point['Y']);
        }
        if ($connectEnds) {
            $points .= ' Z';
        }
        $this->_addElement(
            '<path ' .
                 'd="' . $points . '" ' .
                 'style="' . $style . '"' .
            '/>'
        );

        parent::polygonEnd($connectEnds);
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
        if (!$connectEnds) {
            $fillColor = 'transparent';
        }
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);

        $first = true;
        $spline = false;
        foreach($this->_polygon as $point) {
            if ($first) {
                $points = 'M';
            } elseif (!$spline) {
                $points .= ' L';
            }

            $points .= ' ' . round($point['X']) . ',' . round($point['Y']);

            if ((isset($point['P1X'])) && (isset($point['P1Y'])) &&
                (isset($point['P2X'])) && (isset($point['P2Y'])))
            {
                if (($first) || (!$spline)) {
                    $points .= ' C';
                }
                $points .= ' ' .round($point['P1X']) . ',' . round($point['P1Y']) . ' ' .
                           round($point['P2X']) . ',' . round($point['P2Y']);
                $spline = true;
            } else {
                $spline = false;
            }
            $first = false;
        }
        if ($connectEnds) {
            $point .= ' Z';
        }
        $this->_addElement(
            '<path ' .
                 'd="' . $points . '" ' .
                 'style="' . $style . '"' .
            '/>'
        );

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
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);
        if ($style != '') {
            $this->_addElement(
                '<rect ' .
                    'x="' . round($x0) . '" ' .
                    'y="' . round($y0) . '" ' .
                    'width="' . round(abs($x1 - $x0)) . '" ' .
                    'height="' . round(abs($y1 - $y0)) . '" ' .
                    'style="' . $style . '"' .
                '/>'
            );
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
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);
        if ($style != '') {
            $this->_addElement(
                '<ellipse ' .
                    'cx="' . round($x) . '" ' .
                    'cy="' . round($y) . '" ' .
                    'rx="' . round($rx) . '" ' .
                    'ry="' . round($ry) . '" ' .
                    'style="' . $style . '"' .
                '/>'
            );
        }
        parent::ellipse($x, $y, $rx, $ry, $fillColor, $lineColor);
    }

    /**
     * Draw a pie slice
     *
     * @param int $x Center point x-value
     * @param int $y Center point y-value
     * @param int $rx X-radius of ellipse
     * @param int $ry Y-radius of ellipse
     * @param int $v1 The starting angle
     * @param int $v2 The end angle
     * @param mixed $fillColor The fill color, can be omitted
     * @param mixed $lineColor The line color, can be omitted
     */
    function pieSlice($x, $y, $rx, $ry, $v1, $v2, $fillColor = false, $lineColor = false)
    {
        
        // TODO Pieslices with v2-v1 < 90 "curl" the wrong way
        
        $style = $this->_getLineStyle($lineColor) . $this->_getFillStyle($fillColor);
        if ($style != '') {
            $x1 = ($x + $rx * cos(deg2rad(min($v1, $v2) % 360)));
            $y1 = ($y + $ry * sin(deg2rad(min($v1, $v2) % 360)));
            $x2 = ($x + $rx * cos(deg2rad(max($v1, $v2) % 360)));
            $y2 = ($y + $ry * sin(deg2rad(max($v1, $v2) % 360)));
            $this->_addElement(
                '<path d="' .
                    'M' . round($x) . ',' . round($y) . ' ' .
                    'L' . round($x1) . ',' . round($y1) . ' ' .
                    'A' . round($rx) . ',' . round($ry) . ' 0 0,1 ' .
                        round($x2) . ',' . round($y2) . ' ' .
                    'z" ' .
                    'style="' . $style . '"' .
                '/>'
            );
        }
    }

    /**
     * Get the width of a text,
     *
     * @param string $text The text to get the width of
     * @return int The width of the text
     */
    function textWidth($text)
    {
        if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
            return $this->_font['size'];
        } else {
            return round($this->_font['size'] * 0.7 * strlen($text));
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
        if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
            return round($this->_font['size'] * 0.7 * strlen($text));
        } else {
            return $this->_font['size'];
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
        $textHeight = $this->textHeight($text);

        $align = '';

        if ((isset($this->_font['vertical'])) && ($this->_font['vertical'])) {
            $align .= 'writing-mode: tb-rl;';

            if ($alignment & IMAGE_GRAPH_ALIGN_BOTTOM) {
                $align .= 'text-anchor:end;';
                //$y = $y + $textHeight;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_Y) {
                //$y = $y + ($textHeight / 2);
                $align .= 'text-anchor:middle;';
            }
        } else {
            if ($alignment & IMAGE_GRAPH_ALIGN_RIGHT) {
                $align .= 'text-anchor:end;';
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_X) {
                $align .= 'text-anchor:middle;';
            }

            if ($alignment & IMAGE_GRAPH_ALIGN_TOP) {
                $y = $y + $textHeight;
            } elseif ($alignment & IMAGE_GRAPH_ALIGN_CENTER_Y) {
                $y = $y + ($textHeight / 2);
            }
        }

        if (($color === false) && (isset($this->_font['color']))) {
            $color = $this->_font['color'];
        }

        $textColor = $this->_color($color);
        $textOpacity = $this->_opacity($color);

        $this->_addElement(
            '<text ' .
                'x="' . round($x) . '" ' .
                'y="' . round($y) . '" ' .
/*                (isset($this->_font['angle']) && ($this->_font['angle'] > 0) ?
                    'rotate="' . $this->_font['angle'] . '" ' :
                    ''
                ) .*/
                'style="' .
                (isset($this->_font['file']) ?
                    'font-family:' . $this->_font['file'] . ';' : '') .
                        'font-size:' . $this->_font['size'] . 'px;fill=' .
                        $textColor . ($textOpacity ? ';fill-opacity:' .
                        $textOpacity :
                    ''
                ) . ';' . $align . '">' .
                str_replace('&', '&amp;', $text) .
            '</text>'
        );
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
    function overlayImage($x, $y, $filename, $width = false, $height = false)
    {
        // TODO Make images work in SVG
        $filename = 'file:///' . str_replace('\\', '/', $filename);
        $this->_addElement(
            '<image xlink:href="' . $filename . '" x="' . $x . '" y="' . $y .
                ($width ? '" width="' . $width : '') .
                ($height ? '" height="' . $height : '') .
            '"/>'
        );
        parent::overlayImage($x, $y, $filename, $width, $height);
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
        $name = strtolower(str_replace(' ', '_', $name));
        if (in_array($name, $this->_groupIDs)) {
            $name .= $this->_id;
            $this->_id++;
        }
        $this->_groupIDs[] = $name;
        $this->_addElement('<g id="' . $name . '">');
        $this->_indent .= '    ';        
    }

    /**
     * End the "current" group.
     * 
     * What this does, depends on the driver/format.
     */
    function endGroup()
    {
        $this->_indent = substr($this->_indent, 0, -4);
        $this->_addElement('</g>');
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
        $output = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n" .
            '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"' . "\n\t" .
            ' "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">' . "\n" .
            '<svg width="' . $this->_width . '" height="' . $this->_height .
                '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' . "\n" .
            ($this->_defs ?
                '    <defs>' . "\n" .
                $this->_defs .
                '    </defs>' . "\n" :
                ''
            ) .
            $this->_elements .
            '</svg>';
        if ($param === false) {
            header('Content-Type: image/svg+xml');
            header('Content-Disposition: inline; filename = "' . basename($_SERVER['PHP_SELF'], '.php') . '.svg"');
            print $output;
        } elseif (isset($param['filename'])) {
            $file = fopen($param['filename'], 'w+');
            fwrite($file, $output);
            fclose($file);
        }
    }

}

?>