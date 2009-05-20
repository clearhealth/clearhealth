	function updateRow(input) {
		var row = input.parentNode.parentNode;
		var match = input.name.match(/bill\[(.+)\]\[(.+)\]/);
		var coding_data_id = match[1];
		var type = match[2];

		var amountNode 		= row.cells[1].firstChild;
		var currentPaidNode 	= row.cells[2].firstChild;
		var currentWriteoffNode	= row.cells[3].firstChild;
		var paidNode		= row.cells[4].firstChild;
		var carryNode 		= row.cells[5].firstChild;
		var writeoffNode	= row.cells[6].firstChild;

		var amount = amountNode.nodeValue - currentPaidNode.nodeValue - currentWriteoffNode.nodeValue;
		var paid = paidNode.value;
		var carry = carryNode.value;
		var writeoff = writeoffNode.value;

		if (carry > amount) {
			carry = amount;
			carryNode.value = Math.round(carry*100)/100;
		}
		if (writeoff > amount) {
			writeoff = amount;
			writeoffNode.value = Math.round(writeoff*100)/100;
		}

		if (type == "paid" || type == "writeoff") {
			// update carry value
			carry = amount - paid - writeoff;

			if (carry < 0) {
				if (type == "writeoff") {
					writeoff = amount-paid;
					writeoffNode.value = Math.round(writeoff*100)/100;
				}
				carry = 0;
			}

			carryNode.value = Math.round(carry*100)/100;
		}
		else if (type == "carry") {
			// update writeoff value
			writeoff = amount - paid - carry;
			if (writeoff < 0) {
				carry = amount-paid;
				carryNode.value = Math.round(carry*100)/100;
				writeoff = 0;
			}
			writeoffNode.value = Math.round(writeoff*100)/100;
		}
	}

	function addAdjustment() {
		$('adjustmentTable').style.display = '';
		var row = document.createElement('tr');
		var tdC = document.createElement('td');
		var tdT = document.createElement('td');
		var tdV = document.createElement('td');

		var type = $('adjType').options[$('adjType').selectedIndex];

		var i = $('adjustmentListTable').tBodies[0].rows.length;
		var select = $('adjType').cloneNode(true);
		var input = $('adjValue').cloneNode(true);
		var selCode = $('adjCode').cloneNode(true);

		select.name = 'adjustment['+i+'][type]';
		select.id = '';
		select.selectedIndex = $('adjType').selectedIndex;
		input.name = 'adjustment['+i+'][value]';
		input.id = '';
		input.selectedIndex = $('adjType').selectedIndex;
		selCode.name = 'adjustment['+i+'][code]';
		selCode.id = '';
		selCode.selectedIndex = $('adjCode').selectedIndex;

		$('adjValue').value = '';
		$('adjType').selectedIndex = 0;
		$('adjCode').selectedIndex = 0;

		tdC.appendChild(selCode);
		tdT.appendChild(select);
		tdV.appendChild(input);

		row.appendChild(tdC);
		row.appendChild(tdT);
		row.appendChild(tdV);
		$('adjustmentTable').tBodies[0].appendChild(row);
		
		return false;
	}

	function populateAdjCodes() {
		return;
		var rows = $('codeTable').tBodies[0].rows;
		var select = $('adjCode');
		for(var i = 0; i < rows.length; i++) {
			var code = rows[i].cells[0].innerHTML.split(':')[0];
			var code_id = rows[i].cells[0].innerHTML.match(/value="(.+)" /);
			select.options[i+1] = new Option('Line: '+code,code_id[1]);
		}
	}

	function showAdjustmentList() {
		document.getElementById('adjustmentLink').style.display='none';
		new Effect.BlindDown('adjustmentList');
//		new Effect.BlindUp('adjustmentLink');

		//window.setTimeout(function() {$('adjustmentList').style.overflow = 'auto';},400);
	}

	function hideAdjustmentList() {
		new Effect.BlindUp('adjustmentList');
		document.getElementById('adjustmentLink').style.display='inline';
//		new Effect.BlindDown('adjustmentLink');
	}

	function show_ref_num(val) {
		if (val == "check") {
			document.getElementById("chk_num_label").style.visibility = 'visible';
			document.getElementById("chk_num_input").style.visibility = 'visible';
		}
		else {
			document.getElementById("chk_num_label").style.visibility = 'hidden';
			document.getElementById("chk_num_input").style.visibility = 'hidden';	
		}
		
	}

	Behavior.register('#adjustmentTable',function(element) {
		populateAdjCodes();
	});

	function rebillSelfPay() {
		var cb = function(result) {
			var eob = $('eob');
			if (!eob) {
				var eob = $('eobInner');
			}
			eob.innerHTML = result;
			new Effect.Highlight(eob);
		}
		
		$('submit').disabled = true;
		$('rebill').disabled = true;
		if(document.getElementById('rebillnext')) {
			$('rebillnext').disabled = true;
		}
		$('rebill').innerHTML = 'Processing Please Wait ...';
		HTML_AJAX.post($('rebillAction').value,{process:'true',claim_id:$('claimId').value},cb);
		
		return false;
	}
	function rebillCollections() {
		var cb = function(result) {
			var eob = $('eob');
			if (!eob) {
				var eob = $('eobInner');
			}
			eob.innerHTML = result;
			new Effect.Highlight(eob);
		}
		
		$('submit').disabled = true;
		$('rebill').disabled = true;
		$('collections').disabled = true;
		if(document.getElementById('rebillcollections')) {
			$('rebillnext').disabled = true;
		}
		$('rebill').innerHTML = 'Processing Please Wait ...';
		alert($('collectionsAction').value);
		HTML_AJAX.post($('collectionsAction').value,{process:'true',claim_id:$('claimId').value},cb);
		
		return false;
	}

	function rebillNextPayer() {
		var cb = function(result) {
			var eob = $('eob');
			if (!eob) {
				var eob = $('eobInner');
			}
			eob.innerHTML = result;
			new Effect.Highlight(eob);
		}
		
		$('submit').disabled = true;
		$('rebill').disabled = true;
		$('rebillnext').disabled = true;
		$('rebillnext').innerHTML = 'Processing Please Wait ...';
		HTML_AJAX.post($('billnextAction').value,{process:'true',claim_id:$('claimId').value},cb);
		
		return false;
	}
