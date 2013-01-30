//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function eXcell_context(cell){if (cell){this.cell = cell;this.grid = this.cell.parentNode.grid;if (!this.grid._sub_context)return;this._sub=this.grid._sub_context[cell._cellIndex];if (!this._sub)return;this._sindex=this._sub[1];this._sub=this._sub[0]};this.getValue = function(){return _isIE?this.cell.innerText:this.cell.textContent};this.setValue = function(val){this.cell._val=val;val = this._sub.getItem(this.cell._val).textTag.innerHTML;this.setCValue((val||"&nbsp;"),val)};this.edit = function(){var arPos = this.grid.getPosition(this.cell);this._sub._del_table.style.left = (arPos[0]+this.cell.offsetWidth)+'px';this._sub._del_table.style.top = arPos[1]+'px';this._sub._del_table.style.position = 'absolute';this._sub.showBar();var a=this.grid.editStop;this.grid.editStop=function(){};this.grid.editStop=a};this.detach=function(){if (this.grid._sub_id != null){var old=this.cell._val;this.setValue(this.grid._sub_id);this.grid._sub_id = null;return this.cell._val!=old};this._sub.hideBar()}};eXcell_context.prototype = new eXcell;dhtmlXGridObject.prototype.setSubContext=function(ctx,s_index,t_index){var that=this;ctx.setOnClickHandler(function(id,value){that._sub_id = id;that.editStop();ctx.hideBar();return true});if (!this._sub_context)this._sub_context=[];this._sub_context[s_index]=[ctx,t_index];ctx.hideBar()};//(c)dhtmlx ltd. www.dhtmlx.com
//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/