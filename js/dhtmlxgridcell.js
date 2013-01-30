//v.2.0 build 81107

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/

function dhtmlXGridCellObject(obj){this.destructor=function(){this.cell.obj=null;this.cell=null;this.grid=null;this.base=null;return null};this.cell=obj;this.getValue=function(){if ((this.cell.firstChild)&&(this.cell.firstChild.tagName == "TEXTAREA"))
 return this.cell.firstChild.value;else
 return this.cell.innerHTML._dhx_trim()};this.getMathValue=function(){if (this.cell.original)return this.cell.original;else
 return this.getValue()};this.getFont=function(){arOut=new Array(3);if (this.cell.style.fontFamily)arOut[0]=this.cell.style.fontFamily

 if (this.cell.style.fontWeight == 'bold'||this.cell.parentNode.style.fontWeight == 'bold')arOut[1]='bold';if (this.cell.style.fontStyle == 'italic'||this.cell.parentNode.style.fontWeight == 'italic')arOut[1]+='italic';if (this.cell.style.fontSize)arOut[2]=this.cell.style.fontSize
 else
 arOut[2]="";return arOut.join("-")
 };this.getTextColor=function(){if (this.cell.style.color)return this.cell.style.color
 else
 return "#000000"};this.getBgColor=function(){if (this.cell.bgColor)return this.cell.bgColor
 else
 return "#FFFFFF"};this.getHorAlign=function(){if (this.cell.style.textAlign)return this.cell.style.textAlign;else if (this.cell.style.textAlign)return this.cell.style.textAlign;else
 return "left"};this.getWidth=function(){return this.cell.scrollWidth};this.setFont=function(val){fntAr=val.split("-");this.cell.style.fontFamily=fntAr[0];this.cell.style.fontSize=fntAr[fntAr.length-1]

 if (fntAr.length == 3){if (/bold/.test(fntAr[1]))
 this.cell.style.fontWeight="bold";if (/italic/.test(fntAr[1]))
 this.cell.style.fontStyle="italic";if (/underline/.test(fntAr[1]))
 this.cell.style.textDecoration="underline"}};this.setTextColor=function(val){this.cell.style.color=val};this.setBgColor=function(val){if (val == "")val=null;this.cell.bgColor=val};this.setHorAlign=function(val){if (val.length == 1){if (val == 'c')this.cell.style.textAlign='center'

 else if (val == 'l')this.cell.style.textAlign='left';else
 this.cell.style.textAlign='right'}else
 this.cell.style.textAlign=val
 };this.wasChanged=function(){if (this.cell.wasChanged)return true;else
 return false};this.isCheckbox=function(){var ch = this.cell.firstChild;if (ch&&ch.tagName == 'INPUT'){type=ch.type;if (type == 'radio'||type == 'checkbox')return true;else
 return false}else
 return false};this.isChecked=function(){if (this.isCheckbox()){return this.cell.firstChild.checked}};this.isDisabled=function(){return this.cell._disabled};this.setChecked=function(fl){if (this.isCheckbox()){if (fl != 'true'&&fl != 1)fl=false;this.cell.firstChild.checked=fl}};this.setDisabled=function(fl){if (fl != 'true'&&fl != 1)fl=false;if (this.isCheckbox()){this.cell.firstChild.disabled=fl;if (this.disabledF)this.disabledF(fl)};this.cell._disabled=fl}};dhtmlXGridCellObject.prototype={getAttribute: function(name){return this.cell._attrs[name]},
 setAttribute: function(name, value){this.cell._attrs[name]=value}};dhtmlXGridCellObject.prototype.setValue=function(val){if (( typeof (val)!= "number")&&(!val||val.toString()._dhx_trim() == "")){val="&nbsp;"
 this.cell._clearCell=true}else
 this.cell._clearCell=false;this.setCValue(val)};dhtmlXGridCellObject.prototype.getTitle=function(){return (_isIE ? this.cell.innerText : this.cell.textContent)};dhtmlXGridCellObject.prototype.setCValue=function(val, val2){this.cell.innerHTML=val};dhtmlXGridCellObject.prototype.setCTxtValue=function(val){this.cell.innerHTML="";this.cell.appendChild(document.createTextNode(val))};dhtmlXGridCellObject.prototype.setLabel=function(val){this.cell.innerHTML=val};dhtmlXGridCellObject.prototype.getMath=function(){if (this._val)return this.val;else
 return this.getValue()};function eXcell(){this.obj=null;this.val=null;this.changeState=function(){return false
 };this.edit=function(){this.val=this.getValue()
 };this.detach=function(){return false
 };this.getPosition=function(oNode){var oCurrentNode = oNode;var iLeft = 0;var iTop = 0;while (oCurrentNode.tagName != "BODY"){iLeft+=oCurrentNode.offsetLeft;iTop+=oCurrentNode.offsetTop;oCurrentNode=oCurrentNode.offsetParent};return new Array(iLeft, iTop)}};eXcell.prototype=new dhtmlXGridCellObject;function eXcell_ed(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid};this.edit=function(){this.cell.atag=((!this.grid.multiLine)&&(_isKHTML||_isMacOS||_isFF)) ? "INPUT" : "TEXTAREA";this.val=this.getValue();this.obj=document.createElement(this.cell.atag);this.obj.setAttribute("autocomplete", "off");this.obj.style.height=(this.cell.offsetHeight-(_isIE ? 4 : 2))+"px";this.obj.className="dhx_combo_edit";this.obj.wrap="soft";this.obj.style.textAlign=this.cell.style.textAlign;this.obj.onclick=function(e){(e||event).cancelBubble=true
 };this.obj.onmousedown=function(e){(e||event).cancelBubble=true
 };this.obj.value=this.val
 this.cell.innerHTML="";this.cell.appendChild(this.obj);if (_isFF){this.obj.style.overflow="visible";if ((this.grid.multiLine)&&(this.obj.offsetHeight >= 18)&&(this.obj.offsetHeight < 40)){this.obj.style.height="36px";this.obj.style.overflow="scroll"}};this.obj.onselectstart=function(e){if (!e)e=event;e.cancelBubble=true;return true};if (_isIE){this.obj.select();this.obj.value=this.obj.value};this.obj.focus()
 };this.getValue=function(){if ((this.cell.firstChild)&&((this.cell.atag)&&(this.cell.firstChild.tagName == this.cell.atag)))
 return this.cell.firstChild.value;if (this.cell._clearCell)return "";return this.cell.innerHTML.toString()._dhx_trim()};this.detach=function(){this.setValue(this.obj.value);return this.val != this.getValue()}};eXcell_ed.prototype=new eXcell;function eXcell_edtxt(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid};this.getValue=function(){if ((this.cell.firstChild)&&((this.cell.atag)&&(this.cell.firstChild.tagName == this.cell.atag)))
 return this.cell.firstChild.value;if (this.cell._clearCell)return "";return (_isIE ? this.cell.innerText : this.cell.textContent)};this.setValue=function(val){if (!val||val.toString()._dhx_trim() == ""){val=" ";this.cell._clearCell=true}else
 this.cell._clearCell=false;this.setCTxtValue(val)}};eXcell_edtxt.prototype=new eXcell_ed;function eXcell_ch(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid;this.cell.obj=this};this.disabledF=function(fl){if ((fl == true)||(fl == 1))
 this.cell.innerHTML=this.cell.innerHTML.replace("item_chk0.", "item_chk0_dis.").replace("item_chk1.",
 "item_chk1_dis.");else
 this.cell.innerHTML=this.cell.innerHTML.replace("item_chk0_dis.", "item_chk0.").replace("item_chk1_dis.",
 "item_chk1.")};this.changeState=function(){if ((!this.grid.isEditable)||(this.cell.parentNode._locked)||(this.isDisabled()))
 return;if (this.grid.callEvent("onEditCell", [
 0,
 this.cell.parentNode.idd,
 this.cell._cellIndex
 ])){this.val=this.getValue()

 if (this.val == "1")this.setValue("0")
 else
 this.setValue("1")

 this.cell.wasChanged=true;this.grid.callEvent("onEditCell", [
 1,
 this.cell.parentNode.idd,
 this.cell._cellIndex
 ]);this.grid.callEvent("onCheckbox", [
 this.cell.parentNode.idd,
 this.cell._cellIndex,
 (this.val != '1')
 ]);this.grid.callEvent("onCheck", [
 this.cell.parentNode.idd,
 this.cell._cellIndex,
 (this.val != '1')
 ])}else {this.editor=null}};this.getValue=function(){return this.cell.chstate ? this.cell.chstate.toString() : "0"};this.isCheckbox=function(){return true};this.isChecked=function(){if (this.getValue()== "1")
 return true;else
 return false};this.setChecked=function(fl){this.setValue(fl.toString())
 };this.detach=function(){return this.val != this.getValue()};this.edit=null};eXcell_ch.prototype=new eXcell;eXcell_ch.prototype.setValue=function(val){this.cell.style.verticalAlign="middle";if (val){val=val.toString()._dhx_trim();if ((val == "false")||(val == "0"))
 val=""};if (val){val="1";this.cell.chstate="1"}else {val="0";this.cell.chstate="0"
 };var obj = this;this.setCValue("<img src='"+this.grid.imgURL+"item_chk"+val
 +".gif' onclick='new eXcell_ch(this.parentNode).changeState();(arguments[0]||event).cancelBubble=true;'>",
 this.cell.chstate)};function eXcell_ra(cell){this.base=eXcell_ch;this.base(cell)
 this.grid=cell.parentNode.grid;this.disabledF=function(fl){if ((fl == true)||(fl == 1))
 this.cell.innerHTML=this.cell.innerHTML.replace("radio_chk0.", "radio_chk0_dis.").replace("radio_chk1.",
 "radio_chk1_dis.");else
 this.cell.innerHTML=this.cell.innerHTML.replace("radio_chk0_dis.", "radio_chk0.").replace("radio_chk1_dis.",
 "radio_chk1.")};this.changeState=function(mode){if (mode===false && this.getValue()==1) return;if ((!this.grid.isEditable)||(this.cell.parentNode._locked))
 return;if (this.grid.callEvent("onEditCell", [
 0,
 this.cell.parentNode.idd,
 this.cell._cellIndex
 ])!= false){this.val=this.getValue()

 if (this.val == "1")this.setValue("0",true)
 else
 this.setValue("1",true)
 this.cell.wasChanged=true;this.grid.callEvent("onEditCell", [
 1,
 this.cell.parentNode.idd,
 this.cell._cellIndex
 ]);this.grid.callEvent("onCheckbox", [
 this.cell.parentNode.idd,
 this.cell._cellIndex,
 (this.val != '1')
 ]);this.grid.callEvent("onCheck", [
 this.cell.parentNode.idd,
 this.cell._cellIndex,
 (this.val != '1')
 ])}else {this.editor=null}};this.edit=null};eXcell_ra.prototype=new eXcell_ch;eXcell_ra.prototype.setValue=function(val){this.cell.style.verticalAlign="middle";if (val){val=val.toString()._dhx_trim();if ((val == "false")||(val == "0"))
 val=""};if (val){if (!this.grid._RaSeCol)this.grid._RaSeCol=[];if (this.grid._RaSeCol[this.cell._cellIndex]){var z = this.grid.cells4(this.grid._RaSeCol[this.cell._cellIndex]);z.setValue("0");if (arguments[1])z.cell.wasChanged=true;if (this.grid.rowsAr[z.cell.parentNode.idd])this.grid.callEvent("onEditCell", [
 1,
 z.cell.parentNode.idd,
 z.cell._cellIndex
 ])};this.grid._RaSeCol[this.cell._cellIndex]=this.cell;val="1";this.cell.chstate="1"}else {val="0";this.cell.chstate="0"
 };this.setCValue("<img src='"+this.grid.imgURL+"radio_chk"+val+".gif' onclick='new eXcell_ra(this.parentNode).changeState(false);'>",
 this.cell.chstate)};function eXcell_txt(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid};this.edit=function(){this.val=this.getValue()
 this.obj=document.createElement("TEXTAREA");this.obj.className="dhx_textarea";this.obj.onclick=function(e){(e||event).cancelBubble=true
 };var arPos = this.grid.getPosition(this.cell);if (!this.cell._clearCell)this.obj.value=this.val;this.obj.style.display="";this.obj.style.textAlign=this.cell.style.textAlign;if (_isFF){var z_ff = document.createElement("DIV");z_ff.appendChild(this.obj);z_ff.style.overflow="auto";z_ff.className="dhx_textarea";this.obj.style.margin="0px 0px 0px 0px";this.obj.style.border="0px";this.obj=z_ff};document.body.appendChild(this.obj);this.obj.onkeydown=function(e){var ev = (e||event);if (ev.keyCode == 9){globalActiveDHTMLGridObject.entBox.focus();globalActiveDHTMLGridObject.doKey({keyCode: ev.keyCode,
 shiftKey: ev.shiftKey,
 srcElement: "0"
 });return false}};this.obj.style.left=arPos[0]+"px";this.obj.style.top=arPos[1]+this.cell.offsetHeight+"px";if (this.cell.offsetWidth < 200)var pw = 200;else
 var pw = this.cell.offsetWidth;this.obj.style.width=pw+(_isFF ? 18 : 16)+"px"

 if (_isFF){this.obj.firstChild.style.width=parseInt(this.obj.style.width)+"px";this.obj.firstChild.style.height=this.obj.offsetHeight-3+"px"};if (_isIE){this.obj.select();this.obj.value=this.obj.value};if (_isFF)this.obj.firstChild.focus();else {this.obj.focus()
 }};this.detach=function(){var a_val = "";if (_isFF)a_val=this.obj.firstChild.value;else
 a_val=this.obj.value;if (a_val == ""){this.cell._clearCell=true}else
 this.cell._clearCell=false;this.setValue(a_val);document.body.removeChild(this.obj);this.obj=null;return this.val != this.getValue()};this.getValue=function(){if (this.obj){if (_isFF)return this.obj.firstChild.value;else
 return this.obj.value};if (this.cell._clearCell)return "";if ((!this.grid.multiLine))
 return this.cell._brval||this.cell.innerHTML;else
 return this.cell.innerHTML.replace(/<br[^>]*>/gi, "\n")._dhx_trim()}};eXcell_txt.prototype=new eXcell;function eXcell_txttxt(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid};this.getValue=function(){if ((this.cell.firstChild)&&(this.cell.firstChild.tagName == "TEXTAREA"))
 return this.cell.firstChild.value;if (this.cell._clearCell)return "";if ((!this.grid.multiLine)&&this.cell._brval)
 return this.cell._brval;return (_isIE ? this.cell.innerText : this.cell.textContent)};this.setValue=function(val){this.cell._brval=val;if (!val||val.toString()._dhx_trim() == ""){val=" ";this.cell._clearCell=true}else
 this.cell._clearCell=false;this.setCTxtValue(val)}};eXcell_txttxt.prototype=new eXcell_txt;eXcell_txt.prototype.setValue=function(val){if (!val||val.toString()._dhx_trim() == ""){val="&nbsp;"
 this.cell._clearCell=true}else
 this.cell._clearCell=false;this.cell._brval=val;if ((!this.grid.multiLine))
 this.setCValue(val, val);else
 this.setCValue(val.replace(/\n/g, "<br/>"), val)};function eXcell_co(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid;this.combo=(this.cell._combo||this.grid.getCombo(this.cell._cellIndex));this.editable=true
 };this.shiftNext=function(){var z = this.list.options[this.list.selectedIndex+1];if (z)z.selected=true;this.obj.value=this.list.options[this.list.selectedIndex].text;return true};this.shiftPrev=function(){if (this.list.selectedIndex != 0){var z = this.list.options[this.list.selectedIndex-1];if (z)z.selected=true;this.obj.value=this.list.options[this.list.selectedIndex].text};return true};this.edit=function(){this.val=this.getValue();this.text=this.getText()._dhx_trim();var arPos = this.grid.getPosition(this.cell) 

 this.obj=document.createElement("TEXTAREA");this.obj.className="dhx_combo_edit";this.obj.style.height=(this.cell.offsetHeight-4)+"px";this.obj.wrap="soft";this.obj.style.textAlign=this.cell.style.textAlign;this.obj.onclick=function(e){(e||event).cancelBubble=true
 };this.obj.value=this.text
 this.obj.onselectstart=function(e){if (!e)e=event;e.cancelBubble=true;return true};var editor_obj = this;this.obj.onkeyup=function(e){var val = this.readonly ? String.fromCharCode((e||event).keyCode) : this.value;var c = editor_obj.list.options;for (var i = 0;i < c.length;i++)if (c[i].text.indexOf(val)== 0)
 return c[i].selected=true};this.list=document.createElement("SELECT");this.list.className='dhx_combo_select';this.list.style.width=this.cell.offsetWidth+"px";this.list.style.left=arPos[0]+"px";this.list.style.top=arPos[1]+this.cell.offsetHeight+"px";this.list.onclick=function(e){var ev = e||window.event;var cell = ev.target||ev.srcElement

 
 if (cell.tagName == "OPTION")cell=cell.parentNode;editor_obj.setValue(cell.value);editor_obj.editable=false;editor_obj.grid.editStop()};var comboKeys = this.combo.getKeys();var fl = false
 var selOptId = 0;for (var i = 0;i < comboKeys.length;i++){var val = this.combo.get(comboKeys[i])
 this.list.options[this.list.options.length]=new Option(val, comboKeys[i]);this.list.options[(this.list.options.length-1)].title=val;if (comboKeys[i] == this.val){selOptId=this.list.options.length-1;fl=true}};if (fl == false){this.list.options[this.list.options.length]=new Option(this.text, this.val === null ? "" : this.val);this.list.options[(this.list.options.length-1)].title=this.val === null ? "" : this.val;selOptId=this.list.options.length-1};document.body.appendChild(this.list) 
 this.list.size="6";this.cstate=1;if (this.editable){this.cell.innerHTML=""}else {this.obj.style.width="1px";this.obj.style.height="1px"};this.cell.appendChild(this.obj);this.list.options[selOptId].selected=true;if ((!_isFF)||(this.editable)){this.obj.focus();this.obj.focus()};if (!this.editable){this.obj.style.visibility="hidden";this.list.focus();this.list.onkeydown=function(e){e=e||window.event;editor_obj.grid.setActive(true)

 if (e.keyCode < 30)return editor_obj.grid.doKey({target: editor_obj.cell,
 keyCode: e.keyCode,
 shiftKey: e.shiftKey,
 ctrlKey: e.ctrlKey
 })
 }}};this.getValue=function(){return ((this.cell.combo_value == window.undefined) ? "" : this.cell.combo_value)};this.detach=function(){if (this.val != this.getValue()){this.cell.wasChanged=true};if (this.list.parentNode != null){if (this.editable){if (this.list.selectedIndex&&this.list.options[this.list.selectedIndex].text == this.obj.value)this.setValue(this.list.value)
 else
 this.setValue(this.obj.value)
 }else
 this.setValue(this.list.value)
 };if (this.list.parentNode)this.list.parentNode.removeChild(this.list);if (this.obj.parentNode)this.obj.parentNode.removeChild(this.obj);return this.val != this.getValue()}};eXcell_co.prototype=new eXcell;eXcell_co.prototype.getText=function(){return this.cell.innerHTML};eXcell_co.prototype.setValue=function(val){if (typeof (val)== "object"){var optCol = this.grid.xmlLoader.doXPath("./option", val);if (optCol.length)this.cell._combo=new dhtmlXGridComboObject();for (var j = 0;j < optCol.length;j++)this.cell._combo.put(optCol[j].getAttribute("value"),
 optCol[j].firstChild
 ? optCol[j].firstChild.data
 : "");val=val.firstChild.data};if ((val||"").toString()._dhx_trim() == "")
 val=null
 this.cell.combo_value=val;if (val !== null)this.setCValue((this.cell._combo||this.grid.getCombo(this.cell._cellIndex)).get(val)||val, val);else
 this.setCValue("&nbsp;", val)};function eXcell_coro(cell){this.base=eXcell_co;this.base(cell)
 this.editable=false};eXcell_coro.prototype=new eXcell_co;function eXcell_cotxt(cell){this.base=eXcell_co;this.base(cell)
};eXcell_cotxt.prototype=new eXcell_co;eXcell_cotxt.prototype.getText=function(){return (_isIE ? this.cell.innerText : this.cell.textContent)};eXcell_cotxt.prototype.setValue=function(val){if (typeof (val)== "object"){var optCol = this.grid.xmlLoader.doXPath("./option", val);if (optCol.length)this.cell._combo=new dhtmlXGridComboObject();for (var j = 0;j < optCol.length;j++)this.cell._combo.put(optCol[j].getAttribute("value"),
 optCol[j].firstChild
 ? optCol[j].firstChild.data
 : "");val=val.firstChild.data};if ((val||"").toString()._dhx_trim() == "")
 val=null

 if (val !== null)this.setCTxtValue((this.cell._combo||this.grid.getCombo(this.cell._cellIndex)).get(val)||val, val);else
 this.setCTxtValue(" ", val);this.cell.combo_value=val};function eXcell_corotxt(cell){this.base=eXcell_co;this.base(cell)
 this.editable=false};eXcell_corotxt.prototype=new eXcell_cotxt;function eXcell_cp(cell){try{this.cell=cell;this.grid=this.cell.parentNode.grid}catch (er){};this.edit=function(){this.val=this.getValue()
 this.obj=document.createElement("SPAN");this.obj.style.border="1px solid black";this.obj.style.position="absolute";var arPos = this.grid.getPosition(this.cell);this.colorPanel(4, this.obj)
 document.body.appendChild(this.obj);this.obj.style.left=arPos[0]+"px";this.obj.style.top=arPos[1]+this.cell.offsetHeight+"px"};this.toolDNum=function(value){if (value.length == 1)value='0'+value;return value};this.colorPanel=function(index, parent){var tbl = document.createElement("TABLE");parent.appendChild(tbl)
 tbl.cellSpacing=0;tbl.editor_obj=this;tbl.style.cursor="default";tbl.onclick=function(e){var ev = e||window.event
 var cell = ev.target||ev.srcElement;var ed = cell.parentNode.parentNode.parentNode.editor_obj
 ed.setValue(cell.style.backgroundColor)
 ed.grid.editStop()};var cnt = 256 / index;for (var j = 0;j <= (256 / cnt);j++){var r = tbl.insertRow(j);for (var i = 0;i <= (256 / cnt);i++){for (var n = 0;n <= (256 / cnt);n++){R=new Number(cnt*j)-(j == 0 ? 0 : 1)
 G=new Number(cnt*i)-(i == 0 ? 0 : 1)
 B=new Number(cnt*n)-(n == 0 ? 0 : 1)
 var rgb =
 this.toolDNum(R.toString(16))+""+this.toolDNum(G.toString(16))+""+this.toolDNum(B.toString(16));var c = r.insertCell(i);c.width="10px";c.innerHTML="&nbsp;";c.title=rgb.toUpperCase()
 c.style.backgroundColor="#"+rgb

 if (this.val != null&&"#"+rgb.toUpperCase()== this.val.toUpperCase()){c.style.border="2px solid white"
 }}}}};this.getValue=function(){return this.cell.firstChild.style ? this.cell.firstChild.style.backgroundColor : ""};this.getRed=function(){return Number(parseInt(this.getValue().substr(1, 2), 16))
 };this.getGreen=function(){return Number(parseInt(this.getValue().substr(3, 2), 16))
 };this.getBlue=function(){return Number(parseInt(this.getValue().substr(5, 2), 16))
 };this.detach=function(){if (this.obj.offsetParent != null)document.body.removeChild(this.obj);return this.val != this.getValue()}};eXcell_cp.prototype=new eXcell;eXcell_cp.prototype.setValue=function(val){this.setCValue("<div style='width:100%;height:"+(this.cell.offsetHeight-2)+";background-color:"+(val||"")
 +";border:0px;'>&nbsp;</div>",
 val)};function eXcell_img(cell){try{this.cell=cell;this.grid=this.cell.parentNode.grid}catch (er){};this.getValue=function(){if (this.cell.firstChild.tagName == "IMG")return this.cell.firstChild.src+(this.cell.titFl != null
 ? "^"+this.cell.tit
 : "");else if (this.cell.firstChild.tagName == "A"){var out = this.cell.firstChild.firstChild.src+(this.cell.titFl != null ? "^"+this.cell.tit : "");out+="^"+this.cell.lnk;if (this.cell.trg)out+="^"+this.cell.trg
 return out}}};eXcell_img.prototype=new eXcell;eXcell_img.prototype.getTitle=function(){return this.cell.tit
};eXcell_img.prototype.setValue=function(val){var title = val;if (val.indexOf("^")!= -1){var ar = val.split("^");val=ar[0]
 title=ar[1];if (ar.length > 2){this.cell.lnk=ar[2]

 if (ar[3])this.cell.trg=ar[3]
 };this.cell.titFl="1"};this.setCValue("<img src='"+this.grid.iconURL+(val||"")._dhx_trim()+"' border='0'>", val);if (this.cell.lnk){this.cell.innerHTML="<a href='"+this.cell.lnk+"' target='"+this.cell.trg+"'>"+this.cell.innerHTML+"</a>"
 };this.cell.tit=title};function eXcell_price(cell){this.base=eXcell_ed;this.base(cell)
 this.getValue=function(){if (this.cell.childNodes.length > 1)return this.cell.childNodes[1].innerHTML.toString()._dhx_trim()
 else
 return "0"}};eXcell_price.prototype=new eXcell_ed;eXcell_price.prototype.setValue=function(val){if (isNaN(parseFloat(val))){val=this.val||0};var color = "green";if (val < 0)color="red";this.setCValue("<span>$</span><span style='padding-right:2px;color:"+color+";'>"+val+"</span>", val)};function eXcell_dyn(cell){this.base=eXcell_ed;this.base(cell)
 this.getValue=function(){return this.cell.firstChild.childNodes[1].innerHTML.toString()._dhx_trim()
 }};eXcell_dyn.prototype=new eXcell_ed;eXcell_dyn.prototype.setValue=function(val){if (!val||isNaN(Number(val))){val=0};if (val > 0){var color = "green";var img = "dyn_up.gif"}else if (val == 0){var color = "black";var img = "dyn_.gif"}else {var color = "red";var img = "dyn_down.gif"};this.setCValue("<div style='position:relative;padding-right:2px;width:100%;overflow:hidden;'><img src='"+this.grid.imgURL+""+img
 +"' height='15' style='position:absolute;top:0px;left:0px;'><span style=' padding-left:20px;width:100%;color:"+color+";'>"+val
 +"</span></div>",
 val)};function eXcell_ro(cell){if (cell){this.cell=cell;this.grid=this.cell.parentNode.grid};this.edit=function(){};this.isDisabled=function(){return true}};eXcell_ro.prototype=new eXcell;function eXcell_ron(cell){this.cell=cell;this.grid=this.cell.parentNode.grid;this.edit=function(){};this.isDisabled=function(){return true};this.getValue=function(){return this.cell._clearCell?"":this.grid._aplNFb(this.cell.innerHTML.toString()._dhx_trim(), this.cell._cellIndex)}};eXcell_ron.prototype=new eXcell;eXcell_ron.prototype.setValue=function(val){if (val === 0){}else if (!val||val.toString()._dhx_trim() == ""){this.setCValue("&nbsp;");return this.cell._clearCell=true};this.setCValue(val?this.grid._aplNF(val, this.cell._cellIndex):"0");this.cell._clearCell=false};function eXcell_rotxt(cell){this.cell=cell;this.grid=this.cell.parentNode.grid;this.edit=function(){};this.isDisabled=function(){return true};this.setValue=function(val){if (!val||val.toString()._dhx_trim() == "")
 val=" ";this.setCTxtValue(val)};this.getValue=function(){if (this.cell._clearCell)return "";return (_isIE ? this.cell.innerText : this.cell.textContent)}};eXcell_rotxt.prototype=new eXcell;function dhtmlXGridComboObject(){this.keys=new dhtmlxArray();this.values=new dhtmlxArray();this.put=function(key, value){for (var i = 0;i < this.keys.length;i++){if (this.keys[i] == key){this.values[i]=value;return true}};this.values[this.values.length]=value;this.keys[this.keys.length]=key};this.get=function(key){for (var i = 0;i < this.keys.length;i++){if (this.keys[i] == key){return this.values[i]}};return null};this.clear=function(){this.keys=new dhtmlxArray();this.values=new dhtmlxArray()};this.remove=function(key){for (var i = 0;i < this.keys.length;i++){if (this.keys[i] == key){this.keys._dhx_removeAt(i);this.values._dhx_removeAt(i);return true}}};this.size=function(){var j = 0;for (var i = 0;i < this.keys.length;i++){if (this.keys[i] != null)j++};return j};this.getKeys=function(){var keyAr = new Array(0);for (var i = 0;i < this.keys.length;i++){if (this.keys[i] != null)keyAr[keyAr.length]=this.keys[i]};return keyAr};this.save=function(){this._save=new Array();for (var i = 0;i < this.keys.length;i++)this._save[i]=[
 this.keys[i],
 this.values[i]
 ]};this.restore=function(){if (this._save){this.keys[i]=new Array();this.values[i]=new Array();for (var i = 0;i < this._save.length;i++){this.keys[i]=this._save[i][0];this.values[i]=this._save[i][1]}}};return this};function Hashtable(){this.keys=new dhtmlxArray();this.values=new dhtmlxArray();return this};Hashtable.prototype=new dhtmlXGridComboObject;//(c)dhtmlx ltd. www.dhtmlx.com
//v.2.0 build 81107

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
