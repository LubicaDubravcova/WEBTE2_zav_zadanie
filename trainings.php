<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php if(!$printPDF) require("includes/navbar.php");
	else {
		include_once("dbConn.php");
		$db = new DBConn();
		$userData = $db->getUserData();
	}
if(isset($_GET["order"]) && $_GET["order"] != "undefined") $ord = $_GET["order"]." ".$_GET["desc"];
else $ord = "routeID";
$id_record = $_GET['id'];
if (($userData->ROLE != "admin") || !$id_record) $id_record = $userData->ID;
$sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=$id_record ORDER BY $ord";
$result = $db->getAssoc($sql);
?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam tréningov</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
            <div class='table-responsive'>
                <table class='table sortable table-hover'>
                    <thead>
                    <tr>
                        <th data-order="NAME">Trasa</th>
                        <th data-order="DATE">Deň</th>
                        <th data-order="LENGTH" class="sorttable_numeric">Odjazdená vzdialenosť</th>
                        <th data-order="START_TIME">Začiatok: čas</th>
                        <th data-order="END_TIME">Koniec: čas</th>
                        <th data-order="START_LAT">Začiatok: GPS</th>
                        <th data-order="END_LAT">Koniec: GPS</th>
                        <th data-order="RATING">Hodnotenie</th>
                        <th data-order="NOTES">Poznámka</th>
                        <th>Priemerná rýchlosť</th>
                    </tr>
                    </thead>
                    <tbody id="loadTable">
					<?php $lengthSum = 0; if ($result)
					foreach($result as $i=>$res): 
					$lengthSum += $res['LENGTH']/1000; ?>
					<tr class="clickable-row" data-href="route.php?routeId=<?php echo $res["routeID"]; ?>">
						<td> <?php echo $res['NAME'];?></td>
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
                    <tbody id="loadAverage">
						<td>
							<?php if($i != 0) echo $lengthSum/$i . "km/tréning";
							 else echo 0 . "km/tréning"; ?>
						</td>
                    </tbody>
                </table>
            </div>
            <?php if(!$printPDF):?>
            <a id="savepdf" href="workers/printpdf.php?user=<?php echo $id_record?>"class="btn btn-dark text-white">Uložiť PDF</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script type="text/javascript">

function reloadContent() {
	$("#loadTable").load(document.URL + " #loadTable tr","<?php echo $_SERVER['QUERY_STRING'];?>",function(data){
		$("#loadAverage").html($(data).find("#loadAverage").html());
		fixSortOnAjax();
	});
}
reloadContent();
setInterval(reloadContent,5000);
	
$("#savepdf").click(function(e){
	e.preventDefault();
	var $th = $(".sorttable_sorted,.sorttable_sorted_reverse");
	var ord = $th.data("order");
	if ($th.hasClass("sorttable_sorted"))
		window.location = this.href + "&order=" + ord;
	else 
		window.location = this.href + "&order=" + ord + "&desc=desc";
});
</script>
</body>
</html>
