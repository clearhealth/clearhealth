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

include("pdaconf.php");
PDAhead();
dbcon();


$vysledek = MySQL_Query("SELECT idt FROM ".$GLOBALS["rspredpona"]."topic WHERE idt=".$GLOBALS["PDAkat"]." OR id_predka=".$GLOBALS["PDAkat"]." ORDER BY nazev");
$podminka = "";
$radku = MySql_Num_Rows($vysledek);
$i = 1;
while ($radek = MySql_Fetch_Array($vysledek)) {
	if ($radku!=$i) {
		$podminka = $podminka." tema=".$radek["idt"]." OR"; 
	}
	else {
		$podminka = $podminka." tema=".$radek["idt"]; 
	}
	$i++;
}	
$dnesaktdatum=Date("Y-m-d H:i:s");
$PDAkolikataSQL = $GLOBALS["PDAkolikata"]-1;
$vysledekPocet = MySQL_Query("SELECT tema FROM ".$GLOBALS["rspredpona"]."clanky WHERE ".$podminka." AND visible=1 AND datum<='$dnesaktdatum' ");
$vysledek = MySQL_Query("SELECT titulek,uvod,link FROM ".$GLOBALS["rspredpona"]."clanky WHERE ".$podminka." AND visible=1 AND datum<='$dnesaktdatum' ORDER BY datum desc LIMIT ".$PDAkolikataSQL.",".$PDAkolikClanku."");
$radku = MySql_Num_Rows($vysledekPocet);
$radkuTed = MySql_Num_Rows($vysledek);
echo "<B>".$GLOBALS["PDAkatNazev"]."</B>";
if ($radku == 0) {
	echo "<HR><B>Vašemu dotazu neodpovídá ani jeden èlánek.<BR>Prosím, opakujte vyhledávání.</B>";
}
else {

	echo " - zobrazeno ".$radkuTed." z ".$radku."&nbsp;";
	$c = 0;
	if ($radku > $PDAkolikClanku) {
		echo "<BR>\n";
		for ($a=1;$a<=$radku;$a=$a+$PDAkolikClanku) {
			$b=$a+$PDAkolikClanku-1;
			if ($a == $GLOBALS["PDAkolikata"]) {
				echo "|".$a."-".$b."| ";
			}
			elseif ($a > $GLOBALS["PDAkolikata"]) {
				$PDAkolikataHref = $a;
				echo "<a href=\"pdakat.php?PDAkat=".$GLOBALS["PDAkat"]."&PDAkatNazev=".$GLOBALS["PDAkatNazev"]."&PDAkolikata=".$PDAkolikataHref."\">|".$a."-".$b."|</a> ";
			}
			else {
				$PDAkolikataHref = $c+1;
				echo "<a href=\"pdakat.php?PDAkat=".$PDAkat."&PDAkatNazev=".$PDAkatNazev."&PDAkolikata=".$PDAkolikataHref."\">|".$a."-".$b."|</a> ";
			}
		$c = $c + $PDAkolikClanku;
		}
	}
	echo "<hr>";
	
	while ($radek = MySql_Fetch_Array($vysledek)) {
		echo "<a href=\"pdaview.php?link=".$radek["link"]."&PDAkatNazev=".$PDAkatNazev."\">".$radek["titulek"]."</a><br>";
		echo $radek["uvod"]."<p>";
	}
}	
PDAfooter();
?>
</body>
</html>
