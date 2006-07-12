<?php
$loader->requireOnce('includes/Grid.class.php');
class Grid_Renderer_AccountHistory_CSV extends Grid_Renderer_CSV {
	/**
	 * Render the actual CSV rows
	 *
	 * @return string
	 */
	function renderBody() {
		$this->_grid->prepare();
		$this->_grid->_datasource->claims->_type = 'csv';

		$columns = array();
		$columnRows = array();
		
		$ds =& $this->_grid->_datasource;
		$labels = $ds->getColumnLabels();


		$map = $ds->getRenderMap();
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			$data = $ds->get();

			if (is_array($data)) {
				foreach($map as $index => $key) {
					if (!isset($labels[$key]) || $labels[$key] === false) {
						// We only want to display columns that have a label
						continue;
					}
					if (isset($data[$key])) {
						$columns[] = '"'.strip_tags($data[$key]).'"';
					}
					else {
						$columns[] = '""';
					}
				}
				
				$columnRows[] = implode(',', $columns);
				$columns = array();
			}
		}
		
		return implode("\n", $columnRows);
	}
}
?>
