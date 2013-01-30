//v.2.0 build 81107

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
/**
*	@desc: constructor, creates dhtmlxlayout panel
*	@pseudonym: td
*	@type: public
*/
function dhtmlXLayoutPanel(){
	
}

/**
*	@desc: constructor, creats a new dhtmlXLayout object
*	@param: base - object/object id, document.body or dhtmlxWindow - the layout will be attached to it
*	@param: view - layout's pattern
*	@param: skin - skin
*	@type: public
*/
function dhtmlXLayoutObject(base, view, skin) {
	
	// 1. object/objectId - window without borders
	// 2. document.body - fullscreened window
	// 3. window object - simple attach
	
	var that = this;
	
	this.skin = (skin!=null?skin:"dhx_blue");
	
	if (typeof(base) == "string") { base = document.getElementById(base); }
	
	if (!base._skipChecksOnStartUp) {
		// check if layout cell
		if (base._isLayoutCell == true) {
			this._updateDimensions = true;
			base = base.window;
		}
		// check if window
		if (base._isWindow == true) {
			var layout = base.attachLayout(view, this.skin);
			if (this._updateDimensions == true) { layout._dimension = new Array(0, 0); }
			return layout;
		} else if (base == document.body) {
			document.body.style.overflow = "hidden";
			this.parentDhxWins = new dhtmlXWindows();
			this.parentDhxWins.setSkin(this.skin);
			this.parentDhxWindow = this.parentDhxWins.createWindow("parentDhxWins", 10, 10, 800, 600);
			this.parentDhxWindow.setToFullScreen(true);
			var layout = this.parentDhxWindow.attachLayout(view, this.skin);
			return layout;
		} else if (typeof(base) == "object") {
			this.parentDhxWins = new dhtmlXWindows();
			this.parentDhxWins.setSkin(this.skin);
			this.parentDhxWins.enableAutoViewport(false);
			this.parentDhxWins.setViewport(0, 0, base.offsetWidth, base.offsetHeight, base);
			this.parentDhxWindow = this.parentDhxWins.createWindow("parentDhxWins", 0, 0, base.offsetWidth, base.offsetHeight);
			this.parentDhxWindow.denyMove();
			this.parentDhxWindow.denyResize();
			this.parentDhxWindow.denyPark();
			this.parentDhxWindow.button("close").disable();
			this.parentDhxWins._attachWindowContentTo(this.parentDhxWindow, base);
			var globalParent = this;
			// this.parentDhxWindow.setToFullScreen(true);
			this.layout = this.parentDhxWindow.attachLayout(view, this.skin);
			this.layout.globalParent = globalParent;
			return this.layout;
		}
	}
	
	if (_isOpera) {
		this._opera950FixBorder = "#FFFFFF 0px solid";
		switch (this.skin) {
			case "dhx_black":
				this._opera950FixBorder = "#333333 1px solid";
				break;
			case "dhx_blue":
				this._opera950FixBorder = "#D3E2E5 1px solid";
				break;
		}
	}
	
	this.items = new Array();
	/**
	*	@desc: returns cell's object by cell's id
	*	@param: id - cell's id
	*	@type: public
	*/
	this.cells = function(id) {
		if (this.polyObj[id] != null) { return this.polyObj[id]; }
		return null;
	}
	/**
	*	@desc: returns cell's id by index
	*	@param: ind - cell's index
	*	@type: public
	*/
	this.getIdByIndex = function(ind) {
		if (ind < 0) return null;
		if (ind >= this.items.length) return null;
		// return this.items[ind]._link;
		return this.items[ind]._idd;
	}
	/**
	*	@desc: returns cell's index by id
	*	@param: id - cell's id
	*	@type: public
	*/
	this.getIndexById = function(id) {
		if (this.cells(id) != null) return this.cells(id).getIndex();
		return null;
	}
	
	this.base = base; // (typeof(base)=="object"?base:document.getElementById(base));
	
	this.imagePath = globalBaseUrl+"/img/";
	/**
	*	@desc: set path to images
	*	@param: path - path on hard disk
	*	@type: public
	*/
	this.setImagePath = function(path) {
		this.imagePath = path;
	}
	
	// if (parentWindow != null) { this._parentWindow = parentWindow; }
	
	this.polyObj = {};
	this.sepHor = new Array();
	this.sepVer = new Array();
	
	this._layoutView = (view!=null?String(view).toUpperCase():"3E");
	
	this._minWidth = 40;
	this._minHeight = 40;
	//
	this._CPanelBtnsWidth = 32;
	//
	// this._collapsedW = 7;//20;
	// this._collapsedH = (_isFF?7:8);//(_isFF?20:22);
	
	this.skinParams = { "standard"		: {"hor_sep_height": 6, "cpanel_height": 31, "cpanel_collapsed_width": 20, "cpanel_collapsed_height": (_isFF?20:22)},
			    "glassy_blue"	: {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "glassy_caramel"	: {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "glassy_greenapple"	: {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "glassy_rainy"	: {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "glassy_raspberries": {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "glassy_yellow"	: {"hor_sep_height": 4, "cpanel_height": 23, "cpanel_collapsed_width":  7, "cpanel_collapsed_height": (_isFF?7:8)},
			    "dhx_black"		: {"hor_sep_height": 5, "cpanel_height": (_isOpera?34:34), "cpanel_collapsed_width":  18, "cpanel_collapsed_height": 18},
			    "dhx_blue"		: {"hor_sep_height": 5, "cpanel_height": (_isOpera?34:34), "cpanel_collapsed_width":  18, "cpanel_collapsed_height": 18}
	};
	// ff - 34,18
	// ie - 34,18
	// safari - 34,18
	// opera ?
	this._CPanelHeight = this.skinParams[this.skin]["cpanel_height"];
	this._collapsedW = this.skinParams[this.skin]["cpanel_collapsed_width"];
	this._collapsedH = this.skinParams[this.skin]["cpanel_collapsed_height"];
	//
	this.tpl = document.createElement("TABLE");
	this.tpl.className = "dhtmlxLayoutPolyContainer_"+this.skin;
	this.tpl.cellSpacing = 0;
	this.tpl.cellPadding = 0;
	var bd = document.createElement("TBODY");
	this.tpl.appendChild(bd);
	this.tpl.border = 0;
	//
	this.tplSizes = {};
	this.tplData = {"1C": '<layout><autosize hor="a" ver="a" rows="1" cols="1"/><row><cell obj="a" resize="ver" neighbors="a"/></row></layout>',
			"2E": '<layout><autosize hor="a;b" ver="b" rows="2" cols="1"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b"/></row><row sep="true"><cell sep="hor" top="a" bottom="b" dblclick="a"/></row><row><cell obj="b" b_height="*" resize="ver" neighbors="a;b"/></row></layout>',
			"2U": '<layout><autosize hor="b" ver="a;b" rows="1" cols="2"/><row><cell obj="a" a_width="*" resize="hor" neighbors="a;b"/><cell sep="ver" left="a" right="b"/><cell obj="b" b_width="*" resize="hor" neighbors="a;b"/></row></layout>',
			// ---------------------------------------------------------------------------------------------------------------------------------------
			"3E": '<layout><autosize hor="a;b;c" ver="c" rows="3" cols="1"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b;c"/></row><row sep="yes"><cell sep="hor" top="a" bottom="b;c" dblclick="a"/></row><row><cell obj="b" b_height="*" resize="ver" neighbors="a;b;c"/></row><row sep="yes"><cell sep="hor" top="a;b" bottom="c" dblclick="b"/></row><row><cell obj="c" c_height="*" resize="ver" neighbors="a;b;c"/></row></layout>',
			"3J": '<layout><autosize hor="b" ver="b;c" rows="2" cols="2"/><row><cell obj="a" a_width="*" a_height="*" resize="ver" neighbors="a;c"/><cell sep="ver" left="a,c" right="b" dblclick="b" rowspan="3"/><cell obj="b" b_width="*" resize="hor" neighbors="a,c;b" rowspan="3"/></row><row sep="yes"><cell sep="hor" top="a" bottom="c" dblclick="a"/></row><row><cell obj="c" c_width="*" c_height="*" resize="ver" neighbors="a;c"/></row></layout>',
			"3L": '<layout><autosize hor="b;c" ver="a;c" rows="2" cols="2"/><row><cell obj="a" a_width="*" resize="hor" neighbors="a;b,c" rowspan="3"/><cell sep="ver" left="a" right="b,c" dblclick="a" rowspan="3"/><cell obj="b" b_width="*" b_height="*" resize="ver" neighbors="b;c"/></row><row sep="true"><cell sep="hor" top="b" dblclick="b" bottom="c"/></row><row><cell obj="c" c_width="*" c_height="*" resize="ver" neighbors="b;c"/></row></layout>',
			"3T": '<layout><autosize hor="a;c" ver="b;c" rows="2" cols="2"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b,c" colspan="3"/></row><row sep="true"><cell sep="hor" top="a" bottom="b,c" dblclick="a" colspan="3"/></row><row><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="b;c"/><cell sep="ver" left="b" right="c" dblclick="b"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="b;c"/></row></layout>',
			"3U": '<layout><autosize hor="b;c" ver="c" rows="2" cols="2"/><row><cell obj="a" a_width="*" a_height="*" resize="hor" neighbors="a;b"/><cell sep="ver" left="a" right="b" dblclick="a"/><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="a;b"/></row><row sep="true"><cell sep="hor" top="a,b" bottom="c" dblclick="c" colspan="3"/></row><row><cell obj="c" c_height="*" resize="ver" neighbors="a,b;c" colspan="3"/></row></layout>',
			"3W": '<layout><autosize hor="c" ver="a;b;c" rows="1" cols="3"/><row><cell obj="a" a_width="*" resize="hor" neighbors="a;b;c"/><cell sep="ver" left="a" right="b;c" dblclick="a"/><cell obj="b" b_width="*" resize="hor" neighbors="a;b;c"/><cell sep="ver" left="a;b" right="c" dblclick="b"/><cell obj="c" c_width="*" resize="hor" neighbors="a;b;c"/></row></layout>',
			// ---------------------------------------------------------------------------------------------------------------------------------------
			"4H": '<layout><autosize hor="d" ver="a;c;d" rows="2" cols="3"/><row><cell obj="a" a_width="*" resize="hor" neighbors="a;b,c;d" rowspan="3"/><cell sep="ver" left="a" right="b,c;d" dblclick="a" rowspan="3"/><cell obj="b" b_width="*" b_height="*" resize="ver" neighbors="b;c"/><cell sep="ver" left="a;b,c" right="d" dblclick="d" rowspan="3"/><cell obj="d" d_width="*" resize="hor" neighbors="a;b,c;d" rowspan="3"/></row><row sep="true"><cell sep="hor" top="b" dblclick="b" bottom="c"/></row><row><cell obj="c" c_width="*" c_height="*" resize="ver" neighbors="b;c"/></row></layout>',
			"4I": '<layout><autosize hor="a;c;d" ver="d" rows="3" cols="2"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b,c;d" colspan="4"/></row><row sep="true"><cell sep="hor" top="a" bottom="b,c;d" dblclick="a" colspan="4"/></row><row><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="b;c"/><cell sep="ver" left="b" dblclick="b" right="c"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="b;c"/></row><row sep="true"><cell sep="hor" top="a;b,c" bottom="d" dblclick="d" colspan="4"/></row><row><cell obj="d" d_height="*" resize="ver" neighbors="a;b,c;d" colspan="4"/></row></layout>',
			"4T": '<layout><autosize hor="a;d" ver="b;c;d" rows="2" cols="3"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b,c,d" colspan="5"/></row><row sep="true"><cell sep="hor" top="a" bottom="b,c,d" dblclick="a" colspan="5"/></row><row><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="b;c;d"/><cell sep="ver" left="b" right="c;d" dblclick="b"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="b;c;d"/><cell sep="ver" left="b;c" right="d" dblclick="c"/><cell obj="d" d_width="*" d_height="*" resize="hor" neighbors="b;c;d"/></row></layout>',
			"4U": '<layout><autosize hor="c;d" ver="d" rows="2" cols="3"/><row><cell obj="a" a_width="*" a_height="*" resize="hor" neighbors="a;b;c"/><cell sep="ver" left="a" right="b;c" dblclick="a"/><cell obj="b" c_width="*" b_height="*" resize="hor" neighbors="a;b;c"/><cell sep="ver" left="a;b" right="c" dblclick="b"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="a;b;c"/></row><row sep="true"><cell sep="hor" top="a,b,c" bottom="d" dblclick="d" colspan="5"/></row><row><cell obj="d" d_height="*" resize="ver" neighbors="a,b,c;d" colspan="5"/></row></layout>',
			// ---------------------------------------------------------------------------------------------------------------------------------------
			"5H": '<layout><autosize hor="b;c;d" ver="a;c;e" rows="3" cols="3"/><row><cell obj="a" a_width="*" resize="hor" neighbors="a;b,c,d" rowspan="5"/><cell sep="ver" left="a" right="b,c,d;e" dblclick="a" rowspan="5"/><cell obj="b" b_width="*" b_height="*" resize="ver" neighbors="b;c;d"/><cell sep="ver" left="a;b,c,d" right="e" dblclick="e" rowspan="5"/><cell obj="e" e_width="*" resize="hor" neighbors="b,c,d;e" rowspan="5"/></row><row sep="true"><cell sep="hor" top="b" dblclick="b" bottom="c;d"/></row><row><cell obj="c" c_width="*" c_height="*" resize="ver" neighbors="b;c;d"/></row><row sep="true"><cell sep="hor" top="b;c" dblclick="c" bottom="d"/></row><row><cell obj="d" d_width="*" d_height="*" resize="ver" neighbors="b;c;d"/></row></layout>',
			"5I": '<layout><autosize hor="a;d;e" ver="e" rows="3" cols="3"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b,c,d;e" colspan="5"/></row><row sep="match"><cell sep="hor" top="a" bottom="b,c,d;e" dblclick="a" colspan="5"/></row><row><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="b;c;d"/><cell sep="ver" left="b" right="c;d" dblclick="b"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="b;c;d"/><cell sep="ver" left="b;c" right="d" dblclick="c"/><cell obj="d" d_width="*" d_height="*" resize="hor" neighbors="b;c;d"/></row><row sep="match"><cell sep="hor" top="a;b,c,d" bottom="e" dblclick="e" colspan="5"/></row><row><cell obj="e" e_height="*" resize="ver" neighbors="a;b,c,d;e" colspan="5"/></row></layout>',
			// ---------------------------------------------------------------------------------------------------------------------------------------
			"6I": '<layout><autosize hor="a;e;f" ver="f" rows="3" cols="4"/><row><cell obj="a" a_height="*" resize="ver" neighbors="a;b,c,d,e;f" colspan="7"/></row><row sep="true"><cell sep="hor" top="a" bottom="b,c,d,e;f" dblclick="a" colspan="7"/></row><row><cell obj="b" b_width="*" b_height="*" resize="hor" neighbors="b;c;d;e"/><cell sep="ver" left="b" right="c;d;e" dblclick="b"/><cell obj="c" c_width="*" c_height="*" resize="hor" neighbors="b;c;d;e"/><cell sep="ver" left="b;c" right="d;e" dblclick="c"/><cell obj="d" d_width="*" d_height="*" resize="hor" neighbors="b;c;d;e"/><cell sep="ver" left="b;c;d" right="e" dblclick="d"/><cell obj="e" e_width="*" e_height="*" resize="hor" neighbors="b;c;d;e"/></row><row sep="true"><cell sep="hor" top="a;b,c,d,e" bottom="f" dblclick="f" colspan="7"/></row><row><cell obj="f" f_height="*" resize="ver" neighbors="a;b,c,d,e;f" colspan="7"/></row></layout>'
	};
	this._effects = { "collapse": false, "resize": false, "highlight": true };
	
	this.sizer = document.createElement("DIV");
	this.sizer.className = "dhxLayout_Sizer_"+this.skin;
	this.sizer.style.display = "none";
	document.body.appendChild(this.sizer);
	
	this._attachSizer = function(obj) {
		that.sizer.style.left = getAbsoluteLeft(obj)+"px";
		that.sizer.style.top = getAbsoluteTop(obj)+"px";
		that.sizer.style.width = obj.offsetWidth+"px";
		that.sizer.style.height = obj.offsetHeight+"px";
		that.sizer.style.display = "";
		that.sizer.className = "dhxLayout_Sizer_"+that.skin;
		if (obj._dir != null) { that.sizer.className += " "+(obj._dir=="hor"?"dhxCursorNResize":"dhxCursorWResize"); }
	}
	
	/**
	*	@desc: returns array with available layout patterns
	*	@type: public
	*/
	this.listViews = function() {
		var views = new Array();
		for (var a in this.tplData) { views[views.length] = a; }
		return views;
	}
	this._init = function() {
		this.obj = document.createElement("DIV");
		this.obj.className = "dhtmlxLayoutObject";
		this.base.appendChild(this.obj);
		this.obj.appendChild(this.tpl);
		this.w = this.obj.offsetWidth;
		this.h = this.obj.offsetHeight;
		this.dhxWins = new dhtmlXWindows();
		this.dhxWins.setSkin(this.skin);
		// this.dhxWins.enableAutoViewport(false);
		// this.dhxWins.setViewport(0, 0, 800, 600, this.base);
		// this.dhxWins.setImagePath("../dhtmlxWindows/codebase/imgs/");
		this.dhxWins.setImagePath(this.imagePath);
		this.dhxWins.attachEvent("onTextChange", that._changeCPanelText);
		this.dhxWins.dhxLayout = this;
		//
		this._xmlLoader.loadXMLString(this.tplData[this._layoutView]!=null?this.tplData[this._layoutView]:this.tplData["3E"]);
	}
	
	this._autoHor = new Array();
	this._autoVer = new Array();
	// minimal dimension for parent window
	this._dimension = new Array(320, 200);
	this._rowsRatio = 100;
	this._colsRatio = 100;
	/*
	this._doOnLoad = function(){}
	this.loadXML = function(xmlFile, onLoadFunction) {
		if (onLoadFunction != null) { this._doOnLoad = function() { onLoadFunction(); } }
		this._xmlLoader.loadXML(xmlFile);
	}
	*/
	
	this._xmlParser = function() {
		var usedRows = 0;
		var totalHeight = "none";
		var usedHeight = 0;
		var sepHeight = that.skinParams[that.skin]["hor_sep_height"];
		if (that.base.style.height != null) { totalHeight = parseInt(that.base.style.height); }
		if (isNaN(totalHeight)) { totalHeight = that.base.offsetHeight; }
		if (isNaN(totalHeight)) { alert("init error, incorrect height of parent object, aborted"); return; }
		var root = this.getXMLTopNode("layout");
		for (var q=0; q<root.childNodes.length; q++) {
			if (root.childNodes[q].tagName == "row") {
				var row = root.childNodes[q];
				var tr = document.createElement("TR");
				var rowHeight = "";
				if (row.getAttribute("sep") != null) {
					tr.style.height = sepHeight+"px";
					usedHeight += sepHeight;
					//usedRows++;
				} else {
					if (usedRows < that._totalRows - 1) {
						rowHeight = Math.round((totalHeight - (that._totalRows-1)*sepHeight)/that._totalRows);
					} else {
						rowHeight = totalHeight - usedHeight;
					}
					// tr.style.height = rowHeight+"px";
					usedHeight += rowHeight;
					usedRows++;
				}
				//alert(rowHeight)
				// tr._collapse = new Array();
				that.tpl.childNodes[0].appendChild(tr);
				for (var w=0; w<row.childNodes.length; w++) {
					if (row.childNodes[w].tagName == "cell") {
						var cell = row.childNodes[w];
						var td = document.createElement("TD");
						td._dir = "null";
						
						if (cell.getAttribute("obj") != null) {
							var obj = cell.getAttribute("obj");
							td.style.height = rowHeight;
							//var item = Number(obj.replace("p",""))-1;
							//that.items[item] = td;
							
							//td._id = obj;
							//td._ind = item;
							
							td.className = "dhtmlxLayoutSinglePoly";
							td.innerHTML = "";//"<div class='dhtmlxPolyInnerContainer'>&nbsp;</div>";
							td._minW = (cell.getAttribute("minWidth") != null ? Number(cell.getAttribute("minWidth")):that._minWidth);
							td._minH = (cell.getAttribute("minHeight") != null ? Number(cell.getAttribute("minHeight")):that._minHeight);
							td._initCPanel = (cell.getAttribute("cpanel") != null ? (cell.getAttribute("cpanel")=="false"?false:true):true);
							td._resize = cell.getAttribute("resize");
							if (cell.getAttribute("width") != null) { td.style.width = cell.getAttribute("width"); }
							if (cell.getAttribute("height") != null) { td.style.height = cell.getAttribute("height"); }
							// td._initW = (cell.getAttribute("width")!=null?cell.getAttribute("width"):"*");
							// td._initH = (cell.getAttribute("height")!=null?cell.getAttribute("height"):"*");
							var rd = String(cell.getAttribute("neighbors")).split(";");
							for (var e=0; e<rd.length; e++) { var p = String(rd[e]).split(","); if (p.length > 1) { rd[e] = p; } }
							td._rowData = rd;
							that.polyObj[obj] = td;
						}
						if (cell.getAttribute("sep") != null) {
							var sep = cell.getAttribute("sep");
							if (sep == "hor") {
								td.className = "dhtmlxLayoutPolySplitterHor";
								td._dir = "hor";
								// top side
								var top = cell.getAttribute("top").split(";");
								for (var e=0; e<top.length; e++) { var p = String(top[e]).split(","); if (p.length > 1) { top[e] = p; } }
								td._top = top;
								// bottom side
								var bottom = cell.getAttribute("bottom").split(";");
								for (var e=0; e<bottom.length; e++) { var p = String(bottom[e]).split(","); if (p.length > 1) { bottom[e] = p; } }
								td._bottom = bottom;
								that.sepHor[that.sepHor.length] = td;
							} else {
								td.className = "dhtmlxLayoutPolySplitterVer";
								td._dir = "ver";
								// left side
								var left = cell.getAttribute("left").split(";");
								for (var e=0; e<left.length; e++) { var p = String(left[e]).split(","); if (p.length > 1) { left[e] = p; } }
								td._left = left;
								// right side
								var right = cell.getAttribute("right").split(";");
								for (var e=0; e<right.length; e++) { var p = String(right[e]).split(","); if (p.length > 1) { right[e] = p; } }
								td._right = right;
								that.sepVer[that.sepVer.length] = td;
							}
							td._dblClick = cell.getAttribute("dblclick");
							td._isSep = true;
							td.innerHTML = "&nbsp;";
						}
						if (cell.getAttribute("colspan") != null) { td.colSpan = cell.getAttribute("colspan"); }
						if (cell.getAttribute("rowspan") != null) { td.rowSpan = cell.getAttribute("rowspan"); }
						tr.appendChild(td);
					}
				}
			}
			// autosize data
			if (root.childNodes[q].tagName == "autosize") {
				that._autoHor = (root.childNodes[q].getAttribute("hor")).split(";");
				that._autoVer = (root.childNodes[q].getAttribute("ver")).split(";");
				that._totalCols = root.childNodes[q].getAttribute("cols");
				that._totalRows = root.childNodes[q].getAttribute("rows");
				that._dimension[0] = that._totalCols * that._colsRatio;
				that._dimension[1] = that._totalRows * that._rowsRatio;
			}
		}
		if (that._parentWindow != null) {
			that._parentWindow.setMinDimension(that._dimension[0], that._dimension[1]);
		}
		that._buildSurface();
		// that._doOnLoad();
	}
	this._xmlLoader = new dtmlXMLLoaderObject(this._xmlParser, window);
	this._availAutoSize = { "1C_hor": new Array("a"),
				"1C_ver": new Array("a"),
				"2E_hor": new Array("a;b"),
				"2E_ver": new Array("a", "b"),
				"2U_hor": new Array("a", "b"),
				"2U_ver": new Array("a;b"),
				"3E_hor": new Array("a;b;c"),
				"3E_ver": new Array("a", "b", "c"),
				"3J_hor": new Array("a;c", "b"),
				"3J_ver": new Array("a;b", "c;b"),
				"3L_hor": new Array("a", "b;c"),
				"3L_ver": new Array("a;b", "a;c"),
				"3T_hor": new Array("a;b", "a;c"),
				"3T_ver": new Array("a", "b;c"),
				"3U_hor": new Array("a;c", "b;c"),
				"3U_ver": new Array("a;b", "c"),
				"3W_hor": new Array("a", "b", "c"),
				"3W_ver": new Array("a;b;c"),
				"4H_hor": new Array("a", "b;c", "d"),
				"4H_ver": new Array("a;b;d", "a;c;d"),
				"4I_hor": new Array("a;b;d", "a;c;d"),
				"4I_ver": new Array("a", "b;c", "d"),
				"4T_hor": new Array("a;b", "a;c", "a;d"),
				"4T_ver": new Array("a", "b;c;d"),
				"4U_hor": new Array("a;d", "b;d", "c;d"),
				"4U_ver": new Array("a;b;c", "d"),
				"5H_hor": new Array("a", "b;c;d", "e"),
				"5H_ver": new Array("a;b;e", "a;c;e", "a;d;e"),
				"5I_hor": new Array("a;b;e", "a;c;e", "a;d;e"),
				"5I_ver": new Array("a", "b;c;d", "e"),
				"6I_hor": new Array("a;b;f", "a;c;f", "a;d;f", "a;e;f"),
				"6I_ver": new Array("a", "b;c;d;e", "f")
	};
	/**
	*	@desc: returns array with available autosize settings
	*	@type: public
	*/
	this.listAutoSizes = function() {
		var hor = this._availAutoSize[this._layoutView+"_hor"];
		var ver = this._availAutoSize[this._layoutView+"_ver"];
		var currentHor = (this._autoHor).join(";");
		var currentVer = (this._autoVer).join(";");
		return new Array(currentHor, currentVer, hor, ver);
	}
	/**
	*	@desc: sets autosize for the layout
	*	@param: hor - cells that will be autosized horizontally
	*	@param: ver - cells that will be autosized vertically
	*	@type: public
	*/
	this.setAutoSize = function(hor, ver) {
		if (hor != null) {
			var allow = false;
			var data = this._availAutoSize[this._layoutView+"_hor"];
			for (var q=0; q<data.length; q++) { allow = allow || (data[q] == hor); }
			if (allow == true) { this._autoHor = hor.split(";"); }
		}
		if (ver != null) {
			var allow = false;
			var data = this._availAutoSize[this._layoutView+"_ver"];
			for (var q=0; q<data.length; q++) { allow = allow || (data[q] == ver); }
			if (allow == true) { this._autoVer = ver.split(";"); }
		}
	}
	
	this._buildSurface = function() {
		
		for (var r=0; r<this.tpl.childNodes[0].childNodes.length; r++) {
			var tr = this.tpl.childNodes[0].childNodes[r];
			for (var c=0; c<tr.childNodes.length; c++) {
				var td = tr.childNodes[c];
				var that = this;
				if (!td._isSep) {
					td._isLayoutCell = true;
					/**
					*	@desc: returns cell's id
					*	@type: public
					*/
					td.getId = function() {
						return this._idd;
					}
					/**
					*	@desc: returns cell's index
					*	@type: public
					*/
					td.getIndex = function() {
						return this._ind;
					}
					/**
					*	@desc: shows a header
					*	@type: public
					*/
					td.showHeader = function() {
						that.showPanel(this._idd);
					}
					/**
					*	@desc: hides a header
					*	@type: public
					*/
					td.hideHeader = function() {
						that.hidePanel(this._idd);
					}
					/**
					*	@desc: returns true if a header is visible
					*	@type: public
					*/
					td.isHeaderVisible = function() {
						return that.isPanelVisible(this._idd);
					}
					/**
					*	@desc: set header's text
					*	@param: text - new header's text
					*	@type: public
					*/
					td.setText = function(text) {
						that.setText(this._idd, text);
					}
					/**
					*	@desc: expands a cell
					*	@type: public
					*/
					td.expand = function() {
						if (!that._isCollapsed(this._idd)) { return; }
						that._expand(this._idd, "hide");
					}
					/**
					*	@desc: collapses a cell
					*	@type: public
					*/
					td.collapse = function() {
						if (that._isCollapsed(this._idd)) { return; }
						that._collapse(this._idd, "hide");
					}
					/**
					*	@desc: return true if a cell is collapsed
					*	@type: public
					*/
					td.isCollapsed = function() {
						return that._isCollapsed(this._idd);
					}
					/**
					*	@desc: docks a cell from a window
					*	@type: public
					*/
					td.dock = function() {
						if (!that._isCollapsed(this._idd)) { return; }
						that._expand(this._idd, "dock");
						that.dockWindow(this._idd, this._wId);
					}
					/**
					*	@desc: undocks a cell to a window
					*	@type: public
					*/
					td.undock = function() {
						if (that._isCollapsed(this._idd)) { return; }
						that.unDockWindow(this._wId);
						that._collapse(this._idd, "dock");
					}
					/**
					*	@desc: sets cell's width
					*	@param: width
					*	@type: public
					*/
					td.setWidth = function(width) {
						if (!Number(width)) { return; }
						that._setWidth(this._idd, width);
					}
					/**
					*	@desc: returns cell's width
					*	@type: public
					*/
					td.getWidth = function() {
						return parseInt(this.style.width);
					}
					/**
					*	@desc: sets cell's height
					*	@param: height
					*	@type: public
					*/
					td.setHeight = function(height) {
						if (!Number(height)) { return; }
						that._setHeight(this._idd, height);
					}
					/**
					*	@desc: returns cell's height
					*	@type: public
					*/
					td.getHeight = function() {
						return parseInt(this.style.height);
					}
					/**
					*	@desc: fixes cell's size (block resize)
					*	@param: width - true/false
					*	@param: height - true/false
					*	@type: public
					*/
					td.fixSize = function(width, height) {
						that._fixSize(this._idd, width, height);
					}
					/**
					*	@desc: attaches a dhtmlxGrid to a cell
					*	@type: public
					*/
					td.attachGrid = function() {
						this._grid = this.window.attachGrid();
						return this._grid;
					}
					/**
					*	@desc: attaches a dhtmlxTree to a cell
					*	@param: root - not mandatory, tree super root, see dhtmlxTree documentation for details
					*	@type: public
					*/
					td.attachTree = function(root) {
						this._tree = this.window.attachTree(root);
						return this._tree;
					}
					/**
					*	@desc: attaches a dhtmlxTabbar to a cell
					*	@type: public
					*/
					td.attachTabbar = function() {
						this._tabbar = this.window.attachTabbar();
						return this._tabbar;
					}
					/**
					*	@desc: attaches a dhtmlxAccordion to a cell
					*	@type: public
					*/
					td.attachAccordion = function() {
						this._accordion = this.window.attachAccordion();
						return this._accordion;
					}
					/**
					*	@desc: attaches a dhtmlxFolders to a cell
					*	@type: public
					*/
					td.attachFolders = function() {
						this._folders = this.window.attachFolders();
						return this._folders;
					}
					/**
					*	@desc: attaches a status bar to a cell
					*	@type: public
					*/
					td.attachStatusBar = function() {
						this._status = this.window.attachStatusBar();
						return this._status;
					}
					/**
					*	@desc: attaches a dhtmlxMenu to a cell
					*	@type: public
					*/
					td.attachMenu = function() {
						this._menu = this.window.attachMenu();
						return this._menu;
					}
					/**
					*	@desc: attaches a dhtmlxToolbar to a cell
					*	@type: public
					*/
					td.attachToolbar = function() {
						this._toolbar = this.window.attachToolbar();
						return this._toolbar;
					}
					/**
					*	@desc: attaches a dhtmlxEditor to a cell
					*	@type: public
					*/
					td.attachEditor = function() {
						this._editor = this.window.attachEditor();
						return this._editor;
					}
					/**
					*	@desc: attaches an object to a cell
					*	@param: obj - object/object id
					*	@type: public
					*/
					td.attachObject = function(obj) {
						this._obj = this.window.attachObject(obj);
					}
					/**
					*	@desc: attaches an url into a cell
					*	@param: url
					*	@type: public
					*/
					td.attachURL = function(url) {
						this._url = this.window.attachURL(url);
						this._frame = this.window._frame;
					}
				}
				//
				if (td._dir == "ver") {
					td.onselectstart = function(e) { e = e||event; e.returnValue = false; }
					td.onmousedown = function(e) {
						var p = that._findDockCellsVer(this);
						that._resAreaData = new Array();
						if (p[0] != null && p[1] != null) {
							if (String(document.body.className).search("dhxCursorWResize") == -1) { document.body.className += " dhxCursorWResize"; }
							e = e||event;
							that._resObj = this;
							that._anyExpL = p[0];
							that._anyExpR = p[1];
							that._collectResAreaData(p);
							// not needed
							// that._resXScrollLeft = that._countScrollLeft(e.target||e.srcElement);
							that._resX = e.clientX;// + that._resXScrollLeft;
							// sizmple resize
							if (that._effects["resize"] == false) {
								that._attachSizer(this);
								that.sizer._leftXStart = parseInt(that.sizer.style.left);
								// getting neares objects
								var objLeft = that.polyObj[that._anyExpL[0]];
								that._resXMaxWidthLeft = parseInt(objLeft.style.width)-that._minWidth;
								var objRight = that.polyObj[that._anyExpR[0]];
								that._resXMaxWidthRight = parseInt(objRight.style.width)-that._minWidth;
								// checking alternative min width in attached layout case
								if (that._alterSizes.length > 0) {
									for (var q=0; q<that._alterSizes.length; q++) {
										for (var w=0; w<that._anyExpL.length; w++) {
											if (that._alterSizes[q][0] == that._anyExpL[w]) {
												var newVal = that._resXMaxWidthLeft = parseInt(objLeft.style.width)-that._alterSizes[q][1];
												if (newVal < that._resXMaxWidthLeft) { that._resXMaxWidthLeft = newVal; }
											}
										}
										for (var w=0; w<that._anyExpR.length; w++) {
											if (that._alterSizes[q][0] == that._anyExpR[w]) {
												newVal = parseInt(objRight.style.width)-that._alterSizes[q][1];
												if (newVal < that._resXMaxWidthRight) { that._resXMaxWidthRight = newVal; }
											}
										}
									}
								}
								that._resXStart = that._resX;
							}
							//
							that._resFunc = that._resizeVer;
							that._showCovers();
						}
					}
					td.onmouseup = function() {
						if (that._effects["resize"] == true) {
							that._resizeStop();
							that._anyExpL = null;
							that._anyExpR = null;
						}
					}
				}
				if (td._dir == "hor") {
					td.onselectstart = function(e) { e = e||event; e.returnValue = false; }
					td.onmousedown = function(e) {
						var p = that._findDockCellsHor(this);
						that._resAreaData = new Array();
						if (p[0] != null && p[1] != null) {
							if (String(document.body.className).search("dhxCursorNResize") == -1) { document.body.className += " dhxCursorNResize"; }
							e = e||event;
							that._resObj = this;
							that._anyExpT = p[0];
							that._anyExpB = p[1];
							that._collectResAreaData(p);
							// not needed + that._countScrollTop() not needed too
							// that._resYScrollTop = that._countScrollTop(e.target||e.srcElement);
							that._resY = e.clientY;// + that._resYScrollTop;
							// sizmple resize
							if (that._effects["resize"] == false) {
								that._attachSizer(this);
								that.sizer._topYStart = parseInt(that.sizer.style.top);
								// getting neares objects
								var objTop = that.polyObj[that._anyExpT[0]];
								that._resYMaxHeightTop = parseInt(objTop.style.height)-that._minHeight;
								var objBottom = that.polyObj[that._anyExpB[0]];
								that._resYMaxHeightBottom = parseInt(objBottom.style.height)-that._minHeight;
								// checking alternative min height in attached layout case
								if (that._alterSizes.length > 0) {
									for (var q=0; q<that._alterSizes.length; q++) {
										for (var w=0; w<that._anyExpT.length; w++) {
											if (that._alterSizes[q][0] == that._anyExpT[w]) {
												var newVal = parseInt(objTop.style.height)-that._alterSizes[q][2]-(objTop.childNodes[0].style.display!="none"?that.skinParams[that.skin]["cpanel_height"]:0);
												if (newVal < that._resYMaxHeightTop) { that._resYMaxHeightTop = newVal; }
											}
										}
										for (var w=0; w<that._anyExpB.length; w++) {
												if (that._alterSizes[q][0] == that._anyExpB[w]) {
													var newVal = parseInt(objBottom.style.height)-that._alterSizes[q][2]-(objBottom.childNodes[0].style.display!="none"?that.skinParams[that.skin]["cpanel_height"]:0);
													if (newVal < that._resYMaxHeightBottom) { that._resYMaxHeightBottom = newVal; }
												}
										}
									}
								}
								that._resYStart = that._resY;
							}
							//
							that._resFunc = that._resizeHor;
							that._showCovers();
						}
					}
					td.onmouseup = function() {
						if (that._effects["resize"] == true) {
							that._resizeStop();
							that._anyExpT = null;
							that._anyExpB = null;
						}
					}
				}
				td.ondblclick = function() {
					//
					if (this._dblClick == null) { return; }
					if (that.polyObj[this._dblClick] == null) { return; }
					// show/hide
					var obj = that.polyObj[this._dblClick];
					if (obj.childNodes[0].style.display == "none") { return; }
					if (obj._collapsed == true) {
						//
						that._doExpand(obj._resize, this._dblClick, obj._rowData, "hide");
					} else {
						// save dimension
						obj._savedW = parseInt(obj.style.width);
						obj._savedH = parseInt(obj.style.height);
						//
						that._doCollapse(obj._resize, this._dblClick, obj._rowData, "hide");
					}
				}
			}
		}
		
		//return;
		
		//var symboLink = String("a").charCodeAt(0);
		
		var p = {};
		for (var a in this.polyObj) {
			var w = this.polyObj[a].offsetWidth;
			var h = this.polyObj[a].offsetHeight;
			p[a] = new Array(w,h);
		}
		var q = 1;
		for (var a in p) {
			//alert(a+" "+p[a][0]+" "+p[a][1]);
			this.polyObj[a].style.width = p[a][0]-2+"px";
			this.polyObj[a].style.height = p[a][1]-2+"px";
			this.polyObj[a]._collapsed = false;
			this.polyObj[a]._idd = a;
			this.polyObj[a]._ind = this.items.length;
			this.items[this.items.length] = this.polyObj[a];
			//
			var bar = document.createElement("DIV");
			bar._dockCell = a;
			bar._resize = this.polyObj[a]._resize;
			bar.className = "dhtmlxPolyInfoBar";
			bar.innerHTML = "<div class='dhtmlxInfoBarLabel'>&nbsp;</div>"+
					"<div class='dhtmlxInfoBarButtonsFake'>&nbsp;</div>"+
					"<div class='dhtmlxInfoButtonDock' title='Dock'></div>"+
					"<div class='dhtmlxInfoButtonUnDock' style='display: none;' title='UnDock'></div>"+
					"<div class='dhtmlxInfoButtonShowHide_"+bar._resize+"' title='Collapse'></div>";
			if (this.polyObj[a]._initCPanel == true) {
				bar._h = this._CPanelHeight;
				bar.style.display = "";
			} else {
				bar._h = 0;
				bar.style.display = "none";
			}
			
			this.polyObj[a].appendChild(bar);
			//
			
			for (var r=0; r<bar.childNodes.length; r++) {
				bar.childNodes[r].onselectstart = function(e) { e = e||event; e.returnValue = false; }
			}
			
			//
			var wId = "w"+a;//String(q++);
			var win = this.dhxWins.createWindow(wId, 10, 10, p[a][0], p[a][1]);
			win.hide();
			win._tmpRowData = this.polyObj[a]._rowData;
			win._tmpReszie = bar._resize;
			win._dockCell = a;
			//win.setText("dhtmlxWindow "+a);
			win.setText(a);
			//var symboLinkStr = String.fromCharCode(symboLink);
			//win.setText(symboLinkStr);
			//this.polyObj[a]._link = symboLinkStr;
			//this.cells[symboLinkStr] = this.items[symboLink-String("a").charCodeAt(0)];
			//symboLink++;
			win.button("close").hide();
			win.addUserButton("dock", 99, "Dock", "dock");
			win.button("dock").attachEvent("onClick", function(win) {
				that._doExpand(win._tmpReszie, win._dockCell, win._tmpRowData, "dock");
			});
			
			this.polyObj[a]._wId = wId;
			this.polyObj[a].window = win;
			bar._win = wId;
			
			// bar.childNodes[2].style.display = "none";
			bar.childNodes[2].onclick = function() { // dock & show
				that._expand(this.parentNode._dockCell, "dock");
			}
			bar.childNodes[3].onclick = function() { // undock & hide
				that.unDockWindow(this.parentNode._win);
				that._collapse(this.parentNode._dockCell, "dock");
			}
			bar.childNodes[4].onclick = function() { // show/hide
				var pId = this.parentNode._dockCell;
				if (that._isCollapsed(pId)) { that._expand(pId, "hide"); } else { that._collapse(pId, "hide"); }
			}
			
			this.dockWindow(a, wId);
		}
		this._fixIcons();
	}
	
	this._resX = null;
	this._resY = null;
	this._resObj = null;
	this._resFunc = null;
	//
	// optimized resize
	this._anyExpL = null;
	this._anyExpR = null;
	this._anyExpT = null;
	this._anyExpB = null;
	//
	this._expand = function(pId, mode) {
		this._doExpand(this.polyObj[pId]._resize, pId, this.polyObj[pId]._rowData, mode);
	}
	this._collapse = function(pId, mode) {
		if (this._isCollapsed(pId)) { return; }
		// save dimension
		this.polyObj[pId]._savedW = parseInt(this.polyObj[pId].style.width);
		this.polyObj[pId]._savedH = parseInt(this.polyObj[pId].style.height);
		// collapsing
		this._doCollapse(this.polyObj[pId]._resize, pId, this.polyObj[pId]._rowData, mode);
	}
	this._isCollapsed = function(pId) {
		return this.polyObj[pId]._collapsed;
	}
	// used to get alternative width/height for resising cell (in case of attached layout)
	this._checkAlterMinSize = function(data) {
		this._alterSizes = new Array();
		for (var q=0; q<data.length; q++) {
			for (var w=0; w<data[q].length; w++) {
				var win = this.polyObj[data[q][w]].window;
				if (win.layout != null) {
					var dims = win.layout._defineWindowMinDimension(win, true);
					dims[0] = data[q][w];
					this._alterSizes[this._alterSizes.length] = dims;
				}
			}
		}
	}
	//
	this._findDockCellsVer = function(resObj) {
		var res = new Array(null, null);
		if (resObj == null) { return res; }
		// find nearest expanded on the left side
		var anyExpL = null;
		for (var q=resObj._left.length-1; q>=0; q--) {
			if (anyExpL == null) {
				if (typeof(resObj._left[q]) == "object") {
					var isBlocked = false;
					for (var w=0; w<resObj._left[q].length; w++) { isBlocked = isBlocked || (this.polyObj[resObj._left[q][w]]._isBlockedWidth||false); }
					if (!isBlocked) { anyExpL = resObj._left[q]; }
				} else if(this.polyObj[resObj._left[q]]._collapsed == false) {
					if (!this.polyObj[resObj._left[q]]._isBlockedWidth) { anyExpL = resObj._left[q]; }
				}
			}
		}
		// find nearest expanded on the right side
		var anyExpR = null;
		for (var q=0; q<resObj._right.length; q++) {
			if (anyExpR == null) {
				if (typeof(resObj._right[q]) == "object") {
					var isBlocked = false;
					for (var w=0; w<resObj._right[q].length; w++) { isBlocked = isBlocked || (this.polyObj[resObj._right[q][w]]._isBlockedWidth||false); }
					if (!isBlocked) { anyExpR = resObj._right[q]; }
				} else if(this.polyObj[resObj._right[q]]._collapsed == false) {
					if (!this.polyObj[resObj._right[q]]._isBlockedWidth) { anyExpR = resObj._right[q]; }
				}
			}
		}
		// nothing to resize
		if (anyExpL == null || anyExpR == null) { return res; }
		// convert to array if needed
		if (typeof(anyExpL) == "string") { anyExpL = new Array(anyExpL); }
		if (typeof(anyExpR) == "string") { anyExpR = new Array(anyExpR); }
		//
		res[0] = anyExpL;
		res[1] = anyExpR;
		// checking alter size in case of attached layout
		this._checkAlterMinSize(res);
		this._minWLAlter = 0;
		this._minWRAlter = 0;
		if (this._alterSizes.length > 0 && this._effects["resize"] == true) {
			var objL = new Array();
			var objR = new Array();
			for (var q=0; q<anyExpL.length; q++) { objL[q] = this.polyObj[anyExpL[q]]; }
			for (var q=0; q<anyExpR.length; q++) { objR[q] = this.polyObj[anyExpR[q]]; }
			for (var q=0; q<objL.length; q++) { for (var w=0; w<this._alterSizes.length; w++) { if (this._alterSizes[w][0] == objL[q]._idd && this._minWLAlter < this._alterSizes[w][1]) { this._minWLAlter = this._alterSizes[w][1]; } } }
			for (var q=0; q<objR.length; q++) { for (var w=0; w<this._alterSizes.length; w++) { if (this._alterSizes[w][0] == objR[q]._idd && this._maxWRAlter < this._alterSizes[w][1]) { this._minWRAlter = this._alterSizes[w][1]; } } }
		}
		return res;
	}
	//
	this._findDockCellsHor = function(resObj) {
		var res = new Array(null, null);
		if (resObj == null) { return res; }
		// find nearest expanded on the top side
		var anyExpT = null;
		for (var q=resObj._top.length-1; q>=0; q--) {
			if (anyExpT == null) {
				if (typeof(resObj._top[q]) == "object") {
					var isBlocked = false;
					for (var w=0; w<resObj._top[q].length; w++) { isBlocked = isBlocked || (this.polyObj[resObj._top[q][w]]._isBlockedHeight||false); }
					if (!isBlocked) { anyExpT = resObj._top[q]; }
				} else if(this.polyObj[resObj._top[q]]._collapsed == false) {
					if (!this.polyObj[resObj._top[q]]._isBlockedHeight) { anyExpT = resObj._top[q]; }
				}
			}
		}
		// find nearest expanded on the bottom side
		var anyExpB = null;
		for (var q=0; q<resObj._bottom.length; q++) {
			if (anyExpB == null) {
				if (typeof(resObj._bottom[q]) == "object") {
					var isBlocked = false;
					for (var w=0; w<resObj._bottom[q].length; w++) { isBlocked = isBlocked || (this.polyObj[resObj._bottom[q][w]]._isBlockedHeight||false); }
					if (!isBlocked) { anyExpB = resObj._bottom[q]; }
				} else if(this.polyObj[resObj._bottom[q]]._collapsed == false) {
					if (!this.polyObj[resObj._bottom[q]]._isBlockedHeight) { anyExpB = resObj._bottom[q]; }
				}
			}
		}
		// nothing to resize
		if (anyExpT == null || anyExpB == null) { return res; }
		// convert to array if needed
		if (typeof(anyExpT) == "string") { anyExpT = new Array(anyExpT); }
		if (typeof(anyExpB) == "string") { anyExpB = new Array(anyExpB); }
		//
		res[0] = anyExpT;
		res[1] = anyExpB;
		// checking alter size in case of attached layout
		this._checkAlterMinSize(res);
		this._minHTAlter = 0;
		this._minHBAlter = 0;
		if (this._alterSizes.length > 0 && this._effects["resize"] == true) {
			var objT = new Array();
			var objB = new Array();
			for (var q=0; q<anyExpT.length; q++) { objT[q] = this.polyObj[anyExpT[q]]; }
			for (var q=0; q<anyExpB.length; q++) { objB[q] = this.polyObj[anyExpB[q]]; }
			for (var q=0; q<objT.length; q++) { for (var w=0; w<this._alterSizes.length; w++) { if (this._alterSizes[w][0] == objT[q]._idd && this._minHTAlter < this._alterSizes[w][2]) { this._minHTAlter = this._alterSizes[w][2]; } } }
			for (var q=0; q<objB.length; q++) { for (var w=0; w<this._alterSizes.length; w++) { if (this._alterSizes[w][0] == objB[q]._idd && this._minHBAlter < this._alterSizes[w][2]) { this._minHBAlter = this._alterSizes[w][2]; } } }
		}
		//
		return res;
	}
	//
	this._resizeVer = function(e) {
		if (this._resObj == null || this._anyExpL == null || this._anyExpR == null) { return; }
		// simple resize
		if (this._effects["resize"] == false) {
			this._resX = e.clientX;
			var offsetX = e.clientX - this._resXStart;
			if (-offsetX > this._resXMaxWidthLeft && offsetX < 0) { offsetX = -this._resXMaxWidthLeft; this._resX = offsetX+this._resXStart; }
			if (offsetX > this._resXMaxWidthRight && offsetX > 0) { offsetX = this._resXMaxWidthRight; this._resX = offsetX+this._resXStart; }
			this.sizer.style.left = this.sizer._leftXStart+offsetX+"px";
			return;
		}
		// console.log(this._resObj._leftXStart);
		//
		var anyExpL = this._anyExpL;
		var anyExpR = this._anyExpR;
		// resize items
		var newX = e.clientX;
		var offsetX = e.clientX - that._resX;
		//
		var objL = new Array();
		var objR = new Array();
		for (var q=0; q<anyExpL.length; q++) { objL[q] = this.polyObj[anyExpL[q]]; }
		for (var q=0; q<anyExpR.length; q++) { objR[q] = this.polyObj[anyExpR[q]]; }
		//
		var wL = parseInt(objL[0].style.width);
		var wR = parseInt(objR[0].style.width);
		//
		if (offsetX < 0) {
			var newWL = wL + offsetX;
			if (newWL > objL[0]._minW && newWL > this._minWLAlter) {
				var newWR = wR + wL - newWL;
				for (var q=0; q<objL.length; q++) {
					objL[q].style.width = newWL + "px";
					objL[q].childNodes[1].style.width = newWL + "px";
				}
				for (var q=0; q<objR.length; q++) {
					objR[q].style.width = newWR + "px";
					objR[q].childNodes[1].style.width = newWR + "px";
				}
				this._resX = newX;
			}
		} else if (offsetX > 0) {
			var newWR = wR - offsetX;
			if (newWR > objR[0]._minW && newWR > this._minWRAlter) {
				var newWL = wL + wR - newWR;
				for (var q=0; q<objL.length; q++) {
					objL[q].style.width = newWL + "px";
					objL[q].childNodes[1].style.width = newWL + "px";
				}
				for (var q=0; q<objR.length; q++) {
					objR[q].style.width = newWR + "px";
					objR[q].childNodes[1].style.width = newWR + "px";
				}
				this._resX = newX;
			}
		}
	}
	this._resizeHor = function(e) {
		if (this._resObj == null || this._anyExpT == null || this._anyExpB == null) { return; }
		// simple resize
		if (this._effects["resize"] == false) {
			this._resY = e.clientY;
			var offsetY = e.clientY - this._resYStart;
			if (-offsetY > this._resYMaxHeightTop && offsetY < 0) { offsetY = -this._resYMaxHeightTop; this._resY = offsetY + this._resYStart; }
			if (offsetY > this._resYMaxHeightBottom && offsetY > 0) { offsetY = this._resYMaxHeightBottom; this._resY = offsetY + this._resYStart; }
			this.sizer.style.top = this.sizer._topYStart+offsetY+"px";
			return;
		}
		//
		var anyExpT = this._anyExpT;
		var anyExpB = this._anyExpB;
		// resize items
		var newY = e.clientY;
		var offsetY = e.clientY - that._resY;
		//
		var objT = new Array();
		var objB = new Array();
		for (var q=0; q<anyExpT.length; q++) { objT[q] = this.polyObj[anyExpT[q]]; }
		for (var q=0; q<anyExpB.length; q++) { objB[q] = this.polyObj[anyExpB[q]]; }
		//
		var hT = parseInt(objT[0].style.height);
		var hB = parseInt(objB[0].style.height);
		//
		if (offsetY < 0) {
			var newHT = hT + offsetY;
			if (newHT > objT[0]._minH + this._minHTAlter) {
				var newHB = hB + hT - newHT;
				for (var q=0; q<objT.length; q++) {
					objT[q].style.height = newHT + "px";
					objT[q].childNodes[1].style.height = newHT - objT[q].childNodes[0]._h + "px";
				}
				for (var q=0; q<objB.length; q++) {
					objB[q].style.height = newHB + "px";
					objB[q].childNodes[1].style.height = newHB - objB[q].childNodes[0]._h + "px";
				}
				this._resY = newY;
			}
		} else if (offsetY > 0) {
			var newHB = hB - offsetY;
			// console.log(newHB, objB[0]._minH, this._minHBAlter)
			if (newHB > objB[0]._minH + this._minHBAlter) {
				var newHT = hT + hB - newHB;
				for (var q=0; q<objT.length; q++) {
					objT[q].style.height = newHT + "px";
					objT[q].childNodes[1].style.height = newHT - objT[q].childNodes[0]._h + "px";
				}
				for (var q=0; q<objB.length; q++) {
					objB[q].style.height = newHB + "px";
					objB[q].childNodes[1].style.height = newHB - objB[q].childNodes[0]._h + "px";
				}
				this._resY = newY;
			}
		}
	}
	
	this._resizeStop = function() {
		document.body.className = String(document.body.className).replace(/dhxCursorWResize/g,"").replace(/dhxCursorNResize/g,"");
		if (this._resObj == null) { return; }
		// simple resize
		if (this._effects["resize"] == false) {
			this.sizer.style.display = "none";
			if (this._resObj._dir == "hor") {
				var objTop = (typeof(this._anyExpT[0])=="object"?this._anyExpT[0][0]:this._anyExpT[0]);
				var offsetY = this._resY-this._resYStart;
				var newH = parseInt(this.polyObj[objTop].style.height)+offsetY;
				this._setHeight(objTop, newH);
			} else {
				var objLeft = (typeof(this._anyExpL[0])=="object"?this._anyExpL[0][0]:this._anyExpL[0]);
				var offsetX = this._resX-this._resXStart;
				var newW = parseInt(this.polyObj[objLeft].style.width)+offsetX;
				this._setWidth(objLeft, newW);
			}
			// fix inner content
			if (typeof(this._anyExpT) == "object" && this._anyExpT != null) { this._fixInnerContentFromArray(this._anyExpT); this._anyExpT = null; }
			if (typeof(this._anyExpB) == "object" && this._anyExpB != null) { this._fixInnerContentFromArray(this._anyExpB); this._anyExpB = null; }
			if (typeof(this._anyExpL) == "object" && this._anyExpL != null) { this._fixInnerContentFromArray(this._anyExpL); this._anyExpL = null; }
			if (typeof(this._anyExpR) == "object" && this._anyExpR != null) { this._fixInnerContentFromArray(this._anyExpR); this._anyExpR = null; }
			// clear data
			this._resObj = null;
			this._resFunc = null;
			this._hideCovers();
			//
			this.callEvent("onPanelResizeFinish", []);
			//
			// fix for opera with vertical resize
			this._fixCellsContentOpera950();
			//
			return;
		}
		// resize effect
		var poly = new Array();
		if (this._resObj._left != null) { for (var q=0; q<this._resObj._left.length; q++) { poly[poly.length] = this._resObj._left[q]; } }
		if (this._resObj._right != null) { for (var q=0; q<this._resObj._right.length; q++) { poly[poly.length] = this._resObj._right[q]; } }
		if (this._resObj._top != null) { for (var q=0; q<this._resObj._top.length; q++) { poly[poly.length] = this._resObj._top[q]; } }
		if (this._resObj._bottom != null) { for (var q=0; q<this._resObj._bottom.length; q++) { poly[poly.length] = this._resObj._bottom[q]; } }
		this._resFunc = null;
		this._resObj = null;
		this._hideCovers();
		// sizes in grid and tabbar
		var wId = new Array();
		for (var q=0; q<poly.length; q++) {
			if (typeof(poly[q]) == "object") {
				for (var w=0; w<poly[q].length; w++) { wId[wId.length] = this.polyObj[poly[q][w]]._win; }
			} else {
				wId[wId.length] = this.polyObj[poly[q]]._win;
			}
		}
		for (var q=0; q<wId.length; q++) { if (this.dhxWins.window(wId[q]) != null) { this._updateComponentsView(this.dhxWins.window(wId[q])); } }
		//
		this.callEvent("onPanelResizeFinish", []);
	}
	this._showCovers = function() {
		for (var a in this.polyObj) {
			if (this.polyObj[a].childNodes[1] != null) {
				if (this.polyObj[a].childNodes[1].childNodes[this.polyObj[a].childNodes[1].childNodes.length-1] != null) {
					var cover = this.polyObj[a].childNodes[1].childNodes[this.polyObj[a].childNodes[1].childNodes.length-1];
					cover.className = (this._effects["highlight"]&&this._isResizable(a)?"dhxLayout_Cover_"+this.skin:"dhx_content_cover_blocker");
					cover.style.display = "";
				}
			}
		}
	}
	this._hideCovers = function() {
		for (var a in this.polyObj) {
			if (this.polyObj[a].childNodes[1] != null) {
				if (this.polyObj[a].childNodes[1].childNodes[this.polyObj[a].childNodes[1].childNodes.length-1] != null) {
					var cover = this.polyObj[a].childNodes[1].childNodes[this.polyObj[a].childNodes[1].childNodes.length-1];
					cover.style.display = "none";
				}
			}
		}
	}
	this._isResizable = function(pId) {
		var need = false;
		for (var q=0; q<this._resAreaData.length; q++) { need = need || (this._resAreaData[q] == pId); }
		return need;
	}
	this._collectResAreaData = function(obj) {
		for (var q=0; q<obj.length; q++) {
			if (typeof(obj[q]) == "string") {
				this._resAreaData[this._resAreaData.length] = obj[q];
			} else if (typeof(obj[q]) == "object") {
				this._collectResAreaData(obj[q]);
			}
		}
	}
	if (_isIE) {
		document.body.attachEvent("onselectstart", function(){ e = event; if (that._resObj != null) { e.returnValue = false; } });
		document.body.attachEvent("onmousemove", function(e){ e = e||event; if (that._resObj != null && that._resFunc != null) { that._resFunc(e); } }, false);
		document.body.attachEvent("onmouseup", function(){ that._resizeStop(); });
	} else {
		document.body.addEventListener("mousemove", function(e){ e = e||event; if (that._resObj != null && that._resFunc != null) { that._resFunc(e); } }, false);
		document.body.addEventListener("mouseup", function(){ that._resizeStop(); }, false);
	}
	this._fixCellsContentOpera950 = function() {
		if (_isOpera) {
			this.forEachItem(function(item){
				var cell = item.childNodes[1].childNodes[2];
				var brd = that._opera950FixBorder;
				cell.style.border = "#FFFFFF 0px dashed";
				window.setTimeout(function(){cell.style.border=brd;}, 1);
			});
		}
	}
	this._doExpand = function(dir, pId, rowData, mode) { // dir=hor|ver
		// console.log("expand", mode)
		if (rowData.length <= 1) { return; }
		var ind = -1;
		for (var q=0; q<rowData.length; q++) { if (rowData[q] == pId) { ind = q; } }
		if (ind == -1) { return; }
		// go to the right/bottom
		var anyExp = null;
		for (var q=ind+1; q<rowData.length; q++) {
			if (anyExp == null) {
				if (typeof(rowData[q]) == "string") { if (this.polyObj[rowData[q]]._collapsed == false) { anyExp = rowData[q]; } } else { anyExp = rowData[q]; }
			}
		}
		// go to the left/top
		if (anyExp == null) {
			for (var q=ind-1; q>=0; q--) {
				if (anyExp == null) {
					if (typeof(rowData[q]) == "string") { if (this.polyObj[rowData[q]]._collapsed == false) { anyExp = rowData[q]; } } else { anyExp = rowData[q]; }
				}
			}
		}
		if (anyExp == null) { return; }
		//
		if (typeof(anyExp) != "object") { anyExp = new Array(anyExp); }
		if (dir == "hor") {
			
			var availSpace = parseInt(this.polyObj[anyExp[0]].style.width) - this._minWidth;
			var maxSize = this.polyObj[pId]._savedW;
			if (maxSize > availSpace) { maxSize = availSpace; }
			if (maxSize < this._minWidth) { return; }
			var step = Math.round(maxSize/3);
			
			// var maxSize = Math.round(parseInt(this.polyObj[anyExp[0]].style.width)/2);
			// var step = Math.round(this.polyObj[anyExp[0]].offsetWidth/24);
		} else {
			
			var availSpace = parseInt(this.polyObj[anyExp[0]].style.height) - this._minHeight;
			var maxSize = this.polyObj[pId]._savedH;
			if (maxSize > availSpace) { maxSize = availSpace; }
			if (maxSize < this._minHeight) { return; }
			var step = Math.round(maxSize/3);
			
			// var maxSize = Math.round(parseInt(this.polyObj[anyExp[0]].style.height)/2);
			// var step = Math.round(this.polyObj[anyExp[0]].offsetHeight/16);
		}
		
		// do expanding
		this.polyObj[pId].childNodes[1].style.display = "";
		this.polyObj[pId].childNodes[0].className = "dhtmlxPolyInfoBar";
		// icons
		this.polyObj[pId].childNodes[0].childNodes[1].style.display = "";
		this.polyObj[pId].childNodes[0].childNodes[2].style.display = "";
		//this.polyObj[pId].childNodes[0].childNodes[3].style.display = "";
		this.polyObj[pId].childNodes[0].childNodes[4].style.display = "";
		
		
		//
		var obj2 = new Array();
		for (var q=0; q<anyExp.length; q++) { obj2[q] = this.polyObj[anyExp[q]]; }
		//
		// tabbar special mode
		if (this.polyObj[pId].className == "dhtmlxLayoutSinglePolyTabbarCollapsed") {
			this.polyObj[pId].className = "dhtmlxLayoutSinglePolyTabbar";
		}
		// console.log(dir, maxSize, this.polyObj[pId]._savedW, this.polyObj[pId]._savedH)
		this._expandEffect(this.polyObj[pId], obj2, maxSize, mode, (this._effects["collapse"]==true?step:1000000), dir);
		//
	}
	this._doCollapse = function(dir, pId, rowData, mode) { // dir=hor|ver
		// console.log("collapse", mode)
		if (rowData.length <= 1) { return; }
		var ind = -1;
		for (var q=0; q<rowData.length; q++) { if (rowData[q] == pId) { ind = q; } }
		if (ind == -1) { return; }
		// go to the right
		var anyExp = null;
		for (var q=ind+1; q<rowData.length; q++) {
			if (anyExp == null) {
				if (typeof(rowData[q]) == "string") { if (this.polyObj[rowData[q]]._collapsed == false) { anyExp = rowData[q]; } } else { anyExp = rowData[q]; }
			}
		}
		// go to the left
		if (anyExp == null) {
			for (var q=ind-1; q>=0; q--) {
				if (anyExp == null) {
					if (typeof(rowData[q]) == "string") { if (this.polyObj[rowData[q]]._collapsed == false) { anyExp = rowData[q]; } } else { anyExp = rowData[q]; }
				}
			}
		}
		if (anyExp == null) {
			if (rowData[ind+1] != null) { anyExp = rowData[ind+1]; }
		}
		// check first collapsed on the left for expanding
		if (anyExp == null) {
			if (ind-1 >= 0) {
				if (rowData[ind-1] != null) { anyExp = rowData[ind-1]; }
			}
		}
		// do collapsing
		if (anyExp != null) {
			
			if (typeof(anyExp) != "object") {
				
				if (this.polyObj[anyExp]._collapsed == true) {
					this.polyObj[anyExp].childNodes[1].style.display = "";
					this.polyObj[anyExp]._collapsed = false;
					this.polyObj[anyExp].childNodes[0].className = "dhtmlxPolyInfoBar";
					this.polyObj[anyExp].childNodes[0].childNodes[1].style.display = "";
					this.polyObj[anyExp].childNodes[0].childNodes[4].title = "Collapse";
					this.polyObj[anyExp].childNodes[0].childNodes[2].style.display = "";
					this.polyObj[anyExp].childNodes[0].childNodes[3].style.display = "none";
					this.polyObj[anyExp].childNodes[0].childNodes[4].style.display = "";
					//
					// undock expanding window
					var wId = this.polyObj[anyExp].childNodes[0]._win;
					var win = this.dhxWins.window(wId);
					if (!win._isDocked) { this.dockWindow(anyExp, wId); }
					//
					// console.log("need to undock "+this.polyObj[anyExp].childNodes[0]._win)
					//
					// tabbar special mode
					if (this.polyObj[anyExp].className == "dhtmlxLayoutSinglePolyTabbarCollapsed") {
						this.polyObj[anyExp].className = "dhtmlxLayoutSinglePolyTabbar";
					}
					// opera 9.50 height fix
					this._fixCellsContentOpera950();
					// show/hide splitter images
					this._fixSplitters();
					// check icons
					this._fixIcons();
					// event
					this.callEvent("onExpand", [anyExp]);
				}
				
				anyExp = new Array(anyExp);
			}
			var obj2 = new Array();
			for (var q=0; q<anyExp.length; q++) { obj2[q] = this.polyObj[anyExp[q]]; }
			//
			if (dir == "hor") {
				var step = Math.round(Math.max(this.polyObj[pId].offsetWidth, this.polyObj[anyExp[0]].offsetWidth)/3);
			} else {
				var step = Math.round(Math.max(this.polyObj[pId].offsetHeight, this.polyObj[anyExp[0]].offsetHeight)/3);
			}
			
			this.polyObj[pId].childNodes[1].style.display = "none";
			//
			this._collapseEffect(this.polyObj[pId], obj2, mode, (this._effects["collapse"]==true?step:1000000), dir);
		}
	}
	
	/**
	*	@desc: sets effect
	*	@param: efName - effect's name
	*	@param: efValue - true/false
	*	@type: public
	*/
	this.setEffect = function(efName, efValue) {
		if (this._effects[efName] != null && typeof(efValue) == "boolean") {
			this._effects[efName] = efValue;
		}
	}
	/**
	*	@desc: returns true if the effect is enabled
	*	@param: efName - effect name
	*	@param: efValue - true/false
	*	@type: public
	*/
	this.getEffect = function(efName) {
		if (this._effects[efName] != null) { return this._effects[efName]; }
		return null;
	}
	
	this._expandEffect = function(obj, obj2, maxSize, mode, step, dir) {
		//
		if (dir == "hor") {
			var s = parseInt(obj.style.width);
			var s2 = parseInt(obj2[0].style.width);
		} else {
			var s = parseInt(obj.style.height);
			var s2 = parseInt(obj2[0].style.height);
		}
		var newS = s + step;
		if (newS > maxSize) { newS = maxSize; }
		//
		if (dir == "hor") {
			obj.style.width = newS+"px";
			obj.childNodes[1].style.width = newS+"px";
		} else {
			obj.style.height = newS+"px";
			obj.childNodes[1].style.height = newS-obj.childNodes[0]._h+"px";
		}
		//
		for (var q=0; q<obj2.length; q++) {
			if (dir == "hor") {
				obj2[q].style.width = s2+s-newS+"px";
				obj2[q].childNodes[1].style.width = s2+s-newS+"px";
			} else {
				obj2[q].style.height = s2+s-newS+"px";
				obj2[q].childNodes[1].style.height = s2+s-newS-obj2[q].childNodes[0]._h+"px";
			}
		}
		//
		if (newS != maxSize) {
			window.setTimeout(function(){that._expandEffect(obj, obj2, maxSize, mode, step, dir);}, 4);
		} else {
			obj._collapsed = false;
			// dock expanding window
			var wId = obj.childNodes[0]._win;
			var win = this.dhxWins.window(wId);
			if (!win._isDocked) { this.dockWindow(obj._idd, wId); }
			//
			// sizing grid/tabbar
			for (var q=0; q<obj2.length; q++) { if (obj2[q]._win != null) { this._updateComponentsView(this.dhxWins.window(obj2[q]._win)); } }
			this._updateComponentsView(this.dhxWins.window(wId));
			this.polyObj[obj._idd].childNodes[0].childNodes[4].title = "Collapse";
			//
			// opera 9.50 height fix
			this._fixCellsContentOpera950();
			// show/hide splitter images
			this._fixSplitters();
			// check icons
			this._fixIcons();
			// event
			this.callEvent("onExpand", [obj._idd]);
		}
	}
	this._collapseEffect = function(obj, obj2, mode, step, dir) {
		//
		if (dir == "hor") {
			var s = parseInt(obj.style.width);
			var s2 = parseInt(obj2[0].style.width);
		} else {
			var s = parseInt(obj.style.height);
			var s2 = parseInt(obj2[0].style.height);
		}
		var newS = s - step;
		if (dir == "hor") {
			if (newS < this._collapsedW) { newS = this._collapsedW; }
			obj.style.width = newS+"px";
			//obj.childNodes[1].style.width = newS+"px";
		} else {
			if (newS < this._collapsedH) { newS = this._collapsedH; }
			obj.style.height = newS+"px";
			var p = newS-obj.childNodes[0]._h;
			if (p < 0) { p = 0; }
			//obj.childNodes[1].style.height = p+"px";
		}
		
		//
		
		for (var q=0; q<obj2.length; q++) {
			if (dir == "hor") {
				obj2[q].style.width = s2+(s-newS)+"px";
				// obj2[q].childNodes[1].style.width = s2+(s-newS)+"px";
			} else {
				obj2[q].style.height = s2+(s-newS)+"px";
				// obj2[q].childNodes[1].style.height = s2+(s-newS)-obj2[q].childNodes[0]._h+"px";
			}
		}
		
		//
		if ((newS > this._collapsedW && dir == "hor") || (newS > this._collapsedH && dir == "ver")) {
			window.setTimeout(function(){that._collapseEffect(obj, obj2, mode, step, dir);}, 4);
		} else {
			for (var q=0; q<obj2.length; q++) {
				if (dir == "hor") {
					// obj2[q].style.width = s2+(s-newS)+"px";
					obj2[q].childNodes[1].style.width = s2+(s-newS)+"px";
				} else {
					// obj2[q].style.height = s2+(s-newS)+"px";
					obj2[q].childNodes[1].style.height = s2+(s-newS)-obj2[q].childNodes[0]._h+"px";
				}
			}
			// finish collapsing
			obj._collapsed = true;
			// obj.childNodes[1].style.display = "none";
			if (dir == "hor") {
				obj.childNodes[0].className = "dhtmlxPolyInfoBarCollapsedVer";
			} else {
				obj.childNodes[0].className = "dhtmlxPolyInfoBarCollapsedHor";
			}
			// sizing components
			for (var q=0; q<obj2.length; q++) { if (obj2[q]._win != null) { this._updateComponentsView(this.dhxWins.window(obj2[q]._win)); } }
			// icons
			if (mode == "hide") {
				obj.childNodes[0].childNodes[1].style.display = "";
				obj.childNodes[0].childNodes[2].style.display = "none";
				obj.childNodes[0].childNodes[3].style.display = "none";
				obj.childNodes[0].childNodes[4].style.display = "";
			} else {
				obj.childNodes[0].childNodes[1].style.display = "";
				obj.childNodes[0].childNodes[2].style.display = "";
				obj.childNodes[0].childNodes[3].style.display = "none";
				obj.childNodes[0].childNodes[4].style.display = "none";
			}
			// tabbar special mode
			if (obj.className == "dhtmlxLayoutSinglePolyTabbar") {
				obj.className = "dhtmlxLayoutSinglePolyTabbarCollapsed";
			}
			this.polyObj[obj._idd].childNodes[0].childNodes[4].title = "Expand";
			// fix content height in opera 9.50
			this._fixCellsContentOpera950();
			// show/hide splitter images
			this._fixSplitters();
			// check icons
			this._fixIcons();
			// events
			this.callEvent("onCollapse", [obj._idd]);
		}
	}
	
	this._setWidth = function(pId, width) {
		if (this.polyObj[pId] == null) { return; }
		if (!Number(width)) { return; }
		var sep = null;
		//
		for (var q=0; q<this.sepVer.length; q++) {
			var p = this.sepVer[q]._left;
			if (p[p.length-1] == pId) {
				sep = new Array(this.sepVer[q], "left");
			} else if (typeof(p[p.length-1]) == "object") {
				var k = p[p.length-1];
				for (var e=0; e<k.length; e++) { if (k[e] == pId) { sep = new Array(this.sepVer[q], "left"); } }
			}
			//
			var p = this.sepVer[q]._right;
			if (p[0] == pId) {
				sep = new Array(this.sepVer[q], "right");
			} else if (typeof(p[0]) == "object") {
				var k = p[0];
				for (var e=0; e<k.length; e++) { if (k[e] == pId) { sep = new Array(this.sepVer[q], "right"); } }
			}
		}
		if (sep != null) {
			// allow resizing
			var set = this._findDockCellsVer(sep[0]);
			var anyExpL = set[0];
			var anyExpR = set[1];
			if (anyExpL == null || anyExpR == null) { return; }
			var sumSize = parseInt(this.polyObj[anyExpL[0]].style.width) + parseInt(this.polyObj[anyExpR[0]].style.width);
			if (width < this._minWidth) { width = this._minWidth; } else if (width > sumSize - this._minWidth) { width = sumSize - this._minWidth; }
			var width2 = sumSize - width;
			//
			for (var q=0; q<anyExpL.length; q++) {
				this.polyObj[anyExpL[q]].style.width = (sep[1]=="left"?width:width2)+"px";
				this.polyObj[anyExpL[q]].childNodes[1].style.width = (sep[1]=="left"?width:width2)+"px";
				this._updateComponentsView(this.polyObj[anyExpL[q]].window);
			}
			for (var q=0; q<anyExpR.length; q++) {
				this.polyObj[anyExpR[q]].style.width = (sep[1]=="right"?width:width2)+"px";
				this.polyObj[anyExpR[q]].childNodes[1].style.width = (sep[1]=="right"?width:width2)+"px";
				this._updateComponentsView(this.polyObj[anyExpR[q]].window);
			}
		}
	}
	this._setHeight = function(pId, height) {
		if (this.polyObj[pId] == null) { return; }
		if (!Number(height)) { return; }
		var sep = null;
		//
		for (var q=0; q<this.sepHor.length; q++) {
			var p = this.sepHor[q]._top;
			if (p[p.length-1] == pId) {
				sep = new Array(this.sepHor[q], "top");
			} else if (typeof(p[p.length-1]) == "object") {
				var k = p[p.length-1];
				for (var e=0; e<k.length; e++) { if (k[e] == pId) { sep = new Array(this.sepHor[q], "top"); } }
			}
			//
			var p = this.sepHor[q]._bottom;
			if (p[0] == pId) {
				sep = new Array(this.sepHor[q], "bottom");
			} else if (typeof(p[0]) == "object") {
				var k = p[0];
				for (var e=0; e<k.length; e++) { if (k[e] == pId) { sep = new Array(this.sepHor[q], "bottom"); } }
			}
		}
		if (sep != null) {
			// allow resizing
			var set = this._findDockCellsHor(sep[0]);
			var anyExpT = set[0];
			var anyExpB = set[1];
			if (anyExpT == null || anyExpB == null) { return; }
			var sumSize = parseInt(this.polyObj[anyExpT[0]].style.height) + parseInt(this.polyObj[anyExpB[0]].style.height);
			if (height < this._minHeight) { height = this._minHeight; } else if (height > sumSize - this._minHeight) { height = sumSize - this._minHeight; }
			var height2 = sumSize - height;
			//
			for (var q=0; q<anyExpT.length; q++) {
				this.polyObj[anyExpT[q]].style.height = (sep[1]=="top"?height:height2)+"px";
				this.polyObj[anyExpT[q]].childNodes[1].style.height = (sep[1]=="top"?height:height2)-this.polyObj[anyExpT[q]].childNodes[0]._h+"px";
				this._updateComponentsView(this.polyObj[anyExpT[q]].window);
			}
			for (var q=0; q<anyExpB.length; q++) {
				this.polyObj[anyExpB[q]].style.height = (sep[1]=="bottom"?height:height2)+"px";
				this.polyObj[anyExpB[q]].childNodes[1].style.height = (sep[1]=="bottom"?height:height2)-this.polyObj[anyExpB[q]].childNodes[0]._h+"px";
				this._updateComponentsView(this.polyObj[anyExpB[q]].window);
			}
		}
	}
	this._fixInnerContentFromArray = function(obj) {
		for (var q=0; q<obj.length; q++) { if (typeof(obj[q])=="object") { this._fixInnerContentFromArray(obj[q]); } else { this._updateComponentsView(this.polyObj[obj[q]].window); } }
	}
	this._fixInnerContent = function(pId) {
		this._updateComponentsView(this.polyObj[pId].window);
	}
	this._updateComponentsView = function(win) {
		if (win.grid != null) { win.grid.setSizes(); win.grid.setSizes(); }
		if (win.tabbar) { win.tabbar.adjustOuterSize(); }
		if (win.accordion != null) { win.accordion.setSizes(); }
		if (win.layout != null) { win.layout.setSizes(win); }
		if (win.folders != null) { win.folders.setSizes(); }
		if (win.editor != null) { if (_isOpera) { window.setTimeout(function(){win.editor.adjustSize();},10); } else { win.editor.adjustSize(); } }
	}
	this.dockWindow = function(pId, wId) {
		if (this.polyObj[pId] == null) { return; }
		if (this.polyObj[pId]._win != null) { return; }
		if (this.dhxWins.window(wId) == null) { return; }
		// docking
		var win = this.dhxWins.window(wId);
		// editor fix
		if (win.editor != null) { var winEditorStoredData = win.editor.getContent(); }
		//
		win._isDocked = true;
		win._dockCell = pId;
		while (this.polyObj[pId].childNodes.length > 1) { this.polyObj[pId].removeChild(this.polyObj[pId].childNodes[1]); }
		var data = win._content;
		data.parentNode.removeChild(data);
		win.hide();
		// var bar = this.polyObj[pId].childNodes[0];
		// console.log(bar)
		data.style.width = this.polyObj[pId].style.width;
		var p = parseInt(this.polyObj[pId].style.height) - this.polyObj[pId].childNodes[0]._h;
		if (p < 0) { p = 0; }
		data.style.height = p + "px";
		this.polyObj[pId].appendChild(data);
		this.polyObj[pId]._win = wId;
		this._updateComponentsView(this.dhxWins.window(wId));
		// editor fix
		if (win.editor != null && winEditorStoredData != null) {
			var iconsPath = win.editor.iconsPath;
			win.editor = win.attachEditor();
			win.editor.setIconsPath(iconsPath);
			win.editor.init();
			win.editor.setContent(winEditorStoredData);
		}
		// ie small fixes
		if (_isIE && this.dhxWins.window(wId)._IEFixMTS == true) {
			var obj = this.dhxWins.window(wId)._content.childNodes[2];
			var pad = obj.style.paddingBottom;
			obj.style.paddingBottom = "0px";
			window.setTimeout(function(){obj.style.paddingBottom=pad;},1);
		}
		// events
		this.callEvent("onDock", [pId]);
	}
	this.unDockWindow = function(wId) {
		var p = null;
		var win = null;
		for (var a in this.polyObj) {
			if (this.polyObj[a]._win == wId) {
				p = a;
				win = this.dhxWins.window(wId);
			}
		}
		if (p != null && win != null) {
			// editor fix
			if (win.editor != null) { var winEditorStoredData = win.editor.getContent(); }
			//
			var data = win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1];
			var base = this.polyObj[p].childNodes[1];
			//
			this.polyObj[p].removeChild(base);
			var fake = document.createElement("DIV");
			fake.style.position = "relative";
			fake.innerHTML = "&nbsp;";
			this.polyObj[p].appendChild(fake);
			this.polyObj[p]._win = null;
			//
			data.appendChild(base);
			win._isDocked = false;
			if (win._isParked) {
				base.style.height = "0px";
			} else {
				win.setDimension(400, 300);
			}
			win.show();
			win.bringToTop();
			win.center();
			this._updateComponentsView(this.dhxWins.window(wId));
			// editor fix
			if (win.editor != null && winEditorStoredData != null) {
				var iconsPath = win.editor.iconsPath;
				win.editor = win.attachEditor();
				win.editor.setIconsPath(iconsPath);
				win.editor.init();
				win.editor.setContent(winEditorStoredData);
			}
			// ie small fixes
			if (_isIE && this.dhxWins.window(wId)._IEFixMTS == true) {
				var obj = this.dhxWins.window(wId)._content.childNodes[2];
				var pad = obj.style.paddingBottom;
				obj.style.paddingBottom = "0px";
				window.setTimeout(function(){obj.style.paddingBottom=pad;},1);
			}
			// events
			this.callEvent("onUnDock", [p]);
		}
	}
	this.isPanelVisible = function(pId) {
		if (this.polyObj[pId] == null) { return; }
		if (this.polyObj[pId]._collapsed == true) { return; }
		var bar = this.polyObj[pId].childNodes[0];
		var isVisible = (bar.style.display!="none");
		return isVisible;
	}
	this.showPanel = function(pId) { 
		if (this.polyObj[pId] == null) { return; }
		if (this.polyObj[pId]._collapsed == true) { return; }
		var bar = this.polyObj[pId].childNodes[0];
		if (bar._tabbarMode == -2) {
			this.dhxWins.window(bar._win).tabbar._tabZone.style.display='';
			this.dhxWins.window(bar._win).tabbar.adjustOuterSize();
			return bar._tabbarMode = -1;
		}
		if (bar._tabbarMode == -1) return;
		bar._h = this._CPanelHeight;
		this.polyObj[pId].childNodes[1].style.height = parseInt(this.polyObj[pId].style.height) - bar._h + "px";
		bar.style.display = "";
		if (_isOpera) { this._fixCellsContentOpera950(); }
	}
	this.hidePanel = function(pId) {
		if (this.polyObj[pId] == null) { return; }
		if (this.polyObj[pId]._collapsed == true) { return; }
		var bar = this.polyObj[pId].childNodes[0];
		if (typeof bar._tabbarMode == "undefined") {
			bar.style.display = "none";
		} else {
			if (bar._tabbarMode===true) {
				this.polyObj[pId].childNodes[1].style.position = "absolute";
				bar._tabbarMode = -1;
			} else if (bar._tabbarMode == -1) {
				this.dhxWins.window(bar._win).tabbar._tabZone.style.display='none'
				this.dhxWins.window(bar._win).tabbar.adjustOuterSize();
				bar._tabbarMode = -2;
			}
		}
		bar._h = 0;
		var h = parseInt(this.polyObj[pId].style.height);
		this.polyObj[pId].childNodes[1].style.height = h+"px";
		if (_isOpera) { this._fixCellsContentOpera950(); }
	}
	this.setText = function(pId, text) {
		// this._changeCPanelText(pId, text);
		this.dhxWins.window("w"+pId).setText(text);
	}
	this._changeCPanelText = function(pId, text) {
		var layout = that;
		if (layout.polyObj[pId] == null) { return; }
		layout.polyObj[pId].childNodes[0].childNodes[0].innerHTML = text;
	}
	/**
	*	@desc: iterator, calls a user-defined function n-times
	*	@param: handler - user-defined function, passed cell's object as an argument
	*	@type: public
	*/
	this.forEachItem = function(handler) {
		for (var q=0; q<this.items.length; q++) {
			handler(this.items[q]);//, this.items[q]._idd, this.items[q]._ind);
		}
	}
	this._fixPositionInWin = function(w, h) {
		this.base.style.width = w+"px";
		this.base.style.height = h+"px";
	}
	/**
	*	@desc: attaches a dhtmlxMenu to the whole container
	*	@type: public
	*/
	this.attachMenu = function() {
		this._menu = this._parentWindow.attachMenu();
		return this._menu;
	}
	/**
	*	@desc: attaches a dhtmlxToolbar to the whole container
	*	@type: public
	*/
	this.attachToolbar = function() {
		this._toolbar = this._parentWindow.attachToolbar();
		return this._toolbar;
	}
	/**
	*	@desc: attaches a status bar to the whole container
	*	@type: public
	*/
	this.attachStatusBar = function() {
		this._status = this._parentWindow.attachStatusBar();
		return this._status;
	}
	// static sizes
	this._fixSize = function(pId, width, height) {
		if (this.polyObj[pId] == null) { return; }
		this.polyObj[pId]._isBlockedWidth = width;
		this.polyObj[pId]._isBlockedHeight = height;
		this._fixSplitters();
	}
	this._fixSplitters = function() {
		// vertical splitters
		// console.log(this.sepVer)
		for (var q=0; q<this.sepVer.length; q++) {
			var data = this._findDockCellsVer(this.sepVer[q]);
			// console.log(data)
			if (data[0] == null || data[1] == null) {
				if (this.sepVer[q].className != "dhtmlxLayoutPolySplitterVerInactive") { this.sepVer[q].className = "dhtmlxLayoutPolySplitterVerInactive"; }
			} else {
				if (this.sepVer[q].className != "dhtmlxLayoutPolySplitterVer") { this.sepVer[q].className = "dhtmlxLayoutPolySplitterVer"; }
			}
		}
		// horizontal splitters
		for (var q=0; q<this.sepHor.length; q++) {
			var data = this._findDockCellsHor(this.sepHor[q]);
			if (data[0] == null || data[1] == null) {
				if (this.sepHor[q].className != "dhtmlxLayoutPolySplitterHorInactive") { this.sepHor[q].className = "dhtmlxLayoutPolySplitterHorInactive"; }
			} else {
				if (this.sepHor[q].className != "dhtmlxLayoutPolySplitterHor") { this.sepHor[q].className = "dhtmlxLayoutPolySplitterHor"; }
			}
		}
	}
	this._fixIcons = function() {
		for (var a in this.polyObj) {
			// 1. get cell index in _rowData
			var data = this.polyObj[a]._rowData;
			var cps = this.polyObj[a]._collapsed;
			var idx = -1;
			for (var q=0; q<data.length; q++) {
				if (typeof(data[q]) == "object") {
					// nothing there?
				} else {
					if (data[q] == a) { idx = q; }
				}
			}
			// 2. search first expanded item next to the right, then to the left of the collapsed cell
			var newIcon = null;
			if (idx != -1) {
				// to the right
				for (var q=idx+1; q<data.length; q++) {
					if (typeof(data[q]) == "object") {
						newIcon = (this.polyObj[a]._resize=="ver"?(cps?"b":"t"):(cps?"r":"l"));
					} else if (this.polyObj[data[q]]._collapsed == false) {
						newIcon = (this.polyObj[a]._resize=="ver"?(cps?"b":"t"):(cps?"r":"l"));
					}
				}
				if (newIcon == null && idx >= 1) {
					// to the left
					for (var q=idx-1; q>=0; q--) {
						if (typeof(data[q]) == "object") {
							newIcon = (this.polyObj[a]._resize=="ver"?(cps?"t":"b"):(cps?"l":"r"));
						} else if (this.polyObj[data[q]]._collapsed == false) {
							newIcon = (this.polyObj[a]._resize=="ver"?(cps?"t":"b"):(cps?"l":"r"));
						}
					}
				}
			}
			// 3. update icon
			if (newIcon != null) {
				var dir = this.polyObj[a]._resize;
				this.polyObj[a].childNodes[0].childNodes[4].className = "dhtmlxInfoButtonShowHide_"+dir+" dhxLayoutButton_"+this.skin+"_"+dir+(this.polyObj[a]._collapsed?"2":"1")+newIcon;
			}
		}
	}
	
	/* RESIZE IN WINDOWS */
	this._defineWindowMinDimension = function(win, inLayout) {
		if (inLayout == true) {
			var dim = new Array();
			dim[0] = parseInt(win._content.style.width);
			dim[1] = parseInt(win._content.style.height);
		} else {
			var dim = win.getDimension();
			if (dim[0] == "100%") { dim[0] = win.offsetWidth; }
			if (dim[1] == "100%") { dim[1] = win.offsetHeight; }
		}
		// getting cells which will touched by resize
		var hor = that._getNearestParents("hor");
		var ver = that._getNearestParents("ver");
		//
		if (!inLayout) {
			// window-based init, checking cells if any layout attached
			var resH = new Array();
			var resV = new Array();
			for (var a in hor) { resH[resH.length] = a; }
			for (var a in ver) { resV[resV.length] = a; }
			that._checkAlterMinSize(new Array(resH, resV));
			// calculating new avail width/height
			var hor2 = {};
			var ver2 = {};
			for (var q=0; q<that._alterSizes.length; q++) {
				var a = that._alterSizes[q][0];
				var w = that._alterSizes[q][1];
				var h = that._alterSizes[q][2];
				if (hor2[a] == null) { hor2[a] = w; } else { if (w > hor2[a]) { hor2[a] = w; } }
				if (ver2[a] == null) { ver2[a] = h; } else { if (h > ver2[a]) { ver2[a] = h; } }
			}
			for (var a in hor) { if (hor2[a] != null) { hor[a] = hor[a]-hor2[a]+that._minWidth; } }
			for (var a in ver) { if (ver2[a] != null) { ver[a] = ver[a]-ver2[a]+that._minHeight-(that.polyObj[a].childNodes[0].style.display!="none"?that.skinParams[that.skin]["cpanel_height"]:0); } }
		}
		// 1. detect available minimal width
		var minWidth = 65536;
		for (var a in hor) { if (hor[a] < minWidth) { minWidth = hor[a]; } }
		// console.log(minWidth)
		minWidth = minWidth - that._minWidth;
		minWidth = dim[0] - minWidth;
		if (minWidth < that._dimension[0]) { minWidth = that._dimension[0]; }
		// 2. detect available minimal height
		var minHeight = 65536;
		for (var a in ver) { if (ver[a] < minHeight) { minHeight = ver[a]; } }
		minHeight = minHeight - that._minHeight;
		minHeight = dim[1] - minHeight;
		if (minHeight < that._dimension[1]) { minHeight = that._dimension[1]; }
		// 3. set min dimension to window
		if (inLayout == true) {
			return new Array("", minWidth, minHeight);
		} else {
			win.setMinDimension(minWidth, minHeight);
		}
	}
	this._getNearestParents = function(resize) {
		var data = (resize=="hor"?this._autoHor:this._autoVer);
		var pool = {};
		for (var q=0; q<data.length; q++) {
			var id = data[q];
			if (this.polyObj[id]._collapsed == true && this.polyObj[id]._resize == resize) {
				// search neares parents for object
				var rowData = this.polyObj[id]._rowData;
				var e = -1;
				for (var w=0; w<rowData.length; w++) { if (typeof(rowData[w]) == "object") { e = w; } else { if (rowData[w] == id) e = w; } }
				var r = e;
				id = null;
				if (e > 0) { for (var w=e-1; w>=0; w--) { if (typeof(rowData[w]) == "object") { id = rowData[w]; } else { if (this.polyObj[rowData[w]]._collapsed == false && id == null) { id = rowData[w]; } } } }
				if (id == null) { for (var w=r; w<rowData.length; w++) { if (typeof(rowData[w]) == "object") { id = rowData[w]; } else { if (this.polyObj[rowData[w]]._collapsed == false && id == null) { id = rowData[w]; } } } }
			}
			if (id != null) {
				if (typeof(id) == "string") { id = new Array(id); }
				for (var w=0; w<id.length; w++) {
					pool[id[w]] = parseInt(resize=="hor"?this.polyObj[id[w]].style.width:this.polyObj[id[w]].style.height);
				}
			}
		}
		
		// console.log(resize, pool)
		
		return pool;
	}
	
	this.adjustOuterSize = function() {
		// not implemented yet
	}
	
	/**
	*	@desc: sets outer size for the container in case of a window-based initialization
	*	@param: winObj - dhtmlxWindow object (layout's parent)
	*	@type: public
	*/
	this.setSizes = function(winObj) {
		// console.log(1)
		var bw = parseInt(this.base.style.width);
		var bh = parseInt(this.base.style.height);
		//
		// var ww = parseInt(winObj._content.style.width);
		// var wh = parseInt(winObj._content.style.height);
		
		// var ww = winObj._content.offsetWidth-(_isIE&&winObj._isFullScreened?4:0);
		// var wh = winObj._content.childNodes[2].offsetHeight-(_isIE&&winObj._isFullScreened?4:0);
		var ww = winObj._content.offsetWidth;
		var wh = winObj._content.childNodes[2].offsetHeight;
		//
		if (_isIE) {
			if (winObj.sb != null) { wh = wh-winObj._sbH; }
			if (winObj.menu != null) { wh = wh-winObj._menuH; }
			if (winObj.toolbar != null) { wh = wh-winObj._toolbarH; }
		}
		//
		var ax = ww-bw;
		var ay = wh-bh;
		//
		var hor = this._getNearestParents("hor");
		var ver = this._getNearestParents("ver");
		var both = {};
		//
		for (var a in hor) {
			both[a] = a;
			this.polyObj[a].style.width = hor[a]+ax+"px";
			this.polyObj[a].childNodes[1].style.width = hor[a]+ax+"px";
		}
		for (var a in ver) {
			both[a] = a;
			this.polyObj[a].style.height = ver[a]+ay+"px";
			this.polyObj[a].childNodes[1].style.height = ver[a]-this.polyObj[a].childNodes[0]._h+ay+"px";
		}
		// main
		this.base.style.width = ww+"px";
		this.base.style.height = wh+"px";
		// inner content fixes
		for (var a in both) { this._updateComponentsView(this.dhxWins.window(this.polyObj[a]._win)); }
		//
		this.callEvent("onResizeFinish", []);
	}
	
	this._cleatTDActions = function(obj) {
		obj._dir = null;
		obj._top = null;
		obj._bottom = null;
		obj._left = null;
		obj._right = null;
		obj._dblClick = null;
		obj._minW = null;
		obj._minH = null;
		obj._initCPanel = null;
		obj._resize = null;
		obj._rowData = null;
		obj.onselectstart = null;
		obj.onmousedown = null;
		obj.onmouseup = null;
		obj.onmousemove = null;
		obj.onclick = null;
		obj.ondblclick = null;
	}
	
	this.clearAll = function() {
		// closing object
		for (var a in this.polyObj) {
			// if (this.dhxWins.window(this.polyObj[a]._win) != null) { this.dhxWins.window(this.polyObj[a]._win).close(); }
			var bar = this.polyObj[a].childNodes[0];
			while (bar.childNodes.length > 0) {
				this._cleatTDActions(bar.childNodes[0]);
				bar.removeChild(bar.childNodes[0]);
			}
			bar = null;
			while (this.polyObj[a].childNodes.length > 0) {
				this.polyObj[a].removeChild(this.polyObj[a].childNodes[0]);
			}
			delete this.polyObj[a];
		}
		// destroy table
		while (this.tpl.childNodes[0].childNodes.length > 0) {
			var tr = this.tpl.childNodes[0].childNodes[0];
			while (tr.childNodes.length > 0) {
				this._cleatTDActions(tr.childNodes[0]);
				tr.removeChild(tr.childNodes[0]);
			}
			this.tpl.childNodes[0].removeChild(tr);
			tr = null;
		}
	}
	
	this.dhx_Event();
	this.dhxLayout_destructor();
	this._init();
}
dhtmlXLayoutObject.prototype.dhx_Event = function() {
	this.dhx_SeverCatcherPath="";
	/**
	*   @desc: attaches an event handler to a dhtmlxLayout
	*   @param: original - event's original name
	*   @param: catcher - event handler
	*   @param: CallObj - object that will call the event
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
	*   @desc: returns true if the event exists
	*   @param: name - event's name
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
dhtmlXLayoutObject.prototype.dhxLayout_destructor = function() {
	this.destructor = function() {
		// single variables
		var vars = new Array("_CPanelBtnsWidth", "_CPanelHeight", "_resFunc", "_resObj", "_resX", "_resY", "_totalCols", "_totalRows", "_autoHor", "_autoVer",
				     "_anyExpB", "_anyExpL", "_anyExpR", "_anyExpT", "_layoutView", "_minHeight", "_minWidth", "_availAutoSize", "_dimension", "_effects",
				     "_collapsedH", "_collapsedW", "_colsRatio", "_rowsRatio", "h", "w", "skin", "imagePath");
		for (var q=0; q<vars.length; q++) { delete this[vars[q]]; }
		vars = null;
		// separators
		var seps = new Array("sepHor", "sepVer");
		var vars = new Array("_bottom", "_top", "_left", "_right", "_dblClick", "_dir", "_isLayoutCell", "_isSep");
		var funcs = new Array("ondblclick", "onmousedown", "onmouseup", "onselectstart");
		for (var w=0; w<seps.length; w++) {
			for (var a in this[seps[w]]) {
				var sep = this[seps[w]][a];
				sep.className = null;
				for (var q=0; q<vars.length; q++) { delete sep[vars[q]]; }
				for (var q=0; q<funcs.length; q++) { sep[funcs[q]] = null; delete sep[funcs[q]]; }
				sep = null;
			}
			delete this[seps[w]];
		}
		vars = null;
		funcs = null;
		seps = null;
		// objects
	}
	
}
