//---------------------------------------------------------------------------------
// Based loosely on nsXmlRpcClient: http://mozblog.mozdev.org/nsXmlRpcClient.js
// @version $Id: httpclient.js,v 1.6 2004/11/22 22:07:49 harryf Exp $
//---------------------------------------------------------------------------------

// Decorates a normal JS exception for client side errors
// @param Error
// @param string error code
function JPSpan_Client_Error(e, code) {
    e.name = 'Client_Error';
    e.code = code;
    return e;
};

//---------------------------------------------------------------------------------

function JPSpan_HttpClient() {};
JPSpan_HttpClient.prototype = {
    xmlhttp: null,
    userhandler: null,
    timeout_id: null,
    
    // @throws Error code 1000
    init: function() {
        try {
            // Mozilla / Safari
            this.xmlhttp = new XMLHttpRequest();
        } catch (e) {
            // IE
            var MSXML_XMLHTTP_PROGIDS = new Array(
                'MSXML2.XMLHTTP.5.0',
                'MSXML2.XMLHTTP.4.0',
                'MSXML2.XMLHTTP.3.0',
                'MSXML2.XMLHTTP',
                'Microsoft.XMLHTTP'
            );
            var success = false;
            for (var i=0;i < MSXML_XMLHTTP_PROGIDS.length && !success; i++) {
                try {
                    this.xmlhttp = new ActiveXObject(MSXML_XMLHTTP_PROGIDS[i]);
                    success = true;
                } catch (e) {}
            }
            if ( !success ) {
                throw JPSpan_Client_Error(
                        new Error('Unable to create XMLHttpRequest.'),
                        1000
                    );
            }
        }
    },
    
    // Place an synchronous call (results returned directly)
    // @param object request object for params and HTTP method
    // @return string response text
    // @throws Error codes 1001 and 1002
    call: function (request) {

        if ( !this.xmlhttp ) {
            this.init();
        }

        if (this.callInProgress()) {
            throw JPSpan_Client_Error(
                    new Error('Call in progress'),
                    1001
                );
        };
        

        request.type = 'sync';
        request.prepare(this.xmlhttp);
        this.xmlhttp.setRequestHeader('Accept-Charset','UTF-8');
        request.send();
        
        if ( this.xmlhttp.status == 200 ) {
            return this.xmlhttp.responseText;
        } else {
            var errorMsg = '['+this.xmlhttp.status
                            +'] '+this.xmlhttp.statusText;
            var err = new Error(errorMsg);
            err.headers = this.xmlhttp.getAllResponseHeaders();
            throw JPSpan_Client_Error(err,1002);
        }
    },

    // Place an asynchronous call (results sent to handler)
    // @param object request object for params and HTTP method
    // @param object handler: user defined object to be called
    // @throws Error code 1001
    asyncCall: function (request,handler) {
    
        var callName = null;
        if ( arguments[2] ) {
            callName = arguments[2];
        }
        
        if ( !this.xmlhttp ) {
            this.init();
        }

        if (this.callInProgress()) {
            throw JPSpan_Client_Error(
                    new Error('Call in progress'),
                    1001
                );
        };

        this.userhandler = handler;
        
        if ( this.userhandler.onInit ) {
            try {
                this.userhandler.onInit(callName);
            } catch(e) {
                this.displayHandlerError(e);
            }
        }
        
        request.type = 'async';
        request.prepare(this.xmlhttp);
        this.xmlhttp.setRequestHeader('Accept-Charset','UTF-8');

        var self = this;

        this.timeout_id = window.setTimeout(function() {
            self.abort(self, callName);
        },request.timeout);

        
        this.xmlhttp.onreadystatechange = function() {
            self.stateChangeCallback(self, callName);
        }

        request.send();
    },

    
    // Checks to see if XmlHttpRequest is busy
    // @return boolean TRUE if busy
    callInProgress: function() {

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
    
    // Callback for timeouts: aborts the request
    // @access private
    abort: function (client, callName) {

        if ( client.callInProgress() ) {
        
            client.xmlhttp.abort();
            var errorMsg = 'Operation timed out';

            if ( callName ) {
                errorMsg += ': '+callName;
            }
            
            if ( client.userhandler.onError ) {
                var ex = JPSpan_Client_Error(new Error(errorMsg), 1003);
                try {
                    client.userhandler.onError(ex, callName);
                } catch (e) {
                    client.displayHandlerError(e);
                }
            }
            
        }
    },
    
    // Called from stateChangeCallback if an error occurs in
    // in handler object
    // @access private
    displayHandlerError: function(e) {
        var errorMsg = "Error in Handler\n";
        if ( e.name ) {
            errorMsg += 'Name: '+e.name+"\n";
        };
        if ( e.message ) {
            errorMsg += 'Message: '+e.message+"\n";
        } else if ( e.description ) {
            errorMsg += 'Description: '+e.description+"\n";
        };
        if ( e.fileName ) {
            errorMsg += 'File: '+e.fileName+"\n";
        };
        if ( e.lineNumber ) {
            errorMsg += 'Line: '+e.lineNumber+"\n";
        };
        alert(errorMsg);
    },

    // Callback for asyncCalls
    // @access private
    stateChangeCallback: function(client, callName) {

        switch (client.xmlhttp.readyState) {

            // XMLHTTPRequest.open() has just been called
            case 1:
                if ( client.userhandler.onOpen ) {
                    try {
                        client.userhandler.onOpen(callName);
                    } catch(e) {
                        client.displayHandlerError(e);
                    }
                }
            break;

            // XMLHTTPRequest.send() has just been called
            case 2:
                if ( client.userhandler.onSend ) {
                    try {
                        client.userhandler.onSend(callName);
                    } catch(e) {
                        client.displayHandlerError(e);
                    }
                }
            break;
            
            // Fetching response from server in progress
            case 3:
                if ( client.userhandler.onProgress ) {
                    try {
                        client.userhandler.onProgress(callName);
                    } catch(e) {
                        client.displayHandlerError(e);
                    }
                }
            break;
            
            // Download complete
            case 4:

                window.clearTimeout(client.timeout_id);

                try {
                    switch ( client.xmlhttp.status ) {
                        case 200:
                            if ( client.userhandler.onLoad ) {
                                try {
                                    client.userhandler.onLoad(client.xmlhttp.responseText, callName);
                                } catch (e) {
                                    client.displayHandlerError(e);
                                }
                            }
                            break;
                        
                        // Special case for IE on aborted requests
                        case 0:
                            // Do nothing
                            break;
                            
                        default:
                            if ( client.userhandler.onError ) {
                                try {
                                var errorMsg = '['+client.xmlhttp.status
                                    +'] '+client.xmlhttp.statusText;
                                var err = new Error(errorMsg);
                                err.headers = this.xmlhttp.getAllResponseHeaders();
                                client.userhandler.onError(JPSpan_Client_Error(err,1002), callName);
                                } catch(e) {
                                    client.displayHandlerError(e);
                                }
                            }
                            break;
                    }

                } catch (e) {
                    // client.xmlhttp.status not available - failed requests
                }
            break;
        }
    }
}
