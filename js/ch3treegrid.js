/*****************************************************************************
*       ch3treegrid.js
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

function eXcell_tree(cell){
	if (cell) {
		this.cell = cell;
		this.grid = this.cell.parentNode.grid
	}
	this.inputTag = null;
}

eXcell_tree.prototype = new eXcell;

eXcell_tree.prototype.getValue = function() {
	// returns the label tag - data that enclosed in <label> tag
	return this.cell.parentNode.labelTag.innerHTML;
};

eXcell_tree.prototype.getLabel = eXcell_tree.prototype.getValue;

eXcell_tree.prototype.setLabel = function(label) {
	// change the label tag - data that enclosed in <label> tag
	this.cell.parentNode.labelTag.innerHTML = label;
};

eXcell_tree.prototype.setValue = function(val) {
	if (this.cell.parentNode.imgTag) {
		this.setLabel(val);
	}

	var id = this.cell.parentNode.idd;
	var row = this.grid._h2.get[id];
	if (!row) {
		return;
	}
	row.label = val;

	if (this.cell._attrs["image"]) {
		row.img = this.cell._attrs["image"];
	}

	var height = 18;
	if (_isIE) {
		height = 20;
	}
	var html = "<div style=\"overflow:hidden;white-space:nowrap;height:" + height + "px;\">";

	var spaceImg = "<img src=\"" + this.grid.imgURL + "blank.gif\" align=\"absmiddle\" class=\"space\" />";
	for (var i = 0; i < row.level; i++) {
		html += spaceImg;
	}

	var onClick = "this.";
	if (!_isKHTML) {
		onClick += "parentNode.";
	}
	onClick += "parentNode.parentNode.parentNode.parentNode.grid.toggleKids(this);event.cancelBubble=true;";

	html += "<img src=\"" + this.grid.imgURL + row.state + ".gif" + "\" align=\"absmiddle\" onclick=\"" + onClick + "\" />";

	var imgSrc = this.grid.imgURL;
	if (this.grid.iconURL) {
		imgSrc = this.grid.iconURL;
	}
	html += "<img src=\"" + imgSrc + row.img + "\" align=\"absmiddle\"";
	if (this.grid._img_height) {
		html += " height=\"" + this.grid._img_height + "\"";
	}
	if (this.grid._img_width) {
		html += " width=\"" + this.grid._img_width + "\"";
	}
	html += " /><label";
	if (_isFF) {
		html += " style=\"position:relative; top:2px;\"";
	}
	html += " id=\"nodeval\">" + row.label + "</label></div>";

	this.cell.innerHTML = html;
	// <img> tag - actual image and not the spacer image
	this.cell.parentNode.imgTag = this.cell.childNodes[0].childNodes[row.level];
	// <label> tag - contains the actual data/label
	this.cell.parentNode.labelTag = this.cell.childNodes[0].childNodes[row.level+2];
};

eXcell_tree.prototype.edit = function() {
	if (this.inputTag) {
		return;
	}
	this.cell.atag = ((!this.grid.multiLine)&&(_isKHTML||_isMacOS||_isFF)) ? "INPUT" : "TEXTAREA";
	this.val = this.getValue();

	var obj = document.createElement(this.cell.atag);
	obj.style.height = (this.cell.offsetHeight-(_isIE ? 4 : 2))+"px";
	obj.style.overflow ="hidden";
	obj.className = "dhx_combo_edit";
	obj.wrap = "soft";
	obj.style.textAlign = this.cell.style.textAlign;
	obj.value = this.val; // do we really need this?

	obj.onmousedown = function(e) {
		(e||event).cancelBubble = true;
	};
	obj.onselectstart = function(e) {
		(e||event).cancelBubble = true;
		return true;
	};

	if (_isFF) {
		obj.style.overflow = "visible";
		if (this.grid.multiLine && obj.offsetHeight >= 18 && obj.offsetHeight < 40) {
			obj.style.height = "36px";
			obj.style.overflow = "scroll"
		}
	};

	// we need to replace the this.cell.parentNode.labelTag to add an input field
	this.inputTag = this.cell.parentNode.labelTag;
	this.inputTag.innerHTML = "";
	this.inputTag.appendChild(obj);
	// this line supports the Enter key as done editing
	this.inputTag.className += " editable";
	this.inputTag.firstChild.value = this.val;
	this.inputTag.firstChild.onclick = function(e) {
		(e||event).cancelBubble = true;
	};
	this.obj = this.inputTag.firstChild;
	if (_isIE) {
		this.obj.select();
	};
	this.obj.focus();
};

eXcell_tree.prototype.detach = function() {
	if (!this.inputTag) {
		return;
	}
	this.setValue(this.inputTag.firstChild.value);
	this.obj = null;
	this.inputTag = null;
	return this.val != this.getValue()
};


/**==========================================================================**\
||| Extends dhtmlXGridObject prototype                                       |||
\**==========================================================================**/
dhtmlXGridObject._emptyLineImg = "line";
dhtmlXGridObject.prototype.render_row_tree = dhtmlXGridObject.prototype.render_row;

dhtmlXGridObject.prototype._process_tree_xml = function(xml,top,pid) {
	if (!xml) {
		return;
	}
	// it's assume here that the xml parameter is valid
	this._parsing = true;
	var isParent = false;
	if (typeof top == "undefined") {
		// xml tree that don't have top is considered as the parent tree
		isParent = true;
		top = xml.getXMLTopNode(this.xml.top);
		if (!this._h2) {
			this._h2 = new dhtmlxHierarchy();
		}
	}
	if (typeof pid == "undefined") {
		pid = top.getAttribute("parent");
		if (pid == null) {
			pid = 0;
		}
	}

	var rows = xml.doXPath(this.xml.row,top);
	var cr = xml.doXPath("//"+this.xml.top)[0].getAttribute("pos");
	if (!cr) {
		cr = 0;
	}
	for (var i = 0; i < rows.length; i++) {
		var row = rows[i];
		var id = row.getAttribute("id");
		if (!id) {
			id = i + cr + 1;
		}
		var item = this._h2.add(id,pid);
		if (item.buff) {
			continue;
		}
		item.buff = {
			idd: id,
			data: rows[i],
			_parser: this._process_xml_row,
			_locator: this._get_xml_data
		};
		if (row.getAttribute("open")) {
			item.state = "minus";
			this._openItems.push(id);
		}
		this.rowsAr[id] = item;
		this._process_tree_xml(xml,row,id);
	}
	if (isParent) {
		this._h2_to_buff();
		this.render_dataset();
		this._parsing = false;
		var ret = xml.xmlDoc;
		if (xml.xmlDoc.responseXML) {
			ret = xml.xmlDoc.responseXML;
		}
		return ret;
	}
};

dhtmlXGridObject.prototype._updateTGRState = function(item) {
	if (!item || !item.update || item.id == 0 || !this.rowsAr[item.id].imgTag) {
		return;
	}
	this.rowsAr[item.id].imgTag.src = this.imgURL + item.state + ".gif";
	item.update = false;
};

dhtmlXGridObject.prototype._getOpenLenght = function(id,start) {
	if (!id) {
		id = 0;
	}
	if (!start) {
		start = 0;
	}
	var children = this._h2.get[id].childs;
	start += children.length;
	for (var i = 0; i < children.length; i++) {
		if (children[i].childs.length > 0 && children[i].state == "minus") {
			start += this._getOpenLenght(children[i].id,0);
		}
	}
	return start;
};

dhtmlXGridObject.prototype.getParentId = function(rowId) {
	if (typeof rowId == "undefined") {
		rowId = 0;
	}
	var item = this._h2.get[rowId];
	if (!item || !item.parent) {
		return null;
	}
	return item.parent.id;
};

dhtmlXGridObject.prototype.getLevel = function(rowId) {
	return this._h2.getLevel(rowId);
};

dhtmlXGridObject.prototype._h2_to_buff = function(top) {
	if (!top) {
		top = this._h2.get[0];
		this.rowsBuffer = new dhtmlxArray();
	}
	var buff = null;
	var row = null;
	var child = null;
	for (var i = 0; i < top.childs.length; i++) {
		child = top.childs[i];
		buff = child.buff;
		row = this.rowsAr[child.buff.idd];
		if (typeof row.buff != "undefined") {
			buff = row.buff;
		}
		this.rowsBuffer.push(buff);
		if (child.state == "minus") {
			this._h2_to_buff(child);
		}
	}
};

dhtmlXGridObject.prototype.expandAll = function() {
	this._renderAllExpand(0);
	this._h2_to_buff();
	this._reset_view();
	this.setSizes();
	if (this._redrawLines) {
		this._redrawLines();
	}
};

dhtmlXGridObject.prototype._renderAllExpand = function(index) {
	var children = this._h2.get[index].childs;
	var child = null;
	for (var i = 0; i < children.length; i++){
		child = children[i];
		if (child.childs.length <= 0) {
			continue;
		}
		this._h2.change(child.id,"state","minus");
		this._updateTGRState(child);
		this._renderAllExpand(child.id);
	}
};

dhtmlXGridObject.prototype.toggleKids = function(obj) {
	this.editStop();
	var row = obj.parentNode.parentNode.parentNode;
	var item = this._h2.get[row.idd];
	if (!item) {
		return;
	}
	if (item.state == "plus") {
		this.expandKids(row);
	}
	else if (item.state == "minus") {
		this.collapseKids(row);
	}
};

dhtmlXGridObject.prototype.expandKids = function(row) {
	var item = this._h2.get[row.idd];
	if (!item || !item.childs.length || item.state != "plus") {
		return;
	}

	var index = this.getRowIndex(item.id) + 1;
	if (item.childs.length) {
		this._h2.change(item.id,"state","minus");
		this._updateTGRState(item);

		if (!this.rowsCol) {
			this.rowsCol = new dhtmlxArray();
		}
		var start = this.rowsCol[index];

		var parentNode = this.obj.rows[0].parentNode;
		if (_isKHTML) {
			parentNode = this.obj;
		}
		this._h2_to_buff();

		if (start) {
			var rowsCol = this.rowsCol.slice(0,index);
		}
		else {
			var rowsCol = this.rowsCol.slice(0);
		}

		var len = this._getOpenLenght(item.id,0);
		for (var i = 0; i < len; i++) {
			var rowCol = this.render_row(index + i);
			// insert row to this.rowsCol
			rowsCol.push(rowCol);
			if (start) {
				start.parentNode.insertBefore(rowCol,start);
			}
			else {
				parentNode.appendChild(rowCol);
			}
		}
		if (start) {
			var rowsColeft = this.rowsCol.slice(index);
			if (rowsColeft) {
				for (var i = 0; i < rowsColeft.length; i++) {
					rowsCol.push(rowsColeft[i]);
				}
			}
		}
		this.rowsCol = rowsCol;
	}
	this.setSizes();
	if (this._redrawLines) {
		this._redrawLines(row.idd);
	}
};

dhtmlXGridObject.prototype.collapseKids = function(row) {
	var item = this._h2.get[row.idd];
	if (item.state != "minus") {
		return;
	}

	this._h2.change(item.id,"state","plus");
	this._updateTGRState(item);

	var index = row.rowIndex;
	var len = this._getOpenLenght(this.rowsCol[index - 1].idd,0);
	for (var i = 0; i < len; i++) {
		this.rowsCol[index + i].parentNode.removeChild(this.rowsCol[index + i]);
	}
	this.rowsCol.splice(index,len);
	this.setSizes();
	this._h2_to_buff();
};


/**==========================================================================**\
||| dhtmlxHierarchy <- used by dhtmlXGridObject                              |||
\**==========================================================================**/

function dhtmlxHierarchy() {
	this.defaultState = dhtmlXGridObject._emptyLineImg;
	this.get = { "0": this.getTemplateItem() };
	return this;
}

dhtmlxHierarchy.prototype.getTemplateItem = function() {
	var item = { id: 0,
		childs: [],
		level: -1,
		parent: null,
		index: 0,
		state: dhtmlXGridObject._emptyLineImg,
		update: false,
		img: "leaf.gif",
		label: ""
	};
	return item;
};

dhtmlxHierarchy.prototype.getLevel = function(id) {
	if (typeof id == "undefined") {
		id = 0;
	}
	var ret = -1;
	var item = this.get[id];
	if (item) {
		ret = item.level;
	}
	return ret;
};

dhtmlxHierarchy.prototype.add = function(id,parentId) {
	return this._addItem(id,parentId);
};

dhtmlxHierarchy.prototype._addItem = function(id,parentId) {
	if (typeof parentId == "undefined") {
		parentId = 0;
	}
	var parentItem = this.get[parentId];
	if (typeof parentItem == "undefined") {
		parentId = 0;
		parentItem = this.get[parentId];
	}
	var item = this.getTemplateItem();
	item.id = id;
	item.level = parentItem.level + 1;
	item.parent = parentItem;
	item.index = parentItem.childs.length;
	if (parentItem.state == this.defaultState) {
		var state = "plus";
		if (parentId == 0) {
			state = "minus";
		}
		this.change(parentId,"state",state);
	}
	parentItem.childs.push(item);
	this.get[id] = item;
	return item;
};

dhtmlxHierarchy.prototype.change = function(id,name,val) {
	var item = this.get[id];
	if (typeof item == "undefined" || item[name] == val) {
		return;
	}
	item[name] = val;
	item.update = true;
};

dhtmlxHierarchy.prototype.forEachChild = function(pid,func,gridObj) {
	var funcType = typeof func;
	var parent = this.get[pid];
	if (typeof parent == "undefined" || funcType != "function" || !(parent.childs.length > 0)) {
		return;
	}
	if (typeof gridObj == "undefined") {
		gridObj = this;
	}
	var child = null;
	for (var i in parent.childs) {
		child = parent.childs[i];
		func.apply(gridObj,[child]);
		if (child.childs.length > 0) {
			this.forEachChild(child.id,func,gridObj);
		}
	}
};
