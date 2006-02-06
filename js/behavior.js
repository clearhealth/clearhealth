/* Clearhealth specific behaviors */

/* Menu Code */
var visibleMenu = false;
Behavior.register(
	"div#nav div.section",
	function(element) {
		element.style.color = 'red';
		var a = element.getElementsByTagName('a').item(0);
		var handler = function(event) {
			if (visibleMenu) {
				visibleMenu.style.visibility = 'hidden';
			}
			var target = HTML_AJAX_Util.eventTarget(event).parentNode.getElementsByTagName('ul').item(0);
			if (target != visibleMenu) {
				target.style.visibility = 'visible';
				visibleMenu = target;
			}
			else {
				visibleMenu = false;
			}
		}
		//HTML_AJAX_Util.registerEvent(a,'mouseover',handler);
		HTML_AJAX_Util.registerEvent(a,'click',handler);
		a.isMenu = true;
	}
);

Behavior.register(
	"html",
	function(element) {
		var handler = function(event) {
			var target = HTML_AJAX_Util.eventTarget(event);
			if (!target.isMenu) {
				if (visibleMenu) {
					visibleMenu.style.visibility = 'hidden';
					visibleMenu = false;
				}
			}
		}
		HTML_AJAX_Util.registerEvent(element,'click',handler);
	}
);
var toolTipTimeout = false;
var toolTip = false;
Behavior.register(
	".tooltip",
	function(element) {

		var handler = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);

			var mousePos = clniUtil.mouseXY(e);

			var divs = target.getElementsByTagName('div');
			for(var i = 0; i < divs.length; i++) {
				if (divs[i].className == 'tooltipMessage') {
					var el = divs[i];
					if (toolTip == false) {
						toolTipTimeout = window.setTimeout(function(e) { el.style.display = 'block'; toolTipTimeout = false; toolTip = true;}, 400);
						divs[i].style.left = (mousePos.x+20) + 'px';
						divs[i].style.top = (mousePos.y-target.clientHeight-5) + 'px';
					}
				}
			}
		}

		var ohandler = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);

			if (toolTipTimeout) {
				window.clearTimeout(toolTipTimeout);
				toolTipTimeout = false;
			}

			var divs = target.getElementsByTagName('div');
			for(var i = 0; i < divs.length; i++) {
				if (divs[i].className == 'tooltipMessage') {
					divs[i].style.display = 'none';
				}
			}
			toolTip = false;
		}

		HTML_AJAX_Util.registerEvent(element,'mouseover',handler);
		HTML_AJAX_Util.registerEvent(element,'mouseout',ohandler);
	}
);

Behavior.register(
	".element",
	function(element) {
		var a = document.createElement('a');
		a.innerHTML = '<img src="../../Images/stock/information.gif">';
		a.className = 'elementLink';
		element.appendChild(a);

		var handler = function(e) {
			var target = HTML_AJAX_Util.eventTarget(e);
			var parent = target.parentNode.parentNode;
			var info = HTML_AJAX_Util.getElementsByClassName('elementInfo',parent)[0];
			if (info.style.display == 'block') {
				info.style.display = 'none';
			}
			else {
				info.style.display = 'block';
				info.style.height = parent.offsetHeight+'px';
			}
		}
		HTML_AJAX_Util.registerEvent(a,'click',handler);
	}
);
