/*****************************************************************************
*       menu.js
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

djConfig.usePlainJson=true;

function submitmenumanagerForm() {
    dojo.xhrPost ({
        url: getBaseUrl() + "/menu-manager.raw/edit-process",
        form: 'menumanager',
        content: {
            siteSection: siteSection,
        },
        load: function(data){
            alert(__(data));
            populateMenuTree();
        },
        error: function (error) {
    	    console.error ('Error: ', error);
        }
    });
}


function updateSiteSection(val) {
    if (val == undefined) {
        val = dojo.byId('chSiteSection').value;
    }
    siteSection = val;
}

function updateConnectionContent(val) {
    if (val == undefined) {
        val = dojo.byId('menuManager-type').value;
    }
    var menuId = dojo.byId('menuManager-menuId').value;
    connectionType = val;
    ajaxGet("connection-content", "chConnectionContent", 
            {connectionType: val, menuId: menuId});
}

updateSiteSection();
updateConnectionContent();
