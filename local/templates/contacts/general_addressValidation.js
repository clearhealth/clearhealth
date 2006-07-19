{clni_register_validation_rule id="addrName" message="Every Person requires an Address, if this person does not have an address add one with a name of \"No Address\" to signify this." rule="address"}
{head type="js"}
	var addressElements = [];
	Behavior.register('#address',function(element) 
		{
			var inputs = element.getElementsByTagName('input');
			for(var i = 0; i < inputs.length; i++) {
				if (inputs[i].type != 'submit' && inputs[i].type != 'hidden') {
					clni_register_validation_rule_hash({obj:inputs[i],rule:'addressRequiredIf'});
					addressElements.push(inputs[i]);
				}
			}
			var selects = element.getElementsByTagName('select');
			for(var i = 0; i < selects.length; i++) {
				clni_register_validation_rule_hash({obj:selects[i],rule:'addressRequiredIf'});
				addressElements.push(selects[i]);
			}

		}
	);

	function clni_rule_address(element) {
		if (document.getElementById('noAddresses')) {
			if ($('relatedAddressGrid')) {
				if (document.getElementById('relatedAddress').checked == true) {
					var addressInputs = $('relatedAddressGrid').getElementsByTagName('INPUT');
					var relatedAddressRegExp = new RegExp('^relatedAddress');
					for (var i = 0; i < addressInputs.length; i++) {
						if (addressInputs[i] && relatedAddressRegExp.test(addressInputs[i].name) && addressInputs[i].checked) {
							return true;
						}
					}
				}
				return false;
			}
			else {
				return clni_rule_required(element);
			}
		}
		return true;
	}

	function clni_rule_addressRequiredIf(element) {
		var required = false;
		// if any of the elements are filled out all are required
		for(var i =0; i < addressElements.length; i++) {
			if (addressElements[i].name[0] != '_' && 
				!(addressElements[i].value == 0 || addressElements[i].value == '' || addressElements[i].checked == false)) {
				required = true;
			}
		}

		if (addressElements[2].value == 'No Address') {
			required = false;
		}
		if (element.name.match(/line2\]$/)) {
			return true;
		}
		if (required) {
			if (element.tagName == 'SELECT' && (element.value == 0 || element.value == '')) {
				return false;
			} else if (element.value == '') {
				return false;
			}
		}
		return true;
	}

	function updateAddrName(select) {
		var name = document.getElementById('addrName');
		if (name.value == '') {
			name.value = select.options[select.selectedIndex].text;
		}
	}
{/head}
