<?php

$loader->requireOnce('includes/renderer_drivers/ClaimRenderer_AbstractDriver.abstract.php');

/**
 * The X12 driver for a claim with nested elements
 */
class ClaimRenderer_NestedDriver extends ClaimRenderer_AbstractDriver
{
	var $total_x12_segments;
	
	/**
	 * {@inheritdoc}
	 */
	function ClaimRenderer_NestedDriver(&$renderer) {
		$this->ClaimRenderer_AbstractDriver($renderer);
	}
	
	function render() {
		if (!$this->_hasCorrectStatus()) {
			return false;
		}
		
		// $all_claimresults = "There are ".count($batch)." claims in this batch"; //For testing... 
		$all_claimresults = '';

		//needed by the header
		$this->_view->assign_by_ref("claim", $this->_claim);
		$this->_view->assign_by_ref("practice", $this->_claim->childEntity("FBPractice"));
		
		//needed by 1000A
		$this->_view->assign_by_ref("billing_contact", $this->_claim->childEntity("FBBillingContact"));
		
		//needed by 1000B
		$this->_view->assign_by_ref("payer", $this->_claim->childEntity("FBPayer"));
		$this->_view->assign_by_ref("payers", $this->_claim->childEntities("FBPayer"));


		$this->_view->assign_by_ref("patient", $this->_claim->childEntity("FBPatient"));
		$this->_view->assign_by_ref("provider", $this->_claim->childEntity("FBProvider"));
		$this->_view->assign_by_ref("referring_provider", $this->_claim->childEntity("FBReferringProvider"));
		$this->_view->assign_by_ref("supervising_provider", $this->_claim->childEntity("FBSupervisingProvider"));
		$this->_view->assign_by_ref("subscriber", $this->_claim->childEntity("FBSubscriber"));
		$this->_view->assign_by_ref("subscribers", $this->_claim->childEntities("FBSubscriber"));
		$this->_view->assign_by_ref("responsible_party", $this->_claim->childEntity("FBResponsibleParty"));
		$this->_view->assign_by_ref("treating_facility", $this->_claim->childEntity("FBTreatingFacility"));
		$this->_view->assign_by_ref("clearing_house", $this->_claim->childEntity("FBClearingHouse"));
		$this->_view->assign_by_ref("claim_lines", $this->_claim->childEntities("FBClaimline"));
		
		$this->_view->assign('loopedData', $this->_createLoopedData());

		
		// What is this doing?
		$this->_claim->set("format",$this->_format);
		
		$claimresult = "";
		$claimresult = $this->_view->fetch($this->_determineTemplateName());
		$total_x12_segments = $this->_helper->postfilterSegmentCount($claimresult);
		$claimresult = $this->_helper->postfilterSEReplacement($total_x12_segments, $claimresult);//calculate the SE field
		return $claimresult;
	}
	
	function _createLoopedData() {
		$hl_count = 0;
		$returnString = '';
		$providerList = $this->_createListOfProviders();
		
		foreach ($providerList as $provider) {
			$hl_count++;
			$parentHLValue = $hl_count;
			
			$providerView =& $this->_newView();
			$providerView->assign('hl_count', $hl_count);
			$providerView->assign_by_ref('practice', $this->_claim->childEntity('FBPractice'));
			$providerView->assign_by_ref('treating_facility', $this->_claim->childEntity('FBTreatingFacility'));
			$providerView->assign_by_ref("billing_contact", $this->_claim->childEntity("FBBillingContact"));
			$providerView->assign_by_ref('provider', $this->_claim->childEntity('FBProvider'));
			$returnString .= $providerView->fetch($this->_determineTemplateName('x12_2000A'));
			
			$providerClaimList =& $this->_createListOfClaimsByProvider($provider);
			foreach ($providerClaimList as $providerClaim) {
				$hl_count++;
				
				$providerClaimView =& $this->_newView();
				$providerClaimView->assign('hl_count', $hl_count);
				$providerClaimView->assign('hl_2000A', $parentHLValue); // need to change assigned name
				
				$claimElementsSingle = array(	
					'payer' => 'FBPayer',
					'practice' => 'FBPractice',
					'patient' => 'FBPatient',
					'provider' => 'FBProvider',
					'referring_provider' => 'FBReferringProvider',
					'supervising_provider' => 'FBSupervisingProvider',
					'subscriber' => 'FBSubscriber',
					'responsible_party' => 'FBResponsibleParty');
				
				$claimElementsArray = array(	
					'payers' => 'FBPayer',
					'subscribers' => 'FBSubscriber');
				
				// setup and include 2000B
				$twoThousandBView =& $this->_newView();
				foreach ($claimElementsSingle as $assignTo => $ordoName) {
					$$assignTo =& $providerClaim->childEntity($ordoName);
					$twoThousandBView->assign_by_ref($assignTo, $$assignTo);
				}
				foreach ($claimElementsArray as $assignTo => $ordoName) {
					$$assignTo =& $providerClaim->childEntities($ordoName);
					$twoThousandBView->assign_by_ref($assignTo, $$assignTo);
				}
				$twoThousandBView->assign('hl_parent', $parentHLValue);
				$twoThousandBView->assign('hl_count', $hl_count);
				$returnString .= $twoThousandBView->fetch($this->_determineTemplateName('x12_2000B'));
				
				
				foreach ($claimElementsSingle as $assignTo => $ordoName) {
					$$assignTo =& $providerClaim->childEntity($ordoName);
					$providerClaimView->assign_by_ref($assignTo, $$assignTo);
				}
				foreach ($claimElementsArray as $assignTo => $ordoName) {
					$$assignTo =& $providerClaim->childEntities($ordoName);
					$providerClaimView->assign_by_ref($assignTo, $$assignTo);
				}
				$providerClaimView->assign_by_ref('claim_lines', $providerClaim->childEntities('FBClaimline'));
				$providerClaimView->assign_by_ref('claim', $providerClaim);
				$providerClaimView->assign_by_ref('treating_facility', $providerClaim->childEntity('FBTreatingFacility'));
				$providerClaimView->assign_by_ref("clearing_house", $providerClaim->childEntity("FBClearingHouse"));
				$providerClaimView->assign_by_ref("practice", $providerClaim->childEntity("FBPractice"));
				$providerClaimView->assign_by_ref('billing_facility', $providerClaim->childEntity('FBBillingFacility'));
				$providerClaimView->assign_by_ref('payers', $providerClaim->childEntities('FBPayer'));
				

				$returnString .= $providerClaimView->fetch($this->_determineTemplateName('x12_2300'));
			}
		}
		
		return $returnString;
	}
	
	function &_createListOfClaimsByProvider(&$provider) {
		$returnArray = array();
		foreach (array_keys($this->_batch) as $claim_id) {
			$claim =& Celini::newORDO('FBClaim', $claim_id);
			$claimProvider =& $claim->childEntity('FBProvider');
			if ($claimProvider->get('id') == $provider->get('id')) {
				$returnArray[] =& $claim;
			}
		}
		
		return $returnArray;
	}
	
	
	/**
	 * @todo refractor this into an FBProviderList iterator that allows lazy
	 *   loading instead of having to take on all the construct cost at the
	 *   start.
	 */
	function _createListOfProviders() {
		static $providerList = array();
		if (count($providerList) <= 0) {
			foreach (array_keys($this->_batch) as $claim_id) {
				$claim =& Celini::newORDO('FBClaim', $claim_id);
				$provider =& $claim->childEntity('FBProvider');
				if (!isset($providerList[$provider->get('id')])) {
					$providerList[] =& $provider;
				}
			}
		}
		return $providerList;
	}
	
	
	/**
	 * @access private
	 */
	function _determineTemplateName($name = null) {
		$cleanFormatName = preg_replace("/[^A-Za-z0-9_-]/","",$this->_format);
		$path = '/variations/' . $cleanFormatName . '/';
		$path .= is_null($name) ?
			$cleanFormatName . '_header.html' :
			$name . '.html';
		return Celini::getTemplatePath($path);
	}
}
