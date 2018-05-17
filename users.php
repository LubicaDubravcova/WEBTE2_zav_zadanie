<?php
header('Content-Type: text/html; charset=utf-8');
include_once("workers/dbConn.php");
$db = new DBConn();
<<<<<<< HEAD
if(isset($_FILES["file"])) {
	if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
		$file = $_FILES["file"]["tmp_name"];
		$db->loadCSV($file);
		unset($file);
	}
}
=======
>>>>>>> origin/master
$sql = "SELECT users.ID, CONCAT(users.FIRSTNAME, ' ', users.SURNAME) as NAME, CONCAT(a.ADDRESS, ', ', a.CITY) as ADDRESS, CONCAT(schools.NAME, ', ' , sa.CITY) as SCHOOL
FROM users JOIN addresses a ON users.ADDRESS_ID=a.ID JOIN schools ON users.SCHOOL_ID=schools.ID JOIN addresses sa ON sa.ID=schools.ADDRESS_ID";
$assoc = $db->getAssoc($sql);
?>
<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
	<style>.clickable-row{cursor: pointer;}</style>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php"); ?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam užívateľov</h2>
        </div>
    </div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
			<table class='table table-hover'>
				<thead>
				<tr>
					<th>#</th>
					<th>Užívateľ</th>
					<th>Adresa</th>
					<th>Škola</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach($assoc as $user): ?>
					<tr class='clickable-row' data-href='index.php?open=<?php echo $user["ID"]; ?>'>
						<td><?php echo $user["ID"]; ?></td>
						<td><?php echo $user["NAME"]; ?></td>
						<td><?php echo $user["ADDRESS"]; ?></td>
						<td><?php echo $user["SCHOOL"]; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
<<<<<<< HEAD
            <form enctype="multipart/form-data" method="post" action="#" class="form-inline">
            	<div class="col-8">
					<div class="row">
						<label for="file" class="col">Načítanie CSV s používateľmi: </label>
						<input type="file" class="form-control-file col" name="file" id="file" accept=".csv" required/>
					</div>
				</div>
=======
            <form method="post" action="#" class="form-inline">
				<label for="file" class="form-control-plaintext">Načítanie CSV s užívateľmi: </label>
				<input type="file" class="form-control-file" name="file" id="file" accept=".csv" required/>
>>>>>>> origin/master
				<input type="submit" class="form-control col-4"/>
			</form>
        </div>
    </div>
</div>
<script>
$(".clickable-row").click(function() {
	window.location = $(this).data("href");
});
</script>
<?php require("includes/footer.php");?>
</body>
</html>
