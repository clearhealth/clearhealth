/*****************************************************************************
*       ch3main.js
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
var mainFileLoaderClass = function(){
	this.value = 0;
	this.sources = Array();
	this.sourcesDB = Array();
	this.totalFiles = 0;
	this.loadedFiles = 0;
	this.totalLinkFiles = 0;
	this.onLoadComplete = null;
	this.dimension = null; // [0] = width [1] = height
};
//Show the loading bar interface
mainFileLoaderClass.prototype.show = function() {
	this.locate();
	document.getElementById("loadingZone").style.display = "block";
};
//Hide the loading bar interface
mainFileLoaderClass.prototype.hide = function() {
	document.getElementById("loadingZone").style.display = "none";
};
//Add all scripts to the DOM
mainFileLoaderClass.prototype.run = function(){
	// add current number of stylesheets to totalLinkFiles
	this.totalLinkFiles += document.styleSheets.length;
	this.show();
	var i;
	var head = document.getElementsByTagName("head")[0];
	var source = null;
	var type = null;
	var elem = null;
	for (i=0; i<this.sourcesDB.length; i++){
		source = this.sourcesDB[i];
		type = this.sources[source];

		if (type == 'script') {
			elem = document.createElement("script");
			elem.type = "text/javascript";
			elem.src = source;
			elem.onload = function() { setTimeout("mainFileLoader.loaded('"+source+"');",500); };
			// IE 6 & 7
			elem.onreadystatechange = function() {
				if (this.readyState == 'complete') {
					setTimeout("mainFileLoader.loaded('"+source+"');",500);
				}
			};
		}
		else if (type == 'link') {
			elem = document.createElement("link");
			elem.media = "screen";
			elem.rel = "stylesheet";
			elem.type = "text/css";
			elem.href = source;
			setTimeout("mainFileLoader.loaded('"+source+"')",1000);
		}
		else {
			continue;
		}
		head.appendChild(elem);
	}
};

//Center in the screen remember it from old tutorials? ;)
mainFileLoaderClass.prototype.locate = function(){
	var loadingZone = document.getElementById("loadingZone");
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	if (this.dimension !== null) {
		windowWidth = this.dimension[0];
		windowHeight = this.dimension[1];
	}
	var popupHeight = loadingZone.clientHeight;
	var popupWidth = loadingZone.clientWidth;
	loadingZone.style.position = "absolute";
	loadingZone.style.top = parseInt((windowHeight/2)-(popupHeight/2),10) + "px";
	loadingZone.style.left = parseInt((windowWidth/2)-(popupWidth/2),10) + "px";
};
//Set the value position of the bar (Only 0-100 values are allowed)
mainFileLoaderClass.prototype.setValue = function(value){
	if(value >= 0 && value <= 100){
		document.getElementById("progressBar").style.width = value + "%";
		document.getElementById("infoProgress").innerHTML = parseInt(value,10) + "%";
	}
};
//Add the specified script to the list
mainFileLoaderClass.prototype.addFile = function(source,type){
	if (type == 'link') {
		this.totalLinkFiles++;
	}
	this.totalFiles++;
	this.sources[source] = type;
	this.sourcesDB.push(source);
};
//Called when a script is loaded. Increment the progress value and check if all files are loaded
mainFileLoaderClass.prototype.loaded = function(file) {
	this.loadedFiles++;
	delete this.sources[file];
	var pc = (this.loadedFiles * 100) / this.totalFiles;
	this.setValue(pc);
	//Are all files loaded?
	if (this.loadedFiles == this.totalFiles){
		setTimeout("mainFileLoader.hide();",300);
		// all were setting up... monitor use activity
		setTimeout("mainFileLoader.onLoadingCompleted();",500);
	}
};

//Global var to reference from other scripts

mainFileLoaderClass.prototype.onLoadingCompleted = function() {
	var timeoutInterval = 10;
	var i = 0;
	do {
		if (document.styleSheets.length >= this.totalLinkFiles) {
			break;
		}
		i++;
	} while (i < timeoutInterval);
	if (mainFileLoader.onLoadComplete !== null) {
		setTimeout(mainFileLoader.onLoadComplete+"();",100);
	}
	else {
		var defaultAction = globalDefaultAction;
		splittedAction = defaultAction.split('/');
		splittedAction[0] += '.raw';
		var action = splittedAction.join('/');
		// render the default page using dojo.xhrGet in raw format
		dojo.xhrGet({
			url: globalBaseUrl+"/"+action,
			handleAs: "text",
			load: function(data, ioArgs) {
				dojo.setInnerHTML(dojo.byId('mainContentLayout'),data);
				return data;
			},
			error: function(response, ioArgs) {
				console.error("HTTP status code: ", ioArgs.xhr.status);
				return response;
			}
		});
	}
	mainController.attachPageActivityListener();
	mainController.startTimer();
	// re-initialize everything
	this.value = 0;
	this.sources = Array();
	this.sourcesDB = Array();
	this.totalFiles = 0;
	this.loadedFiles = 0;
	this.totalLinkFiles = 0;
	this.onLoadComplete = null;
	this.dimension = null; // [0] = width [1] = height
};

mainFileLoaderClass.prototype.render = function(layoutContainer) { // load all the necessary javascript/css files
	if (typeof layoutContainer == "undefined") {
		layoutContainer = "loginContainer";
	}
	var loaderDiv = '<style>table{border-collapse:separate;border-spacing:0pt;} caption,th,td{font-weight:normal;text-align:left;} blockquote:before,blockquote:after,q:before,q:after{content:"";} blockquote,q{quotes:"" "";} a{cursor:pointer;text-decoration:none;} .clear{clear:both;} #button{text-align:center;margin:50px 50px 150px 50px;} #loadingZone{margin:0 auto;width:410px;	text-align:center;} #loadingBar{border:1px solid #c2c2c2;height:2px;text-align:left;line-height:0;margin:0;padding:0;overflow:hidden; /*fix for IE 6*/} #progressBar{height:2px;line-height:0;margin:0;padding:0;background:#ccc;width:0%;} #loadingSms{color:#6ea1fa;float:left;padding:10px 2px;} #infoProgress{color:#6ea1fa;float:right;padding:10px 2px;} #infoLoading{padding:10px;color:#b9b9b9;font-size:10px;}</style> <div id="loadingZone"><div id="loadingSms">LOADING</div><div id="infoProgress">0%</div><br class="clear" /><div id="loadingBar"><div id="progressBar">&nbsp;</div></div><div id="infoLoading"></div></div>';

	document.getElementById(layoutContainer).innerHTML = loaderDiv;

	mainFileLoader.addFile(globalBaseUrl + "/cache-file.raw/css?files=dojocss,dhtmlxcss","link");
	mainFileLoader.addFile(globalBaseUrl + "/cache-file.raw/js?files=chbootstrap,dojojs,dhtmlxjs","script");
	mainFileLoader.run();
};


function mainControllerClass() {

}
mainControllerClass.prototype.personId = 0;
mainControllerClass.prototype.roomId = 0;
mainControllerClass.prototype.buildingId = 0;
mainControllerClass.prototype.practiceId = 0;
mainControllerClass.prototype.visitId = 0;
mainControllerClass.prototype.setActivePatient = function (personId) {
	this.personId = personId;
	if (typeof initMainToolbar == 'function') {
		initMainToolbar(null,personId);
	}
	/*var evt = document.createEvent("Events");
	evt.initEvent('activePatientChanged',true,true);
	document.dispatchEvent(evt);*/
};
mainControllerClass.prototype.getActivePatient = function() {
	return this.personId;
};
mainControllerClass.prototype.refreshActivePatient = function() {
	return this.setActivePatient(this.personId);
};

mainControllerClass.prototype.setActiveRoom = function (roomId) {
	this.roomId = roomId;
};
mainControllerClass.prototype.getActiveRoom = function() {
	return this.roomId;
};

mainControllerClass.prototype.setActiveBuilding = function (buildingId) {
	this.buildingId = buildingId;
};
mainControllerClass.prototype.getActiveBuilding = function() {
	return this.buildingId;
};

mainControllerClass.prototype.setActivePractice = function (practiceId) {
	this.practiceId = practiceId;
};
mainControllerClass.prototype.getActivePractice = function() {
	return this.practiceId;
};

mainControllerClass.prototype.setActiveVisit = function (visitId) {
	this.visitId = visitId;
};
mainControllerClass.prototype.getActiveVisit = function() {
	return this.visitId;
};

mainControllerClass.prototype.popupLoginWindow = function() {
	if (dhxWins.isWindow("windowLoginId")) {
		winSP = dhxWins.window("windowLoginId");
	}
	else {
		winSP = dhxWins.createWindow('windowLoginId',60,10,400,300);
	}
	winSP.progressOn();
	winSP.setText('Login');
	winSP.attachURL(globalBaseUrl + '/login.popup/panel',true);
	winSP.setModal(true);
	winSP.centerOnScreen();
	// hides window buttons
	winSP.button("park").hide();
	winSP.button("minmax1").hide();
	winSP.button("minmax2").hide();
	winSP.button("close").hide();
	// clear timer to prevent multiple login popup
	if (globalLogoutTimer) {
		clearTimeout(globalLogoutTimer);
		globalLogoutTimer = null;
	}
};
mainControllerClass.prototype.startTimer = function() {
	var second = 1000;
	var minute = 60 * second;
	//var interval = 5 * minute;
	var interval = globalTimerTimeout * second;
	if (globalLogoutTimer) {
		clearTimeout(globalLogoutTimer);
		globalLogoutTimer = null;
	}
	interval = 360000000;
	if (globalAutologout > 0) interval = globalAutologout * minute;
	globalLogoutPopupWarning = false;
	setTimeout("if (!globalLogoutPopupWarning) { alert('You are about to automatically logout in 2 minutes.'); globalLogoutPopupWarning = true; }",(interval - (2 * minute)));
	globalLogoutTimer = setTimeout("mainController.forcedLogout()",interval);
};
mainControllerClass.prototype.forcedLogout = function() {
	window.location = globalBaseUrl + "/logout";
	return;
	dojo.xhrGet({
		url: globalBaseUrl + '/logout.raw',
		content: {
			noRedirection: 1
		},
		handleAs: "text",
		load: function(data,ioArgs) {
			globalForceLogout = true;
			mainController.popupLoginWindow();
			return data;
		},
		error: function(response, ioArgs) {
			console.error("HTTP status code: ", ioArgs.xhr.status);
			return response;
		}
	});
};
mainControllerClass.prototype.isForcedLogout = function() {
	return globalForceLogout;
};
mainControllerClass.prototype.attachPageActivityListener = function() {
	dojo.connect(dojo,"xhrGet",null,function(args){ mainController.startTimer(); });
	dojo.connect(dojo,"xhrPost",null,function(args){ mainController.startTimer(); });
};


function barcodeControllerClass () {
	this.codeDelimiter = '';
	this.bufferCode = false;
	this.keyBuffer = '';
	this.cacheMacros = [];
	window.captureEvents(Event.KEYPRESS);
}
barcodeControllerClass.prototype.runMacro = function(cacheIndex) {
	var macro = this.cacheMacros[cacheIndex];
	if (typeof eval('jsBarcodeMacro'+macro.name) == 'function') {
		var jsObject = eval('new jsBarcodeMacro'+macro.name+'()');
		if (typeof jsObject.handleRead == 'function') {
			jsObject.handleRead(macro.barcodeString,mainController.getActivePatient(),mainController.getActiveVisit());
		}
		else {
			alert('Count not find handleRead method in loaded object: jsBarcodeMacro'+macro.name);
		}
		jsObject = undefined;
	} else {
		alert('Count not find macro definition in content load for class: jsBarcodeMacro'+macro.name);
	}
};
barcodeControllerClass.prototype.triggerAction = function(bcString) {
	for (var i = 0; i < this.cacheMacros.length; i++) {
		if (this.cacheMacros[i].barcodeString == bcString) {
			this.runMacro(i);
			return;
		}
	}
	thisClass = this;
	dojo.xhrGet ({
                url: globalBaseUrl + '/barcode.raw/handle-read?barcodeString=' + bcString + '&personId=' + mainController.getActivePatient() + '&visitId=' + mainController.getActiveVisit(),
                handleAs: 'json',
                load: function (data) {
                        //todo: add notification icon on the toolbar in the posts/alerts section
			if (typeof data.ret == "object") {
				var macro = {"barcodeString":bcString,"name":data.ret.name,"regex":data.ret.regex,"order":data.ret.order};
				var cacheIndex = thisClass.cacheMacros.length;
				thisClass.cacheMacros[cacheIndex] = macro;
				// insert macro code to head tag
				var html_doc = document.getElementsByTagName('head').item(0);
				var js = document.createElement('script');
				js.setAttribute('language', 'javascript');
				js.setAttribute('type', 'text/javascript');
				js.text = data.ret.macro;
				html_doc.appendChild(js);
				thisClass.runMacro(cacheIndex);
			}
                },
                error: function (er) {
                        alert('err: ' + er);
                }
        });
};
barcodeControllerClass.prototype.pressed = function (e) {
  	//alert("Key pressed! ASCII-value: " + e.which);
	if (this.bufferCode) {
		this.keyBuffer += String.fromCharCode(e.which);
	}
	if (e.which == 126) {
		this.codeDelimiter += '~';
		//document.getElementById('status').value += "tilde\n";
	}
	else if (e.which == 33 && this.codeDelimiter == '~') {
		this.codeDelimiter += '!';
		//document.getElementById('status').value += "bang\n";
	}
	else if (e.which == 33 && this.codeDelimiter == '~!') {
		this.codeDelimiter += '!';
		//document.getElementById('status').value += "bang2\n";
	}
	else if (e.which == 35 && this.codeDelimiter == '~!!') {
		this.codeDelimiter += '#';
		//document.getElementById('status').value += "pound\n";
	}
	else {
		this.codeDelimiter = '';
	}

	if (this.codeDelimiter == '~!!#') {
		if (this.bufferCode === true) { 
			this.bufferCode = false; 
			this.codeDelimiter = '';
			bcString = this.keyBuffer;
			this.keyBuffer = '';
			if (bcString.length >= 4) {
				bcString = bcString.substr(0,(bcString.length-4));
			}
			this.triggerAction(bcString);
		}
		else { 
			this.bufferCode = true; 
			//document.getElementById('status').value +=  'bufferOn\n';
		}
	}
};



/*a globals function needs to remain as the last function at the end of this file or parser bugs in FF and WebKit are hit with respect to the prototype function attachchments */
function varDump(obj) {
        var out = '';
        for (var i in obj) {
                out += i + ": " + obj[i] + "\n";
        }

        alert(out);
}

function globalCreateWindow(winId,params,url,winText,width,height,prop) {
	if (!prop) {
		prop = {
			"attachURL": true,
			"setModal": false,
		};
	}
	if (!dhxWins) {
		dhxWins = new dhtmlXWindows();
		dhxWins.setImagePath(globalBaseUrl+"/img/");
		dhxWins.pathPrefix = '';
		dhxWins.setSkin('clear_silver');
	}
	if (dhxWins.isWindow(winId)) {
		return dhxWins.window(winId);
	}
	var winCW = dhxWins.createWindow(winId,60,10,width,height);
	winCW.setText(winText);
	if (params.length > 0) {
		url += "?" + params.join("&");
	}
	if (prop.attachURL) {
		winCW.attachURL(url,true);
	}
	if (prop.setModal) {
		winCW.setModal(true);
	}
	winCW.centerOnScreen();
	return winCW;
}




/*
 * Class to draw an image and annotation 
 * Usage:
 * 
 * === HTML Code ===
 * <div id="contentSpace"></div>
 * <input type="button" value="Draw" onClick="drawing.setAction('draw')" />
 * <input type="button" value="Annotate" onClick="drawing.setAction('annotate')" />
 * <input type="button" value="Clear" onClick="drawing.setAction('clear')" />
 * <div id="surface" style="width:1632px;height:1250px;border: 1px solid #000;"></div>
 *
 * === JAVASCRIPT CODE ===
 * var surfaceHeight = 1250;
 * var surfaceWidth = 1650;
 * var drawing = new DrawingClass("surface",surfaceWidth,surfaceHeight,"contentSpace");
 * drawing.loadImage('<?=$this->baseUrl;?>/img/image.jpg');
 */

function DrawingClass(surface,width,height,contentSpace) {

	this.cache = [];
	this.penColor = "black";
	this.zIndex = 0;
	this.lineStroke = {width:2};
	this.lastPoint = {x:-1,y:-1};

	this.surface = surface;
	this.surfaceWidth = width;
	this.surfaceHeight = height;
	this.oSurface = dojox.gfx.createSurface(this.surface,this.surfaceWidth,this.surfaceHeight);
	this.contentSpace = contentSpace;
	this.oContentSpace = dojo.byId(this.contentSpace);
	this.oImage = null;
	this.oCanvas = null;
	this.cursor = "crosshair";

	// data/properties to be dumped
	this.lines = [];
	this.imageSrc = null;
	this.annotations = [];
	this.annotationBoxes = [];
	this.defaultLines = [];
	this.defaultAnnotations = [];

	this.isMouseDown = false;
	this.isMouseUp = false;
	this.isMouseMove = false;
	this.action = 'draw';
	this.isAnnotationOpen = false;
	this.annotationOpen = null;

	this.annotationValue = '';

	this.clinicalNoteId = 0;
	this.editURL = '';
	this.deleteURL = '';

	// action to be set, choices are [draw,annotate,clear]
	this.setAction = function(action) {
		this.action = action;
		switch(action) {
			case "draw":
				this.setImageCursor("crosshair");
				break;
			case "annotate":
				this.setImageCursor("text");
				break;
			case "clear":
				this.clearLines();
				//this.clearAnnotations();
				this.setImageCursor("point");
				break;
			default:
		}
	};

	this.setImageCursor = function(cursor) {
		this.cursor = cursor;
		this.oCanvas.setAttribute('style',"cursor:"+cursor);
	};

	this.createImage = function(width,height,src) {
		var surface = dojo.byId(this.surface);
		surface.style.width = width;
		surface.style.height = height;
		this.oImage = this.oSurface.createImage({width:width,height:height,src:src});
		this.oCanvas = this.oImage.getEventSource();
		this.oCanvas.setAttribute("id","imageId");
		this.setImageCursor("crosshair");

		var method = this;
		// set up mouse handlers
		this.oCanvas.onmousedown = function(evt) { method.onMouseDown(evt); };
		this.oCanvas.onmousemove = function(evt) { method.onMouseMove(evt); };
		this.oCanvas.onmouseup = function(evt) { method.onMouseUp(evt); };
		this.oCanvas.onmouseover = function(evt) { method.onMouseOver(evt); };
		this.oCanvas.onmouseout = function(evt) { method.onMouseOut(evt); };
	};

	this.onImageLoad = function(oImg) {
		this.createImage(oImg.width,oImg.height,oImg.src);
		this.loadDefaultLines();
		this.loadDefaultAnnotations();
	};

	this.onImageError = function(oImg) {
		alert("Image " + oImg.src + " loading error.");
	};

	this.onImageAbort = function(oImg) {
		alert("Image " + oImg.src + " aborted.");
	};

	this.loadImage = function(src) {
		var oImg = new Image();
		oImg.src = src;
		oImg.style.position = "absolute";

		var method = this;
		// set up event handlers for the Image object
		oImg.onload = function() { method.onImageLoad(this); };
		oImg.onerror = function() { method.onImageError(this); };
		oImg.onabort = function() { method.onImageAbort(this); };
	};

	this.onMouseDown = function(evt) {
		if (this.action == "clear" || this.isMouseDown) {
			return;
		}
		var x = evt.layerX;
		var y = evt.layerY;
		this.isMouseDown = true;
		this.lastPoint.x = x;
		this.lastPoint.y = y;
	};

	this.onMouseMove = function(evt) {
		if (this.action == "clear") {
			return;
		}
		if (this.isMouseDown && this.action != "annotate") {
			this.isMouseMove = true;
		}
	};

	this.onMouseUp = function(evt) {
		this.isMouseDown = false;
		if (this.isAnnotationOpen || this.action == "clear") { // allow only if there's no opened annotation
			return;
		}
		var x = evt.layerX;
		var y = evt.layerY;
		if (this.action == "annotate") {
			this.createAnnotation(x,y);
			return;
		}
		do {
			if (this.isMouseMove) {
				this.isMouseMove = false;
				break;
			}
			else if (this.isMouseUp) {
				this.isMouseUp = false;
				break;
			}
			else {
				this.isMouseUp = true;
				this.lastPoint.x = x;
				this.lastPoint.y = y;
				return;
			}
		} while(false);
		if (this.lastPoint.x == -1 || this.lastPoint.y == -1) {
			return;
		}
		this.createLine(x,this.lastPoint.x,y,this.lastPoint.y);
	};

	this.onMouseOver = function(evt) {
		// show all the annotations
		for (var i in this.annotations) {
			this.annotations[i].style.display = "";
		}
	};

	this.onMouseOut = function(evt) {
		// temporarily nothing to do
		return;
		// hide all the annotations only if there's no opened annotation
		if (this.isAnnotationOpen) {
			return;
		}
		for (var i in this.annotations) {
			this.annotations[i].style.display = "none";
		}
	};

	this.createLine = function(x1,x2,y1,y2) {
		var line = {x1:x1,x2:x2,y1:y1,y2:y2};
		var oLine = this.oSurface.createLine(line);
		oLine.setStroke(this.lineStroke);
		this.lines.push({oLine:oLine,line:line});
		this.lastPoint = {x:-1,y:-1};
		return oLine;
	};

	this.clearLines = function() {
		var line = null;
		while (this.lines.length) {
			line = this.lines.pop();
			line.oLine.removeShape();
		}
	};

	this.createAnnotation = function(x,y,isHide,value,valueId) {
		if (typeof isHide == 'undefined') {
			isHide = false;
		}
		var method = this;
		var height = 10;
		var width = 10;
		var divContainer = document.createElement('div');
		var oHref = document.createElement('a');
		//oHref.setAttribute('href','#');
		oHref.style.position = "absolute";
		oHref.style.margin = "0px";
		oHref.style.padding = "2px";
		oHref.style.overflow = "hidden";
		oHref.style.border = "3px groove rgb(0,255,0)"; // bright green
		oHref.style.zIndex = this.zIndex;
		oHref.style.left = x + "px";
		oHref.style.top = y + "px";
		oHref.style.width = width + "px";
		oHref.style.height = height + "px";
		oHref.style.display = "";
		oHref.onmouseover = function(evt) { oHref.style.border = "3px groove rgb(255,204,51)"; }; // medium light yellow
		oHref.onmouseout = function(evt) { oHref.style.border = "3px groove rgb(0,255,0)"; }; // bright green
		if (typeof value != 'undefined') {
			oHref.setAttribute("title",value);
		}

		divContainer.appendChild(oHref);

		var oDiv = document.createElement('div');
		oDiv.style.position = "absolute";
		oDiv.style.margin = "0px";
		oDiv.style.padding = "2px";
		oDiv.style.overflow = "auto";
		oDiv.style.zIndex = this.zIndex;
		oDiv.style.left = x + "px";
		oDiv.style.top = (y + height + 12) + "px";
		if (isHide) {
			oDiv.style.display = "none";
		}
		else {
			oDiv.style.display = "block";
		}

		var oAnnotation = document.createElement('textarea');
		oAnnotation.setAttribute('rows',3);
		oAnnotation.setAttribute('cols',30);
		if (isHide) {
			oAnnotation.value = value;
		}
		oDiv.appendChild(oAnnotation);

		var oBreakInput = document.createElement('br');
		oDiv.appendChild(oBreakInput);

		var oSaveInput = document.createElement('input');
		oSaveInput.setAttribute('type','button');
		oSaveInput.setAttribute('value','Save');
		oSaveInput.onclick = function(evt) { method.saveAnnotation(divContainer); };
		oDiv.appendChild(oSaveInput);

		var oCancelInput = document.createElement('input');
		oCancelInput.setAttribute('type','button');
		oCancelInput.setAttribute('value','Cancel');
		oCancelInput.onclick = function(evt) { method.cancelAnnotation(divContainer); };
		oDiv.appendChild(oCancelInput);

		var oDeleteInput = document.createElement('input');
		oDeleteInput.setAttribute('type','button');
		oDeleteInput.setAttribute('value','Delete');
		if (oAnnotation.value.length == 0) {
			oDeleteInput.style.display = "none";
		}
		else {
			oDeleteInput.style.display = "";
		}
		oDeleteInput.onclick = function(evt) { method.deleteAnnotation(divContainer); };
		oDiv.appendChild(oDeleteInput);

		var oIdInput = document.createElement('input');
		oIdInput.setAttribute('type','hidden');
		oIdInput.setAttribute('name','clinicalNoteAnnotationId');
		if (typeof valueId == 'undefined') {
			oIdInput.setAttribute('value','0');
		}
		else {
			oIdInput.setAttribute('value',valueId);
		}
		oDiv.appendChild(oIdInput);

		oHref.onclick = function(evt) { method.showAnnotationArea(divContainer); };

		divContainer.appendChild(oDiv);
		this.oContentSpace.appendChild(divContainer);

		this.annotations.push(divContainer);

		if (isHide) {
			this.isAnnotationOpen = false;
		}
		else {
			this.isAnnotationOpen = true;
			this.annotationOpen = divContainer;
		}

		return oDiv;
	};

	this.showAnnotationArea = function(oAnnotation) {
		// there must only be one annotation opened at a time
		if (!this.isAnnotationOpen) {
			this.annotationValue = oAnnotation.childNodes[1].childNodes[0].value;
			oAnnotation.childNodes[1].style.zIndex = 9999;
			oAnnotation.childNodes[1].style.display = "block";
			this.isAnnotationOpen = true;
			this.annotationOpen = oAnnotation;
		}
	};

	this.saveAnnotation = function(oAnnotation) {
		if (this.editURL.length == 0) {
			alert("No URL for edit specified.");
			return;
		}
		// retrieve annotation text from textarea
		var annotationValue = oAnnotation.childNodes[1].childNodes[0].value;
		var xAxis = oAnnotation.childNodes[0].style.left.replace(/px/i,'');
		var yAxis = oAnnotation.childNodes[0].style.top.replace(/px/i,'');
		dojo.xhrPost({
			url: this.editURL,
			handleAs: "json",
			content: {
				"annotation[clinicalNoteAnnotationId]": oAnnotation.childNodes[1].childNodes[5].value,
				"annotation[clinicalNoteId]": this.clinicalNoteId,
				"annotation[annotation]": annotationValue,
				"annotation[xAxis]": xAxis,
				"annotation[yAxis]": yAxis
			},
			load: function(data) {
				oAnnotation.childNodes[0].setAttribute("title",annotationValue);
				oAnnotation.childNodes[1].childNodes[5].value = data.clinicalNoteAnnotationId;
				oAnnotation.childNodes[1].style.zIndex = 0;
				// show the delete button
				oAnnotation.childNodes[1].childNodes[4].style.display = "";
				// hide the annotation display
				oAnnotation.childNodes[1].style.display = "none";
			},
			error: function(error) {
				alert(error);
				console.error ('Error: ', error);
			}
		});
		this.isAnnotationOpen = false;
	};

	this.cancelAnnotation = function(oAnnotation) {
		oAnnotation.childNodes[1].style.zIndex = 0;
		if (oAnnotation.childNodes[1].childNodes[4].style.display == "") {
			oAnnotation.childNodes[1].childNodes[0].value = this.annotationValue;
			oAnnotation.childNodes[1].style.display = "none";
		}
		else {
			var annotation = this.annotations.pop();
			annotation.innerHTML = "";
			annotation.style.display = "none";
		}
		this.isAnnotationOpen = false;
	};

	this.deleteAnnotation = function(oAnnotation) {
		if (this.deleteURL.length == 0) {
			alert("No URL for delete specified.");
			return;
		}
		dojo.xhrPost({
			url: this.deleteURL,
			handleAs: "json",
			content: {
				clinicalNoteAnnotationId: oAnnotation.childNodes[1].childNodes[5].value
			},
			load: function(data) {
				oAnnotation.style.zIndex = 0;
				oAnnotation.innerHTML = "";
				oAnnotation.style.display = "none";
			},
			error: function(error) {
				alert(error);
				console.error ('Error: ', error);
			}
		});
		this.isAnnotationOpen = false;
	};

	this.clearAnnotations = function() {
		if (this.isAnnotationOpen) {
			return;
		}
		// removes annotations
		var annotation = null;
		while (this.annotations.length) {
			annotation = this.annotations.pop();
			annotation.style.display = "none";
		}
	};

	this.setDefaultLines = function(lines) {
		this.defaultLines = lines;
	};

	this.loadDefaultLines = function() {
		var line = null;
		while(this.defaultLines.length) {
			line = this.defaultLines.pop();
			this.createLine(line.x1,line.x2,line.y1,line.y2);
		}
	};

	this.setDefaultAnnotations = function(annotations) {
		this.defaultAnnotations = annotations;
	};

	this.loadDefaultAnnotations = function() {
		var annotation = null;
		while(this.defaultAnnotations.length) {
			annotation = this.defaultAnnotations.pop();
			this.createAnnotation(annotation.x,annotation.y,true,annotation.value,annotation.valueId);
		}
	};

	this.getLines = function() {
		var ret = "[";
		var data = [];
		var line = null;
		for (var i in this.lines) {
			line = this.lines[i].line;
			data.push('{x1:'+line.x1+',x2:'+line.x2+',y1:'+line.y1+',y2:'+line.y2+'}');
		}
		ret += data.join(",");
		ret += "]";
		return ret;
	};

	this.setURLs = function(editURL,deleteURL) {
		this.editURL = editURL;
		this.deleteURL = deleteURL;
	};

	this.setEditURL = function(editURL) {
		this.editURL = editURL;
	};

	this.setDeleteURL  = function(deleteURL) {
		this.deleteURL  = deleteURL;
	};

	this.setClinicalNoteId = function(clinicalNoteId) {
		this.clinicalNoteId = clinicalNoteId;
	};

	this.printerFriendlyAnnotations = function() {
		if (this.isAnnotationOpen) {
			// cancel opened annotation
			this.cancelAnnotation(this.annotationOpen);
		}
		var annotation = null;
		var annotationValue = "";
		var box = null;
		this.annotationBoxes = [];
		for (var i in this.annotations) {
			annotation = this.annotations[i];
			box = annotation.childNodes[0];
			// backup first
			this.annotationBoxes[i] = {
				style:{
					width:box.style.width,
					height:box.style.height,
					overflow:box.style.overflow,
					border:box.style.border,
					zIndex:box.style.zIndex
				},
				innerHTML:box.innerHTML
			};
			annotationValue = annotation.childNodes[1].childNodes[0].value + "";
			annotationValue = annotationValue.replace(/\n/g,"<br />");
			box.style.width = "";
			box.style.height = "";
			box.style.overflow = "auto";
			box.style.border = "1px solid rgb(0,255,0)"; // bright green
			box.style.zIndex = i;
			box.innerHTML = annotationValue;
		}
	};

	this.revertPrinterFriendlyAnnotations = function() {
		if (this.isAnnotationOpen) {
			// cancel opened annotation
			this.cancelAnnotation(this.annotationOpen);
		}
		var annotation = null;
		var annotationValue = "";
		var box = null;
		var backup = null;
		for (var i in this.annotations) {
			annotation = this.annotations[i];
			box = annotation.childNodes[0];
			// restore
			backup = this.annotationBoxes[i];
			box.style.width = backup.style.width;
			box.style.height = backup.style.height;
			box.style.overflow = backup.style.overflow;
			box.style.border = backup.style.border;
			box.style.zIndex = backup.style.zIndex;
			box.innerHTML = backup.innerHTML;
		}
	};

}


var tabStateClass = function() {
	this.params = {};
};

tabStateClass.prototype.setParam = function(params) {
	if (typeof params == "object") {
		for (var key in params) {
			eval('this.params.'+key+' = params[key];');
		}
	}
};

tabStateClass.prototype.getParam = function(index) {
	var ret = null;
	if (typeof this.params == "object") {
		for (var key in this.params) {
			if (key == index) {
				ret = this.params[key];
				break;
			}
		}
	}
	return ret;
};

tabStateClass.prototype.getAllParams = function() {
	return this.params;
};





function visitSelectorClass() {
}

visitSelectorClass.prototype.oWindow = null;
visitSelectorClass.prototype.oAccordion = null;
visitSelectorClass.prototype.oVisitDetailsTabbar = null;
visitSelectorClass.prototype.cacheAddSelectVisit = null;
visitSelectorClass.prototype.accordionCacheContent = [];
visitSelectorClass.prototype.visitDetailsTabbarCacheContent = [];

visitSelectorClass.prototype.accordionAddSelectVisitId = "accordionAddSelectVisit";
visitSelectorClass.prototype.accordionVisitDetailsId = "accordionVisitDetails";
visitSelectorClass.prototype.accordionReferralVisitsId = "accordionReferralVisits";
visitSelectorClass.prototype.accordionTelemedVisitsId = "accordionTelemedVisits";
visitSelectorClass.prototype.tabVisitTypeId = "tabVisitType";
visitSelectorClass.prototype.tabDiagnosesId = "tabDiagnoses";
visitSelectorClass.prototype.tabProceduresId = "tabProcedures";
visitSelectorClass.prototype.tabVitalsId = "tabVitals";
visitSelectorClass.prototype.tabImmunizationsId = "tabImmunizations";
visitSelectorClass.prototype.tabEducationId = "tabEducation";
visitSelectorClass.prototype.tabHsaId = "tabHsa";
visitSelectorClass.prototype.tabExamsId = "tabExams";

visitSelectorClass.prototype.getWindow = function() {
	return this.oWindow;
};

visitSelectorClass.prototype.getAccordion = function() {
	return this.oAccordion;
};

visitSelectorClass.prototype.getVisitDetailsTabbar = function() {
	return this.oVisitDetailsTabbar;
};

visitSelectorClass.prototype.openWindow = function() {
	if (mainController.getActivePatient() < 1) {
		if (window.windowSelectPatient && window.visitSelector.openWindow) {
			windowSelectPatient(true,"visitSelector.openWindow()");
		}
		else {
			alert("You must select a patient before selecting a visit.");
		}
		return false;
	}
	// local variable reference to this class
	var thisClass = this;

	dhxWins.setImagePath(globalBaseUrl+"/img/");
	dhxWins.setSkin("clear_silver");
	this.oWindow = dhxWins.createWindow("windowSelectVisitId",60,10,850,800);
	this.oWindow.setText("Select Location & Visit");
	this.oWindow.centerOnScreen();

	var divAccordionId = "divAccordionContainer";
	var obj = document.createElement("DIV");
	obj.id = divAccordionId;
	obj.style.width = "100%";
	obj.style.height = "100%";
	obj.style.position = "relative";
	document.body.appendChild(obj);

	this.oWindow.attachObject(obj.id);
	this.oAccordion = new dhtmlXAccordion(obj.id);

	//this.oAccordion = this.oWindow.attachAccordion();
	this.oAccordion.setIconsPath(globalBaseUrl+"/img/");
	this.oAccordion.addItem(this.accordionAddSelectVisitId,"Add/Select Visit");
	this.oAccordion.addItem(this.accordionVisitDetailsId,"Visit Details");
	this.oAccordion.addItem(this.accordionReferralVisitsId,"Referral Visits");
	this.oAccordion.hideItem(this.accordionReferralVisitsId);
	this.oAccordion.addItem(this.accordionTelemedVisitsId,"Telemedicine Visits");
	this.oAccordion.hideItem(this.accordionTelemedVisitsId);

	this.oAccordion.attachEvent("onActive", function(id){ thisClass.accordionOpen(id); });
	var tthis = this;
	this.oAccordion.attachEvent("onBeforeActive", function(id){
		if (id == tthis.accordionVisitDetailsId && !mainController.getActiveVisit() > 0) {
			alert("No visit selected");
			return false;
		}
		return true;
	});

	this.oVisitDetailsTabbar = this.oAccordion.cells(this.accordionVisitDetailsId).attachTabbar();
	if (mainController.getActiveVisit() > 0) {
		this.oAccordion.openItem(this.accordionVisitDetailsId);
		this.accordionOpen(this.accordionVisitDetailsId);
	}
	else {
		this.oAccordion.openItem(this.accordionAddSelectVisitId);
		this.accordionOpen(this.accordionAddSelectVisitId);
	}

	this.oVisitDetailsTabbar.setImagePath(globalBaseUrl+"/img/");
	this.oVisitDetailsTabbar.setStyle("silver");
	this.oVisitDetailsTabbar.setSkinColors("#FFFFFF,#FFFACD");

	this.oVisitDetailsTabbar.addTab(this.tabVisitTypeId,"Visit Type","95");
	//this.oVisitDetailsTabbar.addTab(this.tabDiagnosesId,"Diagnoses","95");
	//this.oVisitDetailsTabbar.addTab(this.tabProceduresId,"Procedures","95");
	this.oVisitDetailsTabbar.addTab(this.tabVitalsId,"Vitals","95");
	this.oVisitDetailsTabbar.addTab(this.tabImmunizationsId,"Immunizations","95");
	this.oVisitDetailsTabbar.addTab(this.tabEducationId,"Education","95");
	this.oVisitDetailsTabbar.addTab(this.tabHsaId,"HSA","95");
	this.oVisitDetailsTabbar.addTab(this.tabExamsId,"Exams","95");

	this.oVisitDetailsTabbar.setOnSelectHandler(function(id){
		/*
		for (var i in thisClass.visitDetailsTabbarCacheContent) {
			if (thisClass.visitDetailsTabbarCacheContent[i].id == id) {
				thisClass.oVisitDetailsTabbar.setContent(id,thisClass.visitDetailsTabbarCacheContent[i].objContent);
				return true;
			}
		}
		*/
		switch(id) {
			case thisClass.tabVisitTypeId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/visit-details");
				break;
			case thisClass.tabDiagnosesId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/diagnoses");
				break;
			case thisClass.tabProceduresId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/procedures");
				break;
			case thisClass.tabVitalsId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/vitals");
				break;
			case thisClass.tabImmunizationsId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/immunizations");
				break;
			case thisClass.tabEducationId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/education");
				break;
			case thisClass.tabHsaId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/hsa");
				break;
			case thisClass.tabExamsId:
				thisClass.visitDetailsTabbarGetContent(id,globalBaseUrl+"/visit-select.raw/exams");
				break;
			default:
				alert("You selected a tab " + id + " that does not exist");
		}
		return true;
	});

	this.oVisitDetailsTabbar.setTabActive(this.tabVisitTypeId);
};

visitSelectorClass.prototype.accordionOpen = function(id) {
	switch (id) {
		case this.accordionAddSelectVisitId:
			this.accordionGetContent(id,globalBaseUrl+"/visit-select.raw/index?personId="+mainController.getActivePatient()+"&visitId="+mainController.getActiveVisit());
			break;
		case this.accordionReferralVisitsId:
			break;
		case this.accordionTelemedVisitsId:
			break;
	}
};

visitSelectorClass.prototype.visitDetailsTabbarGetContent = function(id,url) {
	var thisClass = this;
	this.attachContent(id,url,function(obj){
			thisClass.oVisitDetailsTabbar.setContent(id,obj);
			thisClass.visitDetailsTabbarCacheContent[thisClass.visitDetailsTabbarCacheContent.length] = {id:id,objContent:obj};
		});
};

visitSelectorClass.prototype.accordionGetContent = function(id,url) {
	var thisClass = this;
	this.attachContent(id,url,function(obj){
			thisClass.oAccordion.cells(id).attachObject(obj);
			thisClass.accordionCacheContent[thisClass.accordionCacheContent.length] = {id:id,objContent:obj};
		});
};

visitSelectorClass.prototype.attachContent = function(id,url,callback) {
	var thisClass = this;
	var newDiv = document.createElement('div');
	newDiv.setAttribute("id",id+"Container");
	dojo.xhrGet({
		url: url,
		handleAs: "text",
		load: function(data,ioArgs) {
			dojo.setInnerHTML(newDiv,data);
			if (typeof callback == "function") {
				callback(newDiv);
			}
			return data;
		},
		error: function(response, ioArgs) {
			console.error("HTTP status code: ", ioArgs.xhr.status);
			return response;
		}
	});
};

visitSelectorClass.prototype.closeWindow = function() {
	this.oWindow.close();
};

function teamSelectorClass() {
}

teamSelectorClass.prototype.oWindow = null;

teamSelectorClass.prototype.getWindow = function() {
	return this.oWindow;
};

teamSelectorClass.prototype.openWindow = function() {
	if (mainController.getActivePatient() < 1) {
		alert('You must select a patient before selecting a care team.');
		return false;
	}
	// local variable reference to this class
	var thisClass = this;

	dhxWins.setImagePath(globalBaseUrl+"/img/");
	dhxWins.setSkin("clear_silver");
	this.oWindow = dhxWins.createWindow("windowSelectTeamId",60,10,500,400);
	this.oWindow.setText("Select Team");
	this.oWindow.attachURL(globalBaseUrl+"/team-manager.raw/select?patientId="+mainController.getActivePatient(),true);
	this.oWindow.centerOnScreen();

};

teamSelectorClass.prototype.setWindowDimension = function(width,height) {
	this.oWindow.setDimension(width,height);
};

teamSelectorClass.prototype.closeWindow = function() {
	this.oWindow.close();
};



function drugScheduleClass() {
}

drugScheduleClass.prototype._computeXID = function(quantity,divisor) {
	quantity = parseInt(quantity);
	divisor = parseInt(divisor);
	var ret = quantity / divisor;
	if (quantity % divisor != 0) {
		ret++;
	}
	return ret;
};

drugScheduleClass.prototype.BID = function(quantity) {
	return this._computeXID(quantity,2);
};

drugScheduleClass.prototype.TID = function(quantity) {
	return this._computeXID(quantity,3);
};

drugScheduleClass.prototype.MOWEFR = function(quantity) {
	quantity = parseInt(quantity);
	return quantity;
};

drugScheduleClass.prototype.NOW = function(quantity) {
	quantity = parseInt(quantity);
	return quantity;
};

drugScheduleClass.prototype.ONCE = function(quantity) {
	quantity = parseInt(quantity);
	return quantity;
};

drugScheduleClass.prototype._computeQXH = function(quantity,multiplier) {
	quantity = parseInt(quantity);
	multiplier = parseInt(multiplier);
	quantity *= multiplier;
	return this._computeXID(quantity,24);
};

drugScheduleClass.prototype.Q12H = function(quantity) {
	return this._computeQXH(quantity,12);
};

drugScheduleClass.prototype.Q24H = function(quantity) {
	return this._computeQXH(quantity,24);
};

drugScheduleClass.prototype.Q2H = function(quantity) {
	return this._computeQXH(quantity,2);
};

drugScheduleClass.prototype.Q3H = function(quantity) {
	return this._computeQXH(quantity,3);
};

drugScheduleClass.prototype.Q4H = function(quantity) {
	return this._computeQXH(quantity,4);
};

drugScheduleClass.prototype.Q6H = function(quantity) {
	return this._computeQXH(quantity,6);
};

drugScheduleClass.prototype.Q8H = function(quantity) {
	return this._computeQXH(quantity,8);
};

drugScheduleClass.prototype.Q5MIN = function(quantity) {
	quantity = parseInt(quantity);
	quantity *= 5;
	return this._computeXID(quantity,1440);
};

drugScheduleClass.prototype.QDAY = function(quantity) {
	quantity = parseInt(quantity);
	return quantity;
};

drugScheduleClass.prototype.getDaysSupply = function(quantity,schedule) {
	quantity = parseInt(quantity);
	switch (schedule) {
		case "BID":
			return this.BID(quantity);
			break;
		case "TID":
			return this.TID(quantity);
			break;
		case "MOWEFR":
			return this.MOWEFR(quantity);
			break;
		case "NOW":
			return this.NOW(quantity);
			break;
		case "ONCE":
			return this.ONCE(quantity);
			break;
		case "Q12H":
			return this.Q12H(quantity);
			break;
		case "Q24H":
			return this.Q24H(quantity);
			break;
		case "Q2H":
			return this.Q2H(quantity);
			break;
		case "Q3H":
			return this.Q3H(quantity);
			break;
		case "Q4H":
			return this.Q4H(quantity);
			break;
		case "Q6H":
			return this.Q6H(quantity);
			break;
		case "Q8H":
			return this.Q8H(quantity);
			break;
		case "Q5MIN":
			return this.Q5MIN(quantity);
			break;
		case "QDAY":
			return this.QDAY(quantity);
			break;
		default:
			return quantity;
	}
}

drugScheduleClass.prototype.getDosage = function(quantity,daysSupply,schedule) {
	quantity = parseInt(quantity);
	daysSupply = parseInt(daysSupply);
	var ret = quantity / daysSupply;
	switch (schedule) {
		case "BID":
		case "Q12H":
			ret /= 2;
			break;
		case "TID":
		case "Q8H":
			ret /= 3;
			break;
		case "MOWEFR":
		case "NOW":
		case "ONCE":
		case "Q24H":
		case "QDAY":
			break;
		case "Q2H":
			ret /= 12;
			break;
		case "Q3H":
			ret /= 8;
			break;
		case "Q4H":
			ret /= 6;
			break;
		case "Q6H":
			ret /= 4;
			break;
		default:
	}
	if (isNaN(ret)) {
		ret = 1;
	}
	return ret;
}

function globalNormalizedName(name) {
	name += "";
	var ret = name.replace(/^(.)|\s(.)/g,function($1){
		return $1.toUpperCase();
	});
	ret = ret.replace(/\ /g,"");
	var nonAlphaNum=/[^0-9,a-z,A-Z]/gi;
	ret = ret.replace(nonAlphaNum,"_");
	ret = ret.replace(/^\d(.)/g,function($1){
		return "_"+$1;
	});
	return ret;
}
