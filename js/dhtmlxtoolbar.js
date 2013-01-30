//v.2.0 build 81107

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
/**
*   @desc: constructor, creates a new dhtmlxToolbar object
*   @param: baseId - id of html element to which webmenu will attached
*   @type: public
*/
function dhtmlXToolbarObject(baseId, skin) {
	var main_self = this;
	this.base = (typeof baseId != "object")?document.getElementById(baseId):baseId;
	while (this.base.childNodes.length > 0) { this.base.removeChild(this.base.childNodes[0]); }
	//
	this._isIE6 = false;
	if (_isIE) { this._isIE6 = (window.XMLHttpRequest==null?true:false); }
	this.skin = (skin==null?"dhx_blue":skin);
	this.base.className = "dhtmlxToolbar_"+this.skin;
	this.base.innerHTML = "<table border='0' cellspacing='0' cellpadding='1' class='dhtmlxToolbarTable_"+this.skin+"'><tr><td width='5'></td></tr></table>";
	this.tr = this.base.childNodes[0].childNodes[0].childNodes[0];
	// this.base.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.objPull = {};
	this.anyUsed = "none";
	/* layOut */
	this.layout = "HOR"; // HOR|VER
	this._setLayout = function(layout) {
		this.layout = ((layout=="HOR"||layout=="VER")?layout:"HOR");
	}
	/* images */
	this.imagePath = "";//"../../codebase/imgs/";
	this.emptyImage = "blank.gif";
	/**
	*   @desc: set path to used images
	*   @param: path - path to images on harddisk
	*   @type: public
	*/
	this.setIconsPath = function(path) { this.imagePath = path; }
	/**
	*   @desc: alias of setIconsPath
	*   @type: public
	*/
	this.setIconPath = this.setIconsPath;
	/* load */
	this._doOnLoad = function() {}
	/**
	*   @desc: loads data to object from xml file
	*   @param: xmlFile - file with dta to load
	*   @param: onLoadFunction - function to call after data will loaded
	*   @type: public
	*/
	this.loadXML = function(xmlFile, onLoadFunction) {
		if (onLoadFunction != null) { this._doOnLoad = function() { onLoadFunction(); } }
		this.callEvent("onXLS", []);
		this._xmlLoader.loadXML(xmlFile);
	}
	/**
	*   @desc: loads data to object from xml string
	*   @param: xmlString - xml string with data to load
	*   @param: onLoadFunction - function to call after data will loaded
	*   @type: public
	*/
	this.loadXMLString = function(xmlString, onLoadFunction) {
		if (onLoadFunction != null) { this._doOnLoad = function() { onLoadFunction(); } }
		this._xmlLoader.loadXMLString(xmlString);
	}
	this._xmlParser = function() {
		var root = this.getXMLTopNode("toolbar");
		for (var q=0; q<root.childNodes.length; q++) { if (root.childNodes[q].tagName == "item") { main_self._addItemToStorage(root.childNodes[q]); } }
		if (main_self.layout == "VER") {
			main_self.base.style.width = main_self.base.childNodes[0].offsetWidth+"px";
			var tr = document.createElement("TR");
			main_self.tr.parentNode.appendChild(tr);
			main_self.tr = tr;
			var td = document.createElement("TD");
			td.width = "5";
			td.height = "1";
			main_self.tr.appendChild(td);
			main_self.tr.childNodes[0].height = "100%";
			//main_self.base.childNodes[0].style.height = main_self.base.offsetHeight-2+"px";
		} else {
			var td = document.createElement("TD"); td.width = "5"; main_self.tr.appendChild(td);
			// var td = document.createElement("TD"); td.width = "100%"; main_self.tr.appendChild(td);
		}
		main_self.callEvent("onXLE", []);
		main_self._doOnLoad();
	}
	this._addItemToStorage = function(itemData, pos) {
		var id = (itemData.getAttribute("id")!=null?itemData.getAttribute("id"):this._genStr(24));
		var type = (itemData.getAttribute("type")!=null?itemData.getAttribute("type"):"");
		if (type != "") {
			if (this["_"+type+"Object"] != null) {
				this.objPull[this.idPrefix+id] = new this["_"+type+"Object"](this, id, itemData, pos);
				this.objPull[this.idPrefix+id]["type"] = type;
				if (this.layout == "VER") {
					var tbody = this.tr.parentNode;
					//
					var tr = document.createElement("TR");
					if (!isNaN(pos)) {
						var total = Math.floor((tbody.childNodes.length - 2)/2);
						if (pos < 1) { pos = 1; }
						if (pos > total) { pos = total; }
						tbody.insertBefore(tr, tbody.childNodes[(pos+1)*2]);
					} else {
						tbody.appendChild(tr);
					}
					this.tr = tr;
					this.tr.height = "1";
					var td = document.createElement("TD");
					td.style.fontSize = "1px";
					td.height = "1";
					this.tr.appendChild(td);
				}
			}
		}
	}
	this._xmlLoader = new dtmlXMLLoaderObject(this._xmlParser, window);
	/* random prefix */
	this._genStr = function(w) {
		var s = ""; var z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for (var q=0; q<w; q++) { s = s + z.charAt(Math.round(Math.random() * z.length)); }
		return s;
	}
	this.rootTypes = new Array("button", "buttonSelect", "buttonTwoState", "separator", "label", "slider", "text", "buttonInput");
	this.idPrefix = this._genStr(12);
	//
	this.dhx_Event();
	this.hotkeyManager();
	//
	this._isWebToolbar = true;
	dhtmlxEvent(document.body, "click", function(e){
		main_self.forEachItem(function(itemId){
			if (main_self.objPull[main_self.idPrefix+itemId]["type"] == "buttonSelect") {
				var item = main_self.objPull[main_self.idPrefix+itemId];
				// console.log(item)
				if (item.polygon.style.display != "none") {
					item.obj.renderAs = "itemDefault";
					item.p1.childNodes[0].className = item.obj.renderAs;
					item.p2.childNodes[0].className = item.obj.renderAs;
					main_self.anyUsed = "none";
					item.polygon.style.display = "none";
					if (main_self._isIE6) { item.polygon._ie6cover.style.display = "none"; }
				}
			}
		});
	});
	//
	return this;
}
/**
*	@desc: return item type by item id
*	@param: itemId
*	@type: public
*/
dhtmlXToolbarObject.prototype.getType = function(itemId) {
	if (this.objPull[this.idPrefix+itemId] == null) { return ""; }
	return this.objPull[this.idPrefix+itemId]["type"];
}
dhtmlXToolbarObject.prototype.inArray = function(array, value) {
	for (var q=0; q<array.length; q++) { if (array[q]==value) return true; }
	return false;
}
dhtmlXToolbarObject.prototype._string2xml = function(xmlString) {
	try {
		var parser = new DOMParser();
		var xmlDoc = parser.parseFromString(xmlString, "text/xml");
	} catch(e) {
		var xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async = this.async;
		xmlDoc["loadXM"+"L"](xmlString);
	}
	return (xmlDoc != null ? xmlDoc : null);
}
/* adding items */
dhtmlXToolbarObject.prototype._addObject = function(obj, pos) {
	//
	var cell = null; // this.tr
	//
	if (this.layout == "VER") {
		var tr = document.createElement("TR");
		if (!isNaN(parseInt(pos))) {
			pos--;
			var tbody = this.tr.parentNode;
			var total = (tbody.childNodes.length - 2)/2;
			if (pos < 0) { pos = 0; }
			if (pos > total) { pos = total; }
			// add node
			tbody.insertBefore(tr, tbody.childNodes[pos*2+1]);
		} else {
			// add node
			this.tr.parentNode.appendChild(tr);
		}
		// that.tr.parentNode.appendChild(tr);
		this.tr = tr;
		cell = this.tr;
	} else {
		cell = obj;
	}
	//
	if (!isNaN(parseInt(pos)) && this.layout != "VER") {
		var total = this.tr.childNodes.length - 3;
		if (total < 0 && this.tr.childNodes.length == 1) {
			var td = document.createElement("TD");
			// td.style.width = "100%";
			td.style.width = "1px";
			this.tr.insertBefore(td, this.tr.childNodes[0]);
			var td = document.createElement("TD");
			td.style.width = "5px";
			this.tr.insertBefore(td, this.tr.childNodes[0]);
			this.tr.insertBefore(obj, this.tr.childNodes[1]);
		} else {
			if (pos < 1) { pos = 1; }
			if (pos > total) { pos = total + 1; }
			this.tr.insertBefore(obj, this.tr.childNodes[pos]);
		}
	} else {
		this.tr.appendChild(obj);
	}
	return cell;
}
dhtmlXToolbarObject.prototype._addItem = function(str, pos) {
	var data = this._string2xml(str);
	if (isNaN(pos)) { pos = null; } else { if (this.layout != "VER") pos++; }
	this._addItemToStorage(data.childNodes[0], pos);
}
/**
*   @desc: adds a button to webbar
*   @param: id - id of a button
*   @param: pos - position of a button
*   @param: text - text for a button (null for no text)
*   @param: imgEnabled - image for enabled state (null for no image)
*   @param: imgDisabled - image for desabled state (null for no image)
*   @type: public
*/
dhtmlXToolbarObject.prototype.addButton = function(id, pos, text, imgEnabled, imgDisabled) {
	var itemText = (text!=null?(text.length==0?null:text):null);
	var str = '<item id="'+id+'" type="button"'+(imgEnabled!=null?' img="'+imgEnabled+'"':'')+(imgDisabled!=null?' imgdis="'+imgDisabled+'"':'')+(itemText!=null?' text="'+itemText+'"':"")+'/>';
	this._addItem(str, pos);
}
/**
*   @desc: adds a text item to webbar
*   @param: id - id of a text item
*   @param: pos - position of a text item
*   @param: text - text for a text item
*   @type: public
*/
dhtmlXToolbarObject.prototype.addText = function(id, pos, text) {
	var str = '<item id="'+id+'" type="text" text="'+text+'"/>';
	this._addItem(str, pos);
}
//#tool_list:06062008{
/**
*   @desc: adds a select button to webbar
*   @param: id - id of a select button
*   @param: pos - position of a select button
*   @param: text - text for a select button (null for no text)
*   @param: opts - listed options for a select button
*   @param: imgEnabled - image for enabled state (null for no image)
*   @param: imgDisabled - image for desabled state (null for no image)
*   @type: public
*/
dhtmlXToolbarObject.prototype.addButtonSelect = function(id, pos, text, opts, imgEnabled, imgDisabled) { 
	// opts: Array(...Array(id, type=obj|sep, text, img),...)
	// opts = Array(Array('id1', 'obj', 'option1', 'img1'), Array('id2', 'obj', 'option2', 'img2'), Array('sep01', 'sep', '', ''), Array('id3', 'obj', 'option3', 'img3'), );
	var itemText = (text!=null?(text.length==0?null:text):null);
	var str = '<item id="'+id+'" type="buttonSelect"'+(imgEnabled!=null?' img="'+imgEnabled+'"':'')+(imgDisabled!=null?' imgdis="'+imgDisabled+'"':'')+(itemText!=null?' text="'+itemText+'"':"")+'>';
	for (var q=0; q<opts.length; q++) {
		if (opts[q][1] == "obj") {
			str += '<item type="button" id="'+opts[q][0]+'" text="'+opts[q][2]+'"'+(opts[q][3]!=null?' img="'+opts[q][3]+'"':'')+'/>';
		} else if (opts[q][1] == "sep") {
			str += '<item id="'+opts[q][0]+'" type="separator"/>';
		}
	}
	str += '</item>';
	this._addItem(str, pos);
}
//#}
//#tool_2state:06062008{
/**
*   @desc: adds a two-state button to webbar
*   @param: id - id of a two-state button
*   @param: pos - position of a two-state button
*   @param: text - text for a two-state button (null for no text)
*   @param: imgEnabled - image for enabled state (null for no image)
*   @param: imgDisabled - image for desabled state (null for no image)
*   @type: public
*/
dhtmlXToolbarObject.prototype.addButtonTwoState = function(id, pos, text, imgEnabled, imgDisabled) {
	var itemText = (text!=null?(text.length==0?null:text):null);
	var str = '<item id="'+id+'" type="buttonTwoState"'+(imgEnabled!=null?' img="'+imgEnabled+'"':'')+(imgDisabled!=null?' imgdis="'+imgDisabled+'"':'')+(itemText!=null?' text="'+itemText+'"':"")+'/>';
	this._addItem(str, pos);
}
//#}
/**
*   @desc: adds a separator to webbar
*   @param: id - id of a separator
*   @param: pos - position of a separator
*   @type: public
*/
dhtmlXToolbarObject.prototype.addSeparator = function(id, pos) {
	var str = '<item id="'+id+'" type="separator"/>';
	this._addItem(str, pos);
}
//#tool_slider:06062008{
/**
*   @desc: adds a slider to webbar
*   @param: id - id of a slider
*   @param: pos - position of a slider
*   @param: len - length (width) of a slider (px)
*   @param: valueMin - minimal available value of a slider
*   @param: valueMax - maximal available value of a slider
*   @param: valueNow - initial current value of a slider
*   @param: textMin - label for minimal value side (on the left side)
*   @param: textMax - label for maximal value side (on the right side)
*   @param: tip - tooltip template (%v will replaced with current value)
*   @type: public
*/
dhtmlXToolbarObject.prototype.addSlider = function(id, pos, len, valueMin, valueMax, valueNow, textMin, textMax, tip) {
	var itemTextMin = (textMin!=null?(textMin.length==0?null:textMin):null);
	var itemTextMax = (textMax!=null?(textMax.length==0?null:textMax):null);
	var str = '<item id="'+id+'" type="slider" length="'+len+'" valueMin="'+valueMin+'" valueMax="'+valueMax+'" valueNow="'+valueNow+'"'+(itemTextMin!=null?' textMin="'+itemTextMin+'"':'')+(itemTextMax!=null?' textMax="'+itemTextMax+'"':'')+' toolTip="'+tip+'"/>';
	this._addItem(str, pos);
}
//#}
/**
*   @desc: adds an input item to webbar
*   @param: id - id of an input item
*   @param: pos - position of an input item
*   @param: value - value (text) in an input item by the default
*   @param: width - width of an input item (px)
*   @type: public
*/
dhtmlXToolbarObject.prototype.addInput = function(id, pos, value, width) {
	var str = '<item id="'+id+'" type="buttonInput" value="'+value+'" width="'+width+'"/>';
	this._addItem(str, pos);
}
/**
*   @desc: iterator, calls user handler for each item
*   @param: handler - user function, will take item id as an argument
*   @type: public
*/
dhtmlXToolbarObject.prototype.forEachItem = function(handler) {
	for (var a in this.objPull) {
		if (this.inArray(this.rootTypes, this.objPull[a]["type"])) {
			handler(this.objPull[a]["id"].replace(this.idPrefix,""));
		}
	}
};
(function(){
	var list="showItem,hideItem,isVisible,enableItem,disableItem,isEnabled,setItemText,getItemText,setItemToolTip,getItemToolTip,setItemImage,setItemImageDis,clearItemImage,clearItemImageDis,setItemState,getItemState,setItemToolTipTemplate,getItemToolTipTemplate,setValue,getValue,setMinValue,getMinValue,setMaxValue,getMaxValue,setWidth,getWidth".split(",")
	var ret=["","",false,"","",false,"","","","","","","","","",false,"","","",null,"",[null,null],"",[null,null],"",null]
	var functor=function(name,res){
			return function(itemId,a,b){
				itemId = this.idPrefix+itemId;
				if (this.objPull[itemId][name] != null) 
					return this.objPull[itemId][name].call(this.objPull[itemId],a,b)
				else 
					return res;
				};
			}
			
	for (var i=0; i<list.length; i++){
		var name=list[i];
		var res=ret[i];
		dhtmlXToolbarObject.prototype[name]= functor(name,res);
	}	
})()


/**
*   @desc: shows a specified item
*   @param: itemId - id of an item to show
*   @type: public
*/
//dhtmlXToolbarObject.prototype.showItem = function(itemId) {
/**
*   @desc: hides a specified item
*   @param: itemId - id of an item to hide
*   @type: public
*/
//dhtmlXToolbarObject.prototype.hideItem = function(itemId) {
/**
*   @desc: returns true if a specified item is visible
*   @param: itemId - id of an item to check
*   @type: public
*/
//dhtmlXToolbarObject.prototype.isVisible = function(itemId) {
/**
*   @desc: enables a specified item
*   @param: itemId - id of an item to enable
*   @type: public
*/
//dhtmlXToolbarObject.prototype.enableItem = function(itemId) {
/**
*   @desc: disables a specified item
*   @param: itemId - id of an item to disable
*   @type: public
*/
//dhtmlXToolbarObject.prototype.disableItem = function(itemId) {
/**
*   @desc: returns true if a specified item is enabled
*   @param: itemId - id of an item to check
*   @type: public
*/
//dhtmlXToolbarObject.prototype.isEnabled = function(itemId) {
/**
*   @desc: sets new text for an item
*   @param: itemId - id of an item
*   @param: text - new text for an item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemText = function(itemId, text) {
/**
*   @desc: return cureent item's text
*   @param: itemId - id of an item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getItemText = function(itemId) {
/**
*   @desc: sets a tooltip for an item
*   @param: itemId - id of an item
*   @param: tip - tooltip (empty for clear)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemToolTip = function(itemId, tip) {
/**
*   @desc: return current item's tooltip
*   @param: itemId - id of an item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getItemToolTip = function(itemId) {
/**
*   @desc: sets an image for an item in enabled state
*   @param: itemId - id of an item
*   @param: url - url of an image
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemImage = function(itemId, url) {
/**
*   @desc: sets an image for an item in disabled state
*   @param: itemId - id of an item
*   @param: url - url of an image
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemImageDis = function(itemId, url) {
/**
*   @desc: removes an image of an item in enabled state
*   @param: itemId - id of an item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.clearItemImage = function(itemId) {
/**
*   @desc: removes an image of an item in disabled state
*   @param: itemId - id of an item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.clearItemImageDis = function(itemId) {
/**
*   @desc: sets a pressed/released state for a two-state button
*   @param: itemId - id of a two-state item
*   @param: state - state, true for pressed, false for released
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemState = function(itemId, state) {
/**
*   @desc: returns current state of a two-state button
*   @param: itemId - id of a two-state item to check
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getItemState = function(itemId) {
/**
*   @desc: sets a tooltip template for a slider
*   @param: itemId - id of a slider
*   @param: template - tooltip template (%v will replaced with current value)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setItemToolTipTemplate = function(itemId, template) {
/**
*   @desc: returns a current tooltip template of a slider
*   @param: itemId - id of a slider
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getItemToolTipTemplate = function(itemId) {
/**
*   @desc: sets a value for a slider or an input item
*   @param: itemId - id of a slider or an input item
*   @param: value - value (int for slider, any for input item)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setValue = function(itemId, value, callEvent) {
/**
*   @desc: returns a current value of a slider or an input item
*   @param: itemId - id of a slider or an input item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getValue = function(itemId) {
/**
*   @desc: sets minimal value and label for a slider
*   @param: itemId - id of a slider
*   @param: value - value (int)
*   @param: label - label for value (empty for no label)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setMinValue = function(itemId, value, label) {
/**
*   @desc: return current minimal value and label of a slider
*   @param: itemId - id of a slider
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getMinValue = function(itemId) {
/**
*   @desc: sets maximal value and label for a slider
*   @param: itemId - id of a slider
*   @param: value - value (int)
*   @param: label - label for value (empty for no label)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setMaxValue = function(itemId, value, label) {
/**
*   @desc: returns current maximal value and label of a slider
*   @param: itemId - id of a slider
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getMaxValue = function(itemId) {
/**
*   @desc: sets a width for an text/input/buttonSelect item
*   @param: itemId - id of an text/input/buttonSelect item
*   @param: width - new width (px)
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setWidth = function(itemId, width) {
/**
*   @desc: returns a current width of an input item
*   @param: itemId - id of an input item
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getWidth = function(itemId) {
/**
*   @desc: sets a new position for an item (moves item to desired position)
*   @param: itemId - id of an item
*   @param: pos - new position
*   @type: public
*/
dhtmlXToolbarObject.prototype.setPosition = function(itemId, pos) {
	this._setPosition(itemId, pos);
}
/**
*   @desc: returns a current position of an item
*   @param: itemId - id of an item
*   @type: public
*/
dhtmlXToolbarObject.prototype.getPosition = function(itemId) {
	return this._getPosition(itemId);
}
dhtmlXToolbarObject.prototype._setPosition = function(id, pos) {
	itemId = this.idPrefix+id;
	if (this.objPull[itemId] == null) { return; }
	var itemPos = this._getPosition(id);
	if (itemPos == pos) { return; }
	//
	if (this.layout == "VER") {
		var tbody = this.base.childNodes[0].childNodes[0];
		//
		var tbody = this.tr.parentNode;
		var total = (tbody.childNodes.length - 2)/2;
		if (pos < 1) { pos = 1; }
		if (pos > total) { pos = total; }
		// add node
		var k1 = tbody.childNodes[(itemPos-1)*2+1];
		var k2 = tbody.childNodes[(itemPos-1)*2+2];
		tbody.removeChild(k1);
		tbody.removeChild(k2);
		// move
		tbody.insertBefore(k2, tbody.childNodes[pos*2-1]);
		tbody.insertBefore(k1, tbody.childNodes[pos*2-1]);
	} else {
		var tr = this.base.childNodes[0].childNodes[0].childNodes[0];
		//
		var total = tr.childNodes.length - 3;
		if (pos < 1) { pos = 1; }
		if (pos > total) { pos = total; }
		//
		var k = tr.childNodes[itemPos];
		tr.removeChild(k);
		tr.insertBefore(k, tr.childNodes[pos])
	}
}
dhtmlXToolbarObject.prototype._getPosition = function(id) {
	var pos = -1;
	id = this.idPrefix+id;
	if (this.objPull[id] == null) { return pos; }
	//
	if (this.layout == "VER") {
		var tbody = this.base.childNodes[0].childNodes[0];
		for (var q=0; q<tbody.childNodes.length; q++) {
			if (tbody.childNodes[q].childNodes[0] == this.objPull[id].obj) { pos = Math.ceil(q/2); }
			if (tbody.childNodes[q].childNodes[0].childNodes[0] == this.objPull[id].obj) { pos = Math.ceil(q/2); } // separators, text
		}
	} else {
		var tr = this.base.childNodes[0].childNodes[0].childNodes[0];
		for (var q=0; q<tr.childNodes.length; q++) {
			if (tr.childNodes[q] == this.objPull[id].obj) { pos = q; }
			if (tr.childNodes[q].childNodes[0] == this.objPull[id].obj) { pos = q; } // separators, text
		}
	}
	return pos;
}
/**
*   @desc: completely removes an item for a webbar
*   @param: itemId - id of an item
*   @type: public
*/
dhtmlXToolbarObject.prototype.removeItem = function(itemId) {
	
	if (this.objPull[this.idPrefix+itemId] == null) { return; }
	//
	if (this.layout == "VER") {
		var obj = this.objPull[this.idPrefix+itemId].obj.parentNode.parentNode;
		var pos = this._getPosition(itemId);
		obj.removeChild(obj.childNodes[pos+1]);
		obj.removeChild(obj.childNodes[pos+1]);
	} else {
		var obj = this.objPull[this.idPrefix+itemId].obj;
		obj.parentNode.removeChild(obj);
	}
	// polygons in buttonSelect
	if (this.objPull[this.idPrefix+itemId].polygon != null) {
		var polygon = this.objPull[this.idPrefix+itemId].polygon;
		polygon.parentNode.removeChild(polygon);
		polygon = null;
	}
	// penlabel in slider
	if (this.objPull[this.idPrefix+itemId].obj != null) {
		if (this.objPull[this.idPrefix+itemId].obj.pen != null) {
			if (this.objPull[this.idPrefix+itemId].obj.pen.label != null) {
				var label = this.objPull[this.idPrefix+itemId].obj.pen.label;
				label.parentNode.removeChild(label);
				label = null;
			}
		}
	}
	// main object
	obj = null;
	delete this.objPull[this.idPrefix+itemId];
};
//#tool_list:06062008{
(function(){
	var list="addListOption,removeListOption,showListOption,hideListOption,isListOptionVisible,enableListOption,disableListOption,isListOptionEnabled,setListOptionPosition,getListOptionPosition,setListOptionText,getListOptionText,setListOptionToolTip,getListOptionToolTip,setListOptionImage,getListOptionImage,clearListOptionImage,forEachListOption,getAllListOptions,setListOptionSelected,getListOptionSelected".split(",")
	var functor = function(name){
				return function(parentId,a,b,c,d,e){
				parentId = this.idPrefix+parentId;
				if (this.objPull[parentId] == null) { return; }
				if (this.objPull[parentId]["type"] != "buttonSelect") { return; }
				return this.objPull[parentId][name].call(this.objPull[parentId],a,b,c,d,e);
			}
		}
	for (var i=0; i<list.length; i++){
		var name=list[i];
		dhtmlXToolbarObject.prototype[name]=functor(name)
	}
})()
/**
*   @desc: adds a listed option to a select button
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @param: pos - position of a listed option
*   @param: type - type of a listed option (button|separator)
*   @param: text - text for a listed option
*   @param: img - image for a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.addListOption = function(parentId, optionId, pos, type, text, img) {
/**
*   @desc: completely removes a listed option from a select button
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.removeListOption = function(parentId, optionId) {
/**
*   @desc: shows a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.showListOption = function(parentId, optionId) {
/**
*   @desc: hides a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.hideListOption = function(parentId, optionId) {
/**
*   @desc: return true if a listed option is visible
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.isListOptionVisible = function(parentId, optionId) {
/**
*   @desc: enables a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.enableListOption = function(parentId, optionId) {
/**
*   @desc: disables a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.disableListOption = function(parentId, optionId) {
/**
*   @desc: return true if a listed option is enabled
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.isListOptionEnabled = function(parentId, optionId) {
/**
*   @desc: sets a position of a listed option (moves listed option)
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @param: pos - position of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setListOptionPosition = function(parentId, optionId, pos) {
/**
*   @desc: returns a position of a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getListOptionPosition = function(parentId, optionId) {
/**
*   @desc: sets a text for a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @param: text - text for a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setListOptionText = function(parentId, optionId, text) {
/**
*   @desc: returns a text of a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getListOptionText = function(parentId, optionId) {
/**
*   @desc: sets a tooltip for a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @param: tip - tooltip for a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setListOptionToolTip = function(parentId, optionId, tip) {
/**
*   @desc: returns a tooltip of a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getListOptionToolTip = function(parentId, optionId) {
/**
*   @desc: sets an image for a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @param: img - image for a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setListOptionImage = function(parentId, optionId, img) {
/**
*   @desc: returns an image of a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getListOptionImage = function(parentId, optionId) {
/**
*   @desc: removes an image (if exists) of a listed option
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.clearListOptionImage = function(parentId, optionId) {
/**
*   @desc: calls user defined handler for each listed option of parentId
*   @param: parentId - id of a select button
*   @param: handler - user defined function, listed option id will passed as an argument
*   @type: public
*/
//dhtmlXToolbarObject.prototype.forEachListOption = function(parentId, handler) {
/**
*   @desc: returns array with ids of all listed options for parentId
*   @param: parentId - id of a select button
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getAllListOptions = function(parentId) {
/**
*   @desc: sets listed option selected
*   @param: parentId - id of a select button
*   @param: optionId - id of a listed option
*   @type: public
*/
//dhtmlXToolbarObject.prototype.setListOptionSelected = function(parentId, optionId) {
/**
*   @desc: returns selected listed option
*   @param: parentId - id of a select button
*   @type: public
*/
//dhtmlXToolbarObject.prototype.getListOptionSelected = function(parentId) {
//#}

/*****************************************************************************************************************************************************************
	object: separator
**************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._separatorObject = function(that, id, data, pos) {
	//
	this.id = that.idPrefix+id;
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.idd = String(id);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	//
	this.obj.innerHTML = "<table cellspacing='0' cellpadding='0' class='itemDefault'>"+
				"<tr>"+
					"<td><div class='dhtmlxToolbarSep'>&nbsp;</div></id>"+
				"</tr>"+
			"</table>";
	// add object
	this.tr = that._addObject(this.obj, pos);
	// functions
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	
	//
	return this;
}
/*****************************************************************************************************************************************************************
	object: text
**************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._textObject = function(that, id, data, pos) {
	this.id = that.idPrefix+id;
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.idd = String(id);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	//
	this.obj.innerHTML = "<table cellspacing='0' cellpadding='0' class='itemDefault'>"+
				"<tr>"+
					"<td class='dhtmlxToolbarTEXT' valign='middle'><span>"+data.getAttribute("text")+"</span></td>"+
				"</tr>"+
			"</table>";
	//
	this.tr = that._addObject(this.obj, pos);
	//
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	this.setItemText = function(text) {
		this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].innerHTML = text;
	}
	this.getItemText = function() {
		return this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].innerHTML;
	}
	this.setWidth = function(width) {
		this.obj.style.width = width+"px";
	}
	//
	return this;
}
/*****************************************************************************************************************************************************************
	object: button
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._buttonObject = function(that, id, data, pos) {
	this.id = that.idPrefix+id;
	this.state = (data.getAttribute("enabled")!=null?false:true);
	this.imgEn = (data.getAttribute("img")!=null?data.getAttribute("img"):"");
	this.imgDis = (data.getAttribute("imgdis")!=null?data.getAttribute("imgdis"):"");
	this.img = (this.state?(this.imgEn!=""?this.imgEn:""):(this.imgDis!=""?this.imgDis:""));
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.allowClick = false;
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.renderAs = "itemDefault";
	this.obj.idd = String(id);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.obj.pressed = false;
	this.obj.innerHTML = "<table cellspacing='0' cellpadding='0' class='item"+(this.state?"Default":"Disabled")+"'>"+
				"<tr>"+
					"<td class='dhtmlxToolbarIMG' width='" + data.getAttribute("width") + "px' height='" + data.getAttribute("height") + "px' valign='middle'"+(this.img!=""?"":" style='display: none;'")+"><img src='"+(this.img!=""?that.imagePath+this.img:that.imagePath+that.emptyImage)+"' " + (data.getAttribute('imgStyle')?" style='" + data.getAttribute('imgStyle') + "'":"") + ">"+(data.getAttribute("text")!=null?data.getAttribute("text"):data.textContent)+"</td>"+
				"</tr>"+
			"</table>";
	this.obj.that = this;
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.obj.onmouseover = function() { this._doOnMouseOver(); }
	this.obj.onmouseout = function() { this._doOnMouseOut(); }
	this.obj._doOnMouseOver = function() {
		this.allowClick = false;
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		this.childNodes[0].className = "itemOver";
		this.renderAs = "itemOver";
	}
	this.obj._doOnMouseOut = function() {
		this.allowClick = false;
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		this.childNodes[0].className = "itemDefault";
		this.renderAs = "itemDefault";
	}
	this.obj.onclick = function(e) {
		if (this.that.state == false) { return; }
		if (this.allowClick == false) { return; }
		e = e||event;
		e.cancelBubble = true;
		// event
		that.callEvent("onClick", [this.idd.replace(that.idPrefix,"")]);
	}
	this.obj.onmousedown = function(e) {
		this.allowClick = true;
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		that.anyUsed = this.idd;
		this.childNodes[0].className = "itemPressed";
		this.pressed = true;
		this.onmouseover = function() { this._doOnMouseOver(); }
		this.onmouseout = function() { that.anyUsed = "none"; this._doOnMouseOut(); }
		return false;
	}
	this.obj.onmouseup = function(e) {
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { if (that.anyUsed != this.idd) { return; } }
		this._doOnMouseUp();
	}
	this.obj._doOnMouseUp = function() {
		that.anyUsed = "none";
		this.childNodes[0].className = this.renderAs;
		this.pressed = false;
	}
	this.obj._doOnMouseUpOnceAnywhere = function() {
		this._doOnMouseUp();
		this.onmouseover = function() { this._doOnMouseOver(); }
		this.onmouseout = function() { this._doOnMouseOut(); }
	}
	// var obj = this.obj;
	// if (_isIE) { document.body.attachEvent("onmouseup", function(){obj._doOnMouseUpOnceAnywhere();}); } else { window.addEventListener("mouseup", function(){obj._doOnMouseUpOnceAnywhere();}, false); }
	//
	// add object
	this.tr = that._addObject(this.obj, pos);
	//
	// functions
	this.enableItem = function() {
		if (!this.state) {
			this.state = true;
			this.obj.childNodes[0].className = "itemDefault";
			if (this.imgEn != "") {
				// show image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
			} else {
				// hide image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
		}
	}
	this.disableItem = function() {
		if (this.state) {
			this.state = false;
			this.obj.childNodes[0].className = "itemDisabled";
			if (this.imgDis != "") {
				// show image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
			} else {
				// hide image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
			this.obj.renderAs = "itemDefault";
		}
	}
	this.isEnabled = function() {
		return this.state;
	}
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	this.setItemText = function(text) {
		if (text == null || text.length == 0) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = "";
		} else {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = text;
		}
	}
	this.getItemText = function() {
		return this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML;
	}
	this.setItemImage = function(url) {
		this.imgEn = url;
		if (this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
		}
	}
	this.clearItemImage = function() {
		this.imgEn = "";
		if (this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.setItemImageDis = function(url) {
		this.imgDis = url;
		if (!this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
		}
	}
	this.clearItemImageDis = function() {
		this.imgDis = "";
		if (!this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.setItemToolTip = function(tip) {
		this.obj.title = tip;
	}
	this.getItemToolTip = function() {
		return this.obj.title;
	}
	return this;
}
//#tool_list:06062008{
/*****************************************************************************************************************************************************************
	object: buttonSelect
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._buttonSelectObject = function(that, id, data, pos) {
	this.id = that.idPrefix+id;
	this.state = (data.getAttribute("enabled")!=null?(data.getAttribute("enabled")=="true"?true:false):true);
	this.imgEn = (data.getAttribute("img")!=null?data.getAttribute("img"):"");
	this.imgDis = (data.getAttribute("imgdis")!=null?data.getAttribute("imgdis"):"");
	this.img = (this.state?(this.imgEn!=""?this.imgEn:""):(this.imgDis!=""?this.imgDis:""));
	this.obj = document.createElement("TD");
	this.obj.allowClick = false;
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.renderAs = "itemDefault";
	//this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.obj.idd = String(id);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	this.obj.pressed = false;
	this.obj.innerHTML = "<table border='0' cellspacing='0' cellpadding='0'><tr><td>"+
				"<table border='0' cellspacing='0' cellpadding='0' class='item"+(this.state?"Default":"Disabled")+"' width='100%'>"+
					"<tr>"+
						"<td class='dhtmlxToolbarIMG' valign='middle'"+(this.img!=""?"":" style='display: none;'")+"><img src='"+(this.img!=""?that.imagePath+this.img:that.imagePath+that.emptyImage)+"'></td>"+
						"<td class='dhtmlxToolbarTEXT' valign='middle'"+(data.getAttribute("text")!=null?"":" style='display: none;'")+"><span>"+(data.getAttribute("text")!=null?data.getAttribute("text"):"")+"</span></td>"+
					"</tr>"+
				"</table></td>"+
				"<td width='10'><table border='0' cellspacing='0' cellpadding='0' class='itemDefault' width='100%'>"+
					"<tr>"+
						"<td class='dhtmlxToolbarArrow'><div class='dhtmlxToolbarArrow'>&nbsp;</div></td>"+
					"</tr>"+
				"</table></td></tr></table>";
	//
	// add object
	this.tr = that._addObject(this.obj, pos);
	//
	var self = this;
	//
	this.p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
	this.p2 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
	
	this.p1.onmouseover = function(e) {
		e = e||event;
		if (that.anyUsed != "none") { return; }
		if (!self.state) { return; }
		self.obj.renderAs = "itemOver";
		self.p1.childNodes[0].className = self.obj.renderAs;
		self.p2.childNodes[0].className = self.obj.renderAs;
	}
	this.p1.onmouseout = function() {
		self.obj.allowClick = false;
		if (that.anyUsed != "none") { return; }
		if (!self.state) { return; }
		self.obj.renderAs = "itemDefault";
		self.p1.childNodes[0].className = self.obj.renderAs;
		self.p2.childNodes[0].className = self.obj.renderAs;
	}
	this.p2.onmouseover = this.p1.onmouseover;
	this.p2.onmouseout = this.p1.onmouseout;
	//
	this.p1.onclick = function(e) {
		e = e||event;
		e.cancelBubble = true;
		if (!self.obj.allowClick) { return; }
		if (!self.state) { return; }
		if (that.anyUsed != "none") { return; }
		// event
		that.callEvent("onClick", [self.obj.idd.replace(that.idPrefix,"")]);
	}
	this.p1.onmousedown = function(e) {
		e = e||event;
		e.cancelBubble = true;
		if (that.anyUsed != "none") { return; }
		if (!self.state) { return; }
		self.obj.allowClick = true;
		self.p1.childNodes[0].className = "itemPressed";
		self.p2.childNodes[0].className = "itemPressed";
	}
	this.p1.onmouseup = function(e) {
		e = e||event;
		e.cancelBubble = true;
		if (that.anyUsed != "none") { return; }
		if (!self.state) { return; }
		self.p1.childNodes[0].className = self.obj.renderAs;
		self.p2.childNodes[0].className = self.obj.renderAs;
	}
	this.p2.onmousedown = function(e) {
		e = e||event;
		e.cancelBubble = true;
		if (!self.state) { return; }
		if (that.anyUsed == self.obj.idd) {
			// hide
			self.p1.childNodes[0].className = self.obj.renderAs;
			self.p2.childNodes[0].className = self.obj.renderAs;
			that.anyUsed = "none";
			self.polygon.style.display = "none";
			if (that._isIE6) { self.polygon._ie6cover.style.display = "none"; }
		} else if (that.anyUsed == "none") {
			// show
			self.p1.childNodes[0].className = "itemOver";
			self.p2.childNodes[0].className = "itemPressed";
			that.anyUsed = self.obj.idd;
			self.polygon.style.left = getAbsoluteLeft(self.obj)+"px";
			self.polygon.style.top = getAbsoluteTop(self.obj)+self.obj.offsetHeight+"px";
			self.polygon.style.display = "";
			if (that._isIE6) {
				self.polygon._ie6cover.style.left = self.polygon.style.left;
				self.polygon._ie6cover.style.top = self.polygon.style.top;
				self.polygon._ie6cover.style.width = self.polygon.offsetWidth+"px";
				self.polygon._ie6cover.style.height = self.polygon.offsetHeight+"px";
				self.polygon._ie6cover.style.display = "";
			}
		}
		return false;
	}
	this.p2.onclick = function(e) {
		e = e||event;
		e.cancelBubble = true;
	}
	this.p2.onmouseup = function(e) {
		e = e||event;
		e.cancelBubble = true;
	}
	// this.obj.that = this;
	this.obj.iddPrefix = that.idPrefix;
	this._listOptions = {};
	// inner objects: separator
	this._separatorButtonSelectObject = function(that, inner, id, data, pos) {
		this.obj = document.createElement("DIV");
		this.obj.className = "buttonSeparator";
		// this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
		if (isNaN(pos)) {
			inner.polygon.appendChild(this.obj);
		} else {
			if (pos < 1) { pos = 1; }
			if (pos > inner.polygon.childNodes.length) {
				inner.polygon.appendChild(this.obj);
			} else {
				inner.polygon.insertBefore(this.obj, inner.polygon.childNodes[pos-1]);
			}
		}
		self._listOptions[id] = this.obj;
		return this;
	}
	// inner objects: button
	this._buttonButtonSelectObject = function(that, inner, id, data, pos) {
		this.obj = document.createElement("DIV");
		this.obj.en = (data.getAttribute("enabled")=="false"?false:true);
		this.obj._selected = (data.getAttribute("selected")!=null);
		this.obj.className = (this.obj.en?"buttonItem"+(this.obj._selected?"Selected":""):"buttonItemDis");
		var itemText = (data.getAttribute("text")!= null?data.getAttribute("text"):null);
		if (itemText == null) {
			var itm = data.getElementsByTagName("itemText");
			itemText = (itm[0]!=null?itm[0].firstChild.nodeValue:"");
		}
		this.obj.innerHTML = (data.getAttribute("img")!=null?"<img src='"+that.imagePath+data.getAttribute("img")+"' border='0' class='buttonImage'>":"")+"<span>"+itemText+"</span>";//+"<img class='buttonImageFake'>";
		this.obj.onmouseover = function(e) {
			if (!this.en) { return; }
			this.className = "buttonItemOver";
		}
		this.obj.onmouseout = function(e) {
			if (!this.en) { return; }
			if (this._selected == true) {
				this.className = "buttonItemSelected";
			} else {
				this.className = "buttonItem";
			}
		}
		this.obj.idd = String(id);
		this.obj.globalObj = that;
		this.obj.parentObj = inner;
		this.obj.onclick = function(e) {
			e = e||event;
			e.cancelBubble = true;
			if (!this.en) { return; }
			// this.className = "buttonItem";
			self.setListOptionSelected(this.idd.replace(this.globalObj.idPrefix,""));
			//
			self.obj.renderAs = "itemDefault";
			self.p1.childNodes[0].className = self.obj.renderAs;
			self.p2.childNodes[0].className = self.obj.renderAs;
			self.polygon.style.display = "none";
			if (that._isIE6) { self.polygon._ie6cover.style.display = "none"; }
			that.anyUsed = "none";
			// event
			this.globalObj.callEvent("onClick", [this.idd.replace(this.globalObj.idPrefix,"")]);
		}
		// this.obj.onmouseup = this.obj.onclick;
		this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
		if (isNaN(pos)) {
			inner.polygon.appendChild(this.obj);
		} else {
			if (pos < 1) { pos = 1; }
			if (pos > inner.polygon.childNodes.length) {
				inner.polygon.appendChild(this.obj);
			} else {
				inner.polygon.insertBefore(this.obj, inner.polygon.childNodes[pos-1]);
			}
		}
		self._listOptions[id] = this.obj;
		return this;
	}
	// add polygon
	this.polygon = document.createElement("DIV");
	this.polygon.style.display = "none";
	this.polygon.style.zIndex = 101;
	this.polygon.className = "dhtmlxToolbarPoly_"+that.skin;
	for (var q=0; q<data.childNodes.length; q++) {
		if (data.childNodes[q].tagName == "item") {
			var id = (data.childNodes[q].getAttribute("id")!=null?data.childNodes[q].getAttribute("id"):that._genStr(24));
			var type = (data.childNodes[q].getAttribute("type")!=null?"_"+data.childNodes[q].getAttribute("type")+"ButtonSelectObject":that._genStr(24));
			if (this[type] != null) {
				that.objPull[that.idPrefix+id] = new this[type](that, this, id, data.childNodes[q]);
				that.objPull[that.idPrefix+id]["type"] = "buttonSelectNode";
			}
		}
	}
	document.body.appendChild(this.polygon);
	// add poly ie6cover
	if (that._isIE6) {
		this.polygon._ie6cover = document.createElement("IFRAME");
		this.polygon._ie6cover.frameBorder = 0;
		this.polygon._ie6cover.style.position = "absolute";
		this.polygon._ie6cover.style.border = "none";
		this.polygon._ie6cover.style.backgroundColor = "#000000";
		this.polygon._ie6cover.style.filter = "alpha(opacity=100)";
		this.polygon._ie6cover.style.display = "none";
		document.body.appendChild(this.polygon._ie6cover);
	}
	
	// var obj = this.obj;
	// if (_isIE) { document.body.attachEvent("onclick", function(){obj._doOnHidePolygon();}); } else { window.addEventListener("click", function(){obj._doOnHidePolygon();}, false); }
	// functions
	this.setWidth = function(width) {
		// this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.width = width-4+"px";
		// console.log(this.obj.childNodes[0].offsetWidth);
		// this.obj.childNodes[0].style.width=width+"px";
		// this.polygon.style.width = this.obj.childNodes[0].style.width+"px";//this.obj.offsetWidth+"px";
		this.obj.style.width = width+"px";
		this.obj.childNodes[0].style.width = "100%";
		this.polygon.style.width = this.obj.style.width;//this.obj.offsetWidth+"px";
	}
	this.enableItem = function() {
		if (!this.state) {
			this.state = true;
			var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
			var p2 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
			p1.childNodes[0].className = "itemDefault";
			p2.childNodes[0].className = "itemDefault";
			if (this.imgEn != "") {
				// show image
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
			} else {
				// hide image
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
			this.obj.renderAs = "itemDefault";
		}
	}
	this.disableItem = function() {
		if (this.state) {
			this.state = false;
			var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
			var p2 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
			p1.childNodes[0].className = "itemDisabled";
			p2.childNodes[0].className = "itemDisabled";
			if (this.imgDis != "") {
				// show image
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
				
			} else {
				// hide image
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
			this.obj.renderAs = "itemDefault";
		}
	}
	this.isEnabled = function() {
		return this.state;
	}
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	this.setItemText = function(text) {
		/*
		var obj = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[1];
		obj.style.display = (text==""?"none":"");
		obj.childNodes[0].innerHTML = text;
		*/
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		if (text == null || text.length == 0) {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "none";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = "";
		} else {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = text;
		}
	}
	this.getItemText = function() {
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		return p1.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML;;
	}
	/////////
	this.setItemImage = function(url) {
		this.imgEn = url;
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		if (this.state) {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
		}
	}
	this.clearItemImage = function() {
		this.imgEn = "";
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		if (this.state) {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.setItemImageDis = function(url) {
		this.imgDis = url;
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		if (!this.state) {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
		}
	}
	this.clearItemImageDis = function() {
		this.imgDis = "";
		var p1 = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		if (!this.state) {
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			p1.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.addListOption = function(id, pos, type, text, img) {
		if (type == "button") {
			var str = '<item id="'+id+'" type="button" text="'+text+'"'+(img!=null?' img="'+img+'"':'')+'/>';
			var data = that._string2xml(str);
			that.objPull[that.idPrefix+id] = new this._buttonButtonSelectObject(that, this, id, data.childNodes[0], pos);
			that.objPull[that.idPrefix+id]["type"] = "buttonSelectNode";
		} else if (type == "separator") {
			var str = '<item id="'+id+'" type="separator"/>';
			var data = that._string2xml(str);
			that.objPull[that.idPrefix+id] = new this._separatorButtonSelectObject(that, this, id, data.childNodes[0], pos);
			that.objPull[that.idPrefix+id]["type"] = "buttonSelectNode";
		}
	}
	this.removeListOption = function(id) {
		if (that.objPull[that.idPrefix+id] == null) { return; }
		var obj = that.objPull[that.idPrefix+id].obj;
		obj.parentNode.removeChild(obj);
		obj = null;
		delete that.objPull[that.idPrefix+id];
		delete this._listOptions[id];
	}
	this.showListOption = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		that.objPull[id].obj.style.display = "";
	}
	this.hideListOption = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		that.objPull[id].obj.style.display = "none";
	}
	this.isListOptionVisible = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		return (that.objPull[id].obj.style.display != "none");
	}
	this.enableListOption = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		that.objPull[id].obj.en = true;
		that.objPull[id].obj.className = "buttonItem";
	}
	this.disableListOption = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		that.objPull[id].obj.en = false;
		that.objPull[id].obj.className = "buttonItemDis";
	}
	this.isListOptionEnabled = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		return that.objPull[id].obj.en;
	}
	this.setListOptionPosition = function(id, pos) {
		if (this.getListOptionPosition(id) == pos) { return; }
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (pos < 1) { pos = 1; }
		this.polygon.removeChild(that.objPull[id].obj);
		if (pos > this.polygon.childNodes.length) {
			this.polygon.appendChild(that.objPull[id].obj);
		} else {
			this.polygon.insertBefore(that.objPull[id].obj, this.polygon.childNodes[pos-1]);
		}
	}
	this.getListOptionPosition = function(id) {
		id = that.idPrefix + id;
		var pos = -1;
		if (that.objPull[id] == null) { return pos; }
		for (var q=0; q<this.polygon.childNodes.length; q++) { if (this.polygon.childNodes[q] == that.objPull[id].obj) { pos = q+1; } }
		return pos;
	}
	this.setListOptionImage = function(id, img) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		var imgObj = null;
		if ((that.objPull[id].obj.childNodes[0].tagName).toLowerCase() == "img") {
			imgObj = that.objPull[id].obj.childNodes[0];
		} else {
			imgObj = document.createElement("IMG");
			imgObj.className = "buttonImage";
			imgObj.border = "0";
			that.objPull[id].obj.insertBefore(imgObj, that.objPull[id].obj.childNodes[0]);
		}
		imgObj.src = that.imagePath+img;
	}
	this.getListOptionImage = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		var img = null;
		if ((that.objPull[id].obj.childNodes[0].tagName).toLowerCase() == "img") { img = that.objPull[id].obj.childNodes[0].src; }
		return img;
	}
	this.clearListOptionImage = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		if ((that.objPull[id].obj.childNodes[0].tagName).toLowerCase() == "img") {
			var imgObj = that.objPull[id].obj.childNodes[0];
			that.objPull[id].obj.removeChild(imgObj);
			imgObg = null;
		}
	}
	this.setListOptionText = function(id, text) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		var obj = that.objPull[id].obj;
		for (var q=0; q<obj.childNodes.length; q++) { if ((obj.childNodes[q].tagName).toLowerCase() == "span") { obj.childNodes[q].innerHTML = text; } }
	}
	this.getListOptionText = function(id) {
		var text = "";
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		var obj = that.objPull[id].obj;
		for (var q=0; q<obj.childNodes.length; q++) { if ((obj.childNodes[q].tagName).toLowerCase() == "span") { text = obj.childNodes[q].innerHTML; } }
		return text;
	}
	this.setListOptionToolTip = function(id, tip) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		that.objPull[id].obj.title = tip;
	}
	this.getListOptionToolTip = function(id) {
		id = that.idPrefix + id;
		if (that.objPull[id] == null) { return; }
		if (that.objPull[id].obj.className == "buttonSeparator") { return; }
		return that.objPull[id].obj.title;
	}
	this.setItemToolTip = function(tip) {
		this.obj.title = tip;
	}
	this.getItemToolTip = function() {
		return this.obj.title;
	}
	this.forEachListOption = function(handler) {
		for (var a in this._listOptions) { handler(a); }
	}
	this.getAllListOptions = function() {
		var listData = new Array();
		for (var a in this._listOptions) { listData[listData.length] = a; }
		return listData;
	}
	this.setListOptionSelected = function(id) {
		for (var a in this._listOptions) {
			if (a == id) {
				this._listOptions[a]._selected = true;
				if (this._listOptions[a].className == "buttonItem") { this._listOptions[a].className = "buttonItemSelected"; }
			} else {
				this._listOptions[a]._selected = false;
				if (this._listOptions[a].className == "buttonItemSelected") { this._listOptions[a].className = "buttonItem"; }
			}
		}
	}
	this.getListOptionSelected = function() {
		var id = null;
		for (var a in this._listOptions) { if (this._listOptions[a]._selected == true) { id = a; } }
		return id;
	}
	//
	return this;
}
//#}
	
//#tool_input:06062008{
/*****************************************************************************************************************************************************************
	object: buttonInput
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._buttonInputObject = function(that, id, data, pos) {
	if (that.layout == "VER") { return; }
	//
	this.id = that.idPrefix+id;
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.idd = String(id);
	this.obj.w = (data.getAttribute("width")!=null?data.getAttribute("width"):100);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	//
	this.obj.innerHTML = "<table cellspacing='0' cellpadding='0' class='itemDefault'>"+
				"<tr>"+
					"<td valign='middle'"+(this.img!=""?"":" style='display: none;'")+">"+/*CH hacks start*/"<span>"+(data.getAttribute("text")!=null?data.getAttribute("text"):"")+"</span>"+/*CH hacks end*/
						"<input class='dhtmlxToolbarInp' type='text' style='width:"+this.obj.w+"px;'"+(data.getAttribute("value")!=null?" value='"+data.getAttribute("value")+"'":"")+">"+
					"</td>"+
				"</tr>"+
			"</table>";
	
	var th = that;
	var self = this;
	this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].onkeydown = function(e) {
		e = e||event;
		if (e.keyCode == 13) { th.callEvent("onEnter", [self.obj.idd, this.value]); }
	}
	// add
	this.tr = that._addObject(this.obj, pos);
	//
	this.enableItem = function() {
		this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].disabled = false;
	}
	this.disableItem = function() {
		this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].disabled = true;
	}
	this.isEnabled = function() {
		return (!this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].disabled);
	}
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display != "none");
	}
	this.setValue = function(value) {
		this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].value = value;
	}
	this.getValue = function() {
		return this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].value;
	}
	this.setWidth = function(width) {
		this.obj.w = width;
		this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.width = this.obj.w+"px";
	}
	this.getWidth = function() {
		return this.obj.w;
	}
	this.setItemToolTip = function(tip) {
		this.obj.title = tip;
	}
	this.getItemToolTip = function() {
		return this.obj.title;
	}
	//
	return this;
}
//#}
//#tool_2state:06062008{
/*****************************************************************************************************************************************************************
	object: buttonTwoState
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._buttonTwoStateObject = function(that, id, data, pos) {
	this.id = that.idPrefix+id;
	this.state = (data.getAttribute("enabled")!=null?false:true);
	this.imgEn = (data.getAttribute("img")!=null?data.getAttribute("img"):"");
	this.imgDis = (data.getAttribute("imgdis")!=null?data.getAttribute("imgdis"):"");
	this.img = (this.state?(this.imgEn!=""?this.imgEn:""):(this.imgDis!=""?this.imgDis:""));
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.renderAs = "itemDefault";
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.obj.idd = String(id);
	this.obj.title = (data.getAttribute("title")!=null?data.getAttribute("title"):"");
	this.obj.pressed = (data.getAttribute("selected")!=null);
	if (this.obj.pressed) { this.obj.renderAs = "itemOver"; }
	this.obj.innerHTML = "<table cellspacing='0' cellpadding='0' class='"+(this.obj.pressed?"itemPressed":"item"+(this.state?"Default":"Disabled"))+"'>"+
				"<tr>"+
					"<td class='dhtmlxToolbarIMG' valign='middle'"+(this.img!=""?"":" style='display: none;'")+"><img src='"+(this.img!=""?that.imagePath+this.img:that.imagePath+that.emptyImage)+"'></td>"+
					"<td class='dhtmlxToolbarTEXT' valign='middle'"+(data.getAttribute("text")!=null?"":" style='display: none;'")+"><span>"+(data.getAttribute("text")!=null?data.getAttribute("text"):"")+"</span></td>"+
				"</tr>"+
			"</table>";
	this.obj.that = this;
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	this.obj.onmouseover = function() { this._doOnMouseOver(); }
	this.obj.onmouseout = function() { this._doOnMouseOut(); }
	this.obj._doOnMouseOver = function() {
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		if (this.pressed) { return; }
		this.childNodes[0].className = "itemOver";
		this.renderAs = "itemOver";
	}
	this.obj._doOnMouseOut = function() {
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		if (this.pressed) { return; }
		this.childNodes[0].className = "itemDefault";
		this.renderAs = "itemDefault";
	}
	this.obj.onmousedown = function(e) {
		if (that.checkEvent("onBeforeStateChange")) {
			if (!that.callEvent("onBeforeStateChange", [this.idd.replace(that.idPrefix, ""), this.pressed])) { return; }
		}
		//
		if (this.that.state == false) { return; }
		if (that.anyUsed != "none") { return; }
		this.pressed = !this.pressed;
		this.childNodes[0].className = (this.pressed?"itemPressed":this.renderAs);
		// event
		that.callEvent("onStateChange", [this.idd.replace(that.idPrefix, ""), this.pressed]);
		return false;
	}
	this.obj.onmouseup = function(e) {
		
	}
	// add object
	this.tr = that._addObject(this.obj, pos);
	// functions
	this.setItemState = function(state, callEvent) {
		if (this.obj.pressed != state) {
			this.obj.pressed = state;
			this.obj.childNodes[0].className = (this.obj.pressed?"itemPressed":"itemDefault");
			// event
			if (callEvent == true) { that.callEvent("onStateChange", [this.obj.idd.replace(that.idPrefix, ""), this.obj.pressed]); }
		}
	}
	this.getItemState = function() {
		return this.obj.pressed;
	}
	this.enableItem = function() {
		if (!this.state) {
			this.state = true;
			this.obj.childNodes[0].className = (this.obj.pressed?"itemPressed":"itemDefault");
			if (this.imgEn != "") {
				// show image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
			} else {
				// hide image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
		}
	}
	this.disableItem = function() {
		if (this.state) {
			this.state = false;
			this.obj.childNodes[0].className = "itemDisabled";
			if (this.imgDis != "") {
				// show image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
			} else {
				// hide image
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
			}
			this.obj.renderAs = "itemDefault";
		}
	}
	this.isEnabled = function() {
		return this.state;
	}
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	this.setItemText = function(text) {
		if (text == null || text.length == 0) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = "";
		} else {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML = text;
		}
	}
	this.getItemText = function() {
		return this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].innerHTML;
	}
	this.setItemImage = function(url) {
		this.imgEn = url;
		if (this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgEn;
		}
	}
	this.clearItemImage = function() {
		this.imgEn = "";
		if (this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.setItemImageDis = function(url) {
		this.imgDis = url;
		if (!this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+this.imgDis;
		}
	}
	this.clearItemImageDis = function() {
		this.imgDis = "";
		if (!this.state) {
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = that.imagePath+that.emptyImage;
		}
	}
	this.setItemToolTip = function(tip) {
		this.obj.title = tip;
	}
	this.getItemToolTip = function() {
		return this.obj.title;
	}
	//
	return this;
}
//#}
//#tool_slider:06062008{
/*****************************************************************************************************************************************************************
	object: slider
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype._sliderObject = function(that, id, data, pos) {
	this.id = that.idPrefix+id;
	this.state = (data.getAttribute("enabled")!=null?false:true);
	this.obj = document.createElement("TD");
	this.obj.className = "dhtmlxToolbarItem";
	this.obj.align = "left";
	this.obj.valign = "middle";
	this.obj.idd = String(id);
	this.obj.len = (data.getAttribute("length")!=null?Number(data.getAttribute("length")):50);
	//
	this.obj.innerHTML = "<table border='0' cellspacing='0' cellpadding='0' class='item"+(this.state?"Default":"Disabled")+"'>"+
				"<tr>"+
					"<td class='dhtmlxToolbarTEXT' valign='middle'"+(data.getAttribute("textMin")!=null?"":" style='display: none;'")+"><span>"+(data.getAttribute("textMin")!=null?data.getAttribute("textMin"):"")+"</span></td>"+
					"<td>"+
						"<div style='position: relative;'>"+
							"<table border='0' cellspacing='0' cellpadding='0' style='width:"+this.obj.len+"px;'>"+
								"<tr>"+
									"<td class='dhtmlxToolbarSliderBarLeft'>&nbsp;</td>"+
									"<td class='dhtmlxToolbarSliderBarMiddle'>&nbsp;</td>"+
									"<td class='dhtmlxToolbarSliderBarRight'>&nbsp;</td>"+
								"</tr>"+
							"</table>"+
							"<div class='dhtmlxToolbarBarSliderPen'>&nbsp;</div>"+
						"</div>"+
					"</td>"+
					"<td class='dhtmlxToolbarTEXT' valign='middle'"+(data.getAttribute("textMax")!=null?"":" style='display: none;'")+"><span>"+(data.getAttribute("textMax")!=null?data.getAttribute("textMax"):"")+"</span></td>"+
				"</tr>"+
			"</table>";
	// add object
	this.tr = that._addObject(this.obj, pos);
	//
	this.obj.onselectstart = function(e) { e = e||event; e.returnValue = false; }
	//
	// this.obj.pen = this.obj.childNodes[0].childNodes[1];
	// console.log(this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[1])
	
	this.obj.pen = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[1];
	
	//
	var self = this;
	// this.obj.pen.vert = (that.layout=="VER");
	this.obj.pen.obj = this.obj;
	//
	this.obj.pen.valueMin = (data.getAttribute("valueMin")!=null?Number(data.getAttribute("valueMin")):0);
	this.obj.pen.valueMax = (data.getAttribute("valueMax")!=null?Number(data.getAttribute("valueMax")):100);
	if (this.obj.pen.valueMin >= this.obj.pen.valueMax) { this.obj.pen.valueMin = this.obj.pen.valueMax - 1; }
	this.obj.pen.valueNow = (data.getAttribute("valueNow")!=null?Number(data.getAttribute("valueNow")):this.obj.pen.valueMax);
	if (this.obj.pen.valueNow >= this.obj.pen.valueMax) {this.obj.pen.valueNow = this.obj.pen.valueMax; }
	if (this.obj.pen.valueNow <= this.obj.pen.valueMin) {this.obj.pen.valueNow = this.obj.pen.valueMin; }
	//
	this.obj.pen._detectLimits = function() {
		this.minX = 0;
		this.maxX = this.parentNode.parentNode.childNodes[0].offsetWidth-this.offsetWidth;
	}
	this.obj.pen._detectLimits();
	//
	this.obj.that = this;
	//
	this.obj.pen.initXY = 0;
	//
	this.obj.pen.that = this;
	this.obj.pen.allowMove = false;
	this.obj.pen.onmousedown = function(e) {
		if (this.that.state == false) { return; }
		e = e||event;
		this.initXY = e.clientX;
		this.newValueNow = this.valueNow;
		this.allowMove = true;
		this.className = "dhtmlxToolbarBarSliderPenOver";
		// IE fix
		self.obj.childNodes[0].className = (self.state?"itemDefault":"itemDisabled");
		//
		if (this.label.tip != "") {
			this.label.style.visibility = "hidden";
			this.label.style.display = "";
			this.label.innerHTML = this.label.tip.replace("%v", this.valueNow);
			this.label.style.left = Math.round(getAbsoluteLeft(this)+this.offsetWidth/2-this.label.offsetWidth/2)+"px";
			this.label.style.top = getAbsoluteTop(this)-this.label.offsetHeight-3+"px";
			this.label.style.visibility = "";
		}
	}
	this.obj.pen._doOnMouseMoveStart = function(evnt) {
		if (!this.allowMove) { return; }
		var ofst = (this.vert?evnt.clientY:evnt.clientX) - this.initXY;
		// mouse goes out to left/right from pen
		if (evnt.clientX < getAbsoluteLeft(this)+Math.round(this.offsetWidth/2) && this.nowX == this.minX) { return; }
		if (evnt.clientX > getAbsoluteLeft(this)+Math.round(this.offsetWidth/2) && this.nowX == this.maxX) { return; }
		this.newNowX = this.nowX + ofst;
		if (this.newNowX < this.minX) { this.newNowX = this.minX; }
		if (this.newNowX > this.maxX) { this.newNowX = this.maxX; }
		this.nowX = this.newNowX;
		this.style.left = this.nowX+"px";
		this.initXY = (this.vert?evnt.clientY:evnt.clientX);
		this.newValueNow = Math.round((this.valueMax-this.valueMin)*(this.newNowX-this.minX)/(this.maxX-this.minX)+this.valueMin);
		if (this.label.tip != "") {
			this.label.innerHTML = this.label.tip.replace(/%v/gi, this.newValueNow);
			this.label.style.left = Math.round(getAbsoluteLeft(this)+this.offsetWidth/2-this.label.offsetWidth/2)+"px";
			this.label.style.top = getAbsoluteTop(this)-this.label.offsetHeight-3+"px";
		}
	}
	this.obj.pen._doOnMouseMoveEnd = function() {
		if (!this.allowMove) { return; }
		this.className = "dhtmlxToolbarBarSliderPen";
		// IE fix
		self.obj.childNodes[0].className = (self.state?"itemDefault":"itemDisabled");
		//
		if (this.label.tip != "") { this.label.style.display = "none"; }
		this.allowMove = false;
		this.nowX = this.newNowX;
		this.valueNow = this.newValueNow;
		// event
		that.callEvent("onValueChange", [this.obj.idd.replace(that.idPrefix, ""), this.valueNow]);
	}
	this.obj.pen._definePos = function() {
		this.nowX = Math.round((this.valueNow-this.valueMin)*(this.maxX-this.minX)/(this.valueMax-this.valueMin)+this.minX);
		this.style.left = this.nowX+"px";
		this.newNowX = this.nowX;
	}
	this.obj.pen._definePos();
	//
	this.obj.pen.label = document.createElement("DIV");
	this.obj.pen.label.className = "dhtmlxToolbarSliderPenLabel_"+that.skin;
	this.obj.pen.label.style.display = "none";
	this.obj.pen.label.tip = (data.getAttribute("toolTip")!=null?data.getAttribute("toolTip"):"%v");
	document.body.appendChild(this.obj.pen.label);
	//
	var pen = this.obj.pen;
	//
	if (_isIE) {
		document.body.attachEvent("onmousemove", function(e){e=e||event;pen._doOnMouseMoveStart(e);});
		document.body.attachEvent("onmouseup", function(){pen._doOnMouseMoveEnd();});
	} else {
		window.addEventListener("mousemove", function(e){e=e||event;pen._doOnMouseMoveStart(e);}, false);
		window.addEventListener("mouseup", function(){pen._doOnMouseMoveEnd();}, false);
	}
	// functions
	this.enableItem = function() {
		if (!this.state) {
			this.state = true;
			this.obj.childNodes[0].className = "itemDefault";
		}
	}
	this.disableItem = function() {
		if (this.state) {
			this.state = false;
			this.obj.childNodes[0].className = "itemDisabled";
		}
	}
	this.isEnabled = function() {
		return this.state;
	}
	this.showItem = function() {
		this.tr.style.display = "";
	}
	this.hideItem = function() {
		this.tr.style.display = "none";
	}
	this.isVisible = function() {
		return (this.tr.style.display == "");
	}
	this.setValue = function(value, callEvent) {
		if (value < this.obj.pen.valueMin) { value = this.obj.pen.valueMin; }
		if (value > this.obj.pen.valueMax) { value = this.obj.pen.valueMax; }
		this.obj.pen.valueNow = Number(value);
		this.obj.pen._definePos();
		// event
		if (callEvent == true) {
			that.callEvent("onValueChange", [this.obj.idd.replace(that.idPrefix, ""), this.obj.pen.valueNow]);
		}
	}
	this.getValue = function() {
		return this.obj.pen.valueNow;
	}
	this.setMinValue = function(value, label) {
		var obj = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		obj.innerHTML = label;
		obj.style.display = (label.length>0?"":"none");
		//
		this.obj.pen.valueMin = Number(value);
		this.obj.pen._detectLimits();
		this.obj.pen._definePos();
	}
	this.setMaxValue = function(value, label) {
		var obj = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[2].childNodes[0];
		obj.innerHTML = label;
		obj.style.display = (label.length>0?"":"none");
		//
		this.obj.pen.valueMax = Number(value);
		this.obj.pen._detectLimits();
		this.obj.pen._definePos();
	}
	this.getMinValue = function() {
		var label = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].innerHTML;
		var value = this.obj.pen.valueMin;
		return new Array(value, label);
	}
	this.getMaxValue = function() {
		var label = this.obj.childNodes[0].childNodes[0].childNodes[0].childNodes[2].childNodes[0].innerHTML;
		var value = this.obj.pen.valueMax;
		return new Array(value, label);
	}
	this.setItemToolTipTemplate = function(template) {
		this.obj.pen.label.tip = template;
	}
	this.getItemToolTipTemplate = function() {
		return this.obj.pen.label.tip;
	}
	//
	return this;
}
//#}
/*****************************************************************************************************************************************************************
	event handler
***************************************************************************************************************************************************************** */
dhtmlXToolbarObject.prototype.dhx_Event = function() {
	this.dhx_SeverCatcherPath="";
	/**
	*   @desc: attach an event handler to webbar
	*   @param: original - event original name
	*   @param: catcher - event handler
	*   @param: CallObj - object which will call event
	*   @type: public
	*/
	this.attachEvent = function(original, catcher, CallObj) {
		original = original.toLowerCase();
		CallObj = CallObj||this;
		original = 'ev_'+original;
		if ((!this[original]) || (!this[original].addEvent)) {
			var z = new this.eventCatcher(CallObj);
			z.addEvent(this[original]);
			this[original] = z;
		}
		return (original + ':' + this[original].addEvent(catcher)); //return ID (event name & event ID)
	}
	this.callEvent = function(name,arg0) {
		name = name.toLowerCase();
		if (this["ev_"+name]) { return this["ev_"+name].apply(this,arg0); }
		return true;
	}
	/**
	*   @desc: returns true if event exists
	*   @param: name - event name
	*   @type: public
	*/
	this.checkEvent = function(name) {
		name = name.toLowerCase();
		if (this["ev_"+name]) { return true; }
		return false;
	}
	this.eventCatcher = function(obj) {
		var dhx_catch = new Array();
		var m_obj = obj;
		var z = function() {
			if (dhx_catch) var res = true;
			for (var i=0; i<dhx_catch.length; i++) { if (dhx_catch[i] != null) { var zr = dhx_catch[i].apply(m_obj, arguments); res = res && zr; } }
			return res;
		}
		z.addEvent = function(ev) {
			if (typeof(ev) != "function") ev = eval(ev);
			if (ev) return dhx_catch.push( ev ) - 1;
			return false;
                }
		z.removeEvent = function(id) { dhx_catch[id] = null; }
		return z;
	}
	/**
	*   @desc: removes an event handler
	*   @param: id - event id
	*   @type: public
	*/
	this.detachEvent = function(id) {
		if (id != false) {
			var list = id.split(':'); //get EventName and ID
			this[list[0]].removeEvent(list[1]); //remove event
		}
	}
};
dhtmlXToolbarObject.prototype.hotkeyManager = function() {
	
	this._hkPool = {};
	this.attachHotkey = function(hkey, obj) {
		
	}
	
	var that = this;
	if (_isIE) {
		document.attachEvent("onkeyup", that.hotkeyAction);
	} else {
		window.addEventListener("keyup", that.hotkeyAction, false);
	}
	
}
dhtmlXToolbarObject.prototype.hotkeyAction = function(e) {
	e = e||event;
	e.cancelBubble = true;
	e.returnValue = false;
	
}
