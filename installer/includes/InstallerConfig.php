<?php
/*
 * InstallerConfig class
 *
 * This class is used to parse the installer config
 * files.
 */
class InstallerConfig{
	
	var $file_name = '';
	
	var $version_file_name = '';
	
	var $errors;
	
	var $settings;
	
	function InstallerConfig($file_name){
		$this->file_name = $file_name;
		$this->errors = array();	
	}
	
	function parse(){
		$this->settings['ACTION_DIRS'] = array();
		$this->settings['TEST_DIRS'] = array();
		if(is_readable($this->file_name)){
			include $this->file_name;

			// Process app_name setting
			if(isset($app_name) && !empty($app_name)){
				$this->settings['APP_NAME'] = $app_name;
			}else{
				$this->errors[] = "app_name not defined in config file!";
			}
			

			// Process action dirs
			if(isset($action_dirs) && !empty($action_dirs)){
				if(is_array($action_dirs)){
					foreach($action_dirs as $dir){
						if(is_readable($dir) && is_dir($dir)){
							$this->settings['ACTION_DIRS'][] = $dir;
						}else{
							$this->errors[] = "Action Dir $dir is not accessible!";
						}
					}	
				}else{
					if(is_readable($action_dirs) && is_dir($action_dirs)){
						$this->settings['ACTION_DIRS'][] = $action_dirs;
					}else{
						$this->errors[] = "Action Dir $action_dirs is not accessible!";
					}
				}	
			}

			// Process test dirs
			if(isset($test_dirs) && !empty($test_dirs)){
				if(is_array($test_dirs)){
					foreach($test_dirs as $dir){
						if(is_readable($dir) && is_dir($dir)){
							$this->settings['TEST_DIRS'][] = $dir;
						}else{
							$this->errors[] = "Test Dir $dir is not accessible!";
						}
					}	
				}else{
					if(is_readable($test_dirs) && is_dir($test_dirs)){
						$this->settings['TEST_DIRS'][] = $test_dirs;
					}else{
						$this->errors[] = "Test Dir $test_dirs is not accessible!";
					}
				}	
			}
			
			// Process version file
			if(isset($version_file) && is_readable($version_file)){
				$this->version_file_name = $version_file;
				$this->parseVersionFile();
			}else{
				$this->errors[] = "No version_file definition found in config file!";
			}
			
			// Process VersionChecker Implementation
			if(isset($version_detection_class)){
				if(class_exists($version_detection_class)){
					$this->settings['VERSION_CHECK'] = new $version_detection_class();
				}else{
					$this->errors[] = "version_detection_class $version_detection_class class not found!";
				}
			}else{
				$this->errors[] = "version_detection_class not defined correctly in confg file!";
			}
			
			// Process writable dir setting
			if(isset($writable_dir)){
				if(is_dir($writable_dir) && is_writable($writable_dir)){
					$this->settings['WRITABLE_DIR'] = $writable_dir;
				}else{
					$this->errors[] = "writable_dir $writable_dir is not writable!";
				}	
			}else{
				$this->errors[] = "writable_dir not defined in config file!";
			}
			
			// Process template dir setting
			if(isset($template_dir)){
				if(is_dir($template_dir) && is_readable($template_dir)){
					$this->settings['TEMPLATE_DIR'] = $template_dir;
				}else{
					$this->errors[] = "template_dir $template_dir is not readable!";
				}	
			}else{
				$this->settings['TEMPLATE_DIR'] = realpath(dirname(__FILE__).'/../templates/').'/';
			}
		}else{
			$this->errors[] = "Error loading config file ".$this->file_name;
		}
		
		if(count($this->errors) > 0){
			return FALSE;
		}
		
		return TRUE;
	}
	
	function parseVersionFile(){
		include $this->version_file_name;
		if(isset($versions)){
			$this->settings['VERSION_SET'] =& $versions;
		}else{
			ErrorStack::addError("\$versions is not defined in the version file {$this->version_file_name}", ERRORSTACK_FATAL, 'InstallerConfig');	
		}
	}
	
	function getErrors(){
		return $this->errors;	
	}
	
	function getErrorsHTML(){
		$html = '';
		
		if(is_array($this->errors) && count($this->errors) > 0){
			foreach($this->errors as $error){
				$html .= $error."<BR>\n";	
			}	
		}
		
		return $html;
	}

	function &getSetting($name){
		if(isset($this->settings[$name])){
			return $this->settings[$name];
		}
		
		return FALSE;
	}
}
?>
