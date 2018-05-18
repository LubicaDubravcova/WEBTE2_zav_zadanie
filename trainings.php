<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");

?>
<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php");
$id_record = $_GET['open'];
$db = new DBConn();
if (!$id_record) {
	$id_record = $userData->ID;
	$sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=" . $userData->ID;
	$result = $db->getAssoc($sql);
} elseif($userData->ROLE == "admin") {
    $sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=" . $id_record;
	$result = $db->getAssoc($sql);
}
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
                    </tbody>
                </table>
                <table class='table table-hover'>
                    <thead>
                    <tr>
                        <th>Priemerná odjazdená vzdialenosť</th>
                    </tr>
                    </thead>
                    <tbody id="loadAverage">
                    </tbody>
                </table>
            </div>
            <a id="savepdf" href="workers/printpdf.php?user=<?php echo $id_record?>"class="btn btn-dark text-white">Uložiť PDF</a>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script src="scripts/sorttable.js"></script>
<script type="text/javascript">
    function MyPrint() {
        var selectBox = document.getElementById("sel");
        var selectedValue = selectBox.options[selectBox.selectedIndex].value;
        alert(selectedValue);
    }
function reloadContent() {
	$("#loadTable").load("ajax/trainings.php #loadTable tr",{user:<?php echo $id_record;?>},fixSortOnAjax);
	$("#loadAverage").load("ajax/trainings.php #loadAverage",{user:<?php echo $id_record;?>});
}
$(document).ready(function(){
	reloadContent();
	setInterval(reloadContent,5000);
});
</script>
</body>
</html>