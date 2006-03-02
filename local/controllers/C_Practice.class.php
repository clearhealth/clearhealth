<?php
$loader->requireOnce('includes/TypeFileLoader.class.php');
$loader->requireOnce('includes/PracticeConfig.class.php');
/**
 * Controller for editing a clearhealth practice
 */
class C_Practice extends Controller {
	var $location = false;

	function actionAdd() {
		return $this->actionEdit(0);
	}

	function actionEdit($id = 0) {
		if (!is_object($this->location)) {
			$this->location =& Celini::newORDO('Practice',$id);
		}
		
		$this->assign_by_ref("practice",$this->location);
		
		$this->assign("process",true);
		$this->assign("FORM_ACTION",Celini::link('edit',true,true,$id));

		$this->assign('settings',$this->_practiceSettings($id));
		return $this->view->render("edit.html");
	}
	
	function processEdit($id) {
		if ($_POST['practice_id'] == 0) {
			$this->sec_obj->acl_qcheck("add",$this->_me,"","practice",$this,false);
		}

		$this->location =& Celini::newORDO('Practice',$_POST['practice_id']);
		$this->location = new Practice($_POST['practice_id']);
		$this->location->populate_array($_POST);
		$this->location->persist();
		
		$this->location->populate();

		if (isset($_POST['config'])) {
			$this->_processPracticeSettings($id,$_POST['config']);
		}
	}

	function _practiceSettings($id) {
		if ($id == 0) {
			return "";
		}

		$config =& Celini::configInstance('practice');
		$config->loadPractice($id);

		$schema = $config->getSchema();
		$ret = array();

		foreach($schema as $key => $info) {
			$type = $this->_typeClass($info['type']);

			$ret[$key]['label'] = $type->label($key,$info['label']);
			$ret[$key]['widget'] = $type->widget($key,$config->get($key,900));
		}
		return $ret;
	}

	function _processPracticeSettings($id,$settings) {
		$config =& Celini::configInstance('practice');
		$config->loadPractice($id);

		$schema = $config->getSchema();
		$typeLoader =& new TypeFileLoader();

		foreach($settings as $key => $value) {
			if (isset($schema[$key])) {
				$type = $this->_typeClass($schema[$key]['type']);
				$config->set($key,$type->parseValue($value));
			}
		}
	}

	function _typeClass($type) {
		require_once CELINI_ROOT."/includes/clniType/Second.class.php";
		require_once APP_ROOT."/local/includes/clniType/FacilityType.class.php";
		$typeLoader =& new TypeFileLoader();
		$typeLoader->loadType($type);
		$class = 'clniType'.$type;
		$type = new $class();
		return $type;
	}
}
?>