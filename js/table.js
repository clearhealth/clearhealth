// JS Table renderer for Cellini Datasources
// Uses the scrollable interface
// Setup function to render a table, only handles the rows, header and such come from the static html renderer

function clniTable(tableId,dataObject) {
	this.tableId = tableId;
	this.dataObject = dataObject;
	this.windowSize = 30;
	this.prefetchSize = 40;

	this.data = Array();
	this.dataRows = -1;

	this.windowRow = 0;
	
	this.callback = new clniTableDataHandler(this);

	this.tableBody = document.getElementById(tableId).getElementsByTagName('tbody').item(0);
	this.renderMap = this.dataObject.getrendermap();

	this.indexCol = 1;

	// error handling
	this.dataObject.clientErrorFunc = function(e) {
		if (e.code == 1001) {
			// do nothing were just prefetching will catch it latter
		}
		else {
			alert('[Client Error] '+e.name+': '+e.message);
		}
	}

	this.meta = this.dataObject.getmeta();
	this.editableMap = this.meta.editableMap;
	this.updateKey = this.meta._updateKey;

	this.tableBody.grid = this;
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

	var end = (this.windowRow + this.windowSize);
	if (end > this.dataRows) {
		end = this.dataRows;
	}
	for(var i = this.windowRow; i < end; i++) {
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

			if (this.editableMap[field]) {
				if (this.meta.editFunc && this.meta.editFunc[field]) {
					cell.onclick = eval(this.meta.editFunc[field]);
				}
				else {
					cell.onclick = makeEditable;
				}
				cell.style.border = "solid 1px black";

				if (this.meta.passAlong) {
					cell.passAlong = new Object();
					for(f in this.meta.passAlong) {
						cell.passAlong[f] = this.data[rowNum][f];
					}
				}

				cell.rowNum = rowNum;
				cell.field = field;
				cell.updateKey = this.data[rowNum][this.updateKey];
			}
			if (this.data[rowNum][field] == null) {
				this.data[rowNum][field] = " ";
			}
			cell.appendChild(document.createTextNode(this.data[rowNum][field]));
			cell.key = field;

			if (this.meta.filterFunc && this.meta.filterFunc[field]) {
				var tmp = eval(this.meta.filterFunc[field]);
				tmp(cell);
			}
			row.appendChild(cell);
		}
	}
	else {
		//alert('waiting on data for row:' + row.index);
		row.className = "waiting";
		for(var i = 0; i < this.renderMap.length; i++) { 
			var field = this.renderMap[i];
			var cell = document.createElement('td');
			cell.key = field;
			row.appendChild(cell);
		}
		//this.fillFetch();
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
	//alert('updateRow: '+row.index);
	var nrow = document.createElement('tr');

	this._renderRow(row.index,nrow);
	
	this.tableBody.replaceChild(nrow,row);
}

clniTable.prototype.fetchRow = function(rowNum) {
	if(!this.data[rowNum]) {
		if (!this.dataObject.__client.callInProgress()) {
			this.dataObject.Sync();
			this.data[rowNum] = this.dataObject.fetchrow(rowNum);
		}
	}
}

// fixme: i have a bug where i don't update 1 row
clniTable.prototype.fillFetch = function() {
		document.getElementById(this.tableBody.parentNode.id+"_waiting").className = "waitingActive";

		for(var i = 0; i < this.tableBody.rows.length; i++) {
			var row = this.tableBody.rows.item(i);
			if (row.className == "waiting") {
				if (this.data[row.index]) {
					this.updateRow(row);
				}
				else {
					if (this.__callState == "async") {
						while(this.dataObject.__client.callInProgress()) {
							this.dataObject.__client.abort(this.dataObject.__client);
						}
					}
					//alert('bulkFetch: '+row.index);
					this.bulkFetch(row.index,this.windowSize);
					this.updateRow(row);
				}
			}
		}

		document.getElementById(this.tableBody.parentNode.id+"_waiting").className = "waiting";
}

/**
 * This doesn't perform well do to the large number of http calls
 */
clniTable.prototype.prefetchRow = function(rowNum) {
	if (rowNum < 0) {
		rowNum = 0;
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
	if (rowNum < 0) {
		rowNum = 0;
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
	this.dataObject.Sync();
	d = this.dataObject.fetchbulk(start,rows);
	if (d) {
		for(var i = 0; i < d.length; i++) {
			this.data[start+i] = d[i];
		}
	}
}
clniTable.prototype.filter = function() {
	this.dataObject.Sync();

	// clear data cache
	this.data = Array();
	this.dataRows = -1;

	// remove current table rows
	var size = this.tableBody.rows.length;
	for(var i = 0; i < size; i++) {
		this.dropRow(0);
	}

	//document.getElementById('debug').value = this.dataObject.getsql();

	// redraw table
	var status = this.render();
}

clniTable.prototype.addFilter = function(field,value) {
	this.dataObject.Sync();
	
	var status = this.dataObject.addfilter(field,value);
}
clniTable.prototype.dropFilter = function(field) {
	this.dataObject.Sync();
	
	var status = this.dataObject.dropfilter(field);
}

clniTable.prototype.store = function(cell) {
	this.dataObject.Sync();
	var status = this.dataObject.updatefield(cell.updateKey, cell.field, cell.firstChild.value, cell.passAlong);
	this.data[cell.rowNum][cell.field] = cell.firstChild.value;
}
clniTable.prototype.storeSimple = function(cell,value) {
	this.dataObject.Sync();
	var status = this.dataObject.updatefield(cell.updateKey,cell.field,value,cell.passAlong);
	this.data[cell.rowNum][cell.field] = value;
}

clniTable.prototype.moveWindow = function(newRow) {
	if (newRow >= 0 && newRow < this.dataRows) {
		// if were scrolling down append rows to get back to window size
		if (newRow > this.windowRow) {
			// drop rows from the front of the window
			for(var i = this.windowRow; i < newRow; i++) {
				this.dropRow(0);
			}

			// append rows to the back, doing a fetch call to make sure the data exists
			for(var i = (this.windowRow + this.windowSize); i < (newRow + this.windowSize); i++) {
				//this.fetchRow(i);
				this.appendRow(i);
			}
		}
		else if (newRow < this.windowRow) {
			// prepend rows onto the front
			for(var i = this.windowRow-1; i >= newRow; i--) {
				//this.fetchRow(i);
				this.prependRow(i);
			}

			// drop rows from the back of the window
			for(var i = (this.windowRow+this.windowSize); i > (newRow+this.windowSize); i--) {
				this.dropRow(this.windowSize);
			}
		}

		//this.fetchRow(newRow);

		this.windowRow = newRow;
	}
}

clniTable.prototype.up = function() {
	var newRow = this.windowRow-20;
	if (newRow < 0) {
		newRow = 0;
	}
	this.prefetch(newRow-this.prefetchSize); 
	this.moveWindow(newRow);
}

clniTable.prototype.down = function() {
	var newRow = this.windowRow+20;
	this.prefetch(this.windowRow+this.windowSize+this.prefetchSize); 
	this.moveWindow(newRow);
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

// right now were just making stuff input boxes
// you can only have 1 editable element at a time
// todo: should we update the data for the field when we make it editable
function makeEditable() {
	if (!this.editing) {
		closeEditable(this.parentNode.parentNode);
		input = document.createElement('input');
		input.type = 'text';
		input.className = 'gridInput';
		input.value = this.firstChild.nodeValue;

		this.removeChild(this.firstChild);
		this.appendChild(input);
		this.parentNode.editing = true;
		this.editing = true;
		input.focus();
	}
}

// like makeEditable only makes things a toogle js element instead of an input box
// you can have many items that have a makeToggle onClick, clicking actually does the change
function makeToggle() {
	if (this.selected) {
		this.selected = 0;
		this.style.backgroundColor = "";
	}
	else {
		this.selected = 1;
		this.style.backgroundColor = "green";
	}
	//(updateKey,field,value,column)
	this.parentNode.parentNode.grid.storeSimple(this,this.selected);
}

function toggleFilter(cell) {
	var value = cell.firstChild.nodeValue;
	if (value == 1) {
		cell.selected = 1;
		cell.style.backgroundColor = "green";
		cell.removeChild(cell.firstChild);
	}
	else if (value == 0) {
		cell.selected = 0;
		cell.style.backgroundColor = "";
		cell.removeChild(cell.firstChild);
	}
}

function closeEditable(tableBody) {
	for(var i =0; i < tableBody.rows.length; i++) {
		if (tableBody.rows.item(i).editing) {
			tableBody.rows.item(i).editing = false;
			for(var c = 0; c < tableBody.rows.item(i).cells.length; c++) {
				if (tableBody.rows.item(i).cells[c].editing) {
					cell = tableBody.rows.item(i).cells[c];
					cell.editing = false;

					cell.appendChild(document.createTextNode(cell.firstChild.value));
					tableBody.grid.store(cell);
					cell.removeChild(cell.firstChild);
					
				}
			}
		}
	}
}

var relateSource = false;
var storedRelations = new Object();

// put me somewhere else
function relateFilter(cell) {
}

function makeRelate() {
	// check if were the current source node, if so turn us off
	if (this.source) {
		_resetRelate(this);
	}
	// check if someone else in our table is the current source
	else if (this.parentNode.parentNode.relateSource) {
		storeRelate();
		_resetRelate(this.parentNode.parentNode.relateSource);
		_newRelate(this);
	}
	// check if someone else is the source 
	else if (relateSource) {
		// we have a source and its not in our table so relate us to it
		if (this.related) {
			dropRelated(this);
		}
		else {
			addRelated(this);
		}
	}
	// make us the source 
	else {
		_newRelate(this);
	}
}

function dropRelated(cell) {
	var code_id = cell.passAlong.code_id;
	relateSource.related[code_id] = false;
	relateSource.text[code_id] = cell.passAlong.code + ": " + cell.passAlong.code_text;
	relateSource.relatedCount--;
	cell.style.backgroundColor = "";
	cell.related = false;

	_updateCurrentRelateMsg();
}
	
function addRelated(cell) {
	var code_id = cell.passAlong.code_id;
	relateSource.related[code_id] = true;
	relateSource.text[code_id] = cell.passAlong.code + ": " + cell.passAlong.code_text;
	relateSource.relatedCount++;
	cell.style.backgroundColor = "green";
	cell.related = true;

	_updateCurrentRelateMsg();
}
	
function _updateCurrentRelateMsg() {
	msg = document.getElementById('currentRelate');
	msg.removeChild(msg.firstChild);
	node = _buildMsg(relateSource);
	msg.appendChild(node);
}

function _buildMsg(data) {
	var node = document.createElement("div");
	if (data.myText) {
		node.appendChild(document.createTextNode(data.myText));
	}
	var sep = "";
	for(var code in data.related) {
		if (data.related[code]) {
			node.appendChild(document.createTextNode(sep+data.text[code]));
			sep = ", ";
		}
	}
	return node;
}

function _resetRelate(cell) {
	cell.source = false;
	relateSource = false;
	cell.parentNode.parentNode.relateSource = false;
	cell.style.backgroundColor = "";
	cell.related = false;
}
function _newRelate(cell) {
	cell.source = true;
	cell.style.backgroundColor = "blue";
	cell.related = Object();
	cell.relatedCount = 0;
	cell.text = Object();
	cell.myText = cell.passAlong.code + ": " + cell.passAlong.code_text + " => ";
	cell.code_id = cell.passAlong.code_id;
	relateSource = cell;
	cell.parentNode.parentNode.relateSource = cell;
	_updateCurrentRelateMsg();
}

function storeRelate() {
	if (relateSource.relatedCount > 0) {
		relateSource.fullText = _buildMsg(relateSource);
		storedRelations[relateSource.code_id] = relateSource;
		drawStoredRelations();
		clearRelate();
	}
}

function drawStoredRelations() {
	target = document.getElementById('storedCodes');
	target.removeChild(target.firstChild);

	surface = document.createElement('div');
	for(var code_id in storedRelations) {
		surface.appendChild(storedRelations[code_id].fullText);
	}
	target.appendChild(surface);
}

function clearRelate() {
	var loop = function(tb) {
		for(var r = 0; r < tb.rows.length; r++) {
			var row = tb.rows.item(r);
			
			for(var c = 0; c < row.cells.length; c++) {
				var cell = row.cells.item(c);
				if (cell.field == "relate") {
					_resetRelate(cell);
				}
			}
		}
	}
	loop(document.getElementById('gicd').getElementsByTagName('tbody').item(0));
	loop(document.getElementById('gcpt').getElementsByTagName('tbody').item(0));
	_updateCurrentRelateMsg();
}
