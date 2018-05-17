<?php
include_once("workers/dbConn.php");
if (!isset($db)) $db = new DBConn();
$database = $db->getDB();

if (isset($_GET['news'])){
	$res = $database->query("SELECT Nazov, Text FROM news WHERE ID = ".$_GET['news']);
    $array = $res->fetch_all(MYSQLI_ASSOC);
} else {
	$res = $database->query("SELECT Nazov, Text FROM news");
    $array = $res->fetch_all(MYSQLI_ASSOC);
}
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
            <h2 class="m-4 d-inline-block">Aktuality</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
			<?php $first = true; foreach($array as $assoc): 
			if($first) $first = false;
			else echo "<hr>" ?>
       		<div class="row">
       			<div class="col">
					<h3><?php echo $assoc["Nazov"]; ?></h3>
					<p><?php echo $assoc["Text"]; ?></p>
       			</div>
       		</div>
    		<?php endforeach;?>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
</body>
</html>