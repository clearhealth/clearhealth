/**
 * Used to create a DHTML pop-up.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
function clniPopup(input, isID) {
	this._input = (isID == undefined || isID == true) ? document.getElementById(input).innerHTML : input
}

clniPopup.prototype = {
	/**
	 * The name of the class to be assigned to the newly created popup
	 *
	 * @var string
	 */
	className: 'confirmBox',

	draggable: false,
	useElement: false,
	draggableOptions: {},
	modal: false,
	
	/**#@+
	 * @access private
	 */
	_popup: '',
	_prepared: false,
	_selects: [],
	_coordinates: [],
	_draggable: false,
	_back: false,
	/**#@-*/
	
	/**
	 * Shows the popup
	 */
	display: function() {
		this.prepare();
		if (this._back) {
			this._back.style.display = "block";
		}

		this._centerDisplay();
		this._cleanUp('hidden');
		this._popup.style.visibility = "visible";
		this._popup.style.display = "block";
	},
	
	// do everything but the final display portions
	prepare: function() {
		if (this._prepared) {
			return;
		}

		if (this.useElement) {
			var el = document.getElementById(this.useElement);
		}
		else {
			var el = document.createElement('div');
			el.innerHTML = this._input;
		}


		el.className = this.className;
		el.style.position = 'absolute';
		el.style.zIndex = 100000;
		this._popup = el;

		if (this.draggable) {
			this._draggable = new Draggable(el,this.draggableOptions);
		}

		if (this.modal) {
			this._back = document.createElement('div');
			this._back.style.position = 'absolute';
			this._back.style.top = 0;
			this._back.style.left = 0;
			this._back.style.width = '100%';
			this._back.style.zIndex = 99999;

			var h = document.body.offsetHeight;
			if (h < 500) {
				h = 2000;
			}
			this._back.style.height = h+'px';
			this._back.style.display = 'none';
			//this._back.style.backgroundColor = 'gray';

			// this should be a better check at some point

			var imgUrl = '../../Images/75p_white.png';
			if (base_dir) {
				imgUrl = base_dir+'index.php/Images/75p_white.png';	
			}
			if (document.all) {
				this._back.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=scale src='"+imgUrl+"');"
			}
			else {
				this._back.style.background = 'url('+imgUrl+')'; 
			}

			document.body.insertBefore(this._back, document.body.lastChild);
		}
		if (!this.useElement) {
			document.body.insertBefore(el, document.body.lastChild);
		}
		this._prepared = true;
	},

	// these funcs should go in a general util lib, maybe in HTML_AJAX
	posLeft: function(obj) {
		var curleft = 0;
		if (obj.offsetParent)
		{
			while (obj.offsetParent)
			{
				curleft += obj.offsetLeft
				obj = obj.offsetParent;
			}
		}
		else if (obj.x)
			curleft += obj.x;
		return curleft;
	},

	posTop: function(obj) {
		var curtop = 0;
		if (obj.offsetParent)
		{
			while (obj.offsetParent)
			{
				curtop += obj.offsetTop
				obj = obj.offsetParent;
			}
		}
		else if (obj.y)
			curtop += obj.y;
		return curtop;
	},
		
	/**
	 * Hides the popup from view
	 */
	hide: function() {
		for(var i = 0; i < this._selects.length; i++) {
			this._selects[i].style.visibility = 'visible';
		}
		delete this._selects;
		this._popup.style.display = 'none';
		if (this._back) {
			this._back.style.display = "none";
		}
		this._cleanUp('visible');
	},
	
	/**
	 * Completely removes the popup from the DOM
	 */
	remove: function() {
		document.body.removeChild(this._popup);
		if (this._back) {
			document.body.removeChild(this._back);
		}
		this._prepared = false;
		this._cleanUp('visible');
	},
	
	/**#@+
	 * @access private
	 */
	
	/**
	 * Handle centering the display of the popup
	 */
	_centerDisplay: function() {
		var cords = clniUtil.centerElement(this._popup);

		this._coordinates['top'] = cords.top;
		this._coordinates['left'] = cords.left;
		
		this._popup.style.top = this._coordinates['top'] + 'px';
		this._popup.style.left = this._coordinates['left'] + 'px';
		this._popup.style.visibility = 'visible';

		this._coordinates['right'] = this._coordinates['left'] + this._popup.clientWidth;
		this._coordinates['bottom'] = this._coordinates['top'] + this._popup.clientHeight;
	},

	/**
	 * Handle any clean-up that should be preformed prior to displaying
	 */
	_cleanUp: function(type) {
		// if were on IE hide any selects in the way
		if (document.all) {
			var psels = this._popup.getElementsByTagName('select');

			this._selects = new Array();
			var selects = document.getElementsByTagName('select');
			for(var i = 0; i < selects.length; i++) {
				var sLeft = this.posLeft(selects[i]);
				var sTop = this.posTop(selects[i]);
				var sRight = sLeft + selects[i].clientWidth;
				var sBottom = sTop + selects[i].clientHeight;

				if ( 
					((sLeft > this._coordinates['left'] && sLeft < this._coordinates['right']) ||
					 (sRight > this._coordinates['left'] && sLeft < this._coordinates['right'] )) &&
					((sTop > this._coordinates['top'] && sTop < this._coordinates['bottom']) ||
					 (sBottom > this._coordinates['top'] && sTop < this._coordinates['bottom']))
				 ) {
					var inside = false;
					for(var p = 0; p < psels.length; p++) {
						if (psels[p] == selects[i]) {
							inside = true;
							break;
						}
					}
					if (!inside) {
						selects[i].style.visibility = type;
						this._selects.push(selects[i]);
					}
				}
				
			}
		}	
	}
}
