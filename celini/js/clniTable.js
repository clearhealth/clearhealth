function clniTableObj(tableId, showLinkId,modifier,labels) {
	this._table = document.getElementById(tableId);
	this._showLink = document.getElementById(showLinkId);
	this._cookieName = this._table.id+'_display';
	this._modifier = modifier;
	this._labels = labels;
	for(a=0;a<this._labels.length;a++) {
		c = clniCookie.getCookie(tableId+a+this._labels[a]);
		if(c != 0 && c != false && this._labels[a] == c) {
			this.hideCol(a,c);
		}
	}
}
clniTableObj.prototype = {
	_hiddenColumns: new Array(),
	_name:"tableObj",
	_modifier:1,
	
	hideCol:function(colIndex) {
		label = this._labels[colIndex];
		for (i = 0; i < this._table.rows.length; i++) {
			if(this._table.rows[i].getElementsByTagName('th') && this._table.rows[i].getElementsByTagName('th').item(colIndex+this._modifier)) {
				this._table.rows[i].getElementsByTagName('th').item(colIndex+this._modifier).style.display = "none";
			}
			if(this._table.rows[i].getElementsByTagName('td') && this._table.rows[i].getElementsByTagName('td').item(colIndex+this._modifier)) {
				this._table.rows[i].getElementsByTagName('td').item(colIndex+this._modifier).style.display = "none";
			}
		}
		this.addShowLink(colIndex);
		clniCookie.setCookie(this._table.id+colIndex+label,label);
	},
	
	showCol:function(col, showLink) {
		label = this._labels[col];
		for (i = 0; i < this._table.rows.length; i++) {
			if(this._table.rows[i].getElementsByTagName('th') && this._table.rows[i].getElementsByTagName('th').item(col+this._modifier)) {
				this._table.rows[i].getElementsByTagName('th').item(col+this._modifier).style.display="";
			}
			if(this._table.rows[i].getElementsByTagName('td') && this._table.rows[i].getElementsByTagName('td').item(col+this._modifier)) {
				this._table.rows[i].getElementsByTagName('td').item(col+this._modifier).style.display="";
			}
		}
		this._showLink.removeChild(showLink.parentNode);
		clniCookie.setCookie(this._table.id+col+label,0);
	},
	
	addShowLink:function(col) {
		label = this._labels[col];
		var newLI = document.createElement('li');
		var showLinkA = document.createElement('a');
		var self = this;
		showLinkA.onclick = function() { self.showCol(col, showLinkA, self._modifier,label); };
		showLinkA.innerHTML = 'Show ' + label;
		showLinkA.href = 'javascript:void(0);';
		newLI.appendChild(showLinkA);
		this._showLink.appendChild(newLI);
	}
}
