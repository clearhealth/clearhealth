<?php
$loader->requireOnce('datasources/DuplicateQueue_DS.class.php');
$loader->requireOnce('includes/clni/clniAudit.class.php');
class C_PatientMerge extends controller {
	var $mergeComplete = false;
	function actionList() {

		$ds =& new DuplicateQueue_DS();
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}

	function actionMerge() {
		if (!$this->mergeComplete) {
			$id = $this->GET->getTyped('dq_id','int');

			$dq =& Celini::newOrdo('DuplicateQueue',$id);
			$this->view->assign_by_ref('dq',$dq);
			$this->view->assign('FORM_ACTION',Celini::link('Merge').'dq_id='.$id);
			return $this->view->render('merge.html');
		}
	}

	function processMerge() {
		$id = $this->GET->getTyped('dq_id','int');

		$dq =& Celini::newOrdo('DuplicateQueue',$id);

		// find all the child subrecords
		$child =& Celini::newOrdo('Patient',$dq->get('child_id'));
		$parent =& Celini::newOrdo('Patient',$dq->get('child_id'));

		$fields = $child->metadata->listFields();
		$fields = array_merge($fields,$child->person->metadata->listFields());
		foreach($fields as $field) {
			$pv = $parent->get($field);
			if(empty($pv)) {
				$parent->set($field,$child->get($field));
			}
		}
		// add audit log notes
		ClniAudit::logOrdo($child,'update','Merge '.$child->value('patient').' into '.$parent->value('patient'));

		// storage
		$moved['patient record'] = 1;
		$moved['other int fields'] = $this->updateId('storage_int','foreign_key',$dq->get('parent_id'),$dq->get('child_id'));
		$moved['other date fields'] = $this->updateId('storage_date','foreign_key',$dq->get('parent_id'),$dq->get('child_id'));
		$moved['other string fields'] = $this->updateId('storage_string','foreign_key',$dq->get('parent_id'),$dq->get('child_id'));
		$moved['other text fields'] = $this->updateId('storage_text','foreign_key',$dq->get('parent_id'),$dq->get('child_id'));


		// addresses
		$moved['addresses'] = $this->updateId('person_address','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// numbers
		$moved['numbers'] = $this->updateId('person_number','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// person_person
		$moved['related people'] = $this->updateId('person_person','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// identifiers
		$moved['identifiers'] = $this->updateId('identifier','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// insured_relationships
		$moved['insurance programs'] = $this->updateId('insured_relationship','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// notes
		$moved['patient notes'] = $this->updateId('patient_note','patient_id',$dq->get('parent_id'),$dq->get('child_id'));

		// statictics
		$moved['patient statistics'] = $this->updateId('patient_statistics','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// chronic care codes
		$moved['patient chronic codes'] = $this->updateId('patient_chronic_code','patient_id',$dq->get('parent_id'),$dq->get('child_id'));

		// find encounters
		$moved['encounters'] = $this->updateId('encounter','patient_id',$dq->get('parent_id'),$dq->get('child_id'));

		// encounter people
		$moved['related encounter people'] = $this->updateId('encounter_person','person_id',$dq->get('parent_id'),$dq->get('child_id'));

		// appointments
		$moved['appointments'] = $this->updateId('appointment','patient_id',$dq->get('parent_id'),$dq->get('child_id'));
		
		// payment_plans
		$moved['payment plans'] = $this->updateId('patient_payment_plan','patient_id',$dq->get('parent_id'),$dq->get('child_id'));

		// account notes
		$moved['account notes'] = $this->updateId('account_note','patient_id',$dq->get('parent_id'),$dq->get('child_id'));
		
		// forms
		$moved['forms'] = $this->updateId('form_data','external_id',$dq->get('parent_id'),$dq->get('child_id'));

		// labs
		$moved['lab results'] = $this->updateId('lab_order','patient_id',$dq->get('parent_id'),$dq->get('child_id'));

		$this->mergeComplete = true;

		$messages = '';
		foreach($moved as $label => $num) {
			if ($num > 0) {
				$messages .= "Moved $num $label<br>\n";
			}
		}
		$this->messages->addMessage('Merged '.$child->value('patient').' into '.$parent->value('patient'),$messages);

		$deletedId = $child->get('id');

		$child->person->drop();
		$child->drop();
		$dq->drop();

		$currentPatient = $this->get('patient_id','c_patient');
		if ($deletedId == $currentPatient) {
			$this->set("encounter_id", false, 'c_patient');	
			$this->set("patient_id", false, 'c_patient');
			$this->session->set('patient_action', Celini::link('list','PatientFinder'));
		}
	}

	function updateId($table,$field,$newId,$oldId) {
		if (!isset($this->dbHelper)) { 
			$this->dbHelper = new clniDb();
		}
		$sql = "update $table set $field = $newId where $field = $oldId";
		$this->dbHelper->_db->execute($sql);
		return $this->dbHelper->affectedRows();
	}
}
?>
