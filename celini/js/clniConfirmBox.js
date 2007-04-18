/**
 * A DHTML pop-up generator designed to be used as a confirmation box.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.celini
 * @subpackage javascript
 */
function clniConfirmBox() {
}

clniConfirmBox.prototype = {
	_popup: null,
	_oldBGColor: '',
	confirmForm: function(formObj, input, isID) {
		this._form = formObj;
		this._hideFormControls();
		this._createPopup(
			input == undefined ? 'confirmBoxText' : input,
			isID == undefined ? true : false);
		return false;
	},
	submit: function() {
		this._form.submit();
	},
	cancel: function() {
		this._showFormControls();
		this._popup.remove();
		this._form.reset();
	},
	hide: function() {
		this._showFormControls();
		this._popup.remove();
	},
	_createPopup: function(input, isID) {
		this._popup = new clniPopup(input, isID);
		this._popup.display();
	},
	_hideFormControls: function() {
		for (var i = 0; i < this._form.length; i++ ) {
			//alert('found ' + this._form.elements[i].type);
			if (this._form.elements[i].type != 'submit' && this._form.elements[i].type != 'reset') {
				continue;
			}
			this._form.elements[i].disabled = true;
			this._oldBGColor = this._form.elements[i].style.backgroundColor; 
			this._form.elements[i].style.backgroundColor = '#ccc';
		}
	},
	_showFormControls: function() {
		for (i = 0; i < this._form.length; i++ ) {
			if (this._form.elements[i].type != 'submit' && this._form.elements[i].type != 'reset') {
				continue;
			}
			this._form.elements[i].disabled = false; 
			this._form.elements[i].style.backgroundColor = this._oldBGColor;
		}
	}
} 

