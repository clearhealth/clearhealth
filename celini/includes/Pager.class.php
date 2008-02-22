<?php
/**
* A pager, handles needed math, building limit clauses and rendering
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
* @package	com.clear-health.celini
*/
class Pager
{
	var $rowsPer = 20;
	var $maxRows = 0;
	var $currentRow = 0;
	var $currentPage = 1;
	var $lastPage = 1;
	var $span = 4;
	var $inGrid = false;
	
	/**
	 * If {@link $inGrid} is true, this will have a {@link cGrid} object in it
	 *
	 * @var object|null
	 */
	var $grid = null;

	/**
	* Set maxRows and update the pager
	*/
	function setMaxRows($rows)
	{
		$this->maxRows = intval($rows);
		$this->lastPage = ceil($this->maxRows/$this->rowsPer);
		if ($this->lastPage == 0) {
			$this->lastPage = 1;
		}
		$this->update();
	}

	/**
	* Update current row from _POST input
	*/
	function update()
	{

		if (isset($GLOBALS['PAGER_PAGE']))
		{
			$this->currentPage = (int)$GLOBALS['PAGER_PAGE'];
			if ($this->currentPage < 0 || $this->currentPage > $this->lastPage) {
				$this->currentPage = 1;
			}
			$this->currentRow = ($this->currentPage - 1)*$this->rowsPer;
		}
	}

	/**
	* Get the limit clause for the sql
	*/
	function getLimit()
	{
		return " limit $this->currentRow, $this->rowsPer ";
	}

	/**
	* Return html for the pager
	*/
	function render()
	{
		$c = new Controller();
		$c->assign("rowsPer",$this->rowsPer);
		$c->assign("currentPage",$this->currentPage);
		$c->assign("minusOne",$this->currentPage-1);
		$c->assign("plusOne",$this->currentPage+1);
		$c->assign("lastPage",$this->lastPage);
		$c->assign('inGrid',$this->inGrid);
		
		if ($this->inGrid) {
			$c->assign_by_ref('grid', $this->grid);
		}

		$tmp = array_keys($_GET);
		if (isset($tmp[0]) && $tmp[0] == "main") {
			array_shift($tmp);
		}

		/*$c->assign("section",array_shift($tmp));
		$c->assign("action",$_GET['action']);
		if (isset($_GET['id'])) {
			$c->assign("id",$_GET['id']);
		}*/

		$qs = "";
		$tmp = explode('&',$_SERVER['QUERY_STRING']);
		foreach($tmp as $val) {
			if (!strstr($val,'PAGER_PAGE')) {
				$qs .= "&$val";
			}
		}
		while (substr($qs,0,1) == '&') {
			$qs = substr($qs,1);
		}
		$c->assign("TOP_ACTION",$_SERVER['PHP_SELF']."?".$qs);
		$c->assign("span",$this->span);
		return $c->fetch(Celini::getTemplatePath("pager.html")); 
		
	}
}
?>
