// Compat.js
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
// Main.js
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
// Queue.js
/**
 * Various processing queues, use when you want to control how multiple requests are made
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */

// Single Buffer queue with interval
// works by attempting to send a request every x miliseconds
// if an item is currently in the queue when a new item is added it will be replaced
// simple queue, just processes the request immediately
// the first request starts the interval timer
function HTML_AJAX_Queue_Interval_SingleBuffer(interval,singleOutstandingRequest) {
    this.interval = interval;
    if (singleOutstandingRequest) {
        this.singleOutstandingRequest = true;
    }
}
HTML_AJAX_Queue_Interval_SingleBuffer.prototype = {
    request: false,
    _intervalId: false,
    singleOutstandingRequest: false,
    client: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        if (!this._intervalId) {
            this.runInterval();
            this.start();
        }
    }, 
    start: function() {
        var self = this;
        this._intervalId = setInterval(function() { self.runInterval() },this.interval);
    },
    stop: function() {
        clearInterval(this._intervalId);
    },
    runInterval: function() {
        if (this.request) {
            if (this.singleOutstandingRequest && this.client) {
                this.client.abort();
            }
            this.client = HTML_AJAX.httpClient();
            this.client.request = this.request;
            this.request = false;
            this.client.makeRequest();
        }
    }
}

// Requests return in the same order they were called
// this helps handle high latency situations
function HTML_AJAX_Queue_Ordered() { }
HTML_AJAX_Queue_Ordered.prototype = {
    request: false,
    order: 0,
    current: 0,
    callbacks: {},
    interned: {},
    addRequest: function(request) {
        request.order = this.order;
        this.request = request;
        this.callbacks[this.order] = this.request.callback;
        var self = this;
        this.request.callback = function(result) {
            self.processCallback(result,request.order);
        } 
    },
    processRequest: function() {
        var client = HTML_AJAX.httpClient();
        client.request = this.request;
        client.makeRequest();
        this.order++;
    },
    requestComplete: function(request,e) {
        // something when wrong with the request lets stop waiting for it
        if (e) {
            this.current++;
        }
    },
    processCallback: function(result,order) {
        if (order == this.current) {
            this.callbacks[order](result);
            this.current++;
        }
        else {
            this.interned[order] = result;
            if (this.interned[this.current]) {
                this.callbacks[this.current](this.interned[this.current]);
                this.current++;
            }
        }
    } 
}

// Make a single request at once, canceling and currently outstanding requests when a new one is made
function HTML_AJAX_Queue_Single() {
}
HTML_AJAX_Queue_Single.prototype = {
    request: false,
    client: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        if (this.request) {
            if (this.client) {
                this.client.abort();
            }
            this.client = HTML_AJAX.httpClient();
            this.client.request = this.request;
            this.request = false;
            this.client.makeRequest();
        }
    }
}

/**
 * Priority queue
 *
 * @author     Arpad Ray <arpad@php.net>
 */
function HTML_AJAX_Queue_Priority_Item(item, time) {
    this.item = item;
    this.time = time;
}
HTML_AJAX_Queue_Priority_Item.prototype = {
    compareTo: function (other) {
        var ret = this.item.compareTo(other.item);
        if (ret == 0) {
            ret = this.time - other.time;
        }
        return ret;
    }
}

function HTML_AJAX_Queue_Priority_Simple(interval) {
    this.interval = interval;   
    this.idleMax = 10;            // keep the interval going with an empty queue for 10 intervals
    this.requestTimeout = 5;      // retry uncompleted requests after 5 seconds
    this.checkRetryChance = 0.1;  // check for uncompleted requests to retry on 10% of intervals
    this._intervalId = 0;
    this._requests = [];
    this._removed = [];
    this._len = 0;
    this._removedLen = 0;
    this._idle = 0;
}
HTML_AJAX_Queue_Priority_Simple.prototype = {
    isEmpty: function () {
        return this._len == 0;
    },
    addRequest: function (request) {
        request = new HTML_AJAX_Queue_Priority_Item(request, new Date().getTime());
        ++this._len;
        if (this.isEmpty()) {
            this._requests[0] = request;
            return;
        }
        for (i = 0; i < this._len - 1; i++) {
            if (request.compareTo(this._requests[i]) < 0) {
                this._requests.splice(i, 1, request, this._requests[i]);
                return;
            }
        }
        this._requests.push(request);
    },
    peek: function () {
        return (this.isEmpty() ? false : this._requests[0]);
    },
    requestComplete: function (request) {
        for (i = 0; i < this._removedLen; i++) {
            if (this._removed[i].item == request) {
                this._removed.splice(i, 1);
                --this._removedLen;
                out('removed from _removed');
                return true;
            }
        }
        return false;
    },
    processRequest: function() {
        if (!this._intervalId) {
            this._runInterval();
            this._start();
        }
        this._idle = 0;
    },
    _runInterval: function() {
        if (Math.random() < this.checkRetryChance) {
            this._doRetries();
        }
        if (this.isEmpty()) {
            if (++this._idle > this.idleMax) {
                this._stop();
            }
            return;
        }
        var client = HTML_AJAX.httpClient();
        if (!client) {
            return;
        }
        var request = this.peek();
        if (!request) {
            this._requests.splice(0, 1);
            return;
        }
        client.request = request.item;
        client.makeRequest();
        this._requests.splice(0, 1);
        --this._len;
        this._removed[this._removedLen++] = new HTML_AJAX_Queue_Priority_Item(request, new Date().getTime());
    },
    _doRetries: function () {
        for (i = 0; i < this._removedLen; i++) {
            if (this._removed[i].time + this._requestTimeout < new Date().getTime()) {
                this.addRequest(request.item);
                this._removed.splice(i, 1);
                --this._removedLen;
                return true;
            }
        }
    },
    _start: function() {
        var self = this;
        this._intervalId = setInterval(function() { self._runInterval() }, this.interval);
    },
    _stop: function() {
        clearInterval(this._intervalId);
        this._intervalId = 0;
    }
};
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// clientPool.js
HTML_AJAX_Client_Pool = function(maxClients, startingClients)
{
    this.maxClients = maxClients;
    this._clients = [];
    this._len = 0;
    while (--startingClients > 0) {
        this.addClient();
    }
}

HTML_AJAX_Client_Pool.prototype = {
    isEmpty: function()
    {
        return this._len == 0;
    },
    addClient: function()
    {
        if (this.maxClients != 0 && this._len > this.maxClients) {
            return false;
        }
        var key = this._len++;
        this._clients[key] = new HTML_AJAX_HttpClient();
        return this._clients[key];
    },
    getClient: function ()
    {
        for (var i = 0; i < this._len; i++) {
            if (!this._clients[i].callInProgress() && this._clients[i].callbackComplete) {
                return this._clients[i];
            }
        }
        var client = this.addClient();
        if (client) {
            return client;
        }
        return false;
    },
    removeClient: function (client)
    {
        for (var i = 0; i < this._len; i++) {
            if (!this._clients[i] == client) {
                this._clients.splice(i, 1);
                return true;
            }
        }
        return false;
    },
    clear: function ()
    {
        this._clients = [];
        this._len = 0;
    }
};

// create a default client pool with unlimited clients
HTML_AJAX.clientPools['default'] = new HTML_AJAX_Client_Pool(0);
// IframeXHR.js
/**
 * XMLHttpRequest Iframe fallback
 *
 * http://lxr.mozilla.org/seamonkey/source/extensions/xmlextras/tests/ - should work with these
 *
 * @category   HTML
 * @package    AJAX
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005 Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */
HTML_AJAX_IframeXHR_instances = new Object();
function HTML_AJAX_IframeXHR()
{
    this._id = 'HAXHR_iframe_' + new Date().getTime();
    HTML_AJAX_IframeXHR_instances[this._id] = this;
}
HTML_AJAX_IframeXHR.prototype = {
// Data not sent with text/xml Content-Type will only be available via the responseText property

    // properties available in safari/mozilla/IE xmlhttprequest object
    onreadystatechange: null, // Event handler for an event that fires at every state change
    readyState: 0, // Object status integer: 0 = uninitialized 1 = loading 2 = loaded 3 = interactive 4 = complete
    responseText: '', // String version of data returned from server process
    responseXML: null, // DOM-compatible document object of data returned from server process
    status: 0, // Numeric code returned by server, such as 404 for "Not Found" or 200 for "OK"
    statusText: '', // String message accompanying the status code
    iframe: true, // flag for iframe

    //these are private properties used internally to keep track of stuff
    _id: null, // iframe id, unique to object(hopefully)
    _url: null, // url sent by open
    _method: null, // get or post
    _async: null, // sync or async sent by open
    _headers: new Object(), //request headers to send, actually sent as form vars
    _response: new Object(), //response headers received
    _phpclass: null, //class to send
    _phpmethod: null, //method to send
    _history: null, // opera has to have history munging

    // Stops the current request
    abort: function()
    {
        var iframe = document.getElementById(this._id);
        if (iframe) {
            document.body.removeChild(iframe);
        }
        if (this._timeout) {
            window.clearTimeout(this._timeout);
        }
        this.readyState = 1;
        if (typeof(this.onreadystatechange) == "function") {
            this.onreadystatechange();
        }
    },

    // This will send all headers in this._response and will include lastModified and contentType if not already set
    getAllResponseHeaders: function()
    {
        var string = '';
        for (i in this._response) {
            string += i + ' : ' + this._response[i] + "\n";
        }
        return string;
    },

    // This will use lastModified and contentType if they're not set
    getResponseHeader: function(header)
    {
        return (this._response[header] ? this._response[header] : null);
    },

    // Assigns a label/value pair to the header to be sent with a request
    setRequestHeader: function(label, value) {
        this._headers[label] = value;
        return; },

    // Assigns destination URL, method, and other optional attributes of a pending request
    open: function(method, url, async, username, password)
    {
        if (!document.body) {
            throw('CANNOT_OPEN_SEND_IN_DOCUMENT_HEAD');
        }
        //exceptions for not enough arguments
        if (!method || !url) {
            throw('NOT_ENOUGH_ARGUMENTS:METHOD_URL_REQUIRED');
        }
        //get and post are only methods accepted
        this._method = (method.toUpperCase() == 'POST' ? 'POST' : 'GET');
        this._decodeUrl(url);
        this._async = async;
        if(!this._async && document.readyState && !window.opera) {
            throw('IE_DOES_NOT_SUPPORT_SYNC_WITH_IFRAMEXHR');
        }
        //set status to loading and call onreadystatechange
        this.readyState = 1;
        if(typeof(this.onreadystatechange) == "function") {
            this.onreadystatechange();
        }
    },

    // Transmits the request, optionally with postable string or DOM object data
    send: function(content)
    {
        //attempt opera history munging
        if (window.opera) {
            this._history = window.history.length;
        }
        //create a "form" for the contents of the iframe
        var form = '<html><body><form method="'
            + (this._url.indexOf('px=') < 0 ? this._method : 'post')
            + '" action="' + this._url + '">';
        //tell iframe unwrapper this IS an iframe
        form += '<input name="Iframe_XHR" value="1" />';
        //class and method
        if (this._phpclass != null) {
            form += '<input name="Iframe_XHR_class" value="' + this._phpclass + '" />';
        }
        if (this._phpmethod != null) {
            form += '<input name="Iframe_XHR_method" value="' + this._phpmethod + '" />';
        }
        // fake headers
        for (label in this._headers) {
            form += '<textarea name="Iframe_XHR_headers[]">' + label +':'+ this._headers[label] + '</textarea>';
        }
        // add id
        form += '<textarea name="Iframe_XHR_id">' + this._id + '</textarea>';
        if (content != null && content.length > 0) {
            form += '<textarea name="Iframe_XHR_data">' + content + '</textarea>';
        }
        form += '<input name="Iframe_XHR_HTTP_method" value="' + this._method + '" />';
        form += '<s'+'cript>document.forms[0].submit();</s'+'cript></form></body></html>';
        form = "javascript:document.write('" + form.replace(/\'/g,"\\'") + "');void(0);";
        this.readyState = 2;
        if (typeof(this.onreadystatechange) == "function") {
            this.onreadystatechange();
        }
        // try to create an iframe with createElement and append node
        try {
            var iframe = document.createElement('iframe');
            iframe.id = this._id;
            // display: none will fail on some browsers
            iframe.style.visibility = 'hidden';
            // for old browsers with crappy css
            iframe.style.border = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            
            if (document.all) {
                // MSIE, opera
                iframe.src = form;
                document.body.appendChild(iframe);
            } else {
                document.body.appendChild(iframe);
                iframe.src = form;
            }
        } catch(exception) {
            // dom failed, write the sucker manually
            var html = '<iframe src="' + form +'" id="' + this._id + '" style="visibility:hidden;border:0;height:0;width:0;"></iframe>';
            document.body.innerHTML += html;
        }
        if (this._async == true) {
            //avoid race state if onload is called first
            if (this.readyState < 3) {
                this.readyState = 3;
                if(typeof(this.onreadystatechange) == "function") {
                    this.onreadystatechange();
                }
            }
        } else {
            //we force a while loop for sync, it's ugly but hopefully it works
            while (this.readyState != 4) {
                //just check to see if we can up readyState
                if (this.readyState < 3) {
                    this.readyState = 3;
                    if(typeof(this.onreadystatechange) == "function") {
                        this.onreadystatechange();
                    }
                }
            }
        }
    },

    // attached as an onload function to the iframe to trigger when we're done
    isLoaded: function(headers, data)
    {
        this.readyState = 4;
        //set responseText, Status, StatusText
        this.status = 200;
        this.statusText = 'OK';
        this.responseText = data;
        this._response = headers;
        if (!this._response['Last-Modified']) {
            string += 'Last-Modified : ' + document.getElementById(this._id).lastModified + "\n";
        }
        if (!this._response['Content-Type']) {
            string += 'Content-Type : ' + document.getElementById(this._id).contentType + "\n";
        }
        // if this is xml populate responseXML accordingly
        if (this._response['Content-Type'] == 'application/xml')
        {
            return new DOMParser().parseFromString(this.responseText, 'application/xml');
        }
        //attempt opera history munging in opera 8+ - this is a REGRESSION IN OPERA
        if (window.opera && window.opera.version) {
            //go back current history - old history
            window.history.go(this._history - window.history.length);
        }
        if (typeof(this.onreadystatechange) == "function") {
            this.onreadystatechange();
        }
        document.body.removeChild(document.getElementById(this._id));
    },

    // strip off the c and m from the url send...yuck
    _decodeUrl: function(querystring)
    {
        //opera 7 is too stupid to do a relative url...go figure
        var url = unescape(location.href);
        url = url.substring(0, url.lastIndexOf("/") + 1);
        var item = querystring.split('?');
        //rip off any path info and append to path above <-  relative paths (../) WILL screw this
        this._url = url + item[0].substring(item[0].lastIndexOf("/") + 1,item[0].length);
        if(item[1]) {
            item = item[1].split('&');
            for (i in item) {
                var v = item[i].split('=');
                if (v[0] == 'c') {
                    this._phpclass = v[1];
                } else if (v[0] == 'm') {
                    this._phpmethod = v[1];
                }
            }
        }
        if (!this._phpclass || !this._phpmethod) {
            var cloc = window.location.href;
            this._url = cloc + (cloc.indexOf('?') >= 0 ? '&' : '?') + 'px=' + escape(HTML_AJAX_Util.absoluteURL(querystring));
        }
    }
}
// serializer/UrlSerializer.js
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
// serializer/phpSerializer.js
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

// Dispatcher.js
/**
 * Class that is used by generated stubs to make actual AJAX calls
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */
function HTML_AJAX_Dispatcher(className,mode,callback,serverUrl,serializerType) 
{
	this.className = className;
	this.mode = mode;
	this.callback = callback;
    this.serializerType = serializerType;

	if (serverUrl) {
		this.serverUrl = serverUrl
	}
	else {
		this.serverUrl = window.location;
	}
}

HTML_AJAX_Dispatcher.prototype = {
    /**
     * Queue to use when making a request
     */
    queue: 'default',

    /**
     * Timeout for async calls
     */
	timeout: 20000,
 
    /**
     * Default request priority
     */
    priority: 0,

    /**
     * Request options 
     */
    options: {},
    
    /**
     * Make an ajax call
     *
     * @param   string callName
     * @param   Array   args    arguments to the report method
     */
	doCall: function(callName,args) 
    {
        var request = new HTML_AJAX_Request();
		request.requestUrl = this.serverUrl;
        request.className = this.className;
        request.methodName = callName;
		request.timeout = this.timeout;
        request.contentType = this.contentType;
        request.serializer = eval('new HTML_AJAX_Serialize_'+this.serializerType);
        request.queue = this.queue;
        request.priority = this.priority;

        for(var i in this.options) {
            request[i] = this.options[i];
        }
        
		for(var i=0; i < args.length; i++) {
		    request.addArg(i,args[i]);
		};

		if ( this.mode == "async" ) {
		    request.isAsync = true;
            if (this.callback[callName]) {
                var self = this;
                request.callback = function(result) { self.callback[callName](result); }
            }

		} else {
		    request.isAsync = false;
		}

        return HTML_AJAX.makeRequest(request);
	},

    Sync: function() 
    {
        this.mode = 'sync';
    },

    Async: function(callback)
    {
        this.mode = 'async';
        if (callback) {
            this.callback = callback;
        }
    }
    
};
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// HttpClient.js
/**
 * XMLHttpRequest Wrapper
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */
function HTML_AJAX_HttpClient() { }
HTML_AJAX_HttpClient.prototype = {
    // request object
    request: null,

    // timeout id
    _timeoutId: null,

	callbackComplete: true,

    // has this request been aborted
    aborted: false,
    
    // method to initialize an xmlhttpclient
    init:function() 
    {
        try {
            // Mozilla / Safari
            //this.xmlhttp = new HTML_AJAX_IframeXHR(); //uncomment these two lines to test iframe
            //return;
            this.xmlhttp = new XMLHttpRequest();
        } catch (e) {
            // IE
            var XMLHTTP_IDS = new Array(
            'MSXML2.XMLHTTP.5.0',
            'MSXML2.XMLHTTP.4.0',
            'MSXML2.XMLHTTP.3.0',
            'MSXML2.XMLHTTP',
            'Microsoft.XMLHTTP' );
            var success = false;
            for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
                try {
                    this.xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
                    success = true;
                } catch (e) {}
            }
            if (!success) {
                try{
                    this.xmlhttp = new HTML_AJAX_IframeXHR();
                    this.request.iframe = true;
                } catch(e) {
                    throw new Error('Unable to create XMLHttpRequest.');
                }
            }
        }
    },

    // check if there is a call in progress
    callInProgress: function() 
    {
        switch ( this.xmlhttp.readyState ) {
            case 1:
            case 2:
            case 3:
                return true;
            break;
            default:
                return false;
            break;
        }
    },

    // make the request defined in the request object
    makeRequest: function() 
    {
        if (!this.xmlhttp) {
            this.init();
        }

        try {
            if (this.request.Open) {
                this.request.Open();
            }
            else if (HTML_AJAX.Open) {
                HTML_AJAX.Open(this.request);
            }

            if (this.request.multipart) {
                if (document.all) {
                    this.iframe = true;
                } else {
                    this.xmlhttp.multipart = true;
                }
            }
    
            // set onreadystatechange here since it will be reset after a completed call in Mozilla
            var self = this;
            this.xmlhttp.open(this.request.requestType,this.request.completeUrl(),this.request.isAsync);
            if (this.request.customHeaders) {
                for (i in this.request.customHeaders) {
                    this.xmlhttp.setRequestHeader(i, this.request.customHeaders[i]);
                }
            }
            if (this.request.customHeaders && !this.request.customHeaders['Content-Type']) {
				var content = this.request.getContentType();
                //opera is stupid for anything but plain text or xml!!
                if(window.opera && content != 'application/xml')
                {
                    this.xmlhttp.setRequestHeader('Content-Type','text/plain; charset=utf-8');
                    this.xmlhttp.setRequestHeader('x-Content-Type', content + '; charset=utf-8');
                }
                else
                {
                    this.xmlhttp.setRequestHeader('Content-Type', content +  '; charset=utf-8');
                }
            }

            if (this.request.isAsync) {
                if (this.request.callback) {
                    this.callbackComplete = false;
                }
                this.xmlhttp.onreadystatechange = function() { self._readyStateChangeCallback(); }
            } else {
                this.xmlhttp.onreadystatechange = function() {}
            }
            var payload = this.request.getSerializedPayload();
            if (payload) {
                this.xmlhttp.setRequestHeader('Content-Length', payload.length);
            }
            this.xmlhttp.send(payload);

            if (!this.request.isAsync) {
                if ( this.xmlhttp.status == 200 ) {
                    HTML_AJAX.requestComplete(this.request);
                    if (this.request.Load) {
                        this.request.Load();
                    } else if (HTML_AJAX.Load) {
                        HTML_AJAX.Load(this.request);
                    }
                        
                    return this._decodeResponse();
                } else {
                    var e = new Error('['+this.xmlhttp.status +'] '+this.xmlhttp.statusText);
                    e.headers = this.xmlhttp.getAllResponseHeaders();
                    this._handleError(e);
                }
            }
            else {
                // setup timeout
                var self = this;
                this._timeoutId = window.setTimeout(function() { self.abort(true); },this.request.timeout);
            }
        } catch (e) {
            this._handleError(e);
        }
    },
    
    // abort an inprogress request
    abort: function (automatic) 
    {
        if (this.callInProgress()) {
            this.aborted = true;
            this.xmlhttp.abort();

            if (automatic) {
                HTML_AJAX.requestComplete(this.request);
                this._handleError(new Error('Request Timed Out: time out was '+this.request.timeout+'ms'));
            }
        }
    },

    // internal method used to handle ready state changes
    _readyStateChangeCallback:function() 
    {
        try {
            switch(this.xmlhttp.readyState) {
                // XMLHTTPRequest.open() has just been called
                case 1:
                    break;
                // XMLHTTPRequest.send() has just been called
                case 2:
                    if (this.request.Send) {
                        this.request.Send();
                    } else if (HTML_AJAX.Send) {
                        HTML_AJAX.Send(this.request);
                    }
                    break;
                // Fetching response from server in progress
                case 3:
                    if (this.request.Progress) {
                        this.request.Progress();
                    } else if (HTML_AJAX.Progress ) {
                        HTML_AJAX.Progress(this.request);
                    }
                break;
                // Download complete
                case 4:
                    window.clearTimeout(this._timeoutId);
                    if (this.aborted) {
                        if (this.request.Load) {
                            this.request.Load();
                        } else if (HTML_AJAX.Load) {
                            HTML_AJAX.Load(this.request);
                        }
                    }
                    else if (this.xmlhttp.status == 200) {
                        if (this.request.Load) {
                            this.request.Load();
                        } else if (HTML_AJAX.Load ) {
                            HTML_AJAX.Load(this.request);
                        }

                        var response = this._decodeResponse();

                        if (this.request.callback) {
                            this.request.callback(response);
                            this.callbackComplete = true;
                        }
                    }
                    else {
                        var e = new Error('HTTP Error Making Request: ['+this.xmlhttp.status+'] '+this.xmlhttp.statusText);
                        this._handleError(e);
                    }
                    HTML_AJAX.requestComplete(this.request);
                break;
            }
        } catch (e) {
                this._handleError(e);
        }
    },

    // decode response as needed
    _decodeResponse: function() {
        //try for x-Content-Type first
        var content = null;
        try {
            content = this.xmlhttp.getResponseHeader('X-Content-Type');
        } catch(e) {}
        if(!content || content == null)
        {
            content = this.xmlhttp.getResponseHeader('Content-Type');
        }
        //strip anything after ;
        if(content.indexOf(';') != -1)
        {
            content = content.substring(0, content.indexOf(';'));
        }
		// hook for xml, it doesn't need to be unserialized
		if(content == 'application/xml')
		{
			return this.xmlhttp.responseXML;
		}
        var unserializer = HTML_AJAX.serializerForEncoding(content);
        //alert(this.xmlhttp.getAllResponseHeaders()); // some sort of debug hook is needed here
        return unserializer.unserialize(this.xmlhttp.responseText);
    },

    // handle sending an error where it needs to go
    _handleError: function(e) 
    {
        HTML_AJAX.requestComplete(this.request,e);
        if (this.request.onError) {
            this.request.onError(e);
        } else if (HTML_AJAX.onError) {
            HTML_AJAX.onError(e,this.request);
        }
        else {
            throw e;
        }
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// Request.js
/**
 * Class that contains everything needed to make a request
 * This includes:
 *    The url were calling
 *    If were calling a remote method, the class and method name
 *    The payload, unserialized
 *    The timeout for async calls
 *    The callback method
 *    Optional event handlers: onError, Load, Send
 *    A serializer instance
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for author/license details
 */
function HTML_AJAX_Request(serializer) {
    this.serializer = serializer;
}
HTML_AJAX_Request.prototype = {

    // Instance of a serializer
    serializer: null,
    
    // Is this an async request
    isAsync: false,

    // HTTP verb
    requestType: 'POST',
    
    // The actual URL the request is sent to
    requestUrl: '',
    
    // Remote Class
    className: null,

    // Remote Method
    methodName: null,

    // Timeout in milliseconds for requests
    timeout: 20000,

    // unserialized data, for rpc calls use add args, to send raw data just set this directly
    args: null,

    // async callback method
    callback: null,

    // Queue to push this request too
    queue: 'default',
    
    // default priority
    priority: 0,
    
    // a hash of headers to add to add to this request
    customHeaders: {},

    // true if this request will be sent using iframes
    iframe: false,
    
    // is this a grab request? if so we need to proxy for iframes
    grab: false,
    
    // true if this request should expect a multipart response
    multipart: false,

    // remote callback
    phpCallback: false,
    
    /**
     * Add an argument for the remote method
     * @param string argument name
     * @param mixed value
     * @return void
     * @throws Error code 1004
     */
    addArg: function(name, value) 
    {
        if ( !this.args ) {
            this.args = [];
        }
        if (!/[^a-zA-Z_0-9]/.test(name) ) {
            this.args[name] = value;
        } else {
            throw new Error('Invalid parameter name ('+name+')');
        }
    },

    /**
     * Get the payload in a serialized manner
     */
    getSerializedPayload: function() {
        return this.serializer.serialize(this.args);
    },

    /**
     * Get the content type
     */
    getContentType: function() {
        return this.serializer.contentType;
    },

    /**
     * Get the complete url, adding in any needed get params for rpc
     */
    completeUrl: function() {
        if (this.className || this.methodName) {
            this.addGet('c', this.className);
            this.addGet('m', this.methodName);
        }
        if (this.phpCallback) {
            if (HTML_AJAX_Util.getType(this.phpCallback) == 'array') {
                this.phpCallback = this.phpCallback.join('.');
            }
            this.addGet('cb', this.phpCallback);
        }
        if (this.multipart) {
            this.addGet('multipart', '1');
        }
        return this.requestUrl;
    },
    
    /**
     * Compare to another request by priority
     */
    compareTo: function(other) {
        if (this.priority == other.priority) {
            return 0;
        }
        return (this.priority > other.priority ? 1 : -1);
    },

    /**
     * Add a GET argument
     */
    addGet: function(name, value) {
        var url = new String(this.requestUrl);
        url += (url.indexOf('?') < 0 ? '?' : '&') + escape(name) + '=' + escape(value);
        this.requestUrl = url;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// serializer/JSON.js
/*
Copyright (c) 2005 JSON.org

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The Software shall be used for Good, not Evil.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

Array.prototype.______array = '______array';

var HTML_AJAX_JSON = {
    org: 'http://www.JSON.org',
    copyright: '(c)2005 JSON.org',
    license: 'http://www.crockford.com/JSON/license.html',

    stringify: function (arg) {
        var c, i, l, s = '', v;

        switch (typeof arg) {
        case 'object':
            if (arg) {
                if (arg.______array == '______array') {
                    for (i = 0; i < arg.length; ++i) {
                        v = this.stringify(arg[i]);
                        if (s) {
                            s += ',';
                        }
                        s += v;
                    }
                    return '[' + s + ']';
                } else if (typeof arg.toString != 'undefined') {
                    for (i in arg) {
                        v = arg[i];
                        if (typeof v != 'undefined' && typeof v != 'function') {
                            v = this.stringify(v);
                            if (s) {
                                s += ',';
                            }
                            s += this.stringify(i) + ':' + v;
                        }
                    }
                    return '{' + s + '}';
                }
            }
            return 'null';
        case 'number':
            return isFinite(arg) ? String(arg) : 'null';
        case 'string':
            l = arg.length;
            s = '"';
            for (i = 0; i < l; i += 1) {
                c = arg.charAt(i);
                if (c >= ' ') {
                    if (c == '\\' || c == '"') {
                        s += '\\';
                    }
                    s += c;
                } else {
                    switch (c) {
                        case '\b':
                            s += '\\b';
                            break;
                        case '\f':
                            s += '\\f';
                            break;
                        case '\n':
                            s += '\\n';
                            break;
                        case '\r':
                            s += '\\r';
                            break;
                        case '\t':
                            s += '\\t';
                            break;
                        default:
                            c = c.charCodeAt();
                            s += '\\u00' + Math.floor(c / 16).toString(16) +
                                (c % 16).toString(16);
                    }
                }
            }
            return s + '"';
        case 'boolean':
            return String(arg);
        default:
            return 'null';
        }
    },
    parse: function (text) {
        var at = 0;
        var ch = ' ';

        function error(m) {
            throw {
                name: 'JSONError',
                message: m,
                at: at - 1,
                text: text
            };
        }

        function next() {
            ch = text.charAt(at);
            at += 1;
            return ch;
        }

        function white() {
            while (ch) {
                if (ch <= ' ') {
                    next();
                } else if (ch == '/') {
                    switch (next()) {
                        case '/':
                            while (next() && ch != '\n' && ch != '\r') {}
                            break;
                        case '*':
                            next();
                            for (;;) {
                                if (ch) {
                                    if (ch == '*') {
                                        if (next() == '/') {
                                            next();
                                            break;
                                        }
                                    } else {
                                        next();
                                    }
                                } else {
                                    error("Unterminated comment");
                                }
                            }
                            break;
                        default:
                            error("Syntax error");
                    }
                } else {
                    break;
                }
            }
        }

        function string() {
            var i, s = '', t, u;

            if (ch == '"') {
outer:          while (next()) {
                    if (ch == '"') {
                        next();
                        return s;
                    } else if (ch == '\\') {
                        switch (next()) {
                        case 'b':
                            s += '\b';
                            break;
                        case 'f':
                            s += '\f';
                            break;
                        case 'n':
                            s += '\n';
                            break;
                        case 'r':
                            s += '\r';
                            break;
                        case 't':
                            s += '\t';
                            break;
                        case 'u':
                            u = 0;
                            for (i = 0; i < 4; i += 1) {
                                t = parseInt(next(), 16);
                                if (!isFinite(t)) {
                                    break outer;
                                }
                                u = u * 16 + t;
                            }
                            s += String.fromCharCode(u);
                            break;
                        default:
                            s += ch;
                        }
                    } else {
                        s += ch;
                    }
                }
            }
            error("Bad string");
        }

        function array() {
            var a = [];

            if (ch == '[') {
                next();
                white();
                if (ch == ']') {
                    next();
                    return a;
                }
                while (ch) {
                    a.push(value());
                    white();
                    if (ch == ']') {
                        next();
                        return a;
                    } else if (ch != ',') {
                        break;
                    }
                    next();
                    white();
                }
            }
            error("Bad array");
        }

        function object() {
            var k, o = {};

            if (ch == '{') {
                next();
                white();
                if (ch == '}') {
                    next();
                    return o;
                }
                while (ch) {
                    k = string();
                    white();
                    if (ch != ':') {
                        break;
                    }
                    next();
                    o[k] = value();
                    white();
                    if (ch == '}') {
                        next();
                        return o;
                    } else if (ch != ',') {
                        break;
                    }
                    next();
                    white();
                }
            }
            error("Bad object");
        }

        function number() {
            var n = '', v;
            if (ch == '-') {
                n = '-';
                next();
            }
            while (ch >= '0' && ch <= '9') {
                n += ch;
                next();
            }
            if (ch == '.') {
                n += '.';
                while (next() && ch >= '0' && ch <= '9') {
                    n += ch;
                }
            }
            if (ch == 'e' || ch == 'E') {
                n += 'e';
                next();
                if (ch == '-' || ch == '+') {
                    n += ch;
                    next();
                }
                while (ch >= '0' && ch <= '9') {
                    n += ch;
                    next();
                }
            }
            v = +n;
            if (!isFinite(v)) {
                ////error("Bad number");
            } else {
                return v;
            }
        }

        function word() {
            switch (ch) {
                case 't':
                    if (next() == 'r' && next() == 'u' && next() == 'e') {
                        next();
                        return true;
                    }
                    break;
                case 'f':
                    if (next() == 'a' && next() == 'l' && next() == 's' &&
                            next() == 'e') {
                        next();
                        return false;
                    }
                    break;
                case 'n':
                    if (next() == 'u' && next() == 'l' && next() == 'l') {
                        next();
                        return null;
                    }
                    break;
            }
            error("Syntax error");
        }

        function value() {
            white();
            switch (ch) {
                case '{':
                    return object();
                case '[':
                    return array();
                case '"':
                    return string();
                case '-':
                    return number();
                default:
                    return ch >= '0' && ch <= '9' ? number() : word();
            }
        }

        return value();
    }
};
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// serializer/haSerializer.js
/**
 * HTML_AJAX_Serialize_HA  - custom serialization
 *
 * This class is used with the JSON serializer and the HTML_AJAX_Action php class
 * to allow users to easily write data handling and dom manipulation related to
 * ajax actions directly from their php code
 *
 * See Main.js for Author/license details
 */
function HTML_AJAX_Serialize_HA() { }
HTML_AJAX_Serialize_HA.prototype =
{
    /**
     *  Takes data from JSON - which should be parseable into a nice array
     *  reads the action to take and pipes it to the right method
     *
     *  @param    string payload incoming data from php
     *  @return   true on success, false on failure
     */
    unserialize: function(payload)
    {
        var actions = eval(payload);
        for(var i = 0; i < actions.length; i++)
        {
            var action = actions[i];
            switch(action.action)
            {
                case 'prepend':
                    this._prependAttr(action.id, action.attributes);
                    break;
                case 'append':
                    this._appendAttr(action.id, action.attributes);
                    break;
                case 'assign':
                    this._assignAttr(action.id, action.attributes);
                    break;
                case 'clear':
                    this._clearAttr(action.id, action.attributes);
                    break;
                case 'create':
                    this._createNode(action.id, action.tag, action.attributes, action.type);
                    break;
                case 'replace':
                    this._replaceNode(action.id, action.tag, action.attributes);
                    break;
                case 'remove':
                    this._removeNode(action.id);
                    break;
                case 'script':
                    this._insertScript(action.data);
                    break;
                case 'alert':
                    this._insertAlert(action.data);
                    break;
            }
        }
    },
	_prependAttr: function(id, attributes)
	{
		var node = document.getElementById(id);
        for (var i in attributes)
        {
            //innerHTML hack bailout
            if(i == 'innerHTML')
            {
                HTML_AJAX_Util.setInnerHTML(node, attributes[i], 'prepend');
            }
            //value hack
            else if(i == 'value')
            {
                node.value = attributes[i];
            }
            //I'd use hasAttribute but IE is stupid stupid stupid
            else
            {
                var value = node.getAttribute(i);
                if(value)
                {
                    node.setAttribute(i, attributes[i] + value);
                }
                else
                {
                    node.setAttribute(i, attributes[i]);
                }
            }
        }
	},
	_appendAttr: function(id, attributes)
	{
		var node = document.getElementById(id);
        for (var i in attributes)
        {
            //innerHTML hack bailout
            if(i == 'innerHTML')
            {
                HTML_AJAX_Util.setInnerHTML(node, attributes[i], 'append');
            }
            //value hack
            else if(i == 'value')
            {
                node.value = attributes[i];
            }
            //I'd use hasAttribute but IE is stupid stupid stupid
            else
            {
                var value = node.getAttribute(i);
                if(value)
                {
                    node.setAttribute(i, value + attributes[i]);
                }
                else
                {
                    node.setAttribute(i, attributes[i]);
                }
            }
        }
	},
	_assignAttr: function(id, attributes)
	{
		var node = document.getElementById(id);
        for (var i in attributes)
        {
            //innerHTML hack bailout
            if(i == 'innerHTML')
            {
                HTML_AJAX_Util.setInnerHTML(node,attributes[i]);
            }
            //value hack
            else if(i == 'value')
            {
                node.value = attributes[i];
            }
            //IE doesn't support setAttribute on style so we need to break it out and set each property individually
            else if(i == 'style')
            {
		var styles = [];
		if (attributes[i].indexOf(';')) {
			styles = attributes[i].split(';');
		}
		else {
			styles.push(attributes[i]);
		}
		for(var i = 0; i < styles.length; i++) {
			var r = styles[i].match(/^\s*(.+)\s*:\s*(.+)\s*$/);
			if(r) {
				node.style[this._camelize(r[1])] = r[2];
			}
		}
            }
            //no special rules know for this node so lets try our best
            else
            {
		try {
			node[i] = attributes[i];
		} catch(e) {
		}
		node.setAttribute(i, attributes[i]);
            }
        }
	},
    // should we move this to HTML_AJAX_Util???, just does the - case which we need for style
    _camelize: function(instr)
    {
        var p = instr.split('-');
        var out = p[0];
        for(var i = 1; i < p.length; i++) {
            out += p[i].charAt(0).toUpperCase()+p[i].substring(1);
        }
        return out;
    },
	_clearAttr: function(id, attributes)
	{
		var node = document.getElementById(id);
        for(var i = 0; i < attributes.length; i++)
        {
            //innerHTML hack bailout
            if(attributes[i] == 'innerHTML')
            {
                node.innerHTML = '';
            }
            //value hack
            else if(attributes[i] == 'value')
            {
                node.value = '';
            }
            //I'd use hasAttribute but IE is stupid stupid stupid
            else
            {
                node.removeAttribute(attributes[i]);
            }
        }
	},
    _createNode: function(id, tag, attributes, type)
    {
        var newnode = document.createElement(tag);
        for (var i in attributes)
        {
            //innerHTML hack bailout
            if(i == 'innerHTML')
            {
                newnode.innerHTML = attributes[i];
            }
            //value hack
            else if(i == 'value')
            {
                newnode.value = attributes[i];
            }
            //I'd use hasAttribute but IE is stupid stupid stupid
            else
            {
                newnode.setAttribute(i, attributes[i]);
            }
        }
        switch(type)
        {
            case 'append':
                document.getElementById(id).appendChild(newnode);
                break
            case 'prepend':
                var parent = document.getElementById(id);
                var sibling = parent.firstChild;
                parent.insertBefore(newnode, sibling);
                break;
            case 'insertBefore':
                var sibling = document.getElementById(id);
                var parent = sibling.parentNode;
                parent.insertBefore(newnode, sibling);
                break;
            //this one is tricky, if it's the last one we use append child...ewww
            case 'insertAfter':
                var sibling = document.getElementById(id);
                var parent = sibling.parentNode;
                var next = sibling.nextSibling;
                if(next == null)
                {
                    parent.appendChild(newnode);
                }
                else
                {
                    parent.insertBefore(newnode, next);
                }
                break;
        }
	},
    _replaceNode: function(id, tag, attributes)
    {
		var node = document.getElementById(id);
		var parent = node.parentNode;
        var newnode = document.createElement(tag);
		for (var i in attributes)
        {
            //innerHTML hack bailout
            if(i == 'innerHTML')
            {
                newnode.innerHTML = attributes[i];
            }
            //value hack
            else if(i == 'value')
            {
                newnode.value = attributes[i];
            }
        }
        parent.replaceChild(newnode, node);
	},
	_removeNode: function(id)
	{
		var node = document.getElementById(id);
        if(node)
        {
            var parent = node.parentNode;
            parent.removeChild(node);
        }
	},
    _insertScript: function(data)
    {
        eval(data);
    },
    _insertAlert: function(data)
    {
        alert(data);
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// Loading.js
/**
 * Default loading implementation
 *
 * @category   HTML
 * @package    Ajax
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @copyright  2005 Joshua Eichorn
 * see Main.js for license Author details
 */
HTML_AJAX.Open = function(request) {
    var loading = document.getElementById('HTML_AJAX_LOADING');
    if (!loading) {
        loading = document.createElement('div');
        loading.id = 'HTML_AJAX_LOADING';
        loading.innerHTML = 'Loading...';
        
        loading.style.color           = '#fff';
        loading.style.position        = 'absolute';
        loading.style.top             = 0;
        loading.style.right           = 0;
        loading.style.backgroundColor = '#f00';
        loading.style.border          = '1px solid #f99';
        loading.style.width           = '80px';
        loading.style.padding         = '4px';
        loading.style.fontFamily      = 'Arial, Helvetica, sans';
        loading.count = 0;
    
        document.body.insertBefore(loading,document.body.firstChild);
    }
    else {
        if (loading.count == undefined) {
            loading.count = 0;
        }
    }
    loading.count++;
    if (request.isAsync) {
        request.loadingId = window.setTimeout(function() { loading.style.display = 'block'; },500);
    }
    else {
        loading.style.display = 'block';
    }
}
HTML_AJAX.Load = function(request) {
    if (request.loadingId) {
        window.clearTimeout(request.loadingId);
    }
    var loading = document.getElementById('HTML_AJAX_LOADING');
    loading.count--;

    if (loading.count == 0) {
        loading.style.display = 'none';
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// util.js
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
// behavior/behavior.js
/**

ModifiedBehavior v1.0 by Ron Lancaster based on Ben Nolan's Behaviour, June 2005 implementation.
Modified to use Dean Edward's CSS Query.

Description
----------

Uses css selectors  to apply javascript Behaviors to enable unobtrusive javascript in html documents.

Dependencies
------------

Requires [Dean Edwards CSSQuery](http://dean.edwards.name/my/cssQuery/ "CSSQuery").

Usage
------

		Behavior.register(
			"b.someclass",
			function(element) {
				element.onclick = function(){
					alert(this.innerHTML);
				}
			}
		);

		Behavior.register(
			"#someid u",
			function(element) {
				element.onmouseover = function(){
					this.innerHTML = "BLAH!";
				}
			},
			getElementByID("parent")
		);

Call `Behavior.apply()` to re-apply the rules (if you update the dom, etc).

License
------

Reproduced under BSD licensed. Same license as Ben Nolan's implementation.

More information for Ben Nolan's implementation: <http://ripcord.co.nz/behaviour/>

*/

var Behavior = {
	// so to an id to get debug timings
	debug : false,

	// private data member
	list : new Array(),

	// private method
	addLoadEvent : function(func) {
		var oldonload = window.onload;

		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
				oldonload();
				func();
			}
		}
	},

	// void apply() : Applies the registered ruleset.
	apply : function() {
		if (this.debug) {
			document.getElementById(this.debug).innerHTML += 'Apply: '+new Date()+'<br>';
			var total = 0;
		}
		if (Behavior.list.length > 2) {
			cssQuery.caching = true;
		}
		for (i = 0; i < Behavior.list.length; i++) {
			var rule = Behavior.list[i];
			
			if (this.debug) { var ds = new Date() };
			var tags = cssQuery(rule.selector, rule.from);
	
			if (this.debug) {
				var de = new Date();
				var ts = de.valueOf()-ds.valueOf();
				document.getElementById(this.debug).innerHTML += 'Rule: '+rule.selector+' - Took: '+ts+' - Returned: '+tags.length+' tags<br>';
				total += ts;
			}
			if (tags) {
				for (j = 0; j < tags.length; j++) {
					rule.action(tags[j]);
				}
			}
		}
		if (Behavior.list.length > 2) {
			cssQuery.caching = false;
		}

		if (this.debug) {
			document.getElementById(this.debug).innerHTML += 'Total rule apply time: '+total;
		}
	},

	// void register() : register a css selector, and the action (function) to take,
	// from (optional) is a document, element or array of elements which is filtered by selector.
	register : function(selector, action, from) {
		Behavior.list.push(new BehaviorRule(selector, from, action));
	},

	// void start() : initial application of ruleset at document load.
	start : function() {
		Behavior.addLoadEvent(function() {
			Behavior.apply();
		});
	}
}

function BehaviorRule(selector, from, action) {
	this.selector = selector;
	this.from = from;
	this.action = action;
}

Behavior.start();
// behavior/cssQuery-p.js
/*
	cssQuery, version 2.0.2 (2005-08-19)
	Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('7 x=6(){7 1D="2.0.2";7 C=/\\s*,\\s*/;7 x=6(s,A){33{7 m=[];7 u=1z.32.2c&&!A;7 b=(A)?(A.31==22)?A:[A]:[1g];7 1E=18(s).1l(C),i;9(i=0;i<1E.y;i++){s=1y(1E[i]);8(U&&s.Z(0,3).2b("")==" *#"){s=s.Z(2);A=24([],b,s[1])}1A A=b;7 j=0,t,f,a,c="";H(j<s.y){t=s[j++];f=s[j++];c+=t+f;a="";8(s[j]=="("){H(s[j++]!=")")a+=s[j];a=a.Z(0,-1);c+="("+a+")"}A=(u&&V[c])?V[c]:21(A,t,f,a);8(u)V[c]=A}m=m.30(A)}2a x.2d;5 m}2Z(e){x.2d=e;5[]}};x.1Z=6(){5"6 x() {\\n  [1D "+1D+"]\\n}"};7 V={};x.2c=L;x.2Y=6(s){8(s){s=1y(s).2b("");2a V[s]}1A V={}};7 29={};7 19=L;x.15=6(n,s){8(19)1i("s="+1U(s));29[n]=12 s()};x.2X=6(c){5 c?1i(c):o};7 D={};7 h={};7 q={P:/\\[([\\w-]+(\\|[\\w-]+)?)\\s*(\\W?=)?\\s*([^\\]]*)\\]/};7 T=[];D[" "]=6(r,f,t,n){7 e,i,j;9(i=0;i<f.y;i++){7 s=X(f[i],t,n);9(j=0;(e=s[j]);j++){8(M(e)&&14(e,n))r.z(e)}}};D["#"]=6(r,f,i){7 e,j;9(j=0;(e=f[j]);j++)8(e.B==i)r.z(e)};D["."]=6(r,f,c){c=12 1t("(^|\\\\s)"+c+"(\\\\s|$)");7 e,i;9(i=0;(e=f[i]);i++)8(c.l(e.1V))r.z(e)};D[":"]=6(r,f,p,a){7 t=h[p],e,i;8(t)9(i=0;(e=f[i]);i++)8(t(e,a))r.z(e)};h["2W"]=6(e){7 d=Q(e);8(d.1C)9(7 i=0;i<d.1C.y;i++){8(d.1C[i]==e)5 K}};h["2V"]=6(e){};7 M=6(e){5(e&&e.1c==1&&e.1f!="!")?e:23};7 16=6(e){H(e&&(e=e.2U)&&!M(e))28;5 e};7 G=6(e){H(e&&(e=e.2T)&&!M(e))28;5 e};7 1r=6(e){5 M(e.27)||G(e.27)};7 1P=6(e){5 M(e.26)||16(e.26)};7 1o=6(e){7 c=[];e=1r(e);H(e){c.z(e);e=G(e)}5 c};7 U=K;7 1h=6(e){7 d=Q(e);5(2S d.25=="2R")?/\\.1J$/i.l(d.2Q):2P(d.25=="2O 2N")};7 Q=6(e){5 e.2M||e.1g};7 X=6(e,t){5(t=="*"&&e.1B)?e.1B:e.X(t)};7 17=6(e,t,n){8(t=="*")5 M(e);8(!14(e,n))5 L;8(!1h(e))t=t.2L();5 e.1f==t};7 14=6(e,n){5!n||(n=="*")||(e.2K==n)};7 1e=6(e){5 e.1G};6 24(r,f,B){7 m,i,j;9(i=0;i<f.y;i++){8(m=f[i].1B.2J(B)){8(m.B==B)r.z(m);1A 8(m.y!=23){9(j=0;j<m.y;j++){8(m[j].B==B)r.z(m[j])}}}}5 r};8(![].z)22.2I.z=6(){9(7 i=0;i<1z.y;i++){o[o.y]=1z[i]}5 o.y};7 N=/\\|/;6 21(A,t,f,a){8(N.l(f)){f=f.1l(N);a=f[0];f=f[1]}7 r=[];8(D[t]){D[t](r,A,f,a)}5 r};7 S=/^[^\\s>+~]/;7 20=/[\\s#.:>+~()@]|[^\\s#.:>+~()@]+/g;6 1y(s){8(S.l(s))s=" "+s;5 s.P(20)||[]};7 W=/\\s*([\\s>+~(),]|^|$)\\s*/g;7 I=/([\\s>+~,]|[^(]\\+|^)([#.:@])/g;7 18=6(s){5 s.O(W,"$1").O(I,"$1*$2")};7 1u={1Z:6(){5"\'"},P:/^(\'[^\']*\')|("[^"]*")$/,l:6(s){5 o.P.l(s)},1S:6(s){5 o.l(s)?s:o+s+o},1Y:6(s){5 o.l(s)?s.Z(1,-1):s}};7 1s=6(t){5 1u.1Y(t)};7 E=/([\\/()[\\]?{}|*+-])/g;6 R(s){5 s.O(E,"\\\\$1")};x.15("1j-2H",6(){D[">"]=6(r,f,t,n){7 e,i,j;9(i=0;i<f.y;i++){7 s=1o(f[i]);9(j=0;(e=s[j]);j++)8(17(e,t,n))r.z(e)}};D["+"]=6(r,f,t,n){9(7 i=0;i<f.y;i++){7 e=G(f[i]);8(e&&17(e,t,n))r.z(e)}};D["@"]=6(r,f,a){7 t=T[a].l;7 e,i;9(i=0;(e=f[i]);i++)8(t(e))r.z(e)};h["2G-10"]=6(e){5!16(e)};h["1x"]=6(e,c){c=12 1t("^"+c,"i");H(e&&!e.13("1x"))e=e.1n;5 e&&c.l(e.13("1x"))};q.1X=/\\\\:/g;q.1w="@";q.J={};q.O=6(m,a,n,c,v){7 k=o.1w+m;8(!T[k]){a=o.1W(a,c||"",v||"");T[k]=a;T.z(a)}5 T[k].B};q.1Q=6(s){s=s.O(o.1X,"|");7 m;H(m=s.P(o.P)){7 r=o.O(m[0],m[1],m[2],m[3],m[4]);s=s.O(o.P,r)}5 s};q.1W=6(p,t,v){7 a={};a.B=o.1w+T.y;a.2F=p;t=o.J[t];t=t?t(o.13(p),1s(v)):L;a.l=12 2E("e","5 "+t);5 a};q.13=6(n){1d(n.2D()){F"B":5"e.B";F"2C":5"e.1V";F"9":5"e.2B";F"1T":8(U){5"1U((e.2A.P(/1T=\\\\1v?([^\\\\s\\\\1v]*)\\\\1v?/)||[])[1]||\'\')"}}5"e.13(\'"+n.O(N,":")+"\')"};q.J[""]=6(a){5 a};q.J["="]=6(a,v){5 a+"=="+1u.1S(v)};q.J["~="]=6(a,v){5"/(^| )"+R(v)+"( |$)/.l("+a+")"};q.J["|="]=6(a,v){5"/^"+R(v)+"(-|$)/.l("+a+")"};7 1R=18;18=6(s){5 1R(q.1Q(s))}});x.15("1j-2z",6(){D["~"]=6(r,f,t,n){7 e,i;9(i=0;(e=f[i]);i++){H(e=G(e)){8(17(e,t,n))r.z(e)}}};h["2y"]=6(e,t){t=12 1t(R(1s(t)));5 t.l(1e(e))};h["2x"]=6(e){5 e==Q(e).1H};h["2w"]=6(e){7 n,i;9(i=0;(n=e.1F[i]);i++){8(M(n)||n.1c==3)5 L}5 K};h["1N-10"]=6(e){5!G(e)};h["2v-10"]=6(e){e=e.1n;5 1r(e)==1P(e)};h["2u"]=6(e,s){7 n=x(s,Q(e));9(7 i=0;i<n.y;i++){8(n[i]==e)5 L}5 K};h["1O-10"]=6(e,a){5 1p(e,a,16)};h["1O-1N-10"]=6(e,a){5 1p(e,a,G)};h["2t"]=6(e){5 e.B==2s.2r.Z(1)};h["1M"]=6(e){5 e.1M};h["2q"]=6(e){5 e.1q===L};h["1q"]=6(e){5 e.1q};h["1L"]=6(e){5 e.1L};q.J["^="]=6(a,v){5"/^"+R(v)+"/.l("+a+")"};q.J["$="]=6(a,v){5"/"+R(v)+"$/.l("+a+")"};q.J["*="]=6(a,v){5"/"+R(v)+"/.l("+a+")"};6 1p(e,a,t){1d(a){F"n":5 K;F"2p":a="2n";1a;F"2o":a="2n+1"}7 1m=1o(e.1n);6 1k(i){7 i=(t==G)?1m.y-i:i-1;5 1m[i]==e};8(!Y(a))5 1k(a);a=a.1l("n");7 m=1K(a[0]);7 s=1K(a[1]);8((Y(m)||m==1)&&s==0)5 K;8(m==0&&!Y(s))5 1k(s);8(Y(s))s=0;7 c=1;H(e=t(e))c++;8(Y(m)||m==1)5(t==G)?(c<=s):(s>=c);5(c%m)==s}});x.15("1j-2m",6(){U=1i("L;/*@2l@8(@\\2k)U=K@2j@*/");8(!U){X=6(e,t,n){5 n?e.2i("*",t):e.X(t)};14=6(e,n){5!n||(n=="*")||(e.2h==n)};1h=1g.1I?6(e){5/1J/i.l(Q(e).1I)}:6(e){5 Q(e).1H.1f!="2g"};1e=6(e){5 e.2f||e.1G||1b(e)};6 1b(e){7 t="",n,i;9(i=0;(n=e.1F[i]);i++){1d(n.1c){F 11:F 1:t+=1b(n);1a;F 3:t+=n.2e;1a}}5 t}}});19=K;5 x}();',62,190,'|||||return|function|var|if|for||||||||pseudoClasses||||test|||this||AttributeSelector|||||||cssQuery|length|push|fr|id||selectors||case|nextElementSibling|while||tests|true|false|thisElement||replace|match|getDocument|regEscape||attributeSelectors|isMSIE|cache||getElementsByTagName|isNaN|slice|child||new|getAttribute|compareNamespace|addModule|previousElementSibling|compareTagName|parseSelector|loaded|break|_0|nodeType|switch|getTextContent|tagName|document|isXML|eval|css|_1|split|ch|parentNode|childElements|nthChild|disabled|firstElementChild|getText|RegExp|Quote|x22|PREFIX|lang|_2|arguments|else|all|links|version|se|childNodes|innerText|documentElement|contentType|xml|parseInt|indeterminate|checked|last|nth|lastElementChild|parse|_3|add|href|String|className|create|NS_IE|remove|toString|ST|select|Array|null|_4|mimeType|lastChild|firstChild|continue|modules|delete|join|caching|error|nodeValue|textContent|HTML|prefix|getElementsByTagNameNS|end|x5fwin32|cc_on|standard||odd|even|enabled|hash|location|target|not|only|empty|root|contains|level3|outerHTML|htmlFor|class|toLowerCase|Function|name|first|level2|prototype|item|scopeName|toUpperCase|ownerDocument|Document|XML|Boolean|URL|unknown|typeof|nextSibling|previousSibling|visited|link|valueOf|clearCache|catch|concat|constructor|callee|try'.split('|'),0,{}))
