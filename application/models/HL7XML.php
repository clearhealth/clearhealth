<?php
/*$hl7 = file_get_contents('/tmp/opsam.hl7');
$hl7 = preg_replace('/^(.*?)</m','<',$hl7);
$hl7 = str_replace("<br>","\n",$hl7);
$hl7 = strip_tags($hl7);*/
//$hl7 = preg_replace('/.*?(FHS.*)/s','\1',$hl7);
//$hl7 = preg_replace('/(.*FTS\|.\|).*/s','\1',$hl7);

//$hl7 = str_replace("\r\n","\n",$hl7);
//$hl7 = split("\n",$hl7);

class HL7XML {
	var $hl7 = "";
	var $xml = "";

	function __construct($hl7) {
		$this->hl7 = $hl7;
	}

	function parse() {
		$hl7 = preg_split('/\n/',$this->hl7);
		//var_dump($hl7);
		$xml = new SimpleXMLElement('<HL7Message></HL7Message>');
		//echo $this->hl7;
		$currentNode = '';
		$mshNode = '';
		$testNode = '';
		$orderNode = '';
		$obxNode = '';
		$mshLine = '';
		foreach ($hl7 as $line) {
			if  (strlen($line) == 0) continue;
			//echo "ln1: " . $line . "\n";
			$line = preg_replace('/^.*MSH\|/','MSH|',$line);
			//echo "ln2: " . $line . "\n";
			preg_match_all('((.*?)\|)',trim($line)."|",$matches);
			//echo $line ."\n";
			//var_dump($matches);
			if (isset($matches[1])) {
				for ($i=0;$i < count($matches[1]);$i++) {
					if ($i == 0) {
						switch(trim($matches[1][$i])) {
							case "ORC":
							$orderNode = $mshNode->addChild(trim($matches[1][$i]));
							$currentNode = $orderNode;
							break;
							case "OBR":
							$testNode = $orderNode->addChild(trim($matches[1][$i]));
							$currentNode = $testNode;
							break;
							case "OBX":
							$currentNode = $testNode->addChild(trim($matches[1][$i]));
							$obxNode = $currentNode;
							break;
							case "NTE":
							if (is_object($obxNode)) {
							$currentNode = $obxNode->addChild(trim($matches[1][$i]));
							}
							break;
							case "MSH":
							$mshNode = $xml->addChild(trim($matches[1][$i]));
							$currentNode = $mshNode;
                					$testNode = '';
					                $orderNode = '';
					                $obxNode = '';          		
							break;
							default:
							$currentNode = $mshNode->addChild(trim($matches[1][$i]));
						}
					}
					else {
						if (strpos($matches[1][$i],'^') !== false) {
							$currentNode->addChild(trim($matches[1][0]) . "." . $i);
							$subfields = preg_split('/\^/',$matches[1][$i]);
							for($x=0;$x < count($subfields);$x++) {
								$currentNode->addChild($matches[1][0] . "." . $i . "." . $x,$this->xmlentities($subfields[$x]));
							}
		
						}
						else{
							$currentNode->addChild(trim($matches[1][0]) . "." . $i , $this->xmlentities(trim($matches[1][$i])));
						}
					}
				}
			}
		}
		$this->xml = $xml;
	}

	function xmlentities($string) {
			return str_replace ( array ( '&', '"', "'", '<', '>', '�' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&apos;' ), $string );
	} 

}

/*$str = "MSH|^~\&||GA0000||VAERS PROCESSOR|20010331605||ORU^RO1|20010422GA03|T|2.3.1|||AL|
PID|||1234^^^^SR~1234-12^^^^LR~00725^^^^MR||Doe^John^Fitzgerald^JR^^^L||20001007|M||2106-3^White^HL70005|123 Peachtree St^APT 3B^Atlanta^GA^30210^^M^^GA067||(678) 555-1212^^PRN|
NK1|1|Jones^Jane^Lee^^RN|VAB^Vaccine administered by (Name)^HL70063|
NK1|2|Jones^Jane^Lee^^RN|FVP^Form completed by (Name)-Vaccine provider^HL70063|101 Main Street^^Atlanta^GA^38765^^O^^GA121||(404) 554-9097^^WPN|
ORC|CN|||||||||||1234567^Welby^Marcus^J^Jr^Dr.^MD^L|||||||||Peachtree Clinic|101 Main Street^^Atlanta^GA^38765^^O^^GA121|(404) 554-9097^^WPN|101 Main Street^^Atlanta^GA^38765^^O^^GA121|
OBR|1|||^CDC VAERS-1 (FDA) Report|||20010316|
OBX|1|NM|21612-7^Reported Patient Age^LN||05|mo^month^ANSI|
OBX|1|TS|30947-6^Date form completed^LN||20010316|
OBX|2|FT|30948-4^Vaccination adverse events and treatment, if any^LN|1|fever of 106F, with vomiting, seizures, persistent crying lasting over 3 hours, loss of appetite|
OBX|3|CE|30949-2^Vaccination adverse event outcome^LN|1|E^required emergency room/doctor visit^NIP005|
OBX|4|CE|30949-2^Vaccination adverse event outcome^LN|1|H^required hospitalization^NIP005|
OBX|5|NM|30950-0^Number of days hospitalized due to vaccination adverse event^LN|1|02|d^day^ANSI|
OBX|6|CE|30951-8^Patient recovered^LN||Y^Yes^ HL70239|
OBX|7|TS|30952-6^Date of vaccination^LN||20010216|
OBX|8|TS|30953-4^Adverse event onset date and time^LN||200102180900|
OBX|9|FT|30954-2^Relevant diagnostic tests/lab data^LN||Electrolytes, CBC, Blood culture|
OBR|2|||30955-9^All vaccines given on date listed in #10^LN|
OBX|1|CE30955-9&30956-7^Vaccine type^LN|1|08^HepB-Adolescent/pediatric^CVX|
OBX|2|CE|30955-9&30957-5^Manufacturer^LN|1|MSD^Merck^MVX|
OBX|3|ST|30955-9&30959-1^Lot number^LN|1|MRK12345|
OBX|4|CE|30955-9&30958-3^ Route^LN|1|IM^Intramuscular ^HL70162|
OBX|5|CE|30955-9&31034-2^Site^LN|1|LA^Left arm^ HL70163|
OBX|6|NM|30955-9&30960-9^Number of previous doses^LN|1|01I
OBX|7|CE|CE|30955-9&30956-7^Vaccine type^LN|2|50^DTaP-Hib^CVX|
OBX|8|CE|30955-9&30957-5^ Manufacturer^LN|2|WAL^Wyeth_Ayerst^MVX|
OBX|9|ST|30955-9&30959-1^Lot number^LN|2|W46932777|
OBX|10|CE|30955-9&30958-3^ Route^LN|2|IM^Intramuscular^HL70162|
";


$hl7xml = new HL7XML($str);
$hl7xml->parse();
echo $hl7xml->xml->asXML();
*/
