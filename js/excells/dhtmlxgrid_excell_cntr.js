//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/


function eXcell_cntr(cell){this.cell = cell;this.grid = this.cell.parentNode.grid;if (!this.grid._ex_cntr_ready && !this._realfake){this.grid._ex_cntr_ready=true;if (this.grid._h2)this.grid.attachEvent("onOpenEn",function(id){this.resetCounter(0)});this.grid.attachEvent("onBeforeSorting",function(){var that=this;window.setTimeout(function(){if (that._fake && !that._realfake)that._fake.resetCounter(0);else
 that.resetCounter(0)},1)
 return true})};this.edit = function(){};this.getValue = function(){return this.cell.innerHTML};this.setValue = function(val){this.cell.style.paddingRight = "2px";var cell=this.cell;window.setTimeout(function(){if (!cell.parentNode)return;var val=cell.parentNode.rowIndex;if (cell.parentNode.grid.currentPage)val=val*1+(cell.parentNode.grid.currentPage-1)*cell.parentNode.grid.rowsBufferOutSize;if (val<0 || cell.parentNode.grid._srnd)val=cell.parentNode.grid.rowsBuffer._dhx_find(cell.parentNode)+1;cell.innerHTML = val;if (cell.parentNode.grid._fake && cell.parentNode.grid._fake.rowsAr[cell.parentNode.idd])cell.parentNode.grid._fake.cells(cell.parentNode.idd,cell._cellIndex).setCValue(val);cell=null},100)}};dhtmlXGridObject.prototype.resetCounter=function(ind){if (this._fake && !this._realfake && ind < this._fake._cCount)this._fake.resetCounter(ind)
 var i=0;if (this.currentPage)i=(this.currentPage-1)*this.rowsBufferOutSize;for (i=0;i<this.rowsBuffer.length;i++)if (this.rowsBuffer[i].tagName == "TR")this.rowsAr[this.rowsBuffer[i].idd].cells[ind].innerHTML=i+1};eXcell_cntr.prototype = new eXcell;


//v.2.0 build 81009

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/