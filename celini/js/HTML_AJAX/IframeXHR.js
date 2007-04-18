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
