var clniUtil = {
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

	mouseXY: function(e) {
		var posx = 0;
		var posy = 0;
		if (!e) var e = window.event;
		if (e.pageX || e.pageY)
		{
			posx = e.pageX;
			posy = e.pageY;
		}
		else if (e.clientX || e.clientY)
		{
			posx = e.clientX + document.body.scrollLeft;
			posy = e.clientY + document.body.scrollTop;
		}
		// posx and posy contain the mouse position relative to the document
		// Do something with this information

		return {x:posx,y:posy};
	},
	/**
	 * Use recursion to find the parent of a specific tag for a given element
	 */
	findParentOfTagName: function(element,tagName) {
		if (element.parentNode.nodeName == tagName.toUpperCase()) {
			return element.parentNode;
		}
		else {
			return clniUtil.findParentOfTagName(element.parentNode,tagName);
		}
	},
	centerElement: function(element) {
		var myWidth = 0, myHeight = 0;
		if( typeof( window.innerWidth ) == 'number' ) {
			//Non-IE
			myWidth = window.innerWidth;
			myHeight = window.innerHeight;
		} else if( document.documentElement &&
			( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
			//IE 6+ in 'standards compliant mode'
			myWidth = document.documentElement.clientWidth;
			myHeight = document.documentElement.clientHeight;
		} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
			//IE 4 compatible
			myWidth = document.body.clientWidth;
			myHeight = document.body.clientHeight;
		}


		var t = ((myHeight - element.clientHeight) / 2);
		var l = ((myWidth - element.clientWidth) / 2);

		var scrollTop = (document.documentElement.scrollTop ?  document.documentElement.scrollTop : document.body.scrollTop);
		t = t+scrollTop;

		var scrollLeft = (document.documentElement.scrollLeft ?  document.documentElement.scrollLeft : document.body.scrollLeft);
		l = l+scrollLeft;

		element.style.top = t+'px';
		element.style.left = l+'px';

		return {top:t,left:l};
	},
	viewportSize: function() {
		var x,y;
		if (self.innerHeight) {
			// all except Explorer
			x = self.innerWidth;
			y = self.innerHeight;
		}
		else if (document.documentElement && document.documentElement.clientHeight) {
			// Explorer 6 Strict Mode
			x = document.documentElement.clientWidth;
			y = document.documentElement.clientHeight;
		}   
		else if (document.body) {
			// other Explorers
			x = document.body.clientWidth;
			y = document.body.clientHeight;
		}
		return {'x':x,'y':y};
	}
};
