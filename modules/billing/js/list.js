var deleteClaimUrl = '';
var data = {};
var legendColors = {};
function checkAll(box) {
	var claims = document.getElementById('list').getElementsByTagName('input');
	for(i=0;i<claims.length;i++) {
 		if (claims[i].type == "checkbox" && claims[i].name.substr(0,5) == "batch")  {
 			claims[i].checked = box.checked;
 		}
	}
}
function deleteClaim(identifier,id) {
	if (confirm("Delete claim '"+identifier+"'")) {
		window.location = deleteClaimUrl + "claim_id="+id;
	}
}
Behavior.register(
	"span.status",
	function(element) {
		var classes = element.className.split(' ');
		highlightRow(element,legendColors[classes[1]]);
	}
);

Behavior.register(
	".legendPopup span",
	function(element) {
		var h = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);
			target.parentNode.getElementsByTagName('div').item(0).style.display = 'block';
		}
		HTML_AJAX_Util.registerEvent(element,'mouseover',h);

		var h = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);
			target.parentNode.getElementsByTagName('div').item(0).style.display = 'none';
		}
		HTML_AJAX_Util.registerEvent(element,'mouseout',h);
		element.style.textAlign = 'center';
	}
);
Behavior.register(
	".extraInfo",
	function(element) {
		var h = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);
			try {
			updateInfoBox(target);
			} catch(e) {
				alert(HTML_AJAX_Util.quickPrint(e));
			}
		}
		HTML_AJAX_Util.registerEvent(element,'mouseover',h);
		var h2 = function(e) {
			document.getElementById('infoBox').style.display = 'none';
			clniUtil.centerElement(document.getElementById('infoBox'));	
		}
		HTML_AJAX_Util.registerEvent(element,'mouseout',h2);
	}
);

function updateInfoBox(target) {
	if (document.getElementById('infoBox').style.display == 'block') {
		return false;
	}
	clniUtil.centerElement(document.getElementById('infoBox'));	
	document.getElementById('infoBox').style.display = 'block';

	var id = target.parentNode.parentNode.index;

	if (data[id]) {
		document.getElementById('infoBox').innerHTML =
			document.getElementById('infoBoxTemplate').innerHTML.replace(/{\$([a-zA-Z0-9_]+)}/g,
			function(s,result) {
				if (data[id][result]) {
					return data[id][result]; 
				}
				return '';
			} 
		);
		var table = document.getElementById('infoBox').getElementsByTagName('table').item(0);

		for(var i = (table.rows.length-1); i > 0; i--) {
			if (table.rows[i].cells[1].innerHTML == '') {
				table.deleteRow(i);
			}
		}

		var claimlines = table.rows[1].cells[1].innerHTML.split('/');
		var cd = {};
		for(var i = 0; i < claimlines.length; i++) {
			var tmp = claimlines[i].split('|');
			for(var t = 0; t < tmp.length; t++) {
				if (tmp[0]) {
					var tmp2 = tmp[0].split('-');

					if (tmp2[1]) {
						cd[tmp2[0]] = '<b>'+tmp2[1]+'</b>';
					}
					if (tmp[1]) {
						cd[tmp2[0]] += ': '+tmp[1];
					}
				}
			}
		}
		var content = '';
		for(var i in cd) {
			content += '<div>'+cd[i]+'</div>';
		}
		table.rows[1].cells[1].innerHTML = content;
	}
}


function highlightRow(element,color) {
	var tr = clniUtil.findParentOfTagName(element,'tr');

	tr.cells[0].style.backgroundColor = color;
}

function setupLegend() {
	var rows = document.getElementById('legend').rows;
	for(var i =0; i < rows.length; i++) {
		legendColors[rows[i].cells[1].innerHTML] = rows[i].cells[0].style.backgroundColor;
	}
	if(HTML_AJAX_Util.getElementsByClassName('grid',document.getElementById('list'))[0]) {
	var tbody = HTML_AJAX_Util.getElementsByClassName('grid',document.getElementById('list'))[0].tBodies[0];
	for(var i = 0; i < tbody.rows.length; i++) {
		tbody.rows[i].index = i;
	}
	}
}

function addSelected(queueId) {
	var boxes = document.getElementById('list').getElementsByTagName('input');

	var form = document.getElementById('queueForm');
	var h = document.createElement('input');
	h.type = 'hidden';
	h.name = 'queueId';
	h.value = queueId;
	form.appendChild(h);
	for(var i = 0; i < boxes.length; i++) {
		if (boxes[i].checked) {
			if (boxes[i].parentNode.nodeName == 'DIV') {
				var h = document.createElement('input');
				h.type = 'hidden';
				h.name = 'add[]';
				h.value = boxes[i].value;
				form.appendChild(h);
			}
			boxes[i].checked = false;
		}	
	}
	if (!form.actionTemplate) {
		form.actionTemplate = form.action;
	}
	form.action = form.actionTemplate.replace('replace','add');
	ajaxSubmit(form);
}

function ajaxSubmit(target) {
	target.action += 'ajax=true';
	HTML_AJAX.formSubmit(target,target, {
			Open:function(r) { 
				var div = document.createElement('div');
				target.className = 'loading'; 
				target.appendChild(div); 
				div.style.height = target.offsetHeight + 'px';
				div.innerHTML = '<p>Processing ...</p>';
			}, 
			Load:function(r) { target.className = 'radio'; }
		} 
	);
	return false;
}

function clearQueue(queueId) {
	var form = document.getElementById('queueForm');

	if (!form.actionTemplate) {
		form.actionTemplate = form.action;
	}
	form.action = form.actionTemplate.replace('replace','clear');
	var h = document.createElement('input');
	h.type = 'hidden';
	h.name = 'queueId';
	h.value = queueId;
	form.appendChild(h);
	ajaxSubmit(form);
}

function viewQueue(queueId) {
	var form = document.getElementById('queueForm');

	if (!form.actionTemplate) {
		form.actionTemplate = form.action;
	}
	form.action = form.actionTemplate.replace('index.php/','index.php/main/').replace('replace','viewQueue');
	var h = document.createElement('input');
	h.type = 'hidden';
	h.name = 'queueId';
	h.value = queueId;
	form.appendChild(h);
	form.submit();
}

function selectQueue(a,queueId) {
	var tr = clniUtil.findParentOfTagName(a,'tr');

	document.getElementById('processing').innerHTML = 'Processing Queue: <b>'+tr.cells[0].innerHTML+'</b>';
	document.getElementById('processing').style.display = 'block';

	document.getElementById('submit').disabled = false;
	document.getElementById('eob').disabled = false;
	document.getElementById('queueId').value = queueId;
}

function batchEob(url) {
	window.location = url + $('queueId').value;
	return false;
}
