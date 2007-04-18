/**
 * Utility methods
 *
 * @category   HTML
 * @package    Ajax
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for author/license details
 */
// {{{ HTML_AJAX_Util
/**
 * All the utilities we will be using thorough the classes
 */
var HTML_AJAX_Util = {
    // Set the element event
    registerEvent: function(element, event, handler) 
    {
        element = this.getElement(element);
		if (typeof element.addEventListener != "undefined") {   //Dom2
            element.addEventListener(event, handler, false);
        } else if (typeof element.attachEvent != "undefined") { //IE 5+
            element.attachEvent("on" + event, handler);
        } else {
            if (element["on" + event] != null) {
                var oldHandler = element["on" + event];
                element["on" + event] = function(e) {
                    oldHander(e);
                    handler(e);
                };
            } else {
                element["on" + event] = handler;
            }
        }
    },
    // get the target of an event, automatically checks window.event for ie
    eventTarget: function(event) 
    {
        if (!event) var event = window.event;
        if (event.target) return event.target; // w3c
        if (event.srcElement) return event.srcElement; // ie 5
    },
    // gets the type of a variable or its primitive equivalent as a string
    getType: function(inp) 
    {
        var type = typeof inp, match;
        if(type == 'object' && !inp)
        {
            return 'null';
        }
        if (type == "object") {
            if(!inp.constructor)
            {
                return 'object';
            }
            var cons = inp.constructor.toString();
            if (match = cons.match(/(\w+)\(/)) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    },
    // repeats the input string the number of times given by multiplier. exactly like PHP's str_repeat()
    strRepeat: function(inp, multiplier) {
        var ret = "";
        while (--multiplier > 0) ret += inp;
        return ret;
    },
    // encode a string allowing it to be used in a query string of a url
    encodeUrl: function(input) {
        return encodeURIComponent(input);
    },
    // decode a url encoded string
    decodeUrl: function(input) {
        return decodeURIComponent(input);
    },
    // recursive variable dumper similar in output to PHP's var_dump(), the differences being: this function displays JS types and type names; JS doesn't provide an object number like PHP does
    varDump: function(inp, printFuncs, _indent, _recursionLevel)
    {
        if (!_recursionLevel) _recursionLevel = 0;
        if (!_indent) _indent = 1;
        var tab = this.strRepeat("  ", ++_indent);    
        var type = this.getType(inp), out = type;
        var consrx = /(\w+)\(/;
        consrx.compile();
        if (++_recursionLevel > 6) {
            return tab + inp + "Loop Detected\n";
        }
        switch (type) {
            case "boolean":
            case "number":
                out += "(" + inp.toString() + ")";
                break;
            case "string":
                out += "(" + inp.length + ") \"" + inp + "\"";
                break;
            case "function":
                if (printFuncs) {
                    out += inp.toString().replace(/\n/g, "\n" + tab);
                }
                break;
            case "array":
            case "object":
                var atts = "", attc = 0;
                try {
                    for (k in inp) {
                        atts += tab + "[" + (/\D/.test(k) ? "\"" + k + "\"" : k)
                            + "]=>\n" + tab + this.varDump(inp[k], printFuncs, _indent, _recursionLevel);
                        ++attc;
                    }
                } catch (e) {}
                if (type == "object") {
                    var objname, objstr = inp.toString();
                    if (objname = objstr.match(/^\[object (\w+)\]$/)) {
                        objname = objname[1];
                    } else {
                        try {
                            objname = inp.constructor.toString().match(consrx)[1];
                        } catch (e) {
                            objname = 'unknown';
                        }
                    }
                    out += "(" + objname + ") ";
                }
                out += "(" + attc + ") {\n" + atts + this.strRepeat("  ", _indent - 1) +"}";
                break;
        }
        return out + "\n";
    },
    // non resursive simple debug printer
    quickPrint: function(input,sep) {
        if (!sep) {
            var sep = "\n";
        }
        var type = HTML_AJAX_Util.getType(input);
        switch (type) {
            case 'string':
                return input;
            case 'array':
                var ret = "";
                for(var i = 0; i < input.length; i++) {
                    ret += i+':'+input[i]+sep;
                }
                return ret;
            default:
                var ret = "";
                for(var i in input) {
                    ret += i+':'+input[i]+sep;
                }
                return ret;
        }
    },
    //compat function for stupid browsers in which getElementsByTag with a * dunna work
    getAllElements: function(parentElement)
    {
        //check for idiot browsers
        if( document.all)
        {
            if(!parentElement) {
                var allElements = document.all;
            }
            else
            {
                var allElements = [], rightName = new RegExp( parentElement, 'i' ), i;
                for( i=0; i<document.all.length; i++ ) {
                    if( rightName.test( document.all[i].parentElement ) )
                    allElements.push( document.all[i] );
                }
            }
            return allElements;
        }
        //real browsers just do this
        else
        {
            if (!parentElement) { parentElement = document.body; }
            return parentElement.getElementsByTagName('*');
        }
    },
    getElementsByProperty: function(property, regex, parentElement) {
        var allElements = HTML_AJAX_Util.getAllElements(parentElement);
        var items = [];
        for(var i=0,j=allElements.length; i<j; i++)
        {
            if(regex.test(allElements[i][property]))
            {
                items.push(allElements[i]);
            }
        }
        return items;
    },
    getElementsByClassName: function(className, parentElement) {
        return HTML_AJAX_Util.getElementsByProperty('className',new RegExp('(^| )' + className + '( |$)'),parentElement);
    },
    getElementsById: function(id, parentElement) {
        return HTML_AJAX_Util.getElementsByProperty('id',new RegExp(id),parentElement);
    },
    getElementsByCssSelector: function(selector,parentElement) {
        return cssQuery(selector,parentElement);
    },
    htmlEscape: function(inp) {
        var div = document.createElement('div');
        var text = document.createTextNode(inp);
        div.appendChild(text);
        return div.innerHTML;
    },
    // return the base of the given absolute url, or the filename if the second argument is true
    baseURL: function(absolute, filename) {
        var qPos = absolute.indexOf('?');
        if (qPos >= 0) {
            absolute = absolute.substr(0, qPos);
        }
        var slashPos = Math.max(absolute.lastIndexOf('/'), absolute.lastIndexOf('\\'));
        if (slashPos < 0) {
            return absolute;
        }
        return (filename ? absolute.substr(slashPos + 1) : absolute.substr(0, slashPos + 1));
    },
    // return the query string from a url
    queryString: function(url) {
        var qPos = url.indexOf('?');
        if (qPos >= 0) {
            return url.substr(qPos+1);
        }
    },
    // return the absolute path to the given relative url
    absoluteURL: function(rel, absolute) {
        if (/^https?:\/\//i.test(rel)) {
            return rel;
        }
        if (!absolute) {
            var bases = document.getElementsByTagName('base');
            for (i in bases) {
                if (bases[i].href) {
                    absolute = bases[i].href;
                    break;
                }
            }
            if (!absolute) {
                absolute = window.location.href;
            }
        }
        if (rel == '') {
            return absolute;
        }
        if (rel.substr(0, 2) == '//') {
            // starts with '//', replace everything but the protocol
            var slashesPos = absolute.indexOf('//');
            if (slashesPos < 0) {
                return 'http:' + rel;
            }
            return absolute.substr(0, slashesPos) + rel;
        }
        var base = this.baseURL(absolute);
        var absParts = base.substr(0, base.length - 1).split('/');
        var absHost = absParts.slice(0, 3).join('/') + '/';
        if (rel.substr(0, 1) == '/') {
            // starts with '/', append it to the host
            return absHost + rel;
        }
        if (rel.substr(0, 1) == '.' && rel.substr(1, 1) != '.') {
            // starts with '.', append it to the base
            return base + rel.substr(1);
        }
        // remove everything upto the path and beyond 
        absParts.splice(0, 3);
        var relParts = rel.split('/');
        var loopStart = relParts.length - 1;
        relParts = absParts.concat(relParts);
        for (i = loopStart; i < relParts.length;) {
            if (relParts[i] == '..') {
                if (i == 0) {
                    return absolute;
                }
                relParts.splice(i - 1, 2);
                --i;
                continue;
            }
            i++;
        }
        return absHost + relParts.join('/');
    },
    // sets the innerHTML of an element. the third param decides how to write, it replaces by default, others are append|prepend
    setInnerHTML: function(node, innerHTML, type)
    {
        node = this.getElement(node);

        if (type != 'append') {
            if (type == 'prepend') {
                var oldHtml = node.innerHTML;
            }
            node.innerHTML = '';
        }
        var good_browser = (window.opera || navigator.product == 'Gecko');
        var regex = /^([\s\S]*?)<script([\s\S]*?)>([\s\S]*?)<\/script>([\s\S]*)$/i;
        var regex_src = /src=["'](.*?)["']/i;
        var matches, id, script, output = '', subject = innerHTML;
        var scripts = [];
        
        while (true) {
            matches = regex.exec(subject);
            if (matches && matches[0]) {
                subject = matches[4];
                id = 'ih_' + Math.round(Math.random()*9999) + '_' + Math.round(Math.random()*9999);

                var startLen = matches[3].length;
                script = matches[3].replace(/document\.write\(([\s\S]*?)\)/ig, 
                    'document.getElementById("' + id + '").innerHTML+=$1');

                output += matches[1];
                if (startLen != script.length) {
                        output += '<span id="' + id + '"></span>';
                }
                
                output += '<script' + matches[2] + '>' + script + '</script>';
                if (good_browser) {
                    continue;
                }
                if (script) {
                    scripts.push(script);
                }
                if (regex_src.test(matches[2])) {
                    var script_el = document.createElement("SCRIPT");
                    var atts_regex = /(\w+)=["'](.*?)["']([\s\S]*)$/;
                    var atts = matches[2];
                    for (var i = 0; i < 5; i++) { 
                        var atts_matches = atts_regex.exec(atts);
                        if (atts_matches && atts_matches[0]) {
                            script_el.setAttribute(atts_matches[1], atts_matches[2]);
                            atts = atts_matches[3];
                        } else {
                            break;
                        }
                    }
                    scripts.push(script_el);
                }
            } else {
                output += subject;
                break;
            }
        }
        innerHTML = output;

        if (good_browser) {
            var el = document.createElement('span');
            el.innerHTML = innerHTML;

            for(var i = 0; i < el.childNodes.length; i++) {
                node.appendChild(el.childNodes[i].cloneNode(true));
            }
        }
        else {
            node.innerHTML += innerHTML;
        }

        if (oldHtml) {
            node.innerHTML += oldHtml;
        }

        if (!good_browser) {
            for(var i = 0; i < scripts.length; i++) {
                if (HTML_AJAX_Util.getType(scripts[i]) == 'string') {
                    scripts[i] = scripts[i].replace(/^\s*<!(\[CDATA\[|--)|((\/\/)?--|\]\])>\s*$/g, '');
                    window.eval(scripts[i]);
                }
                else {
                    node.appendChild(scripts[i]);
                }
            }
        }
        return;
    },
    classSep: '(^|$| )',
    hasClass: function(o, className) {
        var o = this.getElement(o);
        var regex = new RegExp(this.classSep + className + this.classSep);
        return regex.test(o.className);
    },
    addClass: function(o, className) {
        var o = this.getElement(o);
        if(!this.hasClass(o, className)) {
            o.className += " " + className;
        }
    },
    removeClass: function(o, className) {
        var o = this.getElement(o);
        var regex = new RegExp(this.classSep + className + this.classSep);
        o.className = o.className.replace(regex, " ");
    },
    replaceClass: function(o, oldClass, newClass) {
        var o = this.getElement(o);
        var regex = new RegExp(this.classSep + oldClass + this.classSep);
        o.className = o.className.replace(regex, newClass);
    },
    getElement: function(el) {
        if (typeof el == 'string') {
            return document.getElementById(el);
        }
        return el;
    }
}
// }}}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
