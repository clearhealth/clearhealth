//v.1.2 build 80512

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/




function dhtmlXTabBar(parentObject,mode,height)
{this._isIE7s=((_isIE)&&window.XMLHttpRequest&&(document.compatMode != "BackCompat"));mode=mode||"top";this._mode=mode+"/";this._eczF=true;if (_isIE)this.preventIECashing(true);if (typeof(parentObject)!="object")
 this.entBox=document.getElementById(parentObject);else
 this.entBox=parentObject;this.width = this.entBox.getAttribute("width") || this.entBox.style.width || (window.getComputedStyle?window.getComputedStyle(this.entBox,null)["width"]:(this.entBox.currentStyle?this.entBox.currentStyle["width"]:0));this.height = this.entBox.getAttribute("height") || this.entBox.style.height || (window.getComputedStyle?window.getComputedStyle(this.entBox,null)["height"]:(this.entBox.currentStyle?this.entBox.currentStyle["height"]:0));if (((this.width||"").indexOf("%")!=-1)||((this.width||"").indexOf("%")!=-1))
 this.enableAutoReSize(true,true);if ((!this.width)||(this.width=="auto")||(this.width.indexOf("%")!=-1)||(parseInt(this.width)==0))
 this.width=this.entBox.offsetWidth+"px";if ((!this.height)||(this.height.indexOf("%")!=-1)||(this.height=="auto"))
 this.height=this.entBox.offsetHeight+"px";this.activeTab = null;this.tabsId = new Object();this._align="left";this._offset=5;this._margin=1;this._height=parseInt(height||20);this._bMode=(mode=="right"||mode=="bottom");this._tabSize='150';this._content=new Array();this._tbst="win_text";this._styles={winDflt:["p_left.gif","p_middle.gif","p_right.gif","a_left.gif","a_middle.gif","a_right.gif","a_middle.gif",3,3,6,"#F4F3EE","#F0F8FF",false],
 winScarf:["with_bg/p_left.gif","with_bg/p_middle.gif","with_bg/p_right_skos.gif","with_bg/a_left.gif","with_bg/a_middle.gif","with_bg/a_right_skos.gif","with_bg/p_middle_over.gif",3,18,6,false,false,false],
 winBiScarf:["with_bg/p_left_skos.gif","with_bg/p_middle.gif","with_bg/p_right_skos.gif","with_bg/a_left_skos.gif","with_bg/a_middle.gif","with_bg/a_right_skos.gif","with_bg/p_middle_over.gif",18,18,6,false,false,false],
 winRound:["circuses/p_left.gif","circuses/p_middle.gif","circuses/p_right.gif","circuses/a_left.gif","circuses/a_middle.gif","circuses/a_right.gif","circuses/p_middle_over.gif",10,10,6,false,false,false],
 silver:["silver/p_left.gif","silver/p_middle.gif","silver/p_right.gif","silver/a_left.gif","silver/a_middle.gif","silver/a_right.gif","silver/p_middle.gif",7,8,6,"#F4F3EE","#F0F8FF","white"],
 modern:["modern/p_left.gif","modern/p_middle.gif","modern/p_right.gif","modern/a_left.gif","modern/a_middle.gif","modern/a_right.gif","modern/p_middle_over.gif",5,5,6,false,false,"white"]


 };this._createSelf(mode=="right"||mode=="left");this.setStyle("winDflt");this._TabCloseButton = false;this._TabCloseButtonSrc = 'close.png';this._enableAutoRowAdd = false;return this};dhtmlXTabBar.prototype.setOffset = function(offset){this._offset=offset};dhtmlXTabBar.prototype.setAlign = function(align){if (align=="top")align="left";if (align=="bottom")align="right";this._align=align};dhtmlXTabBar.prototype.setMargin = function(margin){this._margin=margin};dhtmlXTabBar.prototype._createSelf = function(vMode)
{this._tabAll=document.createElement("DIV");this._tabZone=document.createElement("DIV");this._conZone=document.createElement("DIV");this.entBox.appendChild(this._tabAll);if (this._bMode){this._tabAll.appendChild(this._conZone);this._tabAll.appendChild(this._tabZone)}else

 {this._tabAll.appendChild(this._tabZone);this._tabAll.appendChild(this._conZone)};this._vMode=vMode;if (vMode){this._tabAll.className='dhx_tabbar_zoneV';this._setSizes=this._setSizesV;this._redrawRow=this._redrawRowV}else

 this._tabAll.className='dhx_tabbar_zone';if (this._bMode)this._tabAll.className+='B';this._tabZone.className='dhx_tablist_zone';this._conZone.className='dhx_tabcontent_zone';this._tabZone.onselectstart = function(){return false};this._tabAll.onclick = this._onClickHandler;this._tabAll.onmouseover = this._onMouseOverHandler;if (_isFF)this._tabZone.onmouseout = this._onMouseOutHandler;else
 this._tabZone.onmouseleave = this._onMouseOutHandler;this._tabAll.tabbar=this;this._lineA=document.createElement("div");this._lineA.className="dhx_tablist_line";this._lineA.style[vMode?"left":"top"]=(this._bMode?0:(this._height+2))+"px";this._lineA.style[vMode?"height":"width"]=parseInt(this[vMode?"height":"width"])+((_isIE && document.compatMode!="BackCompat")?2:0)+"px";if(vMode)this._conZone.style.height=parseInt(this.height)+"px";else

 this._conZone.style.width=parseInt(this.width)-(_isFF?2:0)+"px";this.rows=new Array();this.rowscount=1;this._createRow();this._setSizes()};dhtmlXTabBar.prototype._createRow = function(){var z=document.createElement("DIV");z.className='dhx_tabbar_row';this._tabZone.appendChild(z);z._rowScroller=document.createElement('DIV');z._rowScroller.style.display="none";z.appendChild(z._rowScroller);this.rows[this.rows.length]=z;if (this._vMode){z.style.width=this._height+3+"px";z.style.height=parseInt(this.height)+"px";if (!this._bMode)this.setRowSizesA();else
 this.setRowSizesB()}else

 {z.style.height=parseInt(this._height)+3+"px";z.style.width=parseInt(this.width)+((_isIE && document.compatMode!="BackCompat")?2:0)+"px"};z.appendChild(this._lineA)};dhtmlXTabBar.prototype._removeRow=function(row){row.parentNode.removeChild(row);var z=new Array();for (var i=0;i<this.rows.length;i++)if (this.rows[i]!=row)z[z.length]=this.rows[i];this.rows=z};dhtmlXTabBar.prototype._checkSizes = function(row){var count=parseInt(this._offset);for (var i=0;i<row.tabCount;i++){if (row.childNodes[i].style.display=="none")continue;count+=row.childNodes[i]._offsetSize+this._margin*1};return (row.offsetWidth<(count-this._margin*1))};dhtmlXTabBar.prototype._setSizes = function(){this._tabAll.height=this.height;this._tabAll.width=this.width;if (this._tabZone.childNodes.length)var z=this._tabZone.lastChild.offsetTop-this._tabZone.firstChild.offsetTop+this._height;else
 var z=this._height+(_isIE?5:0);var a=z-2;this._tabZone.style.height=(a>0?a:0)+"px";a=parseInt(this.height)-z-4;this._conZone.style.height=(a>0?a:0)+"px"};dhtmlXTabBar.prototype._setSizesV = function(){this._tabAll.height=this.height;this._tabAll.width=this.width;var z=this._height*this.rows.length;if (!this._bMode){this._tabZone.style.width=z+3+"px";this._conZone.style.width=parseInt(this.width)-(z+(_isFF?5:3))+"px";this._conZone.style.left= z+3+"px"}else{this._tabZone.style.width=z+3+"px";this._conZone.style.width=parseInt(this.width)-(z+3)+"px";this._tabZone.style.left=parseInt(this.width)-(z+3)+"px"};this._conZone.style.height=parseInt(this.height)-(_isFF?2:0)+"px";this._tabZone.style.height=parseInt(this.height)+"px"};dhtmlXTabBar.prototype._redrawRowV=function(row){var talign=this._align=="left"?"top":"bottom";var count=parseInt(this._offset);for (var i=0;i<row.tabCount;i++){if (row.childNodes[i].style.display=="none")continue;row.childNodes[i]._cInd=i;row.childNodes[i].style[talign]=count+"px";count+=row.childNodes[i]._offsetSize+parseInt(this._margin)};if ((row.offsetHeight<count-parseInt(this._margin))||(parseInt(row.childNodes[0].style[this._align=="left"?"top":"bottom"])<0))
 this._showRowScroller(row);else
 this._hideRowScroller(row)};dhtmlXTabBar.prototype._setTabTop=function(tab){if (!this._vMode){if (tab.parentNode!=this.rows[0])this._tabZone.insertBefore(tab.parentNode,this.rows[0])};var j=new Array();j[j.length]=tab.parentNode;for (var i=0;i<this.rows.length;i++)if (this.rows[i]!=tab.parentNode)j[j.length]=this.rows[i];this.rows=j;if (this._vMode)this.setRowSizesB();else this.setRowSizesC();this._lineA.parentNode.removeChild(this._lineA);this.rows[0].appendChild(this._lineA)};dhtmlXTabBar.prototype.setRowSizesA=function(){for (var i=0;i<this.rows.length;i++){this.rows[i].style.left=i*this._height+"px";this.rows[i].style.zIndex=5+i}};dhtmlXTabBar.prototype.setRowSizesB=function(){for (var i=this.rows.length-1;i>=0;i--){this.rows[i].style.left=i*this._height+"px";this.rows[i].style.zIndex=15-i}};dhtmlXTabBar.prototype.setRowSizesC=function(){for (var i=this.rows.length-1;i>=0;i--){this.rows[i].style.zIndex=15-i}};dhtmlXTabBar.prototype._initScroller = function(row){var z=row._rowScroller;if (this._vMode)z.innerHTML="<img src='"+this._imgPath+"scrl_t.gif' style='display:block;'><img src='"+this._imgPath+"scrl_b.gif'>";else

 z.innerHTML="<img src='"+this._imgPath+"scrl_l.gif'><img src='"+this._imgPath+"scrl_r.gif'>";if (this._align=="left"){z.childNodes[1].onclick=this._scrollRight;z.childNodes[0].onclick=this._scrollLeft}else
 {z.childNodes[1].onclick=this._scrollLeft;z.childNodes[0].onclick=this._scrollRight};z.className='dhx_tablist_scroll';z._init=1};dhtmlXTabBar.prototype._scrollLeft=function(){var that=this.parentNode.parentNode.parentNode.parentNode.tabbar;var row=this.parentNode.parentNode;if (!row.scrollIndex)row.scrollIndex=0;row.scrollIndex--;if (row.scrollIndex<0){row.scrollIndex=0;return};var shift=row.childNodes[row.scrollIndex]._offsetSize+that._margin*1;that._offset+=shift;that._redrawRow(row);return shift};dhtmlXTabBar.prototype._scrollTo=function(tab){var that=this;var row=tab.parentNode;if (!row._rowScroller._init)this._initScroller(row);if (this._vMode)var z=parseInt(tab.style[that._align=="left"?"top":"bottom"])+tab._offsetSize-parseInt(that.height);else

 var z=parseInt(tab.style[that._align])+tab._offsetSize-parseInt(that.width);while (z>0)if (that._align=="left")z-=row._rowScroller.childNodes[1].onclick();else
 z-=row._rowScroller.childNodes[0].onclick();if (this._vMode)var z=parseInt(tab.style[that._align=="left"?"top":"bottom"])-tab._offsetSize;else

 var z=parseInt(tab.style[that._align])-tab._offsetSize;while (z<0)if (that._align=="left")z+=row._rowScroller.childNodes[0].onclick();else
 z+=row._rowScroller.childNodes[1].onclick()};dhtmlXTabBar.prototype._scrollRight=function(){var that=this.parentNode.parentNode.parentNode.parentNode.tabbar;var row=this.parentNode.parentNode;if (row.tabCount-row.scrollIndex<2)return;if (!row.scrollIndex)row.scrollIndex=0;var shift=row.childNodes[row.scrollIndex]._offsetSize+that._margin*1;that._offset-=shift;that._redrawRow(row);row.scrollIndex++;return shift};dhtmlXTabBar.prototype._hideRowScroller = function(row){row._rowScroller.style.display='none'};dhtmlXTabBar.prototype.enableScroll = function(mode){this._edscr=(!convertStringToBoolean(mode));if(this._edscr)for (var i=0;i<this.rows.length;i++)this._hideRowScroller(this.rows[i])};dhtmlXTabBar.prototype._showRowScroller = function(row){if (this._edscr)return;if (!row._rowScroller._init)this._initScroller(row);row._rowScroller.style.display='block';if (this._vMode){if (this._align=="left")row._rowScroller.style.top=row.scrollTop-38+parseInt(this.height)+"px";else
 row._rowScroller.style.top=row.scrollTop+4+"px";this._lineA.style.top=row.scrollLeft+"px"}else

 {if (this._align=="left")row._rowScroller.style.left=row.scrollLeft-38+parseInt(this.width)+"px";else
 row._rowScroller.style.left=row.scrollLeft+4+"px";this._lineA.style.left=row.scrollLeft+"px"}};dhtmlXTabBar.prototype._onMouseOverHandler=function(e)
{if (_isIE)var target = event.srcElement;else
 var target = e.target;target=this.tabbar._getTabTarget(target);if (!target){this.tabbar._hideHover(target);return};this.tabbar._showHover(target);(e||event).cancelBubble=true};dhtmlXTabBar.prototype._onMouseOutHandler=function(e)
{this.parentNode.tabbar._hideHover(null);return};dhtmlXTabBar.prototype._onClickHandler=function(e)
{if (_isIE)var target = event.srcElement;else
 var target = e.target;if (document.body.onclick)document.body.onclick(e);if (_isIE){document.body.fireEvent("onclick",event)}else {var cl=document.createEvent("MouseEvents")
 cl.initEvent("click", true, true)
 document.body.dispatchEvent(cl)
 };(e||event).cancelBubble=true;target=this.tabbar._getTabTarget(target);if (!target)return;this.tabbar._setTabActive(target);return false};dhtmlXTabBar.prototype._getTabTarget=function(t){if (!t)return null;while ((!t.className)||(t.className.indexOf("dhx_tab_element")==-1)){if ((t.className)&&(t.className.indexOf("dhx_tabbar_zone")!=-1)) return null;t=t.parentNode;if (!t)return null};return t};dhtmlXTabBar.prototype._redrawRow=function(row){var count=parseInt(this._offset);for (var i=0;i<row.tabCount;i++){if (row.childNodes[i].style.display=="none")continue;row.childNodes[i]._cInd=i;row.childNodes[i].style[this._align]=count+"px";count+=row.childNodes[i]._offsetSize+parseInt(this._margin)};if ((row.offsetWidth<count-parseInt(this._margin))||(parseInt(row.childNodes[0].style[this._align])<0))
 this._showRowScroller(row);else
 this._hideRowScroller(row)};dhtmlXTabBar.prototype.removeTab = function(tab,mode){var tab=this.tabsId[tab];if (!tab)return;if (this._content[tab.idd]){this._content[tab.idd].parentNode.removeChild(this._content[tab.idd]);this._content[tab.idd]=null};this._goToAny(tab,mode);var row=tab.parentNode;row.removeChild(tab);row.tabCount--;if ((row.tabCount==0)&&(this.rows.length>1))
 this._removeRow(row);delete this.tabsId[tab.idd];this._redrawRow(row)
 this._setSizes()};dhtmlXTabBar.prototype._goToAny=function(tab,mode){if ((this._lastActive)==tab)
 if (convertStringToBoolean(mode)) {if (null===this.goToPrevTab()) if (null===this.goToNextTab()) this._lastActive=null}else this._lastActive=null};dhtmlXTabBar.prototype.addTab = function(id, text, size, position, row){row=row||0;if (this.rows.length<=row)for (var i=this.rows.length;i<=row;i++)this._createRow();var z=this.rows[row].tabCount||0;if ((!position)&&(position!==0))
 position=z;var nss=this._getTabStyle(id);var tab=this._createTab(text, size, this._TabCloseButton, nss);tab.idd=id;this.tabsId[id] = tab;var close = tab.childNodes[2].getElementsByTagName('img')[0];if (this._TabCloseButton && close){var self = this;close.onclick = function() {if ((!self.dhx_tab_close)||(self.dhx_tab_close(id))) self.removeTab(id, true)}};this.rows[row].insertBefore(tab,this.rows[row].childNodes[position]);var prevCount = this.rows[row].tabCount;this.rows[row].tabCount=z+1;if (size=="*")this.adjustTabSize(tab);if ( this._enableAutoRowAdd && this._checkSizes(this.rows[row])) {this.rows[row].tabCount = prevCount;delete this.tabsId[id];this.rows[row].removeChild(tab);row++;position = this.rows[row] ? this.rows[row].tabCount : 0;this.addTab(id, text, size, position, row);return};this._redrawRow(this.rows[row]);this._setSizes()};dhtmlXTabBar.prototype.enableAutoRow=function(mode){this._enableAutoRowAdd=convertStringToBoolean(mode)};dhtmlXTabBar.prototype.normalize=function(limit,full){limit=limit||this._tabZone.offsetWidth;var tabs=[];for (var j=0;j<this.rows.length;j++)for (var i=0;i<this.rows[j].tabCount;i++)tabs[tabs.length]=this.rows[j].removeChild(this.rows[j].childNodes[0]);this._tabZone.innerHTML="";this.rows=[];this._createRow();var row=0;var size=this._offset*1;var sizes=[];this.rows[row].tabCount=0;for (var i=0;i<tabs.length;i++)if ((size + tabs[i]._offsetSize + this._margin*1)< limit){this.rows[row].insertBefore(tabs[i],this.rows[row].childNodes[this.rows[row].tabCount]);this.rows[row].tabCount++
 size+=tabs[i]._offsetSize + this._margin*1}else {sizes[row]=size;this._createRow();i--;row++;size=this._offset*1;this.rows[row].tabCount=0;continue};sizes[row]=size;if (full){for (var i=1;i<this.rows.length;i++)if (sizes[i]<sizes[0]){var tab=this.rows[i].childNodes[this.rows[i].tabCount-1];var size=tab._offsetSize+(sizes[0]-sizes[i]);this.adjustTabSize(tab,size)}};for (var i=0;i<this.rows.length;i++)this._redrawRow(this.rows[i]);this._setSizes()};dhtmlXTabBar.prototype._showHover=function(tab){if (tab._disabled)return;this._hideHover(tab);if (tab==this._lastActive)return;var nss=this._getTabStyle(tab.idd);switch (this._tbst){case "win_text":
 tab._lChild.style.backgroundImage='url('+this._imgPath+this._mode+nss[6]+')';break};this._lastHower=tab};dhtmlXTabBar.prototype._getTabStyle=function(id){var nss=this._styles[this._cstyle];if (nss["id_"+id])nss=nss["id_"+id];return nss};dhtmlXTabBar.prototype.setCustomStyle=function(id,color,scolor,css){var nss=this._styles[this._cstyle];if (nss["id_"+id])nss=nss["id_"+id];else {nss = ( nss["id_"+id] = ([]).concat(nss) )};nss[10]=color;nss[11]=scolor;nss[13]=css
 };dhtmlXTabBar.prototype._hideHover=function(tab){if ((!this._lastHower)||(this._lastHower==tab)||(this._lastHower==this._lastActive))
 return;var nss=this._getTabStyle(this._lastHower.idd);switch (this._tbst){case "win_text":
 this._lastHower._lChild.style.backgroundImage='url('+this._imgPath+this._mode+nss[1]+')';break};this._lastHower=null};dhtmlXTabBar.prototype._getTabById=function(tabId){return this.tabsId[tabId]};dhtmlXTabBar.prototype.setTabActive=function(tabId,mode){var tab=this._getTabById(tabId);if (tab)this._setTabActive(tab,(mode===false))};dhtmlXTabBar.prototype._setTabActive=function(tab,mode){if (tab==this._lastActive)return false;var nss=this._styles[this._cstyle]
 if (nss["id_"+tab.idd])nss=nss["id_"+tab.idd];if ((tab._disabled)||(tab.style.display=="none")) return false;if (((!mode)&& this._onsel)&&(!this._onsel(tab.idd,this._lastActive?this._lastActive.idd:null))) return false;tab.className=tab.className.replace(/dhx_tab_element_inactive/g,"dhx_tab_element_active");if (nss[11])tab.style.backgroundColor=nss[11];this._setContent(tab);this._deactivateTab();if (this._vMode){switch (this._tbst){case "win_text":
 tab._lChild.style.backgroundImage='url('+this._imgPath+this._mode+nss[4]+')';tab.childNodes[0].childNodes[0].src=this._imgPath+this._mode+nss[3];tab.childNodes[1].childNodes[0].src=this._imgPath+this._mode+nss[5];tab.style.height=parseInt(tab.style.height)+nss[9]+"px";tab._lChild.style.height=parseInt(tab._lChild.style.height)+nss[9]+"px";tab.style[this._align=="right"?"marginBottom":"marginTop"]="-3px"
 tab.style.width=this._height+3+"px";if (this._bMode)tab._lChild.style.width=this._height+3+"px";this._conZone.scrollLeft=tab._scrollState||0;break}}else

 {switch (this._tbst){case "win_text":
 tab._lChild.style.backgroundImage='url('+this._imgPath+this._mode+nss[4]+')';tab.childNodes[0].childNodes[0].src=this._imgPath+this._mode+nss[3];tab.childNodes[1].childNodes[0].src=this._imgPath+this._mode+nss[5];tab.style.width=parseInt(tab.style.width)+nss[9]+"px";tab._lChild.style.width=parseInt(tab._lChild.style.width)+nss[9]+"px";tab.style[this._align=="left"?"marginLeft":"marginRight"]="-3px"
 tab.style.height=this._height+3+"px";if (this._bMode)tab._lChild.style.height=this._height+3+"px";this._conZone.scrollTop=tab._scrollState||0;break}};if (this._bMode)this._setTabTop(tab);else

 this._setTabBottom(tab);this._scrollTo(tab);this._lastActive=tab;return true};dhtmlXTabBar.prototype._setTabBottom=function(tab){if (!this._vMode){if (tab.parentNode!=this.rows[this.rows.length-1])this._tabZone.appendChild(tab.parentNode)};var j=new Array();for (var i=0;i<this.rows.length;i++)if (this.rows[i]!=tab.parentNode)j[j.length]=this.rows[i];j[j.length]=tab.parentNode;this.rows=j;if (this._vMode)this.setRowSizesA();if (this._lineA.parentNode!=this.rows[this.rows.length-1]){this._lineA.parentNode.removeChild(this._lineA);this.rows[this.rows.length-1].appendChild(this._lineA)}};dhtmlXTabBar.prototype._createTab = function(text,size,IsCloseButton,nss){var tab=document.createElement("DIV");tab.className='dhx_tab_element dhx_tab_element_inactive';var thml="";if (size=="*"){size="10";tab.style.whiteSpace="nowrap"};switch (this._tbst){case 'text':
 thml=text;break;case 'win_text':

 if (this._vMode){thml='<div style="position:absolute;'+(this._bMode?"right":"left")+':0px;top:0px;height:'+nss[7]+'px;width:'+(this._height+3)+'px;"><img src="'+this._imgPath+this._mode+nss[0]+(((_isFF||this._isIE7s||_isOpera))?'" style="position:absolute;'+(this._bMode?"right":"left")+':1px;"':'"')+'></div>';thml+='<div style="position:absolute;'+(this._bMode?"right":"left")+':0px;bottom:0px;height:'+nss[8]+'px;width:'+(this._height+3)+'px;"><img src="'+this._imgPath+this._mode+nss[2]+(((_isFF||this._isIE7s||_isOpera))?'" style="position:absolute;'+(this._bMode?"right":"left")+':1px;"':'"')+'></div>';thml+='<div style="position:absolute;background-repeat: repeat-y;background-image:url('+this._imgPath+this._mode+nss[1]+');width:'+(this._height)+'px;left:0px;top:'+nss[7]+'px;height:'+(parseInt(size||this._tabSize)-nss[8]-nss[7]+"px")+(nss[13]?('" class="'+nss[13]):'')+'">'+text+'';if (IsCloseButton){thml+='<img src="'+(this._imgPath+this._TabCloseButtonSrc)+'" style="cursor:pointer;position:absolute;right:2px;bottom:4px;" onclick="" />'};thml+='</div>'}else

 {thml='<div style="position:absolute;'+(this._bMode?"bottom":"top")+':0px;left:0px;width:'+nss[7]+'px;height:'+(this._height+3)+'px;"><img src="'+this._imgPath+this._mode+nss[0]+((this._bMode&&(_isOpera||_isFF||this._isIE7s))?'" style="position:absolute;bottom:0px;"':'"')+'></div>';thml+='<div style="position:absolute;'+(this._bMode?"bottom":"top")+':0px;right:0px;width:'+nss[8]+'px;height:'+(this._height+3)+'px;"><img src="'+this._imgPath+this._mode+nss[2]+((this._bMode&&(_isOpera||_isFF||this._isIE7s))?'" style="position:absolute;bottom:0px;left:0px;"':'"')+'></div>';thml+='<div style="position:absolute;background-repeat: repeat-x;background-image:url('+this._imgPath+this._mode+nss[1]+');height:'+(this._height+(this._bMode?1:3))+'px;top:0px;left:'+nss[7]+'px;width:'+(parseInt(size||this._tabSize)-nss[8]-nss[7]+"px")+';">';if (IsCloseButton){thml+='<img src="'+(this._imgPath+this._TabCloseButtonSrc)+'" style="cursor:pointer;position:absolute;right:0px;top:4px;" onclick="" />'};thml+='<div style="padding-top:3px;" '+(nss[13]?('" class="'+nss[13]+'"'):'')+'>'+text+'</div>';thml+='</div>'};if (!nss[10])tab.style.backgroundColor='transparent';else tab.style.backgroundColor=nss[10];break};tab.innerHTML=thml;tab.style.padding="0px";tab._lChild=tab.childNodes[tab.childNodes.length-1];if (this._vMode){tab.style.height=parseInt(size||this._tabSize)+"px";tab.style.width=this._height+1+"px"}else

 {tab.style.width=parseInt(size||this._tabSize)+"px";tab.style.height=this._height+1+"px"};tab._offsetSize=parseInt(size||this._tabSize);return tab};dhtmlXTabBar.prototype.adjustTabSize=function(tab,size){var nss=this._getTabStyle(tab.idd);size=size||tab.scrollWidth+(this._TabCloseButton?50:20);tab.style[this._vMode?"height":"width"]=size+"px";tab.childNodes[2].style[this._vMode?"height":"width"]=size-nss[8]-nss[7]+"px";tab._offsetSize=size};dhtmlXTabBar.prototype.clearAll = function(){var z=this._conZone.style.backgroundColor;this._content=new Array();this.tabsId=new Array();this.rows=new Array();this._lastActive=null;this._lastHower=null;this.entBox.innerHTML="";this._glframe=null;this._createSelf(this._vMode);this.setStyle(this._cstyle);if (z)this._conZone.style.backgroundColor=z;this.enableContentZone(this._eczF)};dhtmlXTabBar.prototype.setImagePath = function(path){this._imgPath=path};dhtmlXTabBar.prototype.loadXMLString=function(xmlString,afterCall){this.XMLLoader=new dtmlXMLLoaderObject(this._parseXML,this,true,this.no_cashe);this.XMLLoader.waitCall=afterCall||0;this.XMLLoader.loadXMLString(xmlString)};dhtmlXTabBar.prototype.loadXML=function(file,afterCall){this.XMLLoader=new dtmlXMLLoaderObject(this._parseXML,this,true,this.no_cashe);this.XMLLoader.waitCall=afterCall||0;this.XMLLoader.loadXML(file)};dhtmlXTabBar.prototype._getXMLContent=function(node){var text="";for (var i=0;i<node.childNodes.length;i++){var z=node.childNodes[i];text+=(z.nodeValue===null?"":z.nodeValue)};return text};dhtmlXTabBar.prototype._parseXML=function(that,a,b,c,obj){that.clearAll();var selected="";if (!obj)obj=that.XMLLoader;var atop=obj.getXMLTopNode("tabbar");var arows = obj.doXPath("//row",atop);that._hrfmode=atop.getAttribute("hrefmode")||that._hrfmode;that._margin =atop.getAttribute("margin")||that._margin;that._align =atop.getAttribute("align") ||that._align;that._offset =atop.getAttribute("offset")||that._offset;var acs=atop.getAttribute("tabstyle");if (acs)that.setStyle(acs);acs=atop.getAttribute("skinColors");if (acs)that.setSkinColors(acs.split(",")[0],acs.split(",")[1]);for (var i=0;i<arows.length;i++){var atabs = obj.doXPath("./tab",arows[i]);for (var j=0;j<atabs.length;j++){var width=atabs[j].getAttribute("width");var name=that._getXMLContent(atabs[j]);var id=atabs[j].getAttribute("id");that.addTab(id,name,width,"",i);if (atabs[j].getAttribute("selected")) selected=id;if (that._hrfmode)that.setContentHref(id,atabs[j].getAttribute("href"));else


 for (var k=0;k<atabs[j].childNodes.length;k++){var cont=atabs[j].childNodes[k];if (cont.tagName=="content"){if (cont.getAttribute("id"))
 that.setContent(id,cont.getAttribute("id"));else
 that.setContentHTML(id,that._getXMLContent(cont))}}}};if (selected)that.setTabActive(selected);if (that.dhx_xml_end)that.dhx_xml_end(that)};dhtmlXTabBar.prototype.setOnLoadingEnd=function(func){if (typeof(func)=="function")
 this.dhx_xml_end=func;else
 this.dhx_xml_end=eval(func)};dhtmlXTabBar.prototype.setOnTabContentLoaded=function(func){if (typeof(func)=="function")
 this.dhx_tab_loaded=func;else
 this.dhx_tab_loaded=eval(func)};dhtmlXTabBar.prototype.setOnTabClose=function(func){if (typeof(func)=="function")
 this.dhx_tab_close=func;else
 this.dhx_tab_close=eval(func)};
dhtmlXTabBar.prototype.forceLoad=function(tabId,href){
	var tab=this.tabsId[tabId];
	if (href)this._hrefs[tabId]=href;
	this._content[tab.idd]._loaded=false;
	this._setContent(tab,(!this._lastActive || this._lastActive.idd!=tabId))
;
};
dhtmlXTabBar.prototype.setHrefMode=function(mode){this._hrfmode=mode};dhtmlXTabBar.prototype.setContentHref=function(id,href){
	if (!this._hrefs)this._hrefs=new Array();
	this._hrefs[id]=href;
	switch(this._hrfmode){
		case "iframe":
 			if (!this._glframe){
				var z=document.createElement("DIV");
				z.className="dhx_tabcontent_sub_zone";
				z.innerHTML="<iframe frameborder='0' width='100%' height='100%' src='"+this._imgPath+"blank.html'></iframe>";this._glframe=z.childNodes[0];this._conZone.appendChild(this._glframe)};
				return;
				break;
		case "iframes":
 		case "iframes-on-demand":
			var z=document.createElement("DIV");
			z.className="dhx_tabcontent_sub_zone";
			z.style.display='none';
			z.innerHTML="<iframe frameborder='0' width='100%' height='100%' src='"+((this._hrfmode=="iframes")?href:(this._imgPath+"blank.html"))+"'></iframe>";
			this.setContent(id,z);
			break;
		case "ajax":
		case "ajax-html":
			var z=document.createElement("DIV");
			z.className="dhx_tabcontent_sub_zone";
			this.setContent(id,z);
			break;
		};
			this._content[id]._loaded=false;
};
dhtmlXTabBar.prototype.tabWindow=function(tab_id){if (this._hrfmode.indexOf("iframe")==0)
 return (this._content[tab_id]?this._content[tab_id].childNodes[0].contentWindow:null)};dhtmlXTabBar.prototype._ajaxOnLoad=function(obj,a,b,c,loader){if (obj[0]._hrfmode=="ajax"){var z=loader.getXMLTopNode("content");var val=obj[0]._getXMLContent(z)}else var val=loader.xmlDoc.responseText;obj[0]._resolveContent(obj[1],val);obj[0].adjustSize();if (obj[0].dhx_tab_loaded)obj[0].dhx_tab_loaded(obj[1])};dhtmlXTabBar.prototype._resolveContent=function(id,val){var z=val.match(/<script[^>]*>[^\f]*?<\/script>/g);if (this._content[id]){this._content[id].innerHTML=val;if (z)for (var i=0;i<z.length;i++){if (window.execScript)window.execScript(z[i].replace(/<([\/]{0,1})script[^>]*>/g,""));else
 window.eval(z[i].replace(/<([\/]{0,1})script[^>]*>/g,""))}}};dhtmlXTabBar.prototype.setOnSelectHandler=function(func){if (typeof(func)=="function")
 this._onsel=func;else
 this._onsel=eval(func)};
dhtmlXTabBar.prototype.setContent=function(id,nodeId){
		if (typeof(nodeId)=="string")
			nodeId=document.getElementById(nodeId);
		if (this._content[id])
			this._content[id].parentNode.removeChild(this._content[id]);
		this._content[id]=nodeId;
		this._content[id]._loaded=true;
		if (nodeId.parentNode)nodeId.parentNode.removeChild(nodeId);
		nodeId.style.position="absolute";
		if (this._dspN){
			nodeId.style.display="none";
			nodeId.style.visibility="visible"
		}
		else{
			nodeId.style.visibility="hidden";
			nodeId.style.display="block"
		};
		nodeId.style.top="0px";
		nodeId.style.top="0px";
		this._conZone.appendChild(nodeId);
		if ((this._lastActive)&&(this._lastActive.idd==id)) { 
			this._setContent(this._lastActive);
		}
};
dhtmlXTabBar.prototype._setContent=function(tab,stelth){
	if (this._hrfmode)
		switch(this._hrfmode){
			case "iframe":
				this._glframe.src=this._hrefs[tab.idd];
				return;
				break;
			case "iframes":
			case "iframes-on-demand":
 				if ((this._hrfmode=="iframes-on-demand")&&(!this._content[tab.idd]._loaded)) {
				this._content[tab.idd].childNodes[0].src=this._hrefs[tab.idd];
				this._content[tab.idd]._loaded="true"};
				break;
		case "ajax":
		case "ajax-html":
			var z=this._content[tab.idd];
			if (!z._loaded){
				z.innerHTML="<div class='dhx_ajax_loader'><img src='"+this._imgPath+"loading.gif' />&nbsp;Loading...</div>";
			(new dtmlXMLLoaderObject(this._ajaxOnLoad,[this,tab.idd],true,this.no_cashe)).loadXML(this._hrefs[tab.idd]);
			z._loaded=true;
			};
			break;
		};
			if (!stelth){
				if ((this._lastActive)&&(this._content[this._lastActive.idd]))
 				  if (this._dspN)this._content[this._lastActive.idd].style.display='none';
				  else {
					this._content[this._lastActive.idd].style.visibility='hidden';
					this._content[this._lastActive.idd].style.zIndex=-1};
					if (this._content[tab.idd])
					  if (this._dspN)this._content[tab.idd].style.display='block';
					  else{
						this._content[tab.idd].style.visibility='';
						this._content[tab.idd].style.zIndex=2;
					}
				};
		this.adjustSize();
};
dhtmlXTabBar.prototype.setContentHTML=function(id,html){var z=document.createElement("DIV");z.className="dhx_tabcontent_sub_zone";z.innerHTML=html;this.setContent(id,z)};dhtmlXTabBar.prototype.setStyle=function(name){if (this._styles[name]){this._cstyle=name;if(this._styles[this._cstyle][12])this._conZone.style.backgroundColor=this._styles[this._cstyle][12]}};dhtmlXTabBar.prototype.enableContentZone=function(mode){this._eczF=convertStringToBoolean(mode);this._conZone.style.display=this._eczF?"":'none'};dhtmlXTabBar.prototype.enableForceHiding=function(mode){this._dspN=convertStringToBoolean(mode)};dhtmlXTabBar.prototype.setSkinColors=function(a_tab,p_tab,c_zone){this._styles[this._cstyle][10]=p_tab;this._styles[this._cstyle][11]=a_tab;this._conZone.style.backgroundColor=c_zone||a_tab};dhtmlXTabBar.prototype.getActiveTab=function(){if (this._lastActive)return this._lastActive.idd;return null};dhtmlXTabBar.prototype._deactivateTab=function(){if (!this._lastActive)return;var oss=this._styles[this._cstyle]
 if (oss["id_"+this._lastActive.idd])oss=oss["id_"+this._lastActive.idd];if (oss[10])this._lastActive.style.backgroundColor=oss[10];this._lastActive.className=this._lastActive.className.replace(/dhx_tab_element_active/g,"dhx_tab_element_inactive");if (this._vMode)switch (this._tbst){case "win_text":
 if (this._lastActive){this._lastActive._scrollState=this._conZone.scrollLeft;this._lastActive._lChild.style.backgroundImage='url('+this._imgPath+this._mode+oss[1]+')';this._lastActive.childNodes[0].childNodes[0].src=this._imgPath+this._mode+oss[0];this._lastActive.childNodes[1].childNodes[0].src=this._imgPath+this._mode+oss[2];this._lastActive.style.height=parseInt(this._lastActive.style.height)-oss[9]+"px";this._lastActive._lChild.style.height=parseInt(this._lastActive._lChild.style.height)-oss[9]+"px";this._lastActive.style[this._align=="right"?"marginBottom":"marginTop"]="0px"
 this._lastActive.style.width=this._height+1+"px";if (this._bMode)this._lastActive._lChild.style.width=this._height+1+"px"}}else
 switch (this._tbst){case "win_text":
 if (this._lastActive){this._lastActive._scrollState=this._conZone.scrollTop;this._lastActive._lChild.style.backgroundImage='url('+this._imgPath+this._mode+oss[1]+')';this._lastActive.childNodes[0].childNodes[0].src=this._imgPath+this._mode+oss[0];this._lastActive.childNodes[1].childNodes[0].src=this._imgPath+this._mode+oss[2];this._lastActive.style.width=parseInt(this._lastActive.style.width)-oss[9]+"px";this._lastActive._lChild.style.width=parseInt(this._lastActive._lChild.style.width)-oss[9]+"px";this._lastActive.style[this._align=="left"?"marginLeft":"marginRight"]="0px"
 this._lastActive.style.height=this._height+1+"px";if (this._bMode)this._lastActive._lChild.style.height=this._height+1+"px"}};this._lastActive=null};dhtmlXTabBar.prototype.goToNextTab=function(tab){var z=tab||this._lastActive;if (z){if (z.nextSibling.idd){if (!this._setTabActive(z.nextSibling))
 return this.goToNextTab(z.nextSibling);return z.nextSibling.idd}else
 if (z.parentNode.nextSibling){var arow=z.parentNode.nextSibling.childNodes[0];if (!this._setTabActive(arow))
 return this.goToNextTab(arow);return arow.idd}};return null};dhtmlXTabBar.prototype.goToPrevTab=function(tab){var z=tab||this._lastActive;if (z){if (z.previousSibling){if (!this._setTabActive(z.previousSibling))
 return this.goToPrevTab(z.previousSibling);return this._lastActive.idd}else
 if (z.parentNode.previousSibling){var arow=z.parentNode.previousSibling.childNodes;if ((!arow)||(!arow.tabCount)) return null;arow=arow[arow.tabCount-1];if (!this._setTabActive(arow))
 return this.goToPrevTab(arow);return arow.idd}};return null};dhtmlXTabBar.prototype.enableAutoSize=function(autoWidth,autoHeight){this._ahdj=convertStringToBoolean(autoHeight);this._awdj=convertStringToBoolean(autoWidth);if (this._awdj && this._ahdj)this._conZone.style.overflow='hidden';else this._conZone.style.overflow='auto'};dhtmlXTabBar.prototype.enableAutoReSize=function(mode){this._EARS=convertStringToBoolean(mode)
 if (this._EARS){this.entBox.style.overflow="hidden";if (arguments.length==1){if ((this.entBox.style.width||"").indexOf("%")==-1) this.entBox.style.width="100%";if ((this.entBox.style.height||"").indexOf("%")==-1) this.entBox.style.height="100%"};var self=this;if(this.entBox.addEventListener){if ((_isFF)&&(_FFrv<1.8))
 window.addEventListener("resize",function (){window.setTimeout(function(){self.adjustOuterSize()},10)},false);else
 window.addEventListener("resize",function (){if (self.adjustOuterSize)self.adjustOuterSize()},false)}else if (window.attachEvent)window.attachEvent("onresize",function(){if (self._resize_timer)window.clearTimeout(self._resize_timer);if (self.adjustOuterSize)self._resize_timer=window.setTimeout(function(){self.adjustOuterSize()},500)});if (this._lineA)this.adjustOuterSize()}};dhtmlXTabBar.prototype.setSize=function(width,height,contentZone){height=parseInt(height);width=parseInt(width);if (contentZone){if(!this._vMode)height+=20*this.rows.length;else
 
 width+=20*this.rows.length};this.height=height+"px";this.width=width+"px";this._lineA.style[this._vMode?"left":"top"]=(this._bMode?0:(this._height+2))+"px";this._lineA.style[this._vMode?"height":"width"]=this[this._vMode?"height":"width"];if(this._vMode){for (var i=0;i<this.rows.length;i++)this.rows[i].style.height=parseInt(this.height)+"px";this._conZone.style.height=height-(_isFF?2:0)+"px"}else

 {this._conZone.style.width=parseInt(this.width)-(_isFF?2:0)+"px";for (var i=0;i<this.rows.length;i++)this.rows[i].style.width=parseInt(this.width)+"px"};for (var i=0;i<this.rows.length;i++)this._redrawRow(this.rows[i]);this._setSizes()};dhtmlXTabBar.prototype.adjustOuterSize=function(){if (!this._EARS)return;this.setSize(this.entBox.offsetWidth,this.entBox.offsetHeight,false)};dhtmlXTabBar.prototype.adjustSize=function(){if ((!this._ahdj)&&(!this._awdj)) return;var flag=false;var x=0;var y=0;for (var id in this._content){var box=this._content[id];if (!box)continue;if ((this._ahdj)&&(box.scrollHeight>y)){y=box.scrollHeight+(_isIE?6:4);flag=true};if ((this._awdj)&&(box.scrollWidth>x)){x=box.scrollWidth+2;flag=true}};if (flag)this.setSize(x||this._conZone.offsetWidth,y||this._conZone.offsetHeight,true);if (this._EARS)this.adjustOuterSize()};dhtmlXTabBar.prototype.preventIECashing=function(mode){this.no_cashe = convertStringToBoolean(mode);if (this.XMLLoader)this.XMLLoader.rSeed=this.no_cashe};dhtmlXTabBar.prototype.hideTab = function(tab,mode){var tab=this.tabsId[tab];if (!tab)return;this._goToAny(tab,mode);tab.style.display='none';var row=tab.parentNode;this._redrawRow(row)};dhtmlXTabBar.prototype.showTab = function(tab){var tab=this.tabsId[tab];if (!tab)return;tab.style.display='block';var row=tab.parentNode;this._redrawRow(row)
};dhtmlXTabBar.prototype.enableTab = function(tab){var tab=this.tabsId[tab];if (!tab)return;tab._disabled=false;tab.className=(tab.className||"").replace(/dhx_tab_element_disabled/g,"")};dhtmlXTabBar.prototype.disableTab = function(tab,mode){var tab=this.tabsId[tab];if (!tab)return;this._goToAny(tab,mode);tab._disabled=true;tab.className+=" dhx_tab_element_disabled"};dhtmlXTabBar.prototype.setLabel = function(tab,value){var tab=this.tabsId[tab];if (!tab)return;switch(this._tbst){case 'text':
 tab.innerHTML=value;break;case 'win_text':
 tab.childNodes[2].childNodes[this._TabCloseButton?1:0].innerHTML=value;break}};dhtmlXTabBar.prototype.getLabel = function(tab){var tab=this.tabsId[tab];if (!tab)return;switch(this._tbst){case 'text':
 return tab.innerHTML;break;case 'win_text':
 return tab.childNodes[2].childNodes[this._TabCloseButton?1:0].innerHTML;break}};dhtmlXTabBar.prototype.detachTab=function(id) {var WindowName = this.getLabel(id);var tab=this.tabsId[id];if (!tab)return;var node = this._content[tab.idd];tab = this._getTabById(id);var tab_w = parseInt(tab.style.width);this.removeTab(id, true);node.style.position = '';var width = parseInt(this._conZone.style.width)+5;var height = parseInt(this._conZone.style.height)+25;var min_width = 100;var min_height = 50;width = width<min_width?min_width:width;height = height<min_height?min_height:height;var top = Math.ceil(window.offsetHeight/20-height/20);var left = Math.ceil(window.offsetWidth/20-width/20);var win = new dhtmlxWindow(420,300,width,height,WindowName,false);win._tab_w = tab_w;win.attachContent(node);return win};dhtmlXTabBar.prototype.enableTabCloseButton=function(bool) {this._TabCloseButton = convertStringToBoolean(bool)};
//v.1.2 build 80512

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
