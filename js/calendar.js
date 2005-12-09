var origColors = new Array();
var timeStack = new Array();
function toggleCell(box) {
  	
  	var cell = null;
  	cell = box.parentNode;
  
    var domDetect = null;
    var currentColor = null;
    var newColor = null;
    
    if (typeof(window.opera) == 'undefined' && typeof(cell.getAttribute) != 'undefined') {
    	
        currentColor = cell.style.backgroundColor;
        
        if (typeof(origColors[box.name]) == 'undefined') {
        	origColors[box.name] = currentColor;
        }
        
        domDetect    = true;

        if (currentColor == '#ffffff' || currentColor == 'rgb(255, 255, 255)') {
        	newColor = origColors[box.name];
        	for (i=0;i<timeStack.length;i++) {
        			timeStack[i].checked = false;
        			box.checked = false
        			td = timeStack[i].parentNode;
        			td.style.backgroundColor = origColors[timeStack[i].name];
        			newColor = origColors[box.name];
        			
        	}
        	document.forms.make_appointment.date.value = '';
        	document.forms.make_appointment.start_time.value = '';
        	document.forms.make_appointment.end_time.value = '';
        	timeStack = new Array();
        }
        else {
        	newColor = '#ffffff';
        	dt = new Date((box.value*1000) + (timeZoneAdjust*60*1000));



        	//alert(dt.toGMTString());
        	document.forms.make_appointment.date.value = (dt.getMonth()+1) + "/" + dt.getDate() + "/" + (dt.getFullYear());
        	document.forms.make_appointment_popup.date.value = (dt.getMonth()+1) + "/" + dt.getDate() + "/" + (dt.getFullYear());
        	if (timeStack.length == 0) {
        		timeStack.push(box);
        		mins = dt.getMinutes();
        		if (mins < 10)
        			mins = '0'+''+mins; 
        		document.forms.make_appointment.start_time.value = dt.getHours() + ':' + mins;	
        		document.forms.make_appointment_popup.start_time.value = dt.getHours() + ':' + mins;	
        	}
        	else if (timeStack.length == 1) {
        			if (!((box.value - timeStack[0].value) < 86400) || ((box.value - timeStack[0].value) < 0)) {
        				box.checked = false;
        				return true;
        			} 
        			
        			timeStack.push(box);
        		
        			mins = dt.getMinutes();
        			if (mins < 10) {
        				mins = '0'+''+mins;
        			} 
        			
        			document.forms.make_appointment.end_time.value = dt.getHours() + ':' + mins;
        			document.forms.make_appointment_popup.end_time.value = dt.getHours() + ':' + mins;
        			
        			//set the provider dropdown
				selectbox = document.forms.make_appointment.getElementsByTagName('select').item(1);

        			for (index=0;index<selectbox.length; index++){
       					if (selectbox[index].value==box.getAttribute("user")) {
       						selectbox.selectedIndex=index;
        					break;
       					}
    				}
    				
				selectbox = document.forms.make_appointment_popup.getElementsByTagName('select').item(1);
        			for (index=0;index<selectbox.length; index++){
       					if (selectbox[index].value==box.getAttribute("user")) {
       						selectbox.selectedIndex=index;
        					break;
       					}
    				}
    				
    				//the displayed title input field is actually 'notes' for the field name
    				//popUp('controller.php?calendar&appointment_popup&date=' + document.forms.make_appointment.date.value + '&start_time=' + document.forms.make_appointment.start_time.value + '&end_time=' + document.forms.make_appointment.end_time.value + '&title=' + document.forms.make_appointment.notes.value + '&user_id=' + document.forms.make_appointment.user_id[document.forms.make_appointment.user_id.selectedIndex].value  + '&patient=' + document.forms.make_appointment.external_id.value ,250,400)
    				popupEditor();
        	}
        	else {
        		for (i=0;i<timeStack.length;i++) {
        			timeStack[i].checked = false;
        			box.checked = false
        			td = timeStack[i].parentNode;
        			td.style.backgroundColor = origColors[timeStack[i].name];
        			newColor = origColors[box.name];
        			
        		}
        		document.forms.make_appointment.date.value = '';
        		document.forms.make_appointment.start_time.value = '';
        		document.forms.make_appointment.end_time.value = '';
        		
        		document.forms.make_appointment_popup.date.value = '';
        		document.forms.make_appointment_popup.start_time.value = '';
        		document.forms.make_appointment_popup.end_time.value = '';
        		timeStack = new Array();
        	}
        }
        
    }

    if (newColor) {
        if (domDetect) {
        	cell.style.backgroundColor = newColor;
        }
    }

	updateAppointmentTemplate('appointmentTemplate');
	updateAppointmentTemplate('appointmentTemplate1');

    return true;
}
