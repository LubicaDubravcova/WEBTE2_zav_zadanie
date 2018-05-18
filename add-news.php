<?php 

header('Content-Type: text/html; charset=utf-8'); 
include_once("workers/dbConn.php");
require_once "workers/vendor/autoload.php";
$db = new DBConn();
$database = $db->getDB();

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
	   $q1 = "INSERT INTO news (ID, Nazov, Text) VALUES (NULL,'$name','$textarea')";
       $res = $database->query($q1);
       $q2 = "SELECT email FROM users WHERE SUBSCRIBED = 1";
       $res2 = $database->query($q2);

       while($obj = $res2->fetch_object()){
			$email = $obj->email;
			$message = '<html><body>';
			$message .= "
						<h2 style=\"color:#003366\"><b>$name</b></h2> <br/>
						$textarea<br/>	
						"; 
			$message .= '</body></html>';

			$transport = (new Swift_SmtpTransport('smtp.azet.sk', 25))
			  ->setUsername('webte2@azet.sk')
			  ->setPassword('ZavZad22')
			;

			$mail = new Swift_Mailer($transport);

			$msg = (new Swift_Message('Aktuality z Route to Fitness'))
			  ->setFrom(['webte2@azet.sk' => 'Route to Fitness'])
			  ->setTo($email)
			  ->setBody($message,'text/html')
			  ;

			// Send the message
			$emailResult = $mail->send($msg);
       }

       if($res == false){
       	header("Location: http://147.175.98.151/RealZaverecne/add-news.php");
       }
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
				<form method="post" action="add-news.php">
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