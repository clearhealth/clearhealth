function zipGrab(zip) {
	HTML_AJAX.defaultEncoding = 'JSON'; // set encoding to JSON encoding method
	HTML_AJAX.call('zipcode','getData',zipCallback,zip);
	HTML_AJAX.defaultEncoding = 'Null'; // return it to default which is Null
}

function zipCallback(resultSet) {
	if(!resultSet==false){
		document.getElementById('addresscity').value = resultSet['city'];
		var i = 0;
		while(i<document.getElementById('addressstate').options.length) {
			if (document.getElementById('addressstate').options[i].innerHTML == resultSet['state']){
				document.getElementById('addressstate').options[i].selected = true;
			} else {
				document.getElementById('addressstate').options[i].selected = false;
			}
			i+=1;
		}
		document.getElementById('addresspostalcode').value = resultSet['zip'];
	}

}
