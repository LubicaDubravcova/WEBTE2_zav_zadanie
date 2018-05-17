<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
$database = $db->getDB();
$sql = "SELECT FIRSTNAME, SURNAME FROM users";
$result = $database->query($sql);
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
            <h2 class="m-4 d-inline-block">Zoznam používateľov</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
            <div class='table-responsive'>          
			  	<table class='table'>
					<thead>
						<tr>
							<th>#</th>
							<th>Meno</th>
							<th>Priezvisko</th>
							<th>Adresa</th>
							<th>Škola</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user): ?>
						<tr>
							<td>id</td>
							<td><?php echo $user["FIRSTNAME"]; ?></td>
							<td><?php echo $user["SURNAME"]; ?></td>
							<td>adresa</td>
							<td>skola</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
</body>
</html>

