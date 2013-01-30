<?php
/*****************************************************************************
*       NQFPQRIReport.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class NQFPQRIReport implements NSDRMethods {

	protected $tthis;
	protected $context;
	protected $data;
	protected $dateStart;
	protected $dateEnd;
	protected $providerId;

	public static function generatePQRIXML(Array $data) {
		/*Summary Calculation
		Calculation is generic to all measures:
		*) Calculate the final denominator by adding all that meet denominator criteria.
		*) Subtract from the final denominator all that do not meet numerator criteria yet also meet exclusion criteria. Note some measures do not have exclusion criteria.
		*) The performance calculation is the number meeting numerator criteria divided by the final denominator.
		*) For measures with multiple patient populations, repeat this process for each patient population and report each result separately.
		*) For measures with multiple numerators, calculate each numerator separately within each population using the paired exclusion.*/

		$info = NQF::getInfo();
		$arr = array();
		$arr['visitDateStart'] = $info['dateStart'];
		$arr['visitDateEnd'] = $info['dateEnd'];
		$arr['provider'] = $info['provider'];
		$providerId = $arr['provider']->personId;

		$zip = new ZipArchive();
		$filename = tempnam('/tmp','pqri-');
		if ($zip->open($filename,ZIPARCHIVE::CREATE) !== true) {
			trigger_error('Cannot create '.$filename.' zip file');
			return;
		}
		foreach ($data as $queryData) {
			$contents = array();
			foreach ($queryData['rows'] as $row) {
				$results = array();
				$className = null;
				$value = $row['data'][1];
				switch ($row['data'][0]) {
					case 'gov.cms.nqf.0421': // Core - 1
						$className = 'NQF0421';
						break;
					case 'gov.cms.nqf.0013': // Core - 2
						$className = 'NQF0013';
						break;
					case 'gov.cms.nqf.0028a': // Core - 3a
						$className = 'NQF0028a';
						break;
					case 'gov.cms.nqf.0028b': // Core - 3b
						$className = 'NQF0028b';
						break;
					case 'gov.cms.nqf.0041': // Alt Core - 1
						$className = 'NQF0041';
						break;
					case 'gov.cms.nqf.0024': // Alt Core - 2
						/* Criteria 1 = D: 3; N1: 2; P1: 66.67%; N2: 0; P2: 0% N3: 0; P3: 0%<br/>
						Criteria 2 = D: 3; N1: 2; P1: 66.67%; N2: 0; P2: 0% N3: 0; P3: 0%<br/>
						Criteria 3 = D: 0; N1: 0; P1: 0%; N2: 0; P2: 0% N3: 0; P3: 0% */
						$className = 'NQF0024';
						break;
					case 'gov.cms.nqf.0038': // Alt Core - 3
						/* D: 0<br/>
						N1: 0 P1: 0%<br/>
						N2: 0 P2: 0%<br/>
						N3: 0 P3: 0%<br/>
						N4: 0 P4: 0%<br/>
						N5: 0 P5: 0%<br/>
						N6: 0 P6: 0%<br/>
						N7: 0 P7: 0%<br/>
						N8: 0 P8: 0%<br/>
						N9: 0 P9: 0%<br/>
						N10: 0 P10: 0%<br/>
						N11: 0 P11: 0%<br/>
						N12: 0 P12: 0% */
						$className = 'NQF0038';
						break;
					case 'gov.cms.nqf.0059': // CMS - 1
						$className = 'NQF0059';
						break;
					case 'gov.cms.nqf.0064': // CMS - 2
						/* D: 1<br/>N1: 0; P1: 0%<br/>N2: 0; P2: 0%<br/>E: 0 */
						$className = 'NQF0064';
						break;
					case 'gov.cms.nqf.0061': // CMS - 3
						$className = 'NQF0061';
						break;
				}

				if ($className === null) continue;
				$results = call_user_func_array(array($className,'getResults'),null);
				$ctr = count($results);
				$arr['numberOfFiles'] = $ctr;
				$arr['measureNumber'] = substr($className,3);
				for ($i = 0; $i < $ctr; $i++) {
					$arr['fileNumber'] = $i + 1;;
					$result = $results[$i];
					$arr['denominator'] = $result['denominator'];
					$arr['numerator'] = $result['numerator'];
					$arr['exclusions'] = isset($result['exclusions'])?$result['exclusions']:0;
					$arr['percentage'] = $result['percentage'];
					$pqri = new PQRIRegistryXML($arr);
					$contents = $pqri->generate();
					$fname = $className;
					if ($ctr != 1) $fname .= '-'.$arr['fileNumber'];
					$fname .= '.xml';
					$zip->addFromString($fname,$contents);
				}
			}
		}
		$zip->close();
		$contents = file_get_contents($filename);
		@unlink($filename);
		return $contents;
	}

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	function nsdrPersist($tthis,$context,$data) {
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$nqfId = '';
		switch ($tthis->_nsdrNamespace) {
			case 'gov.cms.nqf.0421': // Core - 1
				$nqfId = '0421';
				break;
			case 'gov.cms.nqf.0013': // Core - 2
				$nqfId = '0013';
				break;
			case 'gov.cms.nqf.0028a': // Core - 3a
				$nqfId = '0028a';
				break;
			case 'gov.cms.nqf.0028b': // Core - 3b
				$nqfId = '0028b';
				break;
			case 'gov.cms.nqf.0041': // Alt Core - 1
				$nqfId = '0041';
				break;
			case 'gov.cms.nqf.0024': // Alt Core - 2
				$nqfId = '0024';
				break;
			case 'gov.cms.nqf.0038': // Alt Core - 3
				$nqfId = '0038';
				break;
			case 'gov.cms.nqf.0059': // CMS - 1
				$nqfId = '0059';
				break;
			case 'gov.cms.nqf.0064': // CMS - 2
				$nqfId = '0064';
				break;
			case 'gov.cms.nqf.0061': // CMS - 3
				$nqfId = '0061';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0081-pqri5': // CMS - 4
				$nqfId = '0081';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0070-pqri7': // CMS - 5
				$nqfId = '0070';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0043-pqri111': // CMS - 6
				$nqfId = '0043';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0031-pqri112': // CMS - 7
				$nqfId = '0031';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0034-pqri113': // CMS - 8
				$nqfId = '0034';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0067-pqri6': // CMS - 9
				$nqfId = '0067';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0083-pqri8': // CMS - 10
				$nqfId = '0083';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0105-pqri9': // CMS - 11
				$nqfId = '0105';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0086-pqri12': // CMS - 12
				$nqfId = '0086';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0088-pqri18': // CMS - 13
				$nqfId = '0088';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0089-pqri19': // CMS - 14
				$nqfId = '0089';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0047-pqri53': // CMS - 15
				$nqfId = '0047';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0001-pqri64': // CMS - 16
				$nqfId = '0001';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0002-pqri66': // CMS - 17
				$nqfId = '0002';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0387-pqri71': // CMS - 18
				$nqfId = '0387';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0385-pqri72': // CMS - 19
				$nqfId = '0385';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0389-pqri102': // CMS - 20
				$nqfId = '0389';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0027-pqri115': // CMS - 21
				$nqfId = '0027';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0055-pqri117': // CMS - 22
				$nqfId = '0055';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0062-pqri119': // CMS - 23
				$nqfId = '0062';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0056-pqri163': // CMS - 24
				$nqfId = '0056';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0074-pqri197': // CMS - 25
				$nqfId = '0074';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0084-pqri200': // CMS - 26
				$nqfId = '0084';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0073-pqri201': // CMS - 27
				$nqfId = '0073';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0068-pqri204': // CMS - 28
				$nqfId = '0068';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0004': // CMS - 29
				$nqfId = '0004';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0012': // CMS - 30
				$nqfId = '0012';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0014': // CMS - 31
				$nqfId = '0014';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0018': // CMS - 32
				$nqfId = '0018';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0032': // CMS - 33
				$nqfId = '0032';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0033': // CMS - 34
				$nqfId = '0033';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0036': // CMS - 35
				$nqfId = '0036';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0052': // CMS - 36
				$nqfId = '0052';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0075': // CMS - 37
				$nqfId = '0075';
				break;
			case 'com.clearhealth.meaningfulUse.nqf0575': // CMS - 38
				$nqfId = '0575';
				break;
			default:
				return '';
				break;
		}
		$class = 'NQF'.$nqfId;
		if (!class_exists($class)) return '';
		$object = new $class($tthis,$context,$data);
		$ret = $object->populate();
		return $ret;
	}

}
