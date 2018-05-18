<?php 
header('Content-Type: text/json; charset=utf-8'); 
include_once("dbConn.php");
$db = new DBConn();

$userData = $db->getUserData($_POST['id']);

// overim ci chcem deaktivovat aktivnu trasu
if($userData->ACTIVE_ROUTE == $_POST['route']) {
	$db->resetActiveRouteForUser($_POST['id']);
}
else {
	// zistit info o trase (jej typ)
	$routeData = $db->getRouteData($_POST['route']);

	// overit, ci si smie dany pouzivatel pridat zvolenu trasu
	if($routeData["TYPE"] == "Súkromná" && $routeData["OWNER"] == $_POST['id']) {
		// overit, ci trasa nebola uz cela prejdena
		$routeProgress = $db->getPrivateRouteProgress($_POST['route']);

		if($routeProgress["LENGTH"] < $routeData["LENGTH"]) {
			$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
			$res = $db->getResult($query);
			echo $res;
		}
	}
	else if($routeData["TYPE"] == "Verejná") {
		// overit, ci trasa nebola uz cela prejdena tymto pouzivatelom
		$routeProgress = $db->getPublicRouteProgress($_POST['route']);

		// najst zadaneho pouzivatela v zozname IDcok
		$index = array_search($_POST['id'], $routeProgress["UID"]);
		if($index !== false) {
			// pouzivatel je v zozname, musim overit jeho progress
			if($routeProgress["LENGTH"][$index] < $routeData["LENGTH"]) {
				// pridam
				$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
				$res = $db->getResult($query);
				echo $res;
			}
		}
		else {
			// pouzivatel nie je este v zozname, mozem ho pridat
			$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
			$res = $db->getResult($query);
			echo $res;
		}
	}
	else if($routeData["TYPE"] == "Štafeta") {
		// zistim, ci sa pouzivatel nachadza v nejakom time pre danu stafetu
		$userTeam = $db->getUserTeam($_POST['id'], $_POST['route']);

		if($userTeam !== null && $userTeam["ID"] !== null) {
			// overim progress timu
			$index = array_search($userTeam["ID"], $routeProgress["TID"]);
			if($index !== false) {
				if($routeProgress["LENGTH"][$index] < $routeData["LENGTH"]) {
					// mozem pridat
					$query = "UPDATE users SET ACTIVE_ROUTE=".$_POST['route']." WHERE ID = ".$_POST['id'];
					$res = $db->getResult($query);
					echo $res;
				}
			}
		}
		// ak je null nemozem ho pridat, lebo nema tim
	}
}
?>