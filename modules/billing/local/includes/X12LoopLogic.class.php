<?php

class X12LoopLogic 
{
	var $batch = array();
	
	function X12LoopLogic ($batch) {
		$this->batch = $batch;
	}

	function get_provider_list($claim_list){
		if( $claim_list == null || $claim_list == 0 ) {
			$claim_list = $this->batch;
		}
		
		//use the main array for blank args...
		$provider_list = array();

		foreach ($claim_list as $claim_id => $claim_array) {
			$c =& ORDataObject::factory('FBClaim',$claim_id); //newway
			$p = $c->childEntity("FBProvider");	
			$provider_list[] = $p->get("identifier");//add it to the list
		}
		
		$provider_list = array_unique($provider_list);//remove duplicated
		return $provider_list;
	}

	function claims_from_provider($provider_id, $claim_list) {
		//echo "<br>inside claims from provider $provider_id <br>";
		
		if( $claim_list == null || $claim_list == 0 ) {
			$claim_list = $this->batch;
		}
		
		//use the main array for blank args...
		$resulting_claims = array();	
	
		foreach ($claim_list as $claim_id => $claim_array) {
			$c =& ORDataObject::factory('FBClaim',$claim_id); //newway
			$p = $c->childEntity("FBProvider");
			$this_identifier = $p->get("identifier");
			//echo "<br>This identifier $this_identifier searching for $provider_id <br>";
			if ($this_identifier == $provider_id) {
				$resulting_claims[$claim_id] = $claim_array;
			}
		}
		return $resulting_claims;
	}

	function &get_patient($claim_id){
		$c =& ORDataObject::factory('FBClaim',$claim_id);
		return $c->childEntity("FBPatient",$claim_id);	
	}
	
	function &get_provider($claim_id) {
		return $return =& $this->_getChild('Provider', $claim_id);
	}
	
	function &get_referring_provider($claim_id) {
		return $return =& $this->_getChild('ReferringProvider', $claim_id);
	}

	function &get_supervising_provider($claim_id) {
		return $return =& $this->_getChild('SupervisingProvider', $claim_id);
	}
	
	function &get_subscriber($claim_id) {
		return $return =& $this->_getChild('Subscriber', $claim_id);
	}
	
	function &get_responsible_party($claim_id) {
		return $return =& $this->_getChild('ResponsibleParty', $claim_id);
	}
	
	function &get_treating_facility($claim_id) {
		return $return =& $this->_getChild('TreatingFacility', $claim_id);
	}
	
	function &get_clearing_house($claim_id) {
		return $return =& $this->_getChild('ClearingHouse', $claim_id);
	}

	
	function &_getChild($name, $claim_id) {
		$c =& Celini::newORDO('FBClaim', $claim_id);
		return $c->childEntity('FB' . $name, $claim_id);
	}
}

?>
