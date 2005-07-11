// $Id: serialize.js,v 1.5 2004/11/21 11:14:05 harryf Exp $
// Notes:
// - Watch out for recursive references - call inside a try/catch block if uncertain
// - Objects are serialized to PHP class name JPSpan_Object by default
// - Errors are serialized to PHP class name JPSpan_Error by default
//
// See discussion below for notes on Javascript reflection
// http://www.webreference.com/dhtml/column68/
function JPSpan_Serialize(Encoder) {
    this.Encoder = Encoder;
    this.typeMap = new Object();
};

JPSpan_Serialize.prototype = {

    typeMap: null,
    
    addType: function(cname, callback) {
        this.typeMap[cname] = callback;
    },
    
    serialize: function(v) {
    
        switch(typeof v) {
            //-------------------------------------------------------------------
            case 'object':
            
                // It's a null value
                if ( v === null ) {
                    return this.Encoder.encodeNull();
                }
                
                // Get the constructor
                var c = v.constructor;
                
                if (c != null ) {
                
                    // It's an array
                    if ( c == Array ) {
                        return this.Encoder.encodeArray(v,this);
                    } else {
                    
                        // Get the class name
                        var match = c.toString().match( /\s*function (.*)\(/ );

                        if ( match == null ) {
                            return this.Encoder.encodeObject(v,this,'JPSpan_Object');
                        }
                        
                        // Strip space for IE
                        var cname = match[1].replace(/\s/,'');
                        
                        // Has the user registers a callback for serializing this class?
                        if ( this.typeMap[cname] ) {
                            return this.typeMap[cname](v, this, cname);
                            
                        } else {
                            // Check for error objects
                            var match = cname.match(/Error/);
                        
                            if ( match == null ) {
                                return this.Encoder.encodeObject(v,this,'JPSpan_Object');
                            } else {
                                return this.Encoder.encodeError(v,this,'JPSpan_Error');
                            }

                        }
                    }
                } else {
                    // Return null if constructor is null
                    return this.Encoder.encodeNull();
                }
            break;
            
            //-------------------------------------------------------------------
            case 'string':
                return this.Encoder.encodeString(v);
            break;
            
            //-------------------------------------------------------------------
            case 'number':
                if (Math.round(v) == v) {
                    return this.Encoder.encodeInteger(v);
                } else {
                    return this.Encoder.encodeDouble(v);
                };
            break;
            
            //-------------------------------------------------------------------
            case 'boolean':
                if (v == true) {
                    return this.Encoder.encodeTrue();
                } else {
                    return this.Encoder.encodeFalse();
                };
            break;
            
            //-------------------------------------------------------------------
            default:
                return this.Encoder.encodeNull();
            break;
        }
    }
}

// $Id: xml.js,v 1.7 2004/11/19 21:56:47 harryf Exp $
// See: http://jpspan.sourceforge.net/wiki/doku.php?id=encoding
function JPSpan_Encode_Xml() {
    this.Serialize = new JPSpan_Serialize(this);
};

JPSpan_Encode_Xml.prototype = {

    // Used by rawpost request objects
    contentType: 'text/xml; charset=UTF-8',

    encode: function(data) {
        return '<?xml version="1.0" encoding="UTF-8"?><r>'+this.Serialize.serialize(data)+'</r>';
    },
    
    encodeInteger: function(v) {
        return '<i v="'+v+'"/>';
    },
    
    encodeDouble: function(v) {
        return '<d v="'+v+'"/>';
    },
    
    // Need UFT-8 encoding?
    encodeString: function(v) {
        return '<s>'+v.replace(/&/g, '&amp;').replace(/</g, '&lt;')+'</s>';
    },
    
    encodeNull: function() {
        return '<n/>';
    },
    
    encodeTrue: function() {
        return '<b v="1"/>';
    },
    
    encodeFalse: function() {
        return '<b v="0"/>';
    },
    
    // Arrays being with indexed values - properties added second
    encodeArray: function(v, Serializer) {
        var indexed = new Array();
        var a = '';
        for (var i=0; i<v.length; i++) {
            indexed[i] = true;
            a += '<e k="'+i+'">'+Serializer.serialize(v[i])+'</e>';
        };

        for ( var prop in v ) {
            if ( indexed[prop] ) {
                continue;
            };
            // Assumes prop obeys Javascript naming rules
            a += '<e k="'+prop+'">'+Serializer.serialize(v[prop])+'</e>';
        };
        return '<a>'+a+'</a>';
    },
    
    encodeObject: function(v, Serializer, cname) {
        var o='';
        for (var prop in v) {
            o += '<e k="'+prop+'">'+Serializer.serialize(v[prop])+'</e>';
        };
        return '<o c="'+cname.toLowerCase()+'">'+o+'</o>';
    },
    
    encodeError: function(v, Serializer, cname) {
        var e = new Object();
        if ( !v.name ) {
            e.name = cname;
            e.message = v.description;
        } else {
            e.name = v.name;
            e.message = v.message;
        };
        return this.encodeObject(e,Serializer,cname);
    }
}
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

// $Id: request.js,v 1.4 2004/11/19 20:04:05 harryf Exp $
// Base request "class"
function JPSpan_Request(encoder) {
    this.encoder = encoder;
}
JPSpan_Request.prototype = {

    // Instance of an encoder
    encoder: null,
    
    // The URL of the server
    serverurl: '',
    
    // The actual URL the request is sent to (may be modified for GET requests)
    requesturl: '',
    
    // Body of request (for HTTP POST only)
    body: '',
    
    // Remote method arguments list
    args: null,
    
    // Type of request (async / sync)
    type: null,

    // Instance of XMLHttpRequest
    http: null,

    // Timeout in milliseconds for requests
    timeout: 20000,
    
    // Add an argument for the remote method
    // @param string argument name
    // @param mixed value
    // @return void
    // @throws Error code 1004
    addArg: function(name, value) {
        if ( !this.args ) {
            this.args = [];
        }
        var illegal = /[\W_]/;
        if (!illegal.test(name) ) {
            this.args[name] = value;
        } else {
            throw JPSpan_Client_Error(
                    new Error('Invalid parameter name ('+name+')'),
                    1004
                );
        }
    },

    // Reset the request object
    // @return void
    // @access public
    reset: function() {
        this.serverurl = '';
        this.requesturl = '';
        this.body = '';
        this.args = null;
        this.type = null;
        this.http = null;
        this.timeout = 20000;
    },
    
    // Used internally by request objects to build the request payload
    // @protected
    // @abstract
    build: function() {},
    
    // Used by JPSpan_HTTPClient to prepare the XMLHttpRequest object
    // @param XMLHttpRequest
    // @return void
    // @protected
    // @abstract
    prepare: function(http) {},
    
    // Used by JPSpan_HTTPClient to call send on the XMLHttpRequest object
    // @return void
    // @protected
    // @abstract
    send: function(http) {}
};

// @version $Id: rawpost.js,v 1.3 2004/11/15 12:14:28 harryf Exp $

// For building raw (not urlencoded) HTTP POST requests
function JPSpan_Request_RawPost(encoder) {

    var oParent = new JPSpan_Request(encoder);
    
    // Builds the post body
    // @protected
    // @throws Error code 1006
    oParent.build = function() {
        try {
            this.body = this.encoder.encode(this.args);
        } catch (e) {
            throw JPSpan_Client_Error(e, 1006);
        };
        this.requesturl = this.serverurl;
    };
    
    // Called from JPSpan_HttpClient to prepare the XMLHttpRequest object
    // @param XMLHttpRequest
    // @protected
    // @throws Error codes 1005, 1006 and 1007
    oParent.prepare = function(http) {
        this.http = http;
        this.build();
        switch ( this.type ) {
            case 'async':
                try {
                    this.http.open('POST',this.requesturl,true);
                } catch (e) {
                    throw JPSpan_Client_Error(new Error(e),1007);
                };
            break;
            case 'sync':
                try {
                    this.http.open('POST',this.requesturl,false);
                } catch (e) {
                    throw JPSpan_Client_Error(new Error(e),1007);
                };
            break;
            default:
                throw JPSpan_Client_Error(
                        new Error('Call type invalid '+this.type),
                        1005
                    );
            break;
        };
        this.http.setRequestHeader('Content-Length', this.body.length);
        this.http.setRequestHeader('Content-Type',this.encoder.contentType);
    };
    
    // Send the request
    // @protected
    oParent.send = function() {
        this.http.send(this.body);
    };
    
    return oParent;
};
// $Id: remoteobject.js,v 1.13 2005/05/26 22:40:01 harryf Exp $
// Base class for generated classes
function JPSpan_RemoteObject() {}

JPSpan_RemoteObject.prototype = {

    // Switch to asyncronous mode
    // @param Object user defined handler to call
    // @access public
    Async: function(userHandler) {
        this.__initResponseHandler(this,userHandler);
        this.__callState = "async";
    },
    
    // Switch to syncronous mode. Be warned: timeouts not supported!
    // @access public
    Sync: function() {
        this.__responseHandler = null;
        this.__callState = "sync";
    },
    
    // Returns the instance of XMLHttpRequest being used by
    // JPSpan_HttpClient. Allows you to bypass the APIs and
    // access it directly, for things like setting / getting HTTP 
    // headers - calling open() or send() not recommended
    // @return XMLHttpRequest
    // @access public
    GetXMLHttp: function() {
        if ( !this.__client ) {
            this.__initClient();
        }
        return this.__client.xmlhttp;
    },
    
    // Called when a error occurs in making the request
    // on the client-side. Typically these be transport errors
    // e.g. server HTTP status code != 200
    // Replace with your own function as required
    // @access public
    clientErrorFunc: function(e) {
    
        alert(this.__drawErrorMsg('Client_Error',e));
        
    },
    
    // Timeout for async requests in milliseconds
    // @access public
    timeout: 20000,
    
    // Called when a error in handling the response from
    // the server (e.g. the response was junk or some PHP
    // error occurred)
    // Replace with your own function as required
    // @access public

    serverErrorFunc: function(e) {

        var errorMsg = this.__drawErrorMsg('Server_Error',e);
        
        if ( e.file && e.line ) {
            errorMsg += "\nServer script: "
                +e.file+" on line "+e.line;
        }

        alert(errorMsg);

    },
    
    // Called when the application running on the server
    // returns an error (e.g. a divide by zero error).
    // When making async calls, local error methods
    // will be called first (if they exist)
    // Replace with your own function as required
    // @access public
    applicationErrorFunc: function(e) {

        alert(this.__drawErrorMsg('Application_Error',e));

    },
    
    // Builds a string error message from an exceptions
    // properties
    // @private
    __drawErrorMsg: function(type, e) {
        
        try {
            var errorMsg = '['+e.name+']';
            if ( e.code ) {
                errorMsg += '['+e.code+']';
            }
            errorMsg += ' '+e.message;
        } catch (ex) {
            var errorMsg = '['+type+'] ';
            if ( e.code ) {
                errorMsg += '['+e.code+']';
            }
            errorMsg += ' '+e.message;
        }
        
        if ( e.client && e.call ) {
            errorMsg += "\nMethod called: "
                +e.client+ "."+e.call+"()";
        }
        
        return errorMsg;
    },
    
    // Private stuff from here...
    // @var string Url to server handler
    // @access private
    __serverurl: null,

    // @var JPSpan_Request subclass object
    // @access private
    __request: null,
    
    // @var JPSpan_HttpClient
    // @access private
    __client: null,
    
    // @var Object handlers responses to async calls
    // @access private
    __responseHandler: null,
    
    // @var string type of calls to make: sync or async
    // @access private
    __callState: 'sync',
    
    // @var string Name of the remote class for error messages
    // @acess private
    __remoteClass: '',
    
    // Initialize the XmlHttpClient
    // @access private
    __initClient: function() {
        this.__client = new JPSpan_HttpClient();
    },
    
    // Sets up the response handler
    // @access private
    __initResponseHandler: function(self,userHandler) {
    
        self.__responseHandler = new Object();
        
        self.__responseHandler.context = self;
        
        self.__responseHandler.userHandler = userHandler;
        
        self.__responseHandler.onInit = function(callName) {
            var initFunc = callName+'Init';
            if ( this.userHandler[initFunc] ) {
                try {
                    this.userHandler[initFunc]();
                } catch(e) {
                    self.__client.displayHandlerError(e);
                }
            }
        },
        
        self.__responseHandler.onOpen = function(callName) {
            var openFunc = callName+'Open';
            if ( this.userHandler[openFunc] ) {
                try {
                    this.userHandler[openFunc]();
                } catch(e) {
                    self.__client.displayHandlerError(e);
                }
            }
        },
        
        self.__responseHandler.onSend = function(callName) {
            var sendFunc = callName+'Send';
            if ( this.userHandler[sendFunc] ) {
                try {
                    this.userHandler[sendFunc]();
                } catch(e) {
                    self.__client.displayHandlerError(e);
                }
            }
        },
        
        self.__responseHandler.onProgress = function(callName) {
            var progressFunc = callName+'Progress';
            if ( this.userHandler[progressFunc] ) {
                try {
                    this.userHandler[progressFunc]();
                } catch(e) {
                    self.__client.displayHandlerError(e);
                }
            }
        },
        
        self.__responseHandler.onLoad = function(response, callName) {

            try {
                dataFunc = eval(response);

                try {
                    data = dataFunc();
                    
                    if ( this.userHandler[callName] ) {
                        try {
                            this.userHandler[callName](data);
                        } catch(e) {
                            // Error in handler method (e.g. syntax error) - display it
                            self.__client.displayHandlerError(e);
                        }
                    } else {
                        alert('Your handler must define a method '+callName);
                    }

                } catch (e) {

                    e.client = self.__responseHandler.context.__remoteClass;
                    e.call = callName;
                    
                    if ( e.name == 'Server_Error' ) {
                        this.context.serverErrorFunc(e);
                    } else {
                    
                        var errorFunc = callName+'Error';
                        
                        if ( this.userHandler[errorFunc] ) {
                            try {
                                this.userHandler[errorFunc](e);
                            } catch(e) {
                                // Error in handler method (e.g. syntax error) - display it
                                self.__client.displayHandlerError(e);
                            }
                        } else {
                            this.context.applicationErrorFunc(e);
                        }

                    }

                }

            } catch (e) {

                e.name = 'Server_Error';
                e.code = 2006;
                e.response = response;
                e.client = self.__responseHandler.context.__remoteClass;
                e.call = callName;
                this.context.serverErrorFunc(e);

            }
           
        };
        
        self.__responseHandler.onError = function(e, callName) {
            e.client = self.__responseHandler.context.__remoteClass;
            e.call = callName;
            this.context.clientErrorFunc(e);
        };
        
    },
    
    // Call remote procedure (passes onto __asyncCall or __syncCall)
    // @access private
    __call: function(url,args,callName) {
    
        if ( !this.__client ) {
            this.__initClient();
        }

        
        this.__request.reset();
        this.__request.serverurl = url;
        this.__request.timeout = this.timeout;
        
        for(var i=0; i < args.length; i++) {
            this.__request.addArg(i,args[i]);
        };
        
        if ( this.__callState == "async" ) {
            return this.__asyncCall(this.__request,callName);
        } else {
            return this.__syncCall(this.__request);
        }

    },
    
    // Call remote procedure asynchronously
    // @access private
    __asyncCall: function(request, callName) {

        try {
            this.__client.asyncCall(request,this.__responseHandler,callName);
        } catch (e) {
            this.clientErrorFunc(e);
        }

        return;

    },
    
    // Call remote procedure synchronously
    // @access private
    __syncCall: function(request) {

        try {
            var response = this.__client.call(request);

            try {
                var dataFunc = eval(response);
                
                try {
                    return dataFunc();
                } catch (e) {
                
                    if ( e.name == 'Server_Error' ) {
                        this.serverErrorFunc(e);
                    } else {
                        this.applicationErrorFunc(e);
                    }

                }

            } catch (e) {
                e.name = 'Server_Error';
                e.code = 2006;
                e.response = response;
                this.serverErrorFunc(e);
            }

        } catch(e) {
            this.clientErrorFunc(e);
        }

    }

};


function c_patientfinder() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?c_patientfinder';
    
    oParent.__remoteClass = 'c_patientfinder';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.find_remoting = function() {
        var url = this.__serverurl+'/find_remoting/';
        return this.__call(url,arguments,'find_remoting');
    };
    
    return oParent;
}


function c_coding() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?c_coding';
    
    oParent.__remoteClass = 'c_coding';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.icd_search = function() {
        var url = this.__serverurl+'/icd_search/';
        return this.__call(url,arguments,'icd_search');
    };
    
    // @access public
    oParent.cpt_search = function() {
        var url = this.__serverurl+'/cpt_search/';
        return this.__call(url,arguments,'cpt_search');
    };
    
    return oParent;
}


function feescheduledatasource() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?feescheduledatasource';
    
    oParent.__remoteClass = 'feescheduledatasource';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.updatefield = function() {
        var url = this.__serverurl+'/updatefield/';
        return this.__call(url,arguments,'updatefield');
    };
    
    // @access public
    oParent.getmeta = function() {
        var url = this.__serverurl+'/getmeta/';
        return this.__call(url,arguments,'getmeta');
    };
    
    // @access public
    oParent.getsql = function() {
        var url = this.__serverurl+'/getsql/';
        return this.__call(url,arguments,'getsql');
    };
    
    // @access public
    oParent.fetchrow = function() {
        var url = this.__serverurl+'/fetchrow/';
        return this.__call(url,arguments,'fetchrow');
    };
    
    // @access public
    oParent.fetchbulk = function() {
        var url = this.__serverurl+'/fetchbulk/';
        return this.__call(url,arguments,'fetchbulk');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.setlimit = function() {
        var url = this.__serverurl+'/setlimit/';
        return this.__call(url,arguments,'setlimit');
    };
    
    // @access public
    oParent.numrows = function() {
        var url = this.__serverurl+'/numrows/';
        return this.__call(url,arguments,'numrows');
    };
    
    // @access public
    oParent.getcolumnlabels = function() {
        var url = this.__serverurl+'/getcolumnlabels/';
        return this.__call(url,arguments,'getcolumnlabels');
    };
    
    // @access public
    oParent.rewind = function() {
        var url = this.__serverurl+'/rewind/';
        return this.__call(url,arguments,'rewind');
    };
    
    // @access public
    oParent.valid = function() {
        var url = this.__serverurl+'/valid/';
        return this.__call(url,arguments,'valid');
    };
    
    // @access public
    oParent.next = function() {
        var url = this.__serverurl+'/next/';
        return this.__call(url,arguments,'next');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.preview = function() {
        var url = this.__serverurl+'/preview/';
        return this.__call(url,arguments,'preview');
    };
    
    // @access public
    oParent.numcols = function() {
        var url = this.__serverurl+'/numcols/';
        return this.__call(url,arguments,'numcols');
    };
    
    // @access public
    oParent.addorderrule = function() {
        var url = this.__serverurl+'/addorderrule/';
        return this.__call(url,arguments,'addorderrule');
    };
    
    // @access public
    oParent.adddefaultorderrule = function() {
        var url = this.__serverurl+'/adddefaultorderrule/';
        return this.__call(url,arguments,'adddefaultorderrule');
    };
    
    // @access public
    oParent.loaddefaultorderrules = function() {
        var url = this.__serverurl+'/loaddefaultorderrules/';
        return this.__call(url,arguments,'loaddefaultorderrules');
    };
    
    // @access public
    oParent.ordersort = function() {
        var url = this.__serverurl+'/ordersort/';
        return this.__call(url,arguments,'ordersort');
    };
    
    // @access public
    oParent.getrendermap = function() {
        var url = this.__serverurl+'/getrendermap/';
        return this.__call(url,arguments,'getrendermap');
    };
    
    // @access public
    oParent.prepare = function() {
        var url = this.__serverurl+'/prepare/';
        return this.__call(url,arguments,'prepare');
    };
    
    // @access public
    oParent.registerfilter = function() {
        var url = this.__serverurl+'/registerfilter/';
        return this.__call(url,arguments,'registerfilter');
    };
    
    // @access public
    oParent.clearfilters = function() {
        var url = this.__serverurl+'/clearfilters/';
        return this.__call(url,arguments,'clearfilters');
    };
    
    // @access public
    oParent.registertemplate = function() {
        var url = this.__serverurl+'/registertemplate/';
        return this.__call(url,arguments,'registertemplate');
    };
    
    // @access public
    oParent.setlabel = function() {
        var url = this.__serverurl+'/setlabel/';
        return this.__call(url,arguments,'setlabel');
    };
    
    // @access public
    oParent.toarray = function() {
        var url = this.__serverurl+'/toarray/';
        return this.__call(url,arguments,'toarray');
    };
    
    // @access public
    oParent.emptyfill = function() {
        var url = this.__serverurl+'/emptyfill/';
        return this.__call(url,arguments,'emptyfill');
    };
    
    // @access public
    oParent.enumlookup = function() {
        var url = this.__serverurl+'/enumlookup/';
        return this.__call(url,arguments,'enumlookup');
    };
    
    // @access public
    oParent.setrevision = function() {
        var url = this.__serverurl+'/setrevision/';
        return this.__call(url,arguments,'setrevision');
    };
    
    // @access public
    oParent.addfeeschedule = function() {
        var url = this.__serverurl+'/addfeeschedule/';
        return this.__call(url,arguments,'addfeeschedule');
    };
    
    // @access public
    oParent.reset = function() {
        var url = this.__serverurl+'/reset/';
        return this.__call(url,arguments,'reset');
    };
    
    // @access public
    oParent.addfilter = function() {
        var url = this.__serverurl+'/addfilter/';
        return this.__call(url,arguments,'addfilter');
    };
    
    // @access public
    oParent.dropfilter = function() {
        var url = this.__serverurl+'/dropfilter/';
        return this.__call(url,arguments,'dropfilter');
    };
    
    return oParent;
}


function superbilldatasource() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?superbilldatasource';
    
    oParent.__remoteClass = 'superbilldatasource';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.prepare = function() {
        var url = this.__serverurl+'/prepare/';
        return this.__call(url,arguments,'prepare');
    };
    
    // @access public
    oParent.reset = function() {
        var url = this.__serverurl+'/reset/';
        return this.__call(url,arguments,'reset');
    };
    
    // @access public
    oParent.addfilter = function() {
        var url = this.__serverurl+'/addfilter/';
        return this.__call(url,arguments,'addfilter');
    };
    
    // @access public
    oParent.dropfilter = function() {
        var url = this.__serverurl+'/dropfilter/';
        return this.__call(url,arguments,'dropfilter');
    };
    
    // @access public
    oParent.updatefield = function() {
        var url = this.__serverurl+'/updatefield/';
        return this.__call(url,arguments,'updatefield');
    };
    
    // @access public
    oParent.getmeta = function() {
        var url = this.__serverurl+'/getmeta/';
        return this.__call(url,arguments,'getmeta');
    };
    
    // @access public
    oParent.getsql = function() {
        var url = this.__serverurl+'/getsql/';
        return this.__call(url,arguments,'getsql');
    };
    
    // @access public
    oParent.fetchrow = function() {
        var url = this.__serverurl+'/fetchrow/';
        return this.__call(url,arguments,'fetchrow');
    };
    
    // @access public
    oParent.fetchbulk = function() {
        var url = this.__serverurl+'/fetchbulk/';
        return this.__call(url,arguments,'fetchbulk');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.setlimit = function() {
        var url = this.__serverurl+'/setlimit/';
        return this.__call(url,arguments,'setlimit');
    };
    
    // @access public
    oParent.numrows = function() {
        var url = this.__serverurl+'/numrows/';
        return this.__call(url,arguments,'numrows');
    };
    
    // @access public
    oParent.getcolumnlabels = function() {
        var url = this.__serverurl+'/getcolumnlabels/';
        return this.__call(url,arguments,'getcolumnlabels');
    };
    
    // @access public
    oParent.rewind = function() {
        var url = this.__serverurl+'/rewind/';
        return this.__call(url,arguments,'rewind');
    };
    
    // @access public
    oParent.valid = function() {
        var url = this.__serverurl+'/valid/';
        return this.__call(url,arguments,'valid');
    };
    
    // @access public
    oParent.next = function() {
        var url = this.__serverurl+'/next/';
        return this.__call(url,arguments,'next');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.preview = function() {
        var url = this.__serverurl+'/preview/';
        return this.__call(url,arguments,'preview');
    };
    
    // @access public
    oParent.numcols = function() {
        var url = this.__serverurl+'/numcols/';
        return this.__call(url,arguments,'numcols');
    };
    
    // @access public
    oParent.addorderrule = function() {
        var url = this.__serverurl+'/addorderrule/';
        return this.__call(url,arguments,'addorderrule');
    };
    
    // @access public
    oParent.adddefaultorderrule = function() {
        var url = this.__serverurl+'/adddefaultorderrule/';
        return this.__call(url,arguments,'adddefaultorderrule');
    };
    
    // @access public
    oParent.loaddefaultorderrules = function() {
        var url = this.__serverurl+'/loaddefaultorderrules/';
        return this.__call(url,arguments,'loaddefaultorderrules');
    };
    
    // @access public
    oParent.ordersort = function() {
        var url = this.__serverurl+'/ordersort/';
        return this.__call(url,arguments,'ordersort');
    };
    
    // @access public
    oParent.getrendermap = function() {
        var url = this.__serverurl+'/getrendermap/';
        return this.__call(url,arguments,'getrendermap');
    };
    
    // @access public
    oParent.registerfilter = function() {
        var url = this.__serverurl+'/registerfilter/';
        return this.__call(url,arguments,'registerfilter');
    };
    
    // @access public
    oParent.clearfilters = function() {
        var url = this.__serverurl+'/clearfilters/';
        return this.__call(url,arguments,'clearfilters');
    };
    
    // @access public
    oParent.registertemplate = function() {
        var url = this.__serverurl+'/registertemplate/';
        return this.__call(url,arguments,'registertemplate');
    };
    
    // @access public
    oParent.setlabel = function() {
        var url = this.__serverurl+'/setlabel/';
        return this.__call(url,arguments,'setlabel');
    };
    
    // @access public
    oParent.toarray = function() {
        var url = this.__serverurl+'/toarray/';
        return this.__call(url,arguments,'toarray');
    };
    
    // @access public
    oParent.emptyfill = function() {
        var url = this.__serverurl+'/emptyfill/';
        return this.__call(url,arguments,'emptyfill');
    };
    
    // @access public
    oParent.enumlookup = function() {
        var url = this.__serverurl+'/enumlookup/';
        return this.__call(url,arguments,'enumlookup');
    };
    
    return oParent;
}


function icdcodingdatasource() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?icdcodingdatasource';
    
    oParent.__remoteClass = 'icdcodingdatasource';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.codingdatasource = function() {
        var url = this.__serverurl+'/codingdatasource/';
        return this.__call(url,arguments,'codingdatasource');
    };
    
    // @access public
    oParent.superbilldatasource = function() {
        var url = this.__serverurl+'/superbilldatasource/';
        return this.__call(url,arguments,'superbilldatasource');
    };
    
    // @access public
    oParent.prepare = function() {
        var url = this.__serverurl+'/prepare/';
        return this.__call(url,arguments,'prepare');
    };
    
    // @access public
    oParent.reset = function() {
        var url = this.__serverurl+'/reset/';
        return this.__call(url,arguments,'reset');
    };
    
    // @access public
    oParent.addfilter = function() {
        var url = this.__serverurl+'/addfilter/';
        return this.__call(url,arguments,'addfilter');
    };
    
    // @access public
    oParent.dropfilter = function() {
        var url = this.__serverurl+'/dropfilter/';
        return this.__call(url,arguments,'dropfilter');
    };
    
    // @access public
    oParent.updatefield = function() {
        var url = this.__serverurl+'/updatefield/';
        return this.__call(url,arguments,'updatefield');
    };
    
    // @access public
    oParent.getmeta = function() {
        var url = this.__serverurl+'/getmeta/';
        return this.__call(url,arguments,'getmeta');
    };
    
    // @access public
    oParent.getsql = function() {
        var url = this.__serverurl+'/getsql/';
        return this.__call(url,arguments,'getsql');
    };
    
    // @access public
    oParent.fetchrow = function() {
        var url = this.__serverurl+'/fetchrow/';
        return this.__call(url,arguments,'fetchrow');
    };
    
    // @access public
    oParent.fetchbulk = function() {
        var url = this.__serverurl+'/fetchbulk/';
        return this.__call(url,arguments,'fetchbulk');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.setlimit = function() {
        var url = this.__serverurl+'/setlimit/';
        return this.__call(url,arguments,'setlimit');
    };
    
    // @access public
    oParent.numrows = function() {
        var url = this.__serverurl+'/numrows/';
        return this.__call(url,arguments,'numrows');
    };
    
    // @access public
    oParent.getcolumnlabels = function() {
        var url = this.__serverurl+'/getcolumnlabels/';
        return this.__call(url,arguments,'getcolumnlabels');
    };
    
    // @access public
    oParent.rewind = function() {
        var url = this.__serverurl+'/rewind/';
        return this.__call(url,arguments,'rewind');
    };
    
    // @access public
    oParent.valid = function() {
        var url = this.__serverurl+'/valid/';
        return this.__call(url,arguments,'valid');
    };
    
    // @access public
    oParent.next = function() {
        var url = this.__serverurl+'/next/';
        return this.__call(url,arguments,'next');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.preview = function() {
        var url = this.__serverurl+'/preview/';
        return this.__call(url,arguments,'preview');
    };
    
    // @access public
    oParent.numcols = function() {
        var url = this.__serverurl+'/numcols/';
        return this.__call(url,arguments,'numcols');
    };
    
    // @access public
    oParent.addorderrule = function() {
        var url = this.__serverurl+'/addorderrule/';
        return this.__call(url,arguments,'addorderrule');
    };
    
    // @access public
    oParent.adddefaultorderrule = function() {
        var url = this.__serverurl+'/adddefaultorderrule/';
        return this.__call(url,arguments,'adddefaultorderrule');
    };
    
    // @access public
    oParent.loaddefaultorderrules = function() {
        var url = this.__serverurl+'/loaddefaultorderrules/';
        return this.__call(url,arguments,'loaddefaultorderrules');
    };
    
    // @access public
    oParent.ordersort = function() {
        var url = this.__serverurl+'/ordersort/';
        return this.__call(url,arguments,'ordersort');
    };
    
    // @access public
    oParent.getrendermap = function() {
        var url = this.__serverurl+'/getrendermap/';
        return this.__call(url,arguments,'getrendermap');
    };
    
    // @access public
    oParent.registerfilter = function() {
        var url = this.__serverurl+'/registerfilter/';
        return this.__call(url,arguments,'registerfilter');
    };
    
    // @access public
    oParent.clearfilters = function() {
        var url = this.__serverurl+'/clearfilters/';
        return this.__call(url,arguments,'clearfilters');
    };
    
    // @access public
    oParent.registertemplate = function() {
        var url = this.__serverurl+'/registertemplate/';
        return this.__call(url,arguments,'registertemplate');
    };
    
    // @access public
    oParent.setlabel = function() {
        var url = this.__serverurl+'/setlabel/';
        return this.__call(url,arguments,'setlabel');
    };
    
    // @access public
    oParent.toarray = function() {
        var url = this.__serverurl+'/toarray/';
        return this.__call(url,arguments,'toarray');
    };
    
    // @access public
    oParent.emptyfill = function() {
        var url = this.__serverurl+'/emptyfill/';
        return this.__call(url,arguments,'emptyfill');
    };
    
    // @access public
    oParent.enumlookup = function() {
        var url = this.__serverurl+'/enumlookup/';
        return this.__call(url,arguments,'enumlookup');
    };
    
    return oParent;
}


function cptcodingdatasource() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?cptcodingdatasource';
    
    oParent.__remoteClass = 'cptcodingdatasource';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.codingdatasource = function() {
        var url = this.__serverurl+'/codingdatasource/';
        return this.__call(url,arguments,'codingdatasource');
    };
    
    // @access public
    oParent.superbilldatasource = function() {
        var url = this.__serverurl+'/superbilldatasource/';
        return this.__call(url,arguments,'superbilldatasource');
    };
    
    // @access public
    oParent.prepare = function() {
        var url = this.__serverurl+'/prepare/';
        return this.__call(url,arguments,'prepare');
    };
    
    // @access public
    oParent.reset = function() {
        var url = this.__serverurl+'/reset/';
        return this.__call(url,arguments,'reset');
    };
    
    // @access public
    oParent.addfilter = function() {
        var url = this.__serverurl+'/addfilter/';
        return this.__call(url,arguments,'addfilter');
    };
    
    // @access public
    oParent.dropfilter = function() {
        var url = this.__serverurl+'/dropfilter/';
        return this.__call(url,arguments,'dropfilter');
    };
    
    // @access public
    oParent.updatefield = function() {
        var url = this.__serverurl+'/updatefield/';
        return this.__call(url,arguments,'updatefield');
    };
    
    // @access public
    oParent.getmeta = function() {
        var url = this.__serverurl+'/getmeta/';
        return this.__call(url,arguments,'getmeta');
    };
    
    // @access public
    oParent.getsql = function() {
        var url = this.__serverurl+'/getsql/';
        return this.__call(url,arguments,'getsql');
    };
    
    // @access public
    oParent.fetchrow = function() {
        var url = this.__serverurl+'/fetchrow/';
        return this.__call(url,arguments,'fetchrow');
    };
    
    // @access public
    oParent.fetchbulk = function() {
        var url = this.__serverurl+'/fetchbulk/';
        return this.__call(url,arguments,'fetchbulk');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.setlimit = function() {
        var url = this.__serverurl+'/setlimit/';
        return this.__call(url,arguments,'setlimit');
    };
    
    // @access public
    oParent.numrows = function() {
        var url = this.__serverurl+'/numrows/';
        return this.__call(url,arguments,'numrows');
    };
    
    // @access public
    oParent.getcolumnlabels = function() {
        var url = this.__serverurl+'/getcolumnlabels/';
        return this.__call(url,arguments,'getcolumnlabels');
    };
    
    // @access public
    oParent.rewind = function() {
        var url = this.__serverurl+'/rewind/';
        return this.__call(url,arguments,'rewind');
    };
    
    // @access public
    oParent.valid = function() {
        var url = this.__serverurl+'/valid/';
        return this.__call(url,arguments,'valid');
    };
    
    // @access public
    oParent.next = function() {
        var url = this.__serverurl+'/next/';
        return this.__call(url,arguments,'next');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.preview = function() {
        var url = this.__serverurl+'/preview/';
        return this.__call(url,arguments,'preview');
    };
    
    // @access public
    oParent.numcols = function() {
        var url = this.__serverurl+'/numcols/';
        return this.__call(url,arguments,'numcols');
    };
    
    // @access public
    oParent.addorderrule = function() {
        var url = this.__serverurl+'/addorderrule/';
        return this.__call(url,arguments,'addorderrule');
    };
    
    // @access public
    oParent.adddefaultorderrule = function() {
        var url = this.__serverurl+'/adddefaultorderrule/';
        return this.__call(url,arguments,'adddefaultorderrule');
    };
    
    // @access public
    oParent.loaddefaultorderrules = function() {
        var url = this.__serverurl+'/loaddefaultorderrules/';
        return this.__call(url,arguments,'loaddefaultorderrules');
    };
    
    // @access public
    oParent.ordersort = function() {
        var url = this.__serverurl+'/ordersort/';
        return this.__call(url,arguments,'ordersort');
    };
    
    // @access public
    oParent.getrendermap = function() {
        var url = this.__serverurl+'/getrendermap/';
        return this.__call(url,arguments,'getrendermap');
    };
    
    // @access public
    oParent.registerfilter = function() {
        var url = this.__serverurl+'/registerfilter/';
        return this.__call(url,arguments,'registerfilter');
    };
    
    // @access public
    oParent.clearfilters = function() {
        var url = this.__serverurl+'/clearfilters/';
        return this.__call(url,arguments,'clearfilters');
    };
    
    // @access public
    oParent.registertemplate = function() {
        var url = this.__serverurl+'/registertemplate/';
        return this.__call(url,arguments,'registertemplate');
    };
    
    // @access public
    oParent.setlabel = function() {
        var url = this.__serverurl+'/setlabel/';
        return this.__call(url,arguments,'setlabel');
    };
    
    // @access public
    oParent.toarray = function() {
        var url = this.__serverurl+'/toarray/';
        return this.__call(url,arguments,'toarray');
    };
    
    // @access public
    oParent.emptyfill = function() {
        var url = this.__serverurl+'/emptyfill/';
        return this.__call(url,arguments,'emptyfill');
    };
    
    // @access public
    oParent.enumlookup = function() {
        var url = this.__serverurl+'/enumlookup/';
        return this.__call(url,arguments,'enumlookup');
    };
    
    return oParent;
}


function encounter() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?encounter';
    
    oParent.__remoteClass = 'encounter';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.appointmentlist_remoting = function() {
        var url = this.__serverurl+'/appointmentlist_remoting/';
        return this.__call(url,arguments,'appointmentlist_remoting');
    };
    
    return oParent;
}


function report() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?report';
    
    oParent.__remoteClass = 'report';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.ordataobject = function() {
        var url = this.__serverurl+'/ordataobject/';
        return this.__call(url,arguments,'ordataobject');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.storage_defaults = function() {
        var url = this.__serverurl+'/storage_defaults/';
        return this.__call(url,arguments,'storage_defaults');
    };
    
    // @access public
    oParent.persist = function() {
        var url = this.__serverurl+'/persist/';
        return this.__call(url,arguments,'persist');
    };
    
    // @access public
    oParent.populate = function() {
        var url = this.__serverurl+'/populate/';
        return this.__call(url,arguments,'populate');
    };
    
    // @access public
    oParent.populatemetadata = function() {
        var url = this.__serverurl+'/populatemetadata/';
        return this.__call(url,arguments,'populatemetadata');
    };
    
    // @access public
    oParent.addmetahints = function() {
        var url = this.__serverurl+'/addmetahints/';
        return this.__call(url,arguments,'addmetahints');
    };
    
    // @access public
    oParent.populate_array = function() {
        var url = this.__serverurl+'/populate_array/';
        return this.__call(url,arguments,'populate_array');
    };
    
    // @access public
    oParent.factory = function() {
        var url = this.__serverurl+'/factory/';
        return this.__call(url,arguments,'factory');
    };
    
    // @access public
    oParent.factory_include = function() {
        var url = this.__serverurl+'/factory_include/';
        return this.__call(url,arguments,'factory_include');
    };
    
    // @access public
    oParent.exists = function() {
        var url = this.__serverurl+'/exists/';
        return this.__call(url,arguments,'exists');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.set = function() {
        var url = this.__serverurl+'/set/';
        return this.__call(url,arguments,'set');
    };
    
    // @access public
    oParent.getchecked = function() {
        var url = this.__serverurl+'/getchecked/';
        return this.__call(url,arguments,'getchecked');
    };
    
    // @access public
    oParent.drop = function() {
        var url = this.__serverurl+'/drop/';
        return this.__call(url,arguments,'drop');
    };
    
    // @access public
    oParent.tostring = function() {
        var url = this.__serverurl+'/tostring/';
        return this.__call(url,arguments,'tostring');
    };
    
    // @access public
    oParent.ispopulated = function() {
        var url = this.__serverurl+'/ispopulated/';
        return this.__call(url,arguments,'ispopulated');
    };
    
    // @access public
    oParent.getreportds = function() {
        var url = this.__serverurl+'/getreportds/';
        return this.__call(url,arguments,'getreportds');
    };
    
    // @access public
    oParent.templateviewfilter = function() {
        var url = this.__serverurl+'/templateviewfilter/';
        return this.__call(url,arguments,'templateviewfilter');
    };
    
    // @access public
    oParent.report_factory = function() {
        var url = this.__serverurl+'/report_factory/';
        return this.__call(url,arguments,'report_factory');
    };
    
    // @access public
    oParent.fromtemplateid = function() {
        var url = this.__serverurl+'/fromtemplateid/';
        return this.__call(url,arguments,'fromtemplateid');
    };
    
    // @access public
    oParent.generatedefaulttemplate = function() {
        var url = this.__serverurl+'/generatedefaulttemplate/';
        return this.__call(url,arguments,'generatedefaulttemplate');
    };
    
    // @access public
    oParent.getreportlist = function() {
        var url = this.__serverurl+'/getreportlist/';
        return this.__call(url,arguments,'getreportlist');
    };
    
    // @access public
    oParent.gettemplatelist = function() {
        var url = this.__serverurl+'/gettemplatelist/';
        return this.__call(url,arguments,'gettemplatelist');
    };
    
    // @access public
    oParent.getreportlabels = function() {
        var url = this.__serverurl+'/getreportlabels/';
        return this.__call(url,arguments,'getreportlabels');
    };
    
    // @access public
    oParent.get_templates = function() {
        var url = this.__serverurl+'/get_templates/';
        return this.__call(url,arguments,'get_templates');
    };
    
    // @access public
    oParent.get_id = function() {
        var url = this.__serverurl+'/get_id/';
        return this.__call(url,arguments,'get_id');
    };
    
    // @access public
    oParent.set_id = function() {
        var url = this.__serverurl+'/set_id/';
        return this.__call(url,arguments,'set_id');
    };
    
    // @access public
    oParent.get_label = function() {
        var url = this.__serverurl+'/get_label/';
        return this.__call(url,arguments,'get_label');
    };
    
    // @access public
    oParent.set_label = function() {
        var url = this.__serverurl+'/set_label/';
        return this.__call(url,arguments,'set_label');
    };
    
    // @access public
    oParent.get_description = function() {
        var url = this.__serverurl+'/get_description/';
        return this.__call(url,arguments,'get_description');
    };
    
    // @access public
    oParent.set_description = function() {
        var url = this.__serverurl+'/set_description/';
        return this.__call(url,arguments,'set_description');
    };
    
    // @access public
    oParent.get_query = function() {
        var url = this.__serverurl+'/get_query/';
        return this.__call(url,arguments,'get_query');
    };
    
    // @access public
    oParent.set_query = function() {
        var url = this.__serverurl+'/set_query/';
        return this.__call(url,arguments,'set_query');
    };
    
    // @access public
    oParent.get_exploded_query = function() {
        var url = this.__serverurl+'/get_exploded_query/';
        return this.__call(url,arguments,'get_exploded_query');
    };
    
    // @access public
    oParent.connectedreportlist = function() {
        var url = this.__serverurl+'/connectedreportlist/';
        return this.__call(url,arguments,'connectedreportlist');
    };
    
    return oParent;
}


function menureport() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?menureport';
    
    oParent.__remoteClass = 'menureport';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.ordataobject = function() {
        var url = this.__serverurl+'/ordataobject/';
        return this.__call(url,arguments,'ordataobject');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.storage_defaults = function() {
        var url = this.__serverurl+'/storage_defaults/';
        return this.__call(url,arguments,'storage_defaults');
    };
    
    // @access public
    oParent.persist = function() {
        var url = this.__serverurl+'/persist/';
        return this.__call(url,arguments,'persist');
    };
    
    // @access public
    oParent.populate = function() {
        var url = this.__serverurl+'/populate/';
        return this.__call(url,arguments,'populate');
    };
    
    // @access public
    oParent.populatemetadata = function() {
        var url = this.__serverurl+'/populatemetadata/';
        return this.__call(url,arguments,'populatemetadata');
    };
    
    // @access public
    oParent.addmetahints = function() {
        var url = this.__serverurl+'/addmetahints/';
        return this.__call(url,arguments,'addmetahints');
    };
    
    // @access public
    oParent.populate_array = function() {
        var url = this.__serverurl+'/populate_array/';
        return this.__call(url,arguments,'populate_array');
    };
    
    // @access public
    oParent.factory = function() {
        var url = this.__serverurl+'/factory/';
        return this.__call(url,arguments,'factory');
    };
    
    // @access public
    oParent.factory_include = function() {
        var url = this.__serverurl+'/factory_include/';
        return this.__call(url,arguments,'factory_include');
    };
    
    // @access public
    oParent.exists = function() {
        var url = this.__serverurl+'/exists/';
        return this.__call(url,arguments,'exists');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.set = function() {
        var url = this.__serverurl+'/set/';
        return this.__call(url,arguments,'set');
    };
    
    // @access public
    oParent.getchecked = function() {
        var url = this.__serverurl+'/getchecked/';
        return this.__call(url,arguments,'getchecked');
    };
    
    // @access public
    oParent.drop = function() {
        var url = this.__serverurl+'/drop/';
        return this.__call(url,arguments,'drop');
    };
    
    // @access public
    oParent.tostring = function() {
        var url = this.__serverurl+'/tostring/';
        return this.__call(url,arguments,'tostring');
    };
    
    // @access public
    oParent.ispopulated = function() {
        var url = this.__serverurl+'/ispopulated/';
        return this.__call(url,arguments,'ispopulated');
    };
    
    // @access public
    oParent.getmenulist = function() {
        var url = this.__serverurl+'/getmenulist/';
        return this.__call(url,arguments,'getmenulist');
    };
    
    // @access public
    oParent.addmenuentry = function() {
        var url = this.__serverurl+'/addmenuentry/';
        return this.__call(url,arguments,'addmenuentry');
    };
    
    // @access public
    oParent.updatemenuentry = function() {
        var url = this.__serverurl+'/updatemenuentry/';
        return this.__call(url,arguments,'updatemenuentry');
    };
    
    // @access public
    oParent.deletemenuentry = function() {
        var url = this.__serverurl+'/deletemenuentry/';
        return this.__call(url,arguments,'deletemenuentry');
    };
    
    // @access public
    oParent.get_menu_report_id = function() {
        var url = this.__serverurl+'/get_menu_report_id/';
        return this.__call(url,arguments,'get_menu_report_id');
    };
    
    // @access public
    oParent.set_menu_report_id = function() {
        var url = this.__serverurl+'/set_menu_report_id/';
        return this.__call(url,arguments,'set_menu_report_id');
    };
    
    // @access public
    oParent.get_menu_id = function() {
        var url = this.__serverurl+'/get_menu_id/';
        return this.__call(url,arguments,'get_menu_id');
    };
    
    // @access public
    oParent.set_menu_id = function() {
        var url = this.__serverurl+'/set_menu_id/';
        return this.__call(url,arguments,'set_menu_id');
    };
    
    // @access public
    oParent.get_report_template_id = function() {
        var url = this.__serverurl+'/get_report_template_id/';
        return this.__call(url,arguments,'get_report_template_id');
    };
    
    // @access public
    oParent.set_report_template_id = function() {
        var url = this.__serverurl+'/set_report_template_id/';
        return this.__call(url,arguments,'set_report_template_id');
    };
    
    // @access public
    oParent.get_title = function() {
        var url = this.__serverurl+'/get_title/';
        return this.__call(url,arguments,'get_title');
    };
    
    // @access public
    oParent.set_title = function() {
        var url = this.__serverurl+'/set_title/';
        return this.__call(url,arguments,'set_title');
    };
    
    return oParent;
}


function menuform() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = 'http://localhost/clearhealth/jpspan_server.php?menuform';
    
    oParent.__remoteClass = 'menuform';
    
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
    
    // @access public
    oParent.ordataobject = function() {
        var url = this.__serverurl+'/ordataobject/';
        return this.__call(url,arguments,'ordataobject');
    };
    
    // @access public
    oParent.setup = function() {
        var url = this.__serverurl+'/setup/';
        return this.__call(url,arguments,'setup');
    };
    
    // @access public
    oParent.storage_defaults = function() {
        var url = this.__serverurl+'/storage_defaults/';
        return this.__call(url,arguments,'storage_defaults');
    };
    
    // @access public
    oParent.persist = function() {
        var url = this.__serverurl+'/persist/';
        return this.__call(url,arguments,'persist');
    };
    
    // @access public
    oParent.populate = function() {
        var url = this.__serverurl+'/populate/';
        return this.__call(url,arguments,'populate');
    };
    
    // @access public
    oParent.populatemetadata = function() {
        var url = this.__serverurl+'/populatemetadata/';
        return this.__call(url,arguments,'populatemetadata');
    };
    
    // @access public
    oParent.addmetahints = function() {
        var url = this.__serverurl+'/addmetahints/';
        return this.__call(url,arguments,'addmetahints');
    };
    
    // @access public
    oParent.populate_array = function() {
        var url = this.__serverurl+'/populate_array/';
        return this.__call(url,arguments,'populate_array');
    };
    
    // @access public
    oParent.factory = function() {
        var url = this.__serverurl+'/factory/';
        return this.__call(url,arguments,'factory');
    };
    
    // @access public
    oParent.factory_include = function() {
        var url = this.__serverurl+'/factory_include/';
        return this.__call(url,arguments,'factory_include');
    };
    
    // @access public
    oParent.exists = function() {
        var url = this.__serverurl+'/exists/';
        return this.__call(url,arguments,'exists');
    };
    
    // @access public
    oParent.get = function() {
        var url = this.__serverurl+'/get/';
        return this.__call(url,arguments,'get');
    };
    
    // @access public
    oParent.set = function() {
        var url = this.__serverurl+'/set/';
        return this.__call(url,arguments,'set');
    };
    
    // @access public
    oParent.getchecked = function() {
        var url = this.__serverurl+'/getchecked/';
        return this.__call(url,arguments,'getchecked');
    };
    
    // @access public
    oParent.drop = function() {
        var url = this.__serverurl+'/drop/';
        return this.__call(url,arguments,'drop');
    };
    
    // @access public
    oParent.tostring = function() {
        var url = this.__serverurl+'/tostring/';
        return this.__call(url,arguments,'tostring');
    };
    
    // @access public
    oParent.ispopulated = function() {
        var url = this.__serverurl+'/ispopulated/';
        return this.__call(url,arguments,'ispopulated');
    };
    
    // @access public
    oParent.getformlist = function() {
        var url = this.__serverurl+'/getformlist/';
        return this.__call(url,arguments,'getformlist');
    };
    
    // @access public
    oParent.addmenuentry = function() {
        var url = this.__serverurl+'/addmenuentry/';
        return this.__call(url,arguments,'addmenuentry');
    };
    
    // @access public
    oParent.updatemenuentry = function() {
        var url = this.__serverurl+'/updatemenuentry/';
        return this.__call(url,arguments,'updatemenuentry');
    };
    
    // @access public
    oParent.deletemenuentry = function() {
        var url = this.__serverurl+'/deletemenuentry/';
        return this.__call(url,arguments,'deletemenuentry');
    };
    
    // @access public
    oParent.get_menu_form_id = function() {
        var url = this.__serverurl+'/get_menu_form_id/';
        return this.__call(url,arguments,'get_menu_form_id');
    };
    
    // @access public
    oParent.set_menu_form_id = function() {
        var url = this.__serverurl+'/set_menu_form_id/';
        return this.__call(url,arguments,'set_menu_form_id');
    };
    
    // @access public
    oParent.get_menu_id = function() {
        var url = this.__serverurl+'/get_menu_id/';
        return this.__call(url,arguments,'get_menu_id');
    };
    
    // @access public
    oParent.set_menu_id = function() {
        var url = this.__serverurl+'/set_menu_id/';
        return this.__call(url,arguments,'set_menu_id');
    };
    
    // @access public
    oParent.get_form_id = function() {
        var url = this.__serverurl+'/get_form_id/';
        return this.__call(url,arguments,'get_form_id');
    };
    
    // @access public
    oParent.set_form_id = function() {
        var url = this.__serverurl+'/set_form_id/';
        return this.__call(url,arguments,'set_form_id');
    };
    
    // @access public
    oParent.get_title = function() {
        var url = this.__serverurl+'/get_title/';
        return this.__call(url,arguments,'get_title');
    };
    
    // @access public
    oParent.set_title = function() {
        var url = this.__serverurl+'/set_title/';
        return this.__call(url,arguments,'set_title');
    };
    
    return oParent;
}

