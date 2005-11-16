<?php
$menuarrays=array();
$defPractice=&Celini::newOrdo('Practice',$_SESSION['defaultpractice']);
$menuarrays[]=array(
	'menus'=>array(112,113,114,115),
	'sql'=>"
				SELECT 
					p.name as title,
					CONCAT('".Celini::link(true,true,true)."','changepractice=',p.id) as action,
					p.id as item_id
				FROM
					practices AS p
				ORDER BY p.name",
	'defaultitemid'=>$_SESSION['defaultpractice'],
	'menutitle'=>'Practice: '.$defPractice->get('name')
);
?>
