<?php
//header('Content-Type: text/html; charset=utf-8'); 
include_once("dbConn.php");

if (isset($_SESSION['userData'])) {
	header('Location: profile.php');
	die();
}
$result = true;
if (isset($_POST['email'])) {
	$db = new DBConn();
	$result = $db->login($_POST);
	if ($result != false) {
		$_SESSION['userData'] = $result;
		header('Location: profile.php');
		die();
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Zadanie 3</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body class="bg-dark text-white text-center">
	<div class="container">
		<h1 class="mt-5">Webové Technológie 2</h1>
		<h2 class="m-4">Záverečné Zadanie</h2>
		<div class="row justify-content-center">
			<div class="col-md-6 col-md-offset-3 bg-light text-dark rounded p-5">
			<?php if (!$result) echo "<div class='row form-group'><div class='btn btn-block btn-danger disabled'>Zlý e-mail alebo heslo!</div></div>"?>
				<form method="post" action="#">
					<div class="form-group row">
						<label for="inputEmail" class="col-sm-3 col-form-label text-right">Email:</label>
						<div class="col">
							<input type="email" name="email" class="form-control" id="inputEmail" required placeholder="Zadajte email">
						</div>
					</div>
					<div class="form-group row">
						<label for="inputPassword" class="col-sm-3 col-form-label text-right">Heslo</label>
						<div class="col">
							<input type="password" name="password" class="form-control" minlength="8" id="inputPassword" required placeholder="Password">
						</div>
					</div>
					
					<div class="form-group row">
						<button type="submit" class="btn btn-dark btn-lg btn-block">Prihlásenie</button>
					</div>
					<div class="form-group row">
						<a href="register.php" class="btn btn-block btn-default bg-white">Registrácia</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>