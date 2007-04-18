<?php
$loader->requireOnce('ordo/RelationshipORDO.abstract.php');

/**
 * Provides a default ORDO for creating relationship tables
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.celini
 */
class Relationship extends RelationshipORDO
{
	/**#@+
	 * Properties
	 *
	 * @access protected
	 */
	var $relationship_id = '';
	var $parent_id = '';
	var $parent_type = '';
	/**#@-*/
	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_table = 'relationship';
	var $_key = 'relationship_id';
	
	function Relationship() {
		parent::RelationshipORDO();
	}
	/**#@-*/
	
	
	/**
	 * Returns a related of this {@link Relationship}.
	 *
	 * Valid children $name values on a {@link Relationship} are 'child' and 'parent'.
	 *
	 * @param  string
	 * @return ORDataObject
	 */
	function &getChild($name = 'child') {
		assert('$name == "child" || $name == "parent"');
		
		if ($name != 'parent') {
			$child =& parent::getChild($name);
			return $child;
		}
		if($this->get('parent_type')!='' && $this->get('parent_id') > 0){
			$child =& Celini::newORDO($this->get('parent_type'), $this->get('parent_id'));
		} else {
			$child=false;
		}
		return $child;
	}
}

