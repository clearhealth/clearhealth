<?php
/**
* Controller for Enumerations
*
* @package	com.uversainc.celini
*/
/**#@+
 * Required Libs
 */
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/
//$loader->requireOnce('lib/PEAR/HTML/AJAX/Serializer/JSON.php');
//$loader->requireOnce('includes/EnumManager.class.php');

/**
*
*/
class C_FormRule extends Controller {
	 
	/**
	* Sets up TOP_ACTION
	*/
	function C_FormRule ($template_mod = "general") {
		parent::Controller($template_mod);
		$this->assign("TOP_ACTION", Celini::link('default'));
	}

	/**
	* If no action is specified use the list action
	*/
	function actionDefault_view() {
		return $this->actionList();
	}

	/**
	* List all Form Rules
	*/

	
	function actionList_view() {
		$rule =& ORDataObject::factory('FormRule');

		$ds =& $rule->ruleList();
		$ds->template['rule_name'] = "<a href='".Celini::link('edit','FormRule')."form_rule_id={\$form_rule_id}'>{\$rule_name}</a>";
		$grid =& new cGrid($ds);
		$grid->pageSize = 50;

		$this->assign_by_ref('grid',$grid);

		return $this->view->render("list.html");
	}
	
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	
	function actionEdit($form_rule_id = 0) {

		$formRule =& ORDataObject::factory('FormRule',$form_rule_id);

		$this->assign_by_ref('formRule',$formRule);
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$form_rule_id));
		
		return $this->view->render("edit.html");
	}
	
	function processEdit($form_rule_id) {
		if (!isset($_POST['formRule'])) {
			return "";
		}
		$form =& ORDataObject::factory('FormRule',$form_rule_id);
		
		//print_r ($_POST['form']);
		$form->populate_array($_POST['formRule']);
		$form->persist();
		$this->form_rule_id = $form->get('id');

		$this->messages->addMessage('Form Rule Added Successfully');
	}
	
}

?>