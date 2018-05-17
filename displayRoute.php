<?php 
header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
$db = new DBConn();

$canDisplay = false;

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
	<?php
	// ziskam si trasu z databazy a overim, ci ma uzivatel pravo si ju zobrazit
	if(isset($_GET["routeId"])) {
		$route = $db->getRouteData($_GET["routeId"]);
		var_dump($route);
	}
	?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block"> <!-- TODO nazov trasy --> </h2>
			</div>
		</div>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col-10">
				<div class="row my-4">
					<div class="col">
						<div id="routeMap" class="w-100"></div>
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

