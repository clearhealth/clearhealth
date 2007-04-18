// a simple javascript scrollbar widget
// Author, Joshua Eichorn <josh@bluga.net>
// Copyright Joshua Eichorn 2005

function ScrollBar(options) {
	for(var i in options) {
		this[i] = options[i];
	}

	var self = this;
	setTimeout(function() { self.init(); },500);
}

ScrollBar.prototype = {
	id: false,
	currentElement: 0,
	minThumbSize: 30,
	trackSize: 0,
	thumbSize: 0,
	topOffset: 0,
	slider: false,
	scrollTimer: false,
	scrollValue: 0,
	onScrollTimer: false,
	totalElements: 0,
	maxScroll: 0,
	// override this callback to connect the scrollbar to something else
	onScroll: function(to) {
	},
	init: function() {
		var barHTML = '<div class="buttonUp"><img src="'+this.imagePath+'/scrollbar/scrollUp.png" width="21" height="19"></div>'+
			'<div id="'+this.trackId+'" class="track">'+
				'<div class="bar" id="'+this.thumbId+'"><img src="'+this.imagePath+'/scrollbar/bar.png" width="21" height="100%">'+
					'<div class="capTop"><img src="'+this.imagePath+'/scrollbar/capTop.png" width="21" height"2"></div>'+
					'<div class="gripper"><img src="'+this.imagePath+'/scrollbar/gripper.png" width="21" height"8"></div>'+
					'<div class="capBottom"><img src="'+this.imagePath+'/scrollbar/capBottom.png" width="21" height"2"></div>'+
				'</div>'+
			'</div>'+
			'<div class="buttonDown"><img src="'+this.imagePath+'/scrollbar/scrollDown.png" width="21" height="19"></div>'+
			'<div class="barStatus"></div>';
		this.element = document.getElementById(this.id);
		
		this.element.innerHTML = barHTML;
		this.positionBar(true);

		// scrolling code scrolls down to the top of the element not the bottom so now that we've calulated the thumbSize we need 
		// to subtract that from the trackSize
		this.setTrackSize(this.trackSize - this.thumbSize);

		var divs = this.element.getElementsByTagName('div');
		for(var i = 0; i < divs.length; i++) {
			this[divs[i].className] = divs[i];
		}

		var self = this;
		// button up click event handling
		HTML_AJAX_Util.registerEvent(this.buttonUp,'click',	function() { self._onButtonClick(-1); });
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mousedown',	function() { self._onButtonMouseDown(-1); });
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mouseup',	function() { self._onButtonMouseUp(); });

		if (!document.all) {
		// hover
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mouseover',	function() { self._hover(self.buttonUp.firstChild,'scrollUp','hover'); });
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mouseout',	function() { self._hoverOut(self.buttonUp.firstChild,'scrollUp'); });

		// active
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mousedown',	function() { self._hover(self.buttonUp.firstChild,'scrollUp','active'); });
		HTML_AJAX_Util.registerEvent(this.buttonUp,'mouseup',	function() { self._hoverOut(self.buttonUp.firstChild,'scrollUp'); });

		// onclick
		HTML_AJAX_Util.registerEvent(this.buttonDown,'click',	function() { self._onButtonClick(1); });
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mousedown',function() { self._onButtonMouseDown(1); });
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mouseup',	function() { self._onButtonMouseUp(); });

		// hover
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mouseover',function() { self._hover(self.buttonDown.firstChild,'scrollDown','hover'); });
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mouseout' ,function() { self._hoverOut(self.buttonDown.firstChild,'scrollDown'); });

		// active
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mousedown',function() { self._hover(self.buttonDown.firstChild,'scrollDown','active'); });
		HTML_AJAX_Util.registerEvent(this.buttonDown,'mouseup' ,function() { self._hoverOut(self.buttonDown.firstChild,'scrollDown'); });

		// bar
		// hover
		HTML_AJAX_Util.registerEvent(this.bar,'mouseover',function() { self._hover(self.gripper.firstChild,'gripper','hover'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mouseover',function() { self._hover(self.bar.firstChild,'bar','hover'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mouseout' ,function() { self._hoverOut(self.gripper.firstChild,'gripper'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mouseout' ,function() { self._hoverOut(self.bar.firstChild,'bar'); });

		// active
		HTML_AJAX_Util.registerEvent(this.bar,'mousedown',function() { self._hover(self.gripper.firstChild,'gripper','active'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mousedown',function() { self._hover(self.bar.firstChild,'bar','active'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mouseup' ,function() { self._hoverOut(self.gripper.firstChild,'gripper'); });
		HTML_AJAX_Util.registerEvent(this.bar,'mouseup' ,function() { self._hoverOut(self.bar.firstChild,'bar'); });
		}


		// make it a slider
		this.slider = new Control.Slider(this.thumbId,this.trackId,{axis:'vertical',
			onSlide:function(v){self._onSlide(v)},
			onChange:function(v){self._scrollTo(v)}});


	},
	positionBar: function(first) {
		var value = this.slider.value;
		this.element.style.marginTop = this.topOffset +"px";

		var table = this.element.parentNode.parentNode.getElementsByTagName('table').item(0);

		var newHeight = Math.max((19+19+this.minThumbSize),(table.clientHeight-this.topOffset));
		this.element.style.height = newHeight +"px";

		if (first) {
			this.setTrackSize(this.element.clientHeight - 19 - 19); // total slider length - buttons on end
		}
		else {
			this.setTrackSize(this.element.clientHeight - 19 - 19-this.minThumbSize); // total slider length - buttons on end
		}

		this.setThumbSize(Math.max(this.minThumbSize, this.trackSize * (this.visibleElements/this.totalElements)));
		if (this.slider) {
			this.slider.trackLength = this.slider.maximumOffset() - this.slider.minimumOffset();
			this.slider.setValue(value);
		}
	},
	setTrackSize: function(size) {
		this.trackSize = size;
		document.getElementById(this.trackId).style.height = this.trackSize+"px"; 
	},
	setThumbSize:  function(size) {
		this.thumbSize = size;
		document.getElementById(this.thumbId).style.height = this.thumbSize+"px";
	},
	realFromSlide: function(value) {
		var val = Math.round(this.totalElements*value);
		if (val > this.maxScroll) {
			val = this.maxScroll;
		}
		return val;
	},
	// internal event handlers
	_hover: function(image,file,type) {
		image.src = this.imagePath+"/scrollbar/"+file+'_'+type+'.png';
	},
	_hoverOut: function(image,file) {
		image.src = this.imagePath+"/scrollbar/"+file+'.png';
	},
	_onButtonClick: function(dir) {
		this.currentElement += dir;
		this.slider.setValue(this.currentElement/this.totalElements);
	},
	_onSlide: function(value) {
		this.barStatus.innerHTML = (this.realFromSlide(value)+1) +' of '+this.totalElements;
	},
	_scrollTo: function(value) {
		this.barStatus.innerHTML = (this.realFromSlide(value)+1) +' of '+this.totalElements;
		this.currentElement = this.realFromSlide(value);

		var self = this;
		if (this.onScrollTimer) {
			window.clearTimeout(this.onScrollTimer);
		}
		this.onScrollTimer = window.setTimeout(function() { self.onScrollTimer = false; self.onScroll(self.realFromSlide(value)); }, 400);
	},
	_onButtonMouseDown: function(dir) {
		if (this.scrollTimer) {
			// i don't think this case can ever happen but lets be safe
			window.clearInterval(this.scrollTimer);
		}
		var self = this;
		this.scrollTimer = window.setInterval(function() { self._buttonScroll(dir); }, 200);
	},
	_onButtonMouseUp: function() {
		window.clearInterval(this.scrollTimer);
		this.scrollValue = 0;
	},
	_buttonScroll: function(dir) {
		this.currentElement += dir * this.scrollValue++;
		this.slider.setValue(this.currentElement/this.totalElements);
	}
}
