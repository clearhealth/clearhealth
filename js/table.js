// JS Table renderer for Cellini Datasources
// Uses the scrollable interface
// Setup function to render a table, only handles the rows, header and such come from the static html renderer

function clniTable(tableId,dataObject) {
	this.tableId = tableId;
	this.dataObject = dataObject;
	this.windowSize = 30;
	this.prefetchSize = 10;

	this.data = Array();
	this.firstData = 0;
	this.dataRows = -1;

	this.windowRow = 0;
	
	this.callback = new clniTableDataHandler(this);

	this.tableBody = document.getElementById(tableId).getElementsByTagName('tbody').item(0);
	this.renderMap = this.dataObject.getrendermap();

	this.indexCol = 1;

	this.waitingForData = 0;

	// error handling
	this.dataObject.clientErrorFunc = function(e) {
		if (e.code == 1001) {
			// do nothing were just prefetching will catch it latter
		}
		else {
			alert('[Client Error] '+e.name+': '+e.message);
		}
	}
}

clniTable.prototype.render = function() {
	// get the # of total rows where dealing with
	if (this.dataRows == -1) {
		this.dataRows = this.numRows();
	}

	// fetch the data we need, using the bulk api
	i = this.windowRow - this.prefetchSize; 
	if (i < 0) {
		i = 0;
	}
	this.bulkFetch(i,this.windowSize+this.prefetchSize);

	for(var i = this.windowRow; i < (this.windowRow + this.windowSize); i++) {
		this.appendRow(i);
	}
}

clniTable.prototype._renderRow = function(rowNum,row) {
	if (this.indexCol) {
		var cell = document.createElement('td');
		cell.appendChild(document.createTextNode( (rowNum+1) ));
		row.appendChild(cell);
	}
	if (this.data[rowNum]) {
		for(var i = 0; i < this.renderMap.length; i++) { 
			var field = this.renderMap[i];
			var cell = document.createElement('td');
			cell.appendChild(document.createTextNode(this.data[rowNum][field]));
			cell.key = field;
			row.appendChild(cell);
		}
	}
	else {
		row.className = "waiting";
		for(var i = 0; i < this.renderMap.length; i++) { 
			var field = this.renderMap[i];
			var cell = document.createElement('td');
			cell.key = field;
			row.appendChild(cell);
		}
	}
}

clniTable.prototype.dropRow = function(rowIndex) {
	row = this.tableBody.getElementsByTagName('tr').item(rowIndex);
	this.tableBody.removeChild(row);
}

clniTable.prototype.appendRow = function(rowNum) {
	var row = document.createElement('tr');
	row.index = rowNum;

	this._renderRow(rowNum,row);

	this.tableBody.appendChild(row);
}

clniTable.prototype.prependRow = function(rowNum) {
	var row = document.createElement('tr');
	row.index = rowNum;

	this._renderRow(rowNum,row);

	this.tableBody.insertBefore(row,this.tableBody.rows.item(0));
}

clniTable.prototype.updateRow = function(row) {
	var nrow = document.createElement('tr');

	this._renderRow(row.index,nrow);
	
	this.tableBody.replaceChild(nrow,row);
}

clniTable.prototype.fetchRow = function(rowNum) {
	if(!this.data[rowNum]) {
		this.dataObject.Sync();
		this.data[rowNum] = this.dataObject.fetchrow(rowNum);

		this.waitingForData++;
	}
}

clniTable.prototype.fillFetch = function() {
		document.getElementById(this.tableBody.parentNode.id+"_waiting").className = "waitingActive";

		for(var i = 0; i < this.tableBody.rows.length; i++) {
			var row = this.tableBody.rows.item(i);
			if (row.className == "waiting") {
				while(this.dataObject.__client.callInProgress()) {
					this.dataObject.__client.abort(this.dataObject.__client);
				}
				this.fetchRow(row.index);
				this.updateRow(row);
			}
		}

		this.waitingForData = 0;
		document.getElementById(this.tableBody.parentNode.id+"_waiting").className = "waiting";
}

/**
 * This doesn't perform well do to the large number of http calls
 */
clniTable.prototype.prefetchRow = function(rowNum) {
	if (rowNum < 0) {
		rowNum = 0;
	}
	if (this.waitingForData > 2) {
		this.fillFetch();
	}
	if (!this.data[rowNum]) {
		this.callback.prefetchRow = rowNum;
		this.dataObject.Async(this.callback);
		this.data[rowNum] = this.dataObject.fetchrow(rowNum);
	}
}

/**
 * does prefetch in bulk
 */
clniTable.prototype.prefetch = function(rowNum) {
	if (this.waitingForData > 0) {
		this.fillFetch();
	}
	if (!this.data[rowNum]) {
		this.callback.prefetchRow = rowNum;
		this.dataObject.Async(this.callback);
		this.data[rowNum] = this.dataObject.fetchbulk(rowNum,this.prefetchSize);
	}
}

clniTable.prototype.numRows = function() {
	this.dataObject.Sync();
	return this.dataObject.numrows();
}

clniTable.prototype.bulkFetch = function(start,rows) {
	d = this.dataObject.fetchbulk(start,rows);
	for(var i = 0; i < d.length; i++) {
		this.data[start+i] = d[i];
	}
}

clniTable.prototype.moveWindow = function(newRow) {
	if (newRow >= 0 && newRow < this.dataRows) {
		// if were scrolling down append rows to get back to window size
		if (newRow > this.windowRow) {
			// drop rows from the front of the window
			for(var i = this.windowRow; i < newRow; i++) {
				this.dropRow(i-this.windowRow);
			}

			// append rows to the back, doing a fetch call to make sure the data exists
			for(var i = (this.windowRow + this.windowSize); i < (newRow + this.windowSize); i++) {
				this.fetchRow(i);
				this.appendRow(i);
			}
		}
		else if (newRow < this.windowRow) {
			// prepend rows onto the front
			for(var i = newRow; i < this.windowRow; i++) {
				this.fetchRow(i);
				this.prependRow(i);
			}

			// drop rows from the back of the window
			for(var i = (this.windowRow+this.windowSize); i > (newRow+this.windowSize); i--) {
				this.dropRow((i-this.windowRow));
			}
		}

		this.fetchRow(newRow);

		this.windowRow = newRow;
	}
}

clniTable.prototype.up = function() {
	if (this.windowRow % 3 == 0) {
		this.prefetch(this.windowRow-this.prefetchSize); 
	}
	this.moveWindow(this.windowRow-1);
}

clniTable.prototype.down = function() {
	// prefetch the next group
	// we only prefetch every 3 rows to limit traffic
	if (this.windowRow % 3 == 0) {
		this.prefetch(this.windowRow+this.windowSize+this.prefetchSize); 
	}
	this.moveWindow(this.windowRow+1);
}

function clniTableDataHandler(parent) {
	this.prefetchRow = -1;
	this.parent = parent;
}
clniTableDataHandler.prototype.fetchrow = function(result) {
	this.parent.data[this.prefetchRow] = result;
}
clniTableDataHandler.prototype.fetchbulk = function(result) {
	for(var i =0; i < result.length; i++) {
		if (!this.parent.data[this.prefetchRow+i]) {
			this.parent.data[this.prefetchRow+i] = result[i];
		}
	}
}
