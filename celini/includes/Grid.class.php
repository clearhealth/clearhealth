<?php

$loader->requireOnce("/includes/Pager.class.php");

class cGrid {

	/**
	* Grid options
	*/

	/**
	* Add an index column
	*/
	var $indexCol = true;
	
	/**
	 * Should the hidecolumn image show?
	 *
	 * If left at null at the end of the constructor, this will default to {@link $orderLinks}.
	 *
	 * @var boolean
	 */
	var $_allowHideColumns = null;

	/**
	* Attributes
	*
	* row labels is generated labels
	* row table is the table tag
	* row thead is the thead tag
	* col tr is the tr tag, td/th tags are specified by the column name in the query
	*
	* array( row => array(col => array( 'attribute_name' => 'attribute_value')))
	*/
	var $attributes = array(  'table' => array(array('class' => 'grid', 'cellspacing' => '1', 'cellpadding' => '0')));

	/** 
	 * table caption
	 */
	var $caption = false;

	/**
	* Sort Link
	*/
	var $link = false;

	/**
	* Image dir
	*
	* With trailing /
	*/
	var $imageDir = false;

	/**
	 * Number of records per page, if not false, a pager is created
	 */
	var $pageSize = false;

	/**
	 * A pager objecct
	 */
	var $pager = false;

	/**
	 * True when the grid ran prepare but contained 0 rows
	 */
	var $empty = false;

	/**
	 * Produce order by links in the table labels?
	 */
	var $orderLinks = true;

	var $_datasource;
	var $_renderer;
	var $_request = array();
	var $name = 'grid';

	var $_prepared = false;
	
	
	/**
	 * This is the external id required for this grid to properly instantiate
	 *
	 * If this is left null, _exportGridTo*Link()s will return null.
	 *
	 * @var int|null
	 */
	var $_external_id = null;
	
	
	/**
	 * This stores any extra URI information that should be appended to the
	 * export URLs
	 *
	 * @var string|null
	 */
	var $_extraURI = null;
	
	
	/**
	 * Stores the action that the export URL should link to.
	 *
	 * @see setExportAction()
	 * @var string
	 */
	var $_exportAction = 'export_grid';
	
	
	/**
	 * If <i>true</i> does not display the export link.
	 *
	 * @var boolean
	 */
	var $hideExportLink = false;
	

	function cGrid(&$datasource, $renderer = false) {
		if (isset($GLOBALS['cGrid']['counter'])) {
			$GLOBALS['cGrid']['counter']++;
		}
		else {
			$GLOBALS['cGrid']['counter'] = 0;
		}
		$this->name = $_SERVER['PHP_SELF'].$GLOBALS['cGrid']['counter'];
		
		if ($renderer === false) {
			$renderer =& new Grid_Renderer_HTML();
		}
		$this->set_datasource($datasource);
		$this->set_renderer($renderer);

		if (isset($GLOBALS['GRIDMODE']) && $GLOBALS['GRIDMODE'] == 'pdf') {
			$this->updateAttribute('table',0,array('border'=>1,'cellspacing'=>0,'cellpadding'=>2));
			$this->orderLinks = false;
		}
		
		
		$this->_discoverExternalId();
		if (isset($this->_datasource->hideExportLink)) {
			$this->hideExportLink = $this->_datasource->hideExportLink;
		}
	}

	function updateAttribute($row,$col,$values) {
		if (isset($this->attributes[$row][$col])) {
			$this->attributes[$row][$col] = array_merge($this->attributes[$row][$col],$values);
		}
		else {
			$this->attributes[$row][$col] = $values;
		}
	}

	function set_renderer(&$renderer) {
		$this->_renderer =& $renderer;
		$this->_renderer->_grid =& $this;
	}

	function set_datasource(&$source) {
		$this->_datasource =& $source;
	}
	function get_datasource() {
		return  $this->_datasource;
	}

	function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
	}
	
	function render_pager() {
		$this->prepare();
		if ($this->empty) {
			return "";
		}
		$ret = "<table cellspacing='0' cellpadding='0'><tr><td>";
		if (is_object($this->pager)) {
			$ret .= $this->pager->render();
		}
		return $ret;
	}

	function render($render_pager = true) {
		$this->prepare();

		if ($this->empty) {
			return "";
		}
		
		$ret = '';
		if ($render_pager && $this->pageSize > 0) {
			$ret .= "<table cellspacing='0' cellpadding='0'><tr><td>";
			if (is_object($this->pager)) {
				$ret .= $this->pager->render();
			}
		}
		$ret .= $this->_renderer->render();
		if ($render_pager && $this->pageSize > 0) {
			$ret .= "</td></tr></table>";
		}
		return $ret;
	}

	function renderBody() {
		return $this->_renderer->renderBody();
	}

	function _parseRequest() {
		// load in current sorts
		//unset($_SESSION['grid']);
		if (!isset($_SESSION['grid'][$this->name]['orderby'])) {
			$this->_datasource->loadDefaultOrderRules();
		}
		if (isset($_SESSION['grid'][$this->name]['orderby'])) {
			foreach($_SESSION['grid'][$this->name]['orderby'] as $rule) {
				$this->_datasource->addOrderRule($rule[0],$rule[1],$rule[2]);
			}
		}
		
		if (isset($GLOBALS['GRID']) && $GLOBALS['GRID'] == $this->name) {
			// load in new sorts from get
			if (isset($GLOBALS['ORDER'])) {
				$rule = $GLOBALS['ORDER'];

				$this->_datasource->addOrderRule($rule['column'],$rule['direction'],$rule['order']);
				$this->_datasource->_orderCurrent = $rule['column'];
				if (isset($GLOBALS['MOVE'])) {
					$this->_datasource->_orderCurrentDirection = $GLOBALS['MOVE'];
				}
			}
			$this->_datasource->getRenderMap();
			$_SESSION['grid'][$this->name]['orderby'] = $this->_datasource->getAllOrderRules();
		}
	}

	/**
	* Setup grid sorting from _GET and _SESSION
	*
	* Create a pager if needed
	*
	* Sets empty
	*/
	function prepare() {
		if (!$this->_prepared) {
			$this->_parseRequest();
			if ($this->link === false) {
				/*echo "<!--\n";
				print_R($_SERVER);
				echo "-->\n";*/
				$this->link = $_SERVER['PHP_SELF']."?";
				//$_GET['grid'] = $this->name;
				foreach($_GET as $key => $val) {
					if ($key !== "ORDER" && $key != "GRID") {
						$this->link .= "&$key=$val";
					}
				}
				$this->link .= "&GRID=$this->name";
			}
			if  ($this->imageDir === false) {
				$this->imageDir = str_replace('//','/',dirname($_SERVER['SCRIPT_NAME'])."/".$GLOBALS['config']['entry_file']."/images/stock/");
			}
			$this->_datasource->prepare();
			$_SESSION['grid'][$this->name]['orderby'] = $this->_datasource->getAllOrderRules();
			$this->_prepared = true;
		}

		$this->_renderer->indexCol = $this->indexCol;

		$rows = $this->_datasource->numRows();
		if ($rows == 0) {
			$this->empty = true;
		}
		
		if ($this->pageSize !== false) {
			$this->pager = new Pager();
			$this->pager->inGrid = true;
			$this->pager->grid =& $this;

			$this->pager->rowsPer = $this->pageSize;
			$this->pager->setMaxRows($rows);

			$this->pager->update();
			$this->_datasource->setLimit($this->pager->currentRow,$this->pageSize);
		}
	}

	/**
	 * Register a filter for a column
	 */
	function registerFilter($column,$callback,$extra=false) {
		$this->_datasource->registerFilter($column,$callback,$extra);
	}

	/**
	 * Register a template for a column
	 */
	function registerTemplate($column,$template) {
		$this->_datasource->registerTemplate($column,$template);
	}

	/**
	 * Set a label
	 */
	function setLabel($column,$label) {
		$this->_datasource->setLabel($column,$label);
	}
	
	
	/**
	 * Sets the external_id needed by this grid to display.
	 *
	 * If this is called, _exportTo*Link()s will return a valid link for
	 * downloading an exported version of this grid
	 *
	 * @param int
	 */
	function setExternalId($id) {
		$this->_external_id = $id;
	}
	
	
	/**
	 * Sets action to be taken by the export link
	 *
	 * @param string
	 */
	function setExportAction($action) {
		$this->_exportAction = $action;
	}
	
	/**
	 * Creates a link to the export this grid to CSV
	 *
	 * @access private
	 */
	function _exportToCSVLink() {
		if (($this->_datasource->_type != 'html') || $this->hideExportLink) {
			return '';
		}
		$returnUrl = Celini::link($this->_exportAction, 'main', false, 'csv');
		if (!is_null($dsName = $this->_datasource->_internalName)) {
			$returnUrl .= 'ds=' . $dsName . '&gridName=' . $this->name . '&';
		}
		$returnUrl .= 'external_id=' . $this->_external_id . $this->_extraURI;
		$iconPath = Celini::link('displayexporttocsvicon', 'main', false);
		return '<a href="' . $returnUrl . '" title="Export to CSV"><img border="0" width="18" height="18" src="' . $iconPath . '"></a>';
	}
	
	
	/**
	 * Sets the output type that this and the datasource should know what type
	 * of content to return.
	 *
	 * @param string
	 */
	function setOutputType($type) {
		$this->_datasource->_type = $type;
	}
	
	
	/**
	 * Tries to find the external id that is being used
	 *
	 * @access private
	 */
	function _discoverExternalId() {
		if (isset($this->_datasource->external_id) && !is_null($this->_datasource->external_id)) {
			$this->_external_id = $this->_datasource->external_id;
		}
	}
	
	
	/**
	 * Sets extra information to add to the end of the returned export links
	 *
	 * @param string
	 */
	function setExtraURI($string) {
		$this->_extraURI = $string;
	}
	
	
	/**
	 * Returns whether this grid should have hide column links displayed
	 *
	 * @return true
	 */
	function showHideColumns() {
		if (!is_null($this->_allowHideColumns)) {
			return $this->_allowHideColumns;
		}
		
		if (isset($GLOBALS['config']['allowHideColumns'])) {
			return $GLOBALS['config']['allowHideColumns'];
		}
		
		return $this->orderLinks;
	}
}

class sGrid extends cGrid {
        function sGrid(&$datasource, $renderer = false) {
                if (isset($GLOBALS['cGrid']['counter'])) {
                        $GLOBALS['cGrid']['counter']++;
                }
                else {
                        $GLOBALS['cGrid']['counter'] = 0;
                }
                $this->name = $_SERVER['PHP_SELF'].$GLOBALS['cGrid']['counter'];

                if ($renderer === false) {
                        $renderer =& new Grid_Renderer_HTML_scrollable();
                }
                $this->set_datasource($datasource);
                $this->set_renderer($renderer);

                if (isset($GLOBALS['GRIDMODE']) && $GLOBALS['GRIDMODE'] == 'pdf') {
                        $this->updateAttribute('table',0,array('border'=>1,'cellspacing'=>0,'cellpadding'=>2, 'class'=>'sgrid'));
                        $this->orderLinks = false;
                }


                $this->_discoverExternalId();
                if (isset($this->_datasource->hideExportLink)) {
                        $this->hideExportLink = $this->_datasource->hideExportLink;
                }
        }
	function getWidth() {

	}
	function getHeight() {

	}

}

/**
 * An abstract to provide the basic interface for grid renderer objects
 *
 * @abstract
 */
class Grid_Renderer
{
	var $_grid = null;
	var $indexCol = true;
	
	/**
	 * A boolean to cache whether or not prepare() has been called on the grid
	 * object
	 *
	 * @var boolean
	 */
	var $_prepared = false;
	
	/**
	 * This should be overridden by sub-classes to define the type of template
	 * to pull for display.
	 *
	 * @var string
	 * @access protected
	 */
	var $_type = '';
	
	
	/**
	 * Renders the start of the display
	 *
	 * @return string
	 */
	function renderHeader() {
	}
	
	
	/**
	 * Renders the labels for the various columns of data the grid represents
	 *
	 * @return string
	 */
	function renderLabels() {
		
	}
	
	
	/**
	 * Renders the actual data that the grid represents
	 *
	 * @return string
	 */
	function renderBody() {
		$this->_insurePrepared();
	}
	
	
	/**
	 * Renders the closing tags and any footer information for the grid being
	 * displayed
	 *
	 * @return string
	 */
	function renderFooter() {
		
	}
	
	function render() {
		$this->_insurePrepared();
		return $this->renderHeader() .
			$this->renderLabels() .
			$this->renderBody() .
			$this->renderFooter();
	}
	
	
	function _insurePrepared() {
		if (!$this->_prepared) {
			$this->_grid->prepare();
		}
	}
	
	
	/**
	 * Iterates through grid attributes for a given $row and $col and returns
	 * them to be used, if set
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 * @access	protected
	 */
	function _grabAttributes($row, $col) {
		if (!isset($this->_grid->attributes[$row][$col])) {
			return '';
		}
		
		$returnString = '';
		foreach($this->_grid->attributes[$row][$col] as $key => $val) {
			$returnString .= " $key=\"$val\"";
		}
		return $returnString;
	}
}

class Grid_Renderer_HTML_scrollable extends Grid_Renderer_HTML {
        function renderBody() {
                parent::renderBody();

                $ret = $this->_renderTagWithAttributes('tbody','tbody',0, 'class="scrollable"')."\n";

                $row = 0;
                $ds =& $this->_grid->_datasource;
                for($ds->rewind(); $ds->valid(); $ds->next()) {
                        if ($row % 2 == 0) {
                                $this->_grid->attributes[$row]['tr']['class'] = "alt";
                        }
                        else if(isset($this->_grid->attributes[$row]['tr']['class'])){
                                unset($this->_grid->attributes[$row]['tr']['class']);
                        }
                        $ret .= $this->_renderTagWithAttributes('tr',$row,'tr');

                        if ($this->indexCol) {
                                $ret .= $this->_renderTagWithAttributes('td',$row,'indexCol').($row+1)."</td>";
                        }

                        $data = $ds->get();
                        if (is_array($data)) {
                                foreach($ds->getRenderMap() as $index => $key) {
					if (isset($data) && isset($data[$key])) {
                                        $val = $data[$key];
					}
					else {
					$val = '';
					}
                                        if (strlen($val)==0) {
                                                $val = '&nbsp;';
                                        }
                                        $ret .= $this->_renderTagWithAttributes('td',$row,$key)."$val</td>";
                                }
                        }
                        $ret .= "</tr>\n";

                        $row++;
                }
                $ret .= "</tbody>\n";
                return $ret;
        }

}

class Grid_Renderer_HTML extends Grid_Renderer {
	var $name = 0;
	
	function renderHeader() {
		$this->name = str_replace('/','',$this->_grid->name);
		$this->name = str_replace('.','',$this->name);
		$this->name = str_replace(' ','_',$this->name);
		$head =& Celini::HTMLHeadInstance();
		$head->addJs('clniCookie','clniCookie');

		$head->addJs('clniTable','clniTable');
		
		$ret = "<div class='hiddenFieldsList'><ul id='showHidden$this->name'></ul></div>";
		$ret .= $this->_renderTagWithAttributes('table','table',0,"id='clniTable$this->name'");
		if ($this->_grid->caption !== false) {
			$ret .= $this->_renderTagWithAttributes('caption',0,'caption').$this->_grid->caption."</caption>";
		}
		return $ret;
	}
	
	function renderLabels() {
		$ret = $this->_renderTagWithAttributes('thead','thead',0);
		$ret .= $this->_renderTagWithAttributes('tr','labels','tr');

		$ds =& $this->_grid->_datasource;
		if ($this->indexCol) {
			$ret .= $this->_renderTagWithAttributes('th','labels','indexCol')."</th>";
		}

		$labels = $ds->getColumnLabels();
		$colnum=0;
		foreach($ds->getRenderMap() as $index => $key) {
			if ($labels[$key] === false) {
				$ret .= $this->_renderTagWithAttributes('th','labels','')."</th>";
			}
			else {
				if ($this->_grid->orderLinks) {
					$ret .= $this->_renderTagWithAttributes('th','labels',$key).$this->_orderLink($key,$labels[$key],$index).$this->_hideLink($colnum,$labels[$key])."</th>";
				}
				else {
					$ret .= $this->_renderTagWithAttributes('th','labels',$key).$labels[$key].$this->_hideLink($colnum,$labels[$key])."</th>";
				}
			}
			$colnum++;
		}

		$ret .= "</thead>\n";
		return $ret;
	}
	
	function _hideLink($colnum,$text) {
		if($this->_grid->showHideColumns() === false)
			return '';
		$text = strip_tags($text);
		if(strlen($text) > 0) {
			$link = "<a href=\"javascript:tableObj$this->name.hideCol($colnum, '".htmlentities($text)."');\"><img class='trans' src='".Celini::link('stock','Images',false,'media-eject-10.png')."' border=0 width=10 height=10></a>";
		}
		return $link;
	}
	
	
	function _orderLink($col,$text,$order) {
		$curr = "OFF";
		$op = "ASC";
		if ($this->_grid->_datasource->orderRuleExists($col)) {
			$rule = $this->_grid->_datasource->getOrderRule($col);

			$dir = "s_".strtolower($rule[1]).".png";
			$curr = $rule[1];
			if ($rule[1] == "ASC") {
				$op = "DESC";
			}
			else if ($rule[1] == "DESC") {
				$op = "OFF";
			}

			
			if ($rule[1] === "DESC" || $rule[1] === "ASC") {
				$link = "<a href=\"{$this->_grid->link}&ORDER[column]=$col&ORDER[direction]=$op&ORDER[order]=$order\">$text</a><img src='{$this->_grid->imageDir}$dir'>";
			}
		}

		if (!isset($link)) {
			$link = "<a href=\"{$this->_grid->link}&ORDER[column]=$col&ORDER[direction]=$op&ORDER[order]=$order\">$text</a>";
		}

		$left = "<a class='orderLink' href=\"{$this->_grid->link}&MOVE=left&ORDER[column]=$col&ORDER[direction]=$curr&ORDER[order]=".($order-1)."\"><</a>";
		$right = "<a class='orderLink' href=\"{$this->_grid->link}&MOVE=right&ORDER[column]=$col&ORDER[direction]=$curr&ORDER[order]=".($order+1)."\">></a>";
		if ($order == 0) {
			$left = "";
		}
		if ($order == $this->_grid->_datasource->numCols()-1) {
			$right = "";
		}
		return $left.$link.$right;

	}

	function renderBody() {
		parent::renderBody();

		$ret = $this->_renderTagWithAttributes('tbody','tbody',0)."\n";

		$row = 0;
		$ds =& $this->_grid->_datasource;
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			if ($row % 2 == 0) {
				$this->_grid->attributes[$row]['tr']['class'] = "alt";
			}
			else if(isset($this->_grid->attributes[$row]['tr']['class'])){
				unset($this->_grid->attributes[$row]['tr']['class']);
			}
			$ret .= $this->_renderTagWithAttributes('tr',$row,'tr');

			if ($this->indexCol) {
				$ret .= $this->_renderTagWithAttributes('td',$row,'indexCol').($row+1)."</td>";
			}

			$data = $ds->get();
			if (is_array($data)) {
				foreach($ds->getRenderMap() as $index => $key) {
					if (isset($data) && isset($data[$key])) {
					$val = $data[$key];
					}
					else {
					$val = '';
					}
					if (strlen($val)==0) {
						$val = '&nbsp;';
					}
					$ret .= $this->_renderTagWithAttributes('td',$row,$key)."$val</td>";
				}
			}
			$ret .= "</tr>\n";

			$row++;
		}
		$ret .= "</tbody>\n";
		return $ret;
	}

	function renderFooter() {
		$ds = $this->_grid->_datasource;
		$labels = $ds->getColumnLabels();
		$labels = array_values($labels);
		$modifier = $this->_grid->indexCol == false ? 0 : 1;
		
		$out = '';
		if ($this->_grid->showHideColumns()) {
			$out .= "<script type=\"text/javascript\">labels = new Array();\n";
			foreach($labels as $key=>$label) {
				$out.="labels[$key]='".$label."';\n";
			}
			$out.="var tableObj$this->name = new clniTableObj('clniTable$this->name', 'showHidden$this->name',$modifier,labels);\n";
			$out .= "</script><div id='hiddenContent'></div>";
		}
		return "</table>\n".$out;
	}

	function _renderTagWithAttributes($tag,$row,$col,$extra='') {
		return '<' . $tag . $this->_grabAttributes($row, $col) .' '.$extra. '>';
	}
}

class Grid_Renderer_JS extends Grid_Renderer_HTML {
	function renderHeader() {
		return "<div style='position: relative;'>".parent::renderHeader();
	}

	function renderBody() {
		$this->_grid->prepare();

		$ret = $this->_renderTagWithAttributes('tbody','tbody',0)."\n";
		$ret .= "</tbody>\n";
		return $ret;
	}

	function renderFooter() {
		$ret = parent::renderFooter();

		$c = new Controller;
		$base_dir = $c->base_dir;

		$ret .= "<script type='text/javascript' src='{$base_dir}jpspan_server.php?client'></script>\n";
		$tableId = 'clniTable' . $this->name;
		$ret .= "<script type='text/javascript'>
			o_cGrid = new clniTable('{$tableId}',new ".strtolower(get_class($this->_grid->_datasource))."()); 
			o_cGrid.render();</script>\n";
		$ret .= "<div id='{$this->id}_waiting' class='waiting'>Waiting For Data</div></div>";
		$ret .= "<a href='javascript:{}' onclick='o_{$this->id}.up();'>Up</a> <a href='javascript:{}' onclick='o_{$this->id}.down();'>down</a>";
		return $ret;
	}
}


class Grid_Renderer_CSV extends Grid_Renderer 
{
	function renderHeader() {
		return '';
	}
	
	/**
	 * Render the label strings on the first row of the CSV file.
	 *
	 * @return string
	 */
	function renderLabels() {
		$columns = array();
		$ds =& $this->_grid->_datasource;
		$labels = $ds->getColumnLabels();
		foreach($ds->getRenderMap() as $index => $key) {
			if ($labels[$key] === false) {
				continue;
			}
			else {
				$columns[] = "\"{$labels[$key]}\"";
			}
		}

		$ret = implode(',', $columns) . "\n";
		return $ret;
	}
	
	/**
	 * Render the actual CSV rows
	 *
	 * @return string
	 */
	function renderBody() {
		$columns = array();
		$columnRows = array();
		
		$ds =& $this->_grid->_datasource;
		$labels = $ds->getColumnLabels();
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			$data = $ds->get();
			if (is_array($data)) {
				foreach($ds->getRenderMap() as $index => $key) {
					if (!isset($labels[$key]) || $labels[$key] === false) {
						// We only want to display columns that have a label
						continue;
					}
					$columns[] = "\"{$data[$key]}\"";
				}
				
				$columnRows[] = implode(',', $columns);
				$columns = array();
			}
		}
		
		return implode("\n", $columnRows);
	}
	
	function renderFooter() {
		return '';
	}
}

?>
