<?
class HL7LabParser {
	var $content;
	var $tokens;


	var $sections = array(
		'MSH' => '',
		'PID' => '',
		'ORC' => '',
		'OBR' => '',
		'OBX' => '',
		'NTE' => '',
		'MSA' => '',
		'DG1' => '',
		'GT1' => '',
		'IN1' => '',
		);


	var $r = array();

	function parse() {
		$this->tokenize();
		$this->parseSegments();
	}

	function getResults() {
		return $this->r;
	}

	function tokenize() {
		$keys = array_keys($this->sections);

		$regex = '/(TST\|';

		foreach($keys as $key) {
			$regex .= "|$key\|";
		}
		$regex .= ")/";

		$tmp = preg_split($regex,$this->content,-1,PREG_SPLIT_DELIM_CAPTURE);

		$i = 0;
		foreach($tmp as $row) {
			$inKey = false;
			if (strlen($row) == 4) {
				$key = substr($row,0,3);
				if (isset($this->sections[$key])) {
					$this->tokens[$i] = array('id'=>$key);
					$inKey = true;
				}
			}

			if (!$inKey)  {
				$this->tokens[$i]['values'] = explode('|',trim($row));
				if (isset($this->tokens[$i]['id'])) {
					array_unshift($this->tokens[$i]['values'],$this->tokens[$i]['id']); // add the segment id back
				}
				$i++;
			}
		}
		if (!isset($this->tokens[0]['id'])) {
			array_shift($this->tokens);
		}
	}

	function parseSegments() {

		foreach($this->tokens as $row) {
			$method = '_parse'.$row['id'];

			if(method_exists($this,$method)) {
				$this->$method($row['values']);
			}
			else {
				var_dump($row['id']);
			}
		}
	}

	function _parsePID($row) {
		$index = $row[1]; // File Patient Index
		$this->r['patients'][$index] = array();

		$this->r['patients'][$index]['patientId'] = $row[2];
		$this->r['patients'][$index]['requestId'] = $row[3];
		list($last,$first) = explode('^',$row[5]);
		$this->r['patients'][$index]['lastName'] = $last;
		$this->r['patients'][$index]['firstName'] = $first;
		$this->r['patients'][$index]['dateOfBirth'] = $row[7];
		$this->r['patients'][$index]['gender'] = $row[8];
		$this->r['patients'][$index]['accountNumber'] = $row[18];

		$this->currentPatient = $index;
	}

	function _parseORC($row) {
		$this->r['order']['type'] = $row[1]; // should always be RE - Results
		$this->r['order']['placerOrderNum'] = $row[2];
		$this->r['order']['filerOrderNum'] = $row[3];
		$this->r['order']['status'] = $row[5]; // CM - Complete, IP Incomplete
		$this->r['order']['orderingProvider'] = $this->cleanString($row[12]);
	}

	function _parseOBR($row) {
		$i = $row[1];
		$p = $this->currentPatient;
		$this->r['request'][$p][$i]['placerOrderNum'] = $row[2];
		$this->r['request'][$p][$i]['filerOrderNum'] = $row[3];

		$tmp = explode('^',$row[4]);
		$this->r['request'][$p][$i]['service'] = $tmp[0].' '.$tmp[1];
		$this->r['request'][$p][$i]['observationDateTime'] = $row[7];
		$this->r['request'][$p][$i]['specimenReceivedDateTime'] = date('Y-m-d H:i:s',strtotime($row[14]));
		$this->r['request'][$p][$i]['orderingProvider'] = $this->cleanString($row[16]);
		$this->r['request'][$p][$i]['componentCode'] = $this->cleanString($row[20]);
		$this->r['request'][$p][$i]['disclosureInfoCLIA'] = $this->cleanString($row[21]);
		$this->r['request'][$p][$i]['reportDateTime'] = date('Y-m-d H:i:s',strtotime($row[22]));
		$this->r['request'][$p][$i]['resultStatus'] = $row[25]; // C - Correction, F - Final, P - Preliminary, X - Canceled

		$this->currentRequest = $i;
	}

	function _parseOBX($row) {
		$i = $row[1];
		$p = $this->currentPatient;
		$r = $this->currentRequest;

		$this->r['observation'][$p][$r][$i]['valueType'] = $row[2]; // ST - String, NM - Numeric, CE - Coded, TX - Text

		$tmp = explode('^',$row[3]);
		//$this->r['observation'][$p][$r][$i]['identifier'] = $tmp[0].' '.$tmp[1];
		$this->r['observation'][$p][$r][$i]['identifier'] = $tmp[0];
		$this->r['observation'][$p][$r][$i]['value'] = $this->cleanString($row[5]);
		$this->r['observation'][$p][$r][$i]['units'] = $this->cleanString($row[6]);
		$this->r['observation'][$p][$r][$i]['referenceRanges'] = $this->cleanString($row[7]);
		$this->r['observation'][$p][$r][$i]['abnormalFlags'] = $row[8]; // lookup table is on page 17
		$this->r['observation'][$p][$r][$i]['resultStatus'] = $row[11]; // C - Correction, F - Final, P - Preliminary, X - Canceled
		$this->r['observation'][$p][$r][$i]['observationDateTime'] = date('Y-m-d H:i:s',strtotime($row[14]));
		$this->r['observation'][$p][$r][$i]['producersID'] = $this->cleanString($row[15]);
		$this->r['observation'][$p][$r][$i]['description'] = $tmp[1];
	}

	function _parseNTE($row) {
		$p = $this->currentPatient;
		$r = null;
		if (isset($this->currentRequest)) {
			$r = $this->currentRequest;
		}
		if (!isset($this->r['note'][$p][$r])) {
			$this->r['note'][$p][$r] = "";
		}
		$this->r['note'][$p][$r] .= $row[3]."\n";
	}

	function _parseMSH($row) {
		//var_dump($row);
	}

	function cleanString($input) {
		return 
		str_replace(array('~','\\','&'), "\n",
			str_replace(array('^')," ",$input)
		);
	}
}
?>
