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
echo "<B>".$GLOBALS["PDAkatNazev"]."</B><HR>";

$vysledek = MySQL_Query("SELECT titulek,uvod,text,visit,autor,skupina_cl,date_format(datum,'%d.%m.%Y') as vyslden FROM ".$GLOBALS["rspredpona"]."clanky WHERE link=".$GLOBALS["link"]."");
$radek = MySql_Fetch_Array($vysledek);

$vysledek2 = MySQL_Query("SELECT jmeno FROM ".$GLOBALS["rspredpona"]."user WHERE idu=".$radek["autor"]."");
$radek2 = MySql_Fetch_Array($vysledek2);

mysql_query("update ".$GLOBALS["rspredpona"]."clanky set visit=(visit+1) where link=".$GLOBALS["link"]."");
echo "<B>".$radek["titulek"]."</B><BR>\n";
echo "[ ".$radek2["jmeno"].", vydáno dne ".$radek["vyslden"]." (".$radek["visit"]." pøeètení) ]\n";
echo "<P>".$radek["uvod"]."<P>\n";

$text=$radek["text"];

$pozStart=StrPos("x".$text,"<obrazek");

if ($pozStart>0){ 	//pokud je vubec nejakej
	$rotuj=1; // inic. rotace
   	while ($rotuj) {
		$pozStart--;
		$delka = StrLen($text);					// zjistime celou delku textu
		$znackaCela=substr($text,$pozStart,50); //nactem prvnich 50 znaku od <obrazek
		$znackaKonec=StrPos($znackaCela,">"); 	// najdem pozici konce znacky
		$znackaKonec++; 						// prictem kvuli pocitani od nuly
		$znacka=substr($znackaCela,0,$znackaKonec); // ulozime celou obr znacku
	
		$znackaPozStart=StrPos($znacka,"id=\"");	// najdem ID
		$znackaPozStart=$znackaPozStart+4;
		$idCele=substr($znacka,$znackaPozStart,10);
		$idKonec=StrPos($idCele,"\" zar");
		$id=substr($idCele,0,$idKonec);
	
		// dotaz na obr dle ID
		$vysledekObr = MySQL_Query("SELECT * FROM ".$rspredpona."imggal_obr WHERE ido=".$id."");
		$radekObr = MySql_Fetch_Array($vysledekObr);
		if ($radekObr["nahl_poloha"] == "none") {	// pokud neni nahled
			$obr = "<P>(Náhled obr. není k dispozici)<BR><a href=\"../".$radekObr["obr_poloha"]."\"><img src=\"obr.gif\" width=\"24\" height=\"30\" alt=\"Náhled není k dispozici\" border=\"0\"><BR>(Original ".$radekObr["obr_vel"]."b)</a><P>\n";
		}
		else {
			$obr = "<P>(Náhled obrázku)<BR><a href=\"../".$radekObr["obr_poloha"]."\"><img src=\"../".$radekObr["nahl_poloha"]."\" width=\"".$radekObr["nahl_width"]."\" height=\"".$radekObr["nahl_height"]."\" alt=\"Náhled obrázku\" border=\"0\"><BR>(Original ".$radekObr["obr_vel"]."b)</a><P>\n";
		}		
		$tempTextStart=SubStr($text,0,$pozStart-1); 	// ulozime si text po zacatek obr znacky
		$kolikDoKonce2 = $delka-$pozStart-$znackaKonec;
		$kolikDoKonce1 = $delka-$kolikDoKonce2;
		$tempTextEnd=SubStr($text,$kolikDoKonce1,$kolikDoKonce2); 	// ulozime si text od konce obr znacky
		
		echo $tempTextStart;
		echo $obr;
		$pozStart=StrPos($tempTextEnd,"<obrazek");
		if ($pozStart>0 || $tempTextEnd[0]=="<obrazek"){ 	//pokud je dalsi
			$text=$tempTextEnd;
		}
		else {
		   	$rotuj=0;
			echo $tempTextEnd;
		}
	}
}
else {
	echo $text;
}
// související clanky
if ($radek["skupina_cl"] != 0) {
	$vysledekSouv = MySQL_Query("SELECT link,titulek,date_format(datum,'%d.%m.%Y') as vyslden FROM ".$GLOBALS["rspredpona"]."clanky WHERE skupina_cl=".$radek["skupina_cl"]." AND link<>".$GLOBALS["link"]." ORDER BY datum desc");
	$radkuSouv = MySql_Num_Rows($vysledekSouv);
	if ($radkuSouv > 0) {
		echo "<P><B>Související èlánky:</B><BR>\n";
		while ($radekSouv = MySql_Fetch_Array($vysledekSouv)) {
			echo "<a href=\"pdaview.php?link=".$radekSouv["link"]."&PDAkatNazev=".$GLOBALS["PDAkatNazev"]."\">".$radekSouv["titulek"]."</a> (".$radekSouv["vyslden"].")<br>\n";
		}
	}
}
PDAfooter();
?>
</body>
</html>
