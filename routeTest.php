<?php header('Content-Type: text/html; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Zadanie 7</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg"></script>
	<style>
		#map {height: 500px;}
		#map .selected {font-weight: 500; color: black!important;}
	</style>
</head>
<body class="bg-dark text-white">
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h1 class="mt-5">Webové Technológie 2</h1>
			</div>
		</div>
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

