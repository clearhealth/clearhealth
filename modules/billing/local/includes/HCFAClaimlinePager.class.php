<?php

$loader->requireOnce("ordo/FBClaimline.class.php");

class HCFAClaimlinePager {
	
	var $claimlines = array();
	var $pages = array();
	var $index = 0;
	var $EOF = true;
	var $diagnoses = array();
	
	function HCFAClaimlinePager (&$claimlines) {
		//no claimlines, must be a one page information HCFA
		if (count($claimlines) == 0) {
			$this->pages[0][] = new FBClaimline();
			$this->EOF = false;
			return;
		}
		$this->_build_pages($claimlines);
	}
	
	function rewind() {
		$this->index = 0;	
	}
	
	function next() {
		$ci = $this->index;
		$this->index++;
		if ($this->index > (count($this->pages)-1)) {
			$this->EOF = true;
		}
		if (isset($this->pages[$ci]))	{
			return $this->pages[$ci];
		}
		return false;
	}
	
	function _build_pages($claimlines) {
		$page = 0;
		while (count($claimlines) > 0) {
			//the first page, just merge a claimline, gotta start somewhere
			if (count($this->pages) == 0) {
				$this->pages[$page][] = array_shift($claimlines);
				$cl = $this->pages[$page][0];
				$this->diagnoses = $cl->get("diagnoses");
			
				$this->EOF = false;
			}
			else { 
				$claimline = array_shift($claimlines);
				if (count($this->pages[$page]) < 6 && $this->_can_fit($page, $claimline)) {
					$this->pages[$page][] = $claimline;
				}
				else {
					$page++;
					$this->pages[$page] = array();
					$this->pages[$page][] = $claimline;
				}
			}
		}
	}
	
	function _can_fit($page, $claimline) {
		$diagnoses = array();
		foreach ($this->pages[$page] as $cl) {
			$diagnoses = array_merge($diagnoses, $cl->get("diagnoses"));
		}
		$diagnoses = array_merge($diagnoses, $claimline->get("diagnoses"));
		$diagnoses = array_values(array_unique($diagnoses));
		if  (count($diagnoses) > 4) {
			return false;
		}
		$this->diagnoses = $diagnoses;
		return true;
	}
	
	function get_total_pages() {
		return count($this->pages);	
	}
	
	function get_current_page() {
		return $this->index;	
	}
	
	function get_diagnoses() {
		return $this->diagnoses;	
	}
	function get_diagnosis_pointer($claimline) {
		$pointer = array();
		foreach($claimline->get("diagnoses") as $diagnosis ) {
			$pointer[] = (array_search($diagnosis,$this->diagnoses)+1);
		}
		sort($pointer);
		return implode(",",$pointer);
	}
	
}


?>
