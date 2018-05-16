<?php 
header('Content-Type: text/html; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();

$id = $_POST['id'];
$value = $_POST['sub'];

$query = "UPDATE users SET users.SUBSCRIBED='$value' WHERE users.ID = $id";
$res = $db->insertQuery($query);
echo $res;
header("Location: http://147.175.98.151/RealZaverecne/news.php");
?>