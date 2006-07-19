<?php

class C_PersonPerson extends Controller
{
	function processDelete() {
		$ordo =& Celini::newORDO('PersonPerson', $this->GET->getTyped('id', 'int'));
		$ordo->drop();
		
		if ($this->GET->exists('embedded') && $this->GET->get('embedded') == 'true') {
			Celini::redirectURL($_SERVER['HTTP_REFERER']);
		}
	}
}

?>
