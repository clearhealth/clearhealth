dhtmlXGridObject.prototype._process_json_row=function(r, data){
		r._attrs=data;
		for (var j = 0; j < r.childNodes.length; j++)r.childNodes[j]._attrs={
		};
		if (data.userdata)
			for (var a in data.userdata)
				this.setUserData(r.idd,a,data.userdata[a])
		this._fillRow(r, (this._c_order ? this._swapColumns(data.data) : data.data));
		return r;
}	
dhtmlXGridObject.prototype._process_json=function(data){
		this._parsing=true;
	
		if (data&&data.xmlDoc)
			data = JSON.parse(data.xmlDoc.responseText);
		else if (typeof data == "string")
			data = JSON.parse(data);
			
			
		var cr = parseInt(data.pos||0);
		var total = parseInt(data.total_count||0);
		if (total&&!this.rowsBuffer[total-1])
			this.rowsBuffer[total-1]=null;
			
		if (this.isTreeGrid())
			return this._process_tree_json(data);
			
		for (var i = 0; i < data.rows.length; i++){
			if (this.rowsBuffer[i+cr])
				continue;
			var id = data.rows[i].id;
			this.rowsBuffer[i+cr]={
				idd: id,
				data: data.rows[i],
				_parser: this._process_json_row,
				_locator: this._get_json_data
				};
	
			this.rowsAr[id]=data[i];
		//this.callEvent("onRowCreated",[r.idd]);
		}
		if (this.dynLoad) {
			this._initDynamicLoading();
		}
		else {
			this.render_dataset();
		}
		this._parsing=false;
}

dhtmlXGridObject.prototype.setOffsetHeight = function(height){
	this._offsetHeight = parseInt(height);
};

dhtmlXGridObject.prototype._initDynamicLoading = function(){
	var offsetHeight = parseInt(this._offsetHeight||this.entBox.offsetHeight);
	var rowHeight = (this.skin_name == "xp")?22:20;
	var displayRows = Math.floor(offsetHeight / rowHeight);
	var quartDisplay = Math.ceil(displayRows / 4);
	var extraRows = parseInt(displayRows + quartDisplay);
	var rowsLimit = parseInt(displayRows + extraRows);
	var min = 0;
	var max = (this.rowsBuffer.length > rowsLimit)? rowsLimit : this.rowsBuffer.length;
	if (this._ch_debug) console.error("rowsLimit: "+rowsLimit);
	if (this._ch_debug) console.error("rowsBuffer: "+this.rowsBuffer.length);
	if (this._ch_debug) console.error("max: "+max);
	var end = parseInt(max - 1);
	//this._ch_data = (this._ch_data || {
	this._ch_data = {
		"max": max,
		"displayRows": displayRows,
		"rowHeight": rowHeight, // default grid's row height, depending on grid style. xp skin is 22px
		"extraRows": extraRows,
		"start": 0,
		"end": end,
		"lastRowIndex": (this.rowsBuffer.length - 1),
		"renderedLastRow": false,
	};
	this._ch_debug = (this._ch_debug||false);
	if (this._ch_debug) console.error("render dataset from 0 to "+max);
	this.render_dataset(0,max);
	if (this._ch_debug) console.error("buffer len: "+this.rowsBuffer.length);
	if (max > this.rowsBuffer.length) {
		max = this.rowsBuffer.length;
		this._ch_data["max"] = max;
	}
	if (this.rowsBuffer.length > 0 && this.dynLoad && max < this.rowsBuffer.length) {
		this.rowsBuffer[0].style.height = rowHeight+"px";
		// set the height of the last row based
		var height = parseInt(this.rowsBuffer.length - max) * rowHeight;
		var index = parseInt(max - 1);
		if (!this.rowsBuffer[index]) {
			this.render_dataset(index,max);
		}
		if (this.rowsBuffer[index]) {
			this.rowsBuffer[index].style.height = height+"px";
			this.rowsBuffer[index].style.display = "";
		}
		this.attachEvent("onScroll",this._processOnScroll);
	}
};

dhtmlXGridObject.prototype.enableDynamicLoad = function(mode){
	this.dynLoad = mode;
};

dhtmlXGridObject.prototype._mergeRows = function(/*row index start*/start, /*row index end*/end, /*row index old start*/oldStart, /*row index old end*/oldEnd){
	if (this._ch_debug) console.error("_mergeRows: index start="+start+" index end="+end+ " index old start="+oldStart+" index old end="+oldEnd);
	var bufferLen = parseInt(this.rowsBuffer.length - 1);
	if (start < 0) start = 0;
	else if (start > this.rowsBuffer.length) start = bufferLen;
	if (oldStart < 0) oldStart = 0;
	else if (oldStart > this.rowsBuffer.length) oldStart = bufferLen;
	if (end < 0) end = 0;
	else if (end > this.rowsBuffer.length) end = bufferLen;
	if (oldEnd < 0) oldEnd = 0;
	else if (oldEnd > this.rowsBuffer.length) oldEnd = bufferLen;
	this._ch_data["start"] = start;
	this._ch_data["end"] = end;

	// set height of old start and end to default
	var rowHeight = parseInt(this._ch_data["rowHeight"]||20);
	if (this._ch_debug) console.error("_mergeRows: set oldStart index "+oldStart+" height from "+this.rowsBuffer[oldStart].style.height+" to "+rowHeight);
	this.rowsBuffer[oldStart].style.height = rowHeight + "px";
	if (this._ch_debug) console.error("_mergeRows: set oldEnd index "+oldEnd+" height from "+this.rowsBuffer[oldEnd].style.height+" to "+rowHeight);
	this.rowsBuffer[oldEnd].style.height = rowHeight + "px";


	var startHeight = start * rowHeight;
	if (startHeight <= 0) startHeight = rowHeight;
	if (this._ch_debug) console.error("_mergeRows: set start index "+start+" height to "+startHeight);
	this.rowsBuffer[start].style.height = startHeight + "px";
	this.rowsBuffer[start].style.display = "";

	var diffEnd = parseInt(this.rowsBuffer.length - end);
	var endHeight = diffEnd * rowHeight;
	if (endHeight <= 0) endHeight = rowHeight;
	if (this._ch_debug) console.error("_mergeRows: set end index "+end+" height to "+endHeight);
	this.rowsBuffer[end].style.height = endHeight + "px";
	this.rowsBuffer[end].style.display = "";

	// set height of start and end based on difference
	if (oldStart < start) { // scroll down
		for (var i = oldEnd; i < end; i++) {
			if (!this.rowsBuffer[i]) continue;
			var tr = this.rowsBuffer[i];
			if (!tr.style) continue;
			if (this._ch_debug) console.error("_mergeRows: show end index "+i);
			tr.style.display = "";
		}
		for (var i = parseInt(oldStart-1); i < start; i++) {
			if (!this.rowsBuffer[i]) continue;
			var tr = this.rowsBuffer[i];
			if (!tr.style) continue;
			if (this._ch_debug) console.error("_mergeRows: hide start index "+i);
			tr.style.display = "none";
		}
	}
	else { // scroll up
		for (var i = oldStart; i > start; i--) {
			if (!this.rowsBuffer[i]) continue;
			var tr = this.rowsBuffer[i];
			if (!tr.style) continue;
			if (this._ch_debug) console.error("_mergeRows: show start index "+i);
			tr.style.display = "";
		}
		for (var i = parseInt(oldEnd+1); i > end; i--) {
			if (!this.rowsBuffer[i]) continue;
			var tr = this.rowsBuffer[i];
			if (!tr.style) continue;
			if (this._ch_debug) console.error("_mergeRows: hide end index "+i);
			tr.style.display = "none";
		}
	}
};

dhtmlXGridObject.prototype._processOnScroll = function(scrollLeft,scrollTop){
	if (this._dynLoadTimer) window.clearTimeout(this._dynLoadTimer);
	var that = this;
	this._dynLoadTimer = window.setTimeout(function(){
		that._doUpdateView(scrollTop);
	},100);
};

dhtmlXGridObject.prototype._doUpdateView = function(scrollTop){
	if (!scrollTop) scrollTop = this.objBox.scrollTop;
	var rowHeight = parseInt(this._ch_data["rowHeight"]||20);
	if (this._ch_debug) console.error("scrollTop: "+scrollTop);
	var scrollDiff = Math.ceil(scrollTop/rowHeight);
	if (this._ch_debug) console.error("DIFF: "+scrollDiff);
	var max = parseInt(this._ch_data["max"]||0);

	var displayRows = parseInt(this._ch_data["displayRows"]||0);
	// scrollDiff = topmost portion of scrollbar that signifies the row index
	var oldStart = parseInt(this._ch_data["start"]);
	var oldEnd = parseInt(this._ch_data["end"]);
	var extraRows = parseInt(this._ch_data["extraRows"]||0);
	var start = parseInt(scrollDiff - extraRows);
	if (start < 0) start = 0;
	var end = parseInt(scrollDiff + displayRows + extraRows);
	if (end > this.rowsBuffer.length) end = this.rowsBuffer.length;
	this._ch_data["start"] = start;
	this._ch_data["end"] = end;
	if (this._ch_debug) console.error("oldStart: "+oldStart+"; newStart: "+start);
	if (this._ch_debug) console.error("oldEnd: "+oldEnd+"; newEnd: "+end);

	if (this._ch_debug) console.error("start: "+start+"; end: "+end);
	if (this._ch_debug) console.error("displayRows: "+displayRows+"; extraRows: "+extraRows);
	var max = this._ch_data["max"];
	if (end > max) {
		this._ch_data["max"] = end;
	}
	for (var i = start; i < end; i++) {
		if (i == this._ch_data["lastRowIndex"] && !this._ch_data["renderedLastRow"]) {
			this._ch_data["renderedLastRow"] = true;
		}
		else if (this.rowsBuffer[i].style) {
			continue;
		}
		var max = parseInt(i + 1);
		if (this._ch_debug) console.error("infor render dataset from "+i+" to "+max);
		this.render_dataset(i,max);
	}
	this._mergeRows(start,(end-1),oldStart,oldEnd);
	if (this._ch_debug) console.error("START: "+start+"; END: "+end);
};

dhtmlXGridObject.prototype._process_tree_json=function(data,top,pid){
	this._parsing=true;
	var main=false;
	if (!top){
		this.render_row=this.render_row_tree;
		main=true;
		top=data;
		pid=top.parent||0;
		if (pid=="0") pid=0;
		if (!this._h2)	 this._h2=new dhtmlxHierarchy();
		if (this._fake) this._fake._h2=this._h2;
	} 
	
	if (top.rows) 
	for (var i = 0; i < top.rows.length; i++){
			var id = top.rows[i].id;
			var row=this._h2.add(id,pid);
			row.buff={ idd:id, data:top.rows[i], _parser: this._process_json_row, _locator:this._get_json_data };
			if (top.rows[i].open)
			    row.state="minus";
			
			this.rowsAr[id]=row.buff;
		    this._process_tree_json(top.rows[i],top.rows[i],id);
	}
	
	if (main){ 
		if (pid!=0) this._h2.change(pid,"state","minus")
		this._updateTGRState(this._h2.get[pid]);
		this._h2_to_buff();
		
		this.render_dataset();
		if (this._slowParse===false){
			this.forEachRow(function(id){
				this.render_row_tree(0,id)
			})
		}
		this._parsing=false;
	}
	
}	
