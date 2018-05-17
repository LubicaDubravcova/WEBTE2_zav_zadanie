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

	$routeCreationAttempted = false;

	// TODO spracovanie pridania
	/*
	if (isset($_POST['path'])) {
		$routeCreationAttempted = true;

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

				if($userData->ACTIVE_ROUTE == null) {
					// pridam novu trasu ako aktivnu
					$dbconn->setActiveRoute($routeId);
				}
			}
		}
		else {
			$routeCreateFailed = true;
		}
	}
	*/
	?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Pridanie tréningu</h2>
			</div>
		</div>
		<!--
		<?php if ($routeCreationAttempted && $routeCreateFailed) echo "<div class='row'><div class='btn btn-block btn-danger disabled'>Trasu sa nepodarilo pridať. Skontrolujte správnosť zadaných údajov.</div></div>"?>
		<?php if ($routeCreationAttempted && !$routeCreateFailed) echo "<div class='row'><div class='btn btn-block btn-success disabled'>Trasa bola úspešne pridaná.</div></div>"?>
		-->
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col">
				<form method="post">
					<div class="form-group">
						<label for="length_km">Dĺžka prekonanej trasy [km]:</label>
						<input type="number" step="any" min="0" class="form-control" name="length_km" id="length_km" required placeholder="Zadajte dĺžku trasy v km">
						<input type="hidden" name="length" id="length" required>
						<input type="hidden" name="path" id="path" disabled>
					</div>

				<input id="origin-input" class="controls" type="text"
					   placeholder="Zvoľte začiatok tréningu">

				<input id="destination-input" class="controls" type="text"
					   placeholder="Zvoľte koniec tréningu">
				<div id="map" class="w-100"></div>

					<div class="form-group">
						<label for="date">Dátum tréningu:</label>
						<input type="date" name="date" id="date" class="form-control">
						<label for="time_start">Čas začiatku tréningu:</label>
						<input type="time" name="time_start" id="time_start" class="form-control">
						<label for="time_end">Čas konca tréningu:</label>
						<input type="time" name="time_end" id="time_end" class="form-control">

						<label for="rating">Subjektívne hodnotenie tréningu:</label>
						<label class="radio-inline"><input type="radio" name="rating" value="1">:&#593;</label>
						<label class="radio-inline"><input type="radio" name="rating" value="2">:(</label>
						<label class="radio-inline"><input type="radio" name="rating" value="3">:|</label>
						<label class="radio-inline"><input type="radio" name="rating" value="4">:)</label>
						<label class="radio-inline"><input type="radio" name="rating" value="5">:D</label>

						<label for="notes">Poznámka ku tréningu:</label>
						<textarea class="form-control" rows="3" id="notes"></textarea>

						<button type="submit" class="btn btn-default">Pridať tréning</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php require("includes/footer.php");?>
	<script type="text/javascript" src="scripts/addRoute.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=places&callback=initMap"
			async defer></script>
</body>
</html>

