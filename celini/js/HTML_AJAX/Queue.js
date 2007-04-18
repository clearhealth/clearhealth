/**
 * Various processing queues, use when you want to control how multiple requests are made
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */

// Single Buffer queue with interval
// works by attempting to send a request every x miliseconds
// if an item is currently in the queue when a new item is added it will be replaced
// simple queue, just processes the request immediately
// the first request starts the interval timer
function HTML_AJAX_Queue_Interval_SingleBuffer(interval,singleOutstandingRequest) {
    this.interval = interval;
    if (singleOutstandingRequest) {
        this.singleOutstandingRequest = true;
    }
}
HTML_AJAX_Queue_Interval_SingleBuffer.prototype = {
    request: false,
    _intervalId: false,
    singleOutstandingRequest: false,
    client: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        if (!this._intervalId) {
            this.runInterval();
            this.start();
        }
    }, 
    start: function() {
        var self = this;
        this._intervalId = setInterval(function() { self.runInterval() },this.interval);
    },
    stop: function() {
        clearInterval(this._intervalId);
    },
    runInterval: function() {
        if (this.request) {
            if (this.singleOutstandingRequest && this.client) {
                this.client.abort();
            }
            this.client = HTML_AJAX.httpClient();
            this.client.request = this.request;
            this.request = false;
            this.client.makeRequest();
        }
    }
}

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

// Make a single request at once, canceling and currently outstanding requests when a new one is made
function HTML_AJAX_Queue_Single() {
}
HTML_AJAX_Queue_Single.prototype = {
    request: false,
    client: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        if (this.request) {
            if (this.client) {
                this.client.abort();
            }
            this.client = HTML_AJAX.httpClient();
            this.client.request = this.request;
            this.request = false;
            this.client.makeRequest();
        }
    }
}

/**
 * Priority queue
 *
 * @author     Arpad Ray <arpad@php.net>
 */
function HTML_AJAX_Queue_Priority_Item(item, time) {
    this.item = item;
    this.time = time;
}
HTML_AJAX_Queue_Priority_Item.prototype = {
    compareTo: function (other) {
        var ret = this.item.compareTo(other.item);
        if (ret == 0) {
            ret = this.time - other.time;
        }
        return ret;
    }
}

function HTML_AJAX_Queue_Priority_Simple(interval) {
    this.interval = interval;   
    this.idleMax = 10;            // keep the interval going with an empty queue for 10 intervals
    this.requestTimeout = 5;      // retry uncompleted requests after 5 seconds
    this.checkRetryChance = 0.1;  // check for uncompleted requests to retry on 10% of intervals
    this._intervalId = 0;
    this._requests = [];
    this._removed = [];
    this._len = 0;
    this._removedLen = 0;
    this._idle = 0;
}
HTML_AJAX_Queue_Priority_Simple.prototype = {
    isEmpty: function () {
        return this._len == 0;
    },
    addRequest: function (request) {
        request = new HTML_AJAX_Queue_Priority_Item(request, new Date().getTime());
        ++this._len;
        if (this.isEmpty()) {
            this._requests[0] = request;
            return;
        }
        for (i = 0; i < this._len - 1; i++) {
            if (request.compareTo(this._requests[i]) < 0) {
                this._requests.splice(i, 1, request, this._requests[i]);
                return;
            }
        }
        this._requests.push(request);
    },
    peek: function () {
        return (this.isEmpty() ? false : this._requests[0]);
    },
    requestComplete: function (request) {
        for (i = 0; i < this._removedLen; i++) {
            if (this._removed[i].item == request) {
                this._removed.splice(i, 1);
                --this._removedLen;
                out('removed from _removed');
                return true;
            }
        }
        return false;
    },
    processRequest: function() {
        if (!this._intervalId) {
            this._runInterval();
            this._start();
        }
        this._idle = 0;
    },
    _runInterval: function() {
        if (Math.random() < this.checkRetryChance) {
            this._doRetries();
        }
        if (this.isEmpty()) {
            if (++this._idle > this.idleMax) {
                this._stop();
            }
            return;
        }
        var client = HTML_AJAX.httpClient();
        if (!client) {
            return;
        }
        var request = this.peek();
        if (!request) {
            this._requests.splice(0, 1);
            return;
        }
        client.request = request.item;
        client.makeRequest();
        this._requests.splice(0, 1);
        --this._len;
        this._removed[this._removedLen++] = new HTML_AJAX_Queue_Priority_Item(request, new Date().getTime());
    },
    _doRetries: function () {
        for (i = 0; i < this._removedLen; i++) {
            if (this._removed[i].time + this._requestTimeout < new Date().getTime()) {
                this.addRequest(request.item);
                this._removed.splice(i, 1);
                --this._removedLen;
                return true;
            }
        }
    },
    _start: function() {
        var self = this;
        this._intervalId = setInterval(function() { self._runInterval() }, this.interval);
    },
    _stop: function() {
        clearInterval(this._intervalId);
        this._intervalId = 0;
    }
};
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
