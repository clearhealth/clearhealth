<?php

/**
 * Smarty {assign_by_ref} compiler function plugin
 *
 * Type:     compiler function<br>
 * Name:     assign_by_ref<br>
 * Purpose:  assign a value to a template variable by reference
 * @link http://smarty.php.net/manual/en/language.custom.functions.php#LANGUAGE.FUNCTION.ASSIGN {assign}
 *       (Smarty online manual)
 * @param string containing var-attribute and value-attribute
 * @param Smarty_Compiler
 */
function smarty_compiler_assign_by_ref($tag_attrs, &$compiler)
{
    $_params = $compiler->_parse_attrs($tag_attrs);

    if (!isset($_params['var'])) {
        $compiler->_syntax_error("assign_by_ref: missing 'var' parameter", E_USER_WARNING);
        return;
    }

    if (!isset($_params['value'])) {
        $compiler->_syntax_error("assign_by_ref: missing 'value' parameter", E_USER_WARNING);
        return;
    }

    return "\$this->assign_by_ref({$_params['var']}, {$_params['value']});";
}

/* vim: set expandtab: */

?>