HTML_AJAX.onError = function(e) {
document.getElementById('debug').innerHTML = HTML_AJAX_Util.quickPrint(e);
}

var ac_callbacks = {
	update_filter: function(result) {
		//TODO: Update events
	},
	remove_filter: function(result) {
		//TODO: Update events
	},
	get_current_filters: function(result) {
		cur = $('calendarCurrentFilters');
		HTML_AJAX_Util.setInnerHTML(cur, result);
	},
	get_filters: function(result){
		cur = $('filterHTML');
		HTML_AJAX_Util.setInnerHTML(cur, result);
	},
	drop_action: function(result) {	}	
}

function dropEvent(drag, drop){
	drag_id = drag.id;
	newNode = document.createElement('div');
	newNode.innerHTML = drag.innerHTML;
	newNode.id = drag.id;
	newNode.className = 'event-div';
	drop.appendChild(newNode);
	new Draggable(drag_id, {revert:true});
	drag.parentNode.removeChild(drag);

	event_id = drag_id.split('-')[1];
	day_date = drop.id.split('-')[1];
//	D = new Date(day_date.replace(/(\d{4})(\d\d)(\d\d)/, "$1/$2/$3"));
//	start_ts = D.getTime()/1000; 
	var controller = new C_CalendarAJAXEvent(ac_callbacks);
	controller.drop_action(event_id, day_date);

}

function filterMultiselectOnClick(clicked){
	filter_parts = clicked.id.split('_');
	ac = new C_CalendarAJAXEvent(ac_callbacks);
	if(HTML_AJAX_Util.hasClass(clicked, 'ms-highlight')){
		HTML_AJAX_Util.removeClass(clicked, 'ms-highlight');
		ac.remove_filter(filter_parts[1], filter_parts[2]);
	}else{
		HTML_AJAX_Util.addClass(clicked, 'ms-highlight');
		ac.update_filter(filter_parts[1], filter_parts[2]);
	}
}

// click to schedule
var st = {
	mouseDownStatus: false,
	lock: false,
	selected: {length:0},
	selectStart: false,
	mouseOver: function(e) {
		try {
			if (st == undefined) {
				return;
			}
		} catch(e) { return; }

		if (st.lock) {
			return;
		}
		var target = HTML_AJAX_Util.eventTarget(e);
		if (st.mouseDownStatus) {
			st._updateSelection(target);
		}
		else {
			$u.addClass(target,'stOver');
		}
	},
	mouseOut: function(e) {
		try {
			if (st == undefined) {
				return;
			}
		} catch(e) { return; }

		if (st.lock) {
			return;
		}
		if (st.mouseDownStatus) {
			return;
		}
		var target = $u.eventTarget(e);
		$u.removeClass(target,'stOver');
	},
	_updateSelection: function(target) {
		var pieces = target.id.split('-');
		var end = pieces[1];

		var pieces = st.selectStart.split('-');
		var start = pieces[1];
		var schTargets = $u.getElementsByCssSelector('#scheduleColItems-'+pieces[2]+' .scheduleTarget');

		var ret = [];
		for(var i = 0; i < schTargets.length; i++) {
			var pieces = schTargets[i].id.split('-');
			if ( (end > start && (pieces[1] >= start && pieces[1] <= end)) || (end < start && (pieces[1] <= start && pieces[1] >= end)) ) {
				$u.addClass(schTargets[i],'stSelected');
				ret.push(schTargets[i].id);
			}
			else {
				$u.removeClass(schTargets[i],'stSelected');
			}
		}
		return ret;
	},
	mouseClick: function(e) {
		if (st.lock) {
			return;
		}

		var target = $u.eventTarget(e);
		$u.addClass(target,'stSelected');

		if (st.mouseDownStatus) {
			var items = st._updateSelection(target);
			for(var i = 0; i < items.length; i++) {
				st.addItem(items[i]);
			}

			if (st.selected.length > 0) {
				st.lock = true;
				st.mouseDownStatus = false;
				st.selectItems(st.selected);
			}
		}
		else {
			st.selectStart = target.id;
			st.addItem(target.id);
			st.mouseDownStatus = true;
		}
	},
	selectItems: function(items) {
		$('debug').innerHTML = $u.quickPrint(st.selected);
		this.clearSelected();
	},
	addItem: function(id) {
		st.selected[id] = id;
		st.selected.length++;
	},
	clearSelected: function() {
		for(var i in st.selected) {
			if (i != 'length') {
				$u.removeClass(i,'stSelected');
			}
		}
		st.selected = {length:0};
		st.lock = false;
	},
	grabTime: function(id) {
		return /st-([0-9]+)-/.exec(id)[1];
	},
	getEarliestId: function() {
		var earliest = false;
		var id = false;
		for(var i in st.selected) {
			if (i != 'length') {
				var t = this.grabTime(i);
				if (!earliest || t < earliest) {
					earliest = t;
					id = i;
				}
			}
		}
		return id;
	},
	getEarliestSelected: function() {
		var earliest = false;
		for(var i in st.selected) {
			if (i != 'length') {
				var t = this.grabTime(i);
				if (!earliest || t < earliest) {
					earliest = t;
				}
			}
		}
		return new Date((earliest*1000) + (timeZoneAdjust*60*1000));
	},
	getLatestSelected: function() {
		var latest = false;
		for(var i in st.selected) {
			if (i != 'length') {
				var t = this.grabTime(i);
				if (!latest || t > latest) {
					latest = t;
				}
			}
		}
		latest = new Number(latest)+new Number(calendarInterval);
		return new Date((latest*1000) + (timeZoneAdjust*60*1000));
	}
}
Behavior.register(
	'img.scheduleTarget',
	function(element) {
		HTML_AJAX_Util.registerEvent(element,'mouseover',st.mouseOver);
		HTML_AJAX_Util.registerEvent(element,'mouseout',st.mouseOut);
		HTML_AJAX_Util.registerEvent(element,'click',st.mouseClick);
		element.onselectstart = function() { return false; };
	}
);
function toggleHiddenEvents(e) {
	var target = $u.eventTarget(e);
	var hidden = $u.getElementsByCssSelector('.hidden',target.parentNode.parentNode);

	if (target.hidden == undefined) {
		target.hidden = true;
	}
	if (target.hidden) {
		hidden[0].style.display = 'block';
		target.src = target.src.replace('show','hide');
		target.hidden = false;
	}
	else {
		target.hidden = true;
		hidden[0].style.display = 'none';
		target.src = target.src.replace('hide','show');
	}
}
/*
Behavior.register(
	'#calendarOverlay .multiple .handle',
	function(element) {
		HTML_AJAX_Util.registerEvent(element,'click',toggleHiddenEvents);
		HTML_AJAX_Util.registerEvent(element,'mouseover',function(e) { var target = $u.eventTarget(e); target.style.backgroundColor = '#ccc';});
		HTML_AJAX_Util.registerEvent(element,'mouseout',function(e) { var target = $u.eventTarget(e); target.style.backgroundColor = '';});
	}
);
*/
/*
// popup code
var calendarFilters = false;
function showCalendarFilters() {
	if (!calendarFilters) {
		calendarFilters = new clniPopup('',false);
		calendarFilters.draggable = true;
		calendarFilters.draggableOptions = {handle:'title'};
		calendarFilters.useElement = 'filterHTML';
	}
	calendarFilters.display();
}
function hideCalendarFilters() {
	calendarFilters.hide();
}
*/

// show dock
function showCalendarFilters() {
	if (!$('dock').expanded) {
		showHideExtra($('dockSlider'));
	}
}

var conflictsExpanded = {};
function toggleConflicts(id) {
	if (conflictsExpanded[id]) {
		shrinkConflicts(id);
	}
	else {
		expandConflicts(id);
	}
}

function shrinkConflicts(provider_id) {
	conflictsExpanded[provider_id] = false;

	// get the current column
	var col = HTML_AJAX_Util.getElementsByCssSelector('#column-'+provider_id)[0];

	// shirnk it too its original width
	var origWidth = col.originalWidth;
	var currentSize = new Number(stripPx(col.style.width));

	if (origWidth == currentSize) {
		return false;
	}
	col.style.width = origWidth+'px';

	// hide inner columns
	var cols = HTML_AJAX_Util.getElementsByCssSelector('#column-'+provider_id+' .innerColumn');
	for(var i = 0; i < cols.length; i++) {
		if (!$u.hasClass(cols[i],'primaryColumn')) {
			cols[i].style.display = 'none';
		}
	}

	// move everything to the right current column left
	var cols = HTML_AJAX_Util.getElementsByCssSelector('.column');

	var sub = new Number(currentSize-origWidth);

	var right = false;
	for(var i = 0; i < cols.length; i++) {
		if (right) {
			var newLeft = new Number(stripPx(cols[i].style.left))-sub;
			cols[i].style.left = newLeft+'px';
		}
		if (cols[i].id == col.id) {
			right = true;
		}
	}

	var calBody = $('calendarBody');
	var newWidth = new Number(stripPx(calBody.style.width))-sub;
	calBody.style.width = newWidth+'px';

	return true;
}

function expandConflicts(provider_id) {
	conflictsExpanded[provider_id] = true;

	// get the current column
	var col = HTML_AJAX_Util.getElementsByCssSelector('#column-'+provider_id)[0];

	// expand it too its new width
	var origWidth = stripPx(col.style.width);
	if (!col.originalWidth) {
		col.originalWidth = origWidth;
	}
	var size = new Number(col.getAttribute('expandedSize'));

	if (origWidth == size) {
		return false;
	}
	col.style.width = size+'px';

	// show inner columns
	var cols = HTML_AJAX_Util.getElementsByCssSelector('#column-'+provider_id+' .innerColumn');
	for(var i = 0; i < cols.length; i++) {
		if (!$u.hasClass(cols[i],'primaryColumn')) {
			cols[i].style.display = 'block';
		}
	}

	// move everything to the right current column right
	var cols = HTML_AJAX_Util.getElementsByCssSelector('.column');

	var add = new Number(size-origWidth);

	var right = false;
	for(var i = 0; i < cols.length; i++) {
		if (right) {
			var newLeft = new Number(stripPx(cols[i].style.left))+add;
			cols[i].style.left = newLeft+'px';
		}
		if (cols[i].id == col.id) {
			right = true;
		}
	}

	var calBody = $('calendarBody');
	var newWidth = new Number(stripPx(calBody.style.width))+add;
	calBody.style.width = newWidth+'px';
	return true;
}

function toggleAllConflicts(box) {
	var expand = box.checked;

	var schedules = $u.getElementsByClassName('schedule',$('calendarOverlay'));

	for(var i = 0; i < schedules.length; i++) {
		var provider = schedules[i].id.substring(9);
		if (expand) {
			expandConflicts(provider);
		}
		else {
			shrinkConflicts(provider);
		}
	}
}

function calcLeft(el,size) {
	var newSize = new Number(stripPx(el.style.left)) + new Number(size) + new Number(10);
	return newSize;
}

function stripPx(str) {
	return str.substring(0,str.indexOf('p'));
}
function showHideExtra(div) {
	if(document.getElementById('dockInternalDiv').style.display='none') {
		document.getElementById('dockInternalDiv').style.display='block';
	}
	var shrink = new fx.Width(div.parentNode,{duration:500});
	var bigsize = stripPx($('dockInternalDiv').style.width);
	if (!div.parentNode.expanded) {
		shrink.custom(10,190);
		div.parentNode.expanded = true;
	}
	else {
		shrink.custom(190,10);
		document.getElementById('dockInternalDiv').style.display='none';
		div.parentNode.expanded = false;
	}
}
Behavior.register('div#dock',
	function(element) {
		resizeHandler();
		if (!document.all) {
			element.style.position = 'fixed';
		}

	}
);
var dockTimer = false;
function resizeHandler() {
	var size = clniUtil.viewportSize();

	var offset = 40;
	if (document.all) {
		offset = 39;
	}
	$('dock').style.height = (size.y-offset)+'px';
	$('dockSlider').style.height = (size.y-offset)+'px';
	$('dock').maxHeight = (size.y)+'px';
	$('dock').smallHeight = (size.y-offset)+'px';
	scrollHandler();
}
function scrollHandler() {
	if (document.body.scrollTop) {
		var t = document.body.scrollTop;
	}
	else {
		var t = document.documentElement.scrollTop;
	}
	var offset = 40;
	if (document.all) {
		offset = 39;
	}
	if (t < offset) {
		t = offset;

		var sH = $('dock').smallHeight;
		$('dock').style.height = sH;
		$('dockSlider').style.height = sH;
	}
	else {
		var mH = $('dock').maxHeight;
		$('dock').style.height = mH;
		$('dockSlider').style.height = mH;
	}

	if ($('dock').style.position != 'fixed') {
		if (dockTimer) {
			window.clearTimeout(dockTimer);
		}
		dockTimer = window.setTimeout(function() { $('dock').style.top = t+'px'; }, 100);
	}
}

Behavior.register('body',
	function(element) {
		//window.addEventListener("scroll", scrollHandler, false);
		//window.addEventListener("resize", scrollHandler, false);
		window.onscroll = scrollHandler;
		window.onresize = resizeHandler;

		if ($('doubleBooking').checked) {
			toggleAllConflicts($('doubleBooking'));
		}
		if ($('hideProvider').checked) {
			toggleProviders($('hideProvider'));
		}
	}
);

function toggleProviders(box) {
	if (box.checked) {
		$('doubleBooking').disabled = true;
		// hide provider schedules
		var cols = HTML_AJAX_Util.getElementsByCssSelector('.providerColumn');

		var sub = 0;
		var subLeft = 0;
		for(var i = 0; i < cols.length; i++) {
			if (!cols[i].originalWidth) {
				cols[i].originalWidth = stripPx(cols[i].style.width);
			}
			cols[i].lastWidth = stripPx(cols[i].style.width);
			var newLeft = parseInt(stripPx(cols[i].style.left))-subLeft;

			sub += parseInt(cols[i].lastWidth)-10;
			subLeft += parseInt(cols[i].lastWidth)-2;

			cols[i].style.left = newLeft+'px';
			cols[i].style.width = '10px';
			cols[i].style.overflow = 'hidden';
			cols[i].style.marginRight = '2px';
			cols[i].style.paddingRight = '2px';
			
			var blocks = HTML_AJAX_Util.getElementsByCssSelector('.conflictBlockLink',cols[i]);
			for(var b = 0; b < blocks.length; b++) {
				blocks[b].style.display = 'none';
			}
		}

		// move all room schedules left
		var cols = HTML_AJAX_Util.getElementsByCssSelector('.roomColumn');
		for(var i = 0; i < cols.length; i++) {
			var newLeft = parseInt(stripPx(cols[i].style.left))-sub;
			cols[i].style.left = newLeft + 'px';
		}
	}
	else {
		$('doubleBooking').disabled = false;
		// show provider schedules
		var cols = HTML_AJAX_Util.getElementsByCssSelector('.providerColumn');

		var add = 0;
		var addLeft = 0;
		for(var i = 0; i < cols.length; i++) {
			var newWidth = parseInt(cols[i].lastWidth);

			var newLeft = parseInt(stripPx(cols[i].style.left))+addLeft;

			add += parseInt(newWidth)-10;
			addLeft += parseInt(newWidth)-2;

			cols[i].style.left = newLeft+'px';
			cols[i].style.width = newWidth+'px';
			cols[i].style.marginRight = '0px';
			cols[i].style.paddingRight = '10px';

			var blocks = HTML_AJAX_Util.getElementsByCssSelector('.conflictBlockLink',cols[i]);
			for(var b = 0; b < blocks.length; b++) {
				blocks[b].style.display = 'block';
			}
		}

		// move all room schedules right
		var cols = HTML_AJAX_Util.getElementsByCssSelector('.roomColumn');
		for(var i = 0; i < cols.length; i++) {
			var newLeft = parseInt(stripPx(cols[i].style.left))+add;
			cols[i].style.left = newLeft + 'px';
		}
	}
}

for(var i = Behavior.list.length -1; i >= 0; i--) {
	if (Behavior.list[i].selector == 'div.tooltip') {
		Behavior.list.splice(i,1);
	}
	var formRegx = /^form/;
	if (Behavior.list[i].selector.match(formRegx)) {
		Behavior.list.splice(i,1);
	}
}
