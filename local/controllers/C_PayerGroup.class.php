<?php
$loader->requireOnce("includes/Grid.class.php");
/**
 * Controller for the Insurance listing/editing
 */
class C_PayerGroup extends Controller {

	var $payer_group_id = 0;

	function actionDefault_view() {
		return $this->actionList_view();
	}
	
	/**
	 * Provides a specific means of handling add
	 *
	 * @see actionEdit()
	 */
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	/**
	 * Edit/Add an Payer Group
	 *
	 */
	function actionEdit($payer_group_id = 0) {
		if ($this->payer_group_id > 0) {
			$payer_group_id = $this->payer_group_id;
		}
		$this->view->assign('FORM_ACTION',Celini::link('edit'));
		$payer_group =& Celini::newORDO('PayerGroup',$payer_group_id);
		$this->view->assign_by_ref('payerGroup',$payer_group);
		
		$pgpayers = $payer_group->get('payers');
		$program =& Celini::newORDO('InsuranceProgram');
		$payers = $program->valueList('programs');
		$this->assign('pgpayers',$pgpayers);
		$this->view->assign('payers',$payers);

		return $this->view->render('edit.html');
	}
	
	function processEdit() {
		$pg =& Celini::newORDO('PayerGroup');
		$pgarray = $this->POST->getRaw('payergroup');
		$pg->populate_array($pgarray);
		$pg->persist();
		$this->messages->addMessage('Payer Group Updated');
		$this->payer_group_id = $pg->get('id');
		$db =& new clniDB();
		$payers = $pg->valueList('payerids');
		if($this->POST->get('newPayer') != '') {
			if(in_array($this->POST->get('newPayer'),$payers)) {
				$this->messages->addMessage('Cannot add payers multiple times.');
			} else {
				$count = count($payers) + 1;
				$sql = "INSERT INTO insurance_payergroup (insurance_program_id,payer_group_id,`order`)
				VALUES(".$db->quote($this->POST->get('newPayer')).",".$pg->get('id').",{$count})";
				$db->execute($sql);
				$this->messages->addMessage('Payer Added');
			}
		} elseif($this->POST->get('removePayer') != '') {
			$remove_id = $this->POST->get('removePayer');
			$sql = "SELECT `order` FROM insurance_payergroup WHERE payer_group_id=".$pg->get('id')." AND insurance_program_id=".$db->quote($remove_id);
			$res = $db->execute($sql);
			if(!$res->EOF) {
				$sql = "DELETE FROM insurance_payergroup
					WHERE payer_group_id=".$pg->get('id')." AND insurance_program_id = ".$db->quote($remove_id);
				$db->execute($sql);
				$sql = "UPDATE insurance_payergroup
					SET `order` = `order` - 1 WHERE `order` > ".$db->quote($remove_id)." AND payer_group_id = ".$pg->get('id');
				$db->execute($sql);
				$this->messages->addMessage('Payer Removed');
			}
		} elseif(is_array($this->POST->getRaw('payerorder'))) {
			$payerdata = $this->POST->getRaw('payerorder');
			$list = array();
			foreach($payerdata as $payer_id=>$order) {
				if(!is_numeric($order)) {
					$this->messages->addMessage('Please enter numbers only for "Order"');
					return;
				}
				if(isset($list[$order])) {
					$x = 1;
					while(isset($list[$x])) {
						$x++;
					}
					$list[count($list)+$x] = $payer_id;
				} else {
					$list[(int)$order] = $payer_id;
				}
			}
			$sql = "DELETE FROM insurance_payergroup WHERE payer_group_id = ".$pg->get('id');
			$db->execute($sql);
			$keylist = array_flip($list);
			sort($keylist);
			$x=1;
			foreach($keylist as $key) {
				$sqls[] = "(".$pg->get('id').",".$db->quote($list[$key]).",{$x})";
				$x++;
			}
			$sql = "INSERT INTO insurance_payergroup (payer_group_id,insurance_program_id,`order`)
			VALUES".implode(',',$sqls);
			$db->execute($sql);
			$this->messages->addMessage('Payer order updated');
		}
	}

	/**
	 * List Payer Groups
	 */
	function actionList_view() {
		$GLOBALS['loader']->requireOnce('includes/Datasource_sql.class.php');
		$pg =& Celini::newORDO('PayerGroup');
		$ds =& $pg->loadDatasource('List');
//		$ds->template['name'] = "<a href='".Celini::link('edit')."id={\$payer_group_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);
		$grid->indexCol = false;
		$grid->orderLinks = false;

		$this->assign_by_ref('grid',$grid);

		return $this->view->render('list.html');
	}
}
?>