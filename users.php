<?php
include_once("workers/dbConn.php");
if (!isset($db)) $db = new DBConn();
//$result = $conn->getAssoc("SELECT FIRSTNAME, SURNAME FROM users");
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
            <?php echo "<div class='table-responsive'>          
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
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>
  </div>"; ?>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
</body>
</html>

