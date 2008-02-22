<?php
/**
 * Renderer for an active grid
 *
 * @package com.clear-health.celini
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class clniActiveGrid {
	var $ds;
	var $stubName = false;

	var $templates = array(
		'loading' => '<tr class="loading"><td class="gridLoading" colspan=2>Loading ...</td></tr>'
		);
	var $header = false;

	var $gridName;
	var $gridRows = 20;
	var $preloadRows = false;
	var $gridWidth = '400px';


	function clniActiveGrid($name,&$ds) {
		$this->gridName = $name;
		$this->ds =& $ds;

		$head =& Celini::HTMLHeadInstance();
		$head->addExternalCss('activegrid');
	}

	function prepare() {
		if (!$this->stubName) {
			$this->stubName = $this->ds->_internalName;
		}
		if (!$this->preloadRows) {
			$this->preloadRows = $this->gridRows*2;
		}

		if (!isset($this->templates['normal'])) {
			$map = $this->ds->getRenderMap();
			$normal = '<tr class="normal">';
			foreach($map as $var) {
				$normal .= '<td>{$'.$var.'}</td>';
			}
			$normal .= "</tr>\n";
			$this->templates['normal'] = $normal;
		}
		if ($this->header === false) {
			$map = $this->ds->getRenderMap();
			$labels = $this->ds->getColumnLabels();
			$header = '<tr>';
			foreach($map as $var) {
				$header .= '<th>'.$labels[$var].'</th>';
			}
			$header .= "</tr>\n";
			$this->header = $header;
		}
	}

	function render() {
		$this->prepare();

		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries['scriptaculous'] = array('scriptaculous');
		$ajax->jsLibraries['activeGrid'] = array('clniGrid');
		$ajax->jsLibraries['scrollbar'] = array('scrollbar');
		$ajax->stubs[] = $this->stubName;


		$view =& new clniView();
		$view->assign('dsSetup',$this->ds->setupJs($this->gridName.'_dsSetup',$this->preloadRows));

		$view->assign('gridName',$this->gridName);
		$view->assign('gridRows',$this->gridRows);
		$view->assign('gridWidth',$this->gridWidth);
		$view->assign('stubName',$this->stubName);
		$view->assign('header',$this->header);

		$base_dir = dirname($_SERVER['SCRIPT_NAME']);
		if ($base_dir == "/") {
			$base_dir = "/";
		}
		else {
			$base_dir .= "/";
		}
		$view->assign('base_dir',$base_dir);

		$out = '';
		foreach($this->templates as $template) {
			$out .= $template;
		}
		$view->assign('template',$out);

		return $view->fetch(Celini::getTemplatePath('activeGrid.html'));
	}
}
?>
