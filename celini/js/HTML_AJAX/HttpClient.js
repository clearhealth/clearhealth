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
