var QuickSave = {
	
	FirstChange: Array(),
	FormIdentifier: Array(),
	Loading: false,
	quickLoad: function(formid,formIdentifier) {
		QuickSave.Loading = true;
		HTML_AJAX.call('quicksave','loadForm',QuickSave.quickLoadcb,formid,QuickSave.FormIdentifier[formid]);
	},
	
	quickLoadcb: function (resultSet) {
		formid=resultSet[0];
		data=resultSet[1];
		form = document.getElementById(formid);
		inputs = form.getElementsByTagName('input');
		selects = form.getElementsByTagName('select');
		textareas = form.getElementsByTagName('textarea');
		for(i in data) {
			input = false;
			switch (data[i][0]){
				case 'text':
				case 'hidden':
					for(b=0;b<inputs.length;b++) {
						if( (inputs[b].type=='text' || inputs[b].type=='hidden') && inputs[b].name==i) {
							input=inputs[b];
						}
					}
					if(input) {
						input.value=data[i][1];
					}
					break;
				case 'select':
					for(b=0;b<selects.length;b++) {
						if(selects[b].name==i) {
							input=selects[b];
						}
					}
					if(input) {
						options = input.getElementsByTagName('option');
						for(var a=0;a<options.length;a++) {
							for(var b=0;b<data[i][1].length;b++) {
								if(options[a].value == data[i][1][b][0]) {
									options[a].selected=data[i][1][b][1];
								}
							}
						}
					}
					break;
				case 'textarea':
					for(b=0;b<textareas.length;b++) {
						if(textareas[b].name==i) {
							input=textareas[b];
						}
					}
					if(input) {
						input.value=data[i][1];
					}
					break;
				case 'checkbox':
					for(b=0;b<inputs.length;b++) {
						if(inputs[b].type=='checkbox' && inputs[b].name==i) {
							inputs[b].checked=data[i][1];
						}
					}
					break;
				case 'radio':
					for(b=0;b<inputs.length;b++) {
						if(inputs[b].type=='radio' && inputs[b].name==i && inputs[b].value==data[i][1]) {
							inputs[b].checked='checked';
						}
					}
					break
			}
		}
		QuickSave.Loading = false;
	},

	quickTextarea: function(element) {
		var x = new Array();
		x[0] = element.name;
		x[1] = 'textarea';
		x[2] = element.value;
		return x;
	},

	quickInput: function(element) {
		var x = new Array();
		x[0] = element.name;
		x[1] = element.type;
		if(element.type=='text' || element.type=='hidden' || element.type=='') {
			x[2] = element.value;
		}
		if(element.type=='checkbox') {
			x[2] = element.checked;
		}
		if(element.type=='radio') {
			if(element.checked) {
				x[2] = element.value;
			} else {
				x[2] = '';
			}
		}
		return x;
	},

	quickSelect: function(element) {
		var x = new Array();
		x[0] = element.name;
		x[1] = 'select';
		var options = element.getElementsByTagName('option');
		x[2] = new Array();
		for(var s=0;s<options.length;s++) {
			x[2][s] = new Array(options[s].value,options[s].selected);
		}
		return x;
	},

	quickSave: function(formid,info) {
		formarray = new Array();
		formarray[0] = formid;
		formarray[1] = QuickSave.FormIdentifier[formid];
		formarray[2] = new Array(info[0],info[1],info[2]);
		HTML_AJAX.defaultEncoding = 'JSON'; // set encoding to JSON encoding method
		HTML_AJAX.call('quicksave','saveItem',false,formarray);
		HTML_AJAX.defaultEncoding = 'Null'; // set encoding back to default
	},
	
	quickSaveForm: function(formid) {
		if(document.getElementById('qsInfo')) {
			document.getElementById('qsInfo').parentNode.parentNode.parentNode.style.display='none';
		}
		theform = document.getElementById(formid);

		inputs = document.getElementById(formid).getElementsByTagName('input');
		selects = document.getElementById(formid).getElementsByTagName('select');
		textareas = document.getElementById(formid).getElementsByTagName('textarea');
		formarray = new Array();
		formarray[0] = formid;
		formarray[1] = QuickSave.FormIdentifier[formid];
		formarray[2] = new Array();
		for(var i=0;i<inputs.length;i++) {
			if(inputs[i].type != 'submit' && inputs[i].type != 'button') {
				formarray[2][formarray[2].length] = QuickSave.quickInput(inputs[i]);
			}
		}
		for(var i=0;i<selects.length;i++) {
			formarray[2][formarray[2].length] = QuickSave.quickSelect(selects[i]);
		}
		for(var i=0;i<textareas.length;i++) {
			formarray[2][formarray[2].length] = QuickSave.quickTextarea(textareas[i]);
		}
		HTML_AJAX.defaultEncoding = 'JSON'; // set encoding to JSON encoding method
		HTML_AJAX.call('quicksave','saveForm',false,formarray);
		HTML_AJAX.defaultEncoding = 'Null'; // set encoding back to default
	},

	setup: function(element) {
		var inputs = element.getElementsByTagName('input');
		var selects = element.getElementsByTagName('select');
		var textareas = element.getElementsByTagName('textarea');
		
		var inputhandler = function(event) {
			if(!QuickSave.Loading) {
				if(QuickSave.FirstChange[element.id] == true) {
					QuickSave.quickSaveForm(element.id);
					QuickSave.FirstChange[element.id] = false;
				} else {
					var target = HTML_AJAX_Util.eventTarget(event);
					res = QuickSave.quickInput(target);
					QuickSave.quickSave(element.id,res);
				}
			}
		}
		var selecthandler = function(event) {
			if(!QuickSave.Loading) {
				if(QuickSave.FirstChange[element.id] == true) {
					QuickSave.quickSaveForm(element.id);
					QuickSave.FirstChange[element.id] = false;
				} else {
					var target = HTML_AJAX_Util.eventTarget(event);
					res = QuickSave.quickSelect(target);
					QuickSave.quickSave(element.id,res);
				}
			}
		}
		var textareahandler = function(event) {
			if(!QuickSave.Loading) {
				if(QuickSave.FirstChange[element.id] == true) {
					QuickSave.quickSaveForm(element.id);
					QuickSave.FirstChange[element.id] = false;
				} else {
					var target = HTML_AJAX_Util.eventTarget(event);
					res = QuickSave.quickTextarea(target);
					QuickSave.quickSave(element.id,res);
				}
			}
		}

		for(var i=0;i<inputs.length;i++) {
			if(inputs[i].type == 'submit' || inputs[i].type == 'button') 
				continue;
			if(inputs[i].type == 'checkbox') {
				HTML_AJAX_Util.registerEvent(inputs[i],'change',inputhandler);
			} else {
				HTML_AJAX_Util.registerEvent(inputs[i],'blur',inputhandler);
			}
		}
		for(var i=0;i<selects.length;i++) {
			HTML_AJAX_Util.registerEvent(selects[i],'change',selecthandler);
		}
		for(var i=0;i<textareas.length;i++) {
			HTML_AJAX_Util.registerEvent(textareas[i],'blur',textareahandler);
		}
	}
}


