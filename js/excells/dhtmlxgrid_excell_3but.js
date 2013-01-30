//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/

function eXcell_3but(cell){this.cell = cell;this.grid = this.cell.parentNode.grid;this.edit = function(){};this.isDisabled = function(){return true};this.detach = function(){};this.setValue=function(val){this.cell.val=val;this.cell.innerHTML="<input type='button' value='1'/><input type='button' value='2'/><input type='button' value='3'/>";this.cell.childNodes[0].onclick=function(){a3but_f1(this.parentNode.parentNode.idd,this.parentNode._cellIndex)};this.cell.childNodes[1].onclick=function(){a3but_f2(this.parentNode.parentNode.idd,this.parentNode._cellIndex)};this.cell.childNodes[2].onclick=function(){a3but_f3(this.parentNode.parentNode.idd,this.parentNode._cellIndex)}};this.getValue=function(){return (this.cell.val||"")}};eXcell_3but.prototype = new eXcell;




//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/