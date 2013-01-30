dhtmlXGridObject.prototype.enableDynamicLoading = function(mode) {
	this._srnd = mode;
};

dhtmlXGridObject.prototype._get_view_size = function() {
	return Math.floor(parseInt(this.entBox.offsetHeight) / this._srdh) + 4;
};

dhtmlXGridObject.prototype._update_srnd_view = function() {
	if (!this._srdh) this._srdh = (this.skin_name == "xp")?22:20;

	var min = Math.floor(this.objBox.scrollTop / this._srdh) - 4;
	if (min < 0) min = 0;
	var max = min + this._get_view_size();
	if (max > this.rowsBuffer.length) max = this.rowsBuffer.length;
	var ids = [];
	for (var j = min; j < max; j++) {
		if (this.rowsCol[j]) continue;
		var res = this._render_from_buffer(j);
		if (res == -1) continue;
		ids.push(j);
		if (this._tgle) {
			this._updateLine(this._h2.get[this.rowsBuffer[j].idd],this.rowsBuffer[j]);
			this._updateParentLine(this._h2.get[this.rowsBuffer[j].idd],this.rowsBuffer[j]);
		}
		if (j && j == (this._realfake?this._fake:this)["_r_select"]) {
			this.selectCell(j, this.cell?this.cell._cellIndex:0, true);
		}
	}
	this.callEvent("onRowsLoaded",[min,max,ids]);
};

dhtmlXGridObject.prototype._render_from_buffer = function(index) {
	var row = this.render_row(index);
	if (row == -1) return -1;
	if (row._attrs["selected"] || row._attrs["select"]) {
		this.selectRow(row,false,true);
		row._attrs["selected"] = row._attrs["select"] = null;
	}
	if (!this._cssSP) {
		if (this._cssEven && (index % 2) == 0)
			row.className = this._cssEven + ((row.className.indexOf("rowselected") != -1)?" rowselected ":" ") + (row._css||"");
		else if (this._cssUnEven && (index % 2) == 1)
			row.className = this._cssUnEven + ((row.className.indexOf("rowselected") != -1)?" rowselected ":" ") + (row._css||"");
	}
	else if (this._h2) {
		var x = this._h2.get[row.idd];
		row.className += " " + ((x.level%2)?(this._cssUnEven + " " + this._cssUnEven):(this._cssEven + " " + this._cssEven)) + "_" + x.level + (this.rowsAr[x.id]._css||"");
	}
	for (var i = 0; i < this._fillers.length; i++) {
		var filler = this._fillers[i];
		if (!filler || !(filler[0] <= index && (filler[0] + filler[1]) > index)) continue;
		var pos = index - filler[0];
		if (pos == 0) {
			this._insert_row(index,row,filler[2],true);
			this._update_fillers(i,-1,1);
		}
		else if (pos == (filler[1] - 1)) {
			this._insert_row(index,row,filler[2]);
			this._update_fillers(i,-1,0);
		}
		else {
			this._fillers.push(this._add_filler((index+1),(filler[1]-pos-1),filler[2],1));
			this._insert_row(index,row,filler[2]);
			this._update_fillers(i,(-filler[1]+pos),0);
		}
		break;
	}
};

dhtmlXGridObject.prototype._add_filler = function(pos,len,filler) {
	if (!len) return null;
	var row = this._prepareRow("__dummy__");
	row.firstChild.style.width = "1px";
	for (var i = 1; i < row.childNodes.length; i++) {
		row.childNodes[i].style.display = "none";
	}
	row.firstChild.style.height = (len * this._srdh) + "px";
	filler = filler||this.rowsCol[pos];
	if (filler && filler.nextSibling) {
		filler.parentNode.insertBefore(row,filler.nextSibling);
	}
	else {
		if (_isKHTML) this.obj.appendChild(row);
		else this.obj.rows[0].parentNode.appendChild(row);
	}
	return [pos,len,row];
};

dhtmlXGridObject.prototype._update_fillers = function(index,right,left) {
	var filler = this._fillers[index];
	filler[1] = filler[1] + right;
	filler[0] = filler[0] + left;
	if (!filler[1]) {
		filler[2].parentNode.removeChild(filler[2]);
		this._fillers.splice(index,1)
	}
	else {
		filler[2].firstChild.style.height = (parseFloat(filler[2].firstChild.style.height) + right * this._srdh)+"px";
	}
};

dhtmlXGridObject.prototype._insert_row = function(index,row,filler,before) {
	if (typeof before == "undefined") before = false;
	if (before) {
		filler.parentNode.insertBefore(row,filler);
		this.rowsCol[index] = row;
	}
	else if (filler.nextSibling) {
		filler.parentNode.insertBefore(row,filler.nextSibling);
	}
	else {
		filler.parentNode.appendChild(row);this.rowsCol[index] = row;
	}
};
