<?php
class M_Claim extends Manager {
	
	function process_setfilter() {
		if (!isset($_POST['filter'])) { 
			return;
		}
		
		$realFilterValues = $_POST['filter'];
		foreach ($realFilterValues AS $filterName => $filterValue) {
			// Perform any transformation on the filter values right after they are submitted
			switch ($filterName) {
				case 'provider' :
					$explodedName = explode(',', $filterValue);
					if (count($explodedName) > 1) {
						$realFilterValues['provider_lastName'] = trim($explodedName[0]);
						$realFilterValues['provider_firstName'] = trim($explodedName[1]);
					}
					else {
						$realFilterValues['provider_lastName'] = trim($explodedName[0]);
						$realFilterValues['provider_firstName'] = trim($explodedName[0]);
					}
					break;
				case 'name' :
					$explodedName = explode(',', $filterValue);
					if (count($explodedName) > 1) {
						$realFilterValues['patient_lastName'] = trim($explodedName[0]);
						$realFilterValues['patient_firstName'] = trim($explodedName[1]);
					}
					else {
						$realFilterValues['patient_lastName'] = trim($explodedName[0]);
						$realFilterValues['patient_firstName'] = trim($explodedName[0]);
					}
					break;
			}
			
			// check for date objects
			$d =& DateObject::create($filterValue);
			if ($d->isValid()) {
				if ($d->toUSA() == $filterValue || $d->toISO() == $filterValue) {
					$realFilterValues[$filterName] = $d->toISO();
				}
			}
		}
		
		$_SESSION['freeb2']['filters']['c_claim'] = $realFilterValues;
		$this->controller->filters = $realFilterValues;
		
		$cleanedPost =& Celini::filteredPost();
		$this->controller->displayFilters = $cleanedPost->get('filter');
	}
}
?>
