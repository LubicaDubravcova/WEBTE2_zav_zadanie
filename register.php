<?php
header('Content-Type: text/html; charset=utf-8'); 

session_start();
if (isset($_SESSION['userData'])) {
	header('Location: profile.php');
	die();
}

if (isset($_POST['email'])) {
	include_once("workers/dbConn.php");
	$user = new dbConn();
	var_dump($_POST);
	$alreadyExists = $user->exists($_POST['email']);
    if (!$alreadyExists) {
		$user->register($_POST);
		$_SESSION['userData'] = $user->login($_POST);
		header('Location: profile.php');
		die();
	}
}
?>
<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<link href="https://code.jquery.com/ui/1.12.1/themes/black-tie/jquery-ui.css" rel="stylesheet">
	<style>.ui-autocomplete {max-height: 100px;overflow-y: auto;overflow-x: hidden;text-align:left;}
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 5px 10px;
  }</style>
</head>
<body class="bg-dark text-white text-center">
	<?php require("includes/navbar.php"); ?>
	<div class="container">
		<h1 class="mt-5">Webové Technológie 2</h1>
		<h2 class="m-4">Registrácia</h2>
		<div class="row justify-content-center">
			<div class="col-md-6 col-md-offset-3 bg-light text-dark rounded p-5">
				<?php if ($alreadyExists) echo "<div class='row form-group'><div class='btn btn-block btn-danger disabled'>E-mail sa už používa</div></div>"?>
				<form method="post" action="#">
					<div class="form-group row">
						<label for="inputEmail" class="col-sm-3 col-form-label text-right">Email:</label>
						<div class="col">
							<input type="email" name="email" class="form-control" id="inputEmail" required placeholder="Zadajte email">
						</div>
					</div>

					<div class="form-group row">
						<label for="inputFirstname" class="col-sm-3 col-form-label text-right">Meno:</label>
						<div class="col">
							<input type="text" class="form-control" name="firstname" id="inputFirstname" required placeholder="Zadajte meno"/>
						</div>
					</div>

					<div class="form-group row">
						<label for="inputSurname" class="col-sm-3 col-form-label text-right">Priezvisko:</label>
						<div class="col">
							<input type="text" class="form-control" name="surname" id="inputSurname" required placeholder="Zadajte priezvisko"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="inputPassword" class="col-sm-3 col-form-label text-right">Heslo:</label>
						<div class="col">
							<input type="password" name="password" class="form-control" minlength="8" required id="inputPassword" placeholder="Zadajte heslo">
						</div>
					</div>
					
					<div class="form-group row">
						<label for="inputConfirm" class="col-sm-3 col-form-label"></label>
						<div class="col">
							<input type="password" class="form-control" name="confirm" id="inputConfirm" required placeholder="Podvrďte svoje heslo"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="PSC" class="col-sm-3 col-form-label text-right">Bydlisko:</label>
						<div class="col-3">
							<input type="number" name="PSC" class="form-control" min=0 max=99999 id="PSC" placeholder="PSČ" required>
						</div>
						<div class="col">
							<input type="text" name="city" class="form-control" id="city" placeholder="Mesto" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="Address" class="col-sm-3 col-form-label"></label>
						<div class="col">
							<input type="text" name="address" class="form-control" id="address" placeholder="Adresa" required>
						</div>
					</div>
					
					<div class="form-group row">
						<label for="schoolPSC" class="col-sm-3 col-form-label text-right">Stredná Škola:</label>
						<div class="col-3">
							<input type="number" name="schoolPSC" class="form-control" min=0 max=99999 id="schoolPSC" placeholder="PSČ" required>
						</div>
						<div class="col">
							<input type="text" name="schoolCity" class="form-control" id="schoolCity" placeholder="Mesto" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="schoolAddress" class="col-sm-3 col-form-label"></label>
						<div class="col">
							<input type="text" name="schoolAddress" class="form-control" id="schoolAddress" placeholder="Adresa" disabled required>
						</div>
					</div>
					<div class="form-group row">
						<label for="schoolName" class="col-sm-3 col-form-label"></label>
						<div class="col">
							<input type="hidden" id="schoolID" name="schoolID">
							<input type="text" name="schoolName" class="form-control" id="schoolName" placeholder="Názov Školy" disabled required>
						</div>
					</div>
					<div class="form-group row">
						<button type="submit" class="btn btn-dark btn-lg btn-block">Registrácia</button>
					</div>
					<div class="form-group row">
						<a href="login.php" class="btn btn-block btn-default bg-white">Naspäť na prihlásenie</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<script>
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
		confirm_password.onkeyup = validatePassword;
		
    	$("#schoolCity").autocomplete({
        	source: function (request, response) {
				jQuery.get("autocomplete.php", {
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
			$("#schoolAddress").prop("disabled",false);
		});
		$("#schoolAddress").autocomplete({
        	source: function (request, response) {
				jQuery.get("autocomplete.php", {
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
			jQuery.get("autocomplete.php", {
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
	</script>
</body>
</html>