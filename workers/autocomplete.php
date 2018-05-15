<?php
include_once("dbConn.php");

$db = new DBConn();
if (isset($_GET["select"])) {
	$array = $db->getAssoc("SELECT DISTINCT s.name, s.id FROM schools s INNER JOIN addresses a ON s.ADDRESS_ID = a.ID WHERE a.psc LIKE '%".$_GET["psc"]."%' AND a.city LIKE '%".$_GET["city"]."%' AND a.address LIKE '%".$_GET["address"]."%'")[0];
} else if (isset($_GET["address"])) {
	$array = $db->getAssoc("SELECT DISTINCT a.address FROM schools s INNER JOIN addresses a ON s.ADDRESS_ID = a.ID WHERE a.psc LIKE '%".$_GET["psc"]."%' AND a.city LIKE '%".$_GET["city"]."%' AND a.address LIKE '%".$_GET["address"]."%' ORDER BY a.address ASC","address");
} else {
	$array = $db->getAssoc("SELECT DISTINCT a.city FROM schools s INNER JOIN addresses a ON s.ADDRESS_ID = a.ID WHERE a.psc LIKE '%".$_GET["psc"]."%' AND a.city LIKE '%".$_GET["city"]."%' ORDER BY a.city ASC","city");
}
echo json_encode($array);
?>