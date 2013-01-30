<?php
/*****************************************************************************
*       TemplateTextController.php
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


class TemplateTextController extends WebVista_Controller_Action {

	public function indexAction()    {
		$personId = (int)$this->_getParam('personId');
		$clinicalNoteId = (int)$this->_getParam('clinicalNoteId');
		trigger_error('ttpid: ' . $personId,E_USER_NOTICE);

                $cn = new ClinicalNote();
                $cn->clinicalNoteId = (int)$clinicalNoteId;
                $cn->populate();
                $templateId = $cn->clinicalNoteTemplateId;
                $cnTemplate = new ClinicalNoteTemplate();
                $cnTemplate->clinicalNoteTemplateId = (int)$templateId;
                $cnTemplate->populate();
                $xml = simplexml_load_string($cnTemplate->template);
		$objective = '';
		foreach ($xml as $question) {
			foreach($question as $key => $item) {
				if ($key != "dataPoint") {
					continue;
				}
				$namespace = (string)$item->attributes()->template;
				// extract the nsdr: format
				preg_match('/{nsdr:(.*)}/',$namespace,$matches);
				if (isset($matches[1])) {
					$namespace = str_replace('[selectedPatientId]',$personId,$matches[1]);
					$result = NSDR2::populate($namespace);
					$objective .= $result;
				}
			}
		}

		$this->view->objective = $objective;
		$filter = array('personId' => $personId);
		$pl = new ProblemList();
		$pli = $pl->getIterator();
		$pli->setFilters($filter);
		$this->view->problemListIterator = $pli;
	}

	public function templatedTextAction() {
		$personId = $this->_getParam('personId',0);
		$templateId = $this->_getParam('templateId',0);
		$templateName = $this->_getParam('templateName');
		$templatedText = new TemplatedText();
		if ($templateId > 0) {
			$templatedText->templateId = $templateId;
			$templatedText->populate();
		}
		else {
			$templatedText->populateByName($templateName);
		}
		$template = $templatedText->template;
		preg_match_all('/{nsdr:(.*)}/',$template,$matches);
		if (count($matches[1]) > 0) {
		        foreach ($matches[1] as $val) {
				$namespace = str_replace('[selectedPatientId]',$personId,$val);
				if (Zend_Registry::get('memcacheStatus') === 0) {
					$resultValue = __("Memcache server not started");
				}
				else {
					$resultValue = NSDR2::populate($namespace);
				}
				$template = preg_replace('/{nsdr:(.*)}/',$resultValue,$template,1);
		        }
		}
		$this->view->template = $template;
		$this->render();
	}

}
