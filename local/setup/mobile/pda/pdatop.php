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

$dnesaktdatum=Date("Y-m-d H:i:s");
$vysledek = MySQL_Query("SELECT titulek,uvod,link,tema FROM ".$GLOBALS["rspredpona"]."clanky WHERE visible=1 AND datum<='$dnesaktdatum' ORDER BY visit desc LIMIT 0,".$PDAkolikTop."");
$radku = MySql_Num_Rows($vysledek);
echo "<B>Nejètenìjší èlánky</B><HR>";
if ($radku == 0) {
	echo "<HR><B>Vašemu dotazu neodpovídá ani jeden èlánek.<BR>Prosím, opakujte vyhledávání.</B>";
}
else {
	while ($radek = MySql_Fetch_Array($vysledek)) {
		$vysledekTema = MySQL_Query("SELECT nazev FROM ".$GLOBALS["rspredpona"]."topic WHERE idt=".$radek["tema"]."");
		$radekTema = MySql_Fetch_Array($vysledekTema);
		echo "<a href=\"pdaview.php?link=".$radek["link"]."&PDAkatNazev=".$radekTema["nazev"]."\">".$radek["titulek"]."</a><br>";
		echo $radek["uvod"]."<p>";
	}
}	
PDAfooter();
?>
</body>
</html>
