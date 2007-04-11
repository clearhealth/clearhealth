<?php

/**
 * Collection of the ORDO elements
 *
 * @todo Determine why there is an ORDOList file here.
 */
class ORDOList {
	
	/**
	 * ORDO elements array
	 *
	 * @var array
	 */
	var $elements = array();
	
	/**
	 * Class name of the ORDO element which is 
	 * avaliable in this collection
	 *
	 * @var string
	 */
	var $className = '';
	
	/**
	 * Constructr, defines className for the each element of the collection
	 *
	 * @param string $className
	 * @return ORDOList
	 */
	function ORDOList($className) {
		$this->className = $className;
	}
	
	/**
	 * Add element to the collection by key or just push it to the end if key is undefined
	 *
	 * @param ORDataObject $ordo
	 * @param string $key
	 * @return string
	 */
	function add($ordo, $key = null) {
		if(!is_a($ordo, $this->className)) {
			return false;
		}
		if(isset($key)) {
			$this->elements[$key] = $ordo;
			return $key;
		}else{
			array_push($this->elements, $ordo);
			return count($this->elements)-1;
		}	
	}
	
	/**
	 * Remo element from the collection by key
	 *
	 * @param string $key
	 */
	function remove($key) {
		if(isset($this->elements[$key])) {
			unset($this->elements[$key]);	
		}
	}

	/**
	 * Return collection as array with each element converted to the array too
	 *
	 * @return array
	 */
	function toArray() {
		$list = array();
		foreach($this->elements as $key => $ordo) {
			if(!is_a($ordo, $this->className)) {
				continue;
			}			
			$list[$key] = $ordo->toArray();
		}
		return $list;
	}
}

?>