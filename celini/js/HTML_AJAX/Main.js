/**
 * JavaScript library for use with HTML_AJAX
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to:
 * Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   HTML
 * @package    Ajax
 * @author     Joshua Eichorn <josh@bluga.net>
 * @author     Arpad Ray <arpad@php.net>
 * @author     David Coallier <davidc@php.net>
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005 Joshua Eichorn, Arpad Ray, David Coallier, Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */

/**
 * HTML_AJAX static methods, this is the main proxyless api, it also handles global error and event handling
 */
var HTML_AJAX = {
	defaultServerUrl: false,
	defaultEncoding: 'JSON',
    queues: false,
    clientPools: {},
    // get an HttpClient, supply a name to use the pool of that name or the default if it isn't found
    httpClient: function(name) {
        if (name) {
            if (this.clientPools[name]) {
                return this.clientPools[name].getClient();
            }
        }
        return this.clientPools['default'].getClient();
    },
    // Pushing the given request to queue specified by it, in default operation this will immediately make a request
    // request might be delayed or never happen depending on the queue setup
    // making a sync request to a non immediate queue will cause you problems so just don't do it
    makeRequest: function(request) {
        if (!HTML_AJAX.queues[request.queue]) {
            var e = new Error('Unknown Queue: '+request.queue);
            if (HTML_AJAX.onError) {
                HTML_AJAX.onError(e);
                return false;
            }
            else {
                throw(e);
            }
        }
        else {
            var qn = request.queue;
            var q = HTML_AJAX.queues[qn];

            HTML_AJAX.queues[request.queue].addRequest(request);
            return HTML_AJAX.queues[request.queue].processRequest();
        }
    },
    // get a serializer object for a specific encoding
    serializerForEncoding: function(encoding) {
        for(var i in HTML_AJAX.contentTypeMap) {
            if (encoding == HTML_AJAX.contentTypeMap[i] || encoding == i) {
                return eval("new HTML_AJAX_Serialize_"+i+";");
            }
        }
        return new HTML_AJAX_Serialize_Null();
    },
	fullcall: function(url,encoding,className,method,callback,args, options) {
        var serializer = HTML_AJAX.serializerForEncoding(encoding);

        var request = new HTML_AJAX_Request(serializer);
		if (callback) {
            request.isAsync = true;
		}
        request.requestUrl = url;
        request.className = className;
        request.methodName = method;
        request.callback = callback;
        request.args = args;
        if (options) {
            for(var i in options) {
                request[i] = options[i];
            }
            if (options.grab) {
                if (!request.args || !request.args.length) {
                    request.requestType = 'GET';
                }
            }
        }

        return HTML_AJAX.makeRequest(request);
	},
    callPhpCallback: function(phpCallback, jsCallback, url) {
        var args = new Array();
        for (var i = 3; i < arguments.length; i++) {
            args.push(arguments[i]);
        }
        if (HTML_AJAX_Util.getType(phpCallback[0]) == 'object') {
            jsCallback(phpCallback[0][phpCallback[1]](args));
            return;
        }
        if (!url) {
            url = HTML_AJAX.defaultServerUrl;
        }
        HTML_AJAX.fullcall(url, HTML_AJAX.defaultEncoding,
            false, false, jsCallback, args, {phpCallback: phpCallback});
    },
	call: function(className,method,callback) {
        var args = new Array();
        for(var i = 3; i < arguments.length; i++) {
            args.push(arguments[i]);
        }
		return HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,className,method,callback,args);
	},
	grab: function(url,callback,options) {
        if (!options) {
            options = {grab:true};
        }
        else {
            options['grab'] = true;
        }
		return HTML_AJAX.fullcall(url,'Null',false,null,callback, '', options);
	},
    post: function(url,payload,callback,options) {
        var serializer = 'Null';
        if (HTML_AJAX_Util.getType(payload) == 'object') {
            serializer = 'Urlencoded';
        }
		return HTML_AJAX.fullcall(url,serializer,false,null,callback, payload, options);
    },
	replace: function(id) {
        var callback = function(result) {
            HTML_AJAX_Util.setInnerHTML(document.getElementById(id),result);
        }
		if (arguments.length == 2) {
			// grab replacement
            HTML_AJAX.grab(arguments[1],callback);
		}
		else {
			// call replacement
			var args = new Array();
			for(var i = 3; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,arguments[1],arguments[2],callback,args, {grab:true});
		}
	},
    append: function(id) {
        var callback = function(result) {
            HTML_AJAX_Util.setInnerHTML(document.getElementById(id),result,'append');
        }
        if (arguments.length == 2) {
            // grab replacement
            HTML_AJAX.grab(arguments[1],callback);
        }
        else {
            // call replacement
            var args = new Array();
            for(var i = 3; i < arguments.length; i++) {
                args.push(arguments[i]);
            }
            HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,arguments[1],arguments[2],callback,args, {grab:true});
        }
    }, 
    // override to add top level loading notification (start)
    Open: function(request) {
    },
    // override to add top level loading notification (finish)
    Load: function(request) {
    },
    /*
    // A really basic error handler 
    onError: function(e) {
        msg = "";
        for(var i in e) {
            msg += i+':'+e[i]+"\n";
        }
        alert(msg);
    },
    */
    // Class postfix to content-type map
    contentTypeMap: {
        'JSON':         'application/json',
        'Null':         'text/plain',
        'Error':        'application/error',
        'PHP':          'application/php-serialized',
		'HA' :           'application/html_ajax_action',
        'Urlencoded':   'application/x-www-form-urlencoded'
    },
    // used internally to make queues work, override Load or onError to perform custom events when a request is complete
    // fires on success and error
    requestComplete: function(request,error) {
        for(var i in HTML_AJAX.queues) {
            if (HTML_AJAX.queues[i].requestComplete) {
                HTML_AJAX.queues[i].requestComplete(request,error);
            }
        }
    },
    
    // turns a form into a urlencoded string
    formEncode: function(form, array_format) {
        form = HTML_AJAX_Util.getElement(form);
        var el, inpType, value, name;
        var out = (array_format) ? {} : '';
		var inputTags = form.getElementsByTagName('INPUT');
		var selectTags = form.getElementsByTagName('SELECT');
		var buttonTags = form.getElementsByTagName('BUTTON');
		var textareaTags = form.getElementsByTagName('TEXTAREA');
        var arrayRegex = /(.+)%5B%5D/;

        var validElement = function (element) {
            if (!element || !element.getAttribute) {
                return false;
            }
            el = element;
            name = HTML_AJAX_Util.encodeUrl(el.getAttribute('name'));
            if (!name) {
                // no element name so skip
                return false;
            }
            if (element.disabled) {
                return false;
            }
			
            value = HTML_AJAX_Util.encodeUrl(el.value);
            inpType = el.getAttribute('type');
            return true;
        }
        
        inputLoop:
        for (var i=0; i < inputTags.length; i++) {
            if (!validElement(inputTags[i])) {
                continue;
            }
            if (inpType == 'checkbox' || inpType == 'radio') {
                if (!el.checked) {
                    // unchecked radios/checkboxes don't get submitted
                    continue inputLoop;
                }
                var arr_var = arrayRegex.exec(name); 
                if (array_format && arr_var) {
                    if (!out[arr_var[1]]) {
                        out[arr_var[1]] = new Array();
                    }
                    out[arr_var[1]].push(value);
                    continue inputLoop;
                }
            }
            // add element to output array
			if (array_format) {
				out[name] = value;
			} else {
				out += name + '=' + value + '&';
			}
        } // end inputLoop

        selectLoop:
        for (var i=0; i<selectTags.length; i++) {
            if (!validElement(selectTags[i])) {
                continue selectLoop;
            }
            var options = el.options;
            for (var z=0; z<options.length; z++){
                var option=options[z];
                if(option.selected){
                    if (array_format) {
                        if (el.type == 'select-one') {
                            out[name] = option.value;
                            //only one item can be selected
                            continue selectLoop;
                        } else {
                            if (!out[name]) {
                                out[name] = new Array();
                            }
                            out[name].push(option.value);
                        }
                    } else {
                        out += name + '=' + option.value + '&';
                        if (el.type == 'select-one') {
                            continue selectLoop;
                        }
                    }
                }
            }
        } // end selectLoop

        buttonLoop:
        for (var i=0; i<buttonTags.length; i++) {
            if (!validElement(buttonTags[i])) {
                continue;
            }
            // add element to output array
			if (array_format) {
				out[name] = value;
			} else {
				out += name + '=' + value + '&';
			}
        } // end buttonLoop

        textareaLoop:
        for (var i=0; i<textareaTags.length; i++) {
            if (!validElement(textareaTags[i])) {
                continue;
            }
            // add element to output array
			if (array_format) {
				out[name] = value;
			} else {
				out += name + '=' + value + '&';
			}
        } // end textareaLoop
        
        return out;
    },
    // submits a form through ajax. both arguments can be either DOM nodes or IDs, if the target is omitted then the form is set to be the target
    formSubmit: function (form, target, options)
    {
        form = HTML_AJAX_Util.getElement(form);
        if (!form) {
        // let the submit be processed normally
            return false;
        }

        var out = HTML_AJAX.formEncode(form);
        target = HTML_AJAX_Util.getElement(target);
        if (!target) {
            target = form;
        }
        var action = form.attributes['action'].value;
        var callback = function(result) {
            HTML_AJAX_Util.setInnerHTML(target,result);
        }

        var serializer = HTML_AJAX.serializerForEncoding('Null');
        var request = new HTML_AJAX_Request(serializer);
        request.isAsync = true;
        request.callback = callback;

        switch (form.getAttribute('method').toLowerCase()) {
        case 'post':
            var headers = {};
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
            request.customHeaders = headers;
            request.requestType = 'POST';
            request.requestUrl = action;
            request.args = out;
            break;
        default:
            if (action.indexOf('?') == -1) {
                out = '?' + out.substr(0, out.length - 1);
            }
            request.requestUrl = action+out;
            request.requestType = 'GET';
        }

        if(options) {
            for(var i in options) {
                request[i] = options[i];
            }
        }
        HTML_AJAX.makeRequest(request);
        return true;
    }, // end formSubmit()
    makeFormAJAX: function(form,target,options) {
        form = HTML_AJAX_Util.getElement(form);
        var preSubmit = false;
        if(typeof form.onsubmit != 'undefined') {
            preSubmit = form.onsubmit;
            form.onsubmit = function() {};
        }
        form.HAOptions = options;
        var handler = function(e) {
            var form = HTML_AJAX_Util.eventTarget(e);

            var valid = true;
            if (preSubmit) {
                valid = preSubmit();
            }
		    if (valid) {
			    HTML_AJAX.formSubmit(form,target,form.HAOptions);
			}
            // cancel submission in IE
            e.returnValue = false;
            // cancel submission in FF
            if (e.preventDefault) {
                e.preventDefault();
            }
        }
		HTML_AJAX_Util.registerEvent(form,'submit',handler);
    }
}




// small classes that I don't want to put in there own file

function HTML_AJAX_Serialize_Null() {}
HTML_AJAX_Serialize_Null.prototype = {
	contentType: 'text/plain; charset=utf-8',
	serialize: function(input) {
		return new String(input).valueOf();
	},
	
	unserialize: function(input) {
		return new String(input).valueOf();	
	}
}

// serialization class for JSON, wrapper for JSON.stringify in json.js
function HTML_AJAX_Serialize_JSON() {}
HTML_AJAX_Serialize_JSON.prototype = {
	contentType: 'application/json; charset=utf-8',
	serialize: function(input) {
		return HTML_AJAX_JSON.stringify(input);
	},
	unserialize: function(input) {
        try {
            return eval('('+input+')');
        } catch(e) {
            // sometimes JSON encoded input isn't created properly, if eval of it fails we use the more forgiving but slower parser so will at least get something
            return HTML_AJAX_JSON.parse(input);
        }
	}
}

function HTML_AJAX_Serialize_Error() {}
HTML_AJAX_Serialize_Error.prototype = {
	contentType: 'application/error; charset=utf-8',
	serialize: function(input) {
        var ser = new HTML_AJAX_Serialize_JSON();
        return ser.serialize(input);
	},
	unserialize: function(input) {
        var ser = new HTML_AJAX_Serialize_JSON();
        var data = new ser.unserialize(input);

        var e = new Error('PHP Error: '+data.errStr);
        for(var i in data) {
            e[i] = data[i];
        }
        throw e;
	}
}

// Processing Queues

// simple queue, just processes the request immediately
function HTML_AJAX_Queue_Immediate() {}
HTML_AJAX_Queue_Immediate.prototype = {
    request: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        var client = HTML_AJAX.httpClient();
        client.request = this.request;
        return client.makeRequest();
    }
    // requestComplete: function() {} // this is also possible but this queue doesn't need it
}



// create a default queue, has to happen after the Queue class has been defined
HTML_AJAX.queues = new Object();
HTML_AJAX.queues['default'] = new HTML_AJAX_Queue_Immediate();

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
