<?php
header('Content-Type: text/html; charset=utf-8');
include_once("../workers/dbConn.php");
$db = new DBConn();

$userData = $db->getUserData();

$routeData = $db->getRouteData($_POST["routeId"]);

if($routeData != null):
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
	include_once("../workers/routeColorPalette.php");
		for($i = 0; $i < count($routeProgress["LENGTH"]); $i++): ?>
				<tr>
					<td sorttable_customkey="<?php echo $i?>">
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
						<?php echo number_format($routeProgress["LENGTH"][$i]/1000,2,","," ")."km"; ?>
					</td>
					<td>
						<?php $percento = ($routeProgress["LENGTH"][$i]/$routeData["LENGTH"]*100);
						if($percento > 100) echo "100%";
						else echo number_format($percento,2,","," ")."%"; ?>
					</td>
					<?php if($routeData["TYPE"] == "Štafeta" && $userData->ROLE == "admin"): ?>
					<td>
						<a class="btn" href="add-team.php?teamID=<?php echo $routeProgress["TID"][$i]; ?>&routeID=<?php echo $_POST["routeId"]; ?>">Upraviť tím</a>
					</td>
					<?php endif; ?>
				</tr>
			<?php endfor; ?>
<?php endif; ?>
