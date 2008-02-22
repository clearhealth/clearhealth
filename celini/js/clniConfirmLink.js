/**
 * A DHTML pop-up generator designed to be used as a confirmation box for links
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.clear-health.celini
 * @subpackage javascript
 *
 */
function clniConfirmLink() {
}

clniConfirmLink.prototype = {
	modal:false,
	_popup: null,
	_oldBGColor: '',
	_linkObj: null,
	confirmLink: function(linkObj, popupContent, isID) {
		this._linkObj = linkObj;
		this._createPopup(
			popupContent == undefined ? 'confirmBoxText' : popupContent,
			isID == undefined ? true : false);
		return false;
	},
	submit: function() {
		location.href=this._linkObj;
	},
	cancel: function() {
		this._popup.remove();
	},
	_createPopup: function(popupContent, isID) {
		this._popup = new clniPopup(popupContent, isID);
		this._popup.modal = this.modal;
		this._popup.display();
	}
} 

