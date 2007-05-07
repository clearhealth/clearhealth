<?php

class C_TabState extends Controller {
	
	function actionSelectTab($tabKey) {
		return $this->selectTab($tabKey);
	}

	function selectTab($tabKey) {
		$tabKey = preg_replace('/[^A-Za-z0-9\/]*/','',$tabKey);
		$tabKeys = split('/',$tabKey);
		$_SESSION['_clniSession']['here'] = 1;
		if (count($tabKeys) == 2) {
		  $session =& Celini::SessionInstance();
		  $session->setNamespace('tabState'.$tabKeys[0]);
		  $session->clear();
		  $session->set($tabKeys[1],1);
		}
	}
	function isSelected($tabKey) {
		return Celini::isTabSelected($tabKey);
	}

} 

?>
