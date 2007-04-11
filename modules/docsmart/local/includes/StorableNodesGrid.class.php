<?php

class StorableNodesGrid extends cGrid
{
	function StorableNodesGrid(&$ds) {
		parent::cGrid($ds);
		$this->orderLinks = false;
	}
}
