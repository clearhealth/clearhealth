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

if ($GLOBALS["text"] == "") {
	echo "<form method=\"post\">\n";
}

PDAhead();
dbcon();

if ($GLOBALS["text"] == "") {
	echo "<b>Jednoduché vyhledání</b><HR>\n";
	echo "<input type=\"hidden\" name=\"PDAkolikata\" value=\"1\">\n";
	echo "Hledaný výraz:<br><input type=\"text\" name=\"text\" maxlength=\"50\"><BR>\n";
	echo "<input type=\"submit\" value=\"Vyhledat\">\n";
	echo "</form>\n";
}
else {
	PDAhledej();
}
PDAfooter();


// funkce teto stranky
function PDAhledej()
{
//	global $rspredpona,$text,$PDAkolikClankuSearch,$PDAkolikata;
	$dnesaktdatum=Date("Y-m-d H:i:s");
	$PDAkolikataSQL = $GLOBALS["PDAkolikata"]-1;

	$vysledekPocet = MySQL_Query("SELECT link FROM ".$GLOBALS["rspredpona"]."clanky WHERE titulek LIKE ('%".$GLOBALS["text"]."%') OR uvod LIKE ('%".$GLOBALS["text"]."%') OR text LIKE ('%".$GLOBALS["text"]."%') AND visible=1 AND datum<='$dnesaktdatum' ");
	$radku = MySql_Num_Rows($vysledekPocet);
	$vysledek = MySQL_Query("SELECT link,autor,tema,titulek FROM ".$GLOBALS["rspredpona"]."clanky WHERE titulek LIKE ('%".$GLOBALS["text"]."%') OR uvod LIKE ('%".$GLOBALS["text"]."%') OR text LIKE ('%".$GLOBALS["text"]."%') AND visible=1 AND datum<='$dnesaktdatum' ORDER BY datum desc LIMIT ".$PDAkolikataSQL.",".$GLOBALS["PDAkolikClankuSearch"]."");
	$radkuTed = MySql_Num_Rows($vysledek);

	if ($radku > 0) {
		echo "<B>Výsledek hledání</B>";
		echo " - zobrazeno ".$radkuTed." z ".$radku."&nbsp;";

		$c = 0;
		if ($radku > $GLOBALS["PDAkolikClankuSearch"]) {
			echo "<BR>\n";
			for ($a=1;$a<=$radku;$a=$a+$GLOBALS["PDAkolikClankuSearch"]) {
				$b=$a+$GLOBALS["PDAkolikClankuSearch"]-1;
				if ($a == $GLOBALS["PDAkolikata"]) {
					echo "|".$a."-".$b."| ";
				}
				elseif ($a > $GLOBALS["PDAkolikata"]) {
					$PDAkolikataHref = $a;
					echo "<a href=\"pdasearch.php?text=".$GLOBALS["text"]."&PDAkolikata=".$PDAkolikataHref."\">|".$a."-".$b."|</a> ";
				}
				else {
					$PDAkolikataHref = $c+1;
					echo "<a href=\"pdasearch.php?text=".$GLOBALS["text"]."&PDAkolikata=".$PDAkolikataHref."\">|".$a."-".$b."|</a> ";
				}
			$c = $c + $GLOBALS["PDAkolikClankuSearch"];
			}
		}
		echo "<hr>";

	
		$radek = MySql_Num_Rows($vysledek);
			while ($radek = MySql_Fetch_Array($vysledek)) {
			$vysledek2 = MySQL_Query("SELECT jmeno FROM ".$GLOBALS["rspredpona"]."user WHERE idu=".$radek["autor"]."");
			$radek2 = MySql_Fetch_Array($vysledek2);
			$vysledek3 = MySQL_Query("SELECT nazev FROM ".$GLOBALS["rspredpona"]."topic WHERE idt=".$radek["tema"]."");
			$radek3 = MySql_Fetch_Array($vysledek3);
			
			echo "<a href=\"pdaview.php?link=".$radek["link"]."&PDAkatNazev=".$radek3["nazev"]."\">".$radek["titulek"]."</a> [".$radek2["jmeno"]." -  <b>".$radek3["nazev"]."</b>]<br>\n";
			}
	}
	else {
		echo "<B>Vašemu dotazu neodpovídá ani jeden èlánek. <BR> Prosím, opakujte vyhledávání. </B>\n";

	}

}


?>
</body>
</html>
