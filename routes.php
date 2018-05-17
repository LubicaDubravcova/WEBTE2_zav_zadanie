<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
$sql = "SELECT routes.ID, routes.NAME, routes.LENGTH, routes.TYPE,users.FIRSTNAME, users.SURNAME FROM routes 
JOIN users ON routes.OWNER=users.ID";
$result = $db->getResult($sql); //aby hodil error ked je chyba
?>
<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php"); ?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam trás</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <?php
        /*
        // overim si, ci ma uzivatel pravo trasu zobrazit
        $routeAccess = false;
        if($role == "admin") {
            $routeAccess = true;
        }
        // trasa je public/stafeta
        else if($route["TYPE"] != 1) {
            $routeAccess = true;
        }
        // user je vlastnikom trasy
        else if($route["OWNER"] == $userData->ID) {
            $routeAccess = true;
        }
*/
        ?>
        <div class="form-group">
            <label for="sel">Užívateľ: </label>
            <select class="form-control" id="sel">
                <option value="2" <?php if ($role != "admin") {echo "disabled";}?> > meno </option>
            </select>
        </div>
        <div class="col">
            <div class='table-responsive'>
                <table class='table sortable'>
                    <thead>
                    <tr>
                        <th>Trasa</th>
                        <th>Dĺžka</th>
                        <th>Typ</th>
                        <th>Mód</th>
                        <th>Pridal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user): ?>
                        <tr>
                            <td><?php echo $user["NAME"]; ?></td>
                            <td><?php echo ($user["LENGTH"]/1000)." km"; ?></td>
                            <td>aktivna/pasivna</td>
                            <td><?php echo $user["TYPE"]; ?></td>
                            <td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script src="scripts/sorttable.js"></script>
</body>
</html>

