/**
 * Compat functions
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */
/**
 *  Functions for compatibility with older browsers
 */
if (!String.fromCharCode && !String.prototype.fromCharCode) {
    String.prototype.fromCharCode = function(code)
    {
        var h = code.toString(16);
        if (h.length == 1) {
            h = '0' + h;
        }
        return unescape('%' + h);
    }
}
if (!String.charCodeAt && !String.prototype.charCodeAt) {
    String.prototype.charCodeAt = function(index)
    {
        var c = this.charAt(index);
        for (i = 1; i < 256; i++) {
            if (String.fromCharCode(i) == c) {
                return i;
            }
        } 
    }
}
// http://www.crockford.com/javascript/remedial.html
if (!Array.splice && !Array.prototype.splice) {
    Array.prototype.splice = function(s, d)
    {
        var max = Math.max,
        min = Math.min,
        a = [], // The return value array
        e,  // element
        i = max(arguments.length - 2, 0),   // insert count
        k = 0,
        l = this.length,
        n,  // new length
        v,  // delta
        x;  // shift count

        s = s || 0;
        if (s < 0) {
            s += l;
        }
        s = max(min(s, l), 0);  // start point
        d = max(min(typeof d == 'number' ? d : l, l - s), 0);    // delete count
        v = i - d;
        n = l + v;
        while (k < d) {
            e = this[s + k];
            if (!e) {
                a[k] = e;
            }
            k += 1;
        }
        x = l - s - d;
        if (v < 0) {
            k = s + i;
            while (x) {
                this[k] = this[k - v];
                k += 1;
                x -= 1;
            }
            this.length = n;
        } else if (v > 0) {
            k = 1;
            while (x) {
                this[n - k] = this[l - k];
                k += 1;
                x -= 1;
            }
        }
        for (k = 0; k < i; ++k) {
            this[s + k] = arguments[k + 2];
        }
        return a;
    }
}
if (!Array.push && !Array.prototype.push) {
    Array.prototype.push = function()
    {
        for (var i = 0, startLength = this.length; i < arguments.length; i++) {
            this[startLength + i] = arguments[i];
        }
        return this.length;
    }
}
if (!Array.pop && !Array.prototype.pop) {
    Array.prototype.pop = function()
    {
        return this.splice(this.length - 1, 1)[0];
    }
}
/*
    From IE7, version 0.9 (alpha) (2005-08-19)
    Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
*/
if (!DOMParser && window.ActiveXObject)
{
function DOMParser() {/* empty constructor */};
DOMParser.prototype = {
    parseFromString: function(str, contentType) {
        var xmlDocument = new ActiveXObject('Microsoft.XMLDOM');
        xmlDocument.loadXML(str);
        return xmlDocument;
    }
};

function XMLSerializer() {/* empty constructor */};
XMLSerializer.prototype = {
    serializeToString: function(root) {
        return root.xml || root.outerHTML;
    }
};
}
