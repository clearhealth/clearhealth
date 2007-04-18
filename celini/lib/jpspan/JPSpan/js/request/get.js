/**@
* include 'request.js';
*/
// @version $Id: get.js,v 1.4 2004/11/16 22:30:52 harryf Exp $

// For building HTTP GET requests
function JPSpan_Request_Get(encoder) {

    var oParent = new JPSpan_Request(encoder);
    
    // Builds the URL encoded request string: called from this.prepare()
    // @todo should be more careful when in respect to the existing server URL
    // @protected
    // @throws Error code 1006
    oParent.build = function() {
        var uri = '';
        var sep = '';
    
        for ( var argName in this.args ) {
            try {
                uri += sep + argName + '=';
                uri += encodeURIComponent(this.encoder.encode(this.args[argName]));
            } catch (e) {
                throw JPSpan_Client_Error(e, 1006);
            }
            sep = '&';
        };
        
        this.requesturl = this.serverurl;
        
        if ( uri.length != '' ) {
        
            // Need to analyse url more carefully...
            if ( this.serverurl.lastIndexOf('?') == -1 ) {
                this.requesturl += '?'+uri;
            } else {
                this.requesturl += '&'+uri;
            };
    
        };
    };
    
    // Called from XmlHttpClient to prepare the XmlHttpRequest object
    // @param XmlHttpRequest
    // @protected
    // @throws Error codes 1005 and 1007 
    oParent.prepare = function (http) {

        this.http = http;
        
        this.build();
    
        switch ( this.type ) {
            case 'async':
                try {
                    this.http.open('GET',this.requesturl,true);
                } catch (e) {
                    throw JPSpan_Client_Error(new Error(e),1007);
                };
            break;
            case 'sync':
                try {
                    this.http.open('GET',this.requesturl,false);
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
    };
    
    // Send the request
    // @protected
    oParent.send = function() {
        this.http.send(null);
    };
    
    return oParent;
};

