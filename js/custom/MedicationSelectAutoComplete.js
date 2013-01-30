/*****************************************************************************
*       MedicationSelectAutoComplete.js
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/
dojo.provide("custom.MedicationSelectAutoComplete");
dojo.declare("custom.MedicationSelectAutoComplete", dojox.data.QueryReadStore, {
	fetch:function (request) {
		// strip off the *
		var queryName = request.query.name;
		var tradename = queryName.substr(0,queryName.length-1);
        	request.serverQuery = {'': tradename} ;
		delete(request.start);
		delete(request.count);
        	return this.inherited("fetch", arguments);
    	},
	_filterResponse: function(data) {
		var retData = new Object();
		retData.items = new Array();
		retData.numRows = data.length;
		retData.identifier = 'label';
		for (var i=0; i<data.length; i++) {
			retData.items[i] = new Object();
			retData.items[i].label = data[i].id;
			var drugName = data[i].tradename + ' ' + data[i].strength + ' ' + data[i].unit + ' ' + data[i].packsize + data[i].packtype;// + ' ' + data[i].ndc;
			if (data[i].inFormulary == 1) {
				//varDump(data[i]);
				drugName = "<span style='background-color:yellow;color:#000000;'>" + drugName + '</span>';
			}
			retData.items[i].name = drugName;
		}
		return retData;
	}
});
dojo.provide("custom.MedicationSelectComboBox");
dojo.declare("custom.MedicationSelectComboBox", dijit.form.ComboBox, {
	_doSelect: function(tgt){
			tgt.item.i.name = tgt.item.i.name.replace(/<.*?>/g, "");
			this.selectedKey = tgt.item.i.label;
                        this.item = tgt.item;
                        this.setValue(this.store.getValue(tgt.item, this.searchAttr), true);
                },
	_autoCompleteText: function(/*String*/ text){
                        var fn = this.focusNode;
                        dijit.selectInputText(fn, fn.value.length);
                        var caseFilter = this.ignoreCase? 'toLowerCase' : 'substr';
                        if(text[caseFilter](0).indexOf(this.focusNode.value[caseFilter](0)) == 0){
                                var cpos = this._getCaretPos(fn);
                                if((cpos+1) > fn.value.length){
                                        fn.value = text;//.substr(cpos);
                                        dijit.selectInputText(fn, cpos);
                                }
                        }else{
                                // text does not autoComplete; replace the whole value and highlight
                                fn.value = text.replace(/<.*?>/g, "");
;
                                dijit.selectInputText(fn);
                        }
                }
});

