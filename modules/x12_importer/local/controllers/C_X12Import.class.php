<?php
$loader->requireOnce('includes/transaction/TransactionManager.class.php');
$loader->requireOnce('includes/X12Objects.class.php');
$loader->requireOnce('includes/X12Util.class.php');
$loader->requireOnce('includes/X12StringReader.class.php');
$loader->requireOnce('includes/X12MapParser.class.php');
$loader->requireOnce('includes/X12Tokenizer.class.php');
$loader->requireOnce('includes/X12Transaction.class.php');
$loader->requireOnce('includes/X12TransactionBuilder.class.php');
$loader->requireOnce('includes/X12MapTreeBuilder.class.php');

$loader->requireOnce('datasources/X12ImportedData_DS.class.php');

/**
 */
class C_X12Import extends Controller {
	
	function C_X12Import() {
		$this->Controller();
	}

	/**
	 * @todo list all the currently imported x12 files here
	 */
	function actionList() {
		$ds =& new X12ImportedData_DS();
		$grid =& new cGrid($ds);
		$grid->setLabel('checkbox', '');
		$grid->registerTemplate('checkbox', '<input type="radio" name="X12ImportedDataId" value="{$x12imported_data_id}" />');
		
		$this->view->assign_by_ref('grid', $grid);
		$this->view->assign('FORM_ACTION', Celini::link('parse', 'X12Import'));
		return $this->view->render('list.html');
	}

	function actionUpload_edit() {
		$session =& Celini::sessionInstance();
		return $this->view->render('upload.html');
	}

	function processUpload_edit() {
		if (isset($_FILES['x12file']) && $_FILES['x12file']['size'] > 0 && $_FILES['x12file']['error'] === 0) {
			if (is_uploaded_file($_FILES['x12file']['tmp_name'])) {
				$this->_saveUploadedFile($_FILES['x12file']);
			}
			else {
				$this->messages->addMessage('File Upload Error', 'There was an issue with the file that was uploaded');
			}
		}
		else {
			$this->messages->addMessage('File Upload Error','There was an error uploading your x12 file please try again');
		}
	}
	
	function _saveUploadedFile($fileArray) {
		$file = file_get_contents($_FILES['x12file']['tmp_name']);
		$fileHash = md5($file);
		
		$rawData =& Celini::newORDO('X12ImportedData', $fileHash, 'ByDataHash');
		if ($rawData->isPopulated()) {
			$this->messages->addMessage('Duplicate File Detected', 'This file has already been uploaded.');
			return;
		}
		
		$rawData->set('data', $file);
		$rawData->set('filename', $_FILES['x12file']['name']);
		$rawData->persist();
		
		$this->messages->addMessage('File Upload Success', 'The file was successfully uploaded.');
	}

	function actionParse_edit($x12ImportedDataId) {
		$x12ImportedData =& Celini::newORDO('X12ImportedData', $x12ImportedDataId);
		if (!$x12ImportedData->isPopulated()) {
			$this->messages->addMessage('X12 Parsing Failed', 'There was a problem loading the file you selected');
			Celini::redirect('X12Import', 'list');
		}
		$builder = new X12TransactionBuilder();
		$builder->build($this->_parseFile($x12ImportedData));
		$_SESSION['X12Import']['transactions'][1] = serialize($builder->transactions);
		$this->view->assign('numTrans',count($builder->transactions));

		$claimCounts = array();
		$claims = 0;
		foreach($builder->transactions as $id => $trans) {
			$id = $trans->summary->get('identifier');
			$claimCounts[$id] = array('claims'=>count($trans->details),'payment'=>$trans->summary->get('amount'));
			$claims += $claimCounts[$id]['claims'];
			
		}
		$this->view->assign('claimCounts',$claimCounts);
		$this->view->assign('claims',$claims);
		$this->view->assign('summary',$builder->transactions->summary);

		/*
		echo "<pre>";
		print_r($builder->transactions);
		echo "</pre>";
		*/
		
		$this->view->assign('ACTION_APPLY',Celini::link('view','X12Apply'));
		return $this->view->render('parse.html');
	}


	function _parseFile(&$x12ImportedData) {
		$parser = new X12MapParser();

		if ($this->GET->exists('debug')) {
			$parser->debug = $this->GET->get('debug');
		}
		$parser->loadKnownElementsFromFile('maps/835.elements.php');
		$parser->loadInput(new X12StringReader($x12ImportedData->get('data')));
		
		$builder = new X12MapTreeBuilder();
		$builder->loadMapFromFile('maps/835.map.php');
		$parser->setTreeBuilder($builder);
		
		$parser->parse();

		return $parser->getTree();
	}

	function actionProcessX12_edit() {
		$tm = new TransactionManager();

		$trans = $tm->createTransaction('Claim');

		$trans->setClaim('206530-4290-201442');
		$trans->setPayer('CHDP','CHDP');

		$trans->type = 'credit';
		$trans->amount = 13.00;
		$trans->paymentDate = date('Y-m-d');

		$tm->processTransaction($trans);
	}

	function actionKillLock() {
		unlink($this->lockFile);
	}
}
?>
