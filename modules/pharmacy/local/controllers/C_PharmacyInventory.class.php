<?php
/**
 * Controller for adding/editing/listing/searching inventory items
 */

class C_PharmacyInventory extends Controller {

	function C_PharmacyInventory() {
		parent::Controller();
	}

	function actionEdit() {
		return($this->view->render('edit.html'));
	}
}
?>