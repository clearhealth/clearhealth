//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/

function eXcell_acheck(cell){try{this.cell = cell;this.grid = this.cell.parentNode.grid;this.cell.obj = this}catch(er){};this.changeState = function(){if ((!this.grid.isEditable)||(this.cell.parentNode._locked)||(this.isDisabled())) return;if(this.grid.callEvent("onEditCell",[0,this.cell.parentNode.idd,this.cell._cellIndex])!=false){this.val = this.getValue()
 if(this.val=="1")this.setValue("<checkbox state='false'>")
 else
 this.setValue("<checkbox state='true'>")
 
 this.cell.wasChanged=true;this.grid.callEvent("onEditCell",[1,this.cell.parentNode.idd,this.cell._cellIndex]);this.grid.callEvent("onCheckbox",[this.cell.parentNode.idd,this.cell._cellIndex,(this.val!='1')])}else{this.editor=null}};this.getValue = function(){try{return this.cell.chstate.toString()}catch(er){return null}};this.isCheckbox = function(){return true};this.isChecked = function(){if(this.getValue()=="1")
 return true;else
 return false};this.setChecked = function(fl){this.setValue(fl.toString())
 };this.detach = function(){return this.val!=this.getValue()};this.drawCurrentState=function(){if (this.cell.chstate==1)return "<div onclick='(new eXcell_acheck(this.parentNode)).changeState();(arguments[0]||event).cancelBubble=true;' style='cursor:pointer;font-weight:bold;text-align:center;'><img height='13px' src='"+this.grid.imgURL+"green.gif'>&nbsp;Yes</div>";else
 return "<div onclick='(new eXcell_acheck(this.parentNode)).changeState();(arguments[0]||event).cancelBubble=true;' style='cursor:pointer;text-align:center;'><img height='13px' src='"+this.grid.imgURL+"red.gif'>&nbsp;No</div>"}};eXcell_acheck.prototype = new eXcell;eXcell_acheck.prototype.setValue = function(val){val=(val||"").toString();if(val.indexOf("1")!=-1 || val.indexOf("true")!=-1){val = "1";this.cell.chstate = "1"}else{val = "0";this.cell.chstate = "0"
 };var obj = this;this.setCValue(this.drawCurrentState(),this.cell.chstate)};//(c)dhtmlx ltd. www.dhtmlx.com
//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/