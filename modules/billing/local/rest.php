<?php
exit;
$password = "";

$report_id = "";
$report_template_id = "";

$cciGetWorkList_id = "";
$cciGetWorkList_templ_id = "";

$ws_get_worklist_id = "";
$ws_get_worklist_templ_id = "";

$ws_get_patient2_id = "";
$ws_get_patient2_templ_id = "";


require_once '../../../celini/bootstrap.php';
$loader->requireOnce('ordo/ORDataObject.class.php');
$loader->requireOnce('datasources/Person_Appointment_DS.class.php');
$loader->requireOnce('datasources/Person_AddressList_DS.class.php');
$loader->requireOnce('datasources/Patient_NoteList_DS.class.php');
$loader->requireOnce('datasources/Person_PhoneList_DS.class.php');


function Ws_get_patient($last, $passkey) {
	global $password;	
	global $ws_get_patient2_id;
	global $ws_get_patient2_templ_id;
	
        if ($passkey !== $password) return array("access not permitted");
	//$_REQUEST['rf'] = array("last" => $last);
	$_REQUEST['rf'] = array("last" => $last);
	
	$GLOBALS['loader']->requireOnce('includes/ReportAction.class.php');
        $action =& new ReportAction();
        $action->controller = new Controller();
        $action->action( $ws_get_patient2_id, $ws_get_patient2_templ_id );

	$ret = $action->controller->view->_tpl_vars['reports']['default']['grid']->_datasource->toArray();
	//var_dump(array2xml($ret));exit;
	return array("<patient_list>" . array2xml($ret) . "</patient_list>");
	
}


function Ws_get_worklist( $passkey ) {
	
	global $password;
	global $ws_get_worklist_id;
	global $ws_get_worklist_templ_id;

    if ($passkey !== $password) return array("access not permitted");
	// $_REQUEST['rf'] = array("start" => $startDate, "end" => $endDate, "room" => $roomId);
	$_REQUEST['rf'] = array();

	$GLOBALS['loader']->requireOnce('includes/ReportAction.class.php');
        $action =& new ReportAction();
        $action->controller = new Controller();
        $action->action( $ws_get_worklist_id, $ws_get_worklist_templ_id );

	$ret = $action->controller->view->_tpl_vars['reports']['default']['grid']->_datasource->toArray();
	//var_dump(array2xml($ret));exit;
	return array("<work_list>" . array2xml($ret) . "</work_list>");
}

function array2xml($array) {
	$string = "";
	foreach($array as $k => $v) {
		if(is_array($v) == TRUE) {
			if (is_numeric($k)) $k = "row" . $k;
			$string .="<$k>";
			$string .= array2xml($v);
			$string .= "</$k>";
		}
		else {
			if (strlen($v) == 0) $v = " ";
			$string .="<$k>" . $v . "</$k>";
		}
	}
		$string=  preg_replace("/<#>/","<rec_num>",$string);
		$string=  preg_replace("/<\/#>/","</rec_num>",$string);

		return $string;
}

// 
// ws_get_patient
// 
if (strtolower($_SERVER['PATH_INFO']) == "/ws_get_patient") {
	if (    isset($_GET['last']) && 
                isset($_GET['passkey'])) {

                        $array = Ws_get_patient($_GET['last'],$_GET['passkey']);
                        if (isset($array[0])) { echo $array[0];exit;}
                        else {echo "No results";exit;}
        }
        else {
                echo "Required arguments are: personId, passkey";exit;
        }
}

// 
// ws_get_worklist
//
else if (strtolower($_SERVER['PATH_INFO']) == "/ws_get_worklist") {
	if (	isset( $_GET['passkey'] )) {
			
			$array = Ws_get_worklist( $_GET['passkey'] );
			
			if (isset($array[0])) { echo $array[0];exit;}
			else {echo "No results";exit;}
	}
	else {
		echo "Required arguments: are passkey";exit;
	}
	  
}


else {


}
?>
