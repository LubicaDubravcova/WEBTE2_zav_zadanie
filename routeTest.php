<?php 
header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
$db = new DBConn();
?>

<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg"></script>
	<style>
		#map {height: 500px;}
		#map .selected {font-weight: 500; color: black!important;}
	</style>
</head>
<body class="bg-dark text-white">
	<?php require("includes/navbar.php"); ?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Test zobrazenia trasy</h2>
			</div>
		</div>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col-10">
				<div class="row">
					<div class="row my-4">
						<form method="get">
							<label for="routeStart">Zaciatok: </label>
							<input type="text" name="routeStart" id="routeStart"><br>
							<label for="routeEnd">Koniec: </label>
							<input type="text" name="routeEnd" id="routeEnd"><br>
						</form>
						<button type="button" onclick="sendRouteRequest()" >Najdi trasu</button>
					</div>
				</div>
				<div class="row my-4">
					<div class="col">
						<!-- Quick and dirty hack aby mala mapa aj vysku -->
						<div id="routeMap" class="w-100" style="min-height: 500px"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php require("includes/footer.php");?>
	<script type="text/javascript">

		function sendRouteRequest() {
			// DirectionRequest na ziskanie trasy
			var dirRequest = {
				origin: document.getElementById("routeStart").value,
				destination: document.getElementById("routeEnd").value,
				provideRouteAlternatives: false,
				travelMode: 'WALKING', // google vie hladat aj pre bicykle, ale asi tak v 10 krajinach sveta...
				unitSystem: google.maps.UnitSystem.METRIC
			}

			// overim, ci mam nejake udaje
			if(dirRequest.origin != "" && dirRequest.destination != "") {
				// ziskam trasu z googlu
				calcRoute(dirRequest);
			}
		}

	</script>
	<script type="text/javascript" src="scripts/displayRoute.js"></script>
</body>
</html>

