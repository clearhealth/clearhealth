<?php
class C_CodeCategory extends controller {
	var $_ordo;

	function actionList() {
		$GLOBALS['loader']->requireOnce('datasources/CodeCategory_DS.class.php');

		$ds =& new CodeCategory_DS();
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		return $this->view->render('list.html');
	}

	function actionAdd() {
		return $this->actionEdit();
	}

	function actionDelete() {
		return true;
	}
	function processDelete() {
		$id = $this->POST->get('code_category_id');
		$ordo =& Celini::newOrdo('CodeCategory',$id);
		$ordo->drop();
	}

	function actionEdit() {
		$id = $this->getDefault('code_category_id',0);

		if (!is_object($this->_ordo)) {
			$this->_ordo =& Celini::newOrdo('CodeCategory',$id);
		}

		$this->view->assign_by_ref('code_category',$this->_ordo);

		$GLOBALS['loader']->requireOnce('datasources/CodeCategory_Code_DS.class.php');

		$ds =& new CodeCategory_Code_DS($id);
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);

		$this->view->assign('FORM_ACTION',Celini::link('edit')."code_category_id=$id");

		return $this->view->render('edit.html');
	}

	function process($data) {
		if (isset($data['code_category_id'])) {
			$this->_ordo =& Celini::newOrdo('CodeCategory',$data['code_category_id']);
			$this->_ordo->populateArray($data);
			$this->_ordo->persist();
			$this->messages->addMessage('Category Updated');
		}
	}

	function actionImport() {
		return $this->view->render('import.html');
	}

	function processImport() {
		if (isset($_FILES['file']) && $_FILES['file']['error'] == 0 && is_uploaded_file($_FILES['file']['tmp_name'])) {
			$lines = file($_FILES['file']['tmp_name']);
		}
		else {
			$this->messages->addMessage('Error Uploading File');
			return;
		}

		$db = new clniDb();
		$sql = "select code,code_id from codes";
		$codes = $db->getAssoc($sql);
		$count = 0;
		foreach($lines as $line) {
			if (preg_match('/(.+)\s{2,}([0-9]+)-*([0-9]*)/',$line,$match)) {
				$cat =& Celini::newOrdo('CodeCategory');
				$cat->set('category_name',$match[1]);
				$cat->persist();
				$catId = $cat->get('id');

				if (empty($match[3])) {
					$match[3] = $match[2];
				}
				$sql = "insert into code_to_category values ";
				$added = false;
				for($i = $match[2]; $i <= $match[3]; $i++) {
					$added = true;
					if (isset($codes[$i])) {
						$id = $codes[$i];
						$sql .= " ($catId,'$id'), ";
					}
				}
				$sql = substr($sql,0,strlen($sql)-2);

				if ($added) {
					$db->execute($sql);
				}
				else {
					$this->messages->addMessage("No codes added to category: $match[1]","Codes are: $match[2] $match[3]");
				}
				$count++;
			}
		}

		$this->messages->addMessage("$count Categories Imported");
	}
}
?>
