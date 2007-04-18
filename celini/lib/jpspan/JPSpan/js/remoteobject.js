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
