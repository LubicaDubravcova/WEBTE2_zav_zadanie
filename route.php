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
						<h3 class="m-4 d-inline-block">Prekonaná časť: <?php
							$percento = ($progress["LENGTH"]/$route["LENGTH"]*100);
							if($percento > 100) echo "100%";
							else echo $percento."%";
							?></h3>
					</div>
				</div>
				<?php endif; ?>
				<div class="row my-4">
					<div class="col">
						<div id="map" class="w-100"></div>
					</div>
				</div>
				<?php if($routeAccess): ?>
				<div class="row my-4">
					<div class="table-responsive">
						<table class="table-hover sortable">
							<thead>
								<tr>
								<?php if($route["TYPE"] == "Verejná"): ?>
									<th>Farba</th><th>Meno</th><th class="sorttable_numeric">Prejdená vzdialenosť</th><th class="sorttable_numeric">Prejdená časť</th>
								<?php else: ?>
									<th>Farba</th><th>Členovia týmu</th><th class="sorttable_numeric">Prejdená vzdialenosť</th><th class="sorttable_numeric">Prejdená časť</th>
								<?php if(($userData->ROLE == "admin")): ?>
									<th class="sorttable_nosort">Spáva tímov</th>
								<?php endif; ?>
								<?php endif; ?>
								</tr>
							</thead>
							<tbody id="load">
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
				<?php if($route["TYPE"] == "Štafeta" && $userData->ROLE == "admin"): ?>
				<div class="row my-4">
					<a class="btn" href="add-team.php?routeID=<?php echo $_POST["routeId"]; ?>">Pridať tím</a>
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

		// callback google inicializacie mapy
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
			displayRoute(decodedPath, progress, true);
		}
	</script>
	<?php if($route["TYPE"] != "Súkromná"): ?>
	<script>

		function AjaxMap() {
			// AJAX pre update tras na mape
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
			displayRoute(decodedPath, dataArray.LENGTH, false);
		}

		function AjaxTable() {
			$("#load").load("workers/routeTable.php",{routeId: "<?php echo $_GET["routeId"];?>"},fixSortOnAjax);
		}
		$(document).ready(function(){
			setInterval(AjaxMap, 5000);
			AjaxTable();
			setInterval(AjaxTable,5000);
		});
	</script>
	<?php endif; endif;?>
	<script src="scripts/sorttable.js"></script>
</body>
</html>

