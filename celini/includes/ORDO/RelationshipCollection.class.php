<?php

/**
 * A specialized {@link ORDOCollection} for returning related ordos.
 */
class RelationshipCollection extends ORDOCollection
{
	var $_relationship = null;
	var $_relatedType = null;
	
	/**
	 * Handle instantiation
	 *
	 * @param  string
	 * @param  object
	 * @param  string
	 * @see ORDOCollection
	 */
	function RelationshipCollection($relatedType, &$results, $relationship = 'child') {
		$this->_relatedType = $relatedType;
		parent::ORDOCollection('Relationship', $results);
		$this->_relationship = $relationship;
	}
	
	
	/**
	 * Returns the related ordo
	 *
	 * @return ORDataObject
	 */
	function &current() {
		$relationship =& parent::current();
		$ordo =& Celini::newORDO(
			$relationship->get($this->_relationship . '_type'),
			$relationship->get($this->_relationship . '_id')
		);
		
		return $ordo;
	}
}

