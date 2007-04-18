//alert('behaviors loaded');
Behavior.register(
	"form .required",
	function(element) {
		clni_register_validation_rule_hash({obj:element, rule:"required"});
	}
);

Behavior.register(
	"form .requiredNumber",
	function(element) {
		clni_register_validation_rule_hash({obj:element, rule:"required"});
		clni_register_validation_rule_hash({obj:element, rule:"number"});
	}
);

Behavior.register(
	"form .validateNumber",
	function(element) {
		clni_register_validation_rule_hash({obj:element, rule:"number"});
	}
);
/*
Behavior.register(
	".messageTarget",
	function(element) {
		var form = clniUtil.findParentOfTagName(element,'form');	

		clni_register_message_target(form,element);
	}
);
*/
Behavior.register(
	"form .innerHTMLExists",
	function(element) {
		clni_register_validation_rule_hash({obj:element, rule:"innerHTMLExists"});
	}
);

if(document.all) {
Behavior.register(
	"img.trans",
	function(element) {
		if (document.all) {
			var f = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+element.src+"', sizingMethod='scale')";
			element.src = '/index.php/Images/stock/blank.gif';
			element.style.filter = f;
		}
	}
);
}

Behavior.register(
	"form.ajax",
	function(element) {
		var submit = element.elements.submit;
		var originalSubmit = submit.value;
		HTML_AJAX.makeFormAJAX(element,element,
			{
				Open: function() { submit.disabled = true; submit.value = 'Processing please wait ...'; },
				Load: function() { submit.disabled = false; submit.value = originalSubmit; }
			});
	}
);