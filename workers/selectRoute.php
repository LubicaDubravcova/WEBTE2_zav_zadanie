<?php 
header('Content-Type: text/json; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();
$database = $db->getDB();

$id = $_POST['id'];
$value = $_POST['route'];
$query = "UPDATE users SET ACTIVE_ROUTE=$value WHERE ID = $id";
$res = $database->query($query);
echo $res;
?>