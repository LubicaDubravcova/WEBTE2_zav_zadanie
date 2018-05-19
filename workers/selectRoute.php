<?php 
header('Content-Type: text/json; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();

$userData = $db->getUserData($_POST['id']);

// overim ci chcem deaktivovat aktivnu trasu
if($userData->ACTIVE_ROUTE == $_POST['route']) {
	$db->resetActiveRouteForUser($_POST['id']);
	echo true;
	return true;
}
else {
	
	$allowedRoutes = $db->getAllowedRoutes($userData->ID);
	if (in_array($_POST['route'],$allowedRoutes)) {
		$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
		$res = $db->getResult($query);
		echo $res;
		return $res;
	}
}
echo false;
return false;
?>