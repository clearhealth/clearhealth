
function loadReferralView(href, ordoId) {
	var lm = new LockManager({hasOrdoTypeHappened: function(result) { doReferralViewConfirm(result, href); }});
	var dateObj = new Date();
	dateObj.setMinutes(dateObj.getMinutes() - 30);
	var date = new Array();
	date[0] = dateObj.getFullYear();
	date[1] = (dateObj.getMonth() + 1) + "";
	date[2] = dateObj.getDate() + "";
	date[3] = dateObj.getHours() + "";
	date[4] = dateObj.getMinutes() + "";
	date[5] = dateObj.getSeconds() + "";
	
	for (var i = 1; i < date.length; i++ ) {
		if (date[i].length == 1) {
			date[i] = "0" + date[i];
		}
	}
	var timestamp = date[0] + "-" + date[1] + "-" + date[2] + " " +
		date[3] + ":" + date[4] + ":" + date[5];
	
	lm.hasOrdoTypeHappened('refRequest', ordoId, timestamp, 'process');
}

function doReferralViewConfirm(result, href) {
	if (result.result == false) { 
		location.href = href;
		return true;
	}
	else if (result.result == true && confirm(result.message + ' at ' + result.log_date + "\n\nContinue?")) {
		location.href = href;
		return true;
	}
	
	return false;
}
