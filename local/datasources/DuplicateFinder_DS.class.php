<?php
set_time_limit(0);
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.clear-health.clearhealth
 */
class DuplicateFinder_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'DuplicateFinder_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	var $_pats = '';
	var $_searchtype = "levenshtein";	
	var $_threshold = 90;	
	
	function DuplicateFinder_DS() {
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
							pat.record_number,
							per.person_id,
							per.last_name,
							per.first_name,
							per.date_of_birth,
							per.identifier,
							'' as matches
							",
						'from' 	=> "
							person AS per
							INNER JOIN patient pat on pat.person_id = per.person_id
", 
						'where'	=> "",
						'orderby'	=> "per.last_name, per.first_name"
			),
			array(
				'record_number' => 'MRN',
				'first_name' => 'First Name',
				'last_name' => 'Last Name',
				'date_of_birth' => 'DOB',
				'identifier' => 'SSN',
				'matches' => 'Matches'
			)
		);
		
		//var_dump($this->preview());
		$this->_loadPats();
		$this->registerFilter('matches', array(&$this, '_levenshtein'));
	}
	function _loadPats() {
		$db = new clniDB();
                $sql = "select pat.record_number, per.person_id, per.last_name, per.first_name, per.date_of_birth, per.identifier from person per inner join patient pat on pat.person_id = per.person_id";
                $res = $db->execute($sql);
		$this->_pats = $res;

	}
	function _levenshtein($value,$row) {
		$match_string = '';
		$res = $this->_pats;
		$res->MoveFirst();
		$loop = 0;
		$threshold = 
		$count = 0;
                while ($res && !$res->EOF & $count <20) {
			if ($row['person_id'] == $res->fields['person_id']) {
			$res->moveNext();
			continue;
			}
			elseif (empty($res->fields['first_name']) || 
				empty($res->fields['last_name']) ||
				empty($res->fields['record_number']) ||
				$res->fields['date_of_birth'] == '0000-00-00' ||
				!$res->fields['identifier'] > 0
			
			) {
			$res->moveNext();
			continue;

			}
                        $score=0;
			if ($this->_searchtype == "levenshtein") {
                        $score+=levenshtein($row['first_name'],$res->fields['first_name']);
                        $score+=levenshtein($row['last_name'],$res->fields['last_name']);
                        $score+=levenshtein($row['date_of_birth'],$res->fields['date_of_birth']);
                        $score+=levenshtein($row['identifier'],$res->fields['identifier']);
			$score = 100 - $score;
			}
			elseif ($this->_searchtype == "metaphone") {
                        $score+= levenshtein(metaphone($row['first_name']),metaphone($res->fields['first_name']));
                        $score+= levenshtein(metaphone($row['last_name']),metaphone($res->fields['first_name']));
                        $score+= levenshtein(metaphone($row['date_of_birth']),metaphone($res->fields['first_name']));
                        $score+= levenshtein(metaphone($row['identifier']),metaphone($res->fields['first_name']));
			$score = 100 - $score;
			}
			else {
			return "";
			}
                	if ($score >= $this->_threshold) {
                		$match_string .=  "Score: " . $score . " MRN:" . $res->fields['record_number'] . " " .$res->fields['first_name'] . " " . $res->fields['last_name'] . " " . $res->fields['date_of_birth'] . ' ' . $res->fields['identifier'] . "<br>";
				$count++;
                	}
                	$res->moveNext();
			$loop++;
                }
		return $match_string;
	}
}
?>
