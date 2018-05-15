<?php
if (isset($_SESSION['userData'])) {
	header('Location: ../profile.php');
	die();
}

if (isset($_POST['email'])) {
	include_once("dbConn.php");
	$db = new DBConn();
	$result = $db->login($_POST);
	if ($result != false) {
		$_SESSION['userData'] = $result;
		header('Location: ../profile.php');
		die();
	} else {
		header('Location: ../'.$_POST["site"]);
	}
}