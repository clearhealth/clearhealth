// {{{ HTML_AJAX_Serialize_PHP
/**
 * PHP serializer
 *
 * This class can be used to serialize and unserialize data in a
 * format compatible with PHP's native serialization functions.
 *
 * @version     0.0.3
 * @copyright   2005 Arpad Ray <arpad@php.net>
 * @license     http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for Author/license details
 */

function HTML_AJAX_Serialize_PHP() {}
HTML_AJAX_Serialize_PHP.prototype = {
    error: false,
    message: "",
    cont: "",
    defaultEncoding: 'UTF-8',
    contentType: 'application/php-serialized; charset: UTF-8',
    // {{{ serialize
    /**
    *  Serializes a variable
    *
    *  @param     mixed  inp the variable to serialize
    *  @return    string   a string representation of the input, 
    *                      which can be reconstructed by unserialize()
    *  @author Arpad Ray <arpad@rajeczy.com>
    *  @author David Coallier <davidc@php.net>
    */
    serialize: function(inp) {
        var type = HTML_AJAX_Util.getType(inp);
        var val;
        switch (type) {
            case "undefined":
                val = "N";
                break;
            case "boolean":
                val = "b:" + (inp ? "1" : "0");
                break;
            case "number":
                val = (Math.round(inp) == inp ? "i" : "d") + ":" + inp;
                break;
            case "string":
                val = "s:" + inp.length + ":\"" + inp + "\"";
                break;
            case "array":
                val = "a";
            case "object":
                if (type == "object") {
                    var objname = inp.constructor.toString().match(/(\w+)\(\)/);
                    if (objname == undefined) {
                        return;
                    }
                    objname[1] = this.serialize(objname[1]);
                    val = "O" + objname[1].substring(1, objname[1].length - 1);
                }
                var count = 0;
                var vals = "";
                var okey;
                for (key in inp) {
                    okey = (key.match(/^[0-9]+$/) ? parseInt(key) : key);
                    vals += this.serialize(okey) + 
                            this.serialize(inp[key]);
                    count++;
                }
                val += ":" + count + ":{" + vals + "}";
                break;
        }
        if (type != "object" && type != "array") val += ";";
        return val;
    },
    // }}} 
    // {{{ unserialize
    /**
     *  Reconstructs a serialized variable
     *
     *  @param    string inp the string to reconstruct
     *  @return   mixed the variable represented by the input string, or void on failure
     */
    unserialize: function(inp) {
        this.error = 0;
        if (inp == "" || inp.length < 2) {
            this.raiseError("input is too short");
            return;
        }
        var val, kret, vret, cval;
        var type = inp.charAt(0);
        var cont = inp.substring(2);
        var size = 0, divpos = 0, endcont = 0, rest = "", next = "";

        switch (type) {
        case "N": // null
            if (inp.charAt(1) != ";") {
                this.raiseError("missing ; for null", cont);
            }
            // leave val undefined
            rest = cont;
            break;
        case "b": // boolean
            if (!/[01];/.test(cont.substring(0,2))) {
                this.raiseError("value not 0 or 1, or missing ; for boolean", cont);
            }
            val = (cont.charAt(0) == "1");
            rest = cont.substring(1);
            break;
        case "s": // string
            val = "";
            divpos = cont.indexOf(":");
            if (divpos == -1) {
                this.raiseError("missing : for string", cont);
                break;
            }
            size = parseInt(cont.substring(0, divpos));
            if (size == 0) {
                if (cont.length - divpos < 4) {
                    this.raiseError("string is too short", cont);
                    break;
                }
                rest = cont.substring(divpos + 4);
                break;
            }
            if ((cont.length - divpos - size) < 4) {
                this.raiseError("string is too short", cont);
                break;
            }
            if (cont.substring(divpos + 2 + size, divpos + 4 + size) != "\";") {
                this.raiseError("string is too long, or missing \";", cont);
            }
            val = cont.substring(divpos + 2, divpos + 2 + size);
            rest = cont.substring(divpos + 4 + size);
            break;
        case "i": // integer
        case "d": // float
            var dotfound = 0;
            for (var i = 0; i < cont.length; i++) {
                cval = cont.charAt(i);
                if (isNaN(parseInt(cval)) && !(type == "d" && cval == "." && !dotfound++)) {
                    endcont = i;
                    break;
                }
            }
            if (!endcont || cont.charAt(endcont) != ";") {
                this.raiseError("missing or invalid value, or missing ; for int/float", cont);
            }
            val = cont.substring(0, endcont);
            val = (type == "i" ? parseInt(val) : parseFloat(val));
            rest = cont.substring(endcont + 1);
            break;
        case "a": // array
            if (cont.length < 4) {
                this.raiseError("array is too short", cont);
                return;
            }
            divpos = cont.indexOf(":", 1);
            if (divpos == -1) {
                this.raiseError("missing : for array", cont);
                return;
            }
            size = parseInt(cont.substring(0, divpos));
            cont = cont.substring(divpos + 2);
            val = new Array();
            if (cont.length < 1) {
                this.raiseError("array is too short", cont);
                return;
            }
            for (var i = 0; i < size; i++) {
                kret = this.unserialize(cont, 1);
                if (this.error || kret[0] == undefined || kret[1] == "") {
                    this.raiseError("missing or invalid key, or missing value for array", cont);
                    return;
                }
                vret = this.unserialize(kret[1], 1);
                if (this.error) {
                    this.raiseError("invalid value for array", cont);
                    return;
                }
                val[kret[0]] = vret[0];
                cont = vret[1];
            }
            if (cont.charAt(0) != "}") {
                this.raiseError("missing ending }, or too many values for array", cont);
                return; 
            }
            rest = cont.substring(1);
            break;
        case "O": // object
            divpos = cont.indexOf(":");
            if (divpos == -1) {
                this.raiseError("missing : for object", cont);
                return;
            }
            size = parseInt(cont.substring(0, divpos));
            var objname = cont.substring(divpos + 2, divpos + 2 + size);
            if (cont.substring(divpos + 2 + size, divpos + 4 + size) != "\":") {
                this.raiseError("object name is too long, or missing \":", cont);
                return;
            }
            var objprops = this.unserialize("a:" + cont.substring(divpos + 4 + size), 1);
            if (this.error) {
                this.raiseError("invalid object properties", cont);
                return;
            }
            rest = objprops[1];
            var objout = "function " + objname + "(){";
            for (key in objprops[0]) {
                objout += "this." + key + "=objprops[0]['" + key + "'];";
            }
            objout += "}val=new " + objname + "();";
            eval(objout);
            break;
        default:
            this.raiseError("invalid input type", cont);
        }
        return (arguments.length == 1 ? val : [val, rest]);
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

