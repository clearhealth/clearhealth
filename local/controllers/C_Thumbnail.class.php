<?php

class C_Thumbnail extends Controller {

	function actionThumb() {
		$GLOBALS['loader']->requireOnce("lib/phpThumb/phpThumb.php");
		return "";
		exit;
	}	

}

?>
