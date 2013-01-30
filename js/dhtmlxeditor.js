//v.2.1 build 90226

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
/*_TOPICS_
@0:Formatting
@1:Getting data 
*/


/**
*  	@desc: Constructor of dhtmlxEditor object.
*	@param: id - parent object id
*	@param: skin - skin name for editor
*  	@type:   public
*/
function dhtmlXEditor(id, skin) {
	
	this.skin = (skin!=null?skin:"dhx_blue");
	this._tbH = 24; // toolbar height
	if (this.skin == "standard") {
		this._tbH = 26;
	}
	this.iconsPath = "codebase/imgs/";
	this.setIconsPath = function(path) {
		this.iconsPath = path;
	}
	this._genStr = function(w) {
		var s = ""; var z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for (var q=0; q<w; q++) { s = s + z.charAt(Math.round(Math.random() * z.length)); }
		return s;
	}
	this.init = function() {
		this._attachToolbar();
	}
	
	this.toolbarId = "dhxToolbar"+this._genStr(6);
	
	this.base = (typeof(id)=="object"?id:document.getElementById(id));
	while (this.base.childNodes > 0) { this.base.removeChild(this.base.childNodes[0]); }
	//this.base.style.overflow = "hidden";
	this.base.innerHTML = "<table border='0' cellspacing='0' cellpadding='0' style='width:100%;height:100%;table-layout:fixed;'>"+
				"<tr><td align='left' valign='top' class='dhxeditor_toolbar_"+this.skin+"'><div id='"+this.toolbarId+"' style='width:100%;overflow:hidden;'></div></td></tr>"+
				"<tr><td align='left' valign='top'>"+
					"<table border='0' cellspacing='0' cellpadding='1' style='width:100%;height:100%;table-layout:fixed;'><tr><td align='left' valign='top'>"+
						"<div class='dhxeditor_container_"+this.skin+"'></div>"+
					"</td></tr>"+
				"</table></td></tr>"+
				"</table>";
	this.editor = document.createElement("IFRAME");
	this.editor.className = "dhxeditor_mainiframe_"+this.skin;
	this.editor.frameBorder = 0;
	if (_isOpera) {
		this.editor.scrolling = "yes";
	}
	// need for size adjusting
	this._td1 = this.base.childNodes[0].childNodes[0].childNodes[0].childNodes[0];
	this._td2 = this.base.childNodes[0].childNodes[0].childNodes[1].childNodes[0];
	this._td2cont = this.base.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0];
	this._cont = this.base.childNodes[0].childNodes[0].childNodes[1].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0].childNodes[0];
	this._cont.appendChild(this.editor);
	
	this.edWin=this.editor.contentWindow;
	this.edDoc=this.edWin.document;
	this.edWin=this.editor.contentWindow;
	var content=this.edDoc;
	
	content.open("text/html", "replace");
	if (_isOpera) {
		content.write("<html><head><style> html, body { overflow:auto; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body contenteditable='true'></body></html>");
	} else {
		var p = false;
		if (window._KHTMLrv) {
			if (window._KHTMLrv < 526) {
				p = true;
				content.write("<html><head><style> html, body { overflow-x:auto; overflow-y:scroll; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body contenteditable='true'></body></html>");
			}
		}
		if (!p) content.write("<html><head><style> html {overflow:hidden; height:100%;} body { overflow-x:auto; overflow-y:scroll; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:8pt; background-color:#ffffff;} </style></head><body contenteditable='true'></body></html>");
	}
	content.close();
	
	dhtmlxEventable(this);
	this.edDoc.designMode='On';
	var that = this;

	dhtmlxEvent(this.edDoc, "click", function(e){
		var ev = e||window.event;
		var el = ev.target||ev.srcElement;
		that.showInfo(el);
	})
	if(_isOpera)
	dhtmlxEvent(this.edDoc, "mousedown", function(e){
		var ev = e||window.event;
		var el = ev.target||ev.srcElement;
		that.showInfo(el);
	})
	dhtmlxEvent(this.edDoc, "keyup", function(e){
		var ev = e||window.event;
		var key = ev.keyCode;
		var el = ev.target||ev.srcElement;
		if((key==37)||(key==38)||(key==39)||(key==40)||(key==13))
			that.showInfo(el);
	})
	
	this.adjustSize = function() {
		this._td1.style.height = this._tbH+"px";
		// this._td2.style.height = this.base.childNodes[0].offsetHeight-this._tbH+"px";
		this._td2.style.height = this.base.offsetHeight-this._tbH+"px";
		// this._td2cont.style.height = this._td2.offsetHeight-3+"px"
		this._td2cont.style.height = this.base.offsetHeight - this._tbH - (_isIE?5:5) +"px";
		this._cont.style.height = this._td2cont.style.height;
		this.editor.style.height = this._td2cont.style.height;
	}
	
	this.adjustSize();
	
	this.style = null;
	//if(_isOpera) this.edDoc.execCommand("styleWithCSS",false,true); 
	
	if(_isFF) this.edDoc.execCommand("useCSS",false,true);
	
	
	/**
	* @desc: makes seleted text bold
	* @type: public
	* @topic: 0
	*/
	this.applyBold = function(){
		this.runCommand("Bold");
	}
	/**
	* @desc: makes seleted text italic
	* @type: public
	* @topic: 0
	*/
	this.applyItalic = function(){
		this.runCommand("Italic");
	}
	/**
	* @desc: makes seletion underscored
	* @type: public
	* @topic: 0
	*/
	this.applyUnderscore = function(){
		this.runCommand("Underline");
	}
	/**
	* @desc: makes seletion strikethroughed
	* @type: public
	* @topic: 0
	*/
	this.applyStrikethrough = function(){
		this.runCommand("StrikeThrough");
	}
	/**
	* @desc: aligns selected text along the left margin
	* @type: public
	* @topic: 0
	*/
	this.alignLeft = function(){
		this.runCommand("JustifyLeft");
	}
	/**
	* @desc: aligns selected text along the right margin
	* @type: public
	* @topic: 0
	*/
	this.alignRight = function(){
		this.runCommand("JustifyRight");
	}
	/**
	* @desc: centres seleted text
	* @type: public
	* @topic: 0
	*/
	this.alignCenter = function(){
		this.runCommand("JustifyCenter");
	}
	/**
	* @desc: makes text to take the entire space available
	* @type: public
	* @topic: 0
	*/
	this.alignJustify = function(){
		this.runCommand("JustifyFull");
	}
	/**
	* @desc: applies subscript
	* @type: public
	* @topic: 0
	*/
	this.applySub = function(){
		this.runCommand("Subscript");
	}
	/**
	* @desc: applies superscript
	* @type: public
	* @topic: 0
	*/
	this.applySuper = function(){
		this.runCommand("Superscript");
	}
	/**
	* @desc: applies H1 to selected block of text
	* @type: public
	* @topic: 0
	*/
	this.applyH1 = function(){
		this.runCommand("FormatBlock","<H1>");
	}
	/**
	* @desc: applies H2 to selected block of text
	* @type: public
	* @topic: 0
	*/
	this.applyH2 = function(){
		this.runCommand("FormatBlock","<H2>");
	}
	/**
	* @desc: applies H3 to selected block of text
	* @type: public
	* @topic: 0
	*/
	this.applyH3 = function(){
		this.runCommand("FormatBlock","<H3>");
	}
	/**
	* @desc: applies H4 to selected block of text
	* @type: public
	* @topic: 0
	*/
	this.applyH4 = function(){
		this.runCommand("FormatBlock","<H4>");
	}
	/**
	* @desc: creates numbered list
	* @type: public
	* @topic: 0
	*/
	this.createNumList = function(){
		this.runCommand("InsertOrderedList");
	}
	/**
	* @desc: creates bullet list
	* @type: public
	* @topic: 0
	*/
	this.createBulList = function(){
		this.runCommand("InsertUnorderedList");
	}
	/**
	* @desc: increases indent of text block
	* @type: public
	* @topic: 0
	*/
	this.increaseIndent = function(){
		this.runCommand("Indent");
	}
	/**
	* @desc: decreases indent of text block
	* @type: public
	* @topic: 0
	*/
	this.decreaseIndent = function(){
		this.runCommand("Outdent");
	}
	/**
	* @desc: gets html content of editor document
	* @type: public
	* @topic: 1
	*/
	this.getContent = function(){
		if(!this.edDoc.body)
			return "";
		else{
			if(_isFF){
				return this.edDoc.body.innerHTML.replace(/<br>$/,"")
			}
			return this.edDoc.body.innerHTML;
		}
	}
	/**
	* @desc: sets content to editor document
	* @type: public
	* @param: html - html string which should be set as editor content 
	* @topic: 0
	*/
	this.setContent = function(str){
		if(this.edDoc.body){
			this.edDoc.body.innerHTML = str; 
			this.callEvent("onContentSet",[]);
			 if(_isFF){ this.runCommand('InsertHTML',' ');}
		}
		else{
			var that = this;
			dhtmlxEvent(this.edWin, "load", function(e){
				that.setContent(str);
			})
		}
	}
	/**
	* @desc: sets content from the html document to editor document
	* @type: public
	* @param: url - path to the html page 
	* @topic: 0
	*/
	this.setContentHTML = function(url){
		(new dtmlXMLLoaderObject(this._ajaxOnLoad,this,false,true)).loadXML(url);
	}
	this._ajaxOnLoad = function(obj,a,b,c,loader){
		if(loader.xmlDoc.responseText) obj.setContent(loader.xmlDoc.responseText); 
	}
	/**
	* @desc: carried out execCommand method
	* @type: private
	* @param: name - name of the command
	* @param: param - command parameter
	*/
	this.runCommand = function(name,param){
		if(arguments.length < 2) param = null;
		this.edDoc.execCommand(name,false,param);
		if(_isIE)this.edWin.focus();
	}
	/**
	* @desc: gets selection bounds: root, start and end nodes
	* @type: private
	*/
	this.getSelectionBounds = function(){
   		var range, root, start, end;
		if(this.edWin.getSelection){ 
      		var selection = this.edWin.getSelection();
      		range = selection.getRangeAt(selection.rangeCount-1);
      		start = range.startContainer;
      		end = range.endContainer;
			root = range.commonAncestorContainer;
      		if(start.nodeName == "#text") root = root.parentNode; 
	    	if(start.nodeName == "#text") start = start.parentNode;
			if (start.nodeName.toLowerCase() == "body") start = start.firstChild;
      		if(end.nodeName == "#text") end = end.parentNode;
			if (end.nodeName.toLowerCase() == "body") end = end.lastChild;
			if(start == end) root = start;	
			return {
        		root: root,
        		start: start,
        		end: end
      		}
		}else if(this.edWin.document.selection){ 
			range = this.edDoc.selection.createRange()
      		if(!range.duplicate) return null;
			root = range.parentElement();
      		var r1 = range.duplicate();
      		var r2 = range.duplicate();
      		r1.collapse(true);
      		r2.moveToElementText(r1.parentElement());
      		r2.setEndPoint("EndToStart",r1);
      		start = r1.parentElement();
      		r1 = range.duplicate();
      		r2 = range.duplicate();
      		r2.collapse(false);
      		r1.moveToElementText(r2.parentElement());
      		r1.setEndPoint("StartToEnd", r2);
      		end = r2.parentElement();
	   		if (start.nodeName.toLowerCase() == "body") start = start.firstChild;
			if (end.nodeName.toLowerCase() == "body") end = end.lastChild;
			
      		if(start == end) root = start;
     	 	return {
         		root: root,
         		start: start,
         		end: end
			}
   		}
   		return null 
	}
	/**
	* @desc: get parent node by tag
	* @type: private
	*/
	this.getParentByTag = function(node, tag_name){
		tag_name = tag_name.toLowerCase()
		var p = node
		do{
			if(tag_name == '' || p.nodeName.toLowerCase() == tag_name) return p
   		}while(p = p.parentNode)
		return node
	}
	/**
	* @desc: check if node has style property (for 2-state buttons in Safari)
	* @type: private
	*/
	this.isStyleProperty = function(node, tag_name, name, value){
		tag_name = tag_name.toLowerCase();
		var n = node;
		do{
			if((n.nodeName.toLowerCase() == tag_name)&&(n.style[name]==value)) return true
   		}while(n = n.parentNode)
		
		return false
	}
	/**
	* @desc: return style hash for the element
	* @type: private
	*/
	this.showInfo = function(el){
		var el = (this.getSelectionBounds().root)?this.getSelectionBounds().root : el;
		if(!el) return
		try{
			if(this.edWin.getComputedStyle){
				var st = this.edWin.getComputedStyle(el, null);
				var fw = ((st.getPropertyValue("font-weight")==401)?700:st.getPropertyValue("font-weight"));
				this.style =  { fontStyle	: st.getPropertyValue("font-style"),
					fontSize	: st.getPropertyValue("font-size"),
					textDecoration	: st.getPropertyValue("text-decoration"),
					fontWeight	: fw,
					fontFamily	: st.getPropertyValue("font-family"),
					textAlign	: st.getPropertyValue("text-align")
				};
				if(window._KHTMLrv){/*if Safari*/
					this.style.fontStyle = st.getPropertyValue("font-style");
					this.style.vAlign = st.getPropertyValue("vertical-align");
					this.style.del = this.isStyleProperty(el,"span","textDecoration","line-through");
					this.style.u = this.isStyleProperty(el,"span","textDecoration","underline");
				}
			}
			else{
				var st = el.currentStyle;
				this.style =  { fontStyle	: st.fontStyle,
					fontSize	: st.fontSize,
					textDecoration	: st.textDecoration,
					fontWeight	:  st.fontWeight,
					fontFamily	: st.fontFamily,
					textAlign	: st.textAlign
				};
			}
			this.setStyleProperty(el,"h1");
			this.setStyleProperty(el,"h2");
			this.setStyleProperty(el,"h3");
			this.setStyleProperty(el,"h4");
			if(!window._KHTMLrv){
				this.setStyleProperty(el,"del");
				this.setStyleProperty(el,"sub");
				this.setStyleProperty(el,"sup");
				this.setStyleProperty(el,"u");
			}
			this.callEvent("onFocusChanged",[this.style, st])
		}
		catch(e){ return null}
	}
	this.setStyleProperty = function(el,prop){
		this.style[prop] = false;
		var n = this.getParentByTag(el,prop);
		if(n&&(n.tagName.toLowerCase()==prop)) this.style[prop] = true;
		if(prop == "del")
		if(this.getParentByTag(el,"strike")&&(this.getParentByTag(el,"strike").tagName.toLowerCase()=="strike")) this.style.del = true;
	}
}

dhtmlXEditor.prototype._attachToolbar = function() {
	
	this.tb = new dhtmlXToolbarObject(this.toolbarId, this.skin);
	this.tb.setIconsPath(this.iconsPath+"dhxeditor_"+this.skin+"/");
	
	this._availFonts = new Array("Arial", "Arial Narrow", "Comic Sans MS", "Courier", "Georgia", "Impact", "Tahoma", "Times New Roman", "Verdana");
	this._initFont = this._availFonts[0];
	this._xmlFonts = "";
	for (var q=0; q<this._availFonts.length; q++) {
		var fnt = String(this._availFonts[q]).replace(/\s/g,"_");
		this._xmlFonts += '<item type="button" id="applyFontFamily:'+fnt+'"><itemText><![CDATA[<img src="'+this.tb.imagePath+'font_'+String(fnt).toLowerCase()+'.gif" border="0" style="/*margin-top:1px;margin-bottom:1px;*/width:110px;height:16px;">]]></itemText></item>';
	}
	//
	this._availSizes = {"1":"8pt", "2":"10pt", "3":"12pt", "4":"14pt", "5":"18pt", "6":"24pt", "7":"36pt"};
	this._xmlSizes = "";
	for (var a in this._availSizes) {
		this._xmlSizes += '<item type="button" id="applyFontSize:'+a+':'+this._availSizes[a]+'" text="'+this._availSizes[a]+'"/>';
	}
	//
	this.tbXML = '<webbar id="0">'+
				// h1-h4
				'<item id="applyH1" type="buttonTwoState" img="h1.gif" imgdis="h4_dis.gif" title="H1"/>'+
				'<item id="applyH2" type="buttonTwoState" img="h2.gif" imgdis="h4_dis.gif" title="H2"/>'+
				'<item id="applyH3" type="buttonTwoState" img="h3.gif" imgdis="h4_dis.gif" title="H3"/>'+
				'<item id="applyH4" type="buttonTwoState" img="h4.gif" imgdis="h4_dis.gif" title="H4"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
				// text
				'<item id="applyBold" type="buttonTwoState" img="bold.gif" imgdis="bold_dis.gif" title="Bold Text"/>'+
				'<item id="applyItalic" type="buttonTwoState" img="italic.gif" imgdis="italic_dis.gif" title="Italic Text"/>'+
				'<item id="applyUnderscore" type="buttonTwoState" img="underline.gif" imgdis="underline_dis.gif" title="Underscore Text"/>'+
				'<item id="applyStrikethrough" type="buttonTwoState" img="strike.gif" imgdis="strike_dis.gif" title="Strikethrough Text"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
				// align
				'<item id="alignLeft" type="buttonTwoState" img="align_left.gif" imgdis="align_left_dis.gif" title="Left Alignment"/>'+
				'<item id="alignCenter" type="buttonTwoState" img="align_center.gif" imgdis="align_center_dis.gif" title="Center Alignment"/>'+
				'<item id="alignRight" type="buttonTwoState" img="align_right.gif" imgdis="align_right_dis.gif" title="Right Alignment"/>'+
				'<item id="alignJustify" type="buttonTwoState" img="align_justify.gif" title="Justified Alignment"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
				// sub/super script
				'<item id="applySub" type="buttonTwoState" img="script_sub.gif" imgdis="script_sub.gif" title="Subscript"/>'+
				'<item id="applySuper" type="buttonTwoState" img="script_super.gif" imgdis="script_super_dis.gif" title="Superscript"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
				// etc
				'<item id="createNumList" type="button" img="list_number.gif" imgdis="list_number_dis.gif" title="Number List"/>'+
				'<item id="createBulList" type="button" img="list_bullet.gif" imgdis="list_bullet_dis.gif" title="Bullet List"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
				'<item id="increaseIndent" type="button" img="indent_inc.gif" imgdis="indent_inc_dis.gif" title="Increase Indent"/>'+
				'<item id="decreaseIndent" type="button" img="indent_dec.gif" imgdis="indent_dec_dis.gif" title="Decrease Indent"/>'+
				'<item id="sep'+this._genStr(6)+'" type="separator"/>'+
			'</webbar>';
	this.tb.loadXMLString(this.tbXML);
	
	
	this._checkAlign = function(alignSelected) {
		this.tb.setItemState("alignCenter", false);
		this.tb.setItemState("alignRight", false);
		this.tb.setItemState("alignJustify", false);
		this.tb.setItemState("alignLeft", false);
		if(alignSelected)
			this.tb.setItemState(alignSelected, true);
	}
	this._checkH = function(h) {
		this.tb.setItemState("applyH1", false);
		this.tb.setItemState("applyH2", false);
		this.tb.setItemState("applyH3", false);
		this.tb.setItemState("applyH4", false);
		if(h)
			this.tb.setItemState(h, true);
	}
	this._doOnFocusChanged = function(state) {
		/*bold*/
		if(!state.h1&&!state.h2&&!state.h3&&!state.h4){
			var bold = (String(state.fontWeight).search(/bold/i) != -1) || (Number(state.fontWeight) >= 700);
			this.tb.setItemState("applyBold", bold);
		}
		else this.tb.setItemState("applyBold", false);
		// align
		var alignId = "alignLeft";
		if (String(state.textAlign).search(/center/) != -1) { alignId = "alignCenter"; }
		if (String(state.textAlign).search(/right/) != -1) { alignId = "alignRight"; }
		if (String(state.textAlign).search(/justify/) != -1) { alignId = "alignJustify"; }
		this.tb.setItemState(alignId, true);
		this._checkAlign(alignId);
		/*heading*/
		this.tb.setItemState("applyH1", state.h1);
		this.tb.setItemState("applyH2", state.h2);
		this.tb.setItemState("applyH3", state.h3);
		this.tb.setItemState("applyH4", state.h4);
		if(window._KHTMLrv) {/*for Safari*/
			state.sub = (state.vAlign == "sub");
			state.sup = (state.vAlign == "super");
		}
		this.tb.setItemState("applyItalic", (state.fontStyle == "italic"));
		this.tb.setItemState("applyStrikethrough", state.del);
		this.tb.setItemState("applySub", state.sub);
		this.tb.setItemState("applySuper", state.sup);
		this.tb.setItemState("applyUnderscore", state.u);
	}
	this._doOnToolbarClick = function(id) {
		var action = String(id).split(":");
		if (this[action[0]] != null) {
			if (typeof(this[action[0]]) == "function") {
				this[action[0]](action[1]);
			}
		}
	}
	this._doOnStateChange = function(itemId, state) {
		this[itemId]();
		switch (itemId) {
			case "alignLeft":
			case "alignCenter":
			case "alignRight":
			case "alignJustify":
				this._checkAlign(itemId);
				break;
			case "applyH1":
			case "applyH2":
			case "applyH3":
			case "applyH4":
				this._checkH(itemId);
				break;
		}
	}
	this._doOnBeforeStateChange = function(itemId, state) {
		if ((itemId == "alignLeft" || itemId == "alignCenter" || itemId == "alignRight" || itemId == "alignJustify") && state == true) {
			return false;
		}
		return true;
	}
	var that = this;
	this.attachEvent("onFocusChanged", function(state){that._doOnFocusChanged(state);});
	this.tb.attachEvent("onClick", function(id){that._doOnToolbarClick(id);});
	this.tb.attachEvent("onStateChange", function(id,st){that._doOnStateChange(id,st);});
	this.tb.attachEvent("onBeforeStateChange", function(id,st){return that._doOnBeforeStateChange(id,st);});
	
}


