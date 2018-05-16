<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
$database = new DBConn();

 $change = false;
    if (!isset($_POST["nazov"])){
    	$name = NULL;
    }
    else{
    	$name = $_POST["nazov"];
    	$change = true;
    }
    
    if (!isset($_POST["Textarea"])){
    	$textarea = NULL;
    	
    }
    else{
    	$textarea = $_POST["Textarea"];
    	$change = true;
    }

    if($change == true){
	   $q2 = "INSERT INTO News SET News.Nazov ='$name', News.Text ='$textarea'";
       $database->getAssoc($q2);
     
   }
?>

<!doctype html>
<html>
<head>
	<title>Záverečné Zadanie</title>
	<?php require("includes/head.php");?>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<link href="https://code.jquery.com/ui/1.12.1/themes/black-tie/jquery-ui.css" rel="stylesheet">
	<style>.ui-autocomplete {max-height: 100px;overflow-y: auto;overflow-x: hidden;text-align:left;}
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 5px 10px;
  }</style>
</head>
<body class="bg-dark text-white text-center">
	<?php require("includes/navbar.php"); ?>
	<div class="container">
		<div class="row">
			<div class="col">
				<h2 class="m-4 d-inline-block">Pridávanie aktualít</h2>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-md-8 col-md-offset-3 bg-light text-dark rounded p-5">
				<form method="post" action="addNews.php">
					<div class="form-group row">
						<label for="inputNazov" class="col-sm-3 col-form-label text-right">Názov:</label>
						<div class="col">
							<input type="text" name="nazov" class="form-control" id="nazov" required placeholder="Zadajte názov">
						</div>
					</div>

					<div class="form-group row">
						<label for="comment" class="col-sm-3 col-form-label text-right">Text:</label>
						<div class="col">
							<textarea name="Textarea" class="form-control" id="text" rows="10" cols="30"></textarea>
						</div>
					</div>

					<div class="form-group row">
						<button type="submit" class="btn btn-dark btn-lg btn-block">Pridaj</button>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	<?php require("includes/footer.php");?>
</body>
</html>