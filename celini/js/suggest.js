// AJAX SUGGEST
function clniSuggest(rowTemplate,textId,valueId,className,methodName) {
	if (!HTML_AJAX.queues['suggest']) {
		HTML_AJAX.queues['suggest'] = new HTML_AJAX_Queue_Interval_SingleBuffer(350,true);
	}

	this.rowTemplate = rowTemplate;
	this.textId = textId;
	this.valueId = valueId;
	this.className = className;
	this.methodName = methodName;

	var self = this;
	HTML_AJAX_Util.registerEvent(document.getElementById(textId),'keyup',function(e) { self._onChange(e) });
	HTML_AJAX_Util.registerEvent(document.getElementById(textId),'keydown',function(e) { self._onKeyDown(e) });
	HTML_AJAX_Util.registerEvent(document.getElementById(textId),'keypress',function(e) { self._onKeyPress(e) });
	HTML_AJAX_Util.registerEvent(document.getElementById(textId),'blur',function(e) { self._onTextBlur() });
}

clniSuggest.prototype = {
	highlight: true,
	rowTemplate:false,
	textId: false,
	valueId: false,
	className: false,
	methodName: false,

	onSelect: false,
	preRequest: false,
	selectedValue: false,
	params: false,
	allowBreaks: true,

	_length: 0,
	_selectedIndex: 0,
	_searchString: '',
	_highlightIndex: 0,
	_lastString: '',
	_dropDown: false,
	_effectHighlight: false,
	_noEvent: false,
	
	suggest: function() {
		this._searchString = this._getSearchString();

		if (this.preRequest) {
			this.preRequest(this);
		}

		if (this._searchString != this._lastString) {
			var self = this;
			HTML_AJAX.fullcall(
				HTML_AJAX.defaultServerUrl,
				'JSON',
				this.className,
				this.methodName,
				function(result) { self._suggestCallback(result); },
				this.getRemoteCallParams(),
				{queue: 'suggest'});
		}

		this._lastString = this._searchString;
	},

	// if you need to pass anything besides the search string to the backend override this method
	// or add it to params and it will be appended to the search string
	getRemoteCallParams: function() {
		if (this.params) {
			var params = this.params;
			params.unshift(this._searchString);
			return params;
		}
		else {
			return [this._searchString];
		}
	},

	// override this method if you want to run extra code on item selection
	onSelect: function(item) {
	},

	_suggestCallback: function(result) {
		this._setupDropdown();
		if (result.length == 0) {
			this._dropdown.style.display = 'none';
			return;
		}

		this._dropdown.style.display = 'block';
		var self = this;
		this._length = result.length;

		var re = new RegExp('('+this._searchString+')', 'i');
		for(var i=0; i<result.length; i++){
			var item = document.createElement("div");
			var data = result[i];
			item.data = data;
			item.index = i;
			
			item.value = this.rowTemplate.replace(/{\$([a-zA-Z0-9_]+)}/g,function(s,result) {return data[result]; } );
			if (this.highlight) {
				if (item.value.charAt(0) == '*') {
				item.innerHTML = "<SPAN CLASS='SuggestMatchingText'>" + item.value + "</SPAN>";
				}
				else {
				item.innerHTML = item.value.replace(re,"<SPAN CLASS='SuggestMatchingText'>$1</SPAN>");
				}
			}
			else {
				if (item.value.charAt(0) == '*') {
				item.innerHTML = "<SPAN CLASS='SuggestMatchingText'>" + item.value + "</SPAN>";
				}
				else {
				item.innerHTML = item.value;
				}
			}

			HTML_AJAX_Util.registerEvent(item,'mousedown',function(e) { self._onSelect(HTML_AJAX_Util.eventTarget(e)) });
			HTML_AJAX_Util.registerEvent(item,'mouseover',function(e) { self._onOver(HTML_AJAX_Util.eventTarget(e)) });
			HTML_AJAX_Util.registerEvent(item,'mouseout',function(e) { self._onOut(HTML_AJAX_Util.eventTarget(e)) });

			this._dropdown.appendChild(item);
			if (result.length == 1) {
				this._onSelect(item);
			}
			else {
				this._onOver(this._dropdown.childNodes[0]);
			}
		}
	},
	
	_onTextBlur: function() {
		if (this._dropdown) {
			this._dropdown.style.display = 'none';
		}
	},

	_getSearchString: function() {
		return document.getElementById(this.textId).value;
	},

	_setupDropdown: function() {
		if (!this._dropdown) {
			c = document.createElement('div');
			c.className = "SuggestContainer";
			document.getElementById(this.textId).parentNode.insertBefore(c,document.getElementById(this.textId).nextSibling);

			this._dropdown = document.createElement('div');
			this._dropdown.className = 'SuggestDropdown';
			if(this.allowBreaks == false) {
				this._dropdown.style.whiteSpace='nowrap';
			}
			c.appendChild(this._dropdown);
		}
		this._dropdown.innerHTML = '';
	},
	
	_onChange: function(evt) {
		if (!this._noEvent) {
			this.suggest();
		}
	},

	_onOut: function(item) {
		if (item.className == 'SuggestMatchingText') {
                item = item.parentNode;
                }
		item.className = '';
	},

	_onKeyPress: function(evt) {
		if(window.event) var evt = window.event;

		if (this._length == 0) {
			return;
		}

		// hit escape
		if (evt.keyCode == 27) {
			this._dropdown.style.display = 'none';
			return;
		}

		// select with entry
		if ((evt.keyCode == 13 || evt.keyCode == 9) && this._length > 1){
			if(this._length > 0){
				this._onSelect(this._dropdown.childNodes[this._highlightIndex]);
			}

			evt.returnValue = false;
			if(evt.preventDefault) evt.preventDefault();
		}
	},

	_onKeyDown: function(evt) {
		if(window.event) var evt = window.event;

		var update = false;
		var hi = this._highlightIndex;
		//up key
		if (evt.keyCode == 38) {
			if (this._highlightIndex > 0) {
				update = true;
				this._highlightIndex--;
			}
		}
		// down key
		if (evt.keyCode == 40) {
			if (this._highlightIndex < this._length) {
				update = true;
				this._highlightIndex++;
			}
		}
		// enter key
		if (evt.keyCode == 13) {
			this._onSelect(HTML_AJAX_Util.eventTarget(evt));
		}

		if (update) {
			this._onOut(this._dropdown.childNodes[hi]);
			this._onOver(this._dropdown.childNodes[this._highlightIndex]);
		}
	},

	_onOver: function(item) {
		if (item.className == 'SuggestMatchingText') {
		item = item.parentNode;
		}
		item.className = 'SuggestHighlight';
		this._highlightIndex = item.index;
	},


	_onSelect: function(item) {
		if (item.className == 'SuggestMatchingText') {
			item = item.parentNode;
		}
		this._selectedIndex = item.index;
		this.selectedValue = item.data;

		this._noEvent = true;
		document.getElementById(this.valueId).value = item.data.id;
		document.getElementById(this.textId).value = item.value;
		this._noEvent = false;
		this._dropdown.style.display = 'none';
		if (this._effectHighlight && this._effectHighlight.state == 'finished') {
			this._effectHighlight = new Effect.Highlight(document.getElementById(this.textId));
		}

		if (this.onSelect) {
			this.onSelect(item);
		}
	}
}


