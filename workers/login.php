<?php
include_once("dbConn.php");
session_start();
$db = new DBConn();
$result = $db->login($_POST);
if ($result != false) {
	if($result === true) {
		echo -1;
		return -1;
	}
	$_SESSION['login'] = $result;
	echo "true";
	return true;
} else {
	echo "false";
	return false;
}