var password = document.getElementById("inputPassword");
var confirm_password = document.getElementById("inputConfirm");

function validatePassword(){
	if(password.value != confirm_password.value) {
		confirm_password.setCustomValidity("Heslá sa nezhodujú!");
	} else {
		confirm_password.setCustomValidity('');
	}
}

password.onchange = validatePassword;
confirm_password.onchange = validatePassword;

$("#schoolCity").autocomplete({
	source: function (request, response) {
		jQuery.get("workers/autocomplete.php", {
			psc: $("#schoolPSC").val(),
			city: request.term
		}, function (data) {
			var parsed = JSON.parse(data);
			// assuming data is a JavaScript array such as
			// ["one@abc.de", "onf@abc.de","ong@abc.de"]
			// and not a string
			response(parsed);
		});
	},
	select: function( event, ui ) {
		$(this).trigger("change");
	},
	minLength: 0
}).focus(function(){
	$(this).data("uiAutocomplete").search($(this).val());
}).change(function() {
	$("#schoolAddress").prop("disabled",false).val("");
	$("#schoolName").val('').prop('disabled',true).val("");
	$("#schoolID").val('');
});
$("#schoolPSC").change(function() {
	$("#schoolAddress").prop("disabled",false).val("");
	$("#schoolName").val('').prop('disabled',true).val("");
	$("#schoolID").val('');
});
$("#schoolAddress").autocomplete({
	source: function (request, response) {
		jQuery.get("workers/autocomplete.php", {
			address: request.term,
			psc: $("#schoolPSC").val(),
			city: $("#schoolCity").val()
		}, function (data) {
			var parsed = JSON.parse(data);
			// assuming data is a JavaScript array such as
			// ["one@abc.de", "onf@abc.de","ong@abc.de"]
			// and not a string
			response(parsed);
		});
	},
	select: function( event, ui ) {
		$(this).val(ui.item.value);
		$(this).trigger("change");
	},
	minLength: 0
}).focus(function(){
	$(this).data("uiAutocomplete").search($(this).val());
}).change(function() {
	jQuery.get("workers/autocomplete.php", {
		address: $(this).val(),
		psc: $("#schoolPSC").val(),
		city: $("#schoolCity").val(),
		select: true
	}, function (data) {
		if (data == "null") {
			$("#schoolName").val('').prop('disabled',false);
			$("#schoolID").val('');
		} else {
			var parsed = JSON.parse(data);
			$("#schoolName").val(parsed.name).prop('disabled',true);
			$("#schoolID").val(parsed.id);
		}
	});
});