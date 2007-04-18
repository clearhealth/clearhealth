<?php

/**
 * Takes a {@link refPatientEligibility} status and changes all "open" {@link refRequest} to the
 * status of that object.
 *
 * @see refPatientEligibility
 */
class refEligibilityForRequestChanger
{
	/**#@+ 
	 * @access private
	 */
	var $_patientEligibility = null;
	var $_collection = null;
	/**#@-*/
	
	
	/**
	 * Handle initialization
	 *
	 * @param refPatientEligibility
	 */
	function refEligibilityForRequestChanger(&$patientEligibility) {
		assert('is_a($patientEligibility, "refPatientEligibility")');
		
		$this->_patientEligibility =& $patientEligibility;
		$this->_initCollection();
	}
	
	
	/**
	 * Handle initializing the {@link ORDOCollection}
	 *
	 * @access private
	 */
	function _initCollection() {
		global $loader;
		$db = new clniDB();
		$qPatientId = $db->quote($this->_patientEligibility->get('patient_id'));
		$qProgramId = $db->quote($this->_patientEligibility->get('refprogram_id'));
		$loader->requireOnce('includes/ORDO/ORDOFinder.class.php'); 
		$finder =& new ORDOFinder('refRequest', "patient_id = {$qPatientId} AND refprogram_id = {$qProgramId}");
		$this->_collection =& $finder->find();
	}
	
	
	/**
	 * Execute the changes on each of the matching {@link refRequest}s.
	 */
	function doChange() {
		//echo "Changing " . $this->_collection->count() . " of refRequests<br />";
		for ($this->_collection->rewind(); $this->_collection->valid(); $this->_collection->next()) {
			$request =& $this->_collection->current();
			///echo "Changing refRequest #" . $request->get('id') . ' to ' . $this->_patientEligibility->get('eligibility') . '<br />';
			$request->set('eligibility', $this->_patientEligibility->get('eligibility'));
			$request->set('eligible_thru', $this->_patientEligibility->get('eligible_thru'));
			$this->_changeStatus($request);
			$request->persist();
		}
	}
	
	
	/**
	 * Handle changing the {@link refStatus}
	 *
	 * @param  refRequest
	 * @access private
	 */
	function _changeStatus(&$request) {
		if (preg_match('/Appointment/', $request->value('refStatus'))) {
			return;
		}
		$em =& Celini::enumManagerInstance();
		switch ($request->value('eligibility')) {
			case 'Eligible' :
			case 'Not Required' :
				$request->set('refStatus', $em->lookupKey('refStatus', 'Requested'));
				break;
			case 'In-Eligible' :
			case 'No Status' :
				$request->set('refStatus', $em->lookupKey('refStatus', 'Requested / Eligibility Pending'));
				break;
		}
	}
}

?>
