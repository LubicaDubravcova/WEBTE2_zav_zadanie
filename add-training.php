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

	$trainingCreationAttempted = false;

	if (isset($_POST['length'])) {
		$trainingCreationAttempted = true;

		include_once("workers/dbConn.php");

		$dbconn = new dbConn();

		$trainingCreateFailed = false;

		// kontrola, ci mam zvolenu aktivnu trasu
		if($userData->ACTIVE_ROUTE != null) {
			// pridam trening do DB
			$date = null;
			if(isset($_POST['date']) && $_POST['date'] != "") {
				$date = $_POST['date'];
			}
			$time_start = null;
			if(isset($_POST['time_start']) && $_POST['time_start'] != "") {
				$time_start = $_POST['time_start'];
			}
			$time_end = null;
			if(isset($_POST['time_end']) && $_POST['time_end'] != "") {
				$time_end = $_POST['time_end'];
			}
			$lat_start = null;
			if(isset($_POST['lat_start']) && $_POST['lat_start'] != "") {
				$lat_start = $_POST['lat_start'];
			}
			$lng_start = null;
			if(isset($_POST['lng_start']) && $_POST['lng_start'] != "") {
				$lng_start = $_POST['lng_start'];
			}
			$lat_end = null;
			if(isset($_POST['lat_end']) && $_POST['lat_end'] != "") {
				$lat_end = $_POST['lat_end'];
			}
			$lng_end = null;
			if(isset($_POST['lng_end']) && $_POST['lng_end'] != "") {
				$lng_end = $_POST['lng_end'];
			}
			$rating = null;
			if(isset($_POST['rating']) && $_POST['rating'] != "") {
				$rating = $_POST['rating'];
			}

			$note = null;
			if(isset($_POST['notes']) && $_POST['notes'] != "") {
				$note = $_POST['notes'];
			}

			$dbconn->addTraining($_POST['length'], $date, $time_start, $time_end, $lat_start, $lng_start, $lat_end, $lng_end, $rating, $note, $userData->ID, $userData->ACTIVE_ROUTE);
		}
	}
	?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Pridanie tréningu</h2>
			</div>
		</div>
		<?php if($userData->ACTIVE_ROUTE == null): ?>
			<div class='row'><div class='btn btn-block btn-danger disabled'>Nie je možné pridať tréning, pokým si nezvolíte aktívnu trasu.</div></div>
		<?php else: ?>
		<?php if ($trainingCreationAttempted && $trainingCreateFailed) echo "<div class='row'><div class='btn btn-block btn-danger disabled'>Trasu sa nepodarilo pridať. Skontrolujte správnosť zadaných údajov.</div></div>"?>
		<?php if ($trainingCreationAttempted && !$trainingCreateFailed) echo "<div class='row'><div class='btn btn-block btn-success disabled'>Trasa bola úspešne pridaná.</div></div>"?>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col">
				<form method="post">
					<div class="form-group">
						<label for="length_km">Dĺžka prekonanej trasy [km]:</label>
						<input type="number" step="any" min="0" class="form-control" name="length_km" id="length_km" required placeholder="Zadajte dĺžku trasy v km" onchange="kmChange()">
						<input type="hidden" name="length" id="length" required>
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

						<label for="rating">Subjektívne hodnotenie tréningu:</label><br>
						<label class="radio-inline"><input type="radio" name="rating" value="1">:&#593;</label>
						<label class="radio-inline"><input type="radio" name="rating" value="2">:(</label>
						<label class="radio-inline"><input type="radio" name="rating" value="3">:|</label>
						<label class="radio-inline"><input type="radio" name="rating" value="4">:)</label>
						<label class="radio-inline"><input type="radio" name="rating" value="5">:D</label>
						<br>

						<label for="notes">Poznámka ku tréningu:</label>
						<textarea class="form-control" rows="3" name="notes" id="notes" maxlength="200"></textarea>

						<input type="hidden" name="lat_start" id="lat_start">
						<input type="hidden" name="lng_start" id="lng_start">
						<input type="hidden" name="lat_end" id="lat_end">
						<input type="hidden" name="lng_end" id="lng_end">

						<button type="submit" class="btn btn-default">Pridať tréning</button>
					</div>
				</form>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php require("includes/footer.php");?>
	<script>
		// listener nech su kilometre a metre rovnake
		function kmChange() {
			document.getElementById("length").value = document.getElementById("length_km").value*1000;
		}
	</script>
	<script type="text/javascript" src="scripts/addRoute.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=places&callback=initMap"
			async defer></script>
</body>
</html>

