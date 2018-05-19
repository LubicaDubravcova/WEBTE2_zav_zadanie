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
						<h3 class="m-4 d-inline-block">Dĺžka: <?php echo number_format($route["LENGTH"]/1000,2,","," "). "km" ?></h3>
					</div>
				</div>
				<?php if($route["TYPE"] == "Súkromná"): ?>
				<div class="row my-4">
					<div class="col">
						<h3 class="m-4 d-inline-block">Prekonaná vzdialenosť: <?php echo  number_format($progress["LENGTH"]/1000,2,","," ")."km"; ?></h3>
					</div>
				</div>
				<div class="row my-4">
					<div class="col">
						<h3 class="m-4 d-inline-block">Prekonaná časť: <?php
							$percento = ($progress["LENGTH"]/$route["LENGTH"]*100);
							if($percento > 100) echo "100%";
							else echo number_format($percento,2,","," ")."%";
							?></h3>
					</div>
				</div>
				<?php endif; ?>
				<div class="row my-4">
					<div class="col">
						<div id="map" class="w-100"></div>
					</div>
				</div>
				<?php if($route["TYPE"] != "Súkromná"): ?>
				<div class="row my-4">
					<div class="table-responsive">
						<table class="table-hover sortable">
							<thead>
								<tr>
								<?php if($route["TYPE"] == "Verejná"): ?>
									<th>Farba</th><th>Meno</th><th class="sorttable_numeric">Prejdená vzdialenosť</th><th class="sorttable_numeric">Prejdená časť</th>
								<?php else: ?>
									<th>Farba</th><th>Členovia tímu</th><th class="sorttable_numeric">Prejdená vzdialenosť</th><th class="sorttable_numeric">Prejdená časť</th>
								<?php if(($userData->ROLE == "admin")): ?>
									<th class="sorttable_nosort">Správa tímov</th>
								<?php endif; ?>
								<?php endif; ?>
								</tr>
							</thead>
							<tbody id="load">
							<?php for($i = 0; $i < count($progress["LENGTH"]); $i++): ?>
								<tr>
									<td sorttable_customkey="<?php echo $i?>">
										<div class="legenColorBlock" style="background-color: <?php echo routeColorPalette::$subrouteColors[$i%count(routeColorPalette::$subrouteColors)]; ?>"></div>
									</td>
									<td>
										<?php
										if($route["TYPE"] == "Verejná") {
											echo $progress["NAME"][$i];
										}
										else {
											echo $progress["MEMBERS"][$i];
										}
										?>
									</td>
									<td>
										<?php echo number_format($progress["LENGTH"][$i]/1000,2,","," ")."km"; ?>
									</td>
									<td>
										<?php $percento = ($progress["LENGTH"][$i]/$route["LENGTH"]*100);
										if($percento > 100) echo "100%";
										else echo number_format($percento,2,","," ")."%"; ?>
									</td>
									<?php if($route["TYPE"] == "Štafeta" && $userData->ROLE == "admin"): ?>
									<td>
										<a class="btn" href="add-team.php?teamID=<?php echo $progress["TID"][$i]; ?>&routeID=<?php echo $_GET["routeId"]; ?>">Upraviť tím</a>
									</td>
									<?php endif; ?>
								</tr>
							<?php endfor; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
				<?php if($route["TYPE"] == "Štafeta" && $userData->ROLE == "admin"): ?>
				<div class="row my-4">
					<a class="btn" href="add-team.php?routeID=<?php echo $_GET["routeId"]; ?>">Pridať tím</a>
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
			$.post("workers/routeData.php",{routeId: "<?php echo $_GET["routeId"];?>"}, updateDisplay);
		}

		function updateDisplay(data) {
			// prekreslenie mapy
			var dataArray = JSON.parse(data);
			removePolylines();

			if(dataArray) {
				displayRoute(decodedPath, dataArray.LENGTH, false);
			}
			else {
				displayRoute(decodedPath, [], false);
			}
		}
		
		//nacita sameho seba (musi byt tbody, nacitava tr), parametre: ID, boolean fixsort - len v pripade ze je to tabulka so sortom, query - ak string tak get, ak object tak post, na poslanie vlastneho pouzite $_SERVER['QUERY_STRING'];
		selfLoad("#load",true,"routeId=<?php echo $_GET["routeId"];?>");
		
		$(document).ready(function(){
			setInterval(AjaxMap, 5000);
		});
	</script>
	<?php endif; endif;?>
</body>
</html>

