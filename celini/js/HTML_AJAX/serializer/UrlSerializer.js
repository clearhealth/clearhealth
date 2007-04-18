// {{{ HTML_AJAX_Serialize_Urlencoded
/**
 * URL-encoding serializer
 *
 * This class can be used to serialize and unserialize data in a
 * format compatible with PHP's handling of HTTP query strings.
 * Due to limitations of the format, all input is serialized as an
 * array or a string. See examples/serialize.url.examples.php
 *
 * @version     0.0.1
 * @copyright   2005 Arpad Ray <arpad@php.net>
 * @license     http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for Author/license details
 */
function HTML_AJAX_Serialize_Urlencoded() {}
HTML_AJAX_Serialize_Urlencoded.prototype = {
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    base: '_HTML_AJAX',
    _keys: [],
    error: false,
    message: "",
    cont: "",
    // {{{ serialize
    /**
     *  Serializes a variable
     *
     *  @param     mixed  inp the variable to serialize
     *  @return    string   a string representation of the input, 
     *                      which can be reconstructed by unserialize()
     */
    serialize: function(input, _internal) {
        if (typeof input == 'undefined') {
            return '';
        }
        if (!_internal) {
            this._keys = [];
        }
        var ret = '', first = true;
        for (i = 0; i < this._keys.length; i++) {
            ret += (first ? HTML_AJAX_Util.encodeUrl(this._keys[i]) : '[' + HTML_AJAX_Util.encodeUrl(this._keys[i]) + ']');
            first = false;
        }
        ret += '=';
        switch (HTML_AJAX_Util.getType(input)) {
            case 'string': 
            case 'number':
                ret += HTML_AJAX_Util.encodeUrl(input.toString());
                break;
            case 'boolean':
                ret += (input ? '1' : '0');
                break;
            case 'array':
            case 'object':
                ret = '';
                for (i in input) {
                    this._keys.push(i);
                    ret += this.serialize(input[i], true) + '&';
                    this._keys.pop();
                }
                ret = ret.substr(0, ret.length - 1);
        }
        return ret;
    },
    // }}}
    // {{{ unserialize
    /**
     *  Reconstructs a serialized variable
     *
     *  @param    string inp the string to reconstruct
     *  @return   array an array containing the variable represented by the input string, or void on failure
     */
    unserialize: function(input) {
        if (!input.length || input.length == 0) {
            // null
            return;
        }
        if (!/^(\w+(\[[^\[\]]*\])*=[^&]*(&|$))+$/.test(input)) {
            this.raiseError("invalidly formed input", input);
            return;
        }
        input = input.split("&");
        var pos, key, keys, val, _HTML_AJAX = [];
        if (input.length == 1) {
            return HTML_AJAX_Util.decodeUrl(input[0].substr(this.base.length + 1));
        }
        for (var i in input) {
            pos = input[i].indexOf("=");
            if (pos < 1 || input[i].length - pos - 1 < 1) {
                this.raiseError("input is too short", input[i]);
                return;
            }
            key = HTML_AJAX_Util.decodeUrl(input[i].substr(0, pos));
            val = HTML_AJAX_Util.decodeUrl(input[i].substr(pos + 1));
            key = key.replace(/\[((\d*\D+)+)\]/g, '["$1"]');
            keys = key.split(']');
            for (j in keys) {
                if (!keys[j].length || keys[j].length == 0) {
                    continue;
                }
                try {
                    if (eval('typeof ' + keys[j] + ']') == 'undefined') {
                        var ev = keys[j] + ']=[];';
                        eval(ev);
                    }
                } catch (e) {
                    this.raiseError("error evaluating key", ev);
                    return; 
                }
            }
            try {
                eval(key + '="' + val + '";');
            } catch (e) {
                this.raiseError("error evaluating value", input);
                return; 
            }
        }
        return _HTML_AJAX;
    },
    // }}}
    // {{{ getError
    /**
    *  Gets the last error message
    *
    *  @return    string   the last error message from unserialize()
    */    
    getError: function() {
        return this.message + "\n" + this.cont;
    },
    // }}}
    // {{{ raiseError
    /**
    *  Raises an eror (called by unserialize().)
    *
    *  @param    string    message    the error message
    *  @param    string    cont       the remaining unserialized content
    */    
    raiseError: function(message, cont) {
        this.error = 1;
        this.message = message;
        this.cont = cont;
    }
    // }}}
}
// }}}
