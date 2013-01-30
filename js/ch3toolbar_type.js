/*****************************************************************************
*       ch3toolbar_type.js
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

dhtmlXToolbarObject.prototype.addDatePicker = function(data,id,pos,text,imgEnabled,imgDisabled) {
	if (!data || typeof data != "object") return;
	var itemText = (text!=null?(text.length==0?null:text):null);
	var str = '<item id="'+id+'" imgStyle="float:left;" type="button"'+(imgEnabled!=null?' img="'+imgEnabled+'"':'')+(imgDisabled!=null?' imgdis="'+imgDisabled+'"':'')+(itemText!=null?' title="'+itemText+'"':"")+'><![CDATA[';
	str += '<span>'+itemText+' <input type="text" name="'+data.name+'" id="'+data.id+'" value="'+(data.value!=null?data.value:"")+'" /></span>';
	str += '<script>';

	str += 'var box'+data.id+' = dijit.byId("'+data.id+'");';
	str += 'if (typeof box'+data.id+' != "undefined") {';
	str += '	box'+data.id+'.destroyRecursive();';
	str += '	box'+data.id+' = null;';
	str += '}';
	if (!data.disabled) {
		data.disabled = false;
	}
	var style = "";
	if (data.style) {
		style = ',style:"'+data.style+'"';
	}
	str += 'box'+data.id+' = new dijit.form.DateTextBox({name:"'+data.name+'",constraints:{datePattern:"yyyy-MM-dd"},disabled:'+data.disabled+style+'},dojo.byId("'+data.id+'"));';
	if (data.value) {
		var x = data.value.split("-");
		if (x.length == 3) {
			var y = parseInt(x[0]);
			var m = parseInt(x[1]) - 1;
			var d = parseInt(x[2]);
			str += "var dateVal = new Date();";
			str += "dateVal.setFullYear("+y+","+m+","+d+");";
			str += 'box'+data.id+'.setValue(dateVal);';
		}
	}
	if (data.onChange) {
		str += 'function box'+data.id+'OnChanged(value) {';
		str += '	'+data.onChange+'(value,"'+data.uid+'");';
		str += '}';
		str += 'dojo.connect(box'+data.id+',"onChange","box'+data.id+'OnChanged");';
		/*str += 'dojo.connect(box'+data.id+',"onBlur","box'+data.id+'OnBlurred");';*/
	}
	str += '</script>';
	str += ']]></item>';
	this._addItem(str,pos);
}

dhtmlXToolbarObject.prototype.addInputBox = function(id,pos,value,width,title) {
	var str = '<item id="'+id+'" type="buttonInput" value="'+value+'" width="'+width+'" title="'+title+'" text="'+title+' " />';
	this._addItem(str,pos);
}
