/**
 * Class that contains everything needed to make a request
 * This includes:
 *    The url were calling
 *    If were calling a remote method, the class and method name
 *    The payload, unserialized
 *    The timeout for async calls
 *    The callback method
 *    Optional event handlers: onError, Load, Send
 *    A serializer instance
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for author/license details
 */
function HTML_AJAX_Request(serializer) {
    this.serializer = serializer;
}
HTML_AJAX_Request.prototype = {

    // Instance of a serializer
    serializer: null,
    
    // Is this an async request
    isAsync: false,

    // HTTP verb
    requestType: 'POST',
    
    // The actual URL the request is sent to
    requestUrl: '',
    
    // Remote Class
    className: null,

    // Remote Method
    methodName: null,

    // Timeout in milliseconds for requests
    timeout: 20000,

    // unserialized data, for rpc calls use add args, to send raw data just set this directly
    args: null,

    // async callback method
    callback: null,

    // Queue to push this request too
    queue: 'default',
    
    // default priority
    priority: 0,
    
    // a hash of headers to add to add to this request
    customHeaders: {},

    // true if this request will be sent using iframes
    iframe: false,
    
    // is this a grab request? if so we need to proxy for iframes
    grab: false,
    
    // true if this request should expect a multipart response
    multipart: false,

    // remote callback
    phpCallback: false,
    
    /**
     * Add an argument for the remote method
     * @param string argument name
     * @param mixed value
     * @return void
     * @throws Error code 1004
     */
    addArg: function(name, value) 
    {
        if ( !this.args ) {
            this.args = [];
        }
        if (!/[^a-zA-Z_0-9]/.test(name) ) {
            this.args[name] = value;
        } else {
            throw new Error('Invalid parameter name ('+name+')');
        }
    },

    /**
     * Get the payload in a serialized manner
     */
    getSerializedPayload: function() {
        return this.serializer.serialize(this.args);
    },

    /**
     * Get the content type
     */
    getContentType: function() {
        return this.serializer.contentType;
    },

    /**
     * Get the complete url, adding in any needed get params for rpc
     */
    completeUrl: function() {
        if (this.className || this.methodName) {
            this.addGet('c', this.className);
            this.addGet('m', this.methodName);
        }
        if (this.phpCallback) {
            if (HTML_AJAX_Util.getType(this.phpCallback) == 'array') {
                this.phpCallback = this.phpCallback.join('.');
            }
            this.addGet('cb', this.phpCallback);
        }
        if (this.multipart) {
            this.addGet('multipart', '1');
        }
        return this.requestUrl;
    },
    
    /**
     * Compare to another request by priority
     */
    compareTo: function(other) {
        if (this.priority == other.priority) {
            return 0;
        }
        return (this.priority > other.priority ? 1 : -1);
    },

    /**
     * Add a GET argument
     */
    addGet: function(name, value) {
        var url = new String(this.requestUrl);
        url += (url.indexOf('?') < 0 ? '?' : '&') + escape(name) + '=' + escape(value);
        this.requestUrl = url;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
