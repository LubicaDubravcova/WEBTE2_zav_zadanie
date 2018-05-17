<?php include_once("dbConn.php");
$db = new DBConn();
$db->getDB()->query("DELETE FROM users WHERE confirmtime <= NOW() - INTERVAL 1 DAY");
