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

if ($menuShow == 1){
	echo "<B>Menu:</B><BR>\n";
	echo $PDAmenu;
	echo "<P>";
}
if ($katShow == 1){
	PDAkatShow();
}


PDAfooter();

// funkce teto stranky
function PDAkatShow(){
	echo "<B>Rubriky:</B><BR>\n";

	// pochazi ze souboru admin/atopic.php
	$poletopic=GenerujSeznam();
	if (!is_array($poletopic)){
	  echo "<p align=\"center\">Databáze je prázdná!</p>\n";
	}
	else{
	  $pocettopic=count($poletopic); // pocet prvku v poli
	  echo "<table border=\"0\">\n";
	  for ($pom=0;$pom<$pocettopic;$pom++){
	    echo "<tr><td align=\"left\">";
	    echo Me($poletopic[$pom][2],3);
	    if ($poletopic[$pom][2]>0){
			echo "<img src=\"strom.gif\" width=\"11\" height=\"11\" align=\"middle\">&nbsp;";
		}
		echo "<a href=\"pdakat.php?PDAkat=".$poletopic[$pom][0]."&PDAkatNazev=".$poletopic[$pom][1]."&PDAkolikata=1\">".$poletopic[$pom][1]."</a></td></tr>\n";
	  }
	  echo "</table>\n";
	}
}


function GenerujSeznam($pocatecnihodnota = 0){
	$dotazsez = mysql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic order by level,nazev");
	$pocetsez=mysql_Num_Rows($dotazsez);
	for ($pom=0;$pom<$pocetsez;$pom++){
	    $vstdata[$pom][0]=mysql_Result($dotazsez,$pom,"idt");       // id
	    $vstdata[$pom][1]=mysql_Result($dotazsez,$pom,"nazev");     // nazev polozky
	    $vstdata[$pom][2]=mysql_Result($dotazsez,$pom,"id_predka"); // id rodice
	    $vstdata[$pom][3]=0;                                        // prepinace pouzito pole
	}
	if ($pocetsez>0){
		$trideni=1;
	}
	else{
		$trideni=0;
	}
	$polehist[0]=$pocatecnihodnota;
	$polex=0;
	$vysledekcislo=0;
	while ($trideni==1){
	  $nasel=0;
	  for ($pom=0;$pom<$pocetsez;$pom++){
	    if ($vstdata[$pom][3]==0){ 
	      if ($vstdata[$pom][2]==$polehist[$polex]){ 
	            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0];
	            $vysledek[$vysledekcislo][1]=$vstdata[$pom][1]; 
	            $vysledek[$vysledekcislo][2]=$polex;
	            $vysledekcislo++;
	            $vstdata[$pom][3]=1;
	            $polex++;
	            $polehist[$polex]=$vstdata[$pom][0];
	            $nasel=1;
	            break;
	      }
	    }
	  }
	
	  if ($nasel==0){
	    if ($polehist[$polex]==$pocatecnihodnota){
	      $trideni=0;
		}
	    else {
	      $polex--;
	    }
	  }
	}
	
	if ($pocetsez>0):
	  return $vysledek;
	else:
	  return 0;
	endif;
}

function Me($velikost = 0,$sirkaintervalu = 1){
	// pochazi ze souboru admin/astdlib.php
	$mez="";
	for ($pom=0;$pom<$velikost;$pom++){
	  for ($pom2=0;$pom2<$sirkaintervalu;$pom2++){
	     $mez=$mez."&nbsp;";
	  }
	}
	return $mez;
}

?>
</body>
</html>
