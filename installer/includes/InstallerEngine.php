<?php
/*
 * InstallerEngine class
 * 
 * This is the main class for managing the 
 * installation process
 * 
 */
class InstallerEngine {
	var $config;
	
	var $phase;
	
	var $old_version = FALSE;
	
	var $special_actions = FALSE;
	
	var $completed_action_html = '';
	
	function InstallerEngine($config){
		if(!is_a($config, 'InstallerConfig')){
			ErrorStack::addError("Invalid configuration object passed in!", ERRORSTACK_FATAL, 'InstallerEngine');	
		}
		
		$this->config = $config;
		$this->phase = 0;
	}
	
	function run(){
		$output = '';
		$field_form = '';
		$versions =& $this->config->getSetting('VERSION_SET');
		$smarty =& $GLOBALS['INSTALLER']['SMARTY'];
		
		if($this->phase == 0){
			$fields = $versions->getFieldsForUpgrade();
			$field_count = count($fields);
			if($field_count == 0){
				$this->phase++;
				return $this->run();
			}

			if(isset($_REQUEST['save_data'])){
				// Save the data
				for($i = 0; $i < $field_count; $i++){
					$field =& $fields[$i];
					$field->saveField();
				}
				$this->phase++;
				return $this->run();
			}else{
				// Draw the form fields
				$field_form .= "<INPUT TYPE='hidden' name='save_data' value='true'>\n";
				for($i = 0; $i < $field_count; $i++){
					$field =& $fields[$i];
					$field_form .= $field->getHTML($smarty);
				}
				$smarty->assign('FORM_FIELDS', $field_form);
				$output .= $smarty->fetch(Installer::getTemplatePath('collect_data.tpl'));
			}
		}elseif($this->phase == 1){
			$vc =& $this->config->getSetting('VERSION_CHECK');
			$fields = $versions->getFieldsForUpgrade();
			if(is_array($fields) && count($fields) > 0) $vc->updateFields($fields);
			$this->old_version = $vc->getCurrentVersion($fields);
			$this->special_actions = $vc->getSpecialActions($this->old_version);
			$smarty->assign('INSTALLED', $this->old_version !== FALSE);
			$smarty->assign('OLD_VERSION', $this->old_version);
			$output .= $smarty->fetch(Installer::getTemplatePath('version_check.tpl'));
			$smarty->assign('CAN_CONTINUE', true);
		}elseif($this->phase == 2){
			$tests =& $versions->getTestsForUpgrade($this->old_version);
			$test_count = count($tests);
			for($i = 0; $i < $test_count; $i++){
				$test =& $tests[$i];
				$test->perform();
				$smarty->assign_by_ref('test', $test);
				$output .= $smarty->fetch(Installer::getTemplatePath('test_result.tpl'));
				$smarty->clear_assign('test');
			}
			if($versions->testsComplete($this->old_version)){
				$smarty->assign('CAN_CONTINUE', true);
			}else{
				$smarty->assign('CAN_CONTINUE', false);				
			}
		}elseif($this->phase == 3){
			if(is_a($this->special_actions, 'ActionSet')){
				// Handle saving of data
				if(isset($_REQUEST['save_action'])){
					$actions =& $this->special_actions;
					$action_count = count($actions);
					for($i = 0; $i < $action_count; $i++){
						$action =& $actions[$i];
						if($action->isInteractive()){
							$action->dataSubmitted();
							$action->perform();
							$smarty->assign_by_ref('ACTION', $action);
							$action_html .= $smarty->fetch(Installer::getTemplatePath('action_complete.tpl'));
							$smarty->clear_assign('ACTION');
						}
					}
				}			
				
				// See whats next
				$actions =& $this->special_actions;
				$action_count = count($actions);
				for($i = 0; $i < $action_count; $i++){
					$action =& $actions[$i];
					if(!$action->success()){
						if($action->isInteractive()){
							$action_html .= $action->getHTML($smarty);
						}else{
							$action->perform();
							$smarty->assign_by_ref('ACTION', $action);
							$action_html .= $smarty->fetch(Installer::getTemplatePath('action_complete.tpl'));
							$smarty->clear_assign('ACTION');
						}
					}
				}
				$smarty->assign('ACTION_HTML', $action_html);
				$output .= $smarty->fetch(Installer::getTemplatePath('actions.tpl'));
				$smarty->clear_assign('ACTION_HTML');
	
				if($versions->actionsComplete($this->old_version)){
					$smarty->assign('CAN_CONTINUE', true);
				}else{
					$smarty->assign('CAN_CONTINUE', false);				
				}	
			}else{
				$this->phase++;
				$output .= $this->run();	
			}
		}elseif($this->phase == 4){
			$action_html = '';

			// Handle saving of data
			if(isset($_REQUEST['save_action'])){
				$actions =& $versions->getNextActionsForUpgrade($this->old_version);
				$action_count = count($actions);
				for($i = 0; $i < $action_count; $i++){
					$action =& $actions[$i];
					if($action->isInteractive()){
						$action->dataSubmitted();
						$action->perform();
						$smarty->assign_by_ref('ACTION', $action);
						$this->completed_action_html .= $smarty->fetch(Installer::getTemplatePath('action_complete.tpl'));
						$smarty->clear_assign('ACTION');
					}
				}
			}
				
			// See whats next
			$actions = $versions->getNextActionsForUpgrade($this->old_version);
			$action_count = count($actions);
			$interactive = false;
			for($i = 0; $i < $action_count; $i++){
				$action =& $actions[$i];
				if(!$action->success()){
					if($action->isInteractive()){
						$action_html .= $action->getHTML($smarty);
						$interactive = true;
					}else{
						$action->perform();
						$smarty->assign_by_ref('ACTION', $action);
						$this->completed_action_html .= $smarty->fetch(Installer::getTemplatePath('action_complete.tpl'));
						$smarty->clear_assign('ACTION');
					}
				}
			}
			if(!$interactive && count($versions->getNextActionsForUpgrade($this->old_version)) > 0) $this->run();
			if($action_count == 0){
				$action_html = "You have completed the Actions phase of installation, click continue below to complete your installation.<BR>\n";
			}
			$action_html = $action_html.$this->completed_action_html;
			$smarty->assign('ACTION_HTML', $action_html);
			$output .= $smarty->fetch(Installer::getTemplatePath('actions.tpl'));
			$smarty->clear_assign('ACTION_HTML');

			if($versions->actionsComplete($this->old_version)){
				$smarty->assign('CAN_CONTINUE', true);
			}else{
				$smarty->assign('CAN_CONTINUE', false);				
			}
		}elseif($this->phase == 5){
			$url = str_replace('installer', '', dirname($_SERVER['SCRIPT_NAME']));
			$smarty->assign('APP_URL', $url);
			$output .= $smarty->fetch(Installer::getTemplatePath('finished.tpl'));
		}
		
		return $output;
	}
	
	function &getSetting($name){
		return $this->config->getSetting($name);	
	}
	
	function &getField($name){
		$versions =& $this->config->getSetting('VERSION_SET');
		$return = $versions->getField($name);
		return $return;
	}
	
	function previousStep(){
		if($this->phase > 0){
			$this->phase--;
		}
	}
	
	function nextStep(){
		$versions =& $this->config->getSetting('VERSION_SET');
		if($this->phase == 2){
			if($versions->testsComplete($this->old_version)){
				$this->phase = 3;
			}
		}elseif($this->phase == 3){
			if($versions->actionsComplete($this->old_version)){
				$this->phase = 4;
			}
		}elseif($this->phase == 5){
			return;
		}else{
			$this->phase++;
		}
		
		
	}

	function getPhaseName(){
		$name = 'Unknown';
		switch($this->phase){
			case 0:
				$name = 'Data Collection';
				break;
			case 1:
				$name = 'Version Check';
				break;
			case 2:
				$name = 'Tests';
				break;
			case 3:
				$name = 'Special Actions';
				break;
			case 4:
				$name = 'Actions';
				break;
			case 5:
				$name = 'Finished';
				break;
		}
		
		return $name;
	}
}
?>
