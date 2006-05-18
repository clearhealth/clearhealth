var conflicts = {
	displayConflicts: function(data,targetId) {
		var target = $(targetId);

		var POST = data._POST;
		delete data._POST;

		for(var section in data) {
			for(var field in data[section]) {
				//target.innerHTML += ;//section+'['+field+'] = '+data[section][field]+'<br>';

				var name = section+'['+field+']';
				var formEl = document.getElementsByName(name).item(0);
				if (!formEl) {
					continue;
				}
				var row = data[section][field];
				row.fieldName = name;
				if (!row.your_value) {
					row.your_value = POST[section][field];
				}
				var yourValue = row.your_value;
				var oldValue = row.old_value;
				var newValue = row.new_value;
				if (formEl.nodeName == 'SELECT') {
					for(var i = 0; i < formEl.options.length; i++) {
						if (formEl.options[i].value == row.your_value) {
							yourValue = formEl.options[i].text;
						}
						if (formEl.options[i].value == row.old_value) {
							oldValue = formEl.options[i].text;
						}
						if (formEl.options[i].value == row.new_value) {
							newValue = formEl.options[i].text;
						}
					}
				}

				$u.addClass(formEl,'conflictingField');

				var tr = document.createElement('tr');

				var td = document.createElement('td');
				td.innerHTML = conflicts.getLabel(formEl); 
				tr.appendChild(td);

				var td = document.createElement('td');
				td.innerHTML = '<a href="#setValue">'+oldValue+'</a>';
				td.value = row.old_value;
				tr.appendChild(td);

				var td = document.createElement('td');
				td.innerHTML = '<a href="#setValue">'+yourValue+'</a>';
				td.value = row.your_value;
				tr.appendChild(td);

				var td = document.createElement('td');
				td.innerHTML = row.username;
				tr.appendChild(td);

				var td = document.createElement('td');
				td.innerHTML = '<a href="#setValue">'+ newValue+'</a>';
				td.value = row.new_value;
				tr.appendChild(td);
				target.appendChild(tr);


				var as = target.getElementsByTagName('a');
				for(var i = 0; i < as.length; i++) {
					if (as[i].href.substr(-8) == 'setValue') {
						$u.registerEvent(as[i],'click',conflicts.setFromClick);
					}
				}
				tr.data = row;
			}
		}

		for(var section in POST) {
			if ($u.getType(POST[section]) == 'object') {
				for(var field in POST[section]) {
					var name = section+'['+field+']';
					var formEl = document.getElementsByName(name).item(0);

					conflicts.setElementValue(formEl,POST[section][field]);
				}
			}
		}
		conflicts.loadingComplete();
	},
	setFromClick: function(e) {
		var a = $u.eventTarget(e);
		var data = a.parentNode.parentNode.data;

		var formEl = document.getElementsByName(data.fieldName).item(0);

		conflicts.setElementValue(formEl,a.parentNode.value);
	},
	getLabel: function(el) {
		var ret = '';
		for (var i=0;i<3;i++) {
			if (el.nodeName != 'TD') {
				el = el.parentNode;
			}
		}
		if (el.nodeName != 'TD') {
			return 'Unknown';
		}
		for(i=0;i<2;i++) {
			if(el && el.nodeName == 'TD' && el.getElementsByTagName('label').length > 0) {
				continue;
			}
			el=el.previousSibling;
		}
		if (el && el.nodeName == 'TD' && el.getElementsByTagName('label').length > 0) {
			return el.getElementsByTagName('label')[0].innerHTML;
		}
		return 'Unknown';
	},

	setElementValue: function(element,value) {
		if (!element) {
			return;
		}
		switch(element.nodeName) {
			case 'INPUT':
				switch(element.type) {
					case 'radio':
						var els = document.getElementsByName(element.name);
						for(var i = 0; i < els.length; i++) {
							if (els[i].value == value) {
								els[i].checked = true;
							}
							else {
								els[i].checked = false;
							}
						}
						break;
					break;
					case 'hidden':
						// don't reset hidden, we might want too, im not sure yet
						break;
					default: 
						element.value = value;
						break;
				}
			default: 
				element.value = value;
				break;
		}
	},

	_loading: false,

	loading: function() {
		conflicts._loading = new clniPopup('Conflicts found while submitting updates. <br>Loading additional information ...',false);
		conflicts._loading.modal = true;
		conflicts._loading.className = 'loading';
		conflicts._loading.display();
	},

	loadingComplete: function() {
		conflicts._loading.remove();
	}
}
