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
