<?
######################################################################
# PDA view for phpRS
######################################################################
// phpRS
// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/

// PDAview4phpRS
// Copyright (c) 2003 by Jaroslav Mallat (mallat@seznam.cz)
// http://hps.euweb.cz/

// This program is free software. - Toto je bezplatny a svobodny software.


include("pdaconf.php");

PDAhead();
dbcon();

$err = "<B>Chyba! Požadovaná stránka nenalezena!</B>";
if (IsSet($GLOBALS["name"])){
	$page=mysql_query("select hodnota from ".$GLOBALS["rspredpona"]."alias where alias='".$GLOBALS["name"]."' and typ='sablona'");
	$pocet=mysql_NumRows($page);
	if ($pocet>0){
		$radek = MySql_Fetch_Array($page);
		// oprava cesty, neb tento script neni v rootu, ale v adresari pda
		$file = "../".$radek["hodnota"]."";
		if (file_exists($file)){
			// diky pouzitim funkce include() je mozno vlozit php scripty
			if ($ScriptShowpage == 1){
				include($file);
			}
			else {
				ReadFile($file);
			}
		}
		else {
			echo $err;
		}
	}
	else {
		echo $err;
	}
}
else {
	echo $err;
}


PDAfooter();
?>
</body>
</html>
