// $Id: pseudoxmlhttp.js,v 1.1 2004/11/17 15:55:03 harryf Exp $
// Status: right now - hopelessly flawed - do not use
// Many ideas taken from JSRS: http://www.ashleyit.com/rs/

function PseudoXmlHttpRequest() {

    if (!document.createElement) {
        throw "document.createElement not supported by "+navigator.userAgent;
    };
    
    this.__browser = this.__getBrowser();

    this.__initContainer();


};

PseudoXmlHttpRequest.prototype = {

    onreadystatechange: function() {
        return;
    },
    
    readyState: 1,
    
    responseText: '',
    
    status: '200',
    
    statusText: 'OK',
    
    abort: function() {
        window.clearInterval(this.__responsePollId);
        window.clearTimeout(this.__timeoutId);
    },
    
    addEventListener: function() {
        throw "addEventListener not supported by PseudoXmlHttpRequest";
    },
    
    dispatchEvent: function() {
        throw "dispatchEvent not supported by PseudoXmlHttpRequest";
    },
    
    getAllResponseHeaders: function () {
        return this.__responseHeaders;
    },
    
    getResponseHeader: function(name) {
        return this.__responseHeaders[name.toLowerCase()];
    },
    
    open: function(method, url, callType) {
    
        this.readyState = 1;
        this.responseText = '';
        
        method = method.toUpperCase();
        
        if ( method == 'GET' || method == 'POST' ) {
            this.__method = method;
        } else {
            throw "HTTP method "+method+" not supported";
        };
        
        this.__url = url;

        if ( callType == false ) {
            throw "PseudoXmlHttpRequest does not support synchronous calls";
        };

    },
    
    overrideMimeType: function() {
        throw "overrideMimeType not supported by PseudoXmlHttpRequest";
    },
    
    removeEventListener: function() {
        throw "removeEventListener not supported by PseudoXmlHttpRequest";
    },
    
    send: function(body) {
    
        this.readyState = 1;
        this.responseText = '';
        
        var form = '';
        
        // Need to check request headers here for Content-Type: application/x-www-form-urlencoded
        if ( this.__method == 'POST' ) {
            var args = this.__encodedStringToArgs(body);
            form = this.__createRequestForm(args);
        } else {
            form = this.__createRequestForm();
        };
        
        var requestDoc = (this.__browser == "IE" ) 
            ? this.__requestFrame.document : this.__requestFrame.contentDocument;
        
        requestDoc.open();
        requestDoc.write(form);
        requestDoc.close();
        requestDoc.forms['PseudoXmlHttpForm'].submit();
        
        var responseDoc = (this.__browser == "IE" ) 
            ? this.__responseFrame.document : this.__responseFrame.contentDocument;
        var self = this;
        
        this.__responsePollId = window.setInterval(function() {
            try {
                if ( responseDoc.firstChild.innerText.length > 0 ) {
                    self.__handleResponse(self,responseDoc.firstChild.innerText);
                }
            } catch(e) {}
        }, 200);
        
        this.__timeoutId = window.setTimeout(function() {
            window.clearInterval(self.__responsePollId)
        }, 30000); // Timeout should be configurable

    },
    
    setRequestHeader: function(name, value) {
        this.__requestHeaders[name.toLowerCase()] = value;
    },
    
    //----------------------------------------------------------------------------------------
    
    __url: null,
    
    __method: 'GET',
    
    __requestHeaders: new Object(),
    
    __responseHeaders: new Object(),
    
    __browser: null,
    
    __requestFrame: null,
    
    __responseFrame: null,
    
    __responsePollId: null,
    
    __timeoutId: null,
    
    __getBrowser: function() {

        if (document.layers) return "NS";
        if (document.all) {
            // But is it really IE?
            // convert all characters to lowercase to simplify testing
            var agt=navigator.userAgent.toLowerCase();
            var is_opera = (agt.indexOf("opera") != -1);
            var is_konq = (agt.indexOf("konqueror") != -1);
            if(is_opera) {
                return "OPR";
            } else {
                if(is_konq) {
                    return "KONQ";
                } else {
                    // Really is IE
                    return "IE";
                }
            }
        }
        if (document.getElementById) return "MOZ";
        return "OTHER";
    },
    
    __initContainer: function() {
        switch( this.__browser ) {

            // -------------------------------------------------------------------------------
            case 'NS':
                this.__requestFrame = new Layer(100);
                this.__requestFrame.name = 'PseudoXmlHttpRequestFrame';
                this.__requestFrame.visibility = 'hidden';
                this.__requestFrame.clip.width = 100;
                this.__requestFrame.clip.height = 100;
                
                this.__responseFrame = new Layer(100);
                this.__responseFrame.name = 'PseudoXmlHttpResponseFrame';
                this.__responseFrame.visibility = 'hidden';
                this.__responseFrame.clip.width = 100;
                this.__responseFrame.clip.height = 100;
                break;

            // -------------------------------------------------------------------------------
            case 'IE':
                document.body.insertAdjacentHTML( 'afterBegin',
                    '<span id="PseudoXmlHttpSpan"></span>' );
                var span = document.all['PseudoXmlHttpSpan'];
                var reqFrame = '<iframe name="PseudoXmlHttpRequestFrame" src=""></iframe>';
                var resFrame = '<iframe name="PseudoXmlHttpResponseFrame" src=""></iframe>';
                span.innerHTML = reqFrame;
                span.innerHTML += resFrame;
                span.style.display = 'none';
                this.__requestFrame = window.frames['PseudoXmlHttpRequestFrame'];
                this.__responseFrame = window.frames['PseudoXmlHttpResponseFrame'];
                break;

            // -------------------------------------------------------------------------------
            case 'MOZ':  
            case 'OPR':  
            case 'KONQ':
            
                var span = document.createElement('SPAN');
                span.id = "PseudoXmlHttpSpan";
                span.style.visibility = 'hidden';
                
                var reqFrame = document.createElement('IFRAME');
                reqFrame.name = 'PseudoXmlHttpRequestFrame';
                reqFrame.id = 'PseudoXmlHttpRequestFrame';
                
                var resFrame = document.createElement('IFRAME');
                resFrame.name = 'PseudoXmlHttpResponseFrame';
                resFrame.id = 'PseudoXmlHttpResponseFrame';
                resFrame.onLoad = 'alert("Got here");';
                
                if ( this.__browser == 'OPR' ) {
                
                    reqFrame.width = 0;
                    reqFrame.height = 0;
                    resFrame.width = 0;
                    resFrame.height = 0;
                    
                } else if ( this.__browser == 'KONQ' ) {
                
                    span.style.display = none;
                    reqFrame.style.display = none;
                    reqFrame.style.visibility = hidden;
                    reqFrame.height = 0;
                    reqFrame.width = 0;
                    resFrame.style.display = none;
                    resFrame.style.visibility = hidden;
                    resFrame.height = 0;
                    resFrame.width = 0;
                    
                }
                
                document.body.appendChild( span );

                span.appendChild(reqFrame);
                span.appendChild(resFrame);
                
                this.__requestFrame = reqFrame;
                this.__responseFrame = resFrame;
                
                break;
        }
    },
    
    __createRequestForm: function() {
    
        var params = new Object();

        if ( arguments[0] ) {
            params = arguments[1];
        }
        
        var form = '';
        form += '<html><body>';
        form += '<form name="PseudoXmlHttpForm" action="'+this.__url+'"';
        form += ' method="'+this.__method+'" target="PseudoXmlHttpResponseFrame"';
        if ( this.__requestHeaders['accept'] ) {
            form += ' accept="'+this.__requestHeaders['accept']+'"';
        }
        if ( this.__requestHeaders['accept-charset'] ) {
            form += ' accept-charset="'+this.__requestHeaders['accept-charset']+'"';
        }
        if ( this.__method == 'POST' && this.__requestHeaders['content-type'] ) {
            form += ' enctype="'+this.__requestHeaders['content-type']+'"';
        }
        form += '>';
        
        for ( prop in params ) {
            if ( prop == 'var_dump' || prop == 'toPHP' ) {
                continue;
            };
            form += '<input type="hidden" name="'+prop+'"';
            form += 'value="'+params[prop].replace(/'"'/g, '\\"')+'">';
        }
        
        form += '</form></body></html>';
        
        return form;
    },
    
    __encodedStringToArgs: function(string) {
        var fields = string.split('&');
        var args = new Object();
        for (var i=0;i<fields.length;i++) {
            var arg = fields[i].split('=');
            args[arg[0]] = arg[1];
        };
        return args;
    },
    
    __handleResponse: function(self, response) {
        window.clearInterval(self.__responsePollId);
        window.clearTimeout(self.__timeoutId);
        if ( self.__responseFrame.lastModified ) {
            self.__responseHeaders['last-modified'] = self.__responseFrame.lastModified;
        }
        self.__responseHeaders['content-length'] = response.length;
        self.readyState = 4;
        self.responseText = response;
        self.onreadystatechange();
    }

}
