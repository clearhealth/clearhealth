<?php
global $menuarrays;
$menuarrays=array();
if (isset($_SESSION['defaultpractice'])) {
	$defPractice=&Celini::newOrdo('Practice',$_SESSION['defaultpractice']);
	$trail =& Celini::trailInstance();

	$get = '';
	if (count($_GET) > 0) {
		$get = $_SERVER['QUERY_STRING'];
	}
	if (isset($_GET['changepractice'])) {
		$get = str_replace('changepractice='.$_GET['changepractice'],'',$get);
	}
	if ($get != '') {
		if (substr($get,-1) != '&') {
			$get .= '&';
		}
	}
	$default = false;
	if (isset($_GET[0])) {
		$default = $_GET[0];
	}
	
	$userProfile =& Celini::getCurrentUserProfile();
	$userPracticeList = $userProfile->getPracticeIdList();
	$menuarrays[]=array(
		'menus'=>array(54),
		'sql'=>"
					SELECT 
						p.name as title,
						CONCAT('".Celini::link(true,true,true,$default).$get."','changepractice=',p.id) as action,
						p.id as item_id
					FROM
						practices AS p
					WHERE
						p.id IN (" . implode(', ', $userPracticeList) . ")
					ORDER BY p.name",
		'defaultitemid'=>$_SESSION['defaultpractice'],
		'menutitle'=>'Practice: '.$defPractice->get('name')
	);
	var_dump("
SELECT 
	p.name as title,
	CONCAT('".Celini::link(true,true,true,$default).$get."','changepractice=',p.id) as action,
	p.id as item_id
FROM
	practices AS p
WHERE
	p.id IN (" . implode(', ', $userPracticeList) . ")
ORDER BY p.name");
}
?>
