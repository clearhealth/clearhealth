// $Id: mock.js,v 1.6 2004/11/19 21:56:47 harryf Exp $
// Mock Object implementation in Javascript
// See: http://jpspan.sourceforge.net/wiki/doku.php?id=javascript:mock

// Creates mock objects given function reference
function JPSpan_Util_MockCreate(Obj) {

    var oMock = new JPSpan_Util_MockObject();
    
    for (prop in Obj.prototype) {
    
        if ( typeof Obj.prototype[prop] == 'function' && prop.charAt(0) != '_' ) {
            oMock.addMethod(prop);         
        };
    };
    
    return oMock;
    
};

function JPSpan_Util_MockObject(){
    this.__methods = new Object();
}

JPSpan_Util_MockObject.prototype = {

    __methods: null,
    
    __registerMethod: function(method) {
        var Method = new Object();
        Method.returnValue = function(){};
        Method.calls = new Array();
        this.__methods[method] = Method;
    },

    
    // Add a method to the mock object
    addMethod: function(method) {
        this.__registerMethod(method);
        this[method] = function() {
            this.__methods[method].calls.push(arguments);
            return this.__methods[method].returnValue();
        }
    },



    // Get the number of time a method was called
    getCallCount: function(method) {
        if ( this.__methods[method] ) {
            return this.__methods[method].calls.length;
        } else {
            throw "Method "+method+" not found";
        }
    },

    

    // Get the arguments from the last call to this method

    getLastCallArgs: function(method) {
        if ( this.__methods[method] ) {
            return this.__methods[method].calls.pop();
        } else {
            throw "Method "+method+" not found";
        }
    },

    

    // Get the arguments from the call with the specified index
    getCallArgsAt: function(method, index) {
        if ( this.__methods[method] ) {
            if ( this.__methods[method].calls[index] ) {
                return this.__methods[method].calls[index];
            } else {
                throw "Call Index "+index+" not found for method "+method;
            }
        } else {
            throw "Method "+method+" not found";
        }
    },

    

    // Set the return value for the named method

    setReturnValue: function(method, value) {
        if ( this.__methods[method] ) {
            this.__methods[method].returnValue = function() {
                return value;
            }
        } else {
            throw "Method "+method+" not found";
        }
    },
    
    // Return an exception with the provided message

    setReturnException: function(method, message) {
        if ( this.__methods[method] ) {
            this.__methods[method].returnValue = function() {
                throw message;
            }
        } else {
            throw "Method "+method+" not found";
        }

    }

};
