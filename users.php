<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
$database = $db->getDB();
$sql = "SELECT users.ID, users.FIRSTNAME, users.SURNAME, addresses.ADDRESS, addresses.CITY, addresses.PSC, schools.NAME  
FROM users JOIN addresses ON users.ADDRESS_ID=addresses.ID
JOIN schools ON users.SCHOOL_ID=schools.ID";
//JOIN adresses ON schools.ADDRESS_ID=addresses.ID";
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
                        <th>Užívateľ</th>
                        <th>Adresa</th>
                        <th>Škola</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user): ?>
                        <tr>
                            <td><?php echo $user["ID"]; ?></td>
                            <td>
                                <a href='index.php?open=<?php echo $user["ID"]; ?>'>
                                    <?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?>
                                </a>
                            </td>
                            <td><?php echo $user["ADDRESS"].", ".$user["CITY"]." ".$user["PSC"]; ?></td>
                            <td><?php echo $user["NAME"]; ?></td>
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