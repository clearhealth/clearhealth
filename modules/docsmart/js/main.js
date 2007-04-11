var DocSmart = {
	refreshTarget: 'content',
	uploadTargetName: 'uploadTarget',
	uploadCompleteCallback: 'new Effect.Fade("uploadStatus", {})'
}

function doUploadComplete(url) {
	IFrameDoc = frames[DocSmart.uploadTargetName].document;
	try {
		if(IFrameDoc.body.innerHTML != "") {
			if($('uploadStatusMessage')) {
				$('uploadStatusMessage').innerHTML = 'Upload is complete';
			}
			setTimeout(function(){
				eval(DocSmart.uploadCompleteCallback);
				if(url) {
					HTML_AJAX.replace(DocSmart.refreshTarget, url);
				}
			}, 1000);
			return true;
		}	
	}catch(e) {
		alert(e);
	}	
	setTimeout(function(){ 
		doUploadComplete(url); 
	}, 1000);
}

function doUpload(form) {
	if(!clni_validate(form)) {
		return false;
	}
	try {
		IFrameDoc = frames[DocSmart.uploadTargetName].document;
		IFrameDoc.body.innerHTML = "";
	}catch(e) {
		return;
	}
	new Effect.Appear('uploadStatus', {});
	if($('uploadStatusMessage')) {
		$('uploadStatusMessage').innerHTML = 'Uploading ...';
	}
	doUploadComplete(form.getAttribute('onCompleteUrl'));
}

function showForm(type) {
	initializeForm(type);
	new Effect.Highlight('folderForm');
}

function initializeForm(type) {
	if(type == 'edit') {
		$('folderFormLegend').innerHTML='Edit folder'; 
		$('folderName').value = folderName;
		$('webDavName').value = webDavName;		
		$('folderId').value = folderId;
		$('treeId').value = folderTreeId;
		for(i=0;i<$('folderParent').options.length;i++) {
			if($('folderParent').options[i].value == parentId) {
				$('folderParent').selectedIndex = i;
			}
		}
	}
	if(type == 'add') {
		$('folderFormLegend').innerHTML='Add folder';
		$('folderName').value = '';
		$('webDavName').value = '';		
		$('folderId').value = '';
		$('treeId').value = '';
		
		for(i=0;i<$('folderParent').options.length;i++) {
			if($('folderParent').options[i].value == folderTreeId) {
				$('folderParent').selectedIndex = i;
			}
		}
	}	
}

function changeSatatus(collection, element) {
	for(i=0;i<collection.length;i++) {
		if(collection[i] == element || collection[i].getAttribute('type') != "checkbox") {
			continue;
		}
		collection[i].checked = element.checked;
	}
}

function doRemoveStorables(form) {
	if(!confirm('Do you realy want delete it?')) {
		return false;
	}
	HTML_AJAX.grab(form.getAttribute('action') + Form.serialize(form));
	collection = form.getElementsByTagName('input');
	for(i=0;i<collection.length;i++) {
		if(collection[i].getAttribute('id') == 'bulkChecker' || collection[i].getAttribute('type') != "checkbox" || !collection[i].checked) {
			continue;
		}
		e = collection[i].parentNode;
		while(e.tagName != "TR") {
			e = e.parentNode;
		}
		new Effect.Fade(e);
	}	
	return false;	
}