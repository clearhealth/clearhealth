<?php
/*****************************************************************************
*       ReportBase.php
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


class ReportBase extends WebVista_Model_ORM {

	protected $reportBaseId;
	protected $guid = '';
	protected $displayName = '';
	protected $systemName = '';
	protected $filters = '';

	protected $reportFilters = array();
	protected $reportQueries = array();
	protected $reportViews = array();

	protected $_primaryKeys = array('reportBaseId');
	protected $_table = 'reportBase';

	const FILTER_OPTIONS_SINGLE_QUOTES = 'sq';
	const FILTER_OPTIONS_DOUBLE_QUOTES = 'dq';
	const FILTER_OPTIONS_LOWERCASE = 'lc';
	const FILTER_OPTIONS_UPPERCASE = 'uc';
	const FILTER_OPTIONS_QUOTE_ESCAPE_STRING = 'qes';

	const FILTER_TYPE_DATE = 'DATE';
	const FILTER_TYPE_STRING = 'STRING';
	const FILTER_TYPE_ENUM = 'ENUM';
	const FILTER_TYPE_QUERY = 'QUERY';
	const FILTER_TYPE_LIST_BUILDING = 'Building List';
	const FILTER_TYPE_LIST_PRACTICE = 'Practice List';
	const FILTER_TYPE_LIST_PROVIDER = 'Provider List';
	const FILTER_TYPE_LIST_ROOM = 'Room List';
	const FILTER_TYPE_LIST_BUILDING_PREF = 'My Prefs Building List';
	const FILTER_TYPE_LIST_ROOM_PREF = 'My Prefs Room List';
	const FILTER_TYPE_LIST_PROVIDER_PREF = 'My Prefs Provider List';
	const FILTER_TYPE_SPECIAL = 'Special';

	public static $_filterOptions = array(
		'sq'=>'Single Quotes',
		'dq'=>'Double Quotes',
		'lc'=>'Lower Case',
		'uc'=>'Upper Case',
		'qes'=>'Quote & Escape String',
	);

	public static $_specialOptions = array(
		'currentPracticeId'=>'My Prefs->Current Practice Id',
		'currentBuildingId'=>'My Prefs->Current Building Id',
		'currentRoomId'=>'My Prefs->Current Room Id',
		'currentUserId'=>'Current User Id',
	);

	public function populate() {
		$ret = parent::populate();
		$this->reportFilters = array();
		if (strlen($this->filters) > 0) {
			$this->reportFilters = unserialize($this->filters);
		}
		return $ret;
	}

	public function persist() {
		$db = Zend_Registry::get('dbAdapter');
		$reportBaseId = (int)$this->reportBaseId;
		if ($this->_persistMode == self::DELETE) {
			try {
				$db->delete($this->_table,'reportBaseId = '.$reportBaseId);
				$db->delete('reportQueries','reportBaseId = '.$reportBaseId);
				$db->delete('reportViews','reportBaseId = '.$reportBaseId);
				return true;
			}
			catch (Exception $e) {
				return false;
			}
		}
		$data = $this->toArray();
		unset($data['reportFilters']);
		unset($data['reportQueries']);
		unset($data['reportViews']);
		if (strlen($data['guid']) <= 0) {
			$data['guid'] = uniqid('',true);
		}
		if ($reportBaseId > 0) {
			$ret = $db->update($this->_table,$data,'reportBaseId = '.$reportBaseId);
		}
		else {
			$this->reportBaseId = WebVista_Model_ORM::nextSequenceId();
			$data['reportBaseId'] = $this->reportBaseId;
			$ret = $db->insert($this->_table,$data);
		}
		return $ret;
	}

	public static function getFilterTypes() {
		$filterTypes = array();
		$filterTypes[self::FILTER_TYPE_DATE] = '';
		$filterTypes[self::FILTER_TYPE_STRING] = array();
		$filterTypes[self::FILTER_TYPE_ENUM] = array();
		$queries = array();
		$reportQuery = new ReportQuery();
		$reportQueryIterator = $reportQuery->getIterator();
		foreach ($reportQueryIterator as $query) {
			$queries[] = $query->query;
		}
		$filterTypes[self::FILTER_TYPE_QUERY] = $queries;
		$filterTypes[self::FILTER_TYPE_LIST_BUILDING] = array();
		$filterTypes[self::FILTER_TYPE_LIST_PRACTICE] = array();
		$filterTypes[self::FILTER_TYPE_LIST_PROVIDER] = array();
		$filterTypes[self::FILTER_TYPE_LIST_ROOM] = array();
		$filterTypes[self::FILTER_TYPE_LIST_BUILDING_PREF] = array();
		$filterTypes[self::FILTER_TYPE_LIST_ROOM_PREF] = array();
		$filterTypes[self::FILTER_TYPE_LIST_PROVIDER_PREF] = array();
		$filterTypes[self::FILTER_TYPE_SPECIAL] = array();
		return $filterTypes;
	}

	public static function getAllEnums($match,$parentId=0) {
		$db = Zend_Registry::get('dbAdapter');
		$enumeration = new Enumeration();
		$enumerationsClosure = new EnumerationsClosure();
		$whrSelect = $db->select()
				->from(array('cc'=>$enumerationsClosure->_table),'cc.descendant')
				->where('cc.ancestor != cc.descendant')
				->where('cc.descendant = c.ancestor');
		$sqlSelect = $db->select()
				->from(array('e'=>$enumeration->_table))
				->join(array('c'=>$enumerationsClosure->_table),'e.enumerationId = c.ancestor',array())
				->where('e.name LIKE ?',$match.'%')
				->where('c.ancestor = c.descendant')
				->where('c.depth = 0')
				->where('c.ancestor NOT IN ?',$whrSelect)
				->order('c.weight ASC');
		if ($parentId > 0) {
			$sqlSelect->where('c.ancestor = ?',(int)$parentId);
		}
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		return $enumeration->getIterator($sqlSelect);
	}

	public static function getEnumById($enumId) {
		$enumerationClosure = new EnumerationsClosure();
		$enumerationClosureIterator = $enumerationClosure->getAllDescendants($enumId);
		foreach ($enumerationClosureIterator as $enum) {
			$children = self::getEnumById($enum->enumerationId);
			if (!count($children) > 0) {
			}
			$ret[$enum->enumerationId] = $enum->name;
		}
	}

	public function executeQueries(Array $filters,ReportView $view) {
		$config = Zend_Registry::get('config');
		$dbName = $config->database->params->dbname;
		$db = Zend_Registry::get('dbAdapter');
		$ret = array();

		$filterQueries = array();
		$reportFilters = array();
		foreach ($this->reportFilters as $key=>$filter) {
			if ($filter->type == 'QUERY') $filterQueries[$filter->query] = $filter->query;
			$reportFilters['{{'.$filter->name.'}}'] = $filter;
		}
		//trigger_error(print_r($reportFilters,true),E_USER_NOTICE);
		$reportQuery = new ReportQuery();
		$reportQueryIterator = $reportQuery->getIteratorByBaseId($this->reportBaseId);
		$viewColumnDefinitions = array();
		$unserializedColumnDefinitions = $view->unserializedColumnDefinitions;
		if ($unserializedColumnDefinitions === null) $unserializedColumnDefinitions = array();
		foreach ($unserializedColumnDefinitions as $id=>$value) {
			$viewColumnDefinitions[$value->queryId][$id] = $value;
		}
		//file_put_contents('/tmp/columns.txt',print_r($columnDefinitions,true));
		foreach ($reportQueryIterator as $query) {
			if (isset($filterQueries[$query->reportQueryId])) continue; // report query associated with filter not included
			$row = array();
			$row['reportQuery'] = $query->toArray();
			$row['reportQuery']['customColNames'] = (boolean)$view->customizeColumnNames;
			$queryValue = $query->query;
			$tokens = $this->_extractTokens($queryValue);
			if (isset($tokens[0])) { // tokens defined
				// check for undefined/orphaned filter
				$undefinedTokens = array();
				foreach ($tokens as $token) {
					if (!isset($reportFilters[$token])) {
						$undefinedTokens[] = $token;
					}
				}
				if (isset($undefinedTokens[0])) {
					$error = 'Query "'.$query->displayName.'" contains undefined tokens: '.implode(', ',$undefinedTokens);
					$row['error'] = $error;
					trigger_error($error,E_USER_NOTICE);
					$ret[] = $row;
					continue;
				}
				$queryValue = $this->_applyFilters($filters,$queryValue);
			}
			$columnDefinitions = array();
			if (isset($viewColumnDefinitions[$query->reportQueryId])) {
				$columnDefinitions = $viewColumnDefinitions[$query->reportQueryId];
			}
			else {
				foreach ($viewColumnDefinitions as $id=>$value) {
					foreach ($value as $i=>$v) {
						if ($v->queryId != 0 || $v->queryName != $query->reportQueryId) continue;
						$columnDefinitions[$i] = $v;
					}
				}
			}
			//file_put_contents('/tmp/columns.txt',$query->reportQueryId.' = '.print_r($columnDefinitions,true),FILE_APPEND);
			$columnDefinitionLen = count($columnDefinitions);
			switch ($query->type) {
				case ReportQuery::TYPE_SQL:
					//file_put_contents('/tmp/query.sql',$queryValue);
					trigger_error($queryValue,E_USER_NOTICE);
					// extract and execute defined SQL variables
					$tmp = explode(';',$queryValue);
					if (count($tmp) > 0) {
						foreach ($tmp as $key=>$line) {
							$line = trim($line);
							if (substr($line,0,5) == 'SET @') {
								unset($tmp[$key]);
								trigger_error('EXECUTING SQL: '.$line);
								$db->query($line);
							}
						}
						$queryValue = implode(';',$tmp);
					}
					try {
						$results = array();
						$headers = array();
						$stmt = $db->query($queryValue,array(),Zend_Db::FETCH_NUM);
						$columnInfo = array();
						$rowCount = $stmt->rowCount();
						for ($i = 0; $i < $rowCount; $i++) {
							$fetchRow = $stmt->fetch(Zend_Db::FETCH_NUM,null,$i);
							if ($i == 0) {
								for ($ctr=0,$rowLen=count($fetchRow);$ctr<$rowLen;$ctr++) {
									$columnMeta = $stmt->getColumnMeta($ctr);
									if ($view->customizeColumnNames && $columnDefinitionLen > 0) {
										$resultSetName = $dbName.'.'.$columnMeta['table'].'.'.$columnMeta['name'];
										foreach ($columnDefinitions as $id=>$mapping) { // id, queryId, queryName, resultSetName, displayName, transform
											if ($mapping->resultSetName == $resultSetName) {
												$headers[$ctr] = $mapping->displayName;
												$columnInfo[$ctr] = $mapping;
												break;
											}
										}
									}
									else {
										$headers[$ctr] = $columnMeta['name'];
									}
								}
							}
							$tmpResult = array('id'=>$i,'data'=>array());
							if ($view->customizeColumnNames && $columnDefinitionLen > 0) {
								$tmp = array();
								foreach ($columnInfo as $index=>$mapping) {
									$tmp[$mapping->displayName] = $this->_applyTransforms($mapping->transforms,$fetchRow[$index]);
								}
								$tmpHeaders = $headers;
								$headers = array();
								foreach ($columnDefinitions as $id=>$mapping) { // id, queryId, queryName, resultSetName, displayName, transform
									$tmpResult['data'][] = $tmp[$mapping->displayName];
									foreach ($tmpHeaders as $key=>$header) {
										if ($header != $mapping->displayName) continue;
										$headers[] = $header;
										unset($tmpHeaders[$key]);
										break;
									}
								}
							}
							else {
								foreach ($headers as $index=>$header) {
									$tmpResult['data'][] = $fetchRow[$index];
								}
							}
							$results[] = $tmpResult;
						}
						$row['headers'] = $headers;
						$row['rows'] = $results;
					}
					catch (Exception $e) {
						$uniqErrCode = uniqid();
						$row['error'] = 'There was a problem executing query: '.$query->displayName.'. Contact your administrator with error code: '.$uniqErrCode;
						trigger_error('Exception error ('.$uniqErrCode.'): '.$e->getMessage(),E_USER_NOTICE);
						trigger_error('SQL Query ('.$uniqErrCode.'): '.$queryValue,E_USER_NOTICE);
					}
					$ret[] = $row;
					break;
				case ReportQuery::TYPE_NSDR:
					$nsdr = explode("\n",$queryValue);
					$nsdrResults = array();
					$allDefaults = true;
					foreach ($nsdr as $key=>$value) {
						$value = $this->_applyFilters($filters,$value);
						$resultSetName = ReportView::extractNamespace($value);
						//$displayName = ReportView::metaDataPrettyName($resultSetName);
						$nsdrResult = NSDR2::populate($value);
						// NOTE: return of NSDR2::populate() can be either of the following: string, associative array (all keys are treated as column names and values as row/ cell values), and multidimensional array (contains multiple associative arrays)
						$data = array($resultSetName,$nsdrResult);
						if (is_array($nsdrResult)) {
							if (array_key_exists(0,$nsdrResult)) {
								if (!is_array($nsdrResult[0])) {
									$data[1] = implode("\n",$nsdrResult);
								}
								else { // multi dimensional array
									$allDefaults = false;
									foreach ($nsdrResult as $index=>$results) {
										$data = array();
										$row['headers'] = array();
										foreach ($results as $k=>$v) {
											$row['headers'][] = ReportView::metaDataPrettyName($k);
											$data[] = $v;
										}
										$nsdrResults[$resultSetName][] = array('id'=>$key.$index,'data'=>$data);
									}
									continue;
								}
							}
							else {
								$allDefaults = false;
								$row['headers'] = array();
								$data = array();
								foreach ($nsdrResult as $k=>$v) {
									$row['headers'][] = ReportView::metaDataPrettyName($k);
									$data[] = $v;
								}
							}
						}
						$nsdrResults[$resultSetName][] = array('id'=>$key,'data'=>$data);
					}
					if ($allDefaults) {
						$row['headers'] = array('Name','Value');
					}
					/*else {
						if (isset($row['headers'][0]) && $row['headers'][0] != 'Name') $row['headers'][0] .= ' / Name';
						if (isset($row['headers'][1]) && $row['headers'][1] != 'Value') $row['headers'][1] .= ' / Value';
					}*/

					if ($view->customizeColumnNames && $columnDefinitionLen > 0) {
						foreach ($columnDefinitions as $id=>$mapping) { // id, queryId, queryName, resultSetName, displayName, transform
							$displayName = $mapping->resultSetName;
							if (!isset($nsdrResults[$displayName]) || 
							    (($mapping->queryId != 0 && $mapping->queryId != $query->reportQueryId) || ($mapping->queryId == 0 && $mapping->queryName != $query->reportQueryId)) &&
							    $mapping->queryName != $query->displayName) continue;
							//$displayName = ReportView::metaDataPrettyName($mapping->resultSetName);
							foreach ($nsdrResults[$displayName] as $key=>$value) {
								$data = $nsdrResults[$displayName][$key]['data'];
								if (!array_key_exists(0,$data)) continue;
								foreach ($data as $k=>$v) {
									if ($k == 0 && $v == $displayName) {
										$v = $mapping->displayName;
									}
									else {
										$v = $this->_applyTransforms($mapping->transforms,$v);
									}
									$nsdrResults[$displayName][$key]['data'][$k] = $v;
								}
								$data = $nsdrResults[$displayName][$key]['data'][1];
							}
						}
					}
					$results = array();
					foreach ($nsdrResults as $result) {
						foreach ($result as $key=>$value) {
							$results[] = $value;
						}
					}
					$row['rows'] = $results;
					$ret[] = $row;
					break;
			}
		}
		return $ret;
	}

	protected function _extractTokens($data) {
		$tokens = array();
		$patterns = '/[^\\\\](\{\{[^\}]*[^\\\\]\}\})/';
		if(preg_match_all($patterns,$data,$matches)) {
			$tokens = $matches[1];
		}
		return $tokens;
	}

	protected function _applyFilters(Array $filters,$value) {
		$db = Zend_Registry::get('dbAdapter');
		foreach ($this->reportFilters as $key=>$filter) {
			$content = '';
			if ((isset($filters[$key]) && strlen($filters[$key]) > 0) || $filter->type == self::FILTER_TYPE_SPECIAL) {
				$content = $filters[$key];
				switch ($filter->type) {
					case self::FILTER_TYPE_DATE:
						$content = date('Y-m-d',strtotime($content));
						break;
					case self::FILTER_TYPE_STRING:
						break;
					case self::FILTER_TYPE_ENUM:
						/*$enumeration = new Enumeration();
						$enumeration->enumerationId = (int)$content;
						$enumeration->populate();
						$content = $enumeration->name;*/
						break;
					case self::FILTER_TYPE_QUERY:
						/*$reportQuery = new ReportQuery();
						$reportQuery->reportQueryId = (int)$content;
						$reportQuery->populate();
						$content = $reportQuery->query;*/
						break;
					case self::FILTER_TYPE_SPECIAL:
						$room = User::myPreferencesLocation();
						if ($filter->special == 'currentPracticeId') {
							$content = (int)$room->building->practiceId;
						}
						else if ($filter->special == 'currentBuildingId') {
							$content = (int)$room->buildingId;
						}
						else if ($filter->special == 'currentRoomId') {
							$content = (int)$room->roomId;
						}
						else if ($filter->special == 'currentUserId') {
							$content = (int)Zend_Auth::getInstance()->getIdentity()->personId;
						}
						break;
				}
			}
			$options = $filter->options;
			foreach ($options as $optId=>$optValue) {
				switch ($optId) {
					case self::FILTER_OPTIONS_DOUBLE_QUOTES:
						$content = '"'.$content.'"';
						break;
					case self::FILTER_OPTIONS_SINGLE_QUOTES:
						$content = "'".$content."'";
						break;
					case self::FILTER_OPTIONS_UPPERCASE:
						$content = strtoupper($content);
						break;
					case self::FILTER_OPTIONS_LOWERCASE:
						$content = strtolower($content);
						break;
					case self::FILTER_OPTIONS_QUOTE_ESCAPE_STRING:
						$content = $db->quote($content);
						break;
				}
			}
			$value = preg_replace('/\{\{'.$filter->name.'\}\}/',$content,$value);
		}
		return $value;
	}

	protected function _applyTransforms(Array $transforms,$data) {
		$ret = $data;
		if (!$transforms) {
			return $ret;
		}
		foreach ($transforms as $id=>$transform) { // id, displayName, systemName, options
			$options = array();
			if (isset($transform->options)) {
				$options = $transform->options;
			}
			switch ($transform->systemName) {
				case 'ucase':
					$ret = strtoupper($ret);
					break;
				case 'lcase':
					$ret = strtolower($ret);
					break;
				case 'ucwords':
					$ret = ucwords($ret);
					break;
				case 'squote':
					$ret = "'".$ret."'";
					break;
				case 'dquote':
					$ret = '"'.$ret.'"';
					break;
				case 'pad':
					$ret = str_pad($ret,$options['len'],$options['char'],$options['type']);
					break;
				case 'truncate':
					$ret = substr($ret,0,$options['len']);
					break;
				case 'customLink':
					$href = str_replace('{{value}}',$ret,$options['href']);
					$ret = '<a href="'.$href.'" onclick="'.$options['onclick'].'" target="_blank">'.$ret.'</a>';
					break;
				case 'regex':
					$ret = preg_replace('/'.$options['pattern'].'/',$options['replacement'],$ret);
					break;
				case 'enumLookup':
					$enumKey = $options['enumKey'];
					$enumValue = $options['enumValue'];
					$direction = $options['direction'];
					$enumeration = new Enumeration();
					$enumeration->enumerationId = (int)$enumKey;
					$enumeration->populate();
					$enumerationClosure = new EnumerationClosure();
					$descendants = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1); // get the first level descendants
					foreach ($descendants as $descendant) {
						if ($direction == 'displayName' && ($descendant->enumerationId == $ret || $descendant->key == $ret)) {
							$ret = $descendant->name;
							break;
						}
						else if ($direction == 'key' && $descendant->name == $ret) {
							$ret = $descendant->key;
							break;
						}
					}
					break;
				case 'dateFormat':
					$ret = date($options['format'],strtotime($ret));
					break;
				case 'total':
					break;
			}
		}
		return $ret;
	}

	public function getUnserializedFilters() {
		$ret = null;
		if (strlen($this->filters) > 0) {
			$ret = unserialize($this->filters);
		}
		return $ret;
	}

	public function setSerializedFilters($value) {
		$this->filters = serialize($value);
	}

	public static function generateSystemName($value) {
		$systemName = preg_replace('/[^A-Z0-9 ]/i','',strtolower($value));
		$systemName = str_replace(' ','',ucwords($systemName));
		$systemName = lcfirst($systemName);
		if (is_numeric(substr($systemName,0,1))) {
			$systemName = '_'.$systemName;
		}
		return $systemName;
	}

	public static function generateResults($viewId,$filterParams) {
		$reportView = new ReportView();
		$reportView->reportViewId = $viewId;
		$reportView->populate();
		$reportBase = $reportView->reportBase;

		$filterBase = $reportView->reportBase->unserializedFilters;
		$filters = array();
		if ($filterBase !== null) {
			foreach ($filterBase as $key=>$filter) {
				$value = $filter->defaultValue;
				if (isset($filterParams[$key]) && strlen($filterParams[$key]) > 0) {
					$value = $filterParams[$key];
				}
				$filters[$key] = $value;
			}
		}
		$data = $reportBase->executeQueries($filters,$reportView);
		$ret = array('data'=>$data,'type'=>$reportView->showResultsIn,'customizeColumnNames'=>$reportView->customizeColumnNames);
		if (strlen($reportView->showResultsIn) <= 0 || $reportView->showResultsIn != 'grid') {
			$showResultsOptions = $reportView->unserializedShowResultsOptions;
			switch ($reportView->showResultsIn) {
				case 'file':
					$separator = $showResultsOptions['separator'];
					$lineEndings = $showResultsOptions['lineEndings'];
					$resultsSeparator = $showResultsOptions['resultsSeparator'];
					$includeHeaderRow = (isset($showResultsOptions['includeHeaderRow']) && $showResultsOptions['includeHeaderRow'])?true:false;
					$queriesData = array();
					$sep = "";
					foreach ($data as $queryData) {
						$contents = array();
						if ($includeHeaderRow) {
							$contents[] = implode($separator,$queryData['headers']);
						}
						foreach ($queryData['rows'] as $row) {
							$contents[] = implode($separator,$row['data']);
						}
						switch ($lineEndings) {
							case 'windows':
								$sep = "\r\n";
								break;
							case 'mac':
								$sep = "\r";
								break;
							case 'linux':
								$sep = "\n";
								break;
							default:
								$sep = "";
								break;
						}
						$queriesData[] = implode($sep,$contents);
					}
					$flatfile = implode($sep.$resultsSeparator.$sep,$queriesData);
					$ret['value'] = $flatfile;
					return $ret;
				case 'xml':
					$xml = self::_generateResultsXML($data);
					$ret['value'] = $xml->asXML();
					return $ret;
				case 'pdf':
					$xml = self::_generateResultsXML($data); //PdfController::toXML($data,'Report',null);
					$xmlData = $xml->asXML();
					$xmlData = preg_replace('/dataNode=/','xfa:dataNode=',$xmlData);
					$xmlData = preg_replace('/<\?.*\?>/','',$xmlData);

					$ret['value'] = array('attachmentReferenceId'=>$reportView->reportViewId,'xmlData'=>$xmlData);
					return $ret;
				case 'graph': // to be implemented
					break;
				case 'pqri':
					$ret['value'] = NQFPQRIReport::generatePQRIXML($data);
					return $ret;
			}
		}
		return $ret;
	}

	protected static function _generateResultsXML($data) {
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><report/>');
		foreach ($data as $rows) {
			$systemName = ReportBase::generateSystemName($rows['reportQuery']['displayName']);
			$xmlQuery = $xml->addChild($systemName);
			$ctr = 0;
			$tagNames = array();
			foreach ($rows['headers'] as $key=>$value) {
				$tagNames[$key] = $value;
			}
			foreach ($rows['rows'] as $row) {
				$xmlRow = $xmlQuery->addChild('row');
				$xmlRow->addAttribute('sequence',$ctr++);
				foreach ($row['data'] as $key=>$value) {
					$xmlRow->addChild($tagNames[$key],$value);
				}
			}
		}
		return $xml;
	}

	protected static function _strFill($str,$len,$type=0) {
		// $type: 0/default = LJBF, 1 = RJZF
		switch ($type) {
			case 1:
				$str = str_pad($str,$len,'0',STR_PAD_LEFT);
				break;
			default:
				$str = str_pad($str,$len);
				break;
		}
		return $str;
	}

	public static function mergepdfset(SimpleXMLElement $xml,$data) {
		$error = null;
		$pdfset = '/usr/local/bin/pdfset';
		if (!file_exists($pdfset)) $error = 'pdfset binary does not exists';
		if (!is_executable($pdfset)) $error = 'pdfset binary is not executable';
		if (!$data) $error = 'Failed to merge pdf, data is empty';
		if ($error !== null) {
			trigger_error($error);
			throw new Exception($error);
		}
		$xml = preg_replace('/<\?.*\?>/','',$xml->asXML());

		$inputFile = tempnam('/tmp','tmp_');
		if (!file_put_contents($inputFile,$data)) {
			unlink($inputFile);
			$error = 'Failed to write '.$inputFile;
			trigger_error($error);
			throw new Exception($error);
		}
		$xmlFile = tempnam('/tmp','tmp_');
		if (!file_put_contents($xmlFile,$xml)) {
			unlink($inputFile);
			unlink($xmlFile);
			$error = 'Failed to write '.$xmlFile;
			trigger_error($error);
			throw new Exception($error);
		}
		$outputFile = tempnam('/tmp','tmp_');
		$cmd = "$pdfset -i $inputFile -X $xmlFile -o $outputFile";
		$output = array();
		$lastLine = exec($cmd,$output,$ret);
		if ($ret != 0) $error = implode("\n",$output);
		$content = file_get_contents($outputFile);
		// cleanup mess
		unlink($inputFile);
		unlink($outputFile);
		unlink($xmlFile);
		if ($error) {
			trigger_error($error);
			throw new Exception($error);
		}
		return $content;
	}

}
