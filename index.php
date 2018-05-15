<?php 
header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
$db = new DBConn();
?>

<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=places"></script>
	<script src="scripts/markerclusterer.js"></script>
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
				<h1 class="mt-5">Webové Technológie 2</h1>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Mapa používateľov</h2>
			</div>
		</div>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col-10">
				<div class="row my-4">
					<div class="col">
						<div id="map" class="w-100"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<a href="login.php" class="btn btn-block btn-dark">Prihlásenie</a>
					</div>
					<div class="col-6">
						<a href="register.php" class="btn btn-block btn-dark">Registrácia</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
    	var locations = <?php echo json_encode($db->getAssoc("SELECT a.lat, a.lng FROM users u INNER JOIN addresses a ON u.ADDRESS_ID = a.ID"));?>;
		var schools = <?php echo json_encode($db->getAssoc("SELECT a.lat, a.lng, s.name FROM users u INNER JOIN schools s ON u.SCHOOL_ID = s.ID INNER JOIN addresses a ON s.ADDRESS_ID = a.ID"));?>;
	</script>
	<script type="text/javascript" src="scripts/map.js"></script>
</body>
</html>

