// Simulates :hover pseudo class in IE
function hover(el) {
	//document.body.innerHTML += el.className + "<br />";
	if (el.className.indexOf('Hover') > -1) {
		el.className = el.className.replace(/ [a-z]+Hover/g, '');
	}
	else {
		el.className += ' ' + el.className + 'Hover';
	}
}
