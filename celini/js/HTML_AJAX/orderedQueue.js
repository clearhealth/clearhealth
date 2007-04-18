// Requests return in the same order they were called
// this helps handle high latency situations
function HTML_AJAX_Queue_Ordered() { }
HTML_AJAX_Queue_Ordered.prototype = {
    request: false,
    order: 0,
    current: 0,
    callbacks: {},
    interned: {},
    addRequest: function(request) {
        request.order = this.order;
        this.request = request;
        this.callbacks[this.order] = this.request.callback;
        var self = this;
        this.request.callback = function(result) {
            self.processCallback(result,request.order);
        } 
    },
    processRequest: function() {
        var client = HTML_AJAX.httpClient();
        client.request = this.request;
        client.makeRequest();
        this.order++;
    },
    requestComplete: function(request,e) {
        // something when wrong with the request lets stop waiting for it
        if (e) {
            this.current++;
        }
    },
    processCallback: function(result,order) {
        if (order == this.current) {
            this.callbacks[order](result);
            this.current++;
        }
        else {
            this.interned[order] = result;
            if (this.interned[this.current]) {
                this.callbacks[this.current](this.interned[this.current]);
                this.current++;
            }
        }
    } 
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
