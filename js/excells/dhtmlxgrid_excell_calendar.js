//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
document.write("<script src='"+_js_prefix+"calendar/YAHOO.js'></script>");document.write("<script src='"+_js_prefix+"calendar/event.js'></script>");document.write("<script src='"+_js_prefix+"calendar/calendar.js'></script>");document.write("<script src='"+_js_prefix+"calendar/calendar_init.js'></script>");document.write("<link rel='stylesheet' type='text/css' href='"+_js_prefix+"calendar/calendar.css'></link>");function eXcell_calendar(cell){try{this.cell = cell;this.grid = this.cell.parentNode.grid}catch(er){};this.edit = function(){if (!window._grid_calendar)_grid_calendar_init();var arPos = this.grid.getPosition(this.cell);var pval=this._date2str2(this.cell.val||new Date());window._grid_calendar.render(arPos[0],arPos[1]+this.cell.offsetHeight,this,pval);var zFunc=function(e){(e||event).cancelBubble=true};dhtmlxEvent(window._grid_calendar.table.parentNode.parentNode,"click",zFunc);this.cell._cediton=true;this.val=this.cell.val};this.getValue = function(){if (this.cell.val)return this._date2str2(this.cell.val);return this.cell.innerHTML.toString()._dhx_trim()
 };this.getContent = function(){return this.cell.innerHTML.toString()._dhx_trim()
 };this.detach = function(){var z=window._grid_calendar.getSelectedDates()[0];window._grid_calendar.hide();if (this._skip_detach)return;if (!z.getFullYear()) return;this.cell.val=new Date(z.valueOf());this.setCValue(this._date2str(z),z);if (!this.val)return true;return (z.valueOf())!=(this.val.valueOf());return false};this._2dg=function(val){if (val.toString().length==1)
 return ("0"+val.toString());return val};this._date2str2=function(z){return ("m/d/y").replace("m",this._2dg((z.getMonth()*1+1))).replace("d",this._2dg(z.getDate())).replace("y",this._2dg((z.getFullYear()*1)))};this._date2str=function(z){return (this.grid._dtmask||"m/d/y").replace("m",this._2dg((z.getMonth()*1+1))).replace("d",this._2dg(z.getDate())).replace("y",this._2dg((z.getFullYear()*1)))}};eXcell_calendar.prototype = new eXcell;eXcell_calendar.prototype.setValue = function(val){if(!val || val.toString()._dhx_trim()=="")
 val="";this.cell.val=new Date(val.toString());if ((this.cell.val=="NaN")||(this.cell.val=="Invalid Date")){this.cell.val="";this.setCValue("&nbsp;",0)}else
 this.setCValue(this._date2str(this.cell.val),this.cell.val)};
//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/