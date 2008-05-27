<?php

$loader->requireOnce('includes/renderer_drivers/ClaimRenderer_AbstractDriver.abstract.php');

/**
 * The default X12 driver for a claim renderer
 */
class ClaimRenderer_X12Driver extends ClaimRenderer_AbstractDriver
{
	var $total_x12_segments;
	
	/**
	 * {@inheritdoc}
	 */
	function ClaimRenderer_X12Driver(&$renderer) {
		$this->ClaimRenderer_AbstractDriver($renderer);
	}
	
	function render() {
		if (!$this->_hasCorrectStatus()) {
			return false;
		}
		
		// $all_claimresults = "There are ".count($batch)." claims in this batch"; //For testing...
		$all_claimresults = '';
		//printf('<pre>%s</pre>', var_export($this->_batch , true));
		if (count($this->_batch) > 0) {
			foreach ($this->_batch as $claim_id => $status) {
				if ($status['on'] != 1) {
					continue;
				}
				
				$c = Celini::newORDO('FBClaim', $claim_id);
				$all_claimresults .= $this->_renderClaimBlock($c);
				unset($c);
			}
		}
		else {
			$all_claimresults = $this->_renderClaimBlock($this->_claim);
		}
		
		return $all_claimresults;
	}
	
	/**
	 * @param  FBClaim
	 * @access private
	 * @return string
	 */
	function _renderClaimBlock(&$claim) {
		//needed by the header
		$this->_view->assign_by_ref("claim", $claim);
		$this->_view->assign_by_ref("practice", $claim->childEntity("FBPractice"));
		
		//needed by 1000A
		$this->_view->assign_by_ref("billing_contact", $claim->childEntity("FBBillingContact"));
		
		//needed by 1000B
		$this->_view->assign_by_ref("payer", $claim->childEntity("FBPayer"));
		$this->_view->assign_by_ref("payers", $claim->childEntities("FBPayer"));
		
		
		$this->_view->assign_by_ref("patient", $claim->childEntity("FBPatient"));
		$this->_view->assign_by_ref("provider", $claim->childEntity("FBProvider"));
		$this->_view->assign_by_ref("referring_provider", $claim->childEntity("FBReferringProvider"));
		$this->_view->assign_by_ref("supervising_provider", $claim->childEntity("FBSupervisingProvider"));
		$this->_view->assign_by_ref("subscriber", $claim->childEntity("FBSubscriber"));
		$this->_view->assign_by_ref("subscribers", $claim->childEntities("FBSubscriber"));
		$this->_view->assign_by_ref("responsible_party", $claim->childEntity("FBResponsibleParty"));
		$this->_view->assign_by_ref("treating_facility", $claim->childEntity("FBTreatingFacility"));
		$this->_view->assign_by_ref("clearing_house", $claim->childEntity("FBClearingHouse"));
		$this->_view->assign_by_ref("claim_lines", $claim->childEntities("FBClaimline"));
		$this->_view->assign_by_ref('billing_facility', $claim->childEntity('FBBillingFacility'));

		
		// What is this doing?
		$claim->set("format",$this->_format);
		
		$claimresult = "";
		$claimresult = $this->_view->fetch($this->_determineTemplateName());
		$total_x12_segments = $this->_helper->postfilterSegmentCount($claimresult);
		$claimresult = $this->_helper->postfilterSEReplacement($total_x12_segments, $claimresult);//calculate the SE field
		unset($claim);
		unset($this->view->_tpl_vars['claim']);
		unset($this->view->_tpl_vars['practice']);
		unset($this->view->_tpl_vars['payer']);
		unset($this->view->_tpl_vars['payers']);
		unset($this->view->_tpl_vars['patient']);
		unset($this->view->_tpl_vars['provider']);
		unset($this->view->_tpl_vars['referring_provider']);
		unset($this->view->_tpl_vars['supervising_provider']);
		unset($this->view->_tpl_vars['subscriber']);
		unset($this->view->_tpl_vars['subscribers']);
		unset($this->view->_tpl_vars['responsible_party']);
		unset($this->view->_tpl_vars['treating_facility']);
		unset($this->view->_tpl_vars['clearing_house']);
		unset($this->view->_tpl_vars['claim_lines']);
		unset($this->view->_tpl_vars['billing_facility']);
		return $claimresult;
	}
	
	/**
	 * @access private
	 */
	function _determineTemplateName() {
		$cleanFormatName = preg_replace("/[^A-Za-z0-9_]/","",$this->_format);
		return Celini::getTemplatePath("/variations/{$cleanFormatName}/{$cleanFormatName}_header.html");
	}
}
