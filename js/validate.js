// Cellini JS validation functions
// author: Joshua Eichorn jeichorn@mail.com

var clni_validation_rules = Array();
var clni_notify_alert_reset_store = new Object;

/**
 * Use on a forms onSubmit action to validate a form, uses the rules specified in clni_validation_rules
 */
function clni_validate() {
	var ret = true;
	for(var i =0; i < clni_validation_rules.length; i++) {
		rule = clni_validation_rules[i];

		//alert("Checking rule: "+rule.rule+" on "+rule.id+" disabled: "+document.getElementById(rule.id).disabled);
		// only run validation on non disabled elements
		if (document.getElementById(rule.id).disabled != true) {
			eval('var res = clni_rule_'+rule.rule+'(document.getElementById("'+rule.id+'"));');
			
			if (!res) {
				eval('clni_notify_'+rule.notify+'(document.getElementById("'+rule.id+'"));');
				ret = false;
			}
			else {
				eval('clni_notify_'+rule.notify+'_reset(document.getElementById("'+rule.id+'"));');
			}
		}
	}
	return ret;
}

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
 * Set an element to the alert css class
 */
function clni_notify_alert_reset(element) {
	if (clni_notify_alert_reset_store[element.id] != null) {
		element.className = clni_notify_alert_reset_store[element.id];
	}
}

/**
 * Register a validation rule
 */
function clni_register_validation_rule(id,rule,notify) {
	o = new Object;
	o.id = id;
	o.rule = rule;
	o.notify = notify;
	clni_validation_rules[clni_validation_rules.length] = o;
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
