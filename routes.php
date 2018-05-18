<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
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
            <select class="form-control" id="sel" onchange="MyPrint();">
                <?php foreach($result2->fetch_all(MYSQLI_ASSOC) as $user): ?>
                <option value="<?php echo $user["ID"]; ?>"> <?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="col">
            <div class='table-responsive'>
                <table class='table sortable table-hover'>
                    <thead>
                    <tr>
                        <th>Trasa</th>
                        <th>Dĺžka</th>
                        <th>Aktívna</th>
                        <th>Mód</th>
                        <?php if ($role == "admin") :?>
                        <th>Pridal</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user): 
						if(($role == "admin") or (($role == "user") and (($user["TYPE"] != "Súkromná") or $user["ID"] == $userData->ID))): ?>
                        <tr class="clickable-row" data-href="route.php?routeId=<?php echo $user["ROUTE_ID"]; ?>">
                            <td><?php echo $user["ROUTE_NAME"]; ?></td>
                            <td><?php echo ($user["LENGTH"]/1000)." km"; ?></td>
                            <?php if ($userData->ACTIVE_ROUTE==$user["ROUTE_ID"]): ?>
                            <td><img alt='active' src='images/green-dot.png'>
                            <?php else: ?>
                            <td><img alt='pasive' src='images/red-dot.png'>
                            <?php endif; ?>
                            </td>
                            <td><?php echo $user["TYPE"]; ?></td>
                            <?php if($role == "admin"): ?>
                            <td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
                        	<?php endif; ?>
                        </tr>
                    <?php endif; endforeach; ?>
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