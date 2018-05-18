<?php 
header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
$db = new DBConn();

$route = null;

if(isset($_GET["routeId"])) {
	$route = $db->getRouteData($_GET["routeId"]);
	if($route == null) {
		header('Location: index.php');
		die();
	}
}
else {
	// presmerujem inam
	header('Location: index.php');
	die();
}

?>

<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=geometry"></script>
	<style>
		#map {height: 500px;}
		#map .selected {font-weight: 500; color: black!important;}
	</style>
</head>
<body class="bg-dark text-white">
	<?php require("includes/navbar.php"); ?>
	<?php
	// overim si, ci ma uzivatel pravo trasu zobrazit
	$routeAccess = false;
	if($role == "admin") {
		$routeAccess = true;
	}
	// trasa je public/stafeta
	else if($route["TYPE"] != "Súkromná") {
		$routeAccess = true;
	}
	// user je vlastnikom trasy
	else if($route["OWNER"] == $userData->ID) {
		$routeAccess = true;
	}

	?>
	<div class="container text-center">
		<?php if(!$routeAccess): ?>
			<div class='row'><div class='btn btn-block btn-danger disabled'>Nemáte právo zobraziť podrobnosti tejto trasy.</div></div>
		<?php else: ?>
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Trasa: <?php echo $route["NAME"] ?></h2>
			</div>
		</div>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col-10">
				<div class="row my-4">
					<div class="col">
						<h3 class="m-4 d-inline-block">Dĺžka: <?php echo $route["LENGTH"]/1000 . "km" ?></h3>
					</div>
				</div>
				<div class="row my-4">
					<div class="col">
						<div id="map" class="w-100"></div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php require("includes/footer.php");?>
	<?php if($routeAccess): ?>
	<script type="text/javascript" src="scripts/displayRoute.js"></script>
	<script type="text/javascript">
		// nastavenie callbecku, aby sa pri nacitani stranky spustila mapa
		google.maps.event.addDomListener(window, "load", callback);

		function callback() {
			var encodedPath = <?php echo json_encode($route["PATH"]); ?>;

			// prekonvertujem path z encoded verzie na decoded
			var decodedPath = google.maps.geometry.encoding.decodePath(encodedPath);

			// inicializujeme  mapu
			initMap();

			// az potom mozeme vykreslit trasu
			// TODO pole s vykonmi
			displayRoute(decodedPath, []);
		}
	</script>
	<?php endif; ?>
</body>
</html>

