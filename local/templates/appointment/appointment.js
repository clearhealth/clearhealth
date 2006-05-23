var aptEditorChanged = false;

function timeToSeconds(time) {
	var p = time.split(':');
	return (p[0]*60*60)+(p[1]*60);
}
function secondsToTime(seconds) {
	var h = Math.floor(seconds/(60*60));
	var m = Math.floor( (seconds%(60*60))/60);
	return h+':'+m;
}
function updateAp(select,id) {
	var divs = document.getElementById(id).getElementsByTagName('div');
	for(var i = 0; i < divs.length; i++) {
		if (divs[i].className == select.value) {
			divs[i].style.display = 'block';
			var selects = divs[i].getElementsByTagName('select');
			for(var b = 0; b < selects.length; b++) {
				selects[b].disabled = false;
			}
		}
		else {
			divs[i].style.display = 'none';
			var selects = divs[i].getElementsByTagName('select');
			for(var b = 0; b < selects.length; b++) {
				selects[b].disabled = true;
			}
		}
	}
	updateAppointmentTemplate(id);
}
function updateAppointmentTemplate(id) {
	var divs = document.getElementById(id).getElementsByTagName('div');
	var length = 0;
	var form = false;

	for(var i = 0; i < divs.length; i++) {
		if (divs[i].style.display != 'none') {
			var input = divs[i].getElementsByTagName('input').item(0);
			if (input) {
				length = input.value;
				form = input.form;
			}
		}
	}
	if (form) {
		var start = false;
		var end = false;
		for(var i = 0; i < form.elements.length; i++) {
			if (form.elements[i].name == 'Appointment[end_time]') {
				end = form.elements[i];
			}
			if (form.elements[i].name == 'Appointment[start_time]') {
				start = form.elements[i];
			}
		}

		if (start.value != '') {
			end.value = secondsToTime(timeToSeconds(start.value)+Math.floor(length));
		}
	}
}

var currentlyEditing = 0;
var appointmentPopup = false;
function showAddAppointment() {
	appointmentPopup = new clniPopup('',false);
	if(aptEditorChanged != false && aptEditorChanged != currentlyEditing) {
		editAppointment(0);
		return;
	}
	appointmentPopup.draggable = true;
	appointmentPopup.draggableOptions = {handle:'title'};
	appointmentPopup.useElement = 'appointmentEditor';
	appointmentPopup.modal = true;
	appointmentPopup.display();
	currentlyEditing = 0;
}
function hideAddAppointment() {
	appointmentPopup.hide();
}

st.selectItems = function() {
	showAddAppointment();
	var start = st.getEarliestSelected();
	var end = st.getLatestSelected();
	var id = st.getEarliestId();
	this.clearSelected();

	// set times
	var mins = start.getMinutes();
        if (mins < 10) {
        	mins = '0'+''+mins;
	}
	$('aeStart').value = start.getHours()+':'+mins;

	var mins = end.getMinutes();
        if (mins < 10) {
        	mins = '0'+''+mins;
	}
	$('aeEnd').value = end.getHours()+':'+mins;

	$('aeDate').value = start.print('%m/%d/%Y');

	var p = id.lastIndexOf('-');
	var provider = id.substr(p+1);

	var select = $('aeProvider').getElementsByTagName('select')[0];

	for(var i = 0; i < select.options.length; i++) {
		if (select.options[i].value == provider) {
			select.selectedIndex = i;
			break;
		}
	}
}

function editAppointment(id){
	currentlyEditing = id;
	HTML_AJAX.call('appointment','ajax_edit',editAppointmentcb,id);
}
function editAppointmentcb(resultSet){
	aptEditorChanged = resultSet[0];
	HTML_AJAX_Util.setInnerHTML(document.getElementById('appointmentEditor'),resultSet[1]);
	showAddAppointment();
}

function cancelAppointment(id) {
	HTML_AJAX.call('appointment','ajax_cancel',updateAppointmentcb,id);
}
function NSAppointment(id) {
	HTML_AJAX.call('appointment','ajax_ns',updateAppointmentcb,id);
}

var delConfirm = false;
function deleteAppointment(id) {
	delConfirm = new clniPopup('<div style="background-color: white; border: groove 2px black; text-align:center; padding:4px;">Delete Appointment #'+id+'?<br> <button onclick="cancelDeleteAppointment()">No</button> <button onclick="reallyDeleteAppointment('+id+')">Yes</button></div>',false);
	delConfirm.modal = true;
	delConfirm.display();
}
function reallyDeleteAppointment(id) {
	delConfirm.hide();
	HTML_AJAX.call('appointment','ajax_delete',deleteAppointmentcb,id);
}
function cancelDeleteAppointment() {
	delConfirm.remove();
}

function updateAppointmentcb(resultSet) {
	document.getElementById('appointmentstatus'+resultSet[0]).innerHTML = resultSet[1];
}

function deleteAppointmentcb(resultSet) {
	window.location = window.location;
	return;
	document.getElementById('appointmentstatus'+resultSet[0]).innerHTML = resultSet[1];
	document.getElementById('event'+resultSet[0]).style.backgroundColor = 'gray';
}

function expandAppointment(id,el) {
	if(el.offsetHeight < 100) {
		if(document.getElementById('event'+id+'oldheightholder').innerHTML == '') {
			document.getElementById('event'+id+'oldheightholder').innerHTML = el.offsetHeight;
		}
		if(document.getElementById('event'+id+'newheightholder').innerHTML != '') {
			el.style.height=document.getElementById('event'+id+'newheightholder').innerHTML+'px';
		} else {
			el.style.height='auto';
			document.getElementById('event'+id+'newheightholder').innerHTML = el.offsetHeight;
		}
		el.style.zIndex=100;
	}

	el.style.border = 'solid 2px black';
}

function shrinkAppointment(event, id, el) {
	var mousePos = clniUtil.mouseXY(event);
	var left = clniUtil.posLeft(el);
	var top = clniUtil.posTop(el);
	if(mousePos.x >= left && mousePos.x <= (left + el.clientWidth) && mousePos.y >= top && mousePos.y <= (top + el.clientHeight)) return;
	
	el.style.zIndex=50;
	if(document.getElementById('event'+id+'oldheightholder').innerHTML != '') {
		el.style.height=document.getElementById('event'+id+'oldheightholder').innerHTML+'px';
	}

	el.style.border = 'solid 1px black';
}

function makeAppointment(form) {

		inputs = form.getElementsByTagName('input');
		selects = form.getElementsByTagName('select');
		textareas = form.getElementsByTagName('textarea');
		valuesarray = new Array();
		keysarray = new Array();
		for(var i=0;i<inputs.length;i++) {
			if(inputs[i].type != 'submit' && inputs[i].type != 'button') {
				keysarray[i] = inputs[i].name;
				valuesarray[i] = inputs[i].value;
			}
		}
		for(var i=0;i<selects.length;i++) {
			options = selects[i].getElementsByTagName('option');
			for(var j=0;j<options.length;j++) {
				if(options[j].selected) {
					keysarray[keysarray.length] = selects[i].name;
					valuesarray[valuesarray.length] = options[j].value;
				}
			}
		}
		HTML_AJAX.defaultEncoding = 'JSON'; // set encoding to JSON encoding method
		HTML_AJAX.call('appointment','ajax_process',makeAppointmentcb,keysarray,valuesarray);
		HTML_AJAX.defaultEncoding = 'Null'; // set encoding back to default
}

/*
rs[0] = error message
rs[1] = column (provider) id
rs[2] = old event id (empty for new appointment)
rs[3] = new appointment html
*/
function makeAppointmentcb(resultSet) {
	if(resultSet[0] != 0) {
		document.getElementById('aeMessageTarget').innerHTML = resultSet[0];
	} else {
		if(resultSet[2] > 0) {
			document.getElementById('event'+resultSet[2]).innerHTML = resultSet[3];
		} else {
			eventholder = document.getElementById('schedule'+resultSet[1]+'events');
			newevent = document.createElement('div');
			newevent.className='innerColumn';
			eventholder.appendChild(newevent);
			newevent.name='newevent'+resultSet[4];
			newevent.style.display='block';
			newevent.innerHTML = resultSet[3];
		}
		hideAddAppointment();
	}
	//	alert(resultSet);
}

/*
This function will call the rules checker
before actually submitting the appointment.
*/
function checkRules(form) {
	if(document.getElementById('appointmentOverride') && document.getElementById('appointmentOverride').checked) {
		form.submit();
	}
	aptString = HTML_AJAX.formEncode(form);
	url = HTML_AJAX.defaultServerUrl+'?'+aptString;
	HTML_AJAX.fullcall(url,'JSON','appointment','check_rules',checkRulescb);
}

function checkRulescb(resultSet) {
	if(resultSet.length > 0) {
		alertbox = document.getElementById('appointmentAlerts');
		alertbox.innerHTML = '';
		for(var i=0;i<resultSet.length;i++) {
			adiv = document.createElement('div');
			adiv.id='alert'+i;
			alertbox.appendChild(adiv);
			document.getElementById('alert'+i).style.width='250px';
			HTML_AJAX_Util.setInnerHTML(document.getElementById('alert'+i),resultSet[i]);
		}
		alertbox.style.display='block';
	} else {
		document.getElementById('AppointmentEdit').submit();
	}
}

