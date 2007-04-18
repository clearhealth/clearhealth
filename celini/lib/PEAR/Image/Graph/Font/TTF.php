<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
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
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: TTF.php,v 1.5 2005/02/21 20:49:59 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Font/Extended.php
 */
require_once 'Image/Graph/Font/Extended.php';

/**
 * A truetype font.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.3.0
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_Font_TTF extends Image_Graph_Font_Extended
{

    /**
     * The file of the font.
     * On Windows systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS
     * @var string
     * @access private
     */
    var $_fontFile;

    /**
     * FontTTF [Constructor]
     *
     * @param string $fontFile The filename of the TTF font file. On Windows
     *   systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS
     */
    function &Image_Graph_Font_TTF($fontFile)
    {
        parent::Image_Graph_Font();
        $this->setFontFile($fontFile);
    }

    /**
     * Set another font file
     *
     * @param string $fontFile The filename of the TTF font file. On Windows
     *   systems they will be located in %SYSTEMROOT%\FONTS, ie C:\WINDOWS\FONTS
     */
    function setFontFile($fontFile)
    {
        $this->_fontFile = $fontFile;
    }

    /**
     * Get the font 'array'
     *
     * @return array The font 'summary' to pass to the driver
     * @access private
     */
    function _getFont($options = false)
    {
        $options = parent::_getFont($options);
        unset($options['font']);
        $options['ttf'] = $this->_fontFile;
        if (!isset($options['size'])) {
            $options['size'] = $this->_size;
        }
        if (!isset($options['angle'])) {
            $options['angle'] = $this->_angle;
        }
        return $options;
    }

}

?>