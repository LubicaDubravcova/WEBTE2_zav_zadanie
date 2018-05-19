<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php"); 
	$id = $_GET['id'];
	if ($id!=null) {
		$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
	JOIN users ON routes.OWNER=users.ID WHERE users.ID=" . $id;
	}
	else
		$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
	JOIN users ON routes.OWNER=users.ID";
	$result = $db->getResult($sql); //aby hodil error ked je chyba
?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam trás</h2>
        </div>
    </div>

	<div class='row'><div class='btn btn-block btn-danger disabled' style="display: none" id="activeWrong">Zvolenú trasu nie je možné nastaviť ako aktívnu.</div></div>

    <div class="row justify-content-center bg-light text-dark rounded p-5">

        <?php if ($role == "admin") :?>
            <div class="container" id="select">

            </div>
        <?php endif; ?>
        <div class="col">
            <div class='table-responsive'>
                <table class='table sortable table-hover'>
                    <thead>
                    <tr>
                        <th>Trasa</th>
                        <th class="sorttable_numeric">Dĺžka</th>
                        <th>Aktívna</th>
                        <th>Mód</th>
                        <?php if ($role == "admin") :?>
                        <th>Pridal</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody id="load">
                    <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user):
						if(($userData->ROLE == "admin") or (($userData->ROLE == "user") and (($user["TYPE"] != "Súkromná") or $user["ID"] == $userData->ID))): ?>
						<tr class="clickable-row" data-href="route.php?routeId=<?php echo $user["ROUTE_ID"]; ?>">
							<td><?php echo $user["ROUTE_NAME"]; ?></td>
							<td><?php echo number_format($user["LENGTH"]/1000,2,","," ")."km"; ?></td>
							<td sorttable_customkey="<?php $routeactive = false; if ($userData->ACTIVE_ROUTE==$user["ROUTE_ID"]){$routeactive = true; echo 1;} else echo 2;?>">
								<a class="<?php if($routeactive) echo "selected "; ?>routeSelector" data-id="<?php echo $user["ROUTE_ID"] ?>"></a>
							</td>
							<td><?php echo $user["TYPE"]; ?></td>
							<?php if($userData->ROLE == "admin"): ?>
								<td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
							<?php endif; ?>
						</tr>
					<?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script src="scripts/sorttable.js"></script>
<script type="text/javascript">
	
//nacita sameho seba (musi byt tbody, nacitava tr), parametre: ID, boolean fixsort - len v pripade ze je to tabulka so sortom, query - ak string tak get, ak object tak post, na poslanie vlastneho pouzite $_SERVER['QUERY_STRING'];
selfLoad("#load",true,<?php echo $_SERVER['QUERY_STRING'];?>); 
	
function loadUsers() {
    $("#select").load("workers/selectUser.php",function(){});
}
	
$(document).on("click", '.routeSelector', function(event) {
    event.stopPropagation();
    $.post("workers/selectRoute.php", {id : <?php echo $userData->ID;?>, route: $(this).data("id")}, function(data){if (data == "1") reloadContent(); else {document.getElementById("activeWrong").style.display = "block"; setTimeout(hideActionWrong, 5000);}});
});
	
$(document).ready(function(){
	loadUsers();
});

function hideActionWrong() {
	document.getElementById("activeWrong").style.display = "none";
}
</script>
</body>
</html>
