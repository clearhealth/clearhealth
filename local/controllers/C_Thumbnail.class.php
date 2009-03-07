<?php
$GLOBALS['loader']->requireOnce("ordo/Document.class.php");

class C_Thumbnail extends Controller {

	function actionThumb() {
		$GLOBALS['loader']->requireOnce("lib/phpThumb/phpThumb.php");
		return "";
		exit;
	}	
	function actionPatientPic($patientId = 0,$width = 0) {
		if ($width == 0) {
			$width =  Celini::config_get('PatientPicture:thumbWidth');
		}
                $d = Document::FirstDocumentByCategoryName((int)$patientId,"Picture");
                if (is_object($d)) {
                        $_GET['src'] = '/' . (int)$patientId . "/" . $d->get("name");
			$_GET['w']= (int)$width;
			$GLOBALS['loader']->requireOnce("lib/phpThumb/phpThumb.php");
                }
		else {
			return "";
		}
	}

}

?>
