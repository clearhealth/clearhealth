HTML_AJAX.onError = function(e) 
{
	msg = "";
	for(var i in e) {
		msg += i+':'+e[i]+"\n";
	}
	alert(msg);
}
// AJAX SUGGEST

// create a javascript hash to hold or callback methods
// resultset = array(id,resultarray,js)
function suggestcb(resultSet) {
	var resultDiv = document.getElementById(resultSet[0]+'suggestions');
	resultDiv.innerHTML = '';
	resultDiv.style.display = 'block';
	if (!resultSet) resultDiv.style.display = 'none';
	else{
		for(var f=0; f<resultSet[1].length; ++f){
			var result=document.createElement("span");
			result.name=resultSet[0];
			result.innerHTML = resultSet[1][f][1];
			result.onmouseover = highlight;
			result.onmouseout = unHighlight;
			result.onmousedown = selectEntry;
			result.class='nohighlight';
			resultDiv.appendChild(result);
		}
		if(resultSet[2]){
			for(var i=0;i<resultSet[2].length;i++){
				eval(resultSet[2][i]);
			}
		}
	}
}

HTML_AJAX.queues['suggest'] = new HTML_AJAX_Queue_Interval_SingleBuffer(350);

//functions for interactivity
function highlight (){
	this.className = 'highlight';
}

function unHighlight () {
	this.className = 'nohighlight';
}

function selectEntry () {
	do_suggest(this.name,this.innerHTML);
	document.getElementById(this.name+'suggestions').style.display = 'none';
}

var string = '';
var oldstring = '';
var timeout= 350; /*milliseconds to timeout; good value is 350*/

function do_suggest(id,preset,type,func) {
	if (preset!=false){
		document.getElementById(id).value = preset;
		oldstring=preset;
	}
	string = document.getElementById(id).value;
	if (string != oldstring) {
		/* don't send request when input field is empty */
		if (string!='') {
			var doit = "var remoteSuggest = new "+type+"(suggestCallback);";
			eval(doit);
			remoteSuggest.dispatcher.queue = 'suggest';
			var doit = "remoteSuggest."+func+"('"+string+"','"+id+"');";
			eval(doit);
		} else {
			/*hide div instead */
			document.getElementById(id+'suggestions').style.display = 'none';
		}
		oldstring = string;
	}
}

suggestCallback = {
	SmartSearch:function(resultSet){
		suggestcb(resultSet);
	}
}