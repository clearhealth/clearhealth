<?php
/*****************************************************************************
*       XML2ORM.php
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

class XML2ORM {

	protected $_helpers = array(
		'<<@randomPatientId>>' => 'getRandomPatientId',
		'<<@randomProviderId>>' => 'getRandomProviderId',
		'<<@randomPracticeId>>' => 'getRandomPracticeId',
		'<<@randomBuildingId>>' => 'getRandomBuildingId',
		'<<@randomRoomId>>' => 'getRandomRoomId',
		'<<@randomRoutingId>>' => 'getRandomRoutingId',
	);

	public function getRandomId(ORM $orm,$cache=true) {
		static $ids = array();
		$id = 0;
		$class = get_class($orm);
		if (!isset($ids[$class]) || $cache === false) {
			$ids[$class] = array();
			$iterator = $orm->getIterator();
			foreach ($iterator as $item) {
				// we only need the first primary key
				$key = $item->_primaryKeys[0];
				$ids[$class][] = $item->$key;
			}
		}
		$len = count($ids[$class]);
		if ($len > 0) {
			$index = rand(0,($len-1));
			$id = $ids[$class][$index];
		}
		return $id;
	}

	public function getRandomPatientId($cache=true) {
		return $this->getRandomId(new Patient());
	}

	public function getRandomProviderId($cache=true) {
		return $this->getRandomId(new Provider());
	}

	public function getRandomPracticeId($cache=true) {
		return $this->getRandomId(new Practice());
	}

	public function getRandomBuildingId($cache=true) {
		return $this->getRandomId(new Building());
	}

	public function getRandomRoomId($cache=true) {
		return $this->getRandomId(new Room());
	}

	public function getRandomRoutingId($cache=true) {
		return $this->getRandomId(new Routing());
	}

	protected function _populateXML(SimpleXMLElement $xml,ORM $orm) {
		$fields = $orm->ORMFields();
		foreach ($xml as $tag=>$value) {
			if (in_array($tag,$fields)) {
				if ($orm->$tag instanceof ORM) {
					$children = $value->children();
					if ($children) {
						$this->_populateXML($children,$orm->$tag);
					}
				}
				else {
					$val = (string)$value;
					if (array_key_exists($val,$this->_helpers) && method_exists($this,$this->_helpers[$val])) {
						$helper = $this->_helpers[$val];
						$val = $this->$helper();
					}
					$orm->$tag = $val;
				}
			}
		}
	}

	public function convert($ormName,$xmlFile,Array $helpers = array()) {
		$ret = array();
		if (!class_exists($ormName)) {
			return $ret;
		}
		$orm = new $ormName();
		if (!$orm instanceof ORM) {
			return $ret;
		}

		$basePath = Zend_Registry::get('basePath');
		$basePaths = array();
		$basePaths[] = $basePath;
		$basePaths[] = $basePath.'/xml';
		$file = $xmlFile;
		while (!file_exists($file)) {
			$dir = array_pop($basePaths);
			if ($dir === null) {
				return $ret;
			}
			$file = $dir.'/'.$xmlFile;
		}
		$xmlFile = $file;

		if (count($helpers) > 0) {
			$this->_helpers = $helpers;
		}

		$xml = simplexml_load_file($xmlFile);
		foreach ($xml as $elem) {
			$orm = new $ormName();
			$this->_populateXML($elem,$orm);
			$ret[] = $orm;
		}
		return $ret;
	}

	public function persistORM($ormClass,$xmlFile) {
		$xml2orm = new XML2ORM();
		$rows = $this->convert($ormClass,$xmlFile);
		$ctr = 0;
		foreach ($rows as $row) {
			$ctr++;
			$row->persist();
		}
		return $ctr;
	}

}
