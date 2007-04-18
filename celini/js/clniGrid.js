function clniDatasource(remote,load) {
	this.remote = remote;

	this.callback = {
		parent: this,
		fetchBulk: function(result) {
			this.parent._loadIntoCache(result);
		},
		setSort: function(result) {
			this.parent.block = false;
		},
		setColumnWidths: function(result) {
		},
		// see the query used, useful for debugging
		preview: function(result) {
			alert(result);
		}
	}

	this.remote.Async(this.callback);

	this.primaryKey = load.primaryKey;
	this.numRows = load.numRows;
	this.renderMap = load.map;
	this.sort = load.sort;
	this.widths = load.widths;
	this._loadIntoCache(load.data);
}
clniDatasource.prototype = {
	primaryKey: false,
	remote: false,
	numRows: 0,
	fetchSize: 10,
	map: {},
	sort: {},
	widths: {},
	// this is used to cause data for index calls to be delayed and immediatly return _loadingRow, no other remote calls will happen while its true
	block: false,
	cache: {},
	lookup: {},
	fetchInProgress: {},
	dataForIndex: function(index) {
		if (this.block) {
			this.needData(index,1);
			return this._loadingRow(index);
		}
		if (this._cacheCheck(index)) {
			return this._cacheRead(index);
		}
		return this._loadingRow(index);
	},
	// let the ds know your going to be needing data in this area in the future
	// todo figure out when we just need new lookup data and not new data lines
	needData: function(start,rows) {
		// see if we have any of the data
		var fetch = start;
		if (rows > 0) {
			for(var i = start; i < start+rows;i++) {
				if (!this._cacheCheck(i)) {
					fetch = i;
					break;
				}
			}
		}
		else {
			for(var i = start; i > start-this.fetchSize;i--) {
				if (!this._cacheCheck(i)) {
					fetch = i;
				}
			}
		}
		this.fetchData(fetch);

	},
	// fetch some data
	fetchData: function(start) {
		if (!this.fetchInProgress[start]) {
			this.fetchInProgress[start] = true;
			this.remote.fetchBulk(start,this.fetchSize);
		}
	},
	// sort the data
	setSort: function(column,direction) {
		// sorting doesn't kill the cache but it kills the lookup
		this.lookup = {};
		this.block = true;
		this.remote.setSort(column,direction);
	},
	// store widths of columns on the server (ignores the blocking flag)
	setColumnWidths: function(widths) {
		this.remote.setColumnWidths(widths);
	},
	_loadIntoCache: function(data) {
		for(var i = 0; i < data.length; i++) {
			data[i]._id = data[i][this.primaryKey];
			data[i]._index = (data.start+i);
			this.cache[data[i][this.primaryKey]] = data[i];
			this.lookup[(data.start+i)] = data[i][this.primaryKey];
		}
		if (this.fetchInProgress[data.start]) {
			delete this.fetchInProgress[data.start];
			this.delayedData(data.start);
		}
	},
	_idFromIndex: function(index) {
		if (this.lookup[index]) {
			return this.lookup[index];
		}
		return false;
	},
	_cacheCheck: function(index) {
		if (this.cache[this._idFromIndex(index)]) {
			return true;
		}
		return false;
	},
	_cacheRead: function(index) {
		return this.cache[this._idFromIndex(index)];
	},
	_loadingRow: function(index) {
		var data = new Object();
		data._loading = true;
		data._id = index;
		return data;
	},
	delayedData: function(start) {
	}
}

function clniGrid(ds,options) {
	for(var i in options) {
		this[i] = options[i];
	}
	this.ds = ds;
	this.ds.fetchSize = this.rows*2;
	var self = this;
	this.ds.delayedData = function() { self.renderLoading(); }

	this._loadTemplate();
	this.render();

	document.getElementById(this.tableId).style.position = 'relative';
	this._setupHeader();
}

clniGrid.prototype = {
	tableId: false,
	templateId: false,
	scrollBar: false,
	templates: {},
	rows: 0,
	currentRow: 0,
	ds: false,
	imagePath: '/clearhealth/index.php/images',
	images: {DESC:'/stock/s_desc.png',ASC:'/stock/s_asc.png'},
	renderRow: function(data) {
		if (data._loading) {
			var row = this.templates.loading.cloneNode(true);
			row.colspan = this.ds.renderMap.length;
			row._id = data._id;
			row._loading = true;
		}
		else {
			var row = this.templates.normal.cloneNode(true);
			if (data._index %2 == 1) {
				row.className = 'alt';
			}
			row._id = data._id;
			var tds = row.getElementsByTagName('td');
			for(var i = 0; i < tds.length; i++) {
				tds[i].innerHTML = tds[i].innerHTML.replace(/{\$([a-zA-Z0-9_]+)}/g,function(s,result) {return data[result]; } );
			}
		}
		return row;
	},
	render: function() {
		var height = document.getElementById(this.tableId).clientHeight;
		var tbody = document.getElementById(this.tableId).tBodies[0];
		for(var i = this.currentRow; i < this.currentRow + this.rows; i++) {
			tbody.appendChild(this.renderRow(this.ds.dataForIndex(i)));
		}
		if (this.scrollBar && document.getElementById(this.tableId).clientHeight != height) {
			this.scrollBar.positionBar();
		}
	},
	renderLoading: function() {
		var height = document.getElementById(this.tableId).clientHeight;
		var tbody = document.getElementById(this.tableId).tBodies[0];
		for(var i = 0; i < tbody.rows.length; i++) {
			if (tbody.rows[i]._loading) {
				tbody.replaceChild(this.renderRow(this.ds.dataForIndex(tbody.rows[i]._id)),tbody.rows[i]);
			}
		}
		if (this.scrollBar && document.getElementById(this.tableId).clientHeight != height) {
			this.scrollBar.positionBar();
		}
	},
	scrollTo: function(position) {
		var height = document.getElementById(this.tableId).clientHeight;
		// figure out if this move leaves any of the current rows showing
		if (position < (this.currentRow + this.rows) && position > (this.currentRow - this.rows)) {
			// small move
			var tbody = document.getElementById(this.tableId).tBodies[0];
			var move = (position - this.currentRow);

			if (move > 0) {
				this.ds.needData(this.currentRow+this.rows,move);
				// were removing rows from the top adding them to the bottom
				for(var i = 0; i < move; i++) {
					tbody.deleteRow(0);
					tbody.appendChild(this.renderRow(this.ds.dataForIndex(this.currentRow+this.rows)));
					this.currentRow++;
				}
			}
			else {
				this.ds.needData(this.currentRow,move);
				// were removing rows from the bottom adding them to the top
				for(var i = 0; i < Math.abs(move); i++) {
					this.currentRow--;
					tbody.deleteRow(this.rows-1);
					tbody.insertBefore(this.renderRow(this.ds.dataForIndex(this.currentRow)),tbody.rows[0]);
				}
			}
		}
		else {
			// big move repainting entire grid
			this._emptyGridRows();
			this.ds.needData(position,10);
			this.currentRow = position;
			this.render();
		}
		if (this.scrollBar && document.getElementById(this.tableId).clientHeight != height) {
			this.scrollBar.positionBar();
		}
	},
	// send column widths to the server so they will be populated on the next reload
	storeColumnWidths: function() {
		var widths = {};
		var ths = document.getElementById(this.tableId).tHead.getElementsByTagName('th');
		for(var i = 0; i < ths.length; i++) {
			widths[ths[i]._colId] = ths[i].clientWidth;
		}
		
		this.ds.setColumnWidths(widths);
	},
	_resizeCol: function(dragabble) {
		var handle = dragabble.element;
		if (handle.offsetLeft > 0) {
			var th = handle.parentNode.parentNode;
			th.style.width = (handle.offsetLeft) + 'px';
		}
	},
	_setupHeader: function() {
		var thead = document.getElementById(this.tableId).tHead;
		var ths = thead.getElementsByTagName('th');

		var header = document.getElementById(this.tableId).tHead.getElementsByTagName('tr').item(0);
		header.style.position = 'relative';

		var self = this;
		for(var i = 0; i < ths.length; i++) {
			var cell = document.createElement('div');
			cell.style.position = 'relative';
			var a = document.createElement('a');
			a.innerHTML = ths[i].innerHTML;
			a._index = i;

			if (this.ds.sort[this.ds.renderMap[i]]) {
				a._dir = this.ds.sort[this.ds.renderMap[i]][1];
			}
			a.href = 'javascript:void(0);';

			a.id = this.tableId+'_'+i;


			if (a._dir) {
				a.innerHTML += '<img src="'+this.imagePath+this.images[a._dir]+'" border="0">';
			}

			cell.appendChild(a);

			if (i < (ths.length-1)) {
				var handle = document.createElement('div');
				handle.className = 'gridHeaderHandle';
				handle.innerHTML = '&nbsp;';
				handle.style.height = header.clientHeight + 'px';
				handle.style.right = '0px';
				handle._index = i;
				handle._type = 'headerHandle';
				handle.style.position = 'absolute';
				new Draggable(handle,{constraint:'horizontal',change:function(h) { self._resizeCol(h); } });

				Draggables.addObserver({onStart:function(){},onEnd:function(eventName, draggable, event){ 
					var handle = draggable;
					if (handle._type && handle._type == 'headerHandle') {
						handle.style.left = '';
						self.storeColumnWidths();
						self.scrollBar.positionBar();
						
					}
				}});
				cell.appendChild(handle);
			}

			ths[i].innerHTML = '';
			ths[i].appendChild(cell);
			ths[i]._colId = this.ds.renderMap[i];

			HTML_AJAX_Util.registerEvent(a,'click', function(e) { self._onTHClick(HTML_AJAX_Util.eventTarget(e)); });
		}

		// set widths
		for(var i = 0; i < this.ds.renderMap.length; i++) {
			ths[i].style.width = this.ds.widths[this.ds.renderMap[i]] + 'px';
		}
	},
	_loadTemplate: function() {
		var trs = document.getElementById(this.templateId).getElementsByTagName('tr');
		for(var i = 0; i < trs.length; i++) {
			this.templates[trs[i].className] = trs[i];
		}
	},
	_emptyGridRows: function() {
		var rows = document.getElementById(this.tableId).tBodies[0].rows;
		while(rows.length > 0) {
			document.getElementById(this.tableId).tBodies[0].deleteRow(0);
		}
	},
	_onTHClick: function(a) {
		var dir = "ASC";
		if (a._dir) {
		 	if (a._dir == "ASC") {
				dir = "DESC";
			} 
			else {
				dir = false;
			}
		}
		a._dir = dir;
		var img = a.getElementsByTagName('img');
		if (a._dir) {
			if (img.length == 1) {
				img[0].src = this.imagePath+this.images[a._dir];
			}
			else {
				a.innerHTML += '<img src="'+this.imagePath+this.images[a._dir]+'" border="0">';
			}
		}
		else {
			if (img.length == 1) {
				a.removeChild(img[0]);
			}
		}
		this.ds.setSort(this.ds.renderMap[a._index],dir);
		this.ds.needData(this.currentRow,this.rows);
		this._emptyGridRows();
		this.render();
	}
}
