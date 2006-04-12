<?php

$loader->requireOnce('includes/Grid.class.php');

class Grid_Renderer_AccountHistory extends Grid_Renderer_HTML {

	function renderBody() {

		$this->_grid->prepare();

		$ret = $this->_renderTagWithAttributes('tbody','tbody',0)."\n";

		$row = 0;
		$ds =& $this->_grid->_datasource;
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			$data = $ds->get();
			$type = isset($data['type']) ? strtolower($data['type']) : '';
			
			if ($type == "claim") {
				$this->_grid->attributes[$row]['tr']['class'] = "alt";				
			}
			else {
				if (isset($this->_grid->attrbiutes[$row]['tr']['class'])) {
					unset($this->_grid->attributes[$row]['tr']['class']);
				}
			}
			$ret .= $this->_renderTagWithAttributes('tr',$row,'tr');

			if ($this->indexCol && $type == "claim") {
				$ret .= $this->_renderTagWithAttributes('td',$row,'indexCol').($row+1)."</td>";
				$row++;
			}
			elseif ($this->indexCol) {
				//$this->_grid->attributes[$row]['td']['indexCol']['class'];
				$ret .= $this->_renderTagWithAttributes('td',$row,'indexCol')."</td>";
			}

			if (is_array($data)) {
				foreach($ds->getRenderMap() as $index => $key) {
					$ret .= $this->_renderTagWithAttributes('td',$row,$key);
					if (isset($data[$key])) {
						$ret .= $data[$key];
					}
					$ret .= '</td>';
				}
			}
			$ret .= "</tr>\n";
			
		}
		$ret .= "</tbody>\n";
		return $ret;
	}

}

?>