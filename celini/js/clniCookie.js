var clniCookie = {
	setCookie: function(name, value, expires) {
		document.cookie = name + "=" + escape(value) +
			((expires) ? "; expires=" + expires.toGMTString() : "");
	},
	getCookie: function(name) {
		var docCookie = document.cookie.split("; ");
		for (var i=0; i < docCookie.length; i++){
			var piece = docCookie[i].split("=");
			if (piece[0] == name) {
				return unescape(String(piece[1]).replace(/\+/g, " "));
			}
		}
		return false;
	}
}
