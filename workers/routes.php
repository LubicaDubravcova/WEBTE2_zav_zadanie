<?php
header('Content-Type: text/html; charset=utf-8');
include_once("dbConn.php");
$db = new DBConn();
$userData = $db->getUserData();
$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
JOIN users ON routes.OWNER=users.ID";
$result = $db->getResult($sql); //aby hodil error ked je chyba ?> 
<?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user): 
	if(($userData->ROLE == "admin") or (($userData->ROLE == "user") and (($user["TYPE"] != "Súkromná") or $user["ID"] == $userData->ID))): ?>
	<tr class="clickable-row" data-href="route.php?routeId=<?php echo $user["ROUTE_ID"]; ?>">
		<td><?php echo $user["ROUTE_NAME"]; ?></td>
		<td><?php echo number_format($user["LENGTH"]/1000,2,","," ")."km"; ?></td>
		<td><a class="<?php if ($userData->ACTIVE_ROUTE==$user["ROUTE_ID"]) echo "selected " ?>routeSelector" data-id="<?php echo $user["ROUTE_ID"] ?>"></a>
		</td>
		<td><?php echo $user["TYPE"]; ?></td>
		<?php if($userData->ROLE == "admin"): ?>
		<td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
		<?php endif; ?>
	</tr>
<?php endif; endforeach; ?>