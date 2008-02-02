<?php
$GLOBALS['loader']->requireOnce('lib/PEAR/Image/Graph.php');

/**
 * Object Relational Persistence Mapping Class for table: graph_definition
 *
 * @package	com.clearhealth.controllers
 * @author	ClearHealth Inc.
 */
class GraphDefinition extends ORDataObject {

	/**#@+
	 * Fields of table: graph_definition mapped to class members
	 */
	var $graph_definition_id		= '';
	var $report_id		= '';
	var $width		= 350;
	var $height		= 200;
	var $font_size		= 8;
	var $font_type		= 'ttf_font';
	var $font_file		= '/fonts/vera.ttf';
	var $title		= '';
	var $title_size		= 10;
	var $canvas		= 'png';
	var $plot_area		= '';
	var $graph_type		= '';
	var $querylinks		= '';
	var $data		= '';
	var $titles		= '';
	var $filename 		= '';
	var $Graph;
	var $Datasets = array();
	var $Support = array();
	var $colors = array('red','green','blue','orange','red','gray','lightblue','maroon','peach','marine','brown');
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'graph_definition';

	/**
	 * Primary Key
	 */
	var $_key = 'graph_definition_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'GraphDefinition';

	/**
	 * Handle instantiation
	 */
	function __construct() {
		parent::ORDataObject();
                $this->font_file = CELINI_ROOT.$this->font_file;
		$this->Graph =& Image_Graph::factory('graph', array(
                                array(
                                        'width'=>$this->width,
                                        'height'=>$this->height,
                                        'canvas'=>$this->canvas,
                                        'antialias' => 'native',
                                ))
                        );
                $this->Support['Font'] =& $this->Graph->addNew($this->font_type, $this->font_file);
                $this->Support['Font']->setSize($this->font_size);
                $this->Graph->setFont($this->Support['Font']);

                $this->layout();

	}


        function layout() {
                $this->Support['Plotarea'] = Image_Graph::factory('plotarea');
                $this->Support['Legend'] = Image_Graph::factory('legend');
                $this->Support['Legend']->setBorderColor('black');
                $this->Graph->add(
                        Image_Graph::vertical(
                                Image_Graph::factory('title', array($this->title, $this->title_size)),
                                        Image_Graph::vertical(
                                                $this->Support['Plotarea'],
                                                $this->Support['Legend'],
                                                80
                                        ),
                                        5
                        )
                );
                $this->Support['Legend']->setPlotarea($this->Support['Plotarea']);
        }
	
	function set_querylinks($qlinks = '') {
		if (!is_array($qlinks)) {
		$this->querylinks = explode(":",$qlinks);
		}
		else {
		$this->querylinks = $qlinks;
		}
	}

	function getGraphsByReportId($reportId) {
		$labels = array(
                        'title'   => 'Title',
                        'width'          => 'Width',
                        'height'          => 'Height',
                        'plot_area' => 'Plot Area',
                        'graph_type' => 'Type',
                        'querylinks' => 'Queries'
                );
		$query = array(
			'cols' => "*",
			'from' => "graph_definition ",
			'where' => 'report_id = ' . (int)$reportId,
			);
		$db = Celini::dBInstance();
                // build the datasource from the query
                $ds =& new Datasource_sql();
                $ds->setup($db, $query, $labels);
                $grid =& new cGrid($ds);
		return $grid;
	}
	
	function getAllGraphDefsForReport($reportId) {
		$gDefs = array();
		$db = Celini::DbInstance();
		$sql = "select * from graph_definition gd where report_id = " . (int)$reportId;
		$arr = $db->getAll($sql);
		foreach ($arr as $gdrow) {
			$gd = new GraphDefinition();
			$gd->populateArray($gdrow);
			$gDefs[] = $gd;
		}
		return $gDefs;
	}	


	function writeGraph($filename) {
		$this->filename = $filename;
		$function = $this->graph_type . "Graph";
		$this->$function();
	}

	function lineGridGraph() {
                for ($i=0;$i<count($this->data); $i++) {
                        $arr = $this->data[$i];
                        $this->Datasets[$i] =& Image_Graph::factory('dataset');
                        $this->Datasets[$i]->setName($arr[0]['title']);
                        for($j=0; $j < count($arr); $j++) {
                                $this->Datasets[$i]->addPoint(date('m/d/y',strtotime($arr[$j]['date'])),$arr[$j]['value']);
                        }
                }

		$Grid =& $this->Support['Plotarea']->addNew('line_grid', null, IMAGE_GRAPH_AXIS_X);
                $Grid->setLineColor('lightgray@0.1');
                $Grid =& $this->Support['Plotarea']->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
                $Grid->setLineColor('lightgray@0.1');
		foreach(array_keys($this->Datasets) as $key) {
			$this->Support['plots'][$key] =& $this->Support['Plotarea']->addNew('line',$this->Datasets[$key]);
			$this->Support['plots'][$key]->setLineColor($this->colors[$key]);
			$this->Support['plots'][$key]->setLineColor($this->colors[$key]);
			
		}

		$this->Support['AxisX'] =& $this->Support['Plotarea']->getAxis(IMAGE_GRAPH_AXIS_X);
		$font =$this->Support['Font'];
		$font->setAngle(0);
		$this->Support['AxisX']->setTitle('Date',$font);
			$AxisX = $this->Support['Plotarea']->getAxis(IMAGE_GRAPH_AXIS_X);
			$AxisX->setLabelInterval(1); 
			$AxisX->setFontAngle('vertical'); 
		
		$this->Graph->done(array("filename" => $this->filename));
	}
}

?>
