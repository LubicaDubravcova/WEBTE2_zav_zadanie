<?php
header('Content-Type: text/html; charset=utf-8');
include_once("../workers/dbConn.php");
if(isset($_POST["order"])) $ord = $_POST["order"]." ".$_POST["desc"];
else $ord = "routeID";
$db = new DBConn();
$userData = $db->getUserData();
if (isset($_POST["user"]) && ($_POST["user"]==$userData->ID || $userData->ROLE = "admin")) {
	$sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=" . $_POST['user'] . " ORDER BY $ord";
	$result = $db->getAssoc($sql);
}
?>
<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("../includes/head.php");?>
	<style>html {position: relative;min-height: 100%;}body {margin-bottom: 150px;}.footer {position: fixed;bottom: 0;width: 100%;height: auto;background-color: #f5f5f5;}#copyright {font-size: 10px;margin-bottom:5px;}</style>
</head>
<body class="bg-dark text-white">
<div class="container text-center">
	<div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam tréningov</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
			<table class='table sortable table-hover'>
				<thead>
				<tr>
					<th>Trasa</th>
					<th>Deň</th>
					<th>Odjazdená vzdialenosť</th>
					<th>Začiatok: čas</th>
					<th>Koniec: čas</th>
					<th>Začiatok: GPS</th>
					<th>Koniec: GPS</th>
					<th>Hodnotenie</th>
					<th>Poznámka</th>
					<th>Priemerná rýchlosť</th>
				</tr>
				</thead>
				<tbody id="loadTable">
					<?php $lengthSum = 0; if ($result)
					foreach($result as $i=>$res): 
					$lengthSum += $res['LENGTH']/1000; ?>
					<tr>
						<td><a href='route.php?routeID=<?php echo $res['routeID'];?>'> <?php echo $res['NAME'];?></a></td>
						<td><?php echo $res['DATE'];?></td>
						<td><?php echo $res['LENGTH']/1000;?>km</td>
						<td><?php echo $res['START_TIME'];?></td>
						<td><?php echo $res['END_TIME'];?></td>
						<td>
						<?php if($res['START_LAT'] != "") echo round($res['START_LAT'], 3);
							if($res['START_LAT'] != "" && $res['START_LNG'] != "") echo ", ";
							if($res['START_LNG'] != "") echo round($res['START_LNG'], 3); ?>
						</td>
						<td>
						<?php if($res['END_LAT'] != "") echo round($res['END_LAT'], 3);
						if($res['END_LAT'] != "" && $res['END_LNG'] != "") echo ", ";
						if($res['END_LNG'] != "") echo round($res['END_LNG'], 3); ?>
						</td>
						<td><?php echo $res['RATING'];?></td>
						<td><?php echo $res['NOTES'];?></td>
						<td>
						<?php $avgSpeed = "neznáma";
						if($res['END_TIME'] != "" && $res['START_TIME'] != ""){
							$startTime = strtotime($res['START_TIME']);
							$endTime = strtotime($res['END_TIME']);
							$duration = $endTime - $startTime;
							$avgSpeed = ($res['LENGTH']/1000) / ($duration/3600);
						}
						echo $avgSpeed;
						if($avgSpeed != "neznáma")
						echo "km/h"; ?>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<table class='table table-hover'>
				<thead>
				<tr>
					<th>Priemerná odjazdená vzdialenosť</th>
				</tr>
				</thead>
				<tbody>
					<td id="loadAverage">
						<?php
						if($i != 0) {
							echo $lengthSum/$i . "km/tréning";
						} else {
							echo 0 . "km/tréning";
						}
						?>
					</td>
				</tbody>
			</table>
		</div>
    </div>
</div>

<?php $role = false; include("../includes/footer.php")?>
</body>
</html>