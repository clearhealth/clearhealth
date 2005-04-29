function AutoComplete(getDataFunc, textId, numId, divId, highlight, timeout){
	// initialize member variables
	this.getDataFunc = getDataFunc;
	this.oText = document.getElementById(textId);
	this.oIdText = document.getElementById(numId);
	this.oDiv = document.getElementById(divId);
	this.index = 0;
	this.choiceCount = 0;
	this.choices;
	this.searchString = '';
	this.textFieldId = textId;
	this.textNumId = numId;
	this.divId = divId;
	this.timerSet = false;
	this.itemSet = false;
	this.highlight = highlight;
	this.timeout = timeout;
	this.lastKeyTime = 0;

	this.oText.AutoComplete = this;
	this.oText.onblur = AutoComplete.prototype.onTextBlur;
	this.oText.onkeyup = AutoComplete.prototype.onKeyUp;
	this.oText.onkeydown = AutoComplete.prototype.onKeyDown;
}

AutoComplete.prototype.getTopPos = function(){
	// Calculate top position
	top_pos = 0;
	var obj = this.oText;
	while(obj){
		top_pos += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return top_pos;
}

AutoComplete.prototype.getLeftPos = function(){
	// Calculate left position
	pos = 0;
	var obj = this.oText;
	while(obj){
		pos += obj.offsetLeft;
		obj = obj.offsetParent;
	}
	return pos;
}

AutoComplete.prototype.draw = function(){
	// clear the popup-div.
	this.clear();
	if(this.choices.length){
		this.choiceCount = this.choices.length;
	}else{
		this.choiceCount = 0;
	}


	if(this.choices.length == 1){
		this.oText.value = this.choices[0]['name']+ ' #' + this.choices[0]['pubpid'];
		this.oIdText.value = this.choices[0]['id'];
		this.oText.focus();
		this.oText.select();
	}else if(this.choices.length > 1){
		// Position the div
		this.oDiv.style.position='absolute';
		//this.oDiv.style.top = eval(this.getTopPos() + 20) + "px";
		//this.oDiv.style.left = this.getLeftPos() + "px";

		// add each string to the popup-div
		var i, n = this.choices.length;
		//alert('I know about ' + n + ' strings');
		for ( i = 0; i < n; i++ ){
			var oDiv = document.createElement('div');
			this.oDiv.appendChild(oDiv);

			if(this.highlight){
				// Highlight the matching text
				var re = new RegExp('('+this.searchString+')', 'ig');
				var cstr = this.choices[i]['name'] + ' ' + this.choices[i]['DOB'] + ' ' + this.choices[i]['pubpid'] + ' ' + this.choices[i]['ss'];
				var newstr = cstr.replace(re, "<SPAN CLASS=AutoCompleteMatchingText>$1</SPAN>");

				//alert(this.searchString + '\n\n' + newstr);
				oDiv.innerHTML = newstr;
			}else{
				oDiv.innerHTML = this.choices[i]['name'];
			}
			if(i == this.index)
				oDiv.className = "AutoCompleteHighlight";
			oDiv.onmousedown = AutoComplete.prototype.onDivMouseDown;
			oDiv.onmouseover = AutoComplete.prototype.onDivMouseOver;
			oDiv.onmouseout = AutoComplete.prototype.onDivMouseOut;
			oDiv.AutoComplete = this;
		}
		this.oDiv.style.visibility = "visible";
	}

}

AutoComplete.prototype.onTextBlur = function(){
	this.AutoComplete.onblur();
}

AutoComplete.prototype.onblur = function(){
	this.oDiv.style.visibility = "hidden";
}

AutoComplete.prototype.onKeyUp = function(evt){
	if(window.event) evt = window.event;
	//alert(event.keyCode);
	if(evt.keyCode == 40){
		if(this.AutoComplete.index + 1 < this.AutoComplete.choiceCount){
			this.AutoComplete.index += 1;
			this.AutoComplete.draw();
		}
		this.AutoComplete.dray
	}else if(evt.keyCode == 38){
		if(this.AutoComplete.index > 0){
			this.AutoComplete.index -= 1;
			this.AutoComplete.draw();
		}
	}else{
		if(typeof(this.lastKeyTime) == 'undefined' || (evt.timeStamp - this.lastKeyTime) < 1400) {
        	//do nothing as the user is typing speedily
        	this.lastKeyTime = evt.timeStamp;
        	clearTimeout(this.timerid);
            this.timerid = setTimeout('document.getElementById(\''+this.AutoComplete.textFieldId+'\').AutoComplete.getData()', this.AutoComplete.timeout);
		}
        else {
        	this.lastKeyTime = evt.timeStamp;
        	clearTimeout(this.timerid);
        	this.AutoComplete.getData();
        }
    }
}

AutoComplete.prototype.onKeyDown = function(evt){
	if(!this.AutoComplete) return true;

	if(window.event) evt = window.event;
	//alert(event.keyCode);

	if(this.AutoComplete.itemSet)
		return true;

	if(evt.keyCode == 9 || evt.keyCode == 13){
		if(this.AutoComplete.choices.length > 0){
			this.value = this.AutoComplete.choices[this.AutoComplete.index]['name'] + ' #' + this.AutoComplete.choices[this.AutoComplete.index]['pubpid'];
			this.AutoComplete.oIdText.value = this.AutoComplete.choices[this.AutoComplete.index]['id'];
		}
		//alert("Set ID to " + this.AutoComplete.choices[this.AutoComplete.index]['id']);
		//if(event.cancelBubble) event.cancelBubble = true;
		//if(evt.returnValue) evt.returnValuse = false;
		//if(evt.preventDefault) evt.preventDefault();
		//if(evt.stopPropagation) evt.stopPropagation();
		//return false;
		if(evt.keyCode == 13 && window.event){
			evt.keyCode = 9;
		}
	}

}

AutoComplete.prototype.getData = function(){
	this.itemSet = false;
	var results = this.getDataFunc(this.oText.value);
	this.index = 0;
	this.searchString = this.oText.value;
	this.choices = results;
	if(results.length){
		this.choiceCount = results.length;
	}else{
		this.choiceCount = 0;
	}
	this.draw();

}

AutoComplete.prototype.clear = function(){
		// clear the popup-div.
		while ( this.oDiv.hasChildNodes() )
			this.oDiv.removeChild(this.oDiv.firstChild);

		this.oDiv.style.visibility = "hidden";
}

// Item div event handlers
AutoComplete.prototype.onDivMouseDown = function(e){
	//this.AutoComplete.oText.value = this.innerHTML;
	for(i = 0; i < this.AutoComplete.choices.length; i++){
		var htmlstr = '';
		var newstr = '';
		if(this.AutoComplete.highlight){
			//alert(this.innerHTML);
			var re = new RegExp('('+this.AutoComplete.searchString+')', 'ig');
			var cstr = this.AutoComplete.choices[i]['name'] + ' ' + this.AutoComplete.choices[i]['DOB'] + ' ' + this.AutoComplete.choices[i]['pubpid'] + ' ' + this.AutoComplete.choices[i]['ss'];
			newstr = cstr.replace(re, "<SPAN CLASS=AutoCompleteMatchingText>$1</SPAN>");
			// Strip out double quotes, they are put in incossitently across browsers
			var re2 = new RegExp('"', 'g');
			htmlstr = this.innerHTML.replace(re2, '');
		}else{
			newstr = this.AutoComplete.choices[i]['name'] + ' ' + this.AutoComplete.choices[i]['DOB'] + ' ' + this.AutoComplete.choices[i]['pubpid'] + ' ' + this.AutoComplete.choices[i]['ss'];
			htmlstr = this.innerHTML;
		}
		var nre = new RegExp('\ ','ig');
		if(newstr.replace(nre,'').toLowerCase() == htmlstr.replace(nre,'').toLowerCase()){
			this.AutoComplete.oText.value = this.AutoComplete.choices[i]['name']+ ' #' + this.AutoComplete.choices[i]['pubpid'];
			this.AutoComplete.oIdText.value = this.AutoComplete.choices[i]['id'];
			this.AutoComplete.itemSet = true;
		}
	}
}

AutoComplete.prototype.onDivMouseOver = function(){
	this.AutoComplete.index = 0;
	this.className = "AutoCompleteHighlight";
}

AutoComplete.prototype.onDivMouseOut = function(){
	this.className = "AutoCompleteBackground";
}


