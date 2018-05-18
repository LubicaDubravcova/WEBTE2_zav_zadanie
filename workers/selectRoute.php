<?php 
header('Content-Type: text/json; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();
$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
$res = $db->getResult($query);
echo $res;
?>