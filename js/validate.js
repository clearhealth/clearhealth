// Cellini JS validation functions
// author: Joshua Eichorn jeichorn@mail.com

/***********************************************************************************/
/*				Public API					   */
/***********************************************************************************/
/*
To use Cellini validation call clini_validate_form in the onsubmit event of your form
rules are registered anytime before this uing the clni_register_validation_rule function

Cellini contains smarty plugins for doing both of these thing
Plugins:  clni_form and clni_register_validation_rule
*/
/* rule list */
// required
// number
// ssn
// match
// date
// email


/**
 * Register a validation rule
 */
function clni_register_validation_rule(id,rule,notify,message) {
	o = new Object;
	o.id = id;
	o.rule = rule;
	o.notify = notify;
	o.message = message;
	clni_validation_rules[clni_validation_rules.length] = o;
}

/**
 * Register a forms message target
 */
function clni_register_message_target(formId,targetId) {
	clni_notify_message_targets[formId] = targetId;
}

/**
 * Auto register a date validation on all inputs named date[name]
 */
function clni_validate_auto_register_dates() {
	inputs = document.getElementsByTagName('input');

	for(var i = 0; i < inputs.length; i++) {
		if (inputs[i].name.match(/date\[[a-zA-Z_]+\]/)) {
			if (!inputs[i].id) {
				inputs[i].id = "autoDateId"+i;
			}
			clni_register_validation_rule(inputs[i].id,'date','alert');
		}
	}
}



/***********************************************************************************/
/*			Main Validation functions				   */
/***********************************************************************************/


var clni_validation_rules = Array();
var clni_notify_alert_reset_store = new Object;
var clni_notify_message_targets = new Object();

/**
 * Use on a forms onSubmit action to validate a form, uses the rules specified in clni_validation_rules
 */
function clni_validate(currentForm) {
	var ret = true;
	var target = false;

	try {
	if (clni_notify_message_targets[currentForm.id]) {
		target = document.getElementById(clni_notify_message_targets[currentForm.id]);
		target.innerHTML = "";
		target.className = "clniMessageInActive";
	}
	for(var i =0; i < clni_validation_rules.length; i++) {
		rule = clni_validation_rules[i];

		//alert("Checking rule: "+rule.rule+" on "+rule.id+" disabled: "+document.getElementById(rule.id).disabled);
		// only run validation on non disabled elements
		if (document.getElementById(rule.id).disabled != true && (elementInCurrentForm(document.getElementById(rule.id),currentForm))) {
			document.getElementById(rule.id).ok = true;
			eval('var res = clni_rule_'+rule.rule+'(document.getElementById("'+rule.id+'"));');
			
			if (!res) {
				document.getElementById(rule.id).ok = false;

				if (target) {
					eval('clni_notify_'+rule.notify+'(document.getElementById("'+rule.id+'"),target,rule);');
				}
				else {
					eval('clni_notify_'+rule.notify+'(document.getElementById("'+rule.id+'"));');
				}
				ret = false;
			}
			else {
				eval('clni_notify_'+rule.notify+'_reset(document.getElementById("'+rule.id+'"));');
			}
		}
	}

	} catch (e) {
		var msg = "";
		for (var i in e) {
			msg += i+':'+e[i]+"\n";
		}
		alert('Error in Validation Code form not submitted\n'+msg);
		ret = false;
	}
	ret = false;
	return ret;
}

/**
 * Checks if an element is in the current form, used because we keep a list of rules per page not per form
 */
function elementInCurrentForm(element,currentForm) {
	if (element && currentForm && currentForm  === element.parentNode) {
		//alert("match" + element.parentNode.id);
		return true;
	}
	else if (element){
		if (elementInCurrentForm(element.parentNode,currentForm)) {
			return true;
		}
	}
	return false;
}

/**
 * Set an element to the alert css class
 */
function clni_notify_alert(element) {
	if (!element.className) {
		element.className = "";
	}
	if (clni_notify_alert_reset_store[element.id] == null) {
		clni_notify_alert_reset_store[element.id]=element.className;
	}
	element.className = "clniAlert";
}

/**
 * Adds the elements message to form message destination
 *
 * also runs the normal alert notification
 */
function clni_notify_messageAlert(element,target,info) {
	if (target) {
		target.innerHTML += "<div>"+info.message+"</div>";
		target.className = "clniMessageActive";
	}
	clni_notify_alert(element);
}

/**
 * Set an element to the alert css class
 */
function clni_notify_alert_reset(element) {
	if (clni_notify_alert_reset_store[element.id] != null && element.ok) {
		element.className = clni_notify_alert_reset_store[element.id];
	}
}
function clni_notify_messageAlert_reset(element) {
	clni_notify_alert_reset(element);
}

/***********************************************************************************/
/*					RULES					   */
/***********************************************************************************/





/**
 * Requires that the passed in element is set
 */
function clni_rule_required(element) {
	if (element.disabled == true) {
		return true;
	}

	if (element.value.length == "") {
		return false;
	}
	
	return true;
}

/**
 * Requires that the passed in element contains a valid date or is empty
 */
function clni_rule_date(element) {

	if (element.value.length == 0) {
		return true;
	}

	try {
		parseDateString(element.value);
		return true;
	} 
	catch(e) {
		return false;
	}
}

/**
 * Require that the passed in element has the same value as the element with id + '_match'
 */
function clni_rule_match(element) {
	if (element.value.length == 0) {
		return true;
	}

	match = document.getElementById(element.id+'_match');

	if (match && match.value == element.value) {
		return true;
	}
	return false;
}

/**
 * Require that an element has by selected by an autocomplete rule
 */
function clni_rule_autocomplete(element) {

	var autofield = element.getAttribute('autofield');
	if (autofield) {
		if (document.getElementById(autofield).value != "") {
			return true;
		}
	}
	return false;
}


/**
 * Require tha the passed in element be an email address
 */
function clni_rule_email(element) {

	var str = element.value;

	var at="@"
	var dot="."
	var lat=str.indexOf(at)
	var lstr=str.length
	var ldot=str.indexOf(dot)
	if (str.indexOf(at)==-1) {
		return false
	}

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		return false
	}

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		return false
	}

	if (str.indexOf(at,(lat+1))!=-1){
		return false
	}

	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false
	}

	if (str.indexOf(dot,(lat+2))==-1){
		return false
	}
		
	if (str.indexOf(" ")!=-1){
		return false
	}

	return true					
}

/**
 * Requre the passing in value to be a number
 */
function clni_rule_number(element) {
	if (element.value.match(/^[0-9\.]+$/)) {
		return true;
	}
	return false;
}

/**
 * Require the string to be a SSN
 *
 * 9 digits or
 * 6 digits and 3 letters (fake ssn birthday+initials
 */
function clni_rule_ssn(element) {
	if (element.value.match(/^\d{9}$/)) {
		return true;
	}
	if (element.value.match(/^\d{6}[a-zA-Z]{3}$/)) {
		return true;
	}
	return false;
}
