<?php
/*****************************************************************************
*       Report.php
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


class Report extends WebVista_Model_ORM {
	protected $id;
	protected $uuid;
	protected $name;
	protected $systemName;
	protected $reportQueries = array();
	protected $reportTemplates = array();
	protected $_primaryKeys = array('id');
	protected $_table = "reports";
	
	function __construct() {
		parent::__construct();
	}

	function setReportQueries(array $queries) {
		foreach($queries as $queryData) {
			$reportQuery = new ReportQuery();
			$reportQuery->populateWithArray($queries);
			$this->reportQueries[] = $reportQuery;
		}
	}
	
	function setTemplateQueries(array $templates) {
                foreach($templates as $templateData) {
                        $reportTemplate = new ReportTemplate();
                        $reportTemplate->populateWithArray($templates);
                        $this->reportTemplates[] = $reportTemplate;
                }
        }

	function setReportId($id) {
		$this->id = (int)$id;
		foreach ($this->reportQueries as $reportQuery) {
			$reportQuery->reportId = (int)$id;
		}
		foreach ($this->reportTemplates as $reportTemplate) {
                        $reportTemplate->reportId = (int)$id;
                }

	}
	function populate() {
		$ret = parent::populate();
		$db = Zend_Registry::get('dbAdapter'); 
		$repSelect = $db->select()
                        ->from('reportQueries')
                        ->joinUsing('reportsToQueries', "reportQueryId")
                        ->where('reportsToQueries.reportId =' . (int)$this->id);
                foreach($db->query($repSelect)->fetchAll() as $row) {
                        $rq = new ReportQuery();
			$rq->populateWithArray($row);
			$this->reportQueries[] = $rq;
                }

		$repSelect = $db->select()
                        ->from('reportTemplates')
                        ->joinUsing('reportsToTemplates', "reportTemplateId")
                        ->where('reportsToTemplates.reportId =' . (int)$this->id);
                foreach($db->query($repSelect)->fetchAll() as $row) {
                        $rt = new ReportTemplate();
                        $rt->populateWithArray($row);
                        $this->reportTemplates[] = $rt;
                }
		return $ret;
	}


    public function getReportList() {
        $db = Zend_Registry::get('dbAdapter');
        $select = $db->select();
        $select->from(array('t' => 'report_templates'), 
                      array('t.report_template_id', 't.name', 'r.id', 'r.label'))
               ->joinLeft(array('r' => 'reports'), 'r.id = t.report_id')
               ->order(array('t.report_id ASC'));

        $ret = array();
        if ($rowset = $db->fetchAll($select)) {
            $report = array();
            foreach ($rowset as $row) {
                if (!isset($report[$row['id']]['label'])) {
                    $report[$row['id']]['label'] = $row['label'];
                }
                if (!isset($report[$row['id']]['items'])) {
                    $report[$row['id']]['items'] = array();
                }
                $report[$row['id']]['items'][] = $row;
            }
            $ret = $report;
        }
        return  $ret;
    }

	public function getReportId() {
		return $this->id;
	}

}
