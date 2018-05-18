<?php

set_error_handler(responseErrorHandler);

if(isset($_POST["routeId"])) {
	$routeId = $_POST["routeId"];

	include_once("../workers/dbConn.php");
	$db = new DBConn();

	$routeData = $db->getRouteData($routeId);

	if($routeData != null) {
		// podla typu route ziskam udaje
		switch ($routeData["TYPE"]) {
			case "Verejná":

				$data = $db->getPublicRouteProgress($routeId);

				echo json_encode($data);
				http_response_code(200);
				break;

			case "Štafeta":

				$data = $db->getRelayRouteProgress($routeId);

				echo json_encode($data);
				http_response_code(200);
				break;

			default:
				http_response_code(400);
				break;
		}
	}
	else {
		http_response_code(400);
	}
}
else {
	// BAD REQUEST
	http_response_code(400);
}

// reset na povodny handler
restore_error_handler ();

function responseErrorHandler($errno, $errstr, $errfile, $errline)
{
	// ak nastal error vrat BAD REQUEST
	http_response_code(400);

	// nepusti ku spracovaniu defaultny handler
	return true;
}
