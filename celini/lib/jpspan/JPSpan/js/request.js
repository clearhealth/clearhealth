/**@
* include 'httpclient.js';
*/
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
