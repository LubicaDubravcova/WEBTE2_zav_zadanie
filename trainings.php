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
if (!$id_record)
$sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=" . $userData->ID;
else
    $sql = "SELECT routes.ID AS routeID, `NAME`, `DATE`, trainings.LENGTH, START_TIME, END_TIME, START_LAT, START_LNG, END_LAT, END_LNG, RATING, NOTES FROM trainings JOIN routes ON trainings.ROUTE_ID=routes.ID WHERE USER_ID=" . $id_record;
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
                    <tbody>
                        <?php
                            $i = 0;
                            $lengthSum = 0;
                            while($result[$i]){
                                $lengthSum += $result[$i]['LENGTH']/1000;
                                echo "<tr><td><a href='route.php?routeID=" . $result[$i]['routeID'] . "'>" . $result[$i]['NAME'] . "</a></td><td>" . $result[$i]['DATE'] . "</td><td>" . ($result[$i]['LENGTH']/1000) . "Km</td><td>" . $result[$i]['START_TIME'] . "</td><td>" . $result[$i]['END_TIME'] . "</td><td>";
                                if($result[$i]['START_LAT'] != "") echo round($result[$i]['START_LAT'], 3);
                                if($result[$i]['START_LAT'] != "" && $result[$i]['START_LNG'] != "") echo ", ";
                                if($result[$i]['START_LNG'] != "") echo round($result[$i]['START_LNG'], 3);
                                echo "</td><td>";
                                if($result[$i]['END_LAT'] != "") echo round($result[$i]['END_LAT'], 3);
                                if($result[$i]['END_LAT'] != "" && $result[$i]['END_LNG'] != "") echo ", ";
                                if($result[$i]['END_LNG'] != "") echo round($result[$i]['END_LNG'], 3);
                                echo "</td><td>" . $result[$i]['RATING'] . "</td><td>" . $result[$i]['NOTES'] . "</td>";
                                $avgSpeed = "neznáma";
                                if($result[$i]['END_TIME'] != "" && $result[$i]['START_TIME'] != ""){
                                    $startTime = strtotime($result[$i]['START_TIME']);
                                    $endTime = strtotime($result[$i]['END_TIME']);
                                    $duration = $endTime - $startTime;
                                    $avgSpeed = ($result[$i]['LENGTH']/1000) / ($duration/3600);
                                }
                                echo "<td>" . $avgSpeed;
                                if($avgSpeed != "neznáma")
                                    echo "Km/h";
                                echo "</td></tr>";
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
                <table class='table sortable table-hover'>
                    <thead>
                    <tr>
                        <th>Priemerná odjazdená vzdialenosť</th>
                    </tr>
                    </thead>
                    <tbody>
                        <td>
                            <?php
                            if($i != 0) {
                                echo $lengthSum/$i . "Km/tréning";
                            } else{
                                echo 0 . "Km/tréning";
                            }
                            ?>
                        </td>
                    </tbody>
                </table>
            </div>
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
</script>
</body>
</html>