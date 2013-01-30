//v.2.0 build 81107

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
/**
*	@desc: constructor, creates a single window under dhtmlxWindows system
*	@pseudonym: win
*	@type: public
*/
function dhtmlXWindowsSngl(){
	
}
/**
*	@desc: constructor, creates a button for a window under dhtmlxWindows system
*	@pseudonym: btn
*	@type: public
*/
function dhtmlXWindowsBtn(){
	
}

/**
*   @desc: constructor, creates a new dhtmlxWindows object
*   @type: public
*/
function dhtmlXWindows() {
	
	var that = this;
	// image manipulation
	this.pathPrefix = "dhxwins_";
	this.imagePath = globalBaseUrl+"/img/";
	/**
	*   @desc: sets path to the directory where used images are located
	*   @param: path - url to the directory where images are located
	*   @type: public
	*/
	this.setImagePath = function(path) {
		this.imagePath = path;
	}
	
	// skins
	this.skin = "dhx_blue";
	this.skinParams = { // standard
			    "standard"		: { "header_height": 32, "border_left_width": 6, "border_right_width": 7, "border_bottom_height": 6 },
			    // aqua
			    "aqua_dark"		: { "header_height": 31, "border_left_width": 3, "border_right_width": 3, "border_bottom_height": 3 },
			    "aqua_orange"	: { "header_height": 31, "border_left_width": 3, "border_right_width": 3, "border_bottom_height": 3 },
			    "aqua_sky"		: { "header_height": 31, "border_left_width": 3, "border_right_width": 3, "border_bottom_height": 3 },
			    // clear
			    "clear_blue"	: { "header_height": 32, "border_left_width": 6, "border_right_width": 6, "border_bottom_height": 6 },
			    "clear_green"	: { "header_height": 32, "border_left_width": 6, "border_right_width": 6, "border_bottom_height": 6 },
			    "clear_silver"	: { "header_height": 32, "border_left_width": 6, "border_right_width": 6, "border_bottom_height": 6 },
			    // glassy
			    "glassy_blue"	: { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    "glassy_blue_light"	: { "header_height": 26, "border_left_width": 3, "border_right_width": 3, "border_bottom_height": 3 },
			    "glassy_caramel"	: { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    "glassy_greenapple"	: { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    "glassy_rainy"	: { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    "glassy_raspberries": { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    "glassy_yellow"	: { "header_height": 26, "border_left_width": 4, "border_right_width": 4, "border_bottom_height": 4 },
			    // modern
			    "modern_black"	: { "header_height": 39, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 },
			    "modern_blue"	: { "header_height": 39, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 },
			    "modern_red"	: { "header_height": 39, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 },
			    // web
			    "web"		: { "header_height": 21, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 },
			    // vista
			    "vista_blue"	: { "header_height": 28, "border_left_width": 8, "border_right_width": 8, "border_bottom_height": 8 },
			    // dhx
			    "dhx_black"		: { "header_height": 21, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 },
			    "dhx_blue"		: { "header_height": 21, "border_left_width": 2, "border_right_width": 2, "border_bottom_height": 2 }
			    
	};
	/**
	*   @desc: changes window's skin
	*   @param: skin - skin's name
	*   @type: public
	*/
	this.setSkin = function(skin) {
		this.skin = skin;
		this._redrawSkin();
	}
	this._redrawSkin = function() {
		
		for (var a in this.wins) {
			var win = this.wins[a];
			var skinParams = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
			//
			win.childNodes[0].className = "dhtmlx_wins_"+this.skin;
			// icon
			win.childNodes[1].className = "dhtmlx_wins_icon_"+this.skin;
			this._restoreWindowIcons(win);
			// title
			win.childNodes[2].className = "dhtmlx_wins_title_"+this.skin;
			// butons
			win.childNodes[3].className = "dhtmlx_wins_buttons_"+this.skin;
			this._redrawWindow(win);
		}
		// this._restoreWindowIcons(this.getTopmostWindow());
	}
	
	// return true if window with specified id is exists
	/**
	*   @desc: returns true if the window with specified id exists
	*   @param: id
	*   @type: public
	*/
	this.isWindow = function(id) {
		var t = (this.wins[id] != null);
		return t;
	}
//#wind_uber:09062008{}
	// return array of handlers finded by text
	/**
	*   @desc: returns array of window handlers found by header text
	*   @param: id
	*   @type: public
	*/
	this.findByText = function(text) {
		var wins = new Array();
		for (var a in this.wins) {
			if (this.wins[a].getText().search(text, "gi") >= 0) {
				wins[wins.lentgh] = this.wins[a];
			}
		}
		return wins;
	}
//#}	
	// return handler by id
	/**
	*   @desc: returns the window handler (dhtmlXWindowSngl object) found by id
	*   @param: id
	*   @type: public
	*/
	this.window = function(id) {
		var win = null;
		if (this.wins[id] != null) { win = this.wins[id]; }
		return win;
	}
//#wind_uber:09062008{
	// iterator
	/**
	*   @desc: iterator - goes through all windows and calls a user handler
	*   @param: hander (user function)
	*   @type: public
	*/
	this.forEachWindow = function(handler) {
		for (var a in this.wins) {
			handler(this.wins[a]);
		}
	}
	
	// return bottommost focused window handler
	/**
	*   @desc: returns the bottommost window
	*   @type: public
	*/
	this.getBottommostWindow = function() {
		var bottommost = this.getTopmostWindow();
		for (var a in this.wins) {
			if (this.wins[a].zi < bottommost.zi) {
				bottommost = this.wins[a];
			}
		}
		return (bottommost.zi != 0 ? bottommost : null);
	}
//#}	

	// return topmost focused window handler
	/**
	*   @desc: returns the topmost window
	*   @type: public
	*/
	this.getTopmostWindow = function(visibleOnly) {
		var topmost = {"zi": 0};
		for (var a in this.wins) {
			
			if (this.wins[a].zi > topmost.zi) {
				if (visibleOnly == true && !this._isWindowHidden(this.wins[a])) {
					topmost = this.wins[a];
				}
				if (visibleOnly != true) {
					topmost = this.wins[a];
				}
			}
		}
		return (topmost.zi != 0 ? topmost : null);
	}
	
	// windows storage
	this.wins = {};
	
	// viewport
	this.autoViewport = true;
	this._createViewport = function() {
		this.vp = document.body;
		// modal cover
		this.modalCoverI = document.createElement("IFRAME");
		this.modalCoverI.frameBorder = "0";
		this.modalCoverI.className = "dhx_modal_cover_ifr";
		this.modalCoverI.style.display = "none";
		this.modalCoverI.style.zIndex = 0;
		this.vp.appendChild(this.modalCoverI);
		this.modalCoverD = document.createElement("DIV");
		this.modalCoverD.className = "dhx_modal_cover_dv";
		this.modalCoverD.style.display = "none";
		this.modalCoverD.style.zIndex = 0;
		this.vp.appendChild(this.modalCoverD);
		// vp move/resize cover
		this._vpcover = document.createElement("DIV");
		this._vpcover.className = "dhx_content_vp_cover";
		this._vpcover.style.display = "none";
		this.vp.appendChild(this._vpcover);
		// resize/move carcass
		this._carcass = document.createElement("DIV");
		this._carcass.className = "dhx_carcass_resmove";
		this._carcass.style.display = "none";
		if (_isIE) {
			this._carcass.innerHTML = "<iframe border=0 frameborder=0 style='filter: alpha(opacity=0); width: 100%; height:100%; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;'></iframe><div style='position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;'></div>";
		}
		this._carcass.onselectstart = function(e) {
			e = e||event;
			e.returnValue = false;
		}
		this.vp.appendChild(this._carcass);
	}
	this._autoResizeViewport = function() {
		for (var a in this.wins) {
			if (this.wins[a]._isFullScreened) {
				this.wins[a]._content.style.width = document.body.offsetWidth-(_isIE?4:0)+"px";
				// doctype fix
				if (document.body.offsetHeight == 0) {
					if (window.innerHeight) {
						this.wins[a]._content.style.height = window.innerHeight+"px";
					} else {
						this.wins[a]._content.style.height = document.body.scrollHeight+"px";
					}
				} else {
					this.wins[a]._content.style.height = document.body.offsetHeight-(_isIE?4:0)+"px";
				}
				// this.wins[a]._content.style.height = document.body.offsetHeight-(_isIE?4:0)+"px";
				if (this.wins[a].layout != null && _isOpera) { this.wins[a].layout._fixCellsContentOpera950(); }
				this._fixInnerObjs(this.wins[a]);
			}
			if (this.wins[a]._isMaximized && this.wins[a].style.display != "none") {
				this._restoreWindow(this.wins[a]);
				this._maximizeWindow(this.wins[a]);
			}
		}
		
		if (this.vp == document.body) { return; }
		if (this.autoViewport == false) { return; }
		this.vp.style.width = (_isIE ? document.body.offsetWidth - 4 : window.innerWidth) + "px";
		this.vp.style.height = (_isIE ? document.body.offsetHeight - 4 : window.innerHeight) + "px";
		//
		// check windows out of viewports edge
		for (var a in this.wins) {
			var win = this.wins[a];
			var overX = false;
			var overY = false;
			if (win.x > this.vp.offsetWidth - 10) {
				win.x = this.vp.offsetWidth - 10;
				overX = true;
			}
			var skinParams = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
			if (win.y + skinParams["header_height"] > this.vp.offsetHeight) {
				win.y = this.vp.offsetHeight - skinParams["header_height"];
				overY = true;
			}
			if (overX || overY) {
				this._redrawWindow(win);
			}
		}
	}
	/**
	*   @desc: if true - allows an object to adjust the viewport automatically to document.body
	*   @param: state - true|false
	*   @type: public
	*/
	this.enableAutoViewport = function(state) {
		
		if (this.vp != document.body) { return; }
		this.autoViewport = state;
		if (state == false) {
			this.vp = document.createElement("DIV");
			this.vp.className = "dhtmlx_winviewport";
			this.vp.style.left = "0px";
			this.vp.style.top = "0px";
			document.body.appendChild(this.vp);
			this.vp.ax = 0;
			this.vp.ay = 0;
			this._autoResizeViewport();
			this.vp.appendChild(this.modalCoverI);
			this.vp.appendChild(this.modalCoverD);
			this.vp.appendChild(this._carcass);
		}
	}
	/**
	*   @desc: attaches a vp to an existing object on page (renders an object as a viewport)
	*   @param: objId - object id
	*   @type: public
	*/
	this.attachViewportTo = function(objId) {
		if (this.autoViewport == false) {
			if (this.vp != document.body) { this.vp.parentNode.removeChild(this.vp); }
			this.vp = document.getElementById(objId);
			this.vp.style.position = "relative";
			this.vp.style.overflow = "hidden";
			this.vp.ax = 0;
			this.vp.ay = 0;
			this.vp.appendChild(this.modalCoverI);
			this.vp.appendChild(this.modalCoverD);
			this.vp.appendChild(this._carcass);
		}
	}
	/**
	*   @desc: sets user-defined viewport if enableAutoViewport(false)
	*   @param: x - top-left viewport corner's X-coordinate
	*   @param: y - top-left viewport corner's Y-coordinate
	*   @param: width - viewport's width
	*   @param: height - viewport's height
	*   @type: public
	*/
	this.setViewport = function(x, y, width, height, parentObj) {
		if (this.autoViewport == false) {
			this.vp.style.left = x + "px";
			this.vp.style.top = y + "px";
			this.vp.style.width = width + "px";
			this.vp.style.height = height + "px";
			// attach to parent
			if (parentObj != null) { parentObj.appendChild(this.vp); }
			this.vp.ax = getAbsoluteLeft(this.vp);
			this.vp.ay = getAbsoluteTop(this.vp);
		}
	}
	// effects
	this._effects = {"move" : false, "resize" : false};
	/**
	*   @desc: sets a visual effect
	*   @param: efName - effect's name
	*   @param: efValue - true/false to enable/disable
	*   @type: public
	*/
	this.setEffect = function(efName, efValue) {
		if ((this._effects[efName] != null) && (typeof(efValue) == "boolean")) {
			this._effects[efName] = efValue;
		}
	}
	/**
	*   @desc: returns true if the effect is enabled
	*   @param: efName - effect's name
	*   @type: public
	*/
	this.getEffect = function(efName) {
		return this._effects[efName];
	}
	// windows
	/**
	*   @desc: creates a new window and returns its handler
	*   @param: id - window's id
	*   @param: x - top-left window corner's X-coordinate
	*   @param: y - top-left window corner's Y-coordinate
	*   @param: width - window's width
	*   @param: height - window's height
	*   @type: public
	*/
	this.createWindow = function(id, x, y, width, height) {
		var win = document.createElement("DIV");
		win.className = "dhtmlx_window_inactive";
		// move all available windows up
		for (var a in this.wins) {
			this.wins[a].zi += this.zIndexStep;
			this.wins[a].style.zIndex = this.wins[a].zi;
		}
		// bottom, bring on top will at the end of createWindow function
		win.zi = this.zIndexStep;// this._getTopZIndex(true) + this.zIndexStep;
		win.style.zIndex = win.zi;
		//
		win.active = false;
		//
		win._isWindow = true;
		
		win.isWindow = true;
		//
		// win.that = this;
		//
		win.w = width;
		win.h = height;
		win.x = x;
		win.y = y;
		this._fixWindowPositionInViewport(win);
		//
		win.style.width = win.w + "px";
		win.style.height = win.h + "px";
		win.style.left = win.x + "px";
		win.style.top = win.y + "px";
		win._isModal = false;
		// resize params
		win._allowResize = true;
		win.maxW = "auto"; // occupy all viewport on click or 
		win.maxH = "auto";
		win.minW = 200;
		win.minH = 140;
		win.iconsPresent = true;
		win.icons = new Array(this.imagePath+this.pathPrefix+this.skin+"/active/icon_normal.gif", this.imagePath+this.pathPrefix+this.skin+"/inactive/icon_normal.gif");
		//
		win._allowMove = true;
		win._allowMoveGlobal = true;
		win._allowResizeGlobal = true;
		//
		win._keepInViewport = false;
		//
		var skin = this.skinParams[this.skin];
		win.idd = id;
		win._midd = "dhxWMNObj_"+this._genStr(12);
		win._tidd = "dhxWTBObj_"+this._genStr(12);
		win._sidd = "dhxSTBObj_"+this._genStr(12);
		//
		win.innerHTML = "<table border='0' cellspacing='0' cellpadding='0' width='100%' height='"+win.h+"' class='dhtmlx_wins_"+this.skin+"'>"+
					// head
					"<tr><td class='dhtmlx_wins_td_header_full' clearonselect='yes'>"+
						"<table border='0' cellspacing='0' cellpadding='0' width='100%' class='dhtmlx_wins_header' clearonselect='yes'>"+
							"<tr>"+
								"<td class='dhtmlx_wins_td_header_left' clearonselect='yes'>&nbsp;</td>"+
								"<td class='dhtmlx_wins_td_header_middle' clearonselect='yes'>&nbsp;</td>"+
								"<td class='dhtmlx_wins_td_header_right' clearonselect='yes'>&nbsp;</td>"+
							"</tr>"+
						"</table>"+
						(_isIE?"<iframe frameborder='0' class='dhx_ie6_wincover_forsel' onload='this.contentWindow.document.body.style.overflow=\"hidden\";'></iframe>":"")+
					"</td></tr>"+
					// body
					"<tr><td class='dhtmlx_wins_td_body_full' height='"+(win.h-skin["header_height"])+"'>"+
						"<table border='0' cellspacing='0' cellpadding='0' width='100%' height='"+(win.h-skin["header_height"])+"' class='dhtmlx_wins_body'>"+
							// window middle row
							"<tr>"+
								"<td class='dhtmlx_wins_body_border_middle_left' clearonselect='yes'>&nbsp;</td>"+
								"<td class='dhtmlx_wins_body_content' align='left' valign='top'>"+
									"<div class='dhtmlx_wins_body_content' style='width: "+(win.w-skin["border_left_width"]-skin["border_right_width"])+"px; height:"+(win.h-skin["header_height"]-skin["border_bottom_height"])+"px;'>"+
										"<div id='"+win._midd+"' class='dhtmlxMenuInWin' style='height: 0px; display: none; position: relative;'></div>"+
										"<div id='"+win._tidd+"' class='dhtmlxToolbarInWin' style='height: 0px; display: none; position: relative;'></div>"+
										"<div class='dhtmlxWindowMainContent' style='position: absolute; overflow: hidden; width=100%; top: 0px; bottom: 0px; height=100%; left: 0px; right: 0px;'></div>"+
										"<div id='"+win._sidd+"' style='height: 0px; display: none;'></div>"+
										"<div class='dhx_content_cover_blocker' style='display: none;'></div>"+
									"</div>"+
								"</td>"+
								"<td class='dhtmlx_wins_body_border_middle_right' clearonselect='yes'>&nbsp;</td>"+
							"</tr>"+
							// window bottom row
							"<tr clearonselect='yes'>"+
								"<td class='dhtmlx_wins_body_border_bottom_left' clearonselect='yes'>&nbsp;</td>"+
								"<td class='dhtmlx_wins_body_border_bottom_middle' clearonselect='yes'>&nbsp;</td>"+
								"<td class='dhtmlx_wins_body_border_bottom_right' clearonselect='yes'>&nbsp;</td>"+
							"</tr>"+
						"</table>"+
						(_isIE?"<iframe frameborder='0' style='top:"+skin["header_height"]+"px;' class='dhx_ie6_wincover_forsel' onload='this.contentWindow.document.body.style.overflow=\"hidden\";'></iframe>":"")+
					"</td></tr>"+
				"</table>"+
				// window icon
				"<img clearonselect='yes' class='dhtmlx_wins_icon_"+this.skin+"' src='"+win.icons[0]+"'>"+
				// window title
				"<div clearonselect='yes' class='dhtmlx_wins_title_"+this.skin+"'>dhtmlxWindow</div>"+
				// buttons
				"<div class='dhtmlx_wins_buttons_"+this.skin+"'>"+
					"<table border='0' cellspacing='0' cellpadding='0'><tr></tr></table>"+
				"</div>"+
				// progress
				"<div clearonselect='yes' class='dhtmlx_wins_progress_"+this.skin+"'></div>"+
				"";
		this.vp.appendChild(win);
		//
		win._content = win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0];
		this._diableOnSelectInWin(win, true);
		//
		this.wins[id] = win;
		//
		win.dhx_Event = this.dhx_Event;
		win.dhx_Event();
		//
		this._makeActive(win, true);
		// moving
		var hdr = win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0]; // table class='header'
		// hdr.that = this;
		// hdr.win = win;
		hdr.onmousedown = function(e) {
			if (!win._allowMove || !win._allowMoveGlobal) { return; }
			e = e || event;
			// save last coords to determine moveFinish event
			win.oldMoveX = win.x;
			win.oldMoveY = win.y;
			//
			win.moveOffsetX = win.x - e.clientX;
			win.moveOffsetY = win.y - e.clientY;
			that.movingWin = win;
			// carcass
			if (that._effects["move"] == false) {
				that._carcass.x = that.movingWin.x;
				that._carcass.y = that.movingWin.y;
				that._carcass.w = parseInt(that.movingWin.style.width)+(_isIE?0:-2);
				that._carcass.h = parseInt(that.movingWin.style.height)+(_isIE?0:-2);
				that._carcass.style.left = that._carcass.x+"px";
				that._carcass.style.top = that._carcass.y+"px";
				that._carcass.style.width = that._carcass.w+"px";
				that._carcass.style.height = that._carcass.h+"px";
				// that._carcass.style.zIndex = that.movingWin.style.zIndex+1;
				that._carcass.style.zIndex = that.movingWin.style.zIndex+that._getTopZIndex()+10;
				
				that._carcass.style.cursor = "move";
				that._carcass._keepInViewport = win._keepInViewport;
				// that._carcass.style.display = "";
			}
			that._blockSwitcher("none");
			// cursor
			that.movingWin.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.cursor = "move";
			that.movingWin.childNodes[2].style.cursor = "move";
			// vpcover
			that._vpcover.style.zIndex = that.movingWin.style.zIndex-1;
			that._vpcover.style.display = "";
			// disabling select for opera
			if (_isOpera) {
				e.returnValue = false;
				e.cancelBubble = true;
			}
		}
		
		hdr.ondblclick = function() {
			// maximize/minimize
			if (win._allowResizeGlobal && !win._isParked) {
				if (win._isMaximized == true) {
					that._restoreWindow(win);
				} else {
					that._maximizeWindow(win);
				}
			}
			// parkup/parkup
			/*
			if (win._isParkedAllowed && win.button("park").isEnabled()) {
				that._parkWindow(win);
			}
			*/
		}
		
		var h_title = win.childNodes[2];
		h_title.onmousedown = hdr.onmousedown;
		h_title.ondblclick = hdr.ondblclick;
		
		// set text
		/**
		*   @desc: sets window's header text
		*   @param: text
		*   @type: public
		*/
		win.setText = function(text) {
			this.childNodes[2].innerHTML = text;
			that.callEvent("onTextChange", [this._dockCell, text]);
		}
		// get text
		/**
		*   @desc: returns window's header text
		*   @type: public
		*/
		win.getText = function() {
			return this.childNodes[2].innerHTML;
		}
		// het id by handler
		/**
		*   @desc: returns window's id
		*   @type: public
		*/
		win.getId = function() {
			return this.idd;
		}
		// show
		/**
		*   @desc: shows a window
		*   @type: public
		*/
		win.show = function() {
			that._showWindow(this);
		}
		// hide
		/**
		*   @desc: hides a window
		*   @type: public
		*/
		win.hide = function() {
			that._hideWindow(this);
		}
		// minimize
		/**
		*   @desc: minimizes a window
		*   @type: public
		*/
		win.minimize = function() {
			that._restoreWindow(this);
		}
		// maximize
		/**
		*   @desc: maximizes a window
		*   @type: public
		*/
		win.maximize = function() {
			that._maximizeWindow(this);
		}
		// close
		/**
		*   @desc: closes a window
		*   @type: public
		*/
		win.close = function() {
			that._closeWindow(this);
		}
		// park
		/**
		*   @desc: parks a window (next action is based on window's current state)
		*   @type: public
		*/
		win.park = function() {
			if (this._isParkedAllowed) {
				that._parkWindow(this);
			}
		}
		// stick/unstick
		/**
		*   @desc: sticks a window
		*   @type: public
		*/
		win.stick = function() {
			that._stickWindow(this);
		}
		/**
		*   @desc: unsticks a window
		*   @type: public
		*/
		win.unstick = function() {
			that._unstickWindow(this);
		}
		/**
		*   @desc: returns true if the window is sticked
		*   @type: public
		*/
		win.isSticked = function() {
			return this._isSticked;
		}
		// set icon
		/**
		*   @desc: sets window's header icon
		*   @param: iconEnabled - url to the icon for the enabled state
		*   @param: iconDisabled - url to the icon for the disabled state
		*   @type: public
		*/
		win.setIcon = function(iconEnabled, iconDisabled) {
			that._setWindowIcon(win, iconEnabled, iconDisabled);
		}
		// return array(iconEnabled, iconDisabled) icons for window
		/**
		*   @desc: returns current window's header icon
		*   @param: text
		*   @type: public
		*/
		win.getIcon = function() {
			return that._getWindowIcon(this);
		}
		// clear icon
		/**
		*   @desc: clears window's header icon
		*   @type: public
		*/
		win.clearIcon = function() {
			that._clearWindowIcons(this);
		}
		// restore default window icon according the loaded skin
		/**
		*   @desc: restores default window's header icon (based on skin)
		*   @type: public
		*/
		win.restoreIcon = function() {
			that._restoreWindowIcons(this);
		}
		//
		/**
		*   @desc: keeps a window within the viewport
		*   @param: state - if true - window is not allowed to be placed outside the viewport
				    if false - window is not allowed to be placed outside the viewport leaving only a small part of its header within the viewport
		*   @type: public
		*/
		win.keepInViewport = function(state) {
			this._keepInViewport = state;
		}
		// ask window be/not be modal
		/**
		*   @desc: makes a window modal/modeless
		*   @param: state - true|false
		*   @type: public
		*/
		win.setModal = function(state) {
			if (state == true) {
				if (that.modalWin != null || that.modalWin == this) { return; }
				that._setWindowModal(this, true);
			} else {
				if (that.modalWin != this) { return; }
				that._setWindowModal(this, false);
			}
		}
		// return true if window is modal
		/**
		*   @desc: returns true if the window is modal
		*   @type: public
		*/
		win.isModal = function() {
			return this._isModal;
		}
		// return true if window is hidden
		/**
		*   @desc: returns true if the window is hidden
		*   @type: public
		*/
		win.isHidden = function() {
			return that._isWindowHidden(this);
		}
		// return true if window is maximized
		/**
		*   @desc: returns true if the window is maximized
		*   @type: public
		*/
		win.isMaximized = function() {
			return this._isMaximized;
		}
		// return true if window is parkded
		/**
		*   @desc: returns true if the window is parked
		*   @type: public
		*/
		win.isParked = function() {
			return this._isParked;
		}
		// allow/deny park
		/**
		*   @desc: allows a window to be parked
		*   @type: public
		*/
		win.allowPark = function() {
			that._allowParking(this);
		}
		/**
		*   @desc: denies a window from parking
		*   @type: public
		*/
		win.denyPark = function() {
			that._denyParking(this);
		}
		/**
		*   @desc: returns true if the window is parkable
		*   @type: public
		*/
		win.isParkable = function() {
			return this._isParkedAllowed;
		}
		// allow/deny for allow window to be resized
		/**
		*   @desc: allows a window to be resized
		*   @type: public
		*/
		win.allowResize = function() {
			that._allowReszieGlob(this);
		}
		/**
		*   @desc: denies a window from resizing
		*   @type: public
		*/
		win.denyResize = function() {
			that._denyResize(this);
		}
		// return true if window resizeable
		/**
		*   @desc: returns true if the window is resizable
		*   @type: public
		*/
		win.isResizable = function() {
			return this._allowResizeGlobal;
		}
		// move
		/**
		*   @desc: allows a window to be moved
		*   @type: public
		*/
		win.allowMove = function() {
			if (!this._isMaximized) { this._allowMove = true; }
			this._allowMoveGlobal = true;
		}
		/**
		*   @desc: denies a window from moving
		*   @type: public
		*/
		win.denyMove = function() {
			this._allowMoveGlobal = false;
		}
		/**
		*   @desc: returns true if the window is movable
		*   @type: public
		*/
		win.isMovable = function() {
			return this._allowMoveGlobal;
		}
		// bring window to top and set focus
		/**
		*   @desc: brings/sends a window on top (z-positioning)
		*   @type: public
		*/
		win.bringToTop = function() {
			that._bringOnTop(this);
			that._makeActive(this);
		}
		// bring window to bottom and set focus
		/**
		*   @desc: brings/sends a window to bottom (z-positioning)
		*   @type: public
		*/
		win.bringToBottom = function() {
			that._bringOnBottom(this);
		}
		// return true if window is on top
		/**
		*   @desc: returns true if the window is on top
		*   @type: public
		*/
		win.isOnTop = function() {
			return that._isWindowOnTop(this);
		}
		// return true if window if on bottom
		/**
		*   @desc: returns true if the window is on bottom
		*   @type: public
		*/
		win.isOnBottom = function() {
			return that._isWindowOnBottom(this);
		}
		// set new position for window, if it will outlay the viewport it was moved into it visible area
		/**
		*   @desc: sets window's position (moves a window to the point set by user)
		*   @param: x - x coordinate
		*   @param: y - y coordinate
		*   @type: public
		*/
		win.setPosition = function(x, y) {
			this.x = x;
			this.y = y;
			that._fixWindowPositionInViewport(this);
			// fixing mozilla artefakts
			if (_isFF) {
				this.h++;
				that._redrawWindow(this);
				this.h--;
			}
			that._redrawWindow(this);
		}
		// return array(x, y) with position of window
		/**
		*   @desc: returns current window's position
		*   @type: public
		*/
		win.getPosition = function() {
			return new Array(this.x, this.y);
		}
		// set new dimension for window, if it will outlay the viewport it was moved into it visible area
		/**
		*   @desc: sets window's dimension
		*   @param: width
		*   @param: height
		*   @type: public
		*/
		win.setDimension = function(width, height) {
			if (width != null) { this.w = width; }
			if (height != null) { this.h = height; }
			that._fixWindowDimensionInViewport(this);
			that._fixWindowPositionInViewport(this);
			that._redrawWindow(this);
		}
		// return array(width, height) with current dimension of window
		/**
		*   @desc: returns current window's dimension
		*   @type: public
		*/
		win.getDimension = function() {
			return new Array(this.w, this.h);
		}
		// set max dimension for window
		/**
		*   @desc: sets max window's dimension
		*   @param: maxWidth
		*   @param: maxHeight
		*   @type: public
		*/
		win.setMaxDimension = function(maxWidth, maxHeight) {
			this.minW = "auto"; // maxWidth;
			this.minH = "auto"; // maxHeight;
			that._redrawWindow(this);
		}
		// return array(maxWidth, maxheight) with max dimension for window
		/**
		*   @desc: returns current max window's dimension
		*   @type: public
		*/
		win.getMaxDimension = function() {
			return new Array(this.maxW, this.maxH);
		}
		// set min dimensuion for window
		/**
		*   @desc: sets min window's dimension
		*   @param: minWidth
		*   @param: minHeight
		*   @type: public
		*/
		win.setMinDimension = function(minWidth, minHeight) {
			if (minWidth != null) { this.minW = minWidth; }
			if (minHeight != null) { this.minH = minHeight; }
			that._fixWindowDimensionInViewport(this);
			that._redrawWindow(this);
		}
		// return array(minWidth, minHeight) with min dimension for window
		/**
		*   @desc: returns current min window's dimension
		*   @type: public
		*/
		win.getMinDimension = function() {
			return new Array(this.minW, this.minH);
		}
//#wind_buttons:09062008{
		// add user button
		/**
		*   @desc: adds a user button
		*   @param: id - button's id
		*   @param: pos - button's position
		*   @param: title - button's tooltip
		*   @param: label - button's name (according to css)
		*   @type: public
		*/
		win.addUserButton = function(id, pos, title, label) {
			var userBtn = that._addUserButton(this, id, pos, title, label);
			return userBtn;
		}
		// remove user button
		/**
		*   @desc: removes a user button
		*   @param: id - button's id
		*   @type: public
		*/
		win.removeUserButton = function(id) {
			if (!((id == "minmax1") || (id == "minmax2") || (id == "park") || (id == "close") || (id == "stick") || (id == "unstick") || (id == "help"))) {
				var btn = this.button(id);
				// if (btn != null) { that._removeUserButton(win, id, btn); }
				if (btn != null) { that._removeUserButton(this, id, btn); }
			}
		}
//#}				
		/**
		*   @desc: shows a progress indicator
		*   @type: public
		*/
		win.progressOn = function() {
			that._switchProgress(this, true);
		}
		/**
		*   @desc: hides a progress indicator
		*   @type: public
		*/
		win.progressOff = function() {
			that._switchProgress(this, false);
		}
		/**
		*   @desc: attaches a status bar to a window
		*   @type: private
		*/
		win.attachStatusBar = function() {
			return that._attachStatusBar(this);
		}
		/**
		*   @desc: attaches a dhtmlxMenu to a window
		*   @type: private
		*/
		win.attachMenu = function() {
			return that._attachWebMenu(this);
		}
		/**
		*   @desc: attaches a dhtmlxToolbar to a window
		*   @type: private
		*/
		win.attachToolbar = function() {
			return that._attachWebToolbar(this);
		}
//#wind_comps:09062008{
		/**
		*   @desc: attaches a dhtmlxGrid to a window
		*   @type: public
		*/
		win.attachGrid = function() {
			var obj = document.createElement("DIV");
			obj.id = "dhxGridObj_"+that._genStr(12);
			obj.style.width = "100%";
			obj.style.height = "100%";
			document.body.appendChild(obj);
			this.attachObject(obj.id);
			this.grid = new dhtmlXGridObject(obj.id);
			this.grid.setSkin(that.skin);
			this.grid.entBox.style.border="0px solid white";
			this.grid._sizeFix=0;
			return this.grid;
		}
		/**
		*   @desc: attaches a dhtmlxTree to a window
		*   @param: rootId - not mandatory, tree super root, see dhtmlxTree documentation for details
		*   @type: public
		*/
		win.attachTree = function(rootId) {
			var obj = document.createElement("DIV");
			obj.id = "dhxTreeObj_"+that._genStr(12);
			obj.style.width = "100%";
			obj.style.height = "100%";
			document.body.appendChild(obj);
			this.attachObject(obj.id);
			this.tree = new dhtmlXTreeObject(obj.id, "100%", "100%", (rootId||0));
			this.tree.setSkin(that.skin);
			// this.tree.allTree.style.paddingTop = "2px";
			this.tree.allTree.childNodes[0].style.marginTop = "2px";
			this.tree.allTree.childNodes[0].style.marginBottom = "2px";
			return this.tree;
		}
		/**
		*   @desc: attaches a dhtmlxTabbar to a window
		*   @type: public
		*/
		win.attachTabbar = function() {
			var obj = document.createElement("DIV");
			obj.id = "dhxTabbarObj_"+that._genStr(12);
			obj.style.width = "100%";
			obj.style.height = "100%";
			obj.style.overflow = "hidden";
			document.body.appendChild(obj);
			this.attachObject(obj.id);
			// manage dockcell if exists
			if (this._dockCell != null && that.dhxLayout != null) {
				var dockCell = that.dhxLayout.polyObj[this._dockCell];
				if (dockCell != null) {
					dockCell.childNodes[0]._tabbarMode = true;
					that.dhxLayout.hidePanel(this._dockCell);
					dockCell.className = "dhtmlxLayoutSinglePolyTabbar";
					// dockCell.childNodes[0]._h = -2;
					// dockCell.childNodes[1].style.height = parseInt(dockCell.childNodes[1].style.height) - dockCell.childNodes[0]._h + "px";
					// dockCell.className = "dhtmlxLayoutSinglePolyTabbar";
					// fix panel
					// that.dhxLayout._panelForTabs(this._dockCell);
				}
			}
			//
			this.tabbar = new dhtmlXTabBar(obj.id, "top",26);
			this.tabbar._linePos=-4;
			if ((_isIE)&&(document.compatMode == "BackCompat")){
				this.tabbar._lineAHeight=this.tabbar._lineA.style.height="6px";
				this.tabbar._bFix=5;
			} else{
				this.tabbar._lineAHeight=this.tabbar._lineA.style.height="4px";
				this.tabbar._bFix=4;
			}
			if (typeof this.tabbar.setSkin != 'undefined') {
				this.tabbar.setSkin(that.skin);	
			}
			this.tabbar._conZone.style.borderWidth="0px";
			this.tabbar._EARS = true;
			this.tabbar.setMargin(-1)
			this.tabbar.setOffset(0)
			this.tabbar.adjustOuterSize();
			this.tabbar.cells=function(id,name){ return this._cells.call(this,that,id,name); };
			return this.tabbar;
		}
		/**
		*   @desc: attaches a dhtmlxFolders to a window
		*   @type: public
		*/
		win.attachFolders = function() {
			var obj = document.createElement("DIV");
			obj.id = "dhxFoldersObj_"+that._genStr(12);
			obj.style.width = "100%";
			obj.style.height = "100%";
			obj.style.overflow = "hidden";
			document.body.appendChild(obj);
			this.attachObject(obj.id);
			this.folders = new dhtmlxFolders(obj.id);
			this.folders.setSizes();
			return this.folders;
		}
		/**
		*   @desc: attaches a dhtmlxAccordion to a window
		*   @type: public
		*/
		win.attachAccordion = function() {
			var obj = document.createElement("DIV");
			obj.id = "dhxAccordionObj_"+that._genStr(12);
			obj.style.width = "100%";
			obj.style.height = "100%";
			obj.style.position = "relative";
			document.body.appendChild(obj);
			this.attachObject(obj.id);
			this.accordion = new dhtmlXAccordion(obj.id, that.skin);
			/* // hide header
			if (this._dockCell != null && that.dhxLayout != null) {
				var dockCell = that.dhxLayout.polyObj[this._dockCell];
				if (dockCell != null) { that.dhxLayout.hidePanel(this._dockCell); }
			}
			*/
			win._content.childNodes[2].className += " dhtmlxAccordionAttached";
			this.accordion.setSizes();
			return this.accordion;
		}
		/**
		*   @desc: attaches a dhtmlxLayout to a window
		*   @param: view - layout's pattern
		*   @param: skin - layout's skin
		*   @type: public
		*/
		win.attachLayout = function(view, skin) {
			var obj = document.createElement("DIV");
			obj.id = "dhxLayoutObj_"+that._genStr(12);
			obj.style.position = "relative";
			document.body.appendChild(obj);
			//
			this.attachObject(obj.id);
			// this.layout = new dhtmlXLayoutObject(obj.id, this);
			// console.log(this._content.style.width, this._content.style.height)
			// var w = parseInt(this._content.style.width);
			// var h = parseInt(this._content.style.height);
			
			var w = this._content.childNodes[2].offsetWidth;
			var h = this._content.childNodes[2].offsetHeight;
			if (w == 0) { w = parseInt(this._content.style.width); }
			
			obj.style.left = "0px";
			obj.style.top = "0px";
			//obj.style.width = w-(_isIE&&this._isFullScreened?4:0)+"px";
			//obj.style.height = h-(_isIE&&this._isFullScreened?4:0)+"px";
			obj.style.width = w+"px";
			obj.style.height = h+"px";
			
			obj._skipChecksOnStartUp = true;
			
			if (skin == null) { skin = that.skin; }
			
			this.layout = new dhtmlXLayoutObject(obj, view, skin);
			this.layout._parentWindow = this;
			// alert(w+" "+h)
			// console.log(w,h)
			
			// this.layout._fixPositionInWin(w, h);
			this.attachEvent("_onBeforeTryResize", this.layout._defineWindowMinDimension);
			return this.layout;
		}
//#}
		/**
		*   @desc: attaches a dhtmlxEditor to a window
		*   @param: skin - not mandatory, editor's skin
		*   @type: public
		*/
		win.attachEditor = function(skin) {
			var obj = document.createElement("DIV");
			obj.id = "dhxEditorObj_"+that._genStr(12);
			obj.style.position = "relative";
			obj.style.display = "none";
			obj.style.overflow = "hidden";
			obj.style.width = "100%";
			obj.style.height = "100%";
			document.body.appendChild(obj);
			//
			this.attachObject(obj.id);
			//
			this.editor = new dhtmlXEditor(obj.id, (skin!=null?skin:that.skin));
			
			return this.editor;
			
		}
		/**
		*   @desc: sets a window to the fullscreen mode
		*   @param: state - true|false
		*   @type: public
		*/
		win.setToFullScreen = function(state) {
			that._setWindowToFullScreen(this, state);
		}
		/**
		*   @desc: shows window's header
		*   @type: public
		*/
		win.showHeader = function() {
			that._showWindowHeader(this);
		}
		/**
		*   @desc: hides window's header
		*   @type: public
		*/
		win.hideHeader = function() {
			that._hideWindowHeader(this);
		}
		//
		win.progressOff();
		// resize cursor modifications and handlers
		win.canStartResize = false;
		win.onmousemove = function(e) {
			// resize not allowed
			if ((!this._allowResize) || (this._allowResizeGlobal == false)) {
				this.canStartResize = false;
				this.style.cursor = "";
				return;
			}
			
			if (that.resizingWin != null) { return; }
			if (this._isParked) { return; }
			//
			e = e || event;
			var targetObj = e.target || e.srcElement;
			//
			var useDefaultCursor = true;
			this.canStartResize = true;
			//
			var skin = (this._skinParams!=null?this._skinParams:that.skinParams[that.skin]);
			var hh = skin["header_height"];
			var bwl = skin["border_left_width"] + 2;
			var bwr = skin["border_right_width"] + 2;
			var bhb = skin["border_bottom_height"] + 2;
			// left border
			if (targetObj.className == "dhtmlx_wins_body_border_middle_left") {
				that.resizingDirs = "border_left";
				this.style.cursor = "w-resize";
				this.resizeOffsetX = this.x - e.clientX;
				useDefaultCursor = false;
			}
			// right border
			if (targetObj.className == "dhtmlx_wins_body_border_middle_right") {
				that.resizingDirs = "border_right";
				this.style.cursor = "e-resize";
				this.resizeOffsetXW = this.x + this.w - e.clientX;
				useDefaultCursor = false;
			}
			// bottom border
			if (targetObj.className == "dhtmlx_wins_body_border_bottom_middle") {
				that.resizingDirs = "border_bottom";
				this.style.cursor = "n-resize";
				this.resizeOffsetYH = this.y + this.h - e.clientY;
				useDefaultCursor = false;
			}
			// corner left
			if (targetObj.className == "dhtmlx_wins_body_border_bottom_left") {
				that.resizingDirs = "corner_left";
				this.style.cursor = "sw-resize";
				this.resizeOffsetX = this.x - e.clientX;
				this.resizeOffsetYH = this.y + this.h - e.clientY;
				useDefaultCursor = false;
			}
			// corner right
			if (targetObj.className == "dhtmlx_wins_body_border_bottom_right") {
				that.resizingDirs = "corner_right";
				this.style.cursor = "nw-resize";
				this.resizeOffsetXW = this.x + this.w - e.clientX;
				this.resizeOffsetYH = this.y + this.h - e.clientY;
				useDefaultCursor = false;
			}
			
			// no matching elements
			if (useDefaultCursor) {
				this.canStartResize = false;
				this.style.cursor = "";
			}
		}
		win.onmousedown = function(e) {
			that._makeActive(this);
			that._bringOnTop(this);
			if (this.canStartResize) {
				that._blockSwitcher("none");
				that.resizingWin = this;
				if (!that._effects["resize"]) {
					that._carcass.x = that.resizingWin.x;
					that._carcass.y = that.resizingWin.y;
					that._carcass.w = that.resizingWin.w+(_isIE?0:-2);
					that._carcass.h = that.resizingWin.h+(_isIE?0:-2);
					that._carcass.style.left = that._carcass.x+"px";
					that._carcass.style.top = that._carcass.y+"px";
					that._carcass.style.width = that._carcass.w+"px";
					that._carcass.style.height = that._carcass.h+"px";
					that._carcass.style.zIndex = that.resizingWin.style.zIndex+1;
					that._carcass.style.cursor = this.style.cursor;
					that._carcass._keepInViewport = this._keepInViewport;
					that._carcass.style.display = "";
				}
				// vpcover
				that._vpcover.style.zIndex = that.resizingWin.style.zIndex-1;
				that._vpcover.style.display = "";
				if (this.layout) { this.callEvent("_onBeforeTryResize", [this]); }
				if (_isOpera) {
					e = e||event;
					e.returnValue = false;
					e.cancelBubble = true;
				}
			}
		}
		// add buttons
		this._addDefaultButtons(win);
		//
//#wind_buttons:09062008{		
		// return button handler
		win.button = function(id) {
			var b = null;
			if (this.btns[id] != null) { b = this.btns[id]; }
			return b;
		}
//#}		
		//
		// attach content obj|url
		/**
		*   @desc: attaches an object into a window
		*   @param: obj - object or object id
		*   @param: autoSize - set true to adjust a window to object's dimension
		*   @type: public
		*/
		win.attachObject = function(obj, autoSize) {
			if (typeof(obj) == "string") { obj = document.getElementById(obj); }
			if (autoSize) {
				obj.style.visibility = "hidden";
				obj.style.display = "";
				var objW = obj.offsetWidth;
				var objH = obj.offsetHeight;
			}
			that._attachContent(this, "obj", obj);
			if (autoSize) {
				obj.style.visibility = "visible";
				var skinParams = that.skinParams[that.skin];
				var newW = objW + skinParams["border_left_width"] + skinParams["border_right_width"];
				var newH = objH + skinParams["header_height"] + skinParams["border_bottom_height"];
				this.setDimension(newW, newH);
			}
		}
		/**
		*   @desc: appends an object into a window
		*   @param: obj - object or object id
		*   @type: public
		*/
		win.appendObject = function(obj) {
			if (typeof(obj) == "string") { obj = document.getElementById(obj); }
			that._attachContent(this, "obj", obj, true);
		}
		/**
		*   @desc: attaches an html string as an object into a window
		*   @param: str - html string
		*   @type: public
		*/
		win.attachHTMLString = function(str) {
			that._attachContent(this, "str", str);
		}
		/**
		*   @desc: attaches an url into a window
		*   @param: url
		*   @param: ajax - loads an url with ajax
		*   @type: public
		*/
		win.attachURL = function(url, ajax) {
			that._attachContent(this, (ajax==true?"urlajax":"url"), url, false);
		}
		/**
		*   @desc: centers a window in the viewport
		*   @type: public
		*/
		win.center = function() {
			that._centerWindow(this, false);
		}
		/**
		*   @desc: centers a window on the screen
		*   @type: public
		*/
		win.centerOnScreen = function() {
			that._centerWindow(this, true);
		}
		//
		this._attachContent(win, "empty", null);
		win.bringToTop();
		//
		return this.wins[id];
	}
	
	this._diableOnSelectInWin = function(obj, state) {
		for (var q=0; q<obj.childNodes.length; q++) {
			var child = obj.childNodes[q];
			if ((child.tagName == "TD") || (child.tagName == "TR") || (child.tagName == "TABLE")  || (child.tagName == "DIV")) {
				if (child.getAttribute("clearonselect") != null) {
					if (state) {
						child.onselectstart = function(e) { e = e || event; e.returnValue = false; }
						
					} else {
						child.onselectstart = null;
					}
				}
			}
			if (child.childNodes.length > 0) { this._diableOnSelectInWin(child, state); }
			child = null;
		}
	}
	this._redrawWindow = function(win) {
		if (win._isFullScreened) return;
		//
		win.style.left = win.x + "px";
		win.style.top = win.y + "px";
		// win.style.width = win.w + "px";
		// win.style.height = win.h + "px";
		win.style.width = (win.w == "100%" ? win.w : win.w+"px");
		win.style.height = (win.h == "100%" ? win.h : win.h+"px");
		if (win.w == "100%") {
			var winW = "100%";
			win.w = win.offsetWidth;
		}
		if (win.h == "100%") {
			var winH = "100%";
			win.h = win.offsetHeight;
		}
		// inner elements
		win.childNodes[0].style.height = win.h + "px";
		var p = win.childNodes[0].childNodes[0].childNodes[1].childNodes[0];
		var s = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
		p.style.height = win.h-s["header_height"] + "px";
		p.childNodes[0].style.height = win.h-s["header_height"] + "px";
		// title width
		// win.childNodes[2].className = "title_"+this.skin;
		var trObj = win.childNodes[3].childNodes[0].childNodes[0].childNodes[0];
		var tdVis = 0;
		for (var q=0; q<trObj.childNodes.length; q++) { if (trObj.childNodes[q].className == "dhtmlx_wins_btn_visible") { tdVis++; } }
		// tdVis
		// var wdth = win.childNodes[3].offsetLeft - win.childNodes[2].offsetLeft - 5;
		var wdth = win.w /* window width */ - tdVis*18 /* icons */ - 30 /* other gaps */;
		if (wdth < 0) { wdth = 0; }
		//
		win.childNodes[2].style.width = wdth + "px";
		// content div
		var w = win.w - s["border_left_width"] - s["border_right_width"];
		var h = win.h - s["header_height"] - s["border_bottom_height"];
		if (w < 0) { w = 0; }
		if (h < 0) { h = 0; }
		//
		var bd = p.childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0];
		//
		/*
		if (win._manageAddons) {
			win._manageAddons(w, h);
		} else {
			bd.style.width = w + "px";
			bd.style.height = h + "px";
		}
		*/
		
			if (bd == null) {
				bd = win._content;
			}
			// new
			bd.style.width = w + "px";
			bd.style.height = h + "px";
			//
		if (winW != null) { win.w = winW; }
		if (winH != null) { win.h = winH; }
	}
	
	this.zIndexStep = 50;
	this._getTopZIndex = function(ignoreSticked) {
		var topZIndex = 0;
		for (var a in this.wins) {
			if (ignoreSticked == true) {
				if (this.wins[a].zi > topZIndex) { topZIndex = this.wins[a].zi; }
			} else {
				if (this.wins[a].zi > topZIndex && !this.wins[a]._isSticked) { topZIndex = this.wins[a].zi; }
			}
		}
		return topZIndex;
	}
	
	this.movingWin = null;
//#wind_move:09062008{
	this._moveWindow = function(e) {
		
		if (this.movingWin != null) {
			//
			if (!this.movingWin._allowMove || !this.movingWin._allowMoveGlobal) { return; }
			if (this._effects["move"] == true) {
				//
				this.movingWin.oldMoveX = this.movingWin.x;
				this.movingWin.oldMoveY = this.movingWin.y;
				//
				this.movingWin.x = e.clientX + this.movingWin.moveOffsetX;
				this.movingWin.y = e.clientY + this.movingWin.moveOffsetY;
				//
				// check out of viewport
				this._fixWindowPositionInViewport(this.movingWin);
				//
				this._redrawWindow(this.movingWin);
				//
				// if (this._compoEnabled) { this._compoFixMove(this.movingWin); }
			} else {
				// console.log(1)
				if (this._carcass.style.display != "") {
					this._carcass.style.display = "";
				}
				this._carcass.x = e.clientX + this.movingWin.moveOffsetX;
				this._carcass.y = e.clientY + this.movingWin.moveOffsetY;
				this._fixWindowPositionInViewport(this._carcass);
				this._carcass.style.left = this._carcass.x+"px";
				this._carcass.style.top = this._carcass.y+"px";
				/*
				this._carcass.style.width = this.movingWin.style.width;
				this._carcass.style.height = this.movingWin.style.height;
				this._carcass.style.zIndex = this.movingWin.style.zIndex+10;
				this._carcass.style.display = "";
				*/
			}
		}
		
		if (this.resizingWin != null) {
			//
			if (!this.resizingWin._allowResize) { return; }
			//
			// resize through left border
			if (this.resizingDirs == "border_left" || this.resizingDirs == "corner_left") {
				if (this._effects["resize"]) {
					var ofs = e.clientX + this.resizingWin.resizeOffsetX;
					var sign = (ofs > this.resizingWin.x ? -1 : 1);
					newW = this.resizingWin.w + Math.abs(ofs - this.resizingWin.x)*sign;
					if ((newW < this.resizingWin.minW) && (sign < 0)) {
						this.resizingWin.x = this.resizingWin.x + this.resizingWin.w - this.resizingWin.minW;
						this.resizingWin.w = this.resizingWin.minW;
					} else {
						this.resizingWin.w = newW;
						this.resizingWin.x = ofs;
					}
					this._redrawWindow(this.resizingWin);
				} else {
					var ofs = e.clientX + this.resizingWin.resizeOffsetX;
					var sign = (ofs > this._carcass.x ? -1 : 1);
					newW = this._carcass.w + Math.abs(ofs - this._carcass.x)*sign;
					if ((newW < this.resizingWin.minW) && (sign < 0)) {
						this._carcass.x = this._carcass.x + this._carcass.w - this.resizingWin.minW;
						this._carcass.w = this.resizingWin.minW;
					} else {
						this._carcass.w = newW;
						this._carcass.x = ofs;
					}
					this._carcass.style.left = this._carcass.x+"px";
					this._carcass.style.width = this._carcass.w+"px";
				}
			}
			// resize through right border
			if (this.resizingDirs == "border_right" || this.resizingDirs == "corner_right") {
				if (this._effects["resize"]) {
					var ofs = e.clientX - (this.resizingWin.x + this.resizingWin.w) + this.resizingWin.resizeOffsetXW;
					newW = this.resizingWin.w + ofs;
					if (newW < this.resizingWin.minW) { newW = this.resizingWin.minW; }
					this.resizingWin.w = newW;
					this._redrawWindow(this.resizingWin);
				} else {
					var ofs = e.clientX - (this._carcass.x + this._carcass.w) + this.resizingWin.resizeOffsetXW;
					newW = this._carcass.w + ofs;
					if (newW < this.resizingWin.minW) { newW = this.resizingWin.minW; }
					this._carcass.w = newW;
					this._carcass.style.width = this._carcass.w+"px";
					// this._redrawWindow(this.resizingWin);
				}
			}
			// resize through bottom border
			if (this.resizingDirs == "border_bottom" || this.resizingDirs == "corner_left" || this.resizingDirs == "corner_right") {
				if (this._effects["resize"]) {
					var ofs = e.clientY - (this.resizingWin.y + this.resizingWin.h) + this.resizingWin.resizeOffsetYH;
					newH = this.resizingWin.h + ofs;
					if (newH < this.resizingWin.minH) { newH = this.resizingWin.minH; }
					this.resizingWin.h = newH;
					//if (this._compoEnabled) {
					//	this._compoFixResize(this.resizingWin, this.resizingDirs);
					//} else {
						this._redrawWindow(this.resizingWin);
					//}
				} else {
					var ofs = e.clientY - (this._carcass.y + this._carcass.h) + this.resizingWin.resizeOffsetYH;
					newH = this._carcass.h + ofs;
					if (newH < this.resizingWin.minH) { newH = this.resizingWin.minH; }
					this._carcass.h = newH;
					this._carcass.style.height = this._carcass.h+"px";
				}
			}
			//
			// if (this._compoEnabled) { this._compoFixResize(this.resizingWin, this.resizingDirs); }
		}
	}
	
	this._stopMove = function() {
		if (this.movingWin != null) {
			if (this._effects["move"]) {
				var win = this.movingWin;
				this.movingWin = null;
				this._blockSwitcher("");
				// cursor
				win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1].style.cursor = "";
				win.childNodes[2].style.cursor = "";
				// fixing mozilla artefakts
				if (_isFF) {
					win.h++;
					this._redrawWindow(win);
					win.h--;
					this._redrawWindow(win);
				}
			} else {
				this._carcass.style.display = "none";
				var win = this.movingWin;
				this.movingWin = null;
				this._blockSwitcher("");
				win.setPosition(parseInt(this._carcass.style.left), parseInt(this._carcass.style.top));
			}
			// vpcover
			this._vpcover.style.display = "none";
			// events
			if (!(win.oldMoveX == win.x && win.oldMoveY == win.y)) {
				//
				if (win.checkEvent("onMoveFinish")) {
					win.callEvent("onMoveFinish",[win]);
				} else {
					this.callEvent("onMoveFinish",[win]);
				}
			}
		}
		if (this.resizingWin != null) {
			var win = this.resizingWin;
			this.resizingWin = null;
			this._blockSwitcher("");
			if (!this._effects["resize"]) {
				this._carcass.style.display = "none";
				win.setPosition(this._carcass.x, this._carcass.y);
				win.setDimension(this._carcass.w+(_isIE?0:2), this._carcass.h+(_isIE?0:2));
			}
			// sizes in attached components
			this._fixInnerObjs(win);
			// event in layout
			if (win.layout) { win.layout.callEvent("onResize", []); }
			// opera fix
			if (_isOpera) {
				win._content.childNodes[2].style.border="#FFFFFF 0px solid";
				var w = win._content.childNodes[2];
				window.setTimeout(function(){ w.style.border="none"; }, 1);
			}
			// vpcover
			this._vpcover.style.display = "none";
			// events
			if (win.checkEvent("onResizeFinish")) {
				win.callEvent("onResizeFinish",[win]);
			} else {
				this.callEvent("onResizeFinish",[win]);
			}
		}
	}
//#}	
	
	// check viewport overflow
	this._fixWindowPositionInViewport = function(win) {
		var skin = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
		if (win._keepInViewport) { // keep strongly in viewport
			if (win.x < 0) { win.x = 0; }
			if (win.x + win.w > this.vp.offsetWidth) { win.x = this.vp.offsetWidth - win.w; }
			// if (win.y < 0) { win.y = 0; }
			if (win.y + win.h > this.vp.offsetHeight) { win.y = this.vp.offsetHeight - win.h; }
			if (win.y < 0) { win.y = 0; }
		} else {
			// if (win.y < 0) { win.y = 0; }
			if (win.y + skin["header_height"] > this.vp.offsetHeight) { win.y = this.vp.offsetHeight - skin["header_height"]; }
			if (win.y < 0) { win.y = 0; }
			if (win.x + win.w - 10 < 0) { win.x = 10 - win.w; }
			if (win.x > this.vp.offsetWidth - 10) { win.x = this.vp.offsetWidth - 10; }
		}
	}
	
	// check and correct window dimensions
	this._fixWindowDimensionInViewport = function(win) {
		if (win.w < win.minW) { win.w = win.minW; }
		if (win.h < win.minH) { win.h = win.minH; }
	}
	
	this._bringOnTop = function(win) {
		var cZIndex = win.zi;
		var topZIndex = this._getTopZIndex(win._isSticked);
		for (var a in this.wins) {
			if (this.wins[a] != win) {
				if (win._isSticked || (!win._isSticked && !this.wins[a]._isSticked)) {
					if (this.wins[a].zi > cZIndex) {
						this.wins[a].zi = this.wins[a].zi - this.zIndexStep;
						this.wins[a].style.zIndex = this.wins[a].zi;
					}
				}
			}
		}
		win.zi = topZIndex;
		win.style.zIndex = win.zi;
	}
	
	this._makeActive = function(win, ignoreFocusEvent) {
		for (var a in this.wins) {
			if (this.wins[a] == win) {
				var needEvent = false;
				if (this.wins[a].className != "dhtmlx_window_active" && !ignoreFocusEvent) { needEvent = true; }
				this.wins[a].className = "dhtmlx_window_active";
				this.wins[a].childNodes[1].src = this.wins[a].icons[0];
				if (needEvent == true) {
					if (win.checkEvent("onFocus")) {
						win.callEvent("onFocus",[win]);
					} else {
						this.callEvent("onFocus",[win]);
					}
				}
			} else {
				this.wins[a].className = "dhtmlx_window_inactive";
				this.wins[a].childNodes[1].src = this.wins[a].icons[1];
			}
		}
	}
	
	this._getActive = function() {
		var win = null;
		for (var a in this.wins) {
			if (this.wins[a].className == "dhtmlx_window_active") {
				win = this.wins[a];
			}
		}
		return win;
	}
	
	this._centerWindow = function(win, onScreen) {
		if (win._isMaximized == true) { return; }
		if (win._isParked == true) { return; }
		if (onScreen == true) {
			var vpw = (_isIE?document.body.offsetWidth:window.innerWidth);
			var vph = (_isIE?document.body.offsetHeight:window.innerHeight);
		} else {
			var vpw = (this.vp==document.body?document.body.offsetWidth:(Number(parseInt(this.vp.style.width))&&String(this.vp.style.width).search("%")==-1?parseInt(this.vp.style.width):this.vp.offsetWidth));
			var vph = (this.vp==document.body?document.body.offsetHeight:(Number(parseInt(this.vp.style.height))&&String(this.vp.style.height).search("%")==-1?parseInt(this.vp.style.height):this.vp.offsetHeight));
		}
		var newX = Math.round((vpw/2) - (win.w/2));
		var newY = Math.round((vph/2) - (win.h/2));
		win.x = newX;
		win.y = newY;
		this._fixWindowPositionInViewport(win);
		this._redrawWindow(win);
	}
	
	this._switchProgress = function(win, state) {
		if (state == true) {
			win.childNodes[1].style.display = "none";
			win.childNodes[4].style.display = "";
		} else {
			win.childNodes[4].style.display = "none";
			win.childNodes[1].style.display = "";
		}
	}
	
	this._addDefaultButtons = function(win) {
//#wind_buttons:09062008{
		// stick
		var btnStick = document.createElement("DIV");
		btnStick.className = "button_stick_default";
		btnStick.title = "Stick";
		btnStick.isVisible = false;
		btnStick._isEnabled = true;
		btnStick.isPressed = false;
		win._isSticked = false;
		btnStick.label = "stick";
		btnStick._doOnClick = function() {
			this.isPressed = true;
			that._stickWindow(win);
		}
		
		// sticked
		var btnSticked = document.createElement("DIV");
		btnSticked.className = "button_sticked_default";
		btnSticked.title = "Unstick";
		btnSticked.isVisible = false;
		btnSticked._isEnabled = true;
		btnSticked.isPressed = false;
		btnSticked.label = "sticked";
		btnSticked._doOnClick = function() {
			this.isPressed = false;
			that._unstickWindow(win);
		}
		
		// help
		var btnHelp = document.createElement("DIV");
		btnHelp.className = "button_help_default";
		btnHelp.title = "Help";
		btnHelp.isVisible = false;
		btnHelp._isEnabled = true;
		btnHelp.isPressed = false;
		btnHelp.label = "help";
		btnHelp.that = this;
		btnHelp._doOnClick = function() { that._needHelp(win); }
		
		// park
		var btnPark = document.createElement("DIV");
		btnPark.className = "button_park_default";
		btnPark.titleIfParked = "Park Down";
		btnPark.titleIfNotParked = "Park Up";
		btnPark.title = btnPark.titleIfNotParked;
		btnPark.isVisible = true;
		btnPark._isEnabled = true;
		btnPark.isPressed = false;
		btnPark.label = "park";
		win._isParked = false;
		win._isParkedAllowed = true;
		btnPark._doOnClick = function() { that._parkWindow(win); }
		
		// minmax maximize
		var btnMinMax1 = document.createElement("DIV");
		btnMinMax1.className = "button_minmax1_default";
		btnMinMax1.title = "Maximize";
		btnMinMax1.isVisible = true;
		btnMinMax1._isEnabled = true;
		btnMinMax1.isPressed = false;
		btnMinMax1.label = "minmax1";
		win._isMaximized = false;
		btnMinMax1._doOnClick = function() { that._maximizeWindow(win); }
		
		// minmax restore
		var btnMinMax2 = document.createElement("DIV");
		btnMinMax2.className = "button_minmax2_default";
		btnMinMax2.title = "Restore";
		btnMinMax2.isVisible = false;
		btnMinMax2._isEnabled = true;
		btnMinMax2.isPressed = false;
		btnMinMax2.label = "minmax2";
		btnMinMax2._doOnClick = function() { that._restoreWindow(win); }
		
		// close
		var btnClose = document.createElement("DIV");
		btnClose.className = "button_close_default";
		btnClose.title = "Close";
		btnClose.isVisible = true;
		btnClose._isEnabled = true;
		btnClose.isPressed = false;
		btnClose.label = "close";
		btnClose._doOnClick = function() { that._closeWindow(win); }
		
		//
		win.btns = {};
		win.btns["stick"] = btnStick;
		win.btns["sticked"] = btnSticked;
		win.btns["help"] = btnHelp;
		win.btns["park"] = btnPark;
		win.btns["minmax1"] = btnMinMax1;
		win.btns["minmax2"] = btnMinMax2;
		win.btns["close"] = btnClose;
		
		var b = win.childNodes[3].childNodes[0].childNodes[0].childNodes[0];
		
		// events
		for (var a in win.btns) {
			
			var btn = win.btns[a];
			
			// add on header
			var td = document.createElement("TD");
			td.className = "dhtmlx_wins_btn_" + (btn.isVisible ? "visible" : "hidden");
			b.appendChild(td);
			td.appendChild(btn);
			// attach events
			this._attachEventsOnButton(win, btn);
			//
			btn = null;
		}
//#}		
	}
//#wind_buttons:09062008{
	this._attachEventsOnButton = function(win, btn) {
		// add events
		
		btn.onmouseover = function() {
			if (this._isEnabled) {
				this.className = "button_"+this.label+"_over_" + (this.isPressed ? "pressed": "default");
			} else {
				this.className = "button_"+this.label+"_disabled";
			}
		}
		btn.onmouseout = function() {
			if (this._isEnabled) {
				this.isPressed = false;
				this.className = "button_"+this.label+"_default";
			} else {
				this.className = "button_"+this.label+"_disabled";
			}
		}
		btn.onmousedown = function() {
			if (this._isEnabled) {
				this.isPressed = true;
				this.className = "button_"+this.label+"_over_pressed";
			} else {
				this.className = "button_"+this.label+"_disabled";
			}
		}
		btn.onmouseup = function() {
			if (this._isEnabled) {
				var wasPressed = this.isPressed;
				this.isPressed = false;
				this.className = "button_"+this.label+"_over_default";
				if (wasPressed) {
					// events
					if (this.checkEvent("onClick")) {
						this.callEvent("onClick", [win, this]);
					} else {
						this._doOnClick();
					}
				}
			} else {
				this.className = "button_"+this.label+"_disabled";
			}
		}
/**
*   @desc: shows a button
*   @type:  public
*/
		btn.show = function() {
			that._showButton(win, this.label);
		}
/**
*   @desc: hides a button
*   @type:  public
*/
		btn.hide = function() {
			that._hideButton(win, this.label);
		}
/**
*   @desc: enables a button
*   @type:  public
*/

		btn.enable = function() {
			that._enableButton(win, this.label);
		}

/**
*   @desc: disables a button
*   @type:  public
*/
		btn.disable = function() {
			that._disableButton(win, this.label);
		}
/**
*   @desc: checks if a button is enabled
*	@returns: true if enabled, otherwise - false
*   @type:  public
*/
		btn.isEnabled = function() {
			return this._isEnabled;
		}

/**
*   @desc: checks  if a button is hidden
*	@returns: true if hidden, otherwise - false
*   @type:  public
*/
		btn.isHidden = function() {
			return (!this.isVisible);
		}
		btn.dhx_Event = this.dhx_Event;
		btn.dhx_Event();
	}
//#}	
//#wind_park:09062008{
	this._parkWindow = function(win) {
		if (!win._isParkedAllowed) { return; }
		if (this.enableParkEffect && win.parkBusy) { return; }
		if (win._isParked) {
			if (this.enableParkEffect) {
				win.parkBusy = true;
				// win.childNodes[0].childNodes[0].childNodes[1].style.display = "";
				win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				this._doParkDown(win);
			} else {
				win.h = win.lastParkH;
				win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "";
				win.btns["park"].title = win.btns["park"].titleIfNotParked;
				if (win._allowResizeGlobal == true) {
					this._enableButton(win, "minmax1");
					this._enableButton(win, "minmax2");
				}
			}
		} else {
			win.lastParkH = (String(win.h).search(/\%$/)==-1?win.h:win.offsetHeight);
			if (win._allowResizeGlobal == true) {
				this._disableButton(win, "minmax1");
				this._disableButton(win, "minmax2");
			}
			//
			if (this.enableParkEffect) {
				win.parkBusy = true;
				this._doParkUp(win);
			} else {
				var skinParams = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
				win.h = skinParams["header_height"] + skinParams["border_bottom_height"];
				win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
				win.btns["park"].title = win.btns["park"].titleIfParked;
			}
		}
		if (!this.enableParkEffect) {
			win._isParked = !win._isParked;
			this._redrawWindow(win);
			// events
			if (!win._isParked) {
				// sizes
				this._fixInnerObjs(win);
				// opera fix
				if (_isOpera) {
					win._content.childNodes[2].style.border="#FFFFFF 0px solid";
					var w = win._content.childNodes[2];
					window.setTimeout(function(){ w.style.border="none"; }, 1);
				}
				// onParkDown event
				if (win.checkEvent("onParkDown")) {
					win.callEvent("onParkDown", [win]);
				} else {
					this.callEvent("onParkDown", [win]);
				}
			} else {
				// bottom border fix for opera
				if (_isOpera) {
					win.childNodes[0].border = 1;
					win.childNodes[0].border = 0;
				}
				// onParkUp event
				if (win.checkEvent("onParkUp")) {
					win.callEvent("onParkUp", [win]);
				} else {
					this.callEvent("onParkUp", [win]);
				}
			}
		}
	}
	
	this._allowParking = function(win) {
		win._isParkedAllowed = true;
		this._enableButton(win, "park");
	}
	this._denyParking = function(win) {
		win._isParkedAllowed = false;
		this._disableButton(win, "park");
	}
	
	// park with effects
	this.enableParkEffect = true;
	this.parkStartSpeed = 80;
	this.parkSpeed = this.parkStartSpeed;
	this.parkTM = null;
	this.parkTMTime = 5;
	
	this._doParkUp = function(win) {
		if (String(win.h).search(/\%$/) != -1) {
			win.h = win.offsetHeight;
		}
		win.h -= this.parkSpeed;
		var skinParams = (win._skinParams!=null?win._skinParams:this.skinParams[this.skin]);
		if (win.h <= skinParams["header_height"] + skinParams["border_bottom_height"]) {
			// end purkUp
			win.h = skinParams["header_height"] + skinParams["border_bottom_height"];
			// win.childNodes[0].childNodes[0].childNodes[1].style.display = "none";
			win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.display = "none";
			win.btns["park"].title = win.btns["park"].titleIfParked;
			win._isParked = true;
			win.parkBusy = false;
			this._redrawWindow(win, true);
			// bottom border fix for opera
			if (_isOpera) {
				win.childNodes[0].border = 1;
				win.childNodes[0].border = 0;
			}
			// onParkUp event
			if (win.checkEvent("onParkUp")) {
				win.callEvent("onParkUp", [win]);
			} else {
				this.callEvent("onParkUp", [win]);
			}
		} else {
			// continue purkUp
			this._redrawWindow(win);
			this.parkTM = window.setTimeout(function(){that._doParkUp(win);}, this.parkTMTime);
		}
	}
	
	this._doParkDown = function(win) {
		win.h += this.parkSpeed;
		
		if (win.h >= win.lastParkH) {
			win.h = win.lastParkH;
			win.btns["park"].title = win.btns["park"].titleIfNotParked;
			if (win._allowResizeGlobal == true) {
				this._enableButton(win, "minmax1");
				this._enableButton(win, "minmax2");
			}
			win._isParked = false;
			win.parkBusy = false;
			this._redrawWindow(win);
			// fix sizes
			this._fixInnerObjs(win);
			// opera fix
			if (_isOpera) {
				win._content.childNodes[2].style.border="#FFFFFF 0px solid";
				var w = win._content.childNodes[2];
				window.setTimeout(function(){ w.style.border="none"; }, 1);
			}
			// onParkDown event
			if (win.checkEvent("onParkDown")) {
				win.callEvent("onParkDown", [win]);
			} else {
				this.callEvent("onParkDown", [win]);
			}
		} else {
			// continue purkDown
			this._redrawWindow(win);
			this.parkTM = window.setTimeout(function(){ that._doParkDown(win); }, this.parkTMTime);
		}
	}
//#}
//#wind_buttons:09062008{
	this._enableButton = function(win, btn) {
		win.btns[btn]._isEnabled = true;
		win.btns[btn].className = "button_"+win.btns[btn].label+"_default";
	}
	
	this._disableButton = function(win, btn) {
		win.btns[btn]._isEnabled = false;
		win.btns[btn].className = "button_"+win.btns[btn].label+"_disabled";
	}
//#}
	// header control
	this._showWindowHeader = function(win) {
		win.childNodes[1].style.display = "";
		win.childNodes[2].style.display = "";
		win.childNodes[3].style.display = "";
		for (var a in win._skinParams) { delete win._skinParams[a]; }
		win._skinParams = null;
		var h = this.skinParams[this.skin]["header_height"]+"px";
		win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.height = h; // td class="dhtmlx_wins_td_header_full"
		win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.height = h; // table class="dhtmlx_wins_header"
		var hdr = win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		for (var q=0; q<hdr.childNodes.length; q++) { hdr.childNodes[q].style.height = h; }
		this._redrawWindow(win);
	}
	this._hideWindowHeader = function(win) {
		win.childNodes[1].style.display = "none";
		win.childNodes[2].style.display = "none";
		win.childNodes[3].style.display = "none";
		win._skinParams = {};
		for (var a in this.skinParams[this.skin]) { win._skinParams[a] = Number(this.skinParams[this.skin][a]).valueOf(); }
		win._skinParams["header_height"] = win._skinParams["border_bottom_height"];
		var h = win._skinParams["header_height"]+"px";
		win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.height = h; // td class="dhtmlx_wins_td_header_full"
		win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].style.height = h; // table class="dhtmlx_wins_header"
		var hdr = win.childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0];
		for (var q=0; q<hdr.childNodes.length; q++) { hdr.childNodes[q].style.height = h; }
		this._redrawWindow(win);
	}
	// resize
	this._allowReszieGlob = function(win) {
		win._allowResizeGlobal = true;
		this._enableButton(win, "minmax1");
		this._enableButton(win, "minmax2");
	}
	
	this._denyResize = function(win) {
		win._allowResizeGlobal = false;
		this._disableButton(win, "minmax1");
		this._disableButton(win, "minmax2");
	}
	
	this._maximizeWindow = function(win) {
		if (win._allowResizeGlobal == false) { return; }
		win.lastMaximizeX = win.x;
		win.lastMaximizeY = win.y;
		win.lastMaximizeW = win.w;
		win.lastMaximizeH = win.h;
		win.x = 0;
		win.y = 0;
		win._isMaximized = true;
		win._allowMove = false;
		win._allowResize = false;
		// win.w = (win.maxW == "auto" ? (this.vp == document.body ? document.body.offsetWidth:parseInt(this.vp.style.width)) : win.maxW);
		// win.h = (win.maxH == "auto" ? (this.vp == document.body ? document.body.offsetHeight:parseInt(this.vp.style.height)) : win.maxH);
		//
		win.w = (win.maxW == "auto" ? (this.vp == document.body ? "100%" : (this.vp.style.width != "" && String(this.vp.style.width).search("%") == -1 ? parseInt(this.vp.style.width) : this.vp.offsetWidth)) : win.maxW);
		win.h = (win.maxH == "auto" ? (this.vp == document.body ? "100%" : (this.vp.style.height != "" && String(this.vp.style.width).search("%") == -1 ? parseInt(this.vp.style.height) : this.vp.offsetHeight)) : win.maxH);
		//
		this._hideButton(win, "minmax1");
		this._showButton(win, "minmax2");
		this._redrawWindow(win);
		// sizes in attached components
		this._fixInnerObjs(win);
		// event
		if (win.checkEvent("onMaximize")) {
			win.callEvent("onMaximize", [win]);
		} else {
			this.callEvent("onMaximize", [win]);
		}
	}
	
	this._restoreWindow = function(win) {
		if (win._allowResizeGlobal == false) { return; }
		if (win.layout) { win.layout._defineWindowMinDimension(win); }
		win.x = win.lastMaximizeX;
		win.y = win.lastMaximizeY;
		win.w = win.lastMaximizeW;
		win.h = win.lastMaximizeH;
		win._isMaximized = false;
		win._allowMove = win._allowMoveGlobal;
		win._allowResize = true;
		this._fixWindowDimensionInViewport(win);
		this._hideButton(win, "minmax2");
		this._showButton(win, "minmax1");
		this._redrawWindow(win);
		// sizes in attached components
		this._fixInnerObjs(win);
		// events
		if (win.checkEvent("onMinimize")) {
			win.callEvent("onMinimize", [win]);
		} else {
			this.callEvent("onMinimize", [win]);
		}
	}
//#wind_buttons:09062008{	
	this._showButton = function(win, btn) {
		win.btns[btn].isVisible = true;
		// win.btns[btn].parentNode.className = "dhtmlx_wins_btn_visible";
		var p = win.btns[btn].parentNode;
		p.className = "dhtmlx_wins_btn_visible";
		p = null;
	}
	
	this._hideButton = function(win, btn) {
		win.btns[btn].isVisible = false;
		// win.btns[btn].parentNode.className = "dhtmlx_wins_btn_hidden";
		var p = win.btns[btn].parentNode;
		p.className = "dhtmlx_wins_btn_hidden";
		p = null;
	}
//#}	
	this._showWindow = function(win) {
		win.style.display = "";
		// event
		if (win.checkEvent("onShow")) {
			win.callEvent("onShow", [win]);
		} else {
			this.callEvent("onShow", [win]);
		}
		// fixed 24.03.2008
		var w = this._getActive();
		if (w == null) {
			this._bringOnTop(win);
			this._makeActive(win);
		} else if (this._isWindowHidden(w)) {
			this._bringOnTop(win);
			this._makeActive(win);
		}
	}
	
	this._hideWindow = function(win) {
		win.style.display = "none";
		// event
		if (win.checkEvent("onHide")) {
			win.callEvent("onHide", [win]);
		} else {
			this.callEvent("onHide", [win]);
		}
		// fixed 24.03.2008
		var w = this.getTopmostWindow(true);
		if (w != null) {
			this._bringOnTop(w);
			this._makeActive(w);
		}
	}
	
	this._isWindowHidden = function(win) {
		var isHidden = (win.style.display == "none");
		return isHidden;
	}
	
	this._closeWindow = function(win) {
		// event
		if (win.checkEvent("onClose")) {
			if (!win.callEvent("onClose", [win])) return;
		} else {
			if(!this.callEvent("onClose", [win])) return;
		}
		// closing
		// for (var a in win.btns) { this._removeButtonGlobal(win, a, win.btns[a]); }
		this._removeWindowGlobal(win);
		/*
		this.vp.removeChild(win);
		delete this.wins[win.idd];
		// make active latest window
		*/
		var latest = { "zi": 0 };
		for (var a in this.wins) { if (this.wins[a].zi > latest.zi) { latest = this.wins[a]; } }
		if (latest != null) { this._makeActive(latest); }
	}
	
	this._needHelp = function(win) {
		// event only
		if (win.checkEvent("onHelp")) {
			win.callEvent("onHelp", [win]);
		} else {
			this.callEvent("onHelp", [win]);
		}
	}
	
	this._attachContent = function(win, type, obj, append) {
		// clear old content
		if (append !== true) {
			while (win._content.childNodes[2].childNodes.length > 0) { win._content.childNodes[2].removeChild(win._content.childNodes[2].childNodes[0]); }
		}
		// attach
		if (type == "url") {
			var fr = document.createElement("IFRAME");
			fr.frameBorder = 0;
			fr.border = 0;
			fr.style.width = "100%";
			fr.style.height = "100%";
			fr.src = obj;
			win._content.childNodes[2].appendChild(fr);
			win._frame = fr;
			if (_isIE) {
				win._frame.onreadystatechange = function(a) { if (win._frame.readyState == "complete") { that.callEvent("onContentLoaded", [win]); } }
			} else {
				win._frame.onload = function() { that.callEvent("onContentLoaded", [win]); }
			}
		} else if (type == "urlajax") {
			var xmlParser = function(){
				this.dhxWindowObject.attachHTMLString(this.xmlDoc.responseText);
				that.callEvent("onContentLoaded", [win]);
				this.destructor();
			}
			var xmlLoader = new dtmlXMLLoaderObject(xmlParser, window);
			xmlLoader.dhxWindowObject = win;
			xmlLoader.loadXML(obj);
		} else if (type == "obj") {
			win._frame = null;
			win._content.childNodes[2].appendChild(obj);
			win._content.childNodes[2].style.overflow = (append===true?"auto":"hidden");
			obj.style.display = "";
		} else if (type == "str") {
			win._frame = null;
			this._setInnerHTML(win._content.childNodes[2], obj);
			//win._content.childNodes[2].innerHTML = obj;
		}
	}
    	this._setInnerHTML = function(node, innerHTML, type) {

        	node.innerHTML = '';
        	var good_browser = (window.opera || navigator.product == 'Gecko');
        	var regex = /^([\s\S]*?)<script([\s\S]*?)>([\s\S]*?)<\/script>([\s\S]*)$/i;
        	var regex_src = /src=["'](.*?)["']/i;
        	var matches, id, script, output = '', subject = innerHTML;
        	var scripts = [];
        	
        	while (true) {
        	    matches = regex.exec(subject);
            	if (matches && matches[0]) {
                	subject = matches[4];
                	id = 'ih_' + Math.round(Math.random()*9999) + '_' + Math.round(Math.random()*9999);

                	var startLen = matches[3].length;
                	script = matches[3].replace(/document\.write\(([\s\S]*?)\)/ig, 
                	    'document.getElementById("' + id + '").innerHTML+=$1');
	
	                output += matches[1];
	                if (startLen != script.length) {
	                        output += '<span id="' + id + '"></span>';
	                }
	                
	       	         output += '<script' + matches[2] + '>' + script + '</script>';
	                if (good_browser) {
	                    continue;
	                }
	                if (script) {
	                    scripts.push(script);
	                }
	                if (regex_src.test(matches[2])) {
	                    var script_el = document.createElement("SCRIPT");
	                    var atts_regex = /(\w+)=["'](.*?)["']([\s\S]*)$/;
	                    var atts = matches[2];
	                    for (var i = 0; i < 5; i++) { 
	                        var atts_matches = atts_regex.exec(atts);
	                        if (atts_matches && atts_matches[0]) {
	                            script_el.setAttribute(atts_matches[1], atts_matches[2]);
	                            atts = atts_matches[3];
	                        } else {
	                            break;
	                        }
	                    }
	                    scripts.push(script_el);
                }
            } else {
                output += subject;
                break;
            }
        }
        innerHTML = output;

        if (good_browser) {
            var el = document.createElement('span');
            el.innerHTML = innerHTML;

            for(var i = 0; i < el.childNodes.length; i++) {
                node.appendChild(el.childNodes[i].cloneNode(true));
            }
        }
        else {
            node.innerHTML += innerHTML;
        }

        if (!good_browser) {
            for(var i = 0; i < scripts.length; i++) {
                if (HTML_AJAX_Util.getType(scripts[i]) == 'string') {
                    scripts[i] = scripts[i].replace(/^\s*<!(\[CDATA\[|--)|((\/\/)?--|\]\])>\s*$/g, '');
                    window.eval(scripts[i]);
                }
                else {
                    node.appendChild(scripts[i]);
                }
            }
        }
        return;
    }
	
	this._setWindowIcon = function(win, iconEnabled, iconDisabled) {
		win.iconsPresent = true;
		win.icons[0] = this.imagePath + iconEnabled;
		win.icons[1] = this.imagePath + iconDisabled;
		win.childNodes[1].src = win.icons[win.isOnTop()?0:1];
	}
	
	this._getWindowIcon = function(win) {
		if (win.iconsPresent) {
			return new Array(win.icons[0], win.icons[1]);
		} else {
			return new Array(null, null);
		}
	}
	
	this._clearWindowIcons = function(win) {
		win.iconsPresent = false;
		win.icons[0] = this.imagePath + this.pathPrefix + this.skin + "/active/icon_blank.gif";
		win.icons[1] = this.imagePath + this.pathPrefix + this.skin + "/inactive/icon_blank.gif";
		win.childNodes[1].src = win.icons[win.isOnTop()?0:1];
	}
	
	this._restoreWindowIcons = function(win) {
		win.iconsPresent = true;
		win.icons[0] = this.imagePath + this.pathPrefix + this.skin + "/active/icon_normal.gif";
		win.icons[1] = this.imagePath + this.pathPrefix + this.skin + "/inactive/icon_normal.gif";
		win.childNodes[1].src = win.icons[win.className=="dhtmlx_window_active"?0:1];
	}
	
	this._attachWindowContentTo = function(win, obj, w, h) {
		
		var data = win._content;
		data.parentNode.removeChild(data);
		win.hide();
		//
		data.style.left = "0px";
		data.style.top = "0px";
		data.style.width = (w!=null?w:obj.offsetWidth)+"px";
		data.style.height = (h!=null?h:obj.offsetHeight)+"px";
		data.style.position = "relative";
		//
		obj.appendChild(data);
		
	}
	
	this._setWindowToFullScreen = function(win, state) {
		if (state == true) {
			//
			var data = win._content;
			data.parentNode.removeChild(data);
			win.hide();
			win._isFullScreened = true;
			//
			data.style.left = "0px";
			data.style.top = "0px";
			// data.style.width = document.body.offsetWidth+"px";
			// data.style.height = document.body.offsetHeight+"px";
			
			data.style.width = document.body.offsetWidth-(_isIE?4:0)+"px";
			if (document.body.offsetHeight == 0) {
				if (window.innerHeight) {
					data.style.height = window.innerHeight+"px";
				} else {
					data.style.height = document.body.scrollHeight+"px";
				}
			} else {
				
				data.style.height = document.body.offsetHeight-(_isIE?4:0)+"px";
			}
			
			data.style.position = "absolute";
			
			document.body.appendChild(data);
			
		} else if (state == false) {
			
			var data = win.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[1];
			var base = win._content;
			document.body.removeChild(base);
			data.appendChild(base);
			win._isFullScreened = false;
			win.setDimension(win.w, win.h);
			//
			win.show();
			win.bringToTop();
			win.center();
			//
		}
		this._fixInnerObjs(win);
	}
	
	this._isWindowOnTop = function(win) {
		var state = (this.getTopmostWindow() == win);
		return state;
	}
	
	this._bringOnBottom = function(win) {
		for (var a in this.wins) {
			if (this.wins[a].zi < win.zi) {
				this.wins[a].zi += this.zIndexStep;
				this.wins[a].style.zIndex = this.wins[a].zi;
			}
		}
		win.zi = 50;
		win.style.zIndex = win.zi;
		//
		this._makeActive(this.getTopmostWindow());
	}
	
	this._isWindowOnBottom = function(win) {
		var state = true;
		for (var a in this.wins) {
			if (this.wins[a] != win) {
				state = state && (this.wins[a].zi > win.zi);
			}
		}
		return state;
	}
	
	this._stickWindow = function(win) {
		win._isSticked = true;
		this._hideButton(win, "stick");
		this._showButton(win, "sticked");
		this._bringOnTop(win);
	}
	
	this._unstickWindow = function(win) {
		win._isSticked = false;
		this._hideButton(win, "sticked");
		this._showButton(win, "stick");
		this._bringOnTopAnyStickedWindows();
	}
//#wind_buttons:09062008{
	// add user button
	this._addUserButton = function(win, id, pos, title, label) {
		var btn = document.createElement("DIV");
		
		btn.className = "button_"+label+"_default";
		btn.title = title;
		btn.isVisible = true;
		btn._isEnabled = true;
		btn.isPressed = false;
		btn.label = label;
		//
		win.btns[id] = btn;
		//
		btn._doOnClick = function() {}
		// events
			
		// btn.that = this;
		// btn.win = win;
		var b = win.childNodes[3].childNodes[0].childNodes[0].childNodes[0];
		// add on header
		var td = document.createElement("TD");
		td.className = "dhtmlx_wins_btn_" + (btn.isVisible ? "visible" : "hidden");
		if (pos > b.childNodes.length) {
			b.appendChild(td);
		} else {
			if (pos < 0) { pos = 0; }
			b.insertBefore(td, b.childNodes[pos]);
		}
		
		td.appendChild(btn);
		
		// attach events
		this._attachEventsOnButton(win, btn);
	}
	
	// remove user button
	this._removeUserButton = function(win, id, btn) {
		this._removeButtonGlobal(win, id, btn);
	}
//#}	
	// add iframe blockers before drag and resize
	this._blockSwitcher = function(state) {
		for (var a in this.wins) {
			var winContent = this.wins[a]._content;
			var cover = null;
			for (var q=0; q<winContent.childNodes.length; q++) { if (winContent.childNodes[q].className == "dhx_content_cover_blocker") { cover = winContent.childNodes[q]; } }
			if (cover != null) { cover.style.display = (state?"":"none"); }
		}
	}
	
	this.resizingWin = null;
	this.modalWin = null;
	this.resizingDirs = "none";
	
	// init functions
	
	this._createViewport();
//#wind_move:09062008{	
	this._doOnMouseUp = function() {
		that._stopMove();
	}
	this._doOnMoseMove = function(e) {
		e = e||event;
		if (that!=null) { that._moveWindow(e); }
	}
//#}	
	this._resizeTM = null;
	this._resizeTMTime = 200;
	this._doOnResize = function() {
		window.clearTimeout(that._resizeTM);
		that._resizeTM = window.setTimeout(function(){that._autoResizeViewport();}, that._resizeTMTime);
	}
	this._doOnUnload = function() {
		that.unload();
	}
	this._doOnSelectStart = function(e) {
		e = e||event;
		if (that.movingWin != null || that.resizingWin != null) { e.returnValue = false; }
	}
	if (_isIE) {
		document.body.attachEvent("onselectstart", this._doOnSelectStart);
	}
	dhtmlxEvent(window, "resize", this._doOnResize);
	dhtmlxEvent(document.body, "unload", this._doOnUnload);
//#wind_move:09062008{		
	dhtmlxEvent(document.body, "mouseup", this._doOnMouseUp);
	dhtmlxEvent(this.vp, "mousemove", this._doOnMoseMove);
	dhtmlxEvent(this.vp, "mouseup", this._doOnMouseUp);
//#}
	
	
	this._setWindowModal = function(win, state) {
		
		if (state == true) {
			
			this._makeActive(win);
			this._bringOnTop(win);
			this.modalWin = win;
			win._isModal = true;
			//
			this.modalCoverI.style.zIndex = win.zi - 2;
			this.modalCoverI.style.display = "";
			//
			this.modalCoverD.style.zIndex = win.zi - 2;
			this.modalCoverD.style.display = "";
		} else {
			this.modalWin = null;
			win._isModal = false;
			//
			this.modalCoverI.style.zIndex = 0;
			this.modalCoverI.style.display = "none";
			//
			this.modalCoverD.style.zIndex = 0;
			this.modalCoverD.style.display = "none";
		}
	}
	
	this._bringOnTopAnyStickedWindows = function() {
		var wins = new Array();
		for (var a in this.wins) { if (this.wins[a]._isSticked) { wins[wins.length] = this.wins[a]; } }
		for (var q=0; q<wins.length; q++) { this._bringOnTop(wins[q]); }
		// if no more sticked search any non-top active and move them on top
		if (wins.length == 0) {
			for (var a in this.wins) {
				if (this.wins[a].className == "dhtmlx_window_active") { this._bringOnTop(this.wins[a]); }
			}
		}
	}
	
	/**
	*   @desc: unloads an object and clears memory
	*   @param: id - button's id
	*   @type: public
	*/
	this.unload = function() {
		this._clearAll();
	}
	
	this._removeButtonGlobal = function(win, id, btn) {
//#wind_buttons:09062008{
		// clear functions
		this._parseNestedForEvents(btn);
		for (var a in btn) {
			if (typeof(btn[a]) == "function") {
				var k = (btn[a].toString()).split("\n");
				if (!(k.length == 3 && k[1].search(/\[native\scode\]/gi) != -1)) { btn[a] = null; }
			}
		}
		// remove from page
		var p = btn.parentNode;
		p.removeChild(btn);
		delete win.btns[id];
		btn = null;
		p = null;
//#}
	}
	
	this._removeWindowGlobal = function(win) {
		// modal check
		if (this.modalWin == win) { this._setWindowModal(win, false); }
		// clear functions
		this._parseNestedForEvents(win);
//#wind_buttons:09062008{
		if (!_isOpera) {
			for (var a in win.btns) {
				this._removeButtonGlobal(win, a, win.btns[a]);
			}
		}
//#}		
		if (!_isOpera) {
			for (var a in win) {
				if (typeof(win[a]) == "function") {
					var k = (win[a].toString()).split("\n");
					if (!(k.length == 3 && k[1].search(/\[native\scode\]/gi) != -1)) { win[a] = null; }
				}
			}
		}
		// remove from page
		win._content = null;
		var p = win.parentNode;
		p.removeChild(win);
		delete this.wins[win.idd];
		win = null;
		p = null;
	}
	
	this._removeEvents = function(obj) {
		obj.onmouseover = null;
		obj.onmouseout = null;
		obj.onmousemove = null;
		obj.onclick = null;
		obj.ondblclick = null;
		obj.onmouseenter = null;
		obj.onmouseleave = null;
		obj.onmouseup = null;
		obj.onmousewheel = null;
		obj.onmousedown = null;
		obj.onselectstart = null;
		obj.onfocus = null;
		obj.style.display = "";
	}
	this._parseNestedForEvents = function(obj) {
		this._removeEvents(obj);
		for (var q=0; q<obj.childNodes.length; q++) {
			if (obj.childNodes[q].tagName != null) { this._parseNestedForEvents(obj.childNodes[q]); }
		}
	}
	
	this._attachStatusBar = function() {
		
	}
	
	this._attachWebMenu = function() {
		return null;
	}
	
	this._attachWebToolbar = function() {
		return null;
	}
	
	this._fixInnerObjs = function(win) {
		if (win.grid) { win.grid.setSizes(); win.grid.setSizes(); }
		if (win.tabbar) { win.tabbar.adjustOuterSize(); }
		if (win.menu) { win.menu._redistribTopLevelPositions(); }
		if (win.accordion) { win.accordion.setSizes(); }
		if (win.layout) { win.layout.setSizes(win); }
		if (win.folders) { win.folders.setSizes(); }
		if (win.editor) { if (_isOpera) { window.setTimeout(function(){win.editor.adjustSize();},10); } else { win.editor.adjustSize(); } }
	}
	
	this._clearAll = function() {
		this._clearDocumentEvents();
		for (var a in this.wins) {
			this._diableOnSelectInWin(this.wins[a]);
			this._removeWindowGlobal(this.wins[a]);
		}
		// modal covers and vp
		this.modalCoverD.style.display = "";
		this._parseNestedForEvents(this.modalCoverD);
		this.modalCoverD.parentNode.removeChild(this.modalCoverD);
		this.modalCoverD = null;
		this._parseNestedForEvents(this.modalCoverI);
		this.modalCoverI.style.display = "";
		this.modalCoverI.parentNode.removeChild(this.modalCoverI);
		this.modalCoverI = null;
		if (this.vp != document.body) { this.vp.parentNode.removeChild(this.vp); }
		this.vp = null;
		// skin params
		for (var a in this.skinParams) { delete this.skinParams[a]; }
		this.skinParams = null;
		that = null;
		// self functions
		for (var a in this) { if (typeof(this[a]) == "function") { this[a] = null; } }
		for (var a in this) { delete this[a]; }
		//
		// console.log(this)
	}
	
	this._clearDocumentEvents = function() {
		if (_isIE) {
			window.detachEvent("onresize", this._doOnResize);
			document.body.detachEvent("onselectstart", this._doOnSelectStart);
			document.body.detachEvent("onmouseup", this._doOnMouseUp);
			document.body.detachEvent("onunload", this._doOnUnload);
			this.vp.detachEvent("onmousemove", this._doOnMoseMove);
			this.vp.detachEvent("onmouseup", this._doOnMouseUp);
		} else {
			window.removeEventListener("resize", this._doOnResize, false);
			document.body.removeEventListener("mouseup", this._doOnMouseUp, false);
			document.body.removeEventListener("unload", this._doOnUnload, false);
			this.vp.removeEventListener("mousemove", this._doOnMoseMove, false);
			this.vp.removeEventListener("mouseup", this._doOnMouseUp, false);
		}
	}
	
	/* additional features */
	if (this._enableStatusBar != null) { this._enableStatusBar(); }
	if (this._enableWebMenu != null) { this._enableWebMenu(); }
	if (this._enableWebToolbar != null) { this._enableWebToolbar(); }
	
	this._genStr = function(w) {
		var s = ""; var z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for (var q=0; q<w; q++) { s = s + z.charAt(Math.round(Math.random() * z.length)); }
		return s;
	}
	
	// events
	this.dhx_Event = function() {
		this.dhx_SeverCatcherPath="";
		/**
		*   @desc: attaches an event handler
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
		*   @param: id - event's id
		*   @type: public
		*/
		this.detachEvent = function(id) {
			if (id != false) {
				var list = id.split(':'); //get EventName and ID
				this[list[0]].removeEvent(list[1]); //remove event
			}
		}
	}
	this.dhx_Event();
	return this;
};
