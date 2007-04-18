<?php

require_once("Smarty.class.php");

/**
 * IntSmarty is an extension on the PHP Smarty templating library
 * found at <A href="http://smarty.php.net/">smarty.php.net</A>. It
 * is designed to allow the easy implementation of localization
 * by harnessing the power of the Smarty templating language for 
 * translations between languages. 
 * 
 * This class is (c) John Coggeshall, all rights reserved. It may be
 * used in accordance to the Coggeshall.org license available at:
 *
 * <http://www.coggeshall.org/oss/license.php>
 *
 * Or by e-mailing the author at <john@coggeshall.org>
 *
 * For the latest version of IntSmarty, visit:
 *
 * <http://www.coggeshall.org/oss/intsmarty/>
 *
 * @author John Coggeshall
 * @version 0.9
 * @since Smarty 2.5.3
 */

 class IntSmarty extends Smarty {

    var $default_lang = "en-us";
    var $lang_path    = "";
    var $version       = "0.9";

    var $a_languages;
    var $cur_language;
    var $translation;
    var $translation_size = false;

    var $transtable_updated;
		
		var $_security;
		var $security;

    /**
     * PHP 5 compatible wrapper for the constructor
     *
     * @param string $lang The language code to set as the active language
     */
    function __construct($lang = NULL)
    {
        $this->IntSmarty($lang);
    }

    /**
     * PHP 5 compatiable destructor 
     */
    function __destruct()
    {
        $this->saveLanguageTable();

    }

    /**
     * The IntSmarty constructor, called when a new instance
     * is created.
     *
     * @param string $lang The default language to assume when translating or
     *                     omit to auto-detect
     */  
    function IntSmarty($lang = NULL) {
        parent::Smarty();

        $this->translation = array();
        $this->translation_size = 0;

        $this->register_prefilter("smarty_lang_prefilter");
        $this->register_function("i18nfile", "smarty_function_i18nfile");

        $this->a_languages = array_unique($this->_determineLangs());
         
        if(! is_null($lang)) {
            array_unshift($this->a_languages , $lang);
        }

        if(isset( $this->a_languages[0])) {
            $this->cur_language = $this->a_languages[0];
        } else {
            $this->cur_language = NULL;
        }
        $this->compile_id = $this->cur_language;
        
        foreach( $this->a_languages as $lang) {

            $this->cur_language = $lang;
            if( $this->loadLanguageTable($this->cur_language)) {
                break;
            }

        }

    }
    
    
    /**
     * Used to determine if the provided language is one IntSmarty
     * has translation tables for or not.
     *
     * @param string $lang The language code to check
     * @return bool A boolean indicating if a language table exists for the given language
     */
    function isAvailableLanguage($lang) {
        return in_array($lang, $this->a_languages);
    }

    /**
     * A overridden method from the Smarty class used to hook into the Smarty
     * engine. See the Smarty documentation for more information
     */
    function fetch($file, $cache = NULL, $compile_id = "", $display = NULL) {
        $cid = $this->cur_language .$compile_id;
        return parent::fetch($file, $cache, $cid, $display);

    }
    
    /**
     * A private method used to auto-resolve the accepted language for the 
     * browser by looking at the $_SERVER['HTTP_ACCEPT_LANGUAGE'] variable.
     * This value can be overridden by passing a language to the constructor
     * as well.
     * 
     * @return array An array of the accepted languages, the primary one first.
     */
    function _determineLangs() {

        @preg_match_all("/([a-z\-]*)?[,;]+/i",
        $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        $matches);


        return (count($matches[1])) ? $matches[1] : array($this->default_lang);

    }

    /**
     * This method is called to load the language table for a given language. It
     * is designed to be overriden in such a way that the default filesystem storage
     * can be substituted for a database query, etc. It is responsible for 
     * setting the translation table, current language, translation size, etc. for
     * the application.
     *
     * @param string $language The language code to load the table for
     * @return bool A boolean indicating if the table was loaded successfully
     */
    function loadLanguageTable($language) {
        $filename = "{$this->lang_path}$language.php";
        if( strlen($language) > 0 && file_exists($filename)) {
            require_once($filename);
            if(isset($GLOBALS['__LANG']) && is_array($GLOBALS['__LANG'])) {
                $this->translation = $GLOBALS['__LANG'];
                $this->cur_language = $language;
                $this->translation_size = count($this->translation);
                $this->assign("lang", $this->cur_language );

                $this->transtable_updated = false;
                //unset( $__LANG);
                return $GLOBALS['__LANG'];

            }
            else {
                echo "<!-- IntSmarty Error: error with lang array. -->";
            }
        }
        else {
// commenting to kill ajax errors because lang file almost never exists.
//            echo "<!-- IntSmarty Error: " . $filename . " not found. " . "-->";
        }
        return false;

    }

    /**
     * This method is used to save the active language table for later use
     * in a translation. It is designed to be overriden by an extending class
     * to allow saving to a database, etc. 
     *
     * @return bool A boolean indicating if the table was saved successfully
     */
    function saveLanguageTable() {

        if( count($this->translation ) != $this->translation_size) {

            $filename = "{$this->lang_path}{$this->cur_language}.php" ;
            $code = '<?php $__LANG = '.var_export($this->translation, true).'; ?>';
            $fr = fopen($filename, "w");

            if(! $fr) {
                return false;
            }

            fputs($fr, $code);
            fclose($fr);

        }

        return true;
    }

    /**
     * Provides a PHP-side facility to translate strings through the IntSmarty system.
     * Identical to the block IntSmarty tag {l}{/l}, it takes the provided string
     * and attempts to find a suitable translation for the current language.
     *
     * @param string $value The string to translate
     * @return string The translated version of the string for the current language,
     *         or the same string passed to the method if no translation was
     *         found.
     */
    function translate($value)
    {
	if( is_string($value)) {

            $hash = md5($value);

            if( key_exists($hash, $this->translation )) {

                return utf8_decode($this->translation[$hash]);

            }  else {
                $this->transtable_updated = true;
                $this->translation[$hash] = $value;
                return $value;
            }

        }

    }

    /**
     * This method is a Localized version of the Smarty assign() method, which
     * will assign a value to a variable using its translated form rather than
     * the original language.
     *
     * @param string $var The name of the Template variable being assigned
     * @param string $value The new value of the given template variable
     */
    function assignLang($var, $value = NULL) {

        $this->assign($var, $this->translate($value));

    }

    /**
     * The _compile_source() method is a private method in the Smarty class (version 2.6.x)
     * which has been overriden to allow us to provide access to the Internationalization tables
     * during the compilation of a new Smarty template. It should never be called directly.
     * 
     * Please see the Smarty documentation for more information.
     */
    function _compile_source($resource_name, &$source_content, &$compiled_content, $cache_include_path=null) {
        if (file_exists(SMARTY_DIR . $this->compiler_file )) {

            require_once( SMARTY_DIR . $this->compiler_file );

        } else {

            // use include_path

            require_once($this->compiler_file);

        }

        $smarty_compiler = new $this->compiler_class;

        $smarty_compiler->template_dir       = $this->template_dir;
        $smarty_compiler->compile_dir        = $this->compile_dir;
        $smarty_compiler->plugins_dir        = $this->plugins_dir;
        $smarty_compiler->config_dir         = $this->config_dir;
        $smarty_compiler->force_compile      = $this->force_compile;
        $smarty_compiler->caching            = $this->caching;
        $smarty_compiler->php_handling       = $this->php_handling;
        $smarty_compiler->left_delimiter    = $this->left_delimiter;
        $smarty_compiler->right_delimiter   = $this->right_delimiter;
        $smarty_compiler->_version           = $this->_version;
        $smarty_compiler->security           = $this->security;
        $smarty_compiler->secure_dir         = $this->secure_dir;
        $smarty_compiler->security_settings = $this->security_settings;
        $smarty_compiler->trusted_dir        = $this->trusted_dir;
        $smarty_compiler->_reg_objects       = &$this->_reg_objects;
        $smarty_compiler->_plugins           = &$this->_plugins;
        $smarty_compiler->_tpl_vars          = &$this->_tpl_vars;
        $smarty_compiler->default_modifiers = $this->default_modifiers;
        $smarty_compiler->compile_id         = $this->_compile_id;
        $smarty_compiler->_config            = $this->_config;
        $smarty_compiler->request_use_auto_globals  = $this->request_use_auto_globals;
        $smarty_compiler->parent_inst        = &$this;

        $smarty_compiler->_cache_serial = null;
        $smarty_compiler->_cache_include = $cache_include_path;

        $_results = $smarty_compiler->_compile_file($resource_name, $source_content, $compiled_content);

        if ($smarty_compiler->_cache_serial) {

            $this->_cache_include_info = array(

            'cache_serial'=>$smarty_compiler->_cache_serial,
            'plugins_code'=>$smarty_compiler->_plugins_code,
            'include_file_path' => $cache_include_path);

        } else {

            $this->_cache_include_info = null;

        }

        return $_results;

    }

    /**
     * The __compile_template() method is a private method in the Smarty class (version 2.5.x)
     * which must be hooked to allow IntSmarty to translate documents at compile time. This
     * method is identical to the _compile_source() method in Smarty 2.6.x which IntSmarty
     * also overrides.
     */
    function _compile_template($tpl_file, $template_source, &$template_compiled) {
        if( file_exists(SMARTY_DIR.$this->compiler_file)) {
            require_once SMARTY_DIR.$this->compiler_file;
        } else {
            // use include_path
            require_once $this->compiler_file;
        }

        $smarty_compiler = new $this->compiler_class;

        $smarty_compiler->template_dir       = $this->template_dir;
        $smarty_compiler->compile_dir        = $this->compile_dir;
        $smarty_compiler->plugins_dir        = $this->plugins_dir;
        $smarty_compiler->config_dir         = $this->config_dir;
        $smarty_compiler->force_compile      = $this->force_compile;
        $smarty_compiler->caching            = $this->caching;
        $smarty_compiler->php_handling       = $this->php_handling;
        $smarty_compiler->left_delimiter    = $this->left_delimiter;
        $smarty_compiler->right_delimiter   = $this->right_delimiter;
        $smarty_compiler->_version           = $this->_version;
        $smarty_compiler->security           = $this->security;
        $smarty_compiler->secure_dir         = $this->secure_dir;
        $smarty_compiler->security_settings = $this->security_settings;
        $smarty_compiler->trusted_dir        = $this->trusted_dir;
        $smarty_compiler->_reg_objects       = &$this->_reg_objects;
        $smarty_compiler->_plugins           = &$this->_plugins;
        $smarty_compiler->_tpl_vars          = &$this->_tpl_vars;
        $smarty_compiler->default_modifiers = $this->default_modifiers;
        $smarty_compiler->compile_id         = $this->_compile_id;
        $smarty_compiler->parent_inst        = &$this;

        if ($smarty_compiler->_compile_file($tpl_file, $template_source, $template_compiled)) {
            return true;
        } else {
            $this->trigger_error($smarty_compiler->_error_msg);
            return false;
        }

    }
    
    /**
     * Clears all compiles of a specific template from the compile directory.
     * Should be used when caching data.
     *
     * @param string $tpl_file
     */
    function clear_compile($tpl_file) {
        $extensions = array('.inc', '.php');
    	$_handle = opendir($this->compile_dir);
    	$_res = true;
    	while (false !== ($_filename = readdir($_handle))) {
    		if($_filename == '.' || $_filename == '..' || is_dir($_filename)) {
    			continue;
    		} else {
    			foreach($extensions as $ext) {
    				$len = strlen($tpl_file.$ext);
		    		if (substr($this->compile_dir . DIRECTORY_SEPARATOR . $_filename, -($len), $len) == $tpl_file.$ext) {
    					$_res &= (bool)$this->_unlink($this->compile_dir . DIRECTORY_SEPARATOR . $_filename);
		    		}
    			}
    		}
    	}
    }

}

/**
 * This function contains the functionality for the block-level {l}{/l} tags used in IntSmarty templates
 * to provide the localization functionality. It is registered during the construction of an IntSmarty
 * object.
 *
 * @param string $content The content of the template before it is compiled
 * @param object $smarty A reference to the active Smarty class
 * @return string The original template with all of the translations in place
 */
function smarty_lang_prefilter($content, &$smarty) {
    $inst = &$smarty->parent_inst;
    $ldq = preg_quote($inst->left_delimiter, '!');
    $rdq = preg_quote($inst->right_delimiter, '!');
    /* Grab all of the tagged strings */
    preg_match_all("!{$ldq}l{$rdq}(.*?){$ldq}/l{$rdq}!s", $content, $match);
    foreach( $match[1] as $str) {
        $q_str = preg_quote($str);
        $hash = md5($str);
	/* Do we have a translation for this string? */
        if(key_exists($hash, $inst->translation)) {
            /* Replace all occurances of this string with its translation */
            $content = preg_replace("!{$ldq}l{$rdq}$q_str{$ldq}/l{$rdq}!s",
            utf8_decode($inst->translation [$hash]), $content);

        }  else {
            
            $inst->transtable_updated = true;
            $inst->translation[$hash] = $str;

        }


    }

    /* Strip off the tags now that the strings have been replaced */
    $content = preg_replace("!{$ldq}l{$rdq}(.*?){$ldq}/l{$rdq}!s",
    "\${1}", $content);

    return $content;

}

/**
 * This function is mapped directly to the {i18nfile} template function provided by
 * the IntSmarty class. The purpose of this function is to provide a standard method
 * of storing Localized images or other files which must be served without having
 * to have such information in the template itself.
 *
 * The {i18nfile} template function itself accepts  two parameters, 'file' (the
 * filename being resolved), and 'lang' (the language to resolve the file to). If
 * the language is not provided, the default current language will be used.
 * 
 * @param string $params The parameters provided to the i18nfile function
 * @param object $smarty the IntSmarty instance 
 * @return string The localized path of the file
 */
function smarty_function_i18nfile($params, &$smarty) {

    if(empty( $params['file'])) {
        $smarty->trigger_error("i18nfile: missing 'file' parameter");
        return;
    }

    $filepath = $params['file'];
    $filename = basename($filepath);
    $path = dirname($filepath);

    if(isset( $params['lang'])) {
        $language = $params['lang'];
    } else {
        $language = $smarty->cur_language;
    }

    $newfile = $path . DIR_SEP . $language . DIR_SEP . $filename;
    return $newfile;
}

?>
