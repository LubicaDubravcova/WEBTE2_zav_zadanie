<?php 
header('Content-Type: text/json; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();

$id = $_POST['id'];
$value = $_POST['sub'];
echo json_encode($_POST);
$query = "UPDATE users SET users.SUBSCRIBED=$value WHERE users.ID = $id";
$res = $db->insertQuery($query);
echo $res;
?>