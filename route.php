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
	// else ziskam progress
	$progress = null;
	if($route["TYPE"] == "Súkromná") {
		$progress = $db->getPrivateRouteProgress($_GET["routeId"]);
	}
	else if($route["TYPE"] == "Verejná") {
		$progress = $db->getPublicRouteProgress($_GET["routeId"]);
	}
	else if($route["TYPE"] == "Štafeta") {
		$progress = $db->getRelayRouteProgress($_GET["routeId"]);
	}

	// pridanie palety farieb
	require_once ("workers/routeColorPalette.php");
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
		.legenColorBlock {
			display: inline-block;
			width: 20px;
			height: 15px;
		}
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
				<?php if($route["TYPE"] == "Súkromná"): ?>
				<div class="row my-4">
					<div class="col">
						<h3 class="m-4 d-inline-block">Prekonaná vzdialenosť: <?php echo ($progress["LENGTH"]/1000)."km"; ?></h3>
					</div>
				</div>
				<div class="row my-4">
					<div class="col">
						<h3 class="m-4 d-inline-block">Prekonaná časť: <?php echo ($progress["LENGTH"]/$route["LENGTH"]*100)."%"; ?></h3>
					</div>
				</div>
				<?php endif; ?>
				<div class="row my-4">
					<div class="col">
						<div id="map" class="w-100"></div>
					</div>
				</div>
				<?php if($route["TYPE"] == "Verejná"): ?>
				<div class="table-responsive">
					<table class="table-hover">
						<tr id="header_row">
							<th>Farba</th><th>Meno</th><th>Prejdená vzdialenosť</th><th>Prejdená časť</th>
						</tr>
						<?php for($i = 0; $i < count($progress["NAME"]); $i++): ?>
						<tr>
							<td>
								<div class="legenColorBlock" style="background-color: <?php echo routeColorPalette::$subrouteColors[$i%count(routeColorPalette::$subrouteColors)]; ?>"></div>
							</td>
							<td>
								<?php echo $progress["NAME"][$i]; ?>
							</td>
							<td>
								<?php echo ($progress["LENGTH"][$i]/1000)."km"; ?>
							</td>
							<td>
								<?php echo ($progress["LENGTH"][$i]/$route["LENGTH"]*100)."%"; ?>
							</td>
						</tr>
						<?php endfor; ?>
					</table>
				</div>
				<?php endif; ?>
				<?php if($route["TYPE"] == "Štafeta"): ?>
					<div class="table-responsive">
						<table class="table-hover">
							<tr id="header_row">
								<th>Farba</th><th>Členovia týmu</th><th>Prejdená vzdialenosť</th><th>Prejdená časť</th>
							</tr>
							<?php for($i = 0; $i < count($progress["MEMBERS"]); $i++): ?>
								<tr>
									<td>
										<div class="legenColorBlock" style="background-color: <?php echo routeColorPalette::$subrouteColors[$i%count(routeColorPalette::$subrouteColors)]; ?>"></div>
									</td>
									<td>
										<?php echo $progress["MEMBERS"][$i]; ?>
									</td>
									<td>
										<?php echo ($progress["LENGTH"][$i]/1000)."km"; ?>
									</td>
									<td>
										<?php echo ($progress["LENGTH"][$i]/$route["LENGTH"]*100)."%"; ?>
									</td>
								</tr>
							<?php endfor; ?>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php require("includes/footer.php");?>
	<?php if($routeAccess) routeColorPalette::addToJavascript(); ?>
	<?php if($routeAccess): ?>
	<script type="text/javascript" src="scripts/displayRoute.js"></script>
	<script type="text/javascript">
		// nastavenie callbecku, aby sa pri nacitani stranky spustila mapa
		google.maps.event.addDomListener(window, "load", callback);

		var encodedPath = <?php echo json_encode($route["PATH"]); ?>;
		// dekodovanie pathu podla googlu
		var decodedPath = google.maps.geometry.encoding.decodePath(encodedPath);

		var routeLength = <?php echo $route["LENGTH"]; ?>;

		function callback() {

			var progress;
			<?php
				if($progress != null) {
					// treba extra osetrit private trasu, lebo funkcia na zobrazenie vyzaduje array
					if($route["TYPE"] == "Súkromná") {
						echo "progress = [".$progress["LENGTH"]."];";
					}
					else {
						echo "progress = ".json_encode($progress["LENGTH"]).";";
					}
				}
			?>

			// inicializujeme  mapu
			initMap();

			// az potom mozeme vykreslit trasu
			displayRoute(decodedPath, progress);
		}
	</script>
	<?php if($route["TYPE"] != "Súkromná"): ?>
	<script>
		// periodicke volanie serveru o update
		setInterval(AJAXRequst, 5000);

		function AJAXRequst() {
			// AJAX pre update tras
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					updateDisplay(JSON.parse(this.responseText));
				}
			};
			xhttp.open("POST", "workers/routeData.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("routeId=<?php echo $_GET["routeId"]; ?>");
		}

		function updateDisplay(dataArray) {
			// prekreslenie mapy
			removePolylines();
			displayRoute(decodedPath, dataArray.LENGTH);

			// update tabuliek
			updateTable(dataArray)
		}

		function updateTable(dataArray) {
			// vyprazdnit tabulku
			var table = document.getElementById("header_row").parentElement;

			while(table.childElementCount != 1) {
				table.removeChild(table.lastElementChild);
			}

			// naplnit tabulku
			for(var i = 0; i < dataArray.LENGTH.length; i++) {
				table.innerHTML += "<td>" +
					"<div class=\"legenColorBlock\" style=\"background-color: " + SUBROUTE_COLORS[i%SUBROUTE_COLORS.length] + "\"></div>" +
					"</td>"+
					"<td>"+
					dataArray.<?php if($route["TYPE"] == "Verejná") echo "NAME"; else echo "MEMBERS"; ?>[i]+
					"</td>"+
					"<td>"+
					dataArray.LENGTH[i]/1000 + "km"+
					"</td>"+
					"<td>"+
					(dataArray.LENGTH[i]/routeLength*100) + "%"+
					"</td>";
			}
		}
	</script>
	<?php endif; ?>
	<?php endif; ?>
</body>
</html>

