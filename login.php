<?php
header('Content-Type: text/html; charset=utf-8'); 

if (isset($_SESSION['userData'])) {
	header('Location: profile.php');
	die();
}
$result = true;
if (isset($_POST['email'])) {
	include_once("workers/dbConn.php");
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
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white text-center">
	<?php require("includes/navbar.php"); ?>
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