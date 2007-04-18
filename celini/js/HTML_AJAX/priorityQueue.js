/**
 * Priority queue
 *
 * @category   HTML
 * @package    AJAX
 * @author     Arpad Ray <arpad@php.net>
 * @copyright  2005 Arpad Ray
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
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