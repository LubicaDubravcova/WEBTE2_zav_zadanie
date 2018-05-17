<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<style>
		#map {height: 500px;}
		.controls {
			margin-top: 10px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		#origin-input,
		#destination-input {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 200px;
		}

		#origin-input:focus,
		#destination-input:focus {
			border-color: #4d90fe;
		}
	</style>
</head>
<body class="bg-dark text-white">
	<?php require("includes/navbar.php"); ?>
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Vytvorenie novej trasy</h2>
			</div>
		</div>
		<div class="row justify-content-center bg-light text-dark rounded p-5">
			<div class="col">
				<form method="post">
					<div class="form-group">
						<label for="name">Názov trasy:</label>
						<input type="text" class="form-control" name="name" id="name">
						<label for="length">Dĺžka trasy v metroch:</label>
						<input type="text" class="form-control" name="length" id="length" disabled>
						<input type="hidden" name="path" id="path">
					</div>
				</form>

				<input id="origin-input" class="controls" type="text"
					   placeholder="Zadajte začiatok trasy">

				<input id="destination-input" class="controls" type="text"
					   placeholder="Zadajte koniec trasy">
				<div id="map" class="w-100"></div>


			</div>
		</div>
	</div>
	<?php// require("includes/footer.php");?>
	<script type="text/javascript" src="scripts/addRoute.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFYtNm-0hyuS4DgPLwbZ5BhbBS2_WEDdg&libraries=places&callback=initMap"
			async defer></script>
</body>
</html>

