<?php

class C_TabState extends Controller {
	
	function actionSelectTab($tabKey) {
		return $this->selectTab($tabKey);
	}

	function selectTab($tabKey) {
		return Celini::setTabSelected($tabKey);
	}
	function isSelected($tabKey) {
		return Celini::isTabSelected($tabKey);
	}
	function actionSetPaletteSelected($tabkey) {
		Celini::setPaletteSelected($tabkey);
                header("HTTP/1.1 204 No Content");
                exit;

        }
	function actionSetPaletteUnselected($tabkey) {
		Celini::setPaletteUnselected($tabkey);
                header("HTTP/1.1 204 No Content");
                exit;

        }

} 

?>
