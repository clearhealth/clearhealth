<?php

/**
 * Provides a default ORDO for creating relationship tables
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.clear-health.celini
 * @abstract
 */
class RelationshipORDO extends ORDataObject
{
	/**#@+
	 * Properties
	 *
	 * @access protected
	 */
	var $child_id = '';
	var $child_type = '';
	/**#@-*/
	
	/**#@+
	 * {@inheritdoc}
	 */
	function RelationshipORDO() {
		parent::ORDataObject();
	}
	/**#@-*/
	
	/**
	 * Returns the ORDO that is related to this 
	 *
	 * @return ORDataObject
	 */
	function &getChild($name) {
		if ($name != 'child') {
			$return =& parent::getChild($name);
			return $return;
		}
		// In case we are given an empty or broken relationship
		if($this->get('child_type')=='' || $this->get('child_id') < 1){
			$return=false;
		} else {
			$return =& Celini::newORDO($this->get('child_type'), $this->get('child_id'));
		}
		return $return;
	}
}

