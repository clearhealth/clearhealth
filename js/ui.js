var tabCount = {};
var replaceTarget = '';
function selectTab(e) {
	selector = $u.eventTarget(e);
	return _selectTabFromDiv(selector);
}
function _selectTabFromDiv(selector) {
	var id = /selector(.+)/.exec(selector.parentNode.id)[1];
	
	var tabs = $u.getElementsByCssSelector('div.tab',$('tabset'+id));

	for(var i = 0; i < tabs.length; i++) {
		if (i == selector.index) {
			$u.addClass(tabs[i],'selected');
			if (tabs[i].attributes.getNamedItem("tabKey")) {
				var tabKey = tabs[i].attributes.getNamedItem("tabKey").value;
				HTML_AJAX.call('CTabState','selectTab',null,tabKey);
			}
			if (tabs[i].attributes.getNamedItem("actionname")) {
				action = tabs[i].attributes.getNamedItem("actionname").value;
				if (tabs[i].innerHTML.length == 0) {
					var callback = function(result) {
         				  replaceTarget.innerHTML = result;
        				   };
					replaceTarget = tabs[i];
					HTML_AJAX.call('WidgetForm','ajaxFillout',callback,action);
				}
			}
		}
		else {
			$u.removeClass(tabs[i],'selected');
		}
	}

	var selectors = selector.parentNode.getElementsByTagName('a');
	for(var i = 0; i < selectors.length; i++) {
		$u.removeClass(selectors[i],'selected');
	}
	$u.addClass(selector,'selected');
}

Behavior.register('div.tabset div.tab',function(element) {
	var id = /tabset(.+)/.exec(element.parentNode.id)[1];

	if (!tabCount[id]) {
		tabCount[id] = 0;
	}

	var newEl = document.createElement('a');
	newEl.index = tabCount[id]++;
	newEl.innerHTML = element.title;
	$('selector'+id).appendChild(newEl);
	$u.registerEvent(newEl,'click',selectTab);

	if (tabCount[id] == 1) {
		$u.addClass(newEl,'selected');
		//_selectTabFromDiv(newEl);
		firstTab = false;
	}
	element.minimizer = newEl;
});

Behavior.register('div.minimizable',function(element) {
	var newEl = document.createElement('a');
	newEl.className = 'minimizer';
	newEl.innerHTML = '-';

	var label = document.createElement('div');
	label.innerHTML = element.title;
	label.className = 'minimizedLabel';
	label.style.display = 'none';

	element.insertBefore(label, element.firstChild);
	$u.registerEvent(label,'click',toggleMinimization);

	element.insertBefore(newEl, element.firstChild);
	$u.registerEvent(newEl,'click',toggleMinimization);

	element.minLabel = label;
	element.minimizer = newEl;

	if ($u.hasClass(element,'minimized')) {
		$u.removeClass(element,'minimized');
		_toggleMinimization(element);
	}
});

var formPopup = false;
function showForm(id,mode) {
	if (formPopup) {
		formPopup.hide();
	}
	formPopup = new clniPopup(id,true);
	formPopup.className = 'formPopup';
	formPopup.draggable = true;
	formPopup.useElement = id;
	formPopup.display();
}

function toggleMinimization(e) {
	var target = $u.eventTarget(e);

	if (!target.minimizer) {
		target = target.parentNode;
	}
	_toggleMinimization(target);
}
function _toggleMinimization(target) {

	if ($u.hasClass(target,'minimized')) {
		target.minimizer.innerHTML = '-';	
		$u.removeClass(target,'minimized');

		target.minLabel.style.display = 'none';
	}
	else {
		target.minimizer.innerHTML = '+';	
		$u.addClass(target,'minimized');

		target.minLabel.style.display = 'block';
	}
}
