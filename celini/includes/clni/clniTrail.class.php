<?php
/**
 * Trail of urls the user has visted
 *
 * Use celini::trailInstance() to get the default instance to work with
 *
 * @package com.uversainc.celini
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class clniTrail {

	var $items = array();
	var $currentPage;
	var $itemToKeep = 20;

	var $_currentKey = false;

	/**
	 * Controllers that we skip when running last item
	 */
	var $skip = array('ie7','ajax','css','images');
	var $skipActions = array();

	function addCurrentPage() {
		$controller = Celini::getCurrentController();
		$action = Celini::getCurrentAction();

		$SERVER = new clniFilter('SERVER');
		$url = $SERVER->get('PHP_SELF');
		$qstring = $SERVER->get('QUERY_STRING');

		$this->currentPage = new clniTrailItem($controller,$action,$url,$qstring,time());
		array_unshift($this->items,$this->currentPage);

		if (count($this->items) > $this->itemToKeep) {
			array_pop($this->items);
		}
	}

	/**
	 * Get a clniTrailItem for the last real page that was viewed, this use $this->skip to not show items that were request to none pages
	 *
	 * This rewinds the objects and then returns it to its previous state
	 */
	function lastItem() {
		$key = $this->key();

		$this->rewind();
		$this->next(); // go back past the current item
		$item = $this->current();
		while( in_array(strtolower($item->controller),$this->skip) || in_array(strtolower($item->action),$this->skipActions) ) {
			$this->next();
			if ($this->valid()) {
				$item = $this->current();
			}
			else {
				$item = $this->currentPage;
				break;
			}
		}

		$this->seek($key);
		return $item;
	}

	// iterator api

	/**
	 * Return current item
	 */
	function current() {
		return $this->items[$this->_currentKey];
	}

	/**
	 * Return the current key
	 */
	function key() {
		return $this->_currentKey;
	}

	/**
	 * Move to the next item
	 */
	function next() {
		$this->_currentKey++;
	}

	/**
	 * Rewind the array back to the start
	 *
	 * Items are stored in revese order, so rewinding means you'll be starting on the most recent url
	 */
	function rewind() {
		$this->_currentKey = 0;
	}

	/**
	 * Is the current index valid
	 */
	function valid() {
		if (isset($this->items[$this->_currentKey])) {
			return true;
		}
		return false;
	}

	/**
	 * Move to any index in the items
	 */
	function seek($pos) {
		$this->_currentKey = $pos;
	}
}

/**
 * A specific url on the trail
 *
 * If go a route where we have a ControllerAction class this object should extend that (or just be replaced by that)
 */
class clniTrailItem {
	var $controller;
	var $action;
	var $url;
	var $queryString;
	var $timestamp;

	function clniTrailItem($controller,$action,$url,$queryString,$timestamp) {
		$this->controller = $controller;
		$this->action = $action;
		$this->url = $url;
		$this->queryString = $queryString;
		$this->timestamp = $timestamp;
	}

	function link() {
		return $this->url.'?'.$this->queryString;
	}
}
?>
