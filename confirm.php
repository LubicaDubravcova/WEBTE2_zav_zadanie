<?php
header('Content-Type: text/html; charset=utf-8');
if (!isset($_GET["confirm"])) {
	header('Location: index.php'); 
	die();
}
include_once("workers/dbConn.php");
$db = new DBConn();
$array = json_decode(base64_decode($_GET["confirm"]),true);
$success = $db->activate($array);
?>
<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white text-center">
	<?php require("includes/navbar.php");?>
	<div class="container">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Aktivácia účtu</h2>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-md-8 col-md-offset-3 bg-light text-dark rounded p-5">
				<?php if ($success) echo "Váš účet bol úspešne aktivovaný, teraz sa môžete prihlásiť.";
				else echo "Chybný aktivačný kód. Pravdepodobne vypršala platnosť a váš účet bol vymazaný.<br>Zaregistrujte sa znova."?>
			</div>
		</div>
	</div>
	<?php require("includes/footer.php");?>
	<script src="scripts/register.js"></script>
</body>
</html>