<?php
$loader->requireOnce('controllers/C_CRUD.class.php');

class C_SecondaryPractice extends C_CRUD
{
	/**
	 * A {@link Person} that is associated with controller.
	 *
	 * @var int
	 * @see C_User::actionEdit()
	 * @todo Figure out a way to "share" this without having it set via a public
	 */
	var $person = null;
	
	/**
	 * Contains a copy of {@link SecondaryPractice} that is being used by this
	 *
	 * @var SecondaryPractice|null
	 * @access private
	 */
	var $_ordo = null;
	var $_ordoName = 'SecondaryPractice';
	
	
	function actionDefault($secondaryPracticeId = 0) {
		$this->_ordo =& Celini::newORDO('SecondaryPractice', $secondaryPracticeId);
		if (!$this->_ordo->isPopulated()) {
			$this->_ordo->set('person_id', $this->person->get('id'));
		}
		
		$this->_initPerson();
		$ds =& $this->person->loadDatasource('SecondaryPractice');
		$grid =& new cGrid($ds);
		
		$this->view->assign_by_ref('secondaryPractice', $this->_ordo);
		$this->view->assign_by_ref('secondaryPracticeGrid', $grid);
		return $this->view->render('default.html');
	}
	
	function _initPerson() {
		if (!is_null($this->person)) {
			return;
		}
		
		$this->person =& Celini::newORDO('Person', $this->_ordo->get('person_id'));
	}
	
	
	function processRemove($secondaryPracticeId) {
		$ordo =& Celini::newORDO('SecondaryPractice', (int)$secondaryPracticeId);
		$ordo->drop();
		
		if ($this->GET->exists('redirect')) {
			Celini::redirectURL($_SERVER['HTTP_REFERER']);
		}
	}
}

?>
