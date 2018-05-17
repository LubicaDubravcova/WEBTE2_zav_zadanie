<?php 
ini_set('display_errors', 1);
header('Content-Type: text/json; charset=utf-8'); 
include_once("workers/dbConn.php");
$db = new DBConn();
$database = $db->getDB();

$q1 = "SELECT COUNT(*) FROM news";
$res = $database->query($q1);
$count = $res->fetch_row();
$q2 = "SELECT Nazov, Text FROM news ORDER BY id DESC";
$res2 = $database->query($q2);
$countObj = 1;

while($obj = $res2->fetch_object()){
	$name = $obj->Nazov;
	$text = $obj->Text;
	if(($countObj <=3) && (($name != NULL)&&($text != NULL))){
		if($countObj ==1){
			 echo "<div class=\"carousel-item active\">
					<div class=\"carousel-content\">
					    <h4>$name</h4>
					    <p>$text</p>
					</div>
				   </div>";
		}
		else{
			echo "<div class=\"carousel-item\">
					<div class=\"carousel-content\">
					    <h4>$name</h4>
					    <p>$text</p>
					</div>
				   </div>";
		}
		$countObj++;
	}
	else{
		break;
	}
}

?>