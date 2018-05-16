<?php
include_once("dbConn.php");
session_start();
$db = new DBConn();
$result = $db->login($_POST);
if ($result != false) {
	$_SESSION['login'] = $result;
	echo "true";
	return true;
} else {
	echo "false";
	return false;
}