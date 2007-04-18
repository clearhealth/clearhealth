HTML_AJAX_Client_Pool = function(maxClients, startingClients)
{
    this.maxClients = maxClients;
    this._clients = [];
    this._len = 0;
    while (--startingClients > 0) {
        this.addClient();
    }
}

HTML_AJAX_Client_Pool.prototype = {
    isEmpty: function()
    {
        return this._len == 0;
    },
    addClient: function()
    {
        if (this.maxClients != 0 && this._len > this.maxClients) {
            return false;
        }
        var key = this._len++;
        this._clients[key] = new HTML_AJAX_HttpClient();
        return this._clients[key];
    },
    getClient: function ()
    {
        for (var i = 0; i < this._len; i++) {
            if (!this._clients[i].callInProgress() && this._clients[i].callbackComplete) {
                return this._clients[i];
            }
        }
        var client = this.addClient();
        if (client) {
            return client;
        }
        return false;
    },
    removeClient: function (client)
    {
        for (var i = 0; i < this._len; i++) {
            if (!this._clients[i] == client) {
                this._clients.splice(i, 1);
                return true;
            }
        }
        return false;
    },
    clear: function ()
    {
        this._clients = [];
        this._len = 0;
    }
};

// create a default client pool with unlimited clients
HTML_AJAX.clientPools['default'] = new HTML_AJAX_Client_Pool(0);
