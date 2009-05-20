function patientSuggest(id,valueId) {
	this.suggest = new clniSuggest('{$name} #{$pubpid} ({$DOB})',id,valueId,'PatientFinder','SmartSearch');
	this.id = id;
	this.suggest.allowBreaks = false;

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
patientSuggest.prototype = {
}
function patientVistaFinder(id,valueId) {
	this.suggest = new clniVistaFinder('{$name} #{$pubpid} ({$DOB})',id,valueId,'VistaPatientFinder','ajaxFind');
	this.id = id;
	this.suggest.allowBreaks = false;

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
patientVistaFinder.prototype = {
}

function personSuggest(id,valueId) {
	this.suggest = new clniSuggest('{$name} #{$pubpid} ({$DOB})',id,valueId,'PatientFinder','SmartSearch');
	this.id = id;
	this.suggest.allowBreaks = false;
	this.suggest.getRemoteCallParams = function() {
		return [this._searchString,1];
	};

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}

function codeSuggest(id,valueId,callback) {
	this.suggest = new clniSuggest('{$code}: {$code_text}',id,valueId,'Coding','cpt_search');
	this.suggest.onSelect = callback;

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
codeSuggest.prototype = {
}

function procedureSuggest(id,valueId,callback,pre) {
	this.suggest = new clniSuggest('{$code}: {$code_text}',id,valueId,'Coding','procedure_search');
	this.suggest.onSelect = callback;
	this.suggest.preRequest = pre;

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
procedureSuggest.prototype = {
}

function diagnosisSuggest(id,valueId,callback,pre) {
	this.suggest = new clniSuggest('{$code}: {$code_text}',id,valueId,'Coding','diagnosis_search');
	this.suggest.onSelect = callback;
	this.suggest.preRequest = pre;

	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
diagnosisSuggest.prototype = {
}

function medicationSuggest(id,valueId) {
	this.suggest = new clniSuggest('{$ndc}: {$tradename} {$strength}{$unit} {$packsize}{$packtype} ',id,valueId,'CHMED','ajaxBaseMed24');
        this.suggest.allowBreaks = false;
	this.suggest.onSelect = populateForm;
	// style the box so you know its special
	document.getElementById(id).style.border = 'solid 1px blue';
}
medicationSuggest.prototype = {
}

function drugnameSuggest(id,valueId) {
        this.suggest = new clniSuggest('{$fda_drugname}/{$tradename}',id,valueId,'CHMED','ajaxDrugName24');
        this.suggest.allowBreaks = false;
        this.suggest.onSelect = populateForm;
        // style the box so you know its special
        document.getElementById(id).style.border = 'solid 1px blue';
}
drugnameSuggest.prototype = {
}

function allergySuggest(id,valueId) {
        this.suggest = new clniSuggest('{$name}',id,valueId,'CHMED','ajaxAllergy');
        this.suggest.allowBreaks = false;
        this.suggest.onSelect = populateAllergyForm;
        // style the box so you know its special
        document.getElementById(id).style.border = 'solid 1px blue';
}
allergySuggest.prototype = {
}

function problemListSuggest(id,valueId) {
        this.suggest = new clniSuggest('{$sctid}: {$description}',id,valueId,'VistaProblemList','problem_list_search');
        this.suggest.allowBreaks = false;

        // style the box so you know its special
        document.getElementById(id).style.border = 'solid 1px blue';
}
problemListSuggest.prototype = {
}
