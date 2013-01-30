//v.1.2 build 80512

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function dhx_init_tabbars(){var z=document.getElementsByTagName("div");for (var i=0;i<z.length;i++)if(z[i].className.indexOf("dhtmlxTabBar")!=-1){var n=z[i];var id=n.id;var k=new Array();for (var j=0;j<n.childNodes.length;j++)if (n.childNodes[j].tagName && n.childNodes[j].tagName!="!")k[k.length]=n.childNodes[j];var w=new dhtmlXTabBar(id,n.getAttribute("mode")||"top",n.getAttribute("tabheight")||20);window[id]=w;acs=n.getAttribute("onbeforeinit");if (acs)eval(acs);if (n.getAttribute("enableForceHiding")) w.enableForceHiding(true);w.setImagePath(n.getAttribute("imgpath"));var acs=n.getAttribute("margin");if (acs!=null)w._margin=acs;acs=n.getAttribute("align");if (acs)w._align=acs;acs=n.getAttribute("hrefmode");if (acs)w.setHrefMode(acs);acs=n.getAttribute("offset");if (acs!=null)w._offset=acs;acs=n.getAttribute("tabstyle");if (acs!=null)w.setStyle(acs);acs=n.getAttribute("select");var clrs=n.getAttribute("skinColors");if (clrs)w.setSkinColors(clrs.split(",")[0],clrs.split(",")[1]);for (var j=0;j<k.length;j++){var m=k[j];m.parentNode.removeChild(m)
 w.addTab(m.id,m.getAttribute("name"),m.getAttribute("width"),null,m.getAttribute("row"));var href=m.getAttribute("href");if (href)w.setContentHref(m.id,href);else w.setContent(m.id,m);if ((!w._dspN)&&(m.style.display=="none"))
 m.style.display=""};if (k.length)w.setTabActive(acs||k[0].id);acs=n.getAttribute("oninit");if (acs)eval(acs)}};dhtmlxEvent(window,"load",dhx_init_tabbars);




//v.1.2 build 80512

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/