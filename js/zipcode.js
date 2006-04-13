function zipGrab(zip,addressid) {
	HTML_AJAX.defaultEncoding = 'JSON'; // set encoding to JSON encoding method
	HTML_AJAX.call('zipcode','getData',zipCallback,zip,addressid);
	HTML_AJAX.defaultEncoding = 'Null'; // return it to default which is Null
}

function zipCallback(resultSet) {
	if(resultSet){
		document.getElementById('zaddresscity'+resultSet['addressid']).value = resultSet['city'];
		var i = 0;
		while(i<document.getElementById('zaddressstate'+resultSet['addressid']).options.length) {
			if (document.getElementById('zaddressstate'+resultSet['addressid']).options[i].innerHTML == resultSet['state']){
				document.getElementById('zaddressstate'+resultSet['addressid']).options[i].selected = true;
			} else {
				document.getElementById('zaddressstate'+resultSet['addressid']).options[i].selected = false;
			}
			i+=1;
		}
//		document.getElementById('addresspostalcode').value = resultSet['zip'];
	}

}
