<?php
header('Content-Type: text/html; charset=utf-8');
include_once("dbConn.php");
$db = new DBConn();

$userData = $db->getUserData();

$routeData = $db->getRouteData($_POST["routeId"]);

if($routeData != null) {
	$routeProgress = null;
	if($routeData["TYPE"] == "Verejná") {
		$routeProgress = $db->getPublicRouteProgress($_POST["routeId"]);
	}
	else if($routeData["TYPE"] == "Štafeta") {
		$routeProgress = $db->getRelayRouteProgress($_POST["routeId"]);
	}

	if($routeProgress == null) {
		die();
	}

	// else: vypis tabulku
	include_once ("routeColorPalette.php");
?>
<!-- Zaciatok tabulky -->
	<div class="table-responsive">
		<table class="table-hover">
			<tr>
				<?php if($routeData["TYPE"] == "Verejná"): ?>
					<th>Farba</th><th>Meno</th><th>Prejdená vzdialenosť</th><th>Prejdená časť</th>
				<?php else: ?>
					<th>Farba</th><th>Členovia týmu</th><th>Prejdená vzdialenosť</th><th>Prejdená časť</th>
				<?php endif; ?>
			</tr>
	<!-- riadky tabulky -->
			<?php for($i = 0; $i < count($routeProgress["LENGTH"]); $i++): ?>
				<tr>
					<td>
						<div class="legenColorBlock" style="background-color: <?php echo routeColorPalette::$subrouteColors[$i%count(routeColorPalette::$subrouteColors)]; ?>"></div>
					</td>
					<td>
						<?php
						if($routeData["TYPE"] == "Verejná") {
							echo $routeProgress["NAME"][$i];
						}
						else {
							echo $routeProgress["MEMBERS"][$i];
						}
						?>
					</td>
					<td>
						<?php echo ($routeProgress["LENGTH"][$i]/1000)."km"; ?>
					</td>
					<td>
						<?php echo ($routeProgress["LENGTH"][$i]/$routeData["LENGTH"]*100)."%"; ?>
					</td>
				</tr>
			<?php endfor; ?>
	<!-- koniec tabulky -->
		</table>
	</div>
<?php
}
?>