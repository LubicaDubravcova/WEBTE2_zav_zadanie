<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
$sql = "SELECT routes.ID, routes.NAME, routes.LENGTH, routes.TYPE,users.FIRSTNAME, users.SURNAME, users.ACTIVE_ROUTE FROM routes 
JOIN users ON routes.OWNER=users.ID";
$result = $db->getResult($sql); //aby hodil error ked je chyba

$sql2 = "SELECT DISTINCT users.ID, users.FIRSTNAME, users.SURNAME FROM users JOIN routes ON users.ID=routes.OWNER";
$result2 = $db->getResult($sql2);

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
        <?php if ($role == "admin"): ?>
        <div class="form-group">
            <label for="sel">Užívateľ: </label>
            <select class="form-control" id="sel">
                <?php foreach($result2->fetch_all(MYSQLI_ASSOC) as $user): ?>
                <option value="<?php $user['ID']?>"> <?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
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
                        <?php if (($role != "admin") and (($user["TYPE"] == "Verejná") or ($user["TYPE"] == "Štafeta"))): ?>
                            <td><?php echo $user["NAME"]; ?></td>
                            <td><?php echo ($user["LENGTH"]/1000)." km"; ?></td>
                            <td>
                                <?php if ($user["ACTIVE_ROUTE"]!=NULL)
                                        echo "<img alt='active' src='images/green-dot.png'>";
                                    else
                                        echo "<img alt='pasive' src='images/red-dot.png'>";
                                ?>
                            </td>
                            <td><?php echo $user["TYPE"]; ?></td>
                            <td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
                        <?php elseif ($role == "admin") :?>
                            <td><?php echo $user["NAME"]; ?></td>
                            <td><?php echo ($user["LENGTH"]/1000)." km"; ?></td>
                            <td>
                                <?php if ($user["ACTIVE_ROUTE"]!=NULL)
                                    echo "<img alt='active' src='images/green-dot.png'>";
                                else
                                    echo "<img alt='pasive' src='images/red-dot.png'>";
                                ?>
                            </td>
                            <td><?php echo $user["TYPE"]; ?></td>
                            <td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
                        <?php endif; ?>
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
