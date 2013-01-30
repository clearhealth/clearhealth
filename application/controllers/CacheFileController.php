<?php
/*****************************************************************************
*       CacheFileController.php
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


/**
 * Cache file controller
 */
class CacheFileController extends WebVista_Controller_Action {

    /**
     * Default action to dispatch
     */
    public function indexAction() {
    }

    /**
     * Handles cascading style sheet files
     */
    public function cssAction() {

        $mediaType = 'css';
        $str = $this->_getParam('files');
	$explodedFiles = explode(',', $str);
	$cssListFiles = $this->_getCSSListFiles();
	$listFiles = array();
	foreach($explodedFiles as $index) {
		// skip if key does not exists
		if (!array_key_exists($index,$cssListFiles)) {
			$listFiles[] = $index;
			continue;
                	//throw new Exception(__("File list does not exist : ") . $index);
		}
		$listFiles[] = implode(',',$cssListFiles[$index]);
	}
	$str = implode(',',$listFiles);
	$cacheKey = $this->generateCacheKey($mediaType, $str);
	$this->return304CacheHit($cacheKey);

        $basename = array();
        $basedir = Zend_Registry::get('basePath');
        $basename[] = $basedir . $mediaType;
        $basename[] = $basedir . 'js/dojo';
        $basename[] = $basedir . 'js/dojox';
        $basename[] = $basedir . 'js/dijit';
        $basename[] = $basedir . 'js/dijit/themes/tundra';

        $fileNames = $this->generateFilenames($str);
	$output = $this->getCachedOutput($mediaType, $cacheKey, $basename,
                                         $fileNames);

        $this->view->css =  $output;
        header('Content-Type: text/css;');
        header("Cache-Control: must-revalidate");
        $this->render('css');
    }

    /**
     * Handles javascript files
     */
    public function jsAction() {

        $mediaType = 'js';
        $mediaExt = array('js','php');
        $str = $this->_getParam('files');
	$explodedFiles = explode(',', $str);
	$jsListFiles = $this->_getJSListFiles();
	$listFiles = array();
	foreach($explodedFiles as $index) {
		// skip if key does not exists
		if (!array_key_exists($index,$jsListFiles)) {
			continue;
                	throw new Exception(__("File list does not exist : ") . $index);
		}
		$listFiles[] = implode(',',$jsListFiles[$index]);
	}
	$str = implode(',',$listFiles);

	$cacheKey = $this->generateCacheKey($mediaType, $str);
	$this->return304CacheHit($cacheKey);

        $basename = array();
        $basedir = Zend_Registry::get('basePath');
        $basename[] = $basedir . $mediaType;
        $basename[] = $basedir . "js/dojo";
        $basename[] = $basedir . "js/dojo/_base";
        $basename[] = $basedir . "js/dojox";
        $basename[] = $basedir . "js/dijit";

        $fileNames = $this->generateFilenames($str);
	$output = $this->getCachedOutput($mediaExt, $cacheKey, $basename,
                                         $fileNames);
        $this->view->js =  $output;
        header('Content-Type: text/javascript;');
        $this->render('js');
    }

	public function imagesAction() {
		$requestUri = explode('/',$_SERVER['REQUEST_URI']);
		foreach ($requestUri as $segment) {
			array_shift($requestUri);
			if (strtolower($segment) == 'images') {
				break;
			}
		}
		$image = array_pop($requestUri);
		$x = explode('.',$image);
		array_pop($x);
		array_push($requestUri,implode('.',$x));
		$file = implode('-',$requestUri);
		$this->_setParam('file',$file);
		$this->imageAction();
	}

    /**
     * Handles images files
     */
    public function imageAction() {

        $mediaType = 'img';
        $imageExt = array('jpg', 'png', 'gif');
        $str = $this->_getParam('file');

	$cacheKey = $this->generateCacheKey($mediaType, $str);
	$this->return304CacheHit($cacheKey);

        $basename = array();
        $basedir = Zend_Registry::get('basePath');
        $basename[] = $basedir . $mediaType;

        $fileNames = $this->generateFilenames($str);
	$output = $this->getCachedOutput($imageExt, $cacheKey, $basename,
                                         $fileNames, '', $ext);
        $this->view->image =  $output;
        header('Content-Type: image/' . $ext . ';');
        $this->render('image');
    }

    /**
     * Handles pdf files
     */
    public function pdfAction() {

        $mediaType = 'pdf';
        $str = $this->_getParam('file');

	$cacheKey = $this->generateCacheKey($mediaType, $str);
	$this->return304CacheHit($cacheKey);

        $basename = array();
        $basedir = Zend_Registry::get('basePath');
        $basename[] = $basedir . $mediaType;

        $fileNames = $this->generateFilenames($str);
	$output = $this->getCachedOutput($mediaType, $cacheKey, $basename,
                                         $fileNames, '');
        $this->view->pdf =  $output;
        header('Content-Type: application/pdf;');
        $this->render('pdf');
    }

    /**
     * Serve media file
     *
     * @param   mixed   $type           Media type/s
     * @param   mixed   $searchPaths    Directory paths to search
     * @param   mixed   $fileNames      Filenames to retrieve
     * @param   string  $separator      Content separator, default: newline
     * @param   mixed   $searchExt      Reference address of search extension
     * @return string   Content file separated by newline per file
     */
    protected function getMediaFiles($types, $searchPaths, $fileNames,
                                   $separator = PHP_EOL, &$searchExt = null) {

        if (!is_array($types)) {
            $types = array($types);
        }
        if (!is_array($searchPaths)) {
            $searchPaths = array($searchPaths);
        }
        if (!is_array($fileNames)) {
            $fileNames = array($fileNames);
        }

        $ret = '';
	$content = '';
        for ($i = 0; $i < count($fileNames); $i++) {
            // check file per directory name
            for ($x = 0; $x < count($searchPaths); $x++) {
		$base = $searchPaths[$x];
                foreach ($types as $type) {
                    $cleanType = str_replace('.', '', $type);
                    $filename = $base . DIRECTORY_SEPARATOR
                              . $fileNames[$i] . '.' . $cleanType;
                    if (file_exists($filename)) {
			if (strtolower($cleanType) == 'php') {
				ob_start();
				require $filename;
				$content = ob_get_contents() . $separator;
				ob_end_clean();
			}
			else {
                        	$content = file_get_contents($filename) . $separator;
			}
                        $searchExt = $cleanType;
                        break 2;
                    }
			//else if ($x == (count($searchPaths)-1)) {
				//echo $filename;exit;
                		//throw new Exception("File does not exist : " . $filename . " also tried paths " . implode(':',$searchPaths));
			//}
                }
            }
            if (!isset($content)) {
				echo $filename;exit;
                throw new Exception("Problem loading : " . $fileNames[$i]);
            }
            $ret .= $content;
            unset($content);
        }
        return $ret;
    }

    protected function generateCacheKey($mediaType, $fileRequestString) {
        $str = preg_replace('/[^a-zA-Z0-9-,_]+/', '', $fileRequestString);
        $str = str_replace(',', '-', $str);
        $str = str_replace('-', '_', $str);
        return $mediaType ."_" .$str;
    }

    protected function return304CacheHit($cacheKey) {
        $cache = Zend_Registry::get('cache');
        if ($cache->test($cacheKey."_hash")) {
            $hash = $cache->load($cacheKey."_hash");
            $lastModified = $cache->load($cacheKey."_lastModified");
            $headers = getallheaders();
            if (isset($headers['If-None-Match']) &&
                preg_match('/'.$hash.'/', $headers['If-None-Match'])) {
                header("Last-Modified: " . $lastModified);
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }
    }

    protected function getCachedOutput($mediaType, $cacheKey, $searchPaths, 
                                       $fileNames, $separator = PHP_EOL,
                                       &$searchExt = null) {
        $cache = Zend_Registry::get('cache');

        if ($cache->test($cacheKey)) {
            $output = $cache->load($cacheKey);
            $searchExt = $cache->load($cacheKey."_ext");
            return $output;
        }
        else {
            try {
                $output = $this->getMediaFiles($mediaType, $searchPaths,
                                               $fileNames, $separator, $searchExt);
            }
            catch (Exception $e) {
                exit;
            }

            $miniSuffix = $mediaType;
            if (is_array($mediaType)) {
                $miniSuffix = $searchExt;
            }
            $minifyFunc = 'minify' . ucfirst($miniSuffix);
            if (method_exists($this,$minifyFunc)) {
            	$output = $this->$minifyFunc($output);
	    }

            $hash = md5($output);
            $lastModified = gmdate("D, d M Y H:i:s")." GMT";
            $cache->save($searchExt, $cacheKey."_ext");
            $cache->save($hash, $cacheKey."_hash");
            $cache->save($lastModified, $cacheKey."_lastModified");
            $cache->save($output, $cacheKey);
            header("ETag: ". $hash);
            header("Last-Modified: ". $lastModified);
            header("Content-length: "  . mb_strlen($output));
            return $output;
        }
    }

    protected function generateFilenames($str) {
        $str = preg_replace('/[^a-zA-Z0-9-,_\/]+/', '', $str);
        $str = str_replace('-', '/', $str);
        return explode(',', $str);
    }

    protected function minifyJs($jsCode) {
        return $jsCode;
    }

    protected function minifyCss($cssCode) {
        return $cssCode;
    }

	protected function _getCSSListFiles() {
		$files = array();
		$files['dojocss'] = array();
		$files['dojocss'][] = 'resources-dojo';
		$files['dojocss'][] = 'tundra';
		$files['dojocss'][] = 'themes-dijit';
		$files['dojocss'][] = 'layout-AccordionContainer';
		$files['dojocss'][] = 'Common';
		$files['dojocss'][] = 'layout-TabContainer';
		$files['dojocss'][] = 'layout-SplitContainer';
		$files['dojocss'][] = 'form-Checkbox';
		$files['dojocss'][] = 'layout-BorderContainer';
		$files['dojocss'][] = 'form-Common';
		$files['dojocss'][] = 'form-RadioButton';
		$files['dojocss'][] = 'form-TextArea';
		$files['dojocss'][] = 'form-Slider';
		$files['dojocss'][] = 'Tree';
		$files['dojocss'][] = 'ProgressBar';
		$files['dojocss'][] = 'TitlePane';
		$files['dojocss'][] = 'Calendar';
		$files['dojocss'][] = 'Toolbar';
		$files['dojocss'][] = 'Dialog';
		$files['dojocss'][] = 'Editor';
		$files['dojocss'][] = 'Menu';
		$files['dojocss'][] = 'ColorPalette';
		$files['dojocss'][] = 'widget-Calendar-Calendar';
		$files['dojocss'][] = 'form-Button';
		$files['dojocss'][] = 'style';

		$files['dhtmlxcss'] = array();
		$files['dhtmlxcss'][] = 'dhtmlxtabbar';
		$files['dhtmlxcss'][] = 'dhtmlxmenu_xp';
		$files['dhtmlxcss'][] = 'dhtmlxtoolbar_clearsilver';
		$files['dhtmlxcss'][] = 'dhtmlxgrid';
		$files['dhtmlxcss'][] = 'dhtmlxgrid_skins';
		$files['dhtmlxcss'][] = 'dhtmlxwindows';
		$files['dhtmlxcss'][] = 'dhtmlxwindows_clear_silver';
		$files['dhtmlxcss'][] = 'dhtmlxtree';
		$files['dhtmlxcss'][] = 'dhtmlxlayout';
		$files['dhtmlxcss'][] = 'dhtmlxlayout_dhx_blue';
		$files['dhtmlxcss'][] = 'dhtmlxeditor';
		$files['dhtmlxcss'][] = 'dhtmlxeditor_dhx_blue';
		$files['dhtmlxcss'][] = 'dhtmlxaccordion_dhx_blue';
		$files['dhtmlxcss'][] = 'dhtmlxmenu_standard';
		$files['dhtmlxcss'][] = 'dhtmlxfolders';
		$files['dhtmlxcss'][] = 'dhtmlxcalendar';

		return $files;
	}

	protected function _getJSListFiles() {
		$files = array();
		$files['dojojs'] = array();
		$files['dojojs'][] = '_loader-bootstrap';
		$files['dojojs'][] = '_loader-loader';
		$files['dojojs'][] = '_loader-hostenv_browser';
		$files['dojojs'][] = 'lang';
		$files['dojojs'][] = 'declare';
		$files['dojojs'][] = 'connect';
		$files['dojojs'][] = 'Deferred';
		$files['dojojs'][] = 'json';
		$files['dojojs'][] = 'array';
		$files['dojojs'][] = 'Color';
		$files['dojojs'][] = 'window';
		$files['dojojs'][] = 'event';
		$files['dojojs'][] = 'html';
		$files['dojojs'][] = 'NodeList';
		$files['dojojs'][] = 'query';
		$files['dojojs'][] = 'xhr';
		$files['dojojs'][] = '_base-fx';
		$files['dojojs'][] = 'browser';
		$files['dojojs'][] = 'i18n';
		$files['dojojs'][] = 'cldr-supplemental';
		$files['dojojs'][] = 'date';
		$files['dojojs'][] = 'regexp';
		$files['dojojs'][] = 'custom-locale';
		$files['dojojs'][] = 'string';
		$files['dojojs'][] = 'date-locale';
		$files['dojojs'][] = '_base-focus';
		$files['dojojs'][] = '_base-manager';
		$files['dojojs'][] = 'dojo-AdapterRegistry';
		$files['dojojs'][] = '_base-place';
		$files['dojojs'][] = 'dijit-_base-window';
		$files['dojojs'][] = '_base-popup';
		$files['dojojs'][] = '_base-scroll';
		$files['dojojs'][] = '_base-sniff';
		$files['dojojs'][] = '_base-bidi';
		$files['dojojs'][] = '_base-typematic';
		$files['dojojs'][] = 'date-stamp';
		$files['dojojs'][] = 'parser';
		$files['dojojs'][] = 'dijit-_base-wai';
		$files['dojojs'][] = 'dijit-_base';
		$files['dojojs'][] = 'dijit-_Widget';
		$files['dojojs'][] = 'dijit-_Templated';
		$files['dojojs'][] = 'dijit-_Container';
		$files['dojojs'][] = 'dijit-layout-_LayoutWidget';
		$files['dojojs'][] = 'dijit-form-_FormWidget';
		$files['dojojs'][] = 'dijit-dijit';
		$files['dojojs'][] = 'dijit-_base-manager';
		$files['dojojs'][] = '_Widget';
		$files['dojojs'][] = '_Templated';
		$files['dojojs'][] = '_Calendar';
		$files['dojojs'][] = 'widget-Calendar';
		$files['dojojs'][] = 'data-QueryReadStore';
		$files['dojojs'][] = 'form-_FormWidget';
		$files['dojojs'][] = 'form-TextBox';
		$files['dojojs'][] = 'Tooltip';
		$files['dojojs'][] = 'form-ValidationTextBox';
		$files['dojojs'][] = 'form-ComboBox';
		$files['dojojs'][] = 'custom-PatientSelectAutoComplete';
		$files['dojojs'][] = 'form-_Spinner';
		$files['dojojs'][] = 'number';
		$files['dojojs'][] = 'form-NumberTextBox';
		$files['dojojs'][] = 'form-NumberSpinner';
		$files['dojojs'][] = 'custom-MedicationSelectAutoComplete';
		$files['dojojs'][] = 'form-_DateTimeTextBox';
		$files['dojojs'][] = 'form-DateTextBox';
		$files['dojojs'][] = 'io-iframe';
		$files['dojojs'][] = 'custom-SelectComboBox';
		$files['dojojs'][] = 'widget-TimeSpinner';

		$files['dojojs'][] = 'colors';
		$files['dojojs'][] = 'gfx-_base';
		$files['dojojs'][] = 'gfx-shape';
		$files['dojojs'][] = 'gfx-path';
		$files['dojojs'][] = 'gfx-matrix';
		$files['dojojs'][] = 'gfx-svg';
		$files['dojojs'][] = 'gfx';
		$files['dojojs'][] = 'lang-functional-lambda';
		$files['dojojs'][] = 'lang-functional-array';
		$files['dojojs'][] = 'lang-functional-object';
		$files['dojojs'][] = 'lang-functional-fold';
		$files['dojojs'][] = 'lang-functional-reversed';
		$files['dojojs'][] = 'lang-functional-sequence';
		$files['dojojs'][] = 'lang-functional';
		$files['dojojs'][] = 'lang-utils';
		$files['dojojs'][] = 'charting-_color';
		$files['dojojs'][] = 'charting-Element';
		$files['dojojs'][] = 'charting-Theme';
		$files['dojojs'][] = 'charting-Series';
		$files['dojojs'][] = 'charting-scaler';
		$files['dojojs'][] = 'charting-axis2d-common';
		$files['dojojs'][] = 'charting-axis2d-Base';
		$files['dojojs'][] = 'charting-axis2d-Default';
		$files['dojojs'][] = 'charting-plot2d-common';
		$files['dojojs'][] = 'charting-plot2d-Base';
		$files['dojojs'][] = 'charting-plot2d-Default';
		$files['dojojs'][] = 'charting-plot2d-Lines';
		$files['dojojs'][] = 'charting-plot2d-Areas';
		$files['dojojs'][] = 'charting-plot2d-Markers';
		$files['dojojs'][] = 'charting-plot2d-MarkersOnly';
		$files['dojojs'][] = 'charting-plot2d-Scatter';
		$files['dojojs'][] = 'charting-plot2d-Stacked';
		$files['dojojs'][] = 'charting-plot2d-StackedLines';
		$files['dojojs'][] = 'charting-plot2d-StackedAreas';
		$files['dojojs'][] = 'charting-plot2d-Columns';
		$files['dojojs'][] = 'charting-plot2d-StackedColumns';
		$files['dojojs'][] = 'charting-plot2d-ClusteredColumns';
		$files['dojojs'][] = 'charting-plot2d-Bars';
		$files['dojojs'][] = 'charting-plot2d-StackedBars';
		$files['dojojs'][] = 'charting-plot2d-ClusteredBars';
		$files['dojojs'][] = 'charting-plot2d-Grid';
		$files['dojojs'][] = 'charting-plot2d-Pie';
		$files['dojojs'][] = 'charting-Chart2D';
		$files['dojojs'][] = 'custom-Chart2D';
		$files['dojojs'][] = 'form-FilteringSelect';
		$files['dojojs'][] = 'currency';
		$files['dojojs'][] = 'form-CurrencyTextBox';
		$files['dojojs'][] = 'dnd-common';
		$files['dojojs'][] = 'dnd-autoscroll';
		$files['dojojs'][] = 'dnd-Mover';
		$files['dojojs'][] = 'dnd-Moveable';
		$files['dojojs'][] = 'dnd-TimedMoveable';
		$files['dojojs'][] = 'fx';
		$files['dojojs'][] = 'dijit-layout-ContentPane';
		$files['dojojs'][] = 'dijit-form-Form';

		$files['dojojs'][] = 'dijit-Dialog';
		$files['dojojs'][] = 'dijit-form-Button';

		$files['dhtmlxjs'] = array();
		$files['dhtmlxjs'][] = 'dhtmlxcommon';
		$files['dhtmlxjs'][] = 'dhtmlxwindows';
		$files['dhtmlxjs'][] = 'dhtmlxtabbar';
		$files['dhtmlxjs'][] = 'dhtmlxprotobar';
		$files['dhtmlxjs'][] = 'dhtmlxmenubar';
		$files['dhtmlxjs'][] = 'dhtmlxtoolbar';
		$files['dhtmlxjs'][] = 'ch3toolbar_type';
		$files['dhtmlxjs'][] = 'dhtmlxgrid';
		$files['dhtmlxjs'][] = 'dhtmlxgridcell';
		$files['dhtmlxjs'][] = 'ch3grid_excell_sub_row';
		$files['dhtmlxjs'][] = 'ch3grid_dynamic_loading';
		$files['dhtmlxjs'][] = 'dhtmlxcalendar';
		$files['dhtmlxjs'][] = 'excells-dhtmlxgrid_excell_dhxcalendar';
		$files['dhtmlxjs'][] = 'excells-dhtmlxgrid_excell_grid';
		$files['dhtmlxjs'][] = 'dhtmlxgrid_drag';
		$files['dhtmlxjs'][] = 'dhtmlxgrid_json';
		$files['dhtmlxjs'][] = 'dhtmlxgrid_pivot';
		$files['dhtmlxjs'][] = 'dhtmlxtree';
		$files['dhtmlxjs'][] = 'dhtmlxtreegrid';
		$files['dhtmlxjs'][] = 'dhtmlxtreegrid_lines';
		$files['dhtmlxjs'][] = 'dhtmlxtree_json';
		$files['dhtmlxjs'][] = 'dhtmlxlayout';
		$files['dhtmlxjs'][] = 'dhtmlxaccordion';
		$files['dhtmlxjs'][] = 'dhtmlxmenu';
		$files['dhtmlxjs'][] = 'dhtmlxeditor';
		$files['dhtmlxjs'][] = 'dhtmlxfolders';
		$files['dhtmlxjs'][] = 'dhtmlxfolders_drag';

		$files['chbootstrap'][] = 'ch3main';
		$files['chbootstrap'][] = 'ch3AppointmentCalendar';
		$files['chbootstrap'][] = 'menuActions';
		return $files;
	}
}

