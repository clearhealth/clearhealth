/**@
* include 'request.js';
*/
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
