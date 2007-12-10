<?
######################################################################
# PDA view for phpRS - v 1.3
######################################################################
// phpRS
// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/

// PDAview4phpRS
// Copyright (c) 2003 by Jaroslav Mallat (jaroslav@mallat.cz)
// http://hps.mallat.cz/

// This program is free software. - Toto je bezplatny a svobodny software.

include("../config.php");
dbcon();

$dotazConf = MySQL_Query("SELECT jmeno,hodnota FROM ".$GLOBALS["rspredpona"]."pdaconf ");
$pocetConf = MySql_Num_Rows($dotazConf);
for ($pom=0;$pom<$pocetConf;$pom++) {
	$conf[mysql_Result($dotazConf,$pom,"jmeno")]="".mysql_Result($dotazConf,$pom,"hodnota");
}
$PDAkolikClanku = Stripslashes($conf[PDAkolikClanku]);
$PDAkolikClankuSearch = Stripslashes($conf[PDAkolikClankuSearch]);
$PDAkolikTop = Stripslashes($conf[PDAkolikTop]);
$ScriptShowpage = Stripslashes($conf[ScriptShowpage]);
$PDAcharset = Stripslashes($conf[PDAcharset]);
$PDAhlavTitleDef = Stripslashes($conf[PDAhlavTitleDef]);
$PDAhlavTitleUser = Stripslashes($conf[PDAhlavTitleUser]);
$PDAhlavHeaderDef = Stripslashes($conf[PDAhlavHeaderDef]);
$PDAhlavHeaderUser = Stripslashes($conf[PDAhlavHeaderUser]);
$PDAhlavDate = Stripslashes($conf[PDAhlavDate]);
$HmenuShow = Stripslashes($conf[HmenuShow]);
$PDAHmenu = Stripslashes($conf[PDAHmenu]);
$menuShow = Stripslashes($conf[menuShow]);
$PDAmenu = Stripslashes($conf[PDAmenu]);
$katShow = Stripslashes($conf[katShow]);
$PDApatka = Stripslashes($conf[PDApatka]);


// spolecne funkce
// toto je vzdy zobrazeno nahore
function PDAhead()
{
//	global $wwwname,$baseadr,$HmenuShow,$PDAHmenu,$PDAhlavTitleDef,$PDAhlavTitleUser,$PDAhlavHeaderDef,$PDAhlavHeaderUser,$PDAhlavDate,$PDAcharset;
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
	echo "<html><head>";
	if ($GLOBALS["PDAhlavTitleDef"] == 1) {
		echo "<title>".$GLOBALS["wwwname"]." pro PDA</title>\n";
	}
	else {
		echo "<title>".$GLOBALS["PDAhlavTitleUser"]."</title>\n";
	}

	// finta pro emulator (pokud bezi v nem - respektive v ramci, aplikuje se pie.css)
	echo "<script type=\"text/javascript\" language=\"JavaScript\">\n";
	echo "<!-- \n";
	echo "if (parent.location.href != self.location.href) {\n";
	echo "document.write(\"<link rel='stylesheet' type='text/css' href='pie.css'>\");\n";
	echo "}\n";
	echo "//-->\n";
	echo "</script>\n";

	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=".$GLOBALS["PDAcharset"]."\">\n";
	echo "</head><body bgcolor=\"#FFFFFF\">";
	if ($GLOBALS["PDAhlavHeaderDef"] == 1) {
		echo "<B>".$GLOBALS["wwwname"]."</B> pro PDA";
	}
	else {
		echo $GLOBALS["PDAhlavHeaderUser"];
	}
	if ($GLOBALS["PDAhlavDate"] == 1) {
		echo Date(" - d.m. Y");
	}
	echo "\n<HR>\n";
	if ($GLOBALS["HmenuShow"] == 1){
		echo $GLOBALS["PDAHmenu"];
		echo "\n<HR>\n";
	}
}

// toto je vzdy zobrazeno dole
function PDAfooter()
{
	echo "\n<HR>".$GLOBALS["PDApatka"];
}
?>
