//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function eXcell_grid(cell){if (cell){this.cell = cell;this.grid = this.cell.parentNode.grid;if (!this.grid._sub_grids)return;this._sub=this.grid._sub_grids[cell._cellIndex];if (!this._sub)return;this._sindex=this._sub[1];this._sub=this._sub[0]};this.getValue = function(){return this.cell._val};this.setValue = function(val){this.cell._val=val;if (this._sub.getRowById(val)) {val=this._sub.cells(val,this._sindex);if (val)val=val.getValue();else val=""};this.setCValue((val||"&nbsp;"),val)};this.edit = function(){this._sub.entBox.style.display='block';var arPos = this.grid.getPosition(this.cell);this._sub.entBox.style.top=arPos[1]+"px";this._sub.entBox.style.left=arPos[0]+"px";this._sub.entBox.style.position="absolute";this._sub.setSizes();var a=this.grid.editStop;this.grid.editStop=function(){};if (this._sub.getRowById(this.cell._val)) 
 this._sub.setSelectedRow(this.cell._val);this._sub.setActive(true)
 
 this.grid.editStop=a};this.detach=function(){var old=this.cell._val;this._sub.entBox.style.display='none';if (this._sub.getSelectedId()===null) return false;this.setValue(this._sub.getSelectedId());this.grid.setActive(true)
 return this.cell._val!=old}};eXcell_grid.prototype = new eXcell;dhtmlXGridObject.prototype.setSubGrid=function(grid,s_index,t_index){if (!this._sub_grids)this._sub_grids=[];this._sub_grids[s_index]=[grid,t_index];grid.entBox.style.display="none";var that=this;grid.attachEvent("onRowSelect",function(id){that.editStop();return true});grid._chRRS=false};//(c)dhtmlx ltd. www.dhtmlx.com
//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/