<?php

$loader->requireOnce('includes/AbstractUserProfile.abstract.php');

class UserProfile extends AbstractUserProfile
{
	function UserProfile($userId = 0) {
		parent::AbstractUserProfile($userId);
	}
	
	
	/**
	 * Returns the ID of primary practice associated with this user
	 *
	 * @return int
	 */
	function getPrimaryPracticeId() {
		$this->_initPerson();
		return $this->_person->get('primary_practice_id');
	}
	
	/**
	 * Returns an array IDs of the practices associated with this user
	 *
	 * @return array
	 */
	function getPracticeIdList() {
		if (count($this->_practiceIds) > 0) {
			return $this->_practiceIds;
		}
		
		if (Auth::canI('override')) {
			$sql = "SELECT id AS practice_id FROM practices";
		}
		else {
			if ($this->getPrimaryPracticeId() != '') {
				$this->_practiceIds['primary'] = $this->getPrimaryPracticeId();
			}
			
			$qPersonId = $this->_db->quote($this->_user->get('person_id'));
			$sql = "SELECT practice_id FROM secondary_practice WHERE person_id = {$qPersonId}";
		}
		
		$result = $this->_db->execute($sql);
		
		while ($result && !$result->EOF) {
			$this->_practiceIds[] = $result->fields['practice_id'];
			$result->moveNext();
		}
		return $this->_practiceIds;
	}
	
	
	/**
	 * Return the user's default location ID
	 *
	 * If the user has permission to the 'override' action, return null
	 *
	 * @return int|null
	 */
	function getDefaultLocationId() {
		if (Auth::canI('override')) {
			return;
		}
		
		$this->_initUser();
		return $this->_user->get('default_location_id');
	}

	/**
	 * Returns an array $id=>$building of the practices associated with this user
	 *
	 * @return array
	 */
	function getBuildingNameList($forSelectedPractice=false) {
		$db = new clniDB();
		if (count($this->_practiceIds) == 0) {
			$this->getPracticeIdList();
		}
		if($forSelectedPractice !== false) {
			$sql = "SELECT b.id,b.name FROM practices p INNER JOIN buildings b ON b.practice_id=".$db->quote($this->getCurrentPracticeId());
		} elseif (Auth::canI('override')) {
			$sql = "SELECT b.id,b.name FROM practices p INNER JOIN buildings b ON b.practice_id=p.id";
		}
		else {
			$sql = "SELECT b.id,b.name FROM practices p INNER JOIN buildings b ON b.practice_id=p.id WHERE p.id IN (".implode(',',$this->_practiceIds).")";
		}
		$sql.=" ORDER BY p.name ASC,b.name ASC";
		$result = $db->getAssoc($sql);
		return $result;
	}

	/**
	 * Returns the user's currently selected practice ID
	 *
	 * @see M_Main::preProcess()
	 * @return int
	 */
	function getCurrentPracticeId() {
		if (!isset($_SESSION['defaultpractice'])) {
			$_SESSION['defaultpractice'] = $this->getPrimaryPracticeId();
		}
		return $_SESSION['defaultpractice'];
	}	
}

?>
