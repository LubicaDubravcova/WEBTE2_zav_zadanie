<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<style>
		#map {height: 500px;}
		.controls {
			margin-top: 10px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		#origin-input,
		#destination-input {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 200px;
		}

		#origin-input:focus,
		#destination-input:focus {
			border-color: #4d90fe;
		}
	</style>
</head>
<body class="bg-dark text-white">
	<?php require("includes/navbar.php"); ?>
	<?php
	// spracovanie formulara

	if (isset($_POST['path'])) {
		include_once("workers/dbConn.php");

		$dbconn = new dbConn();

		$routeCreateFailed = false;

		// overim, ci mam vyplnene vsetky udaje
		if(isset($_POST["length"]) && isset($_POST["type"]) && isset($_POST["name"])) {

			// kontrola, ci ma user spravnu rolu na zvoleny typ trasy
			if($_POST["type"] != "1" && $role != "admin") {
				$routeCreateFailed = true;
			}

			if(!$routeCreateFailed) {
				$routeId = $dbconn->createRoute($_POST["name"], $_POST['path'], $_POST["type"], $userData->ID ,$_POST["length"]);

				// TODO nastavit trasu ako aktivnu, pokial user nema aktivnu trasu (&& trasa nie je stafetova)
			}
		}
		else {
			$routeCreateFailed = true;
		}
	}
	?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Vytvorenie novej trasy</h2>
			</div>
		</div>
		<?php if ($routeCreateFailed) echo "<div class='row'><div class='btn btn-block btn-danger disabled'>Trasu sa nepodarilo pridať. Skontrolujte správnosť zadaných údajov.</div></div>"?>
		<?php if (!$routeCreateFailed) echo "<div class='row'><div class='btn btn-block btn-success disabled'>Trasa bola úspešne pridaná.</div></div>"?>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col">
				<form method="post" id="form">
					<div class="form-group">
						<label for="name">Názov trasy:</label>
						<input type="text" class="form-control" name="name" id="name" required placeholder="Zadajte názov trasy">
						<label for="length_display">Dĺžka trasy:</label>
						<input type="text" class="form-control" name="length_display" id="length_display" disabled required>

						<label for="type">Typ trasy:</label>
						<select class="form-control" id="type" name="type">
							<option value="1" selected>Súkormná trasa</option>
							<option value="2" <?php if ($role != "admin") {echo "disabled";}?> >Verejná trasa</option>
							<option value="3" <?php if ($role != "admin") {echo "disabled";}?> >Štafetová trasa</option>
						</select>

						<input type="hidden" name="length" id="length" required>
						<input type="hidden" name="path" id="path" required>
					</div>
				</form>

				<input id="origin-input" class="controls" type="text"
					   placeholder="Zadajte začiatok trasy">

				<input id="destination-input" class="controls" type="text"
					   placeholder="Zadajte koniec trasy">
				<div id="map" class="w-100"></div>

				<button type="submit" class="btn btn-default" form="form">Pridať trasu</button>

			</div>
		</div>
	</div>
	<?php// require("includes/footer.php");?>
	<script type="text/javascript" src="scripts/addRoute.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=places&callback=initMap"
			async defer></script>
</body>
</html>

