<?php 
ini_set('display_errors', 1);

/*include_once("workers/dbConn.php"); Nieje treba, v navbare sa nacitavaju data do premennej $userData
$db = new DBConn();
$data = $db->getUserData();
var_dump($data);
$id = $data->id;*/
?>

<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg"></script>
	<style>
		#map {height: 500px;}
		#map .selected {font-weight: 500; color: black!important;}
	</style>
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
		<form>
			<input type="button" value="Odoberať aktuality" onclick="changeSubscribe1(<?php echo $userData->ID ?>)">
			<input type="button" value="Zrušiť odber" onclick="changeSubscribe2(<?php echo $userData->ID ?>)">
		</form>
		</div>
	</div>

	<script type="text/javascript" src="scripts/checkSubscribe.js"></script>
</body>
</html>