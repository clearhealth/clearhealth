<?php

class C_CodingTemplate extends Controller 
{
	var $template = null;
	var $coding = null;
	
	function C_CodingTemplate() {
		parent::Controller();
		$GLOBALS['loader']->requireOnce('controllers/C_Coding.class.php');
		$this->coding = new C_Coding();
		$this->coding->view->assign('noform',true);
	}
	
	function actionDefault() {
		return $this->actionList();
	}
	
	function actionList() {
		if(is_numeric($this->GET->get('delete'))) {
			$t =& Celini::newORDO('CodingTemplate',$this->GET->get('delete'));
			$t->drop();
		}
		$template =& Celini::newORDO('CodingTemplate');
		$templatelist = $template->valueList('all');
		$this->view->assign('templatelist',$templatelist);
		return $this->view->render('list.html');
	}
	
	function actionAdd() {
		header('Location: '.Celini::link('Edit','CodingTemplate'));
	}
	
	function actionUpdate($template_id = 0) {
		return $this->actionEdit($template_id);
	}
	
	function actionEdit($template_id=0) {
		if($this->GET->get('delete_id') > 0) {
			$c =& Celini::newORDO('CodingData',$this->GET->get('delete_id'));
			$c->drop();
		}
		$this->coding->assign('incodingtemplate',true);
		if(is_null($this->template)) {
			if($template_id == 0) {
				$template_id = $this->GET->get('template_id',0);
			}
			$template =& Celini::newORDO('CodingTemplate',$template_id);
		} else {
			$template =& $this->template;
		}
		$this->coding->foreign_id = $template->get('id');
		$practice =& Celini::newORDO('Practice',$_SESSION['defaultpractice']);
		$pconfig=&$practice->get_config();
		if($pconfig->get('FacilityType',FALSE)){
			$this->coding->assign('dentalpractice',true);
			$this->coding->assign('teetharray',array(
				'N/A'=>'N/A',
				'All'=>'All',
				1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,
				16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,
				28=>28,29=>29,30=>30,31=>31,32=>32,
				'All (Primary)'=>'All (Primary)',
				'A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J',
				'K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T'
			));
			$this->coding->assign('toothsidearray',array('N/A'=>'N/A','Front'=>'Front','Back'=>'Back','Top'=>'Top','Left'=>'Left','Right'=>'Right'));
		}
		$code_data =& Celini::newORDO('CodingData',$template->get('coding_parent_id'));
		$code_list = $code_data->getCodeList($template->get('id'));
		$code =& Celini::newORDO('Code',$code_data->get('parent_id'));
		$this->view->assign_by_ref('code_data',$code_data);
		$this->view->assign_by_ref('code_list',$code_list);
		$this->view->assign_by_ref('code',$code);
		$this->view->assign('practicelist',$practice->valueList('name'));
		if($this->GET->get('parent_id') > 0) {
			$parent_id = $this->GET->get('parent_id');
		} else {
			$parent_id = $template->get('coding_parent_id');
		}
		$codingHtml = $this->coding->update_action_edit($template->get('id'),$parent_id);
		$this->view->assign('codinghtml',$codingHtml);
		$this->view->assign_by_ref('template',$template);
		$em =& Celini::enumManagerInstance();
		$reasons = $em->enumArray('encounter_reason');
		$this->view->assign('reasons',$reasons);
		$this->view->assign('FORM_ACTION',Celini::link('Edit','CodingTemplate').'template_id='.$template->get('coding_template_id'));
		$this->coding->view->assign('FORM_ACTION',Celini::link('Edit','CodingTemplate').'template_id='.$template->get('coding_template_id'));
		return $this->view->render('edit.html');
	}
	
	function processEdit() {
		$template_array = $this->POST->getRaw('CodingTemplate');
		$template =& Celini::newORDO('CodingTemplate',$template_array['id']);
		$this->template =& $template;
		$template->populateArray($template_array);
		$template->persist();
		$this->coding->foreign_id = $template->get('id');
		$_POST['foreign_id']=$template->get('id');
		$code_data = $this->coding->process($_POST,true);
		if($code_data !== false) {
			$template->set('coding_parent_id',$code_data->get('id'));
			$template->persist();
		}
	}
}

