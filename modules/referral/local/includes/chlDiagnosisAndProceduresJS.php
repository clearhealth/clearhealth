<?php

function chlDiagnosisAndProceduresJS() {
	$view =& new clniView('chlpatientquick');
	return $view->render('diagnosisAndProcedure.js');
}
