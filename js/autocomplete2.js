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
	this.choiceCount = this.choices.length;

	this.oIdText.value = '';

	if(this.choices.length == 1){
		this.oText.value = this.choices[0]['string'];
		this.oIdText.value = this.choices[0]['id'];
		this.oText.focus();
		this.oText.select();
	}else if(this.choices.length > 1){
		// Position the div
		this.oDiv.style.position='absolute';
		this.oDiv.style.top = eval(this.getTopPos() + 20) + "px";
		this.oDiv.style.left = this.getLeftPos() + "px";

		// add each string to the popup-div
		var i, n = this.choices.length;
		//alert('I know about ' + n + ' strings');
		for ( i = 0; i < n; i++ ){
			var oDiv = document.createElement('div');
			this.oDiv.appendChild(oDiv);

			if(this.highlight){
				// Highlight the matching text
				var re = new RegExp('('+this.searchString+')', 'i');
				var newstr = this.choices[i]['string'].replace(re, "<SPAN CLASS=AutoCompleteMatchingText>$1</SPAN>");
				//alert(this.searchString + '\n\n' + newstr);
				oDiv.innerHTML = newstr;
			}else{
				oDiv.innerHTML = this.choices[i]['string'];
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
		if(!this.AutoComplete.timerSet){
			setTimeout('document.getElementById(\''+this.AutoComplete.textFieldId+'\').AutoComplete.getData()', this.AutoComplete.timeout);
			this.AutoComplete.timerSet = true;
		}
		//this.AutoComplete.getData();
	}
}

AutoComplete.prototype.onKeyDown = function(evt){
	if(!this.AutoComplete) return true;

	if(window.event) evt = window.event;
	//alert(event.keyCode);

	if(this.AutoComplete.itemSet)
		return true;

	if(evt.keyCode == 9 || evt.keyCode == 13){
		this.value = this.AutoComplete.choices[this.AutoComplete.index]['string'];
		this.AutoComplete.oIdText.value = this.AutoComplete.choices[this.AutoComplete.index]['id'];
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
	this.timerSet = false;
	var results = this.getDataFunc(this.oText.value);
	this.index = 0;
	this.searchString = this.oText.value;
	this.choices = results;
	this.choiceCount = results.length;
	this.draw();

}

AutoComplete.prototype.clear = function(){
		// clear the popup-div.
		while ( this.oDiv.hasChildNodes() )
			this.oDiv.removeChild(this.oDiv.firstChild);

		this.oDiv.style.visibility = "hidden";
}

// Item div event handlers
AutoComplete.prototype.onDivMouseDown = function(){
	//this.AutoComplete.oText.value = this.innerHTML;
	for(i = 0; i < this.AutoComplete.choices.length; i++){
		var htmlstr = '';
		var newstr = '';
		if(this.AutoComplete.highlight){
			//alert(this.innerHTML);
			var re = new RegExp('('+this.AutoComplete.searchString+')', 'i');
			newstr = this.AutoComplete.choices[i]['string'].replace(re, "<SPAN CLASS=AutoCompleteMatchingText>$1</SPAN>");
			// Strip out double quotes, they are put in incossitently across browsers
			var re2 = new RegExp('"', 'g');
			htmlstr = this.innerHTML.replace(re2, '');
		}else{
			newstr = this.AutoComplete.choices[i]['string'];
			htmlstr = this.innerHTML;
		}
		//alert(newstr+'\n\n'+htmlstr);
		if(newstr.toLowerCase() == htmlstr.toLowerCase()){
			this.AutoComplete.oText.value = this.AutoComplete.choices[i]['string'];
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


