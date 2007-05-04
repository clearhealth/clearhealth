<?php
$loader->requireOnce('/lib/smarty/Smarty.class.php');
$loader->requireOnce('/lib/smarty/intsmarty.class.php');
$loader->requireOnce('/lib/PEAR/Cache/Lite.php');
/**
 * Class that extends smarty and allows us to add custom functionality to it
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class clniView extends intSmarty {
	var $_loadCeliniPlugins = true;
	var $templateType = "general";
	var $path = "";
	
	var $templateExtType = null;
	var $_finder = null;

	var $caching = false;
	var $cache_lifetime = 3600;

	/**
	 * Handle de-initialization
	 *
	 * @internal IntSmarty tries to save the current language table, but only in PHP 5.1.  Since 
	 *    this isn't an expected behavior, we override it here to keep it from happening.
	 */
	function __destruct() {
		
	}

	function _cacheInstance() {
		$cl = new PEAR_Cache_Lite();
		$cl->setOption('cacheDir',APP_ROOT.'/tmp/cache/');
		$cl->setOption('lifeTime',$this->cache_lifetime);
		$cl->setOption('fileNameProtection',false);

		return $cl;
	}
	
	/**
	 * Handle initialization of clniView/Smarty
	 */
	function clniView($path = null) {
		$this->path = $path;
		$this->lang_path = APP_ROOT."/local/templates/lang/";
		if (!file_exists($this->lang_path)) {
			$this->lang_path = CELINI_ROOT ."/templates/lang/";
		}
		$this->compile_dir = APP_ROOT."/tmp";
		$this->cache_dir = APP_ROOT."/tmp/cache";
		$this->compile_check = true;
		$this->secure_dir =& $GLOBALS['config']['template_secure_dir'];
		$this->template_dir = APP_ROOT."/local/templates/";
		$this->security_settings =& $GLOBALS['config']['smarty_security_settings'];
		
		parent::intSmarty();

		$this->_loadCeliniPlugins();
		$this->assign("Celini", new Celini());
	}
	
	
	/**
	* Load plugins in CELINI_ROOT/includes/plugins
	*/
	function _loadCeliniPlugins() {
		if ($this->_loadCeliniPlugins) {
			$plugin_locations = array(CELINI_ROOT."/includes/plugins",APP_ROOT."/local/includes/plugins");
			foreach($plugin_locations as $location) {
				if (file_exists($location)) {
					$d = dir($location);
					while ($entry = $d->read()) {
						if (preg_match('/([a-z]+)\.([a-zA-Z_]+)\.php$/',$entry,$match)) {
							include_once $location.'/'.$entry;
							switch($match[1]) {
								case "function":
									$this->register_function($match[2],'smarty_function_'.$match[2]);
								break;
								case "prefilter":
									//$this->view->register_prefilter('smarty_prefilter_'.$match[2]);
								break;
								case "modifier":
									$this->register_modifier($match[2],'smarty_modifier_'.$match[2]);
								break;
								case "block":
									$this->register_block($match[2],'smarty_block_'.$match[2]);
								break;
							}
						}
					}
				}
			}
			$this->_loadCeliniPlugins = false;
		}
	}

	/**
	 * Fetch a template (cached if available from cache and/or compile id)
	 *
	 * @param string $template
	 * @param string $cache_id
	 * @param string $compile_id
	 * @return string
	 */
	function fetch($template,$cache_id=null,$compile_id="") {
		if ($this->caching && !is_null($cache_id)) {
			if (empty($compile_id)) {
				$compile_id = $this->compile_id;
			}
			$full_cache_id = $cache_id.'-'.$compile_id.'-'.str_replace(array('/','\\'),'^',$template);

			$cl = $this->_cacheInstance();

			$content = $cl->get($full_cache_id);
			if ($content !== false) {
				return $content;
			}
		}
		// load Celini plugins
		$this->_loadCeliniPlugins();

		$realPathToTemplate = $this->templatePath($template);
		if ($realPathToTemplate === false) {
			Celini::raiseError("Can't find template: $template");
		}
		$caching = $this->caching;
		$this->caching = false;
		$content = parent::fetch($realPathToTemplate,null,$compile_id);
		$this->caching = $caching;
		if ($this->caching) {
			$ret = $cl->save($content,$full_cache_id);
			/*if (!$ret) {
				Celini::raiseError('Error writing to cache, check permission on Cache DIR: '.APP_ROOT.'/tmp/cache/');
			}*/
		}
		return $content;
	}

	/**
	 * Fetch a template without having to specify a complete path
	 *
	 * @todo see about removing Celini::getTemplatePath once every place that is calling it, uses this
	 */
	function render($page) {
		$template = $this->_buildTemplate($page);
		return $this->fetch($template);
	}
	
	
	/**
	 * Returns the full path to the requested template or <i>false</i> if it can't be found.
	 *
	 * @see    fetch()
	 * @param  string
	 * @return string|false
	 */
	function templatePath($template) {
		if ($this->template_exists($template)) {
			// Smarty already knows how to find this
			return $template;
		}
		
		$finder = new FileFinder();
		$finder->initCeliniPaths('/local');
		return $finder->find('templates/' . $template);
	}
	
	
	/**
	 * Returns true/false depending on whether a given $page exists
	 *
	 * <code>$view->templateExists('edit.html');</code>
	 *
	 * @param  string  $page  The file to check.  Example: "edit.html"
	 * @return boolean
	 * @see    templatePath(), _buildTemplate()
	 */
	function templateExists($page) {
		$template = $this->_buildTemplate($page);
		return ($this->templatePath($template) !== false ? true : false);
	}
	
	
	/**
	 * Used for building a full template name based on the "action.html" portion.
	 *
	 * @see templateExists(), render()
	 * @access private
	 */
	function _buildTemplate($page) {
		if (!is_null($this->templateExtType)) {
			$page .= '.' . $this->templateExtType;
		}
		
		if (isset($_SESSION['mobile']) && $_SESSION['mobile'] == true) {
			$template = $this->path.'/mobile_'.$page;
			if (!$this->template_exists($template)) {
				$template = $this->path.'/'.$this->templateType.'_'.$page;
			}
		}
		else {
			$template = $this->path.'/'.$this->templateType.'_'.$page;
		}
		return $template;
	}
	
	
	/**
	 * Return true if a variable by <i>$name</i> is assigned
	 *
	 * @param  string  $varName
	 * @return boolean
	 */
	function exists($varName) {
		return isset($this->_tpl_vars[$varName]);
	}
	
	
    function _smarty_include($params)
    {
    	$loadfile = $this->templatePath($params['smarty_include_tpl_file']);
    	if($loadfile !== false){
			$params['smarty_include_tpl_file']=$loadfile;
    	}
        if ($this->debugging) {
            $_params = array();
            require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.get_microtime.php');
            $debug_start_time = smarty_core_get_microtime($_params, $this);
            $this->_smarty_debug_info[] = array('type'      => 'template',
                                                  'filename'  => $params['smarty_include_tpl_file'],
                                                  'depth'     => ++$this->_inclusion_depth);
            $included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

        $this->_tpl_vars = array_merge($this->_tpl_vars, $params['smarty_include_vars']);

        // config vars are treated as local, so push a copy of the
        // current ones onto the front of the stack
        array_unshift($this->_config, $this->_config[0]);

        $_smarty_compile_path = $this->_get_compile_path($params['smarty_include_tpl_file']);


        if ($this->_is_compiled($params['smarty_include_tpl_file'], $_smarty_compile_path)
            || $this->_compile_resource($params['smarty_include_tpl_file'], $_smarty_compile_path))
        {
            include($_smarty_compile_path);
        }

        // pop the local vars off the front of the stack
        array_shift($this->_config);

        $this->_inclusion_depth--;

        if ($this->debugging) {
            // capture time for debugging info
            $_params = array();
            require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.get_microtime.php');
            $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params, $this) - $debug_start_time;
        }

        if ($this->caching) {
            $this->_cache_info['template'][$params['smarty_include_tpl_file']] = true;
        }
    }

	function regexClearCache($regexCacheId) {
		$cl = $this->_cacheInstance();
		$d = dir($this->cache_dir);

		while (false !== ($entry = $d->read())) {
			$cache_id = substr($entry,14);
			if (preg_match($regexCacheId,$cache_id)) {
				$cl->remove($cache_id);
			}
		}

	}	

	function is_cached($template,$cacheId) {
		$cl = $this->_cacheInstance();

		$full_cache_id = $cacheId.'-'.$this->compile_id.'-'.str_replace(array('/','\\'),'^',$template);

		$content = $cl->get($full_cache_id);
		if ($content !== false) {
			return true;
		}
		return false;
	}
}
?>
