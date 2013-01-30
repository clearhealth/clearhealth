<?php
/*****************************************************************************
*       ChmedController.php
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

class ChmedController extends WebVista_Controller_Action {

	function ajaxBasemed24Action() {
		$limit = (int)$this->_getParam('limit');
		if (!$limit > 0)  $limit = 25;
		$tradename = file_get_contents('php://input');
		$tradename = urldecode($tradename);
		$tradename = preg_replace('/[^A-Za-z0-9\-\%\ ]+/','',$tradename);
		//trigger_error('tradename: ' .$tradename ,E_USER_NOTICE);
		//$tradename = 'lipitor';
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$medIterator = new BaseMed24Iterator(null,false);
		$formulary = 'default';
		$config = new ConfigItem();
		$config->configId = 'defaultFormulary';
		$config->populate();
		if (strlen($config->value) > 0) {
			$formulary = substr($config->value,9);
		}
		$medIterator->setFilters(array('tradename' => $tradename, 'limit' => $limit, 'formulary' => $formulary));
		//trigger_error(print_r($medIterator->toJsonArray(array('tradename','strength','unit','packsize','packtype','ndc')),true),E_USER_NOTICE);
		$matches = $medIterator->toJsonArray('id',array('tradename','strength','unit','packsize','packtype','ndc','inFormulary','md5','fda_drugname'),true);
		if (count($matches) > 0) {
			$json->direct($matches);
			return;
		}
                $json->direct(false);
	}

	function ajaxDrugName24($tradename,$limit = 25) {
		$res = $this->lookupDrugName($tradename,(int)$limit);
		if ($res) {
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}
		return false;
	}
	
	function ajaxAllergy($tradename,$limit = 25) {
		$res = $this->lookupAllergy($tradename,(int)$limit);
		if ($res) {
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}
		return false;
	}
/*                $tradename = 'lipitor';
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $medIterator = new BaseMed24Iterator();
                $medIterator->setFilters(array('tradename' => $tradename, 'limit' => $limit));
                //trigger_error(print_r($medIterator->toJsonArray(array('tradename','strength','unit','packsize','packtype','ndc')),true),E_USER_NOTICE);
                $matches = $medIterator->toJsonArray('id',array('tradename','strength','unit','packsize','packtype','ndc'),true);
                if (count($matches) > 0) {
                        $json->direct($matches);
                        return;
                }
                $json->direct(array(false));
        }
*/
	function ajaxBasemed24DetailAction() {
                $pkey = (int) preg_replace('/[^A-Za-z0-9\-\%]+/','',file_get_contents('php://input'));
		//$pkey = 54702;
		$bm24dIter = new BaseMed24DetailIterator(null,false);
		$bm24dIter->setFilters(array('formulary' => 'default', 'pkey' => $pkey));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		//$bm24 = $bm24dIter->current();
		//$res = $this->lookupMedDetail($pkey);
		//$intRes = $this->lookupMedInteractions($pkey);
		$details = $bm24dIter->currentArray();
		if ($details['pkey'] > 0) {
			if (isset($details['hasLabel']) && strlen($details['hasLabel']) > 0 && $details['hasLabel'] == 1) {
				$details['has_xml_label'] = "true";
			}
			else {
				$details['has_xml_label'] = "false";
			}
			//unset($details['labelfile']);
			$details['interactions'] = "";
			/*if ($intRes) {
				while ($ixdetails = $intRes->fetch(PDO::FETCH_ASSOC)) {
					$vaix = array();
					$vaix['interact_pkey'] = $ixdetails['pkey'];
					$vaix['tradename'] = $ixdetails['tradename'];
					$vaix['drugname'] = $ixdetails['fda_drugname'];
					$vaix['strength'] = $ixdetails['strength'];
					$vaix['unit'] = $ixdetails['unit'];
					$vaix['level'] = $ixdetails['level'];
					$details['interactions'][] = array("va_interaction" => $vaix);
				}
			}*/
			//trigger_error(print_r($details,true),E_USER_ERROR);
			return $json->direct($details,true);
		}
		return $json->direct(false);
	}

	function basemed24LabelAction() {
		$pkey = (int) $this->_getParam('pkey');
		$data = array();
		$data['pkey'] = (int)$pkey;
		$data['format'] = 'html';
                $xmlr = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><medication></medication>');
                $n = $xmlr->addChild("data");

                $label = $this->lookupMedLabel($data['pkey']);
                $med = $n->addChild('medicationLabel');
                $doc = new DOMDocument();
                $doc->loadXML($label->asXML());
                $xp = new XsltProcessor();
                $xsl = new DomDocument();
                $xsl->load('/var/www/ch30/chmed/includes/spl-common.xsl');
                $xp->importStyleSheet($xsl);
                $this->view->content = $xp->transformToXML($doc);
	}

	public function lookupDrugName($tradename,$limit) {
                $sql = "select bm24.pkey as id, bm24.*
                from chmed.basemed24 bm24
                where (fda_drugname like " . DB::quote($tradename) . ") group by fda_drugname order by fda_drugname desc limit " . $limit;
                $res = DB::query($sql);
                return $res;
        }
	
	public function lookupAllergy($tradename,$limit) {
                $sql = "select 0 as id, name
                from chmed.allergies
                where name like " . DB::quote($tradename) . " order by name desc limit " . $limit;
                $res = DB::query($sql);
                return $res;
        }

	public function ajaxListInteractionsAction() {
		$severeNotify = PermissionTemplate::hasPermission('medication-alerts','severe-notification')?true:false;
		$criticalNotify = PermissionTemplate::hasPermission('medication-alerts','critical-notification')?true:false;
		$allergyNotify = PermissionTemplate::hasPermission('medication-alerts','allergy-notification')?true:false;

		$personId = (int)$this->_getParam('personId');
		$md5 = preg_replace('/[^A-Za-z0-9]/','',$this->_getParam('md5'));
		$vaclass = preg_replace('/[^A-Za-z0-9]/','',$this->_getParam('vaclass'));

		// regular allergies search
		$interactionIterator = new BaseMed24InteractionIterator();
		$interactionIterator->setFilters(array('personId'=>$personId,'md5'=>$md5));
		$regularAllergies = $interactionIterator->toJsonArray('hipaa_ndc',array('tradename','fda_drugname','notice'));
		$tmpArray = $regularAllergies;
		$regularAllergies = array();
		foreach ($tmpArray as $key=>$value) {
			// notice: S, C, Y, ^
			if ((!$severeNotify && $value['data'][2] == 'SIGNIFICANT') ||
			    (!$criticalNotify && $value['data'][2] == 'CRITICAL')) continue;
			$regularAllergies[] = $value;
		}

		$listSymptoms = array();
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SYMPTOM_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSymptoms[$enum->key] = $enum->name;
		}

		$listSeverities = array();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SEVERITY_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSeverities[$enum->key] = $enum->name;
		}

		// drug class search
		$patientAllergyIterator = new PatientAllergyIterator();
		$patientAllergyIterator->setFilters(array('patientId'=>$personId,'enteredInError'=>0,'drugAllergy'=>$vaclass,'reactionType'=>'Drug Class Allergy'));
		$drugClassAllergies = array();
		foreach($patientAllergyIterator as $allergy)  {
			if (!$allergyNotify) break;
			/*if ((!$severeNotify && $allergy->severity == 'SEVERE') ||
			    (!$criticalNotify && $allergy->severity == 'MOD')) continue;*/
			$symptoms = explode(',',$allergy->symptoms);
			$symptom = array();
			foreach ($symptoms as $sym) {
				$symptom[] = $listSymptoms[$sym];
			}
			$tmpArray = array();
			$tmpArray['id'] = $allergy->patientAllergyId;
			$tmpArray['data'][] = $allergy->causativeAgent;
			$tmpArray['data'][] = $allergy->reactionType;
			$tmpArray['data'][] = $listSeverities[$allergy->severity].' - '.implode(',',$symptom);
			$drugClassAllergies[] = $tmpArray;
		}

		// specific drug search
		$patientAllergyIterator->setFilters(array('patientId'=>$personId,'enteredInError'=>0,'drugAllergy'=>$md5,'reactionType'=>'Specific Drug Allergy'));
		$specificDrugAllergies = array();
		foreach($patientAllergyIterator as $allergy)  {
			if (!$allergyNotify) break;
			/*if ((!$severeNotify && $allergy->severity == 'SEVERE') ||
			    (!$criticalNotify && $allergy->severity == 'MOD')) continue;*/
			$symptoms = explode(',',$allergy->symptoms);
			$symptom = array();
			foreach ($symptoms as $sym) {
				$symptom[] = $listSymptoms[$sym];
			}
			$tmpArray = array();
			$tmpArray['id'] = $allergy->patientAllergyId;
			$tmpArray['data'][] = $allergy->causativeAgent;
			$tmpArray['data'][] = $allergy->reactionType;
			$tmpArray['data'][] = $listSeverities[$allergy->severity].' - '.implode(',',$symptom);
			$specificDrugAllergies[] = $tmpArray;
		}

		$interactions = array_merge_recursive($regularAllergies,$drugClassAllergies,$specificDrugAllergies);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$json->direct(array('rows' => $interactions));
	}

	public function lookupMedLabel($pkey) {
                $sql = "select * from chmed.basemed24labels where pkey = " . (int)$pkey;
		$db = Zend_Registry::get('dbAdapter'); 
                $res = $db->query($sql);
                //print_r($res->fetchAll(PDO::FETCH_ASSOC));
                foreach ($res->fetchAll() as $row) {
                        if (substr($row['labelfile'],-4) == ".xml") {
				if (file_exists("/var/www/ch30/chmed/documents/basemed24labels/" . $row['labelfile'])) {
                                $xml = simplexml_load_file("/var/www/ch30/chmed/documents/basemed24labels/" . $row['labelfile']);
                                return $xml;
			}
                        }
                }
                return false;
        }
}


?>
